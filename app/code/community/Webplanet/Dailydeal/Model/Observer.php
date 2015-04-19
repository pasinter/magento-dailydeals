<?php

class Webplanet_Dailydeal_Model_Observer
{

    CONST QUOTE_ITEM_OPTION_CODE = 'webplanet_dailydeal';
    
    CONST ORDER_ITEM_OPTION_CODE = 'webplanet_dailydeal';
    
    /**
     *
     * @param Varien_Event_Observer $event
     */
    public function onCatalogProductCollectionLoadAfter($event)
    {
        
        return $this;
        $collection = $event->getData('collection');
        $ids = $collection->getLoadedIds();

        Mage::helper('dailydeal')->updateProductCollectionData($collection);
    }
    
    /**
     *
     * @param Varien_Event_Observer $event
     */
    public function onSalesQuoteAddItem($event)
    {
        $quoteItem = $event->getData('quote_item');
        
        $helper = Mage::helper('dailydeal');
        $deal = $helper->getCurrentDealForProduct($quoteItem->getProduct());
        
        if(null === $deal) {
            // no deal found for this product, ignore
            return $this;
        }
        
        // @todo - detect if the product actually has a current deal going on
        // @todo - add option to order - http://stackoverflow.com/questions/9412074/magento-quote-order-product-item-attribute-based-on-user-input or use the code below if it does not work
        
        /*
        // this actually works & populates into order item
        $info_buyRequest = $quoteItem->getOptionByCode('info_buyRequest');
        
        $value = is_array($info_buyRequest->value) ? $info_buyRequest->value : unserialize($info_buyRequest->value);
        
        $value['super_attribute'][] = array(151, date('Y-m-d'));
        $value['super_attribute'][] = array(152, 'test');
        
        $info_buyRequest->value = serialize($value);
         * 
         */
        //var_dump($value);
        //exit;
        
        // add a message to additional_options so it is displayed on the cart page
        $additionalOptions = $quoteItem->getOptionByCode('additional_options');
        
        if(!$additionalOptions) {
            $additionalOptions = new Mage_Sales_Model_Quote_Item_Option();
            $additionalOptions->setCode('additional_options');
            $quoteItem->addOption($additionalOptions);
        }
        
        $additionalOptionsValue = $additionalOptions->getData('value');
        
        if(!$additionalOptionsValue) {
            $additionalOptionsValue = array();
        } elseif(is_string($additionalOptionsValue)) {
            $additionalOptionsValue = unserialize($additionalOptionsValue);
        }
        
        $additionalOptionsValue = array(
            array('label' => 'Daily Deal Special Price', 
                    'value' => Mage::helper('checkout')->formatPrice($deal->getDealPrice()) 
                            . ' instead of ' . Mage::helper('checkout')->formatPrice($quoteItem->getProduct()->getPrice()))
        );
        
        $additionalOptions->setValue(serialize($additionalOptionsValue));
        
        
        // save deal info with the quote item
        $dailyDealOptions = new Mage_Sales_Model_Quote_Item_Option();
        $dailyDealOptions->setCode(self::QUOTE_ITEM_OPTION_CODE);
        $dailyDealOptions->setValue(serialize(array('deal_date' => date('Y-m-d'), 'product_price' => $quoteItem->getProduct()->getPrice(), 'deal_price' => $quoteItem->getPrice())));
        $quoteItem->addOption($dailyDealOptions);
        
        return;
         
        $dailyDealOption = new Mage_Sales_Model_Quote_Item_Option();
        $dailyDealOption->setCode('additional_options');
        //$dailyDealOption->setCode('info_buyRequest');
        
        
        $optionData = array(
            array('label' => 'Daily Deal Special Price')
        );
        $dailyDealOption->setValue(serialize($optionData));
        
        
        //$dailyDealOption->setdata('webplanet_daily_deal_date', date('Y-m-d'));
        
        $quoteItem->addOption($dailyDealOption);
        
        
        
        // a:5:{s:4:"uenc";s:72:"aHR0cDovL3h0cmVtZW51dHJpdGlvbi5sb2NhbC9vaC15ZWFoLXByb3RlaW4tYmFycy5odG1s";s:7:"product";s:4:"1046";s:15:"related_product";s:0:"";s:15:"super_attribute";a:1:{i:497;s:2:"55";}s:3:"qty";s:1:"1";}
        
    
    }
    
    
    public function onSalesConvertQuoteItemToOrderItem(Varien_Event_Observer $observer)
    {
        
        try {
            // update deal_qty_sold 
            $helper = Mage::helper('dailydeal');
            
            $quoteItem = $observer->getItem();
            $deal = $helper->getCurrentDealForProduct($quoteItem->getProduct());
            
            //var_dump($deal);exit;
            
            if(null !== $deal) {                
                //return $this;
                if ($dailydealOptions = $quoteItem->getOptionByCode(self::QUOTE_ITEM_OPTION_CODE)) {
                    $orderItem = $observer->getOrderItem();
                    $options = $orderItem->getProductOptions();
                    $options[self::ORDER_ITEM_OPTION_CODE] = unserialize($dailydealOptions->getValue());
                    $orderItem->setProductOptions($options);
                }

                // save Daily Deal options to order item
                $orderItem = $observer->getOrderItem();
                $options = $orderItem->getProductOptions();
                $options['additional_options'][] = array(
                    'label' => 'Daily Deal Special Price', 
                    'value' => Mage::helper('checkout')->formatPrice($quoteItem->getPrice())
                            . ' instead of ' . Mage::helper('checkout')->formatPrice($quoteItem->getProduct()->getPrice())
                );

                $orderItem->setProductOptions($options);

                $deal->setData('deal_qty_sold', $deal->getData('deal_qty_sold') + 1);
                $deal->save();
            }
        } catch (Exception $e) {
            echo $e;exit;
        }
        return $this;
    }
}

