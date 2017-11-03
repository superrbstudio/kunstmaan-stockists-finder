<?php
namespace Superrb\KunstmaanStockistsFinderBundle\Helper\Menu;

use Kunstmaan\AdminBundle\Helper\Menu\MenuAdaptorInterface;
use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\MenuItem;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;
use Symfony\Component\HttpFoundation\Request;

class ModulesMenuAdaptor implements MenuAdaptorInterface
{

    /**
     * {@inheritDoc}
     */
    public function adaptChildren(MenuBuilder $menu, array &$children, MenuItem $parent = null, Request $request = null)
    {
        if (is_null($parent)) {
            $menuItem = new TopMenuItem($menu);
            $menuItem
                ->setRoute('superrbkunstmaanstockistsfinderbundle_admin_stockist_false')
                ->setLabel('Stockists')
                ->setUniqueId('Stockists')
                ->setFolder(true)
                ->setParent($parent);
            if (stripos($request->attributes->get('_route'), $menuItem->getRoute()) === 0) {
                $menuItem->setActive(true);
            }
            $children[] = $menuItem;
        }

        if (!is_null($parent) && 'superrbkunstmaanstockistsfinderbundle_admin_stockist_false' == $parent->getRoute()) {
            // Posts
            $menuItem = new TopMenuItem($menu);
            $menuItem
                ->setRoute('superrbkunstmaanstockistsfinderbundle_admin_stockist')
                ->setLabel('Stockists')
                ->setUniqueId('Stockists')
                ->setParent($parent);
            if ($request->attributes->get('_route') == 'superrbkunstmaanstockistsfinderbundle_admin_stockist_false') {
                $menuItem->setActive(true);
                $parent->setActive(true);
            }
            $children[] = $menuItem;
        }
    }
}