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
$Id: userlog-details.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/lib/max/language/Loader.php';
require_once MAX_PATH . '/www/admin/config.php';

// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN);

// Get userlog data and enforce it exists
$doUserLog = OA_Dal::staticGetDO('userlog', $userlogid);
OA_Permission::enforceTrue($doUserLog);

/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

phpAds_PageHeader('userlog-index');
phpAds_UserlogSelection("maintenance");

// Load the required language files
Language_Loader::load('userlog');

/*-------------------------------------------------------*/
/* Main code                                             */
/*-------------------------------------------------------*/

if ($row = $doUserLog->toArray())
{
	echo "<br />";
	echo "<table cellpadding='0' cellspacing='0' border='0'>";

	echo "<tr height='20'><td><b>".$strDate."</b>:&nbsp;&nbsp;</td>";
	echo "<td>".strftime($date_format, $row['timestamp']).", ".strftime($minute_format, $row['timestamp'])."</td></tr>";

	echo "<tr height='20'><td><b>".$strUser."</b>:&nbsp;&nbsp;</td><td>";
	switch ($row['usertype'])
	{
		case phpAds_userDeliveryEngine:	echo "<img src='" . OX::assetPath() . "/images/icon-generatecode.gif' align='absmiddle'>&nbsp;".$strDeliveryEngine; break;
		case phpAds_userMaintenance:	echo "<img src='" . OX::assetPath() . "/images/icon-time.gif' align='absmiddle'>&nbsp;".$strMaintenance; break;
		case phpAds_userAdministrator:	echo "<img src='" . OX::assetPath() . "/images/icon-advertiser.gif' align='absmiddle'>&nbsp;".$strAdministrator; break;
	}
	echo "</td></tr>";

	$action = $strUserlog[$row['action']];
	$action = str_replace ('{id}', $row['object'], $action);
	echo "<tr height='20'><td><b>".$strAction."</b>:&nbsp;&nbsp;</td><td>".$action."</td></tr>";
	echo "</table>";

	phpAds_ShowBreak();

	echo "<br /><br />";
	echo "<pre>".$row['details']."</pre>";
	echo "<br /><br />";
}



/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

phpAds_PageFooter();

?>