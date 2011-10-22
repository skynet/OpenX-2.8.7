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
$Id: account-preferences-banner.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/lib/OA/Admin/Option.php';
require_once MAX_PATH . '/lib/OA/Preferences.php';

require_once MAX_PATH . '/lib/max/Plugin/Translation.php';
require_once MAX_PATH . '/www/admin/config.php';


// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN, OA_ACCOUNT_MANAGER, OA_ACCOUNT_ADVERTISER, OA_ACCOUNT_TRAFFICKER);

// Load the account's preferences, with additional information, into a specially named array
$GLOBALS['_MAX']['PREF_EXTRA'] = OA_Preferences::loadPreferences(true, true);

// Create a new option object for displaying the setting's page's HTML form
$oOptions = new OA_Admin_Option('preferences');
$prefSection = "banner";

// Prepare an array for storing error messages
$aErrormessage = array();

// If the settings page is a submission, deal with the form data
if (isset($_POST['submitok']) && $_POST['submitok'] == 'true') {
    // Prepare an array of the HTML elements to process, and which
    // of the preferences are checkboxes
    $aElements   = array();
    $aCheckboxes = array();
    // Default Banners
    $aElements[] = 'default_banner_image_url';
    $aElements[] = 'default_banner_destination_url';
    // Default Weight
    $aElements[] = 'default_banner_weight';
    $aElements[] = 'default_campaign_weight';
    // Save the preferences
    $result = OA_Preferences::processPreferencesFromForm($aElements, $aCheckboxes);
    if ($result) {

        // Queue confirmation message
        $setPref = $oOptions->getSettingsPreferences($prefSection);
        $title = $setPref[$prefSection]['name'];
        $translation = new OX_Translation ();
        $translated_message = $translation->translate($GLOBALS['strXPreferencesHaveBeenUpdated'],
            array(htmlspecialchars($title)));
        OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);


        // The preferences were written correctly saved to the database,
        // go to the "next" preferences page from here
        OX_Admin_Redirect::redirect(basename($_SERVER['SCRIPT_NAME']));
    }
    // Could not write the preferences to the database, store this
    // error message and continue
    $aErrormessage[0][] = $strUnableToWritePrefs;
}


// Set the correct section of the preference pages and display the drop-down menu
$setPref = $oOptions->getSettingsPreferences($prefSection);
$title = $setPref[$prefSection]['name'];

// Display the settings page's header and sections
$oHeaderModel = new OA_Admin_UI_Model_PageHeaderModel($title);
phpAds_PageHeader('account-preferences-index', $oHeaderModel);


// Prepare an array of HTML elements to display for the form, and
// output using the $oOption object
$aSettings = array (
    array (
        'text'  => $strDefaultBanners,
        'items' => array (
            array (
                'type'    => 'text',
                'name'    => 'default_banner_image_url',
                'text'    => $strDefaultBannerUrl,
                'size'    => 35,
                'check'   => 'url'
            ),
            array (
                'type'    => 'break'
            ),
            array (
                'type'    => 'text',
                'name'    => 'default_banner_destination_url',
                'text'    => $strDefaultBannerDestination,
                'size'    => 35,
                'check'   => 'url'
            )
        )
    ),
    array (
        'text'  => $strWeightDefaults,
        'items' => array (
            array (
                'type'  => 'text',
                'name'  => 'default_banner_weight',
                'text'  => $strDefaultBannerWeight,
                'size'  => 12,
                'check' => 'wholeNumber'
            ),
            array (
                'type'  => 'break'
            ),
            array (
                'type'  => 'text',
                'name'  => 'default_campaign_weight',
                'text'  => $strDefaultCampaignWeight,
                'size'  => 12,
                'check' => 'wholeNumber'
            )
        )
    )
);
$oOptions->show($aSettings, $aErrormessage);

// Display the page footer
phpAds_PageFooter();

?>