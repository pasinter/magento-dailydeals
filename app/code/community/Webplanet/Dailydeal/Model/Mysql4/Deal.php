<?php

class Webplanet_Dailydeal_Model_Mysql4_Deal extends Mage_Core_Model_Mysql4_Abstract
{

    public function _construct()
    {
        // Note that the deal_id refers to the key field in your database table.
        $this->_init('dailydeal/deal', 'deal_id');
    }

}
