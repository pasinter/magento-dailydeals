<?php
/**
 * 
 *
 * @author      Ken Golovin <ken@webplanet.co.nz>
 */


$installer = $this;

$installer->startSetup();

$installer->installEntities();

$installer->run("
	CREATE TABLE IF NOT EXISTS `" . $this->getTable('webplanet_dailydeal_deal') . "` (
	  `deal_id` INT(11) NOT NULL AUTO_INCREMENT,
	  `product_id` INT(11) DEFAULT NULL,
      
      `short_description` varchar(255) DEFAULT NULL,
      `description` varchar(255) DEFAULT NULL,
      `deal_start` datetime DEFAULT NULL,
      `deal_end` datetime DEFAULT NULL,
      
      `deal_price`  DECIMAL(12,4) NULL,
      
      `deal_qty`    INT(11) DEFAULT '0',
      `deal_qty_sold`    INT(11) DEFAULT '0',
      
      `limit_per_customer`    INT(11) DEFAULT '1',
      `limit_minimum_bought`    INT(11) DEFAULT '0',
      
      `disable_display_frontend`  TINYINT(1) NOT NULL DEFAULT '0',
      `is_enabled`  TINYINT(1) NOT NULL DEFAULT '1',

      `qty_sold`    INT(11) DEFAULT '0',
      `product_views`       INT(11) DEFAULT '0',

      `status`      TINYINT(1) NOT NULL DEFAULT '1',

      `updated_at` datetime DEFAULT NULL,
      `created_at` datetime DEFAULT NULL,
	  PRIMARY KEY (`deal_id`),
	  KEY `PRODUCT` (`product_id`)
	) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

	");


/*
$installer->addAttribute('catalog_product', 'dailydeal_date', array(
        'backend'       => 'eav/entity_attribute_backend_datetime',
        'frontend'      => '',
        'label'         => 'Allow Gift Message',
        'input'         => 'select',
        'class'         => '',
        'source'        => 'giftmessage/entity_attribute_source_boolean_config',
        'global'        => true,
        'visible'       => true,
        'required'      => false,
        'user_defined'  => false,
        'default'       => '2',
        'visible_on_front' => false
    ));
*/

$installer->endSetup();
