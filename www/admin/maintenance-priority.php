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
$Id: maintenance-priority.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/www/admin/lib-maintenance.inc.php';
require_once MAX_PATH . '/www/admin/lib-statistics.inc.php';
require_once MAX_PATH . '/www/admin/lib-zones.inc.php';

// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN);

/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

phpAds_PageHeader("maintenance-index");
phpAds_MaintenanceSelection("priority");

/*-------------------------------------------------------*/
/* Main code                                             */
/*-------------------------------------------------------*/

function phpAds_showBanners()
{
	$conf = $GLOBALS['_MAX']['CONF'];
	global $strUntitled, $strName, $strID, $strWeight;
	global $strProbability, $strPriority, $strRecalculatePriority;
	global $phpAds_TextDirection;

	$doAdZoneAssoc = OA_Dal::factoryDO('ad_zone_assoc');
	$doAdZoneAssoc->selectAdd();
	$doAdZoneAssoc->selectAs(array('ad_id'), 'bannerid');
	$doAdZoneAssoc->selectAdd('priority');
	$doAdZoneAssoc->zoneid = 0;
	$doAdZoneAssoc->OrderBy('priority DESC');
    $doAdZoneAssoc->find();

    $rows = array();
	$prioritysum = 0;

	while ($doAdZoneAssoc->fetch() && $tmprow = $doAdZoneAssoc->toArray()) {
		if ($tmprow['priority']) {
			$prioritysum += $tmprow['priority'];
			$rows[$tmprow['bannerid']] = $tmprow;
		}
	}

	if (is_array($rows)) {
		$i=0;

		// Header
		echo "<table width='100%' border='0' align='center' cellspacing='0' cellpadding='0'>";
		echo "<tr height='25'>";
		echo "<td height='25'><b>&nbsp;&nbsp;".$strName."</b></td>";
		echo "<td height='25'><b>".$strID."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td>";
		echo "<td height='25'><b>".$strPriority."</b></td>";
		echo "<td height='25'><b>".$strProbability."</b></td>";
		echo "</tr>";

		echo "<tr height='1'><td colspan='5' bgcolor='#888888'><img src='" . OX::assetPath() . "/images/break.gif' height='1' width='100%'></td></tr>";

		// Banners
		foreach (array_keys($rows) as $key) {
			$name = phpAds_getBannerName($rows[$key]['bannerid'], 60, false);

			if ($i > 0) echo "<tr height='1'><td colspan='5' bgcolor='#888888'><img src='" . OX::assetPath() . "/images/break-l.gif' height='1' width='100%'></td></tr>";

	    	echo "<tr height='25' ".($i%2==0?"bgcolor='#F6F6F6'":"").">";

			echo "<td height='25'>";
			echo "&nbsp;&nbsp;";

			// Banner icon
			if ($rows[$key]['storagetype'] == 'html')
				echo "<img src='" . OX::assetPath() . "/images/icon-banner-html.gif' align='absmiddle'>&nbsp;";
			elseif ($rows[$key]['storagetype'] == 'url')
				echo "<img src='" . OX::assetPath() . "/images/icon-banner-url.gif' align='absmiddle'>&nbsp;";
			else
				echo "<img src='" . OX::assetPath() . "/images/icon-banner-stored.gif' align='absmiddle'>&nbsp;";

			// Name
			echo $name;
			echo "</td>";

			echo "<td height='25'>".$rows[$key]['bannerid']."</td>";
			echo "<td height='25'>".$rows[$key]['priority']."</td>";
			echo "<td height='25'>".number_format($rows[$key]['priority'] / $prioritysum * 100, $pref['ui_percentage_decimals'])."%</td>";

			echo "</tr>";
			$i++;
		}

		// Footer
		echo "<tr height='1'><td colspan='5' bgcolor='#888888'><img src='" . OX::assetPath() . "/images/break.gif' height='1' width='100%'></td></tr>";
		echo "</table>";
	}
}

/*-------------------------------------------------------*/
/* Main code                                             */
/*-------------------------------------------------------*/

echo "<br />";

// Show recalculate button
echo "<img src='" . OX::assetPath() . "/images/".$phpAds_TextDirection."/icon-undo.gif' border='0' align='absmiddle'>&nbsp;<a href='maintenance-priority-calculate.php'>$strRecalculatePriority</a>&nbsp;&nbsp;";
echo "<br /><br />";
phpAds_ShowBreak();

echo "<br /><br />";
//echo 'This page needs to be re-written to show an agency-based list of ad/zone priority data...';
echo "<br /><br />";

echo "</table>";
echo "<br /><br />";


/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

phpAds_PageFooter();

?>
