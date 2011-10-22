<?php

/*
+---------------------------------------------------------------------------+
| OpenX v2.8                                                                |
| ==========                                                                |
|                                                                           |
| Copyright (c) 2003-2009 OpenX Limited                                     |
| For contact details, see: http://www.openx.org/                           |
|                                                                           |
| This program is free software; you can redistribute it and/or modify      |
| it under the terms of the GNU General Public License as published by      |
| the Free Software Foundation; either version 2 of the License, or         |
| (at your option) any later version.                                       |
|                                                                           |
| This program is distributed in the hope that it will be useful,           |
| but WITHOUT ANY WARRANTY; without even the implied warranty of            |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
| GNU General Public License for more details.                              |
|                                                                           |
| You should have received a copy of the GNU General Public License         |
| along with this program; if not, write to the Free Software               |
| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
+---------------------------------------------------------------------------+
$Id: Factory.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

/**
 * A factory class to create instances of classes that implement the
 * OA_Admin_Statistics_Common "interface".
 *
 * @package    OpenXAdmin
 * @subpackage Statistics
 * @author     Matteo Beccati <matteo@beccati.com>
 * @author     Andrew Hill <andrew.hill@openx.org>
 */
class OA_Admin_Statistics_Factory
{

    /**
     *  Create a new object of the appropriate OA_Admin_Statistics_Common subclass.
     *
     * @static
     * @param string $controllerType The controller type (e.g. "global-advertiser").
     * @param array  $aParams        An array of parameters to be passed as the parameter
     *                               to the constructor of the class instantiated.
     * @return OA_Admin_Statistics_Common The instantiated class that inherits from
     *                                    OA_Admin_Statistics_Common.
     */
    function &getController($controllerType = '', $aParams = null)
    {
        // Instantiate & return the required statistics class
        $result = OA_Admin_Statistics_Factory::_getControllerClass($controllerType, $aParams, $class, $file);
        if (PEAR::isError($result)) {
            return $result;
        }
        // To allow catch errors and pass it out without calling error handler
        PEAR::pushErrorHandling(null);
        $oStatsController = OA_Admin_Statistics_Factory::_instantiateController($file, $class, $aParams);
        PEAR::popErrorHandling();
        
        $marketPluginEnabled = ($GLOBALS['_MAX']['CONF']['plugins']['openXMarket'] ? true : false);
        if($marketPluginEnabled) {
            require_once MAX_PATH . '/www/admin/market/stats.php';
            $oStatsController->addPlugin('openxMarket', new OX_oxMarket_Stats());
        }
        return $oStatsController;
    }

    function _instantiateController($file, $class, $aParams = null)
    {
        if (!@include_once $file)
        {
            $errMsg = "OA_Admin_Statistics_Factory::_instantiateController() Unable to locate " . basename($file);
            return MAX::raiseError($errMsg, MAX_ERROR_INVALIDARGS);
        }
        if (!class_exists($class)) {
            $errMsg = "OA_Admin_Statistics_Factory::_instantiateController() Class {$class} doesn't exist";
            return MAX::raiseError($errMsg, MAX_ERROR_INVALIDARGS);
        }
        $oController = new $class($aParams);
        return $oController;
    }

    function _getControllerClass($controllerType = '', $aParams = null, &$class, &$file)
    {
        if (!is_array($aParams)) {
            $aParams = array();
        }

        if (empty($controllerType) || $controllerType == '-')
        {
            $controllerType = basename($_SERVER['SCRIPT_NAME']);
            $controllerType = preg_replace('#^(?:stats-)?(.*)\.php#', '$1', $controllerType);
        }

        // Validate
        if (!preg_match('/^[a-z-]+$/Di', $controllerType)) {
            $errMsg = "OA_Admin_Statistics_Factory::_getControllerClass() Unsupported controller type";
            return MAX::raiseError($errMsg, MAX_ERROR_INVALIDARGS, PEAR_ERROR_RETURN);
        }

        // Prepare the strings required to generate the file and class names
        list($primary, $secondary) = explode('-', $controllerType, 2);
        $primary = ucfirst(strtolower($primary));
        $aSecondary = explode('-', $secondary);
        foreach ($aSecondary as $key => $string) {
            $aSecondary[$key] = ucfirst(strtolower($string));
        }

        $file = MAX_PATH . '/lib/OA/Admin/Statistics/Delivery/Controller/';
        $file .= $primary;
        foreach ($aSecondary as $string) {
            $file .= $string;
        }
        $file .= '.php';
        $class = 'OA_Admin_Statistics_Delivery_Controller_';
        $class .= $primary;
        foreach ($aSecondary as $string) {
            $class .= $string;
        }
    }


}

?>