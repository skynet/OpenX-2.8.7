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
$Id: account-preferences-timezone.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

// Obtain the server timezone information *before* the init script is
// called, to ensure that the timezone information from the server is
// not affected by any calls to date_default_timezone_set() or
// putenv("TZ=...") to set the timezone manually
require_once '../../lib/OX/Admin/Timezones.php';
$timezone = OX_Admin_Timezones::getTimezone();

// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/lib/OA/Admin/Option.php';
require_once MAX_PATH . '/lib/OA/Admin/UI/UserAccess.php';

require_once MAX_PATH . '/lib/max/Admin/Languages.php';
require_once MAX_PATH . '/lib/max/Plugin/Translation.php';
require_once MAX_PATH . '/www/admin/config.php';


// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN, OA_ACCOUNT_MANAGER, OA_ACCOUNT_ADVERTISER, OA_ACCOUNT_TRAFFICKER);

// Load the account's preferences, with additional information, into a specially named array
$GLOBALS['_MAX']['PREF_EXTRA'] = OA_Preferences::loadPreferences(true, true);

// Create a new option object for displaying the setting's page's HTML form
$oOptions = new OA_Admin_Option('preferences');
$prefSection = "timezone";

// Prepare an array for storing error messages
$aErrormessage = array();

// If the settings page is a submission, deal with the form data
if (isset($_POST['submitok']) && $_POST['submitok'] == 'true') {
	 // Prepare an array of the HTML elements to process, and which
    // of the preferences are checkboxes
    $aElements   = array();
    $aCheckboxes = array();
    // Timezone
    $aElements[] = 'timezone';
    // Save the preferences
    $result = OA_Preferences::processPreferencesFromForm($aElements, $aCheckboxes);
    if ($result) {
        // Queue confirmation message
        $setPref = $oOptions->getSettingsPreferences($prefSection);
        $title = $setPref[$prefSection]['name'];
        $translation = new OX_Translation ();
        $translatedMessage = $translation->translate($GLOBALS['strXPreferencesHaveBeenUpdated'],
            array(htmlspecialchars($title)));
        OA_Admin_UI::queueMessage($translatedMessage, 'local', 'confirm', 2000);
        // Also display warning after 2 seconds
        $translatedMessage = $translation->translate($GLOBALS['strTZPreferencesWarning']);
        OA_Admin_UI::queueMessage($translatedMessage, 'local', 'warning', 0);
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

// Get timezone dropdown information
$aTimezones = OX_Admin_Timezones::availableTimezones(true);
$oConfigTimezone = trim($GLOBALS['_MAX']['PREF']['timezone']);

if (empty($oConfigTimezone)) {
    // There is no value stored in the configuration file, as it
    // is not required (ie. the TZ comes from the environment) -
    // so set that environment value in the config file now
    $GLOBALS['_MAX']['PREF']['timezone'] = $timezone;
}

// What display string do we need to show for the timezone?
if (!empty($oConfigTimezone)) {
	$strTimezoneToDisplay = $oConfigTimezone;
} else {
    $strTimezoneToDisplay = $timezone;
}
$strTimezoneToDisplay = $GLOBALS['_MAX']['PREF']['timezone'];

// Prepare an array of HTML elements to display for the form, and
// output using the $oOption object
$aSettings = array (
    array (
        'text'  => $strTimezone,
        'items' => array (
            array (
                'type'    => 'select',
                'name'    => 'timezone',
                'text'    => $strTimezoneToDisplay,
                'items'   => $aTimezones,
                'value'   => $strTimezoneToDisplay
            )
        )
    )
);
$oOptions->show($aSettings, $aErrormessage);

// Display the page footer
phpAds_PageFooter();

?>