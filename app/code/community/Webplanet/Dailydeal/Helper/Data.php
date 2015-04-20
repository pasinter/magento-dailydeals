<?php

/**
 * Webplanet
 *
 * @category    Webplanet
 * @package     Webplanet_Dailydeal
 */

/**
 * Dailydeal Helper
 *
 * @category    Webplanet
 * @package     Webplanet_Dailydeal
 */
class Webplanet_Dailydeal_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Get prefix for all Dailydeal URLs
     *
     * @return string
     */
    public function getUrlPrefix()
    {
        return Mage::getStoreConfig('catalog/dailydeal/url_prefix');
    }

    public function getCurrentStartTime()
    {
        $expire_time = $this->getExpireTime();

        if ($this->getIsPastExpireTime()) {
            $time_expire = Mage::app()->getLocale()
                    ->date()
                    ->setHour($expire_time->getHour())
                    ->setMinute($expire_time->getMinute())
                    ->setSecond($expire_time->getSecond());
        } else {
            $time_expire = Mage::app()->getLocale()
                    ->date()
                    ->sub(1, Zend_Date::DAY)
                    ->setHour($expire_time->getHour())
                    ->setMinute($expire_time->getMinute())
                    ->setSecond($expire_time->getSecond());
        }

        return $time_expire;
    }

    public function getNextStartTime()
    {
        $expire_time = $this->getExpireTime();

        if ($this->getIsPastExpireTime()) {
            $time_expire = Mage::app()->getLocale()
                    ->date()
                    ->add(1, Zend_Date::DAY)
                    ->setHour($expire_time->getHour())
                    ->setMinute($expire_time->getMinute())
                    ->setSecond($expire_time->getSecond());
        } else {
            $time_expire = Mage::app()->getLocale()
                    ->date()
                    ->setHour($expire_time->getHour())
                    ->setMinute($expire_time->getMinute())
                    ->setSecond($expire_time->getSecond());
        }

        return $time_expire;
    }

    /**
     * Get deals expire time
     * 
     * @return Zend_Date
     */
    public function getExpireTime()
    {
        $config_time = Mage::getStoreConfig('catalog/dailydeal/expire_time');

        try {
            $time = new Zend_Date($config_time, 'hh,mm,ss');
        } catch (Exception $e) {
            $time = new Zend_Date('23:59:59', 'hh:mm:ss');
        }

        return $time;
    }

    /**
     *
     * @return int 
     */
    public function getExpiresInSeconds()
    {
        $expire_time = $this->getExpireTime();

        $current_time = time();
        // @todo


        $seconds = 0;

        return $seconds;
    }

    /**
     *
     * @return Zend_Date
     */
    public function getNextExpireTime()
    {
        if (!$this->getIsPastExpireTime()) {

            return $this->getTodayExpireTime();
        }

        return $this->getTomorrowExpireTime();
    }

    /**
     *
     * @return Zend_Date
     */
    public function getTodayExpireTime()
    {
        $expire_time = $this->getExpireTime();


        $today_time_expire = Mage::app()->getLocale()->date()
                ->setHour($expire_time->getHour())
                ->setMinute($expire_time->getMinute())
                ->setSecond($expire_time->getSecond());

        return $today_time_expire;
    }

    /**
     *
     * @return Zend_Date
     */
    public function getTomorrowExpireTime()
    {
        $expire_time = $this->getExpireTime();


        // the next expire time is tomorrow
        $tomorrow_time_expire = Mage::app()->getLocale()->date()
                ->add(1, 'dd')
                ->setHour($expire_time->getHour())
                ->setMinute($expire_time->getMinute())
                ->setSecond($expire_time->getSecond());

        return $tomorrow_time_expire;
    }

    public function getIsPastExpireTime()
    {
        $today_time_expire = $this->getTodayExpireTime();

        $store_date = Mage::app()->getLocale()->storeDate(null, null, true);

        //echo "today_time_expire: $today_time_expire<br>";
        //echo "store_date: $store_date<br>";
        //echo '<br>' . $today_time_expire->compare($store_date) . '<br>'; exit;

        if ($today_time_expire->compare(time()) == 1) {

            return false;
        }

        return true;
    }

    /**
     *
     * @param <type> $product
     */
    public function updateProductData($product, $deal)
    {
        $product->setPrice($deal->getDealPrice());
    }

    public function updateProductCollectionData(Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection)
    {
        //$ids = $collection->getLoadedIds();

        $dealsCollection = Mage::getModel('dailydeal/deal')->getCollection()
                //->addDealDateFilter()
                ->addProductsFilter($collection)
        ;

        if (count($dealsCollection) > 0) {
            foreach ($dealsCollection as $deal) {
                $productWithDeal = $collection->getItemById($deal->getProductId());
                $this->updateProductData($productWithDeal, $deal);
            }
        }
    }

    /**
     *
     * @param type $product
     * @return Webplanet_Dailydeal_Model_Deal 
     */
    public function getCurrentDealForProduct($product)
    {
        /**
          if($product->getData('dailydeal')) {
          return $product->getData('dailydeal');
          }
         * 
         */
        $collection = Mage::getModel('dailydeal/deal')->getCollection()
                //->addAttributeToSelect($additionalAttributes)
                ->addFieldToFilter('product_id', $product->getEntityId())
                ->addFieldToFilter('deal_start', array('gt' => date('Y-m-d', time() - 87600)))
        //->setPage(1, 1)
        ;

        //echo $collection->getSelectSql();
        foreach ($collection as $deal) {
            //echo $deal->getData('deal_id');
            //$product->setData('dailydeal', $deal);

            return $deal;
        }

        /*
          $deal = Mage::getModel('dailydeal/deal')->loadByAttribute('product_id', $product->getEntityId());

          var_dump($deal);exit;

          $collection
          ->addAttributeToFilter('product_id', $product->getEntityId())
          ////->addAttributeToSelect('*')
          //->setStoreId($storeId)
          //->addStoreFilter($storeId)
          //->setOrder('qty', 'desc')
          ->setPageSize(1)
          ;
         * 
         */

        //$deal = $collection->

        return null;
    }

    /**
     *
     * @param type $percentage
     * @param type $number
     * @return type 
     */
    public function roundToBlocks($percentage, $number)
    {
        $blocks = floor($percentage / $number);

        $remaining = $percentage % $number;

        if ($remaining > $number / 2) {
            $blocks++;
        }

        return $blocks;
    }

    /**
     * Get the magento date in format:
     * 2033-12-33 0:0:0
     * 
     * @return string 
     */
    public function getMagentoDate($format = 'Y-m-d 00:00:00')
    {
        /* @var $dt Mage_Core_Model_Date */
        $dt = Mage::getModel('core/date');
        $now = $dt->timestamp(time());
        $date = date($format, $now);
        return $date;
    }

}
