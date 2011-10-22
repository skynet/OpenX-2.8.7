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
$Id: constants.php 61063 2010-09-02 23:58:37Z chris.nutting $
*/

/**
 * @package    OpenX
 * @author     Andrew Hill <andrew.hill@openx.org>
 *
 * A file to set up the environment constants for Openads.
 */

/**
 * The environmental constants initialisation function for Openads.
 */
function setupConstants()
{
    // Define this version of Openads's constants
    define('OA_VERSION', '2.8.7');
    define('MAX_PRODUCT_NAME',      'OpenX');
    define('MAX_PRODUCT_URL',       'www.openx.org');
    if (preg_match('/^(\d+\.\d+)/', OA_VERSION, $aMatches)) {
        define('OX_PRODUCT_DOCSURL',   "http://www.openx.org/help/{$aMatches[1]}");
    } else {
        define('OX_PRODUCT_DOCSURL',   "http://www.openx.org/help");
    }
    define('OX_PRODUCT_FORUMURL',  'forum.openx.org');

    // PHP 5.3 compatibility
    if (!defined('E_DEPRECATED')) {
        define('E_DEPRECATED', 0);
    }

    define('OA_INSTALLATION_STATUS_NOTINSTALLED' ,   -1);
    define('OA_INSTALLATION_STATUS_UPGRADING'    ,    0);
    define('OA_INSTALLATION_STATUS_INSTALLED'    ,    1);

    define('OA_AD_DIRECT_ENABLED', false);

    //campaign types
    define('OX_CAMPAIGN_TYPE_REMNANT', 1);
    define('OX_CAMPAIGN_TYPE_CONTRACT_NORMAL', 2);
    define('OX_CAMPAIGN_TYPE_CONTRACT_EXCLUSIVE', 3);
    define('OX_CAMPAIGN_TYPE_ECPM', 4);
    define('OX_CAMPAIGN_TYPE_CONTRACT_ECPM', 5);

    // Database connection constants
    define('MAX_DSN_ARRAY',                 0);
    define('MAX_DSN_STRING',                1);
    $GLOBALS['_OA']['CONNECTIONS'] =        array();

    // Error codes to use with MAX::raiseError()
    // Start at -100 in order not to conflict with PEAR::DB error codes
    define('MAX_ERROR_INVALIDARGS',         -101);  // Wrong args to function
    define('MAX_ERROR_INVALIDCONFIG',       -102);  // Something wrong with the config
    define('MAX_ERROR_NODATA',              -103);  // No data available
    define('MAX_ERROR_NOCLASS',             -104);  // No class exists
    define('MAX_ERROR_NOMETHOD',            -105);  // No method exists
    define('MAX_ERROR_NOAFFECTEDROWS',      -106);  // No rows where affected by update/insert/delete
    define('MAX_ERROR_NOTSUPPORTED'  ,      -107);  // Limit queries on unsuppored databases
    define('MAX_ERROR_INVALIDCALL',         -108);  // Overload getter/setter failure
    define('MAX_ERROR_INVALIDAUTH',         -109);
    define('MAX_ERROR_EMAILFAILURE',        -110);
    define('MAX_ERROR_DBFAILURE',           -111);
    define('MAX_ERROR_DBTRANSACTIONFAILURE',-112);
    define('MAX_ERROR_BANNEDUSER',          -113);
    define('MAX_ERROR_NOFILE',              -114);
    define('MAX_ERROR_INVALIDFILEPERMS',    -115);
    define('MAX_ERROR_INVALIDSESSION',      -116);
    define('MAX_ERROR_INVALIDPOST',         -117);
    define('MAX_ERROR_INVALIDTRANSLATION',  -118);
    define('MAX_ERROR_FILEUNWRITABLE',      -119);
    define('MAX_ERROR_INVALIDMETHODPERMS',  -120);
    define('MAX_ERROR_INVALIDREQUEST',      -121);
    define('MAX_ERROR_INVALIDTYPE',         -122);
    define('MAX_ERROR_INVALIDOPERATIONINT', -123);
    define('MAX_ERROR_INVALIDBANNERSIZE',   -124);
    define('MAX_ERROR_INVALIDBANNERTYPE',   -125);
    define('MAX_ERROR_ALREADYLINKED',       -126);
    define('MAX_ERROR_EXISTINGCAMPAIGNFORDATES',-127);
    define('MAX_ERROR_EMAILNODATES',-128);

    // Ad/Zone association link types
    define('MAX_AD_ZONE_LINK_DIRECT',   0);
    define('MAX_AD_ZONE_LINK_NORMAL',   1);
    define('MAX_AD_ZONE_LINK_CATEGORY', 2);

    // URL construction constants
    define('MAX_URL_ADMIN',          0);
    define('MAX_URL_IMAGE',          1);
    define('MAX_URL_DEL_SCRIPT',     2);
    define('MAX_URL_DEL_SCRIPT_SSL', 3);
    define('MAX_URL_DEL_IMG',        4);
    define('MAX_URL_DEL_IMG_SSL',    5);

    // Connection action type constants
    define('MAX_CONNECTION_AD_IMPRESSION',      0);
    define('MAX_CONNECTION_AD_CLICK',           1);
    define('MAX_CONNECTION_AD_ARRIVAL',         2);
    define('MAX_CONNECTION_MANUAL',             4);

    // Campaign deactivation reasons
    // Values must be x^2, as used as binary flags
    define('OX_CAMPAIGN_DISABLED_IMPRESSIONS',  2);
    define('OX_CAMPAIGN_DISABLED_CLICKS',       4);
    define('OX_CAMPAIGN_DISABLED_CONVERSIONS',  8);
    define('OX_CAMPAIGN_DISABLED_DATE',        16);


    // Time contstants
    define('SECONDS_PER_WEEK', 604800);
    define('MINUTES_PER_WEEK', 10080);
    define('SECONDS_PER_DAY',  86400);
    define('SECONDS_PER_HOUR', 3600);
    define('MINUTES_PER_DAY',  1440);

    // Connection status types. Only the default conversion
    // status (MAX_CONNECTION_STATUS_APPROVED) is defined (4), which
    // is a conversion type that is approved by default.
    // Note also that only ignore, pending and approved
    // are used as possible starting values (ie. in the
    // campaigns_trackers table.
    define('MAX_CONNECTION_STATUS_IGNORE',      1);
    define('MAX_CONNECTION_STATUS_PENDING',     2);
    define('MAX_CONNECTION_STATUS_ONHOLD',      3);
    define('MAX_CONNECTION_STATUS_APPROVED',    4);
    define('MAX_CONNECTION_STATUS_DISAPPROVED', 5);
    define('MAX_CONNECTION_STATUS_DUPLICATE',   6);

    // Connection action type constants
    define('MAX_CONNECTION_TYPE_SALE',   1);
    define('MAX_CONNECTION_TYPE_LEAD',   2);
    define('MAX_CONNECTION_TYPE_SIGNUP', 3);

    // Financial constants
    define('MAX_FINANCE_CPM',    1);
    define('MAX_FINANCE_CPC',    2);
    define('MAX_FINANCE_CPA',    3);
    define('MAX_FINANCE_MT',     4); // Monthly Tennancy
    define('MAX_FINANCE_RS',     5); // % Revenue split     (zone-only)
    define('MAX_FINANCE_BV',     6); // % Basket value      (zone-only)
    define('MAX_FINANCE_AI',     7); // Amount per item     (zone-only)
    define('MAX_FINANCE_ANYVAR', 8); // % of any variable   (zone-only)
    define('MAX_FINANCE_VARSUM', 9); // % of a variable sum (zone-only)

    // OAC communication
    define('OAC_RPC_TIMEOUT',   10); // RPC timeout to OAC in seconds

    $GLOBALS['_MAX']['STATUSES'] = array(
        MAX_CONNECTION_STATUS_IGNORE      => 'strStatusIgnore',       // start value
        MAX_CONNECTION_STATUS_PENDING     => 'strStatusPending',      // start value
        MAX_CONNECTION_STATUS_ONHOLD      => 'strStatusOnHold',
        MAX_CONNECTION_STATUS_APPROVED    => 'strStatusApproved',     // start value
        MAX_CONNECTION_STATUS_DISAPPROVED => 'strStatusDisapproved',
        MAX_CONNECTION_STATUS_DUPLICATE   => 'strStatusDuplicate',
    );

    $GLOBALS['_MAX']['CONN_TYPES'] = array(
        MAX_CONNECTION_TYPE_SALE   => 'strConnTypeSale',
        MAX_CONNECTION_TYPE_LEAD   => 'strConnTypeLead',
        MAX_CONNECTION_TYPE_SIGNUP => 'strConnTypeSignUp',
    );

    // IP Address used to determine which (if any) MaxMind databases are installed
    define('MAX_MIND_TEST_IP', '24.24.24.24');

    // Maximum random number
    define('MAX_RAND',     mt_getrandmax());
    define('MAX_RAND_INV', 1 / MAX_RAND);

    // Maintenance Engine Constants: Number of days to keep old maintenance
    // data, before pruning, where a fixed retention value is appriopriate
    define('OA_MAINTENANCE_FIXED_PRUNING', 30);

    // LegalAgreement Plugin Types
    define('LEGALAGREEMENT_PLUGIN_PRE',         0);
    define('LEGALAGREEMENT_PLUGIN_POST_DENY',   1);
    define('LEGALAGREEMENT_PLUGIN_POST_ACCEPT', 2);

    define('MAX_LIMITATION_EQUAL', 0);
    define('MAX_LIMITATION_NOT_EQUAL', 1);
    define('MAX_LIMITATION_BITWISE', 2);

    // Define the week to start on Sunday (0) so that the PEAR::Date and
    // PEAR::Date_Calc classes agree on what day is the start of the week
    define('DATE_CALC_BEGIN_WEEKDAY', 0);

    // Do not overload DataObjects as it allows us to work with 4.3.10
    define('DB_DATAOBJECT_NO_OVERLOAD', true);

    // Ensure that the initialisation has not been run before
    if (!(isset($GLOBALS['_MAX']['CONF']))) {
        // Define the OpenX installation base path if not defined
        // since Local mode will pre-define this value
        if (!defined('MAX_PATH')) {
            define('MAX_PATH', dirname(__FILE__));
        }
        if (!defined('OX_PATH')) {
            define('OX_PATH', dirname(__FILE__));
        }
        // Ensure that the DIRECTORY_SEPARATOR and PATH_SEPARATOR
        // constants are correctly defined
        if (!defined('DIRECTORY_SEPARATOR')) {
            if (strpos($_ENV['OS'], 'Win') !== false) {
                // Windows
                define('DIRECTORY_SEPARATOR', '/');
            } else {
                // UNIX
                define('DIRECTORY_SEPARATOR', '\\');
            }
        }
        if (!defined('PATH_SEPARATOR')) {
            if (strpos($_ENV['OS'], 'Win') !== false) {
                // Windows
                define('PATH_SEPARATOR', ';');
            } else {
                // UNIX
                define('PATH_SEPARATOR', ':');
            }
        }
        if (!defined('LIB_PATH')) {
            define('LIB_PATH', MAX_PATH. DIRECTORY_SEPARATOR. 'lib'. DIRECTORY_SEPARATOR. 'OX');
        }

        // Setup the include path
        setupIncludePath();

        // Parse the OpenX configuration file
        $GLOBALS['_MAX']['CONF'] = parseIniFile();
        // Define the OpenX Cache File location path (required trailing slash)
        define('MAX_CACHE', MAX_PATH . '/var/cache/');

       // Set the URL access mechanism
        if (!empty($GLOBALS['_MAX']['CONF']['openads']['requireSSL'])) {
            $GLOBALS['_MAX']['HTTP'] = 'https://';
        } else {
            if (isset($_SERVER['SERVER_PORT'])) {
                if (isset($GLOBALS['_MAX']['CONF']['openads']['sslPort']) &&
                    $_SERVER['SERVER_PORT'] == $GLOBALS['_MAX']['CONF']['openads']['sslPort']) {
                    $GLOBALS['_MAX']['HTTP'] = 'https://';
                } else {
                    $GLOBALS['_MAX']['HTTP'] = 'http://';
                }
            }
        }
        // Set the True Type Font path
        if (isset($GLOBALS['_MAX']['CONF']['graphs']['ttfDirectory'])) {
            define('IMAGE_CANVAS_SYSTEM_FONT_PATH', $GLOBALS['_MAX']['CONF']['graphs']['ttfDirectory']);
        }
        // Set the dbms type
        if (isset($GLOBALS['_MAX']['CONF']['database'])
            && $GLOBALS['_MAX']['CONF']['database']['type'] == 'mysql')
        {
            define('phpAds_dbmsname', 'MySQL');
        }
        else if (isset($GLOBALS['_MAX']['CONF']['database'])
            && $GLOBALS['_MAX']['CONF']['database']['type'] == 'pgsql')
        {
            define('phpAds_dbmsname', 'Postgres');
        }
    }
}


if (!function_exists('mergeConfigFiles'))
{
    /**
     * This function is used to merge two config files
     * Any values in the second array will either overwrite or replace corresponding values in the first
     *
     * @param array $realConfig The base config file
     * @param array $fakeConfig The additional elements to add to the base config array
     * @return array The merged config files
     */
    function mergeConfigFiles($realConfig, $fakeConfig)
    {
        foreach ($fakeConfig as $key => $value) {
            if (is_array($value)) {
                if (!isset($realConfig[$key])) {
                    $realConfig[$key] = array();
                }
                $realConfig[$key] = mergeConfigFiles($realConfig[$key], $value);
            } else {
                if (isset($realConfig[$key]) && is_array($realConfig[$key])) {
                    $realConfig[$key][0] = $value;
                } else {
                    if (isset($realConfig) && !is_array($realConfig)) {
                        $temp = $realConfig;
                        $realConfig = array();
                        $realConfig[0] = $temp;
                    }
                    $realConfig[$key] = $value;
                }
            }
        }
        unset($realConfig['realConfig']);
        return $realConfig;
    }
}

?>
