<?php
/**
 * 
 *
 * @author      Ken Golovin <ken@webplanet.co.nz>
 */

class Webplanet_Dailydeal_Model_Resource_Eav_Mysql4_Setup extends Mage_Eav_Model_Entity_Setup
{

  /**
   * @return array
   *
   * @link http://www.magentocommerce.com/wiki/5_-_modules_and_development/0_-_module_development_in_magento/installing_custom_attributes_with_your_module
   */
  public function getDefaultEntities()
  {
      return array(
          
          'catalog_product' => array(
              'entity_model'      => 'catalog/product',
              'attribute_model'   => 'catalog/resource_eav_attribute',
              'table'             => 'catalog/product',
              'additional_attribute_table' => 'catalog/eav_attribute',
              'entity_attribute_collection' => 'catalog/product_attribute_collection',
              'attributes'        => array(
                  'dailydeal_date' => array(
                      'label'             => 'Deal Start Date',
                      'type'              => 'datetime',
                      'input'             => 'date',
                      'default'           => null,
                      'class'             => null,
                      'source'            => null,
                      'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                      'visible'           => false,
                      'required'          => false,
                      'user_defined'      => false,
                      'searchable'        => false,
                      'filterable'        => false,
                      'comparable'        => false,
                      'visible_on_front'  => false,
                      'visible_in_advanced_search' => false,
                      'unique'            => false
                  ),

                  'dailydeal_product_price' => array(
                      'label'             => 'Original Product Price',
                      'type'              => 'decimal',
                      'input'             => 'price',
                      'default'           => null,
                      'class'             => '',
                      'backend'           => '',
                      'frontend'          => '',
                      'source'            => '',
                      'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                      'visible'           => false,
                      'required'          => false,
                      'user_defined'      => false,
                      'searchable'        => false,
                      'filterable'        => false,
                      'comparable'        => false,
                      'visible_on_front'  => false,
                      'visible_in_advanced_search' => false,
                      'unique'            => false
                  ),
                  
                  'dailydeal_price' => array(
                      'label'             => 'Price',
                      'type'              => 'decimal',
                      'input'             => 'price',
                      'default'           => null,
                      'class'             => '',
                      'backend'           => '',
                      'frontend'          => '',
                      'source'            => '',
                      'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
                      'visible'           => false,
                      'required'          => false,
                      'user_defined'      => false,
                      'searchable'        => false,
                      'filterable'        => false,
                      'comparable'        => false,
                      'visible_on_front'  => false,
                      'visible_in_advanced_search' => false,
                      'unique'            => false
                  )


             )
         )
           
    );

  }
}