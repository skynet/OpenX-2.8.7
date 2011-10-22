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
$Id: variables.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

/**
 * @package    OpenX
 * @author     Chris Nutting <chris.nutting@openx.org>
 * @author     Andrew Hill <andrew.hill@openx.org>
 * @author     Radek Maciaszek <radek.maciaszek@openx.org>
 *
 * A file to set up the environment for the OpenX delivery engine.
 *
 * Both opcode and PHP by itself slow things down when we require many
 * files. Therefore maintainability has been sacrificed in order to
 * speed up a delivery:
 * - We are not using classes (if possible) in delivery;
 * - We have as few as possible includes and add new code into
 *   existing files.
 */

/**
 * Setup common variables - used by both delivery and admin part as well
 *
 * This function should be executed after the config file is read in.
 *
 * The reason behind using GLOBAL variables is that
 * there are faster than constants
 */
function setupConfigVariables()
{
    $GLOBALS['_MAX']['MAX_DELIVERY_MULTIPLE_DELIMITER'] = '|';
    $GLOBALS['_MAX']['MAX_COOKIELESS_PREFIX'] = '__';
    $GLOBALS['_MAX']['thread_id'] = uniqid();

    // Set a flag if this request was made over an SSL connection (used more for delivery rather than UI)
    $GLOBALS['_MAX']['SSL_REQUEST'] = false;
    if (
        (!empty($_SERVER['SERVER_PORT']) && ($_SERVER['SERVER_PORT'] == $GLOBALS['_MAX']['CONF']['openads']['sslPort'])) ||
        (!empty($_SERVER['HTTPS']) && ((strtolower($_SERVER['HTTPS']) == 'on') || ($_SERVER['HTTPS'] == 1))) ||
        (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https')) ||
        (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && (strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) == 'on')) ||
        (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && (strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) == 'on')) ||
        (!empty($_SERVER['FRONT-END-HTTPS']) && (strtolower($_SERVER['FRONT-END-HTTPS']) == 'on'))
    ) {
        // This request should be treated as if it was received over an SSL connection
        $GLOBALS['_MAX']['SSL_REQUEST'] = true;
    }

    // Maximum random number (use default if doesn't exist - eg the case when application is upgraded)
    $GLOBALS['_MAX']['MAX_RAND'] = isset($GLOBALS['_MAX']['CONF']['priority']['randmax']) ?
        $GLOBALS['_MAX']['CONF']['priority']['randmax'] : 2147483647;

	list($micro_seconds, $seconds) = explode(" ", microtime());
	$GLOBALS['_MAX']['NOW_ms'] = round(1000 *((float)$micro_seconds + (float)$seconds));

    // Always use UTC when outside the installer
    if (substr($_SERVER['SCRIPT_NAME'], -11) != 'install.php') {
        // Save server timezone for auto-maintenance
        $GLOBALS['serverTimezone'] = date_default_timezone_get();
        OA_setTimeZoneUTC();
    }
}

/**
 * A function to initialize $_SERVER variables which could be missing
 * on some environments
 *
 */
function setupServerVariables()
{
    // PHP-CGI/IIS combination does not set REQUEST_URI
    if (empty($_SERVER['REQUEST_URI'])) {
        $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
        if (!empty($_SERVER['QUERY_STRING'])) {
            $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
        }
    }
}

/**
 * A function to initialize the environmental constants and global
 * variables required by delivery.
 */
function setupDeliveryConfigVariables()
{
    if (!defined('MAX_PATH')) {
        define('MAX_PATH', dirname(__FILE__));
    }
    if (!defined('OX_PATH')) {
        define('OX_PATH', MAX_PATH);
    }
    if (!defined('LIB_PATH')) {
        define('LIB_PATH', MAX_PATH. DIRECTORY_SEPARATOR. 'lib'. DIRECTORY_SEPARATOR. 'OX');
    }
    // Ensure that the initialisation has not been run before
    if ( !(isset($GLOBALS['_MAX']['CONF']))) {
        // Parse the Max configuration file
        $GLOBALS['_MAX']['CONF'] = parseDeliveryIniFile();
    }

    // Set up the common configuration variables
    setupConfigVariables();
}

/**
 * Set a timezone
 *
 * @param string $timezone
 */
function OA_setTimeZone($timezone)
{
    // Set the new time zone
    date_default_timezone_set($timezone);

    // Set PEAR::Date_TimeZone default as well
    //
    // Ideally this should be a Date_TimeZone::setDefault() call, but for optimization
    // purposes, we just override the global variable
    $GLOBALS['_DATE_TIMEZONE_DEFAULT'] = $timezone;
}

/**
 * Set the current default timezone to UTC
 *
 * @see OA_setTimeZone()
 */
function OA_setTimeZoneUTC()
{
    OA_setTimeZone('UTC');
}

/**
 * Set the current default timezone to local
 *
 * @see OA_setTimeZone()
 */
function OA_setTimeZoneLocal()
{
    $tz = !empty($GLOBALS['_MAX']['PREF']['timezone']) ? $GLOBALS['_MAX']['PREF']['timezone'] : 'GMT';
    OA_setTimeZone($tz);
}

/**
 * Returns the hostname the script is running under.
 *
 * @return string containing the hostname (with port number stripped).
 */
function OX_getHostName()
{
    if (!empty($_SERVER['HTTP_HOST'])) {
        $host = explode(':', $_SERVER['HTTP_HOST']);
        $host = $host[0];
    } else if (!empty($_SERVER['SERVER_NAME'])) {
        $host = explode(':', $_SERVER['SERVER_NAME']);
    	$host = $host[0];
    }
    return $host;
}

/**
 * Returns the hostname (with port) the script is running under.
 *
 * @return string containing the hostname with port
 */
function OX_getHostNameWithPort()
{
    if (!empty($_SERVER['HTTP_HOST'])) {
        $host = $_SERVER['HTTP_HOST'];
    } else if (!empty($_SERVER['SERVER_NAME'])) {
    	$host = $_SERVER['SERVER_NAME'];
    }
    return $host;
}

/**
 * A function to define the PEAR include path in a separate method,
 * as it is required by delivery only in exceptional circumstances.
 */
function setupIncludePath()
{
    static $checkIfAlreadySet;
    if (isset($checkIfAlreadySet)) {
        return;
    }
    $checkIfAlreadySet = true;

    $oxPearPath = MAX_PATH . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'pear';
    $oxZendPath = MAX_PATH . DIRECTORY_SEPARATOR . 'lib';

    set_include_path($oxPearPath . PATH_SEPARATOR . $oxZendPath . PATH_SEPARATOR . get_include_path());
}

?>