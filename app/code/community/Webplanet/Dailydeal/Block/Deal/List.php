<?php

/**
 * Webplanet
 *
 * @category    Webplanet
 * @package     Webplanet_Dailydeal
 * @copyright   Copyright (c) 2011 Webplanet Ltd Nz
 */

/**
 * Product list
 *
 * @category    Webplanet
 * @package     Webplanet_Dailydeal
 * @author      Ken Golovin <ken@webplanet.co.nz>
 */
class Webplanet_Dailydeal_Block_Deal_List extends Mage_Core_Block_Template
{

    protected $_dealsCollection;

    /**
     * Retrieve bestsellers collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function getDealCollection()
    {
        $storeId = Mage::app()->getStore()->getStoreId();
        $this->setStoreId($storeId);

        if (is_null($this->_dealsCollection)) {
            $date = date('Y-m-d');
            $this->_dealsCollection = Mage::getResourceModel('dailydeal/deal_collection');

            $this->_dealsCollection//->addAttributeToSelect('*')
                    //->setStoreId($storeId)
                    //->addStoreFilter($storeId)
                    //->setOrder('qty', 'desc')
                    //->addFieldToFilter('deal_start', array('eq' => $date))
                    ->addDealDateFilter($date)
                    ->setPageSize(3)

            ;
            //echo $this->_dealsCollection->getSelectSql();
            //exit;
        }

        return $this->_dealsCollection;
    }

    public function getColumnCount()
    {

        return 3;
    }

    /**
     * Retrieve URL for adding item to shoping cart
     *
     * @param Mage_Catalog_Model_Product|Mage_Wishlist_Model_Item $item
     * @return  string
     */
    public function getAddToCartUrl($deal)
    {

        return $this->helper('checkout/cart')->getAddUrl($deal->getProduct(), array());
    }

}
