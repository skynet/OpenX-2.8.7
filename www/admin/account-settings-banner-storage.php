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
$Id: account-settings-banner-storage.php 62345 2010-09-14 21:16:38Z chris.nutting $
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
$prefSection = "banner-storage";

// Prepare an array for storing error messages
$aErrormessage = array();

// If the settings page is a submission, deal with the form data
if (isset($_POST['submitok']) && $_POST['submitok'] == 'true') {
    // Prepare an array of the HTML elements to process, and the
    // location to save the values in the settings configuration
    // file
    $aElements = array();

    // Allowed Banner Types
    $aElements += array(
        'allowedBanners_sql' => array(
            'allowedBanners' => 'sql',
            'bool'           => true
        ),
        'allowedBanners_web' => array(
            'allowedBanners' => 'web',
            'bool'           => true
        ),
        'allowedBanners_url' => array(
            'allowedBanners' => 'url',
            'bool'           => true
        ),
        'allowedBanners_html' => array(
            'allowedBanners' => 'html',
            'bool'           => true
        ),
        'allowedBanners_text' => array(
            'allowedBanners' => 'text',
            'bool'           => true
        )
    );
    // Webserver Local Banner Storage Settings
    $aElements += array(
        'store_mode'        => array('store' => 'mode'),
        'store_webDir'      => array('store' => 'webDir'),
        'store_ftpHost'     => array('store' => 'ftpHost'),
        'store_ftpPath'     => array('store' => 'ftpPath'),
        'store_ftpUsername' => array('store' => 'ftpUsername'),
        'store_ftpPassword' => array('store' => 'ftpPassword'),
        'store_ftpPassive'  => array(
            'store' => 'ftpPassive',
            'bool'  => 'true'
        )
    );
    // Test the writablility of the web or FTP storage, if required
    phpAds_registerGlobal('store_webDir');
    if (isset($store_webDir)) {
        // Check that the web directory is writable
        if (is_writable($store_webDir)) {
            //  If web store path has changed, copy the 1x1.gif to the
            // new location, else create it
            if ($conf['store']['webDir'] != $store_webDir) {
                if (file_exists($conf['store']['webDir'] .'/1x1.gif')) {
                    copy($conf['store']['webDir'].'/1x1.gif', $store_webDir.'/1x1.gif');
                } else {
                    $fp = fopen($store_webDir.'/1x1.gif', 'w');
                    fwrite($fp, base64_decode('R0lGODlhAQABAIAAAP///wAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=='));
                    fclose($fp);
                }
            }
        } else {
            $aErrormessage[0][] = $strTypeDirError;
        }
    }
    phpAds_registerGlobal('store_ftpHost');
    if (isset($store_ftpHost)) {
    	phpAds_registerGlobal('store_ftpUsername');
    	phpAds_registerGlobal('store_ftpPassword');
    	phpAds_registerGlobal('store_ftpPassive');
    	phpAds_registerGlobal('store_ftpPath');
        // Check that PHP has support for FTP
        if (function_exists('ftp_connect')) {
            // Check that the FTP host can be contacted
            if ($ftpsock = @ftp_connect($store_ftpHost)) {
                // Check that the details to log into the FTP host are correct
                if (@ftp_login($ftpsock, $store_ftpUsername, $store_ftpPassword)) {
                    if ($store_ftpPassive) {
                        ftp_pasv($ftpsock, true);
                    }
                	//Check path to ensure there is not a leading slash
                    if (($store_ftpPath != "") && (substr($store_ftpPath, 0, 1) == "/")) {
                        $store_ftpPath = substr($store_ftpPath, 1);
                    }

                    if (empty($store_ftpPath) || @ftp_chdir($ftpsock, $store_ftpPath)) { // Changes path if store_ftpPath is not empty!
                        // Save the 1x1.gif temporarily
                        $filename = MAX_PATH . '/var/1x1.gif';
                        $fp = @fopen($filename, 'w+');
                        @fwrite($fp, base64_decode('R0lGODlhAQABAIAAAP///wAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw=='));

                        // Upload to server
                        if (!@ftp_put($ftpsock, '1x1.gif', MAX_PATH.'/var/1x1.gif', FTP_BINARY)){
                        	$aErrormessage[0][] = $strTypeFTPErrorUpload;
                        }
                        // Chmod file so that it's world readable
                        if (function_exists('ftp_chmod') && !@ftp_chmod($ftpsock, 0644, '1x1.gif')) {
                            OA::debug('Unable to modify FTP permissions for file: '. $store_ftpPath .'/1x1.gif', PEAR_LOG_INFO);
                        }
                        // Delete temp 1x1.gif file
                        @fclose($fp);
                        @ftp_close($ftpsock);
                        unlink($filename);
                    } else {
                        $aErrormessage[0][] = $strTypeFTPErrorDir;
                    }
                } else {
                    $aErrormessage[0][] = $strTypeFTPErrorConnect;
                }
                @ftp_quit($ftpsock);
            } else {
                $aErrormessage[0][] = $strTypeFTPErrorHost;
            }
        } else {
            $aErrormessage[0][] = $strTypeFTPErrorNoSupport;
        }
    }
    if (empty($aErrormessage)) {
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
}

// Set the correct section of the settings pages and display the drop-down menu
$setPref = $oOptions->getSettingsPreferences($prefSection);
$title = $setPref[$prefSection]['name'];

// Display the settings page's header and sections
$oHeaderModel = new OA_Admin_UI_Model_PageHeaderModel($title);
phpAds_PageHeader('account-settings-index', $oHeaderModel);

// Prepare an array of HTML elements to display for the form, and
// output using the $oOption object
$aSettings = array(
   array (
        'text'  => $strAllowedBannerTypes,
        'items' => array (
            array (
                'type'    => 'checkbox',
                'name'    => 'allowedBanners_sql',
                'text'    => $strTypeSqlAllow
            ),
            array (
                'type'    => 'checkbox',
                'name'    => 'allowedBanners_web',
                'text'    => $strTypeWebAllow
            ),
            array (
                'type'    => 'checkbox',
                'name'    => 'allowedBanners_url',
                'text'    => $strTypeUrlAllow
            ),
            array (
                'type'    => 'checkbox',
                'name'    => 'allowedBanners_html',
                'text'    => $strTypeHtmlAllow
            ),
            array (
                'type'    => 'checkbox',
                'name'    => 'allowedBanners_text',
                'text'    => $strTypeTxtAllow
            )
        )
    ),
    array (
        'text' 	=> $strTypeWebSettings,
        'items'	=> array (
            array (
                'type'      => 'select',
                'name'      => 'store_mode',
                'text'      => $strTypeWebMode,
                'items'     => array('local' => $strTypeWebModeLocal,
                'ftp'       => $strTypeWebModeFtp),
                'depends'   => 'allowedBanners_web==1',
            ),
            array (
                'type'      => 'break',
                'size'      => 'full'
            ),
            array (
                'type' 	    => 'text',
                'name' 	    => 'store_webDir',
                'text' 	    => $strTypeWebDir,
                'size'	    => 35,
                'depends'   => 'allowedBanners_web==1 && store_mode==0'
            ),
            array (
                'type'      => 'break',
                'size'	    => 'full'
            ),
            array (
                'type' 	    => 'text',
                'name'      => 'store_ftpHost',
                'text' 	    => $strTypeFTPHost,
                'size'	    => 35,
                'depends'   => 'allowedBanners_web==1 && store_mode==1'
            ),
            array (
                'type'      => 'break'
            ),
            array (
                'type'   	=> 'text',
                'name' 	    => 'store_ftpPath',
                'text'   	=> $strTypeFTPDirectory,
                'size'	    => 35,
                'depends'   => 'allowedBanners_web==1 && store_mode==1'
            ),
            array (
                'type'      => 'break'
            ),
            array (
                'type'      => 'text',
                'name'      => 'store_ftpUsername',
                'text' 	    => $strTypeFTPUsername,
                'size'	    => 35,
                'depends'   => 'allowedBanners_web==1 && store_mode==1'
            ),
            array (
                'type'      => 'break'
            ),
            array (
                'type'      => 'password',
                'name'      => 'store_ftpPassword',
                'text'      => $strTypeFTPPassword,
                'size'	    => 35,
                'depends'   => 'allowedBanners_web==1 && store_mode==1'
            ),
            array (
                'type'      => 'break'
            ),
            array (
                'type'      => 'checkbox',
                'name'      => 'store_ftpPassive',
                'text'      => $strTypeFTPPassive,
                'depends'   => 'allowedBanners_web==1 && store_mode==1'
            )
        )
    )
);
$oOptions->show($aSettings, $aErrormessage);

// Display the page footer
phpAds_PageFooter();

?>