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
$Id: account-settings-banner-logging.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/lib/OA/Admin/Option.php';
require_once MAX_PATH . '/lib/OA/Admin/Settings.php';

require_once MAX_PATH . '/lib/max/Plugin/Translation.php';
require_once MAX_PATH . '/www/admin/config.php';


// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN);

// Create a new option object for displaying the setting's page's HTML form
$oOptions = new OA_Admin_Option('settings');
$prefSection = "banner-logging";

// Prepare an array for storing error messages
$aErrormessage = array();

// If the settings page is a submission, deal with the form data
if (isset($_POST['submitok']) && $_POST['submitok'] == 'true') {
    // Prepare an array of the HTML elements to process, and the
    // location to save the values in the settings configuration
    // file
    $aElements = array();
    // Banner Logging Settings
    $aElements += array(
        'logging_adRequests'          => array(
             'logging'                => 'adRequests',
             'bool'                   => 'true'
         ),
        'logging_adImpressions'       => array(
             'logging'                => 'adImpressions',
             'bool'                   => 'true'
         ),
        'logging_adClicks'            => array(
             'logging'                => 'adClicks',
             'bool'                   => 'true'
         ),
        'logging_reverseLookup'       => array(
             'logging'                => 'reverseLookup',
             'bool'                   => 'true'
         ),
        'logging_proxyLookup'         => array(
             'logging'                => 'proxyLookup',
             'bool'                   => 'true'
         ),
/*        'logging_sniff'               => array(
              'logging'               => 'sniff',
              'bool'                  => 'true'
         )*/
    );
    // Block Banner Logging Window Settings
    $aElements += array(
         'logging_blockAdClicksWindow'=> array(
             'logging'                => 'blockAdClicksWindow'
         )
     );

    // Block Banner Logging Settings
    $aElements += array(
        'logging_ignoreHosts' => array(
            'logging'      => 'ignoreHosts',
            'preg_split'   => "/ |,|;|\r|\n/",
            'merge'        => ',',
            'merge_unique' => true
        )
    );

    // Block User-Agents
    $aElements += array(
        'logging_ignoreUserAgents' => array(
            'logging'      => 'ignoreUserAgents',
            'preg_split'   => "/\r|\n/",
            'merge'        => '|',
            'merge_unique' => true,
            'trim'         => true,
        )
    );

    // Enforce User-Agents
    $aElements += array(
        'logging_enforceUserAgents' => array(
            'logging'      => 'enforceUserAgents',
            'preg_split'   => "/\r|\n/",
            'merge'        => '|',
            'merge_unique' => true,
            'trim'         => true,
        )
    );
    // Create a new settings object, and save the settings!
    $oSettings = new OA_Admin_Settings();
    $result = $oSettings->processSettingsFromForm($aElements);
    if ($result) {
        // Queue confirmation message
        $setPref = $oOptions->getSettingsPreferences($prefSection);
        $title = $setPref[$prefSection]['name'];
        $translation = new OX_Translation ();
        $translated_message = $translation->translate($GLOBALS['strXSettingsHaveBeenUpdated'],
            array(htmlspecialchars($title)));
        OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);

        // The settings configuration file was written correctly,
        // go to the "next" settings page from here
        OX_Admin_Redirect::redirect(basename($_SERVER['SCRIPT_NAME']));
    }
    // Could not write the settings configuration file, store this
    // error message and continue
    $aErrormessage[0][] = $strUnableToWriteConfig;
}

// Set the correct section of the settings pages and display the drop-down menu
$setPref = $oOptions->getSettingsPreferences($prefSection);
$title = $setPref[$prefSection]['name'];

// Display the settings page's header and sections
$oHeaderModel = new OA_Admin_UI_Model_PageHeaderModel($title);
phpAds_PageHeader('account-settings-index', $oHeaderModel);

// Prepare an array of HTML elements to display for the form, and
// output using the $oOption object
$aSettings = array (
    array (
        'text'  => $strBannerLogging,
        'items' => array (
            array (
                'type'    => 'checkbox',
                'name'    => 'logging_adRequests',
                'text'    => $strLogAdRequests
            ),
            array (
                'type'    => 'checkbox',
                'name'    => 'logging_adImpressions',
                'text'    => $strLogAdImpressions
            ),
            array (
                'type'    => 'checkbox',
                'name'    => 'logging_adClicks',
                'text'    => $strLogAdClicks
            ),
            array (
                'type'    => 'break'
            ),
            array (
                'type'    => 'checkbox',
                'name'    => 'logging_reverseLookup',
                'text'    => $strReverseLookup
            ),
            array (
                'type'    => 'checkbox',
                'name'    => 'logging_proxyLookup',
                'text'    => $strProxyLookup
            ),
/*            array (
                'type'    => 'checkbox',
                'name'    => 'logging_sniff',
                'text'    => $strSniff
            )*/
        )
    ),
    array (
        'text'  => $strPreventLogging,
        'items' => array (
            array (
                'type'    => 'text',
                'name'    => 'logging_blockAdClicksWindow',
                'text'    => $strBlockAdClicks,
                'check'   => 'wholeNumber',
            ),
            array (
                'type'    => 'break'
            ),
            array (
                'type'      => 'textarea',
                'name'      => 'logging_ignoreHosts',
                'text'      => $strIgnoreHosts,
                'preg_split'=> '/,/',
                'merge'     => "\n",
            ),
            array (
                'type'    => 'break'
            ),
            array (
                'type'    => 'textarea',
                'name'    => 'logging_ignoreUserAgents',
                'text'    => $strIgnoreUserAgents,
                'preg_split'=> '/\|/',
                'merge'     => "\n",
            ),
            array (
                'type'    => 'break'
            ),
            array (
                'type'    => 'textarea',
                'name'    => 'logging_enforceUserAgents',
                'text'    => $strEnforceUserAgents,
                'preg_split'=> '/\|/',
                'merge'     => "\n",
            ),
        )
    )
);
$oOptions->show($aSettings, $aErrormessage);

// Display the page footer
phpAds_PageFooter();

?>