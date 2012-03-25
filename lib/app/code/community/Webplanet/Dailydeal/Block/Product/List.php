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
class Webplanet_Dailydeal_Block_Product_List extends Mage_Core_Block_Template
{



    /**
     * Retrieve bestsellers collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {
      $storeId = Mage::app()->getStore()->getStoreId();
      $this->setStoreId($storeId);
      if (is_null($this->_productCollection))
      {
          $this->_dealsCollection = Mage::getResourceModel('dailydeal/deal');
          

          $this->_productCollection//->addAttributeToSelect('*')
                          //->setStoreId($storeId)
                          //->addStoreFilter($storeId)
                          //->setOrder('qty', 'desc')
                          ->setPageSize(3)
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