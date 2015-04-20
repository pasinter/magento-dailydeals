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
class Webplanet_Dailydeal_IndexController extends Mage_Core_Controller_Front_Action
{

    /**
     * Browse action
     *
     * see http://blog.baobaz.com/en/blog/magento-module-create-your-own-controller
     */
    public function indexAction()
    {
        // works

        $this->loadLayout();
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('checkout/session');
        $this->renderLayout();
    }

}
