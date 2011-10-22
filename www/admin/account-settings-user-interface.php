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
$Id: account-settings-user-interface.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/lib/OA/Admin/Option.php';
require_once MAX_PATH . '/lib/OA/Admin/Settings.php';

require_once MAX_PATH . '/lib/max/Plugin/Translation.php';
require_once MAX_PATH . '/www/admin/config.php';

require_once LIB_PATH . '/Plugin/Component.php';

// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN);

// Create a new option object for displaying the setting's page's HTML form
$oOptions = new OA_Admin_Option('settings');
$prefSection = "user-interface";
// Prepare an array for storing error messages
$aErrormessage = array();

// If the settings page is a submission, deal with the form data
if (isset($_POST['submitok']) && $_POST['submitok'] == 'true') {
    // Prepare an array of the HTML elements to process, and the
    // location to save the values in the settings configuration
    // file
    $aElements = array();
    // General Settings
    $aElements += array(
        'ui_enabled' => array(
            'ui'   => 'enabled',
            'bool' => true
        ),
        'ui_applicationName'       => array('ui' => 'applicationName'),
        'ui_headerFilePath'        => array('ui' => 'headerFilePath'),
        'ui_footerFilePath'        => array('ui' => 'footerFilePath'),
        'ui_logoFilePath'          => array('ui' => 'logoFilePath'),
        'ui_headerForegroundColor' => array('ui' => 'headerForegroundColor'),
        'ui_headerBackgroundColor' => array('ui' => 'headerBackgroundColor'),
        'ui_headerActiveTabColor'  => array('ui' => 'headerActiveTabColor'),
        'ui_headerTextColor'       => array('ui' => 'headerTextColor'),
        'ui_gzipCompression' => array(
            'ui'   => 'gzipCompression',
            'bool' => true
        )
    );
    // SSL Settings
    $aElements += array(
        'openads_requireSSL' => array(
            'openads' => 'requireSSL',
            'bool'    => true
        ),
        'openads_sslPort' => array('openads' => 'sslPort')
    );
    // Dashboard Settings
    $aElements += array(
        'ui_dashboardEnabled' => array(
        'ui' => 'dashboardEnabled',
        'bool'    => true
        )
    );

    // Dashboard Settings
    $aElements += array('authentication_type' => array('authentication' => 'type'));


    // Create a new settings object, and save the settings!
    $oSettings = new OA_Admin_Settings();
    $result = $oSettings->processSettingsFromForm($aElements);
    if ($result) {
    	// Delete all the sessions if the UI is disabled
    	// to force all the users to be logged out
    	if (!$GLOBALS['ui_enabled']) {
    		 $doSession = OA_Dal::factoryDO('session');
    		 $doSession->whereAdd('1=1');
    		 $doSession->delete(DB_DATAOBJECT_WHEREADD_ONLY);
    	}
    	// Rebuild the menu because the Enable Dashboard setting could been changed
    	OA_Admin_Menu::_clearCache(OA_ACCOUNT_ADMIN);
    	OA_Admin_Menu::_clearCache(OA_ACCOUNT_MANAGER);
        // Queue confirmation message
        $setPref = $oOptions->getSettingsPreferences($prefSection);
        $title = $setPref[$prefSection]['name'];
        $translation = new OX_Translation ();
        $translated_message = $translation->translate($GLOBALS['strXSettingsHaveBeenUpdated'],
            array(htmlspecialchars($title)));
        OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
         // The settings configuration file was written correctly,
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
        'text'  => $strGeneralSettings,
        'items' => array (
            array (
                'type'  => 'checkbox',
                'name'  => 'ui_enabled',
                'text'  => $uiEnabled
            ),
            array (
                'type'    => 'break'
            ),
            array (
                'type'    => 'text',
                'name'    => 'ui_applicationName',
                'text'    => $strAppName,
                'size'    => 35
            ),
            array (
                'type'    => 'break'
            ),
            array (
                'type'    => 'text',
                'name'    => 'ui_headerFilePath',
                'text'    => $strMyHeader,
                'size'    => 35
            ),
            array (
                'type'    => 'break'
            ),
            array (
                'type'    => 'text',
                'name'    => 'ui_footerFilePath',
                'text'    => $strMyFooter,
                'size'    => 35
            ),
            array (
                'type'    => 'break'
            ),
            array (
                'type'    => 'text',
                'name'    => 'ui_logoFilePath',
                'text'    => $strMyLogo,
                'size'    => 35
            ),
            array (
                'type'    => 'break'
            ),
            array (
                'type'    => 'text',
                'name'    => 'ui_headerForegroundColor',
                'text'    => $strGuiHeaderForegroundColor,
                'size'    => 35
            ),
            array (
                'type'    => 'break'
            ),
            array (
                'type'    => 'text',
                'name'    => 'ui_headerBackgroundColor',
                'text'    => $strGuiHeaderBackgroundColor,
                'size'    => 35
            ),
            array (
                'type'    => 'break'
            ),
            array (
                'type'    => 'text',
                'name'    => 'ui_headerActiveTabColor',
                'text'    => $strGuiActiveTabColor,
                'size'    => 35
            ),
            array (
                'type'    => 'break'
            ),
            array (
                'type'    => 'text',
                'name'    => 'ui_headerTextColor',
                'text'    => $strGuiHeaderTextColor,
                'size'    => 35
            ),
            array (
                'type'    => 'break'
            ),
            array (
                'type'    => 'checkbox',
                'name'    => 'ui_gzipCompression',
                'text'    => $strGzipContentCompression
            )
        )
    ),
    array (
        'text'  => $strSSLSettings,
        'items' => array (
            array (
                'type'  => 'checkbox',
                'name'  => 'openads_requireSSL',
                'text'  => $requireSSL
            ),
            array (
                'type'  => 'break'
            ),
            array (
                'type'  => 'text',
                'name'  => 'openads_sslPort',
                'text'  => $sslPort,
                'check' => 'wholeNumber'
            ),
        ),
     ),
     array (
         'text'  => $strDashboardSettings,
         'items' => array (
             array (
                 'type'  => 'checkbox',
                 'name'  => 'ui_dashboardEnabled',
                 'text'  => ($GLOBALS['_MAX']['CONF']['sync']['checkForUpdates'] ? $strEnableDashboard : $strEnableDashboardSyncNotice),
                 'disabled' => !$GLOBALS['_MAX']['CONF']['sync']['checkForUpdates']
             )
        )
    )
);

$aAuthPlugins = OX_Component::getComponents('authentication');
if (!empty($aAuthPlugins) && is_array($aAuthPlugins)) {
    // Add the 'none' (internal) authentication scheme to the list
    $aItems = array('none' => 'None (internal)');
    foreach ($aAuthPlugins as $oAuthPlugin) {
        $aItems[$oAuthPlugin->getComponentIdentifier()] = $oAuthPlugin->getName();
    }
    $aSettings[] = array (
         'text'  => 'Authentication mechanism',
         'items' => array (
             array (
                 'type'  => 'select',
                 'name'  => 'authentication_type',
                 'text'  => 'Select the plugin-component to be used for authentication',
                 'items' => $aItems,
             )
        )
    );
}

$oOptions->show($aSettings, $aErrormessage);

// Display the page footer
phpAds_PageFooter();

?>