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
class Webplanet_Dailydeal_Block_Adminhtml_Deal_Edit_Product_Form extends Mage_Adminhtml_Block_Widget_Form
{

    public function getFormHtml()
    {
        $products_grid = $this->getLayout()->createBlock('dailydeal/adminhtml_deal_edit_product_grid');
        return parent::getFormHtml() . $products_grid->getHtml();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
