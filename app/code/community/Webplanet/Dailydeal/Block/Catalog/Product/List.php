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
 * @author      pasinter
 */
class Webplanet_Dailydeal_Block_Catalog_Product_List extends Mage_Catalog_Block_Product_Abstract
{

    /**
     * Product Collection
     *
     * @var Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected $_productCollection;

    /**
     * Default product limit
     * @var int
     */
    protected $_default_product_limit = 3;

    /**
     * This is how many product will be selected
     *
     * @var int
     */
    protected $_default_select_limit = 3;

    public function getTimeLimit()
    {
        if ($this->getData('time_limit_in_days')) {
            return intval($this->getData('time_limit_in_days'));
        } else {
            return $this->_default_days_limit;
        }
    }

    public function getProductsLimit()
    {
        if ($this->getData('limit')) {
            return intval($this->getData('limit'));
        } else {
            return $this->_default_product_limit;
        }
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
            if ($this->getTimeLimit()) {
                $product = Mage::getModel('catalog/product');
                $todayDate = $product->getResource()->formatDate(time());
                $startDate = $product->getResource()->formatDate(time() - 60 * 60 * 24 * $this->getTimeLimit());
                $this->_productCollection = $this->_productCollection->addOrderedQty($startDate, $todayDate);
            } else {
                $this->_productCollection = $this->_productCollection->addOrderedQty();
            }
            $this->_productCollection = $this->_productCollection->addAttributeToSelect('*')
                    ->setStoreId($storeId)
                    ->addStoreFilter($storeId)
                    ->setOrder('ordered_qty', 'desc')
                    ->addAttributeToSelect('status')
                    ->setPageSize($this->getProductsLimit());

            $checkedProducts = new Varien_Data_Collection();
            $curPage = 1;
            while (count($checkedProducts) < $this->getProductsLimit()) {
                $this->_productCollection->clear()->setPageSize(20)->setCurPage($curPage)->load();

                if ($this->_productCollection->getCurPage() != $curPage) {
                    break; //if bestsellers list is over simply exit
                }

                foreach ($this->_productCollection as $k => $p) {

                    $p = $p->loadParentProductIds();
                    $parentIds = $p->getData('parent_product_ids');
                    // if product is part of configurable product get first parent product
                    if (is_array($parentIds) && !empty($parentIds)) {
                        if (!$checkedProducts->getItemById($parentIds[0])) {
                            $parentProduct = Mage::getModel('catalog/product')->setStoreId($storeId)->load($parentIds[0]);
                            if ($parentProduct->isVisibleInCatalog()) {
                                $checkedProducts->addItem($parentProduct);
                            }
                        }
                    } else {
                        if (!$checkedProducts->getItemById($k)) {
                            $checkedProducts->addItem($p);
                        }
                    }
                    if (count($checkedProducts) >= $this->getProductsLimit()) {
                        break;
                    }
                }
                $curPage++;
            }
            $this->_productCollection = $checkedProducts;
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
        //return null;
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml()
    {
        $this->_getProductCollection()->load();

        return parent::_beforeToHtml();
    }

    public function setCollection($collection)
    {
        $this->_productCollection = $collection;
        return $this;
    }

}
