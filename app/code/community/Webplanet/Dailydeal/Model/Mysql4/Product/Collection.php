<?php

/**
 * Webplanet
 *
 * @category    Webplanet
 * @package     Webplanet_Dailydeal
 * @author      Ken Golovin <ken@webplanet.co.nz>
 */

/**
 * Daily deal products collection
 *
 * @category    Webplanet
 * @package     Webplanet_Dailydeal
 * @author      Ken Golovin <ken@webplanet.co.nz>
 */
class Webplanet_Dailydeal_Model_Mysql4_Product_Collection extends Mage_Reports_Model_Mysql4_Product_Collection
{

    /**
     *
     * @param string|Zend_Date $date
     * @return Webplanet_Dailydeal_Model_Mysql4_Product_Collection
     */
    public function addDealDateFilter($date)
    {
        $condition = array('eq' => $date);



        $this->addAttributeToFilter('dailydeal_date', $condition);

        return $this;
    }

    public function addIsAvailableFilter()
    {
        
    }

}
