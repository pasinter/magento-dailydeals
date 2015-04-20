<?php

class Webplanet_Dailydeal_Block_Adminhtml_Deal_Edit_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('product_selection');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
        $this->setVarNameFilter('product_filter');
        $this->setRowClickCallback("dealAdmin.onProductSelect");
    }

    protected function _beforeToHtml()
    {
        $this->setId($this->getId() . '_' . $this->getIndex());
        $this->getChild('reset_filter_button')->setData('onclick', $this->getJsObjectName() . '.resetFilter()');
        $this->getChild('search_button')->setData('onclick', $this->getJsObjectName() . '.doFilter()');

        return parent::_beforeToHtml();
    }

    public function getHtml()
    {
        $html = parent::getHtml();
        
        $collection = $this->getCollection();
        $extra_data = array();
        $items = $collection->getItems();
        foreach ($items as $item) {
            $extra_data[$item->getEntityId()] = array(
                "id" => $item->getId(),
                "name" => $item->getName(),
                "sku" => $item->getSku(),
                "qty" => $item->getQty(),
                "url_key" => $item->getUrlKey(),
                "desc" => $item->getDescription(),
                "meta_desc" => $item->getMetaDescription(),
                "small_img" => Mage::getBaseUrl('media') . 'catalog/product' . $item->getSmallImage(),
                "price" => round($item->getPrice(), 2),
                "category_ids" => implode(',', $item->getCategoryIds()),
                //"url"		=>	Mage::getModel('catalog/product')->load($item->getId())->getUrlPath(),
                "curr_sym" => Mage::app()->getStore()->getBaseCurrencyCode(),
                "curr_code" => Mage::app()->getStore()->getBaseCurrencyCode()
            );
        }

        $json = json_encode($extra_data);

        return sprintf("<script type='text/javascript'>
            var DailydealAdmin = Class.create({
                initialize: function() {
                    this.gridProducts = [];
                },
                getDealForm: function()
                {
                    return $('edit_form');
                },
                extractProductDataFromGridRow: function(productRow)
                {
                    var productData = {};
                    productData['id'] = productRow.select('td')[0].innerHTML.trim();
                    productData['name'] = productRow.select('td')[1].innerHTML.trim();
                    productData['sku'] = productRow.select('td')[2].innerHTML.trim();
                    productData['price'] = productRow.select('td')[3].innerHTML.trim().replace(/[^0-9\.]+/i, '');
                    productData['qty'] = productRow.select('td')[4].innerHTML.trim();
              
                    
                    return productData;
                },
                onProductSelect: function(grid, event){
                    console.log(event);

                    var selectedProductRow = Event.findElement(event, 'tr');
                    
                    var productData = dealAdmin.extractProductDataFromGridRow(selectedProductRow);
                    console.log(productData);

                    // id
                    dealAdmin.setDealFormValue('product_id', productData['id']);

                    // product name
                    dealAdmin.setDealFormValue('cur_product', productData['name']);

                    // price
                    dealAdmin.setDealFormValue('price', productData['price']);
                    dealAdmin.setDealFormValue('deal_price', Math.round(productData['price'] * 0.85 * 100) / 100);

                    // qty
                    dealAdmin.setDealFormValue('product_qty', Math.round(productData['qty']));
                    dealAdmin.setDealFormValue('deal_qty', Math.round(productData['qty']));

                    // sku
                    dealAdmin.setDealFormValue('product_sku', productData['sku']);

                    // switch to Config tab
                    dailydeal_tabsJsTabs.showTabContent(document.getElementById('dailydeal_tabs_conf_section'));
                },

                setDealFormValue: function(name, value) {
                    this.getDealForm().getInputs(false, name).first().writeAttribute('value', value);
                }
            });

            var dealAdmin = new DailydealAdmin();

            </script>", $json) . $html;
    }

    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField('websites', 'catalog/product_website', 'website_id', 'product_id=entity_id', null, 'left');
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }

    protected function _prepareCollection()
    {

        $prod_types = array();
        $prod_types[] = 'bundle';
        $prod_types[] = 'configurable';
        $prod_types[] = 'simple';

        $collection = Mage::getModel('catalog/product')->getCollection()
                ->setStore($this->_getStore())
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('small_image')
                ->addAttributeToSelect('meta_description')
                ->addAttributeToSelect('description')
                ->addAttributeToSelect('category_ids')
                ->addAttributeToSelect('sku')
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('price_type')
                ->addAttributeToSelect('url_key')
                ->addAttributeToSelect('type_id')
                ->addAttributeToSelect('attribute_set_id')
                ->addFieldToFilter('visibility', array('gt' => '1'))
                ->addFieldToFilter('type_id', array('in' => $prod_types))
                ->joinField('qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left');

        $store = $this->_getStore();
        if ($store->getId()) {
            //$collection->setStoreId($store->getId());
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter($store);
            $collection->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner', $adminStore);
            $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
            $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
        } else {
            $collection->addAttributeToSelect('price');
            $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
            $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
        }

        $this->setCollection($collection);

        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();

        return $this;
    }

    protected function _afterLoadCollection()
    {
        // Filter out any bundled products that have a dynamic price (We don't handle those yet):
        // @todo: Do this earlier in the flow so collection paging and totals reflect that we removed products!
        $collection = $this->getCollection();
        $keys_to_remove = array();
        foreach ($collection as $key => $product) {
            if (($product->getTypeId() == "bundle") &&
                    ($product->getPriceType() == 0)) {
                $keys_to_remove[] = $key;
            }
        }
        foreach ($keys_to_remove as $key) {
            $collection->removeItemByKey($key);
        }
    }

    protected function _prepareColumns()
    {

        $this->addColumn('prd_entity_id', array(
            'header' => Mage::helper('sales')->__('ID'),
            'sortable' => true,
            'width' => '60px',
            'index' => 'entity_id'
        ));

        $this->addColumn('prd_name', array(
            'header' => Mage::helper('sales')->__('Product Name'),
            'index' => 'name',
            'column_css_class' => 'name'
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
                ->load()
                ->toOptionHash();

        $this->addColumn('prd_sku', array(
            'header' => Mage::helper('sales')->__('SKU'),
            'width' => '80px',
            'index' => 'sku',
            'column_css_class' => 'sku'
        ));

        /*
          $this->addColumn('type_id', array(
          'header'    => Mage::helper('sales')->__('type_id'),
          'width'     => '80px',
          'index'     => 'type_id',
          'column_css_class'=> 'type_id'
          ));
         */


        $this->addColumn('prd_price', array(
            'header' => Mage::helper('sales')->__('Price'),
            'align' => 'center',
            'type' => 'currency',
            'currency_code' => $this->_getStore()->getCurrentCurrencyCode(),
            'rate' => $this->_getStore()->getBaseCurrency()->getRate($this->_getStore()->getCurrentCurrencyCode()),
            'index' => 'price'
        ));

        $this->addColumn('prd_qty', array(
            'header' => Mage::helper('catalog')->__('Qty'),
            'width' => '100px',
            'type' => 'number',
            'index' => 'qty',
        ));

        $this->addColumn('prd_visibility', array(
            'header' => Mage::helper('catalog')->__('Visibility'),
            'width' => '70px',
            'index' => 'visibility',
            'type' => 'options',
            'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
        ));

        $this->addColumn('prd_status', array(
            'header' => Mage::helper('catalog')->__('Status'),
            'width' => '70px',
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('websites', array(
                'header' => Mage::helper('catalog')->__('Websites'),
                'width' => '100px',
                'sortable' => false,
                'index' => 'websites',
                'type' => 'options',
                'options' => Mage::getModel('core/website')->getCollection()->toOptionHash(),
            ));
        }

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {

        return $this;
    }

    public function getGridUrl()
    {
        // This will invoke a call to the controller object DailydealController.php to
        // function name gridProductAction
        $ret = $this->getUrl('dailydeal/adminhtml_dailydeal/gridProduct', array(
            'index' => $this->getIndex(),
            '_current' => true,
        ));
        return $ret;
    }

    protected function _getStore()
    {
        return Mage::app()->getStore($this->getRequest()->getParam('store'));
    }

}
