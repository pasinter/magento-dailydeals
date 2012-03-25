<?php
/**
 * Webplanet
 *
 * @category    Webplanet
 * @package     Webplanet_Dailydeal
 * @author      Ken Golovin <ken@webplanet.co.nz>
 */

/**
 * Deals Controller
 *
 * @category    Webplanet
 * @package     Webplanet_Dailydeal
 * @author      Ken Golovin <ken@webplanet.co.nz>
 */
class Webplanet_Dailydeal_DealsController extends Mage_Core_Controller_Front_Action
{
    /**
     * Browse action
     *
     * see http://blog.baobaz.com/en/blog/magento-module-create-your-own-controller
     */
    public function browseAction()
    {
        // works
      
        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();       
    }

    public function getCollection()
    {
      // dailydeal_date
      // dailydeal_qty_available
      // dailydeal_price


    }
}