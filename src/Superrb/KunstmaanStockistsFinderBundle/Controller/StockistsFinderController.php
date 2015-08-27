<?php namespace Superrb\KunstmaanStockistsFinderBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class StockistsFinderController extends Controller
{
    //pass the request to the action
    public function stockistsAction(Request $request, $limit = 10, $template = 'SuperrbKunstmaanStockistsFinderBundle:StockistsFinder:stockists.html.twig')
    {

        // get the table
        $repository = $this->getDoctrine()
            ->getRepository('SuperrbKunstmaanStockistsFinderBundle:Stockist');

        // get all stockists - $records used for the stockists list.
        $records = $repository->createQueryBuilder('p')
            ->orderBy('p.stockist', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()->getResult();

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

        //render the view
        return $this->render($template, array(
            'records' => $records,
            'jsonRecords' => $jsonRecords
        ));
    }
}