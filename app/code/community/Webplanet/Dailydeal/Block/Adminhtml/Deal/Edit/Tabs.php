<?php

/**
 * Webplanet
 *
 * @category    Webplanet
 * @package     Webplanet_Dailydeal
 * @copyright   Copyright (c) 2011 Webplanet Ltd Nz
 */

/**
 *
 * @category    Webplanet
 * @package     Webplanet_Dailydeal
 * @author      pasinter
 */
class Webplanet_Dailydeal_Block_Adminhtml_Deal_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('dailydeal_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('dailydeal')->__('Daily Deal Setup'));
    }

    protected function _beforeToHtml()
    {
        $deal = Mage::registry('dailydeal');

        if (!$deal->isLocked()) {
            $this->addTab('product_section', array(
                'label' => Mage::helper('dailydeal')->__('Product Selection'),
                'title' => Mage::helper('dailydeal')->__('Product Selection'),
                'content' => $this->getLayout()->createBlock('dailydeal/adminhtml_deal_edit_product_form')->toHtml(),
            ));
        }

        $this->addTab('conf_section', array(
            'label' => Mage::helper('dailydeal')->__('Deal Settings'),
            'title' => Mage::helper('dailydeal')->__('Deal Settings'),
            'content' => $this->getLayout()->createBlock('dailydeal/adminhtml_deal_edit_conf_form')->toHtml(),
        ));

        if ($deal->getProductId()) {
            $this->setActiveTab('conf_section');
        }

        return parent::_beforeToHtml();
    }

}
