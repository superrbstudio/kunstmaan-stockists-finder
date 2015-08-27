<?php namespace Superrb\KunstmaanStockistsFinderBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Superrb\KunstmaanStockistsFinderBundle\Form\StockistsFinderType;

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
            $ids = $this->searchStockists($postcode);
            if(!empty($ids)) {
                $records = $repository->createQueryBuilder('p')
                    ->orderBy('p.stockist', 'ASC')
                    ->where("p.id IN(" . $ids . ")")
                    ->setMaxResults($limit)
                    ->getQuery()->getResult();
            } else {
                //display a message to say no results - will show all
            }
        }

        // convert to an array for json format for the map pins
        $jsonRecords = array();
        foreach ($records as $k => $record) {
            $jsonRecords[$k] = array(
                'stockist' => $record->getStockist(),
                'address' => $record->getAddress(),
                'postcode' => $record->getPostCode(),
                'website' => $record->getWebsite(),
                'latitude' => $record->getLatitude(),
                'longitude' => $record->getLongitude()
            );
        }
        // encode the array
        $jsonRecords = json_encode($jsonRecords);

        $form = $this->createForm(new StockistsFinderType());

        //render the view
        return $this->render($template, array(
            'records' => $records,
            'jsonRecords' => $jsonRecords,
            'form' => $form->createView(),
        ));
    }

    public function searchStockists($postcode)
    {

        //check if country is not Ireland
        //$countryCode = $value['country'];

        //check if postcode is not empty
        if (!empty($postcode)) {
            //look up postcode results
            $lookup = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($postcode));
            $lookup = json_decode($lookup);
            if (isset($lookup->status) and $lookup->status == 'OK' and isset($lookup->results) and isset($lookup->results[0])) {
                //continue
                $latitude = $lookup->results[0]->geometry->location->lat;
                $longitude = $lookup->results[0]->geometry->location->lng;

                // we have to do the query like this as doctrine does not support acos function
                $stmt = $this->getEntityManager()
                    ->getConnection()
                    ->prepare("SELECT sb_ksfb_stockist.id, ( 3959 * acos( cos( radians(" . $latitude . ") ) * cos( radians(sb_ksfb_stockist.latitude) ) * cos( radians(sb_ksfb_stockist.longitude) - radians(" . $longitude . ") ) + sin( radians(" . $latitude . ") ) * sin( radians(sb_ksfb_stockist.latitude) ) ) ) AS distance
                FROM sb_ksfb_stockist
                WHERE sb_ksfb_stockist.latitude != '' AND sb_ksfb_stockist.longitude != ''
                HAVING distance < 2
                ORDER BY distance");
                $stmt->execute();
                $records = $stmt->fetchAll();

                $ids = array();
                //get ids from results
                foreach ($records as $result) {
                    $ids[] = $result['id'];
                }

                // implode the ids ready for the query
                if (count($ids) > 0) {
                    $ids = implode(',', $ids);
                }

                // pass the ids back to the view
                return $ids;

            } else {
                //errors
            }
        }
    }

    public function getEntityManager() {
        return $this->container->get('doctrine')->getEntityManager();
    }

}