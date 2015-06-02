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
class Webplanet_Dailydeal_Block_Adminhtml_Deal_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('dailydealGrid');
        $this->setDefaultSort('deal_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('dailydeal/deal')->getCollection();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection()
    {
        foreach ($this->getCollection() as $deal) {
            // load product
            $product = $deal->getProduct();

            if ($product) {
                foreach ($product->getData() as $product_key => $product_value) {
                    $deal->setData('product_' . $product_key, $product_value);
                }
            }
        }

        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('deal_id', array(
            'header' => Mage::helper('dailydeal')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'deal_id',
        ));

        $this->addColumn('product_name', array(
            'header' => Mage::helper('dailydeal')->__('Name'),
            'align' => 'left',
            'index' => 'product_name',
        ));

        $this->addColumn('product_sku', array(
            'header' => Mage::helper('dailydeal')->__('SKU'),
            'align' => 'left',
            'index' => 'product_sku',
        ));

        $this->addColumn('deal_qty', array(
            'header' => Mage::helper('dailydeal')->__('Quantity'),
            'align' => 'left',
            'index' => 'deal_qty',
        ));

        $this->addColumn('deal_price', array(
            'header' => Mage::helper('dailydeal')->__('Deal Price'),
            'align' => 'left',
            'index' => 'deal_price',
        ));

        $this->addColumn('deal_start', array(
            'header' => Mage::helper('dailydeal')->__('Start Date'),
            'align' => 'left',
            'index' => 'deal_start',
        ));


        $model = Mage::getModel('dailydeal/deal');



        $this->addColumn('status', array(
            'header' => Mage::helper('dailydeal')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                $model::STATUS_SCHEDULED => Mage::helper('dailydeal')->__('Scheduled'),
                $model::STATUS_RUNNING => Mage::helper('dailydeal')->__('Running'),
                $model::STATUS_ENDED => Mage::helper('dailydeal')->__('Ended'),
            ),
        ));


        $this->addColumn('action', array(
            'header' => Mage::helper('dailydeal')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getDealId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('dailydeal')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                ),
                array(
                    'caption' => Mage::helper('dailydeal')->__('Delete'),
                    'url' => array('base' => '*/*/delete'),
                    'field' => 'id',
                    'confirm' => 'Deal will be deleted, are you sure?'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * The purpose of this override is to disable sorting by calculated columns (ex: "phase") so as.
     * to avoid exceptions thrown by Magento's DAL.
     * Here we examine the incoming HTTP request and if it contains a param for sorting by phase/sku,
     * we delete that and also the sort direction param.
     *
     * @todo Properly implement sorting by "phase" and "sku" lower down the grid lifecycle, and remove
     * this code.
     */
    protected function _preparePage()
    {
        $paramName = $this->getVarNameSort();
        if ($this->getRequest()->has($this->getVarNameSort())) {
            $param = $this->getRequest()->getParam($this->getVarNameSort());
            if (($param == "phase") || ($param == "sku")) {
                $this->getRequest()->setParam($this->getVarNameSort(), null);
                $this->getRequest()->setParam($this->getVarNameDir(), null);
            }
        }
        parent::_preparePage();
    }

    /**
     * We override _setFilterValues here to handle calculated columns (ex: "phase").
     * Currently we simply ignore the filter for these columns.
     *
     * @todo Translate filters from calculated columns to proper columns (that appear in the DB)
     */
    protected function _setFilterValues($data)
    {
        unset($data['sku']);
        return parent::_setFilterValues($data);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getDealId()));
    }

}
