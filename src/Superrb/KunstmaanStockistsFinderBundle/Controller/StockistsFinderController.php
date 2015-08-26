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

        // get approved posts - newest first
        // limit passed or defaults to 10
        $posts = '';
        //render the view
        return $this->render($template, array(
            'posts' => $posts,
        ));

    }
}