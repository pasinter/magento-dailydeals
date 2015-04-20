<?php

/**
 * Webplanet
 *
 * @category    Webplanet
 * @package     Webplanet_Dailydeal
 * @author      Ken Golovin <ken@webplanet.co.nz>
 */

/**
 * BrowseBy Helper
 *
 * @category    Webplanet
 * @package     Webplanet_Dailydeal
 * @author      Ken Golovin <ken@webplanet.co.nz>
 */
class Webplanet_Dailydeal_Controller_Router extends Mage_Core_Controller_Varien_Router_Abstract
{

    /**
     * Matches the request URL. If matched, sets request
     * parameters for correct module/controller/action.
     *
     * @param Zend_Controller_Request_Http $request
     * @return boolean
     */
    public function match(Zend_Controller_Request_Http $request)
    {
        $urlPrefix = 'daily-deals';

        $identifier = trim($request->getPathInfo(), '/');
        $parts = explode('/', $identifier);



        if ($parts[0] != $urlPrefix) {

            return false;
        }

        $request->setModuleName('dailydeal')
                ->setControllerName('deals')
                ->setActionName('browse');

        $request->setAlias(
                Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS, $identifier
        );

        return true;
    }

}
