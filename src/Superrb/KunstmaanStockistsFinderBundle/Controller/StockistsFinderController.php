<?php namespace Superrb\KunstmaanStockistsFinderBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Superrb\KunstmaanStockistsFinderBundle\Form\StockistsFinderType;
use Symfony\Component\Intl\Intl;

class StockistsFinderController extends Controller
{
    //pass the request to the action
    public function stockistsAction(Request $request, $limit = 10, $template = 'SuperrbKunstmaanStockistsFinderBundle:StockistsFinder:stockists.html.twig', $ids = null)
    {
        // get the table
        $repository = $this->getDoctrine()
            ->getRepository('SuperrbKunstmaanStockistsFinderBundle:Stockist');

        // get all stockists - $records used for the stockists list.
        $records = $repository->createQueryBuilder('p')
            ->orderBy('p.stockist', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()->getResult();

        if($request->isMethod('POST')) {
            $data = $request->request->get('stockists_finder_form');
            $postcode = $data['postcode'];
            $country = $data['country'];

            if(!empty($country)) {
                $ids = $this->searchStockists($country, $postcode);
                if(!empty($ids)) {
                    // implode the ids ready for the query
                    $idString = count($ids) > 0 ? implode(',', $ids) : $ids ;
                    $records = $repository->createQueryBuilder('p')
                        ->orderBy('p.stockist', 'ASC')
                        ->where("p.id IN(" . $idString . ")")
                        ->getQuery()->getResult();
                }
            }
            else {
                //display a message to say no results - will show all
            }
        }

        // convert to an array for json format for the map pins
        $jsonRecords = array();
        $orderedRecords = array();
        foreach ($records as $k => $record) {
            $orderedRecords[$record->getId()] = $record;
            $jsonRecords[$k] = array(
                'stockist' => $record->getStockist(),
                'address' => $record->getAddress(),
                'county' => $record->getCounty(),
                'country' => $record->getCountry(),
                'postcode' => $record->getPostCode(),
                'website' => $record->getWebsite(),
                'latitude' => $record->getLatitude(),
                'longitude' => $record->getLongitude()
            );
        }

        if(!empty($ids)) {
            $newOrderedRecords = array();
            foreach($ids as $id) {
                $newOrderedRecords[$id] = $orderedRecords[$id];
            }
            $records = $newOrderedRecords;
        }

        // encode the array
        $jsonRecords = json_encode($jsonRecords);

        $form = $this->createForm(StockistsFinderType::class);

        //render the view
        return $this->render($template, array(
            'records' => $records,
            'jsonRecords' => $jsonRecords,
            'form' => $form->createView(),
            'country' => !empty($country) ? $country : '',
            'postcode' => !empty($postcode) ? $postcode : ''
        ));
    }

    public function searchStockists($countryCode, $postcode)
    {
        $countryName = Intl::getRegionBundle()->getCountryName($countryCode);

        //check if postcode is not empty
        if (!empty($postcode)) {
            //look up postcode results
            $lookup = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($postcode));
        } else {
            //look up country results
            $lookup = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($countryName));
        }

        $lookup = json_decode($lookup);
        if (isset($lookup->status) and $lookup->status == 'OK' and isset($lookup->results) and isset($lookup->results[0])) {
            //continue
            $latitude = $lookup->results[0]->geometry->location->lat;
            $longitude = $lookup->results[0]->geometry->location->lng;

            //write the switch condition for either limit or radius.
            // define query end as it has to be in order
            $queryEnd = 'ORDER BY distance ASC';
            $conditions = '';
            if (!empty($postcode) || !empty($countryCode)) {
                if (!empty($countryCode)) {
                    $conditions .= 'AND sb_ksfb_stockist.country = "' . $countryCode . '" ';
                }
                if (!empty($postcode)) {
                    if ($this->container->getParameter('stockistsfindersearchby') == 'radius') {
                        $conditions .= ' HAVING distance < ' . $this->container->getParameter('stockistsfindersearchbyvalue') . ' ' . $queryEnd;
                    } elseif($this->container->getParameter('stockistsfindersearchby') == 'limit') {
                        $conditions .= $queryEnd . ' LIMIT ' . $this->container->getParameter('stockistsfindersearchbyvalue');
                    }
                }
            } else {
                $conditions .= $queryEnd;
            }
            // we have to do the query like this as doctrine does not support acos function
            $stmt = $this->getEntityManager()
                ->getConnection()
                ->prepare("SELECT sb_ksfb_stockist.id, ( 3959 * acos( cos( radians(" . $latitude . ") ) * cos( radians(sb_ksfb_stockist.latitude) ) * cos( radians(sb_ksfb_stockist.longitude) - radians(" . $longitude . ") ) + sin( radians(" . $latitude . ") ) * sin( radians(sb_ksfb_stockist.latitude) ) ) ) AS distance
            FROM sb_ksfb_stockist
            WHERE sb_ksfb_stockist.latitude != '' AND sb_ksfb_stockist.longitude != ''
            " . $conditions);

            $stmt->execute();
            $records = $stmt->fetchAll();

            $ids = array();
            //get ids from results
            foreach ($records as $result) {
                $ids[] = $result['id'];
            }

            // pass the ids back to the view
            return $ids;

        } else {
            //errors
        }
    }

    public function getEntityManager() {
        return $this->container->get('doctrine')->getEntityManager();
    }

}
