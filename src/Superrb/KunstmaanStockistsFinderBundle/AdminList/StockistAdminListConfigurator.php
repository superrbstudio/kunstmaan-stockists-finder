<?php

namespace Superrb\KunstmaanStockistsFinderBundle\AdminList;

use Doctrine\ORM\EntityManager;

use Superrb\KunstmaanStockistsFinderBundle\Form\StockistAdminType;
use Kunstmaan\AdminListBundle\AdminList\FilterType\ORM;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractDoctrineORMAdminListConfigurator;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;

/**
 * The admin list configurator for Stockist
 */
class StockistAdminListConfigurator extends AbstractDoctrineORMAdminListConfigurator
{
    /**
     * @param EntityManager $em        The entity manager
     * @param AclHelper     $aclHelper The acl helper
     */
    public function __construct(EntityManager $em, AclHelper $aclHelper = null)
    {
        parent::__construct($em, $aclHelper);
        $this->setAdminType(new StockistAdminType());
    }

    /**
     * Configure the visible columns
     */
    public function buildFields()
    {
        $this->addField('stockist', 'Stockist', true);
        $this->addField('address', 'Address', true);
        $this->addField('postCode', 'Post code', true);
        $this->addField('website', 'Website', true);
        $this->addField('county', 'County', true);
    }

    /**
     * Build filters for admin list
     */
    public function buildFilters()
    {
        $this->addFilter('stockist', new ORM\StringFilterType('stockist'), 'Stockist');
        $this->addFilter('address', new ORM\StringFilterType('address'), 'Address');
        $this->addFilter('postCode', new ORM\StringFilterType('postCode'), 'Post code');
        $this->addFilter('website', new ORM\StringFilterType('website'), 'Website');
        $this->addFilter('county', new ORM\StringFilterType('county'), 'County');
    }

    /**
     * Get bundle name
     *
     * @return string
     */
    public function getBundleName()
    {
        return 'SuperrbKunstmaanStockistsFinderBundle';
    }

    /**
     * Get entity name
     *
     * @return string
     */
    public function getEntityName()
    {
        return 'Stockist';
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return 50;
    }
}
