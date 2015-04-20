<?php

/**
 * Webplanet
 *
 * @category    Webplanet
 * @package     Webplanet_Dailydeal
 * @copyright   Copyright (c) 2011 Webplanet Ltd Nz
 */

/**
 * Daily deals product list
 *
 * @category    Webplanet
 * @package     Webplanet_Dailydeal
 * @author      Ken Golovin <ken@webplanet.co.nz>
 */
class Webplanet_Dailydeal_Block_Deals extends Mage_Catalog_Block_Product_Abstract
{

    /**
     * (non-PHPdoc)
     * @see app/code/core/Mage/Core/Block/Mage_Core_Block_Abstract#_prepareLayout()
     */
    protected function _prepareLayout()
    {

        //$label = Mage::helper('dailydeal')->__('Daily Deals');
        /*
          $label = '';

          if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs'))
          {
          $breadcrumbBlock->addCrumb('home', array(
          'label' => Mage::helper('dailydeal')->__('Home'),
          'title' => Mage::helper('dailydeal')->__('Go to Home Page'),
          'link' => Mage::getBaseUrl()
          ));
          $breadcrumbBlock->addCrumb('deals', array(
          'label' => $label,
          'title' => $label
          ));

          }
         * 
         */
        parent::_prepareLayout();
        /*
          $title = $label . ' - ' . $value;

          if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
          $breadcrumbBlock->addCrumb('home', array(
          'label' => Mage::helper('browseby')->__('Home'),
          'title' => Mage::helper('browseby')->__('Go to Home Page'),
          'link' => Mage::getBaseUrl()
          ));
          $breadcrumbBlock->addCrumb('attribute', array(
          'label' => $label,
          'title' => $label
          ));
          $breadcrumbBlock->addCrumb('value', array(
          'label' => $value,
          'title' => $value
          ));
          }

          if ($headBlock = $this->getLayout()->getBlock('head')) {
          if ($title) {
          $headBlock->setTitle($title);
          }
          }
         * 
         */
    }

    /**
     * Returns child html of product list
     *
     * @return string
     */
    public function getProductListHtml()
    {

        return $this->getChildHtml('deal_list');
    }

    /**
     *
     * @return Zend_Date
     */
    public function getNextExpireTime()
    {

        return Mage::helper('dailydeal')->getNextExpireTime();
    }

    public function getCurrentStoreTime()
    {

        return Mage::getModel('core/date');
    }

    /**
     * Retrieve bestsellers collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {
        $storeId = Mage::app()->getStore()->getStoreId();
        $this->setStoreId($storeId);
        if (is_null($this->_productCollection)) {
            $this->_productCollection = Mage::getResourceModel('dailydeal/product_collection');

            $this->_productCollection//->addAttributeToSelect('*')
            //->setStoreId($storeId)
            //->addStoreFilter($storeId)
            //->setOrder('qty', 'desc')
            //->setPageSize(3)
            ;
        }

        return $this->_productCollection;
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getLoadedProductCollection()
    {

        return $this->_getProductCollection();
    }

}
