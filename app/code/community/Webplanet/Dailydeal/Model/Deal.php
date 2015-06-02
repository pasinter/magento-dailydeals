<?php

class Webplanet_Dailydeal_Model_Deal extends Mage_Core_Model_Abstract
{

    const STATUS_SCHEDULED = 1;
    const STATUS_RUNNING = 2;
    const STATUS_ENDED = 3;

    public function _construct()
    {
        parent::_construct();
        $this->_init('dailydeal/deal');
    }

    /*     * ***********************************************************************
     * Private Members
     * *********************************************************************** */

    protected function _CalcProductPrice($price, $percent, $type)
    {
        if ($type) {
            return $price * (1 + ($percent / 100));
        }
        
        return $price - ($price / (100 + $percent) * $percent);
    }

    /*     * ***********************************************************************
     * Public Members
     * *********************************************************************** */

    public function getCustomerGroupIds()
    {
        $ids = $this->getData('customer_group_ids');
        if (($ids && !$this->getCustomerGroupChecked()) ||
                is_string($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            $groupIds = Mage::getModel('customer/group')->getCollection()->getAllIds();
            $ids = array_intersect($ids, $groupIds);
            $this->setData('customer_group_ids', $ids);
            $this->setCustomerGroupChecked(true);
        }
        return $ids;
    }

    /**
     *
     * @return string
     */
    public function getStatusDescription()
    {
        $status = 'Not Saved';
        switch ($this->getStatus()) {
            case $this::STATUS_SCHEDULED:
                $status = Mage::helper('dailydeal')->__('Scheduled');
                break;
            case $this::STATUS_RUNNING:
                $status = Mage::helper('dailydeal')->__('Running');
                break;
            case $this::STATUS_ENDED:
                $status = Mage::helper('dailydeal')->__('Ended');
                break;
        }

        return $status;
    }

    public function isScheduled()
    {
        return $this->getStatus() === self::STATUS_SCHEDULED;
    }

    public function isRunning()
    {
        return $this->getStatus() === self::STATUS_RUNNING;
    }

    public function isEnded()
    {
        return $this->getStatus() === self::STATUS_ENDED;
    }

    public function isLocked()
    {
        if ($this->isRunning()) {
            return true;
        }

        if ($this->isEnded()) {
            return true;
        }

        return false;
    }

    public function getStore($website = null)
    {
        if ($website === null) {
            if (isset($this->_data['website_ids'][0]))
                $website = Mage::app()->getWebsite($this->_data['website_ids'][0]);
            else
                $website = Mage::app()->getWebsite(null);
        }
        if ($website === null)
            return Mage::app()->getDefaultStoreView();
        return $website->getDefaultStore();
    }

    /**
     * Gets original product price including/excluding tax
     *
     * @param	null|bool $tax - null = default, true = including, false = excluding
     * @param	mixed $store
     * @return	float $price
     */
    public function getProductPrice($tax = null, $website = null)
    {
        $tax_helper = Mage::helper('tax');
        $store = $this->getStore($website);
        $product = $this->getProduct();
        $price = $product->getPrice();
        $priceIncludesTax = $tax_helper->priceIncludesTax($store);
        $percent = $product->getTaxPercent();
        $includingPercent = null;
        $taxClassId = $product->getTaxClassId();

        if ($percent === null && $taxClassId) {
            $request = Mage::getSingleton('tax/calculation')->getRateRequest(null, null, null, $store);
            $percent = Mage::getSingleton('tax/calculation')->getRate($request->setProductClassId($taxClassId));
        }
        if ($priceIncludesTax && $taxClassId) {
            $request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false, $store);
            $includingPercent = Mage::getSingleton('tax/calculation')->getRate($request->setProductClassId($taxClassId));
        }
        if (($percent === false || $percent === null) && $priceIncludesTax && !$includingPercent)
        //return $store->roundPrice($price);
            return $price;

        if ($priceIncludesTax)
            $price = $this->_CalcProductPrice($price, $includingPercent, false);
        if ($tax || (($tax === null) && ($tax_helper->getPriceDisplayType($store) != Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX)))
            $price = $this->_CalcProductPrice($price, $percent, true);
        //return $store->roundPrice($price);
        return $price;
    }

    public function getProduct()
    {
        if ($this->hasData('product')) {
            return $this->getData('product');
        }

        $product = Mage::getModel('catalog/product')->load($this->getProductId());
        $this->setProduct($product);

        return $product;
    }

    public function getUrl()
    {
        return $this->getProduct()->getProductUrl();
    }

    public function getThumbUrl($size = 50)
    {
        $product = $this->getProduct();
        return (string) Mage::helper('catalog/image')->init($product, 'thumbnail', $product->getSmallImage())->resize($size);
    }

    /**
     *
     * @return double
     */
    public function getSavings()
    {
        $product_price = $this->getProduct()->getPrice();

        return $product_price - $this->getDealPrice();
    }

