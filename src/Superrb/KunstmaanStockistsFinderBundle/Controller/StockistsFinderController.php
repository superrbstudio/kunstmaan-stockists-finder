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
        foreach ($records as $record) {
            $jsonRecords[]['stockist'] = $record->getStockist();
            $jsonRecords[]['address'] = $record->getAddress();
            $jsonRecords[]['postcode'] = $record->getPostCode();
            $jsonRecords[]['website'] = $record->getWebsite();
            $jsonRecords[]['latitude'] = $record->getLatitude();
            $jsonRecords[]['longitude'] = $record->getLongitude();
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