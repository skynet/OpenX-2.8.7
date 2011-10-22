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
$Id: init-delivery.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

/**
 * @package    OpenXDelivery
 * @author     Chris Nutting <chris.nutting@openx.org>
 * @author     Andrew Hill <andrew.hill@openx.org>
 * @author     Radek Maciaszek <radek.maciaszek@openx.org>
 *
 * A file to set up the environment for the OpenX delivery engine.
 *
 * Both opcode and PHP by itself slow things down when we require many
 * files. Therefore we gave up a little bit of maintainability in
 * order to speed up a delivery:
 * - We are not using classes (if possible) in delivery
 * - We have to use as few as possible includes and add new code into
 *   existing files
 */

require_once 'init-delivery-parse.php';
require_once 'memory.php';
require_once 'variables.php';

// Increase the PHP memory_limit value to the OpenX minimum required value, if necessery
OX_increaseMemoryLimit(OX_getMinimumRequiredMemory());

// PHP 5.3 compatibility
if (!defined('E_DEPRECATED')) {
    define('E_DEPRECATED', 0);
}

setupServerVariables();
setupDeliveryConfigVariables();
$conf = $GLOBALS['_MAX']['CONF'];

// Set this script's identifier (from the config file) in the global scope
$GLOBALS['_OA']['invocationType'] = array_search(basename($_SERVER['SCRIPT_FILENAME']), $conf['file']);

// Disable all notices and warnings,
// as some PAN code still generates PHP warnings in places
if (!empty($conf['debug']['production'])) {
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);
} else {
    // show all errors when developing
    error_reporting(E_ALL ^ E_DEPRECATED);
}

require_once MAX_PATH . '/lib/max/Delivery/common.php';
require_once MAX_PATH . '/lib/max/Delivery/cache.php';

OX_Delivery_logMessage('starting delivery script: ' . basename($_SERVER['REQUEST_URI']), 7);
if (!empty($_REQUEST[$conf['var']['trace']])) {
    OX_Delivery_logMessage('trace enabled: ' . $_REQUEST[$conf['var']['trace']], 7);
}

// Set the viewer's remote information used in logging and delivery limitation evaluation
MAX_remotehostSetInfo();

// Set common delivery parameters in the global scope
MAX_commonInitVariables();

// Load cookie data from client/plugin
MAX_cookieLoad();

// Unpack the packed capping cookies
MAX_cookieUnpackCapping();

// Run any plugins which have registered themselves at postInit,
// with the exception of XML-RPC invocation that will call the hook
// explicitly after overriding the $_SERVER variables
if (empty($GLOBALS['_OA']['invocationType']) || $GLOBALS['_OA']['invocationType'] != 'xmlrpc') {
    OX_Delivery_Common_hook('postInit');
}

?>