    /*     * ***********************************************************************
     * Event Handlers
     * *********************************************************************** */

    protected function _afterLoad()
    {
        parent::_afterLoad();

        $this->_prepareDatesAfterLoad();
        $this->_preparePromoAfterLoad();

        $websiteIds = $this->_getData('website_ids');
        if (is_string($websiteIds)) {
            $this->setWebsiteIds(explode(',', $websiteIds));
        }
        $groupIds = $this->getCustomerGroupIds();
        if (is_string($groupIds)) {
            $this->setCustomerGroupIds(explode(',', $groupIds));
        }
    }

    protected function _beforeSave()
    {
        // Clear product cache (to ensure the observer adds watermark and special price etc.):
        $product = Mage::getModel('catalog/product')->load($this->getProductId());
        $product->cleanCache();

        $this->_prepareWebsiteIds();
        $this->_prepareDatesForSave();
        $this->_preparePromoForSave();
        $this->_prepareRecurrenceForSave();

        if (is_array($this->getCustomerGroupIds())) {
            $this->setCustomerGroupIds(join(',', $this->getCustomerGroupIds()));
        }
        parent::_beforeSave();
    }

    protected function _prepareWebsiteIds()
    {
        if (is_array($this->getWebsiteIds())) {
            $this->setWebsiteIds(join(',', $this->getWebsiteIds()));
        }
        return $this;
    }

    protected function _prepareDatesForSave()
    {
        // calc start/end time only if the user entered one...
        // when adding disabled field in form, the value is not passed back to in the POST
        // event
        // This happens when saving from the Admin new/edit pages:
        if ($this->getStartDate()) {
            $start_date_time = strtotime($this->getStartDate() . " " . $this->getStartHour());
            $end_date_time = strtotime($this->getStartDate() . " " . $this->getStartHour() . " +" . $this->getSaleLength() . " hour");

            // When setting to the entity, convert to UTC (GMT-0):
            $this->setStartDateTime(Mage::getModel('core/date')->gmtDate("Y-m-d H:i:s", $start_date_time));
            $this->setEndDateTime(Mage::getModel('core/date')->gmtDate("Y-m-d H:i:s", $end_date_time));
        }

        return $this;
    }

    protected function _preparePromoForSave()
    {
        if (!$this->hasData("promo")) {
            $promo = array();
            foreach ($this->GetData() as $key => $val) {
                if (substr($key, 0, 6) == "promo_") {
                    $promo[substr($key, 6)] = $val;
                    $this->unsetData($key);
                }
            }
            $this->setPromo(serialize($promo));
        }
        return $this;
    }

    public function _prepareRecurrenceForSave()
    {
        if (($this->getRecurrenceLength() > 0) && ($this->getRecurrenceAncestor() == null)) {
            $this->setRecurrenceAncestor($this->getZizioObjectId());
        }
        return $this;
    }

    protected function _preparePromoAfterLoad()
    {
        $promo = $this->_getData("promo");
        $this->unsetData("promo");
        if (is_string($promo))
            $promo = unserialize($promo);
        if (!is_array($promo))
            return;
        foreach ($promo as $key => $val)
            $this->setData("promo_{$key}", $val);
    }

    public function preparePromoAfterLoad()
    {
        $this->_preparePromoAfterLoad();
    }

    protected function _prepareDatesAfterLoad()
    {
        if ($this->getStartDateTime() && $this->getEndDateTime()) {
            // Start Date (converted from UTC to store timezone):
            $start_date_time = Mage::getModel('core/date')->date(null, $this->getStartDateTime());

            // End Date (converted from UTC to store timezone):
            $end_date_time = Mage::getModel('core/date')->date(null, $this->getEndDateTime());

            $start_date = substr($start_date_time, 0, 10);
            $this->setStartDate($start_date);

            $start_time = substr($start_date_time, 11, 5);
            $this->setStartHour($start_time);

            $sale_length = (strtotime($end_date_time) - strtotime($start_date_time)) / 3600;
            $this->setSaleLength($sale_length);
        }

        return $this;
    }

    public function getPercentageRemaining($increment = 5)
    {
        $originalQty = $this->getData('deal_qty');
        $soldQty = $this->getData('deal_qty_sold');

        //var_dump($this->getData());exit;

        $percentage = 100 - round(100 * $soldQty / $originalQty);

        //var_dump($percentage);exit;

        $increments = $this->getDealQty();

        // @todo
        // get the original qty
        // get sold items qty

        return $percentage;
    }
    
    /**
     * 
     * @return int
     */
    public function getRemainingQty()
    {
        return $this->getDealQty() - $this->getDealQtySold();
    }
    
    /**
     * 
     * @return bool
     */
    public function isAvailable()
    {
        return $this->getRemainingQty() > 0;
    }

}
