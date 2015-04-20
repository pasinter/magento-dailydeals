<?php

class Webplanet_Dailydeal_Model_Mysql4_Deal_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('dailydeal/deal');
    }

    /**
     *
     * @param Zend_Date $date
     * @param array $product_ids
     */
    public function loadDealsForDay(Zend_Date $date, $product_ids = array())
    {
        $this;
    }

    /**
     *
     * @param string|Zend_Date $date
     * @return Webplanet_Dailydeal_Model_Mysql4_Product_Collection
     */
    public function addDealDateFilter($date)
    {
        $condition = array('eq' => $date);


        $helper = Mage::helper('dailydeal');

        $this
                ->addFieldToFilter('deal_start', array('gteq' => $helper->getCurrentStartTime()->toString('Y-MM-dd')))
                ->addFieldToFilter('deal_start', array('lt' => $helper->getNextStartTime()->toString('Y-MM-dd')))
        ;

        return $this;
    }

    /**
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Collection_Abstract $products 
     */
    public function addProductsFilter(Mage_Catalog_Model_Resource_Eav_Mysql4_Collection_Abstract $products)
    {
        $condition = array('in' => $products->getLoadedIds());

        $this->addFieldToFilter('product_id', $condition);

        return $this;
    }

}
