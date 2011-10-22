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
$Id: maintenance-appendcode-check.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/lib/max/Plugin.php';
require_once MAX_PATH . '/lib/max/Dal/Inventory/Trackers.php';
require_once MAX_PATH . '/www/admin/lib-maintenance.inc.php';


// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN);

phpAds_registerGlobal('action');

/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

phpAds_PageHeader("maintenance-index");
phpAds_MaintenanceSelection("appendcodes");

/*-------------------------------------------------------*/
/* Main code                                             */
/*-------------------------------------------------------*/

// Init DAL
$tr = & new MAX_Dal_Inventory_Trackers();

if (!empty($action) && ($action == 'Recompile')) {
    $tr->recompileAppendCodes();
    echo "<strong>$strAppendCodesRecompiled<br />";
}

echo $strAppendCodesResult;
phpAds_ShowBreak();
// Check the append codes in the database against the compiled appendcode strings...

echo "<strong>$strTrackers:</strong>";
phpAds_showBreak();

// Check all the trackers...
$diffs = $tr->checkCompiledAppendCodes();

foreach ($diffs as $v) {
    echo "<a href='client-trackers.php?clientid={$v['clientid']}&trackerid={$v['trackerid']}'>{$v['trackername']}</a><br />";
}

if ($allTrackersValid = !count($diffs)) {
    echo $strAppendCodesValid;
}
phpAds_showBreak();

if (!$allTrackersValid) {
    phpAds_ShowBreak();
    echo "<br /><strong>$strErrorsFound</strong><br /><br />";
    echo "$strRepairAppenedCodes<br />";
    echo "<form action='' METHOD='GET'>";
    echo "<input type='submit' name='action' value='$strRecompile' />";
    echo "</form>";
}


/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

phpAds_PageFooter();

?>
