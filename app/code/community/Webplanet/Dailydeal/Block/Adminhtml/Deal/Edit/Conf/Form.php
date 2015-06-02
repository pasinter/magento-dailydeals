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
class Webplanet_Dailydeal_Block_Adminhtml_Deal_Edit_Conf_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('dailydeal_form', array('legend' => Mage::helper('dailydeal')->__('Deal Settings')));

        $deal = Mage::registry('dailydeal');
        $product_data = Mage::getModel('catalog/product');

        $dealDisabled = $deal->isLocked();

        $sku = '';
        $url_key = '';
        if ($deal->getProductId() > 0) {
            $product_data = $product_data->load($deal->getProductId());
            $sku = $product_data->getSku();
            $url_key = $product_data->getUrlKey();
        }

        /*         * ***************************************************** */
        $fieldset->addField('product_id', 'hidden', array(
            'name' => 'product_id',
            'value' => $deal->getProductId()));


        /*         * ***************************************************** */
        $fieldset->addField('cur_product', 'text', array(
            'name' => 'cur_product',
            'label' => Mage::helper('catalog')->__('Product Name'),
            'title' => Mage::helper('catalog')->__('Go to Product Selection tab to choose a different product.'),
            'required' => true,
            'readonly' => true,
            'class' => 'textbox-readonly',
            'value' => $product_data->getName(),
        ));

        /*         * ***************************************************** */
        $fieldset->addField('price', 'text', array(
            'name' => 'price',
            'label' => Mage::helper('catalog')->__('Product Price ') . Mage::app()->getStore()->getBaseCurrency()->getCode(),
            'title' => Mage::helper('catalog')->__('Standard product price'),
            'readonly' => true,
            'class' => 'textbox-readonly',
            'value' => round($product_data->getPrice(), 2),
        ));

        /*         * ***************************************************** */
        $fieldset->addField('product_qty', 'text', array(
            'name' => 'product_qty',
            'label' => Mage::helper('catalog')->__('Product Qty Available'),
            'title' => Mage::helper('catalog')->__('Total available quantity'),
            'readonly' => true,
            'class' => 'textbox-readonly',
            'value' => $product_data->getQty(),
        ));

        /*         * ***************************************************** */
        $fieldset->addField('curr_sym', 'hidden', array(
            'name' => 'curr_sym',
            'value' => Mage::app()->getStore()->getBaseCurrencyCode()));

        /*         * ***************************************************** */
        $fieldset->addField('curr_code', 'hidden', array(
            'name' => 'curr_code',
            'value' => Mage::app()->getStore()->getBaseCurrencyCode()));

        /*         * ***************************************************** */
        $fieldset->addField('product_sku', 'text', array(
            'name' => 'product_sku',
            'label' => Mage::helper('catalog')->__('Product Sku'),
            'readonly' => true,
            'class' => 'textbox-readonly',
            'value' => $sku,
            'after_element_html' => '<br />'
        ));

        /*         * ***************************************************** */
        $params = array(
            'name' => 'deal_price',
            'label' => Mage::helper('dailydeal')->__('Deal Price'),
            'class' => 'required-entry validate-number validate-greater-than-zero',
            'required' => true,
            'value' => number_format($deal->getDealPrice(), 2),
            'after_element_html' => "<br>Default discount is 15% from the regular product price",
        );

        if ($dealDisabled) {
            $params['disabled'] = $dealDisabled;
        }

        $fieldset->addField('deal_price', 'text', $params);

        /*         * ***************************************************** */
        $params = array(
            'name' => 'deal_qty',
            'label' => Mage::helper('dailydeal')->__('Deal Qty'),
            'class' => 'required-entry validate-number validate-greater-than-zero',
            'required' => true,
            'value' => $deal->getDealQty(),
            'after_element_html' => "<br />Quantity available at this price",
        );

        if ($dealDisabled) {
            $params['disabled'] = $dealDisabled;
        }
        $fieldset->addField('deal_qty', 'text', $params);


        /*         * ***************************************************** */
        $params = array(
            'name' => 'deal_start',
            'title' => "Start Date",
            'label' => "Start Date",
            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'time' => false,
            'class' => 'required-entry',
            'required' => true,
            'value' => $deal->getDealStart() ? $deal->getDealStart() : $this->getDateTomorrow()->format('Y-m-d'),
            'format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
            'readonly' => true,
        );

        if ($dealDisabled) {
            $params['disabled'] = $dealDisabled;
        }
        $fieldset->addField('start_date', 'date', $params);


        /********************************************************** */

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('dailydeal')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('dailydeal')->__('Enabled')
                ),
                array(
                    'value' => 2,
                    'label' => Mage::helper('dailydeal')->__('Disabled')
                )
            )
        ));


        return parent::_prepareForm();
    }

    protected function getDateTomorrow()
    {
        return date_add(date_create_from_format('U', time()), new DateInterval('P1D'));
    }

}
