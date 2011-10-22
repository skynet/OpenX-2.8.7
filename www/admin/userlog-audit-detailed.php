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
$Id: userlog-audit-detailed.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/lib/OA/Dll/Audit.php';
require_once MAX_PATH . '/lib/OA/Admin/Template.php';
require_once MAX_PATH . '/www/admin/config.php';

// Register input variables
$auditId = MAX_getStoredValue('auditId', 0);

// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN, OA_ACCOUNT_MANAGER, OA_ACCOUNT_ADVERTISER, OA_ACCOUNT_TRAFFICKER);
OA_Permission::enforceAccountPermission(OA_ACCOUNT_ADVERTISER, OA_PERM_USER_LOG_ACCESS);
OA_Permission::enforceAccountPermission(OA_ACCOUNT_TRAFFICKER, OA_PERM_USER_LOG_ACCESS);
OA_Permission::enforceAccessToObject('audit', $auditId);

/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

phpAds_PageHeader('userlog-index');
if (OA_Permission::isAccount(OA_ACCOUNT_ADMIN)) {
    // Show all "My Account" sections
    phpAds_ShowSections(array("5.1", "5.2", "5.3", "5.5", "5.6", "5.4"));
    phpAds_UserlogSelection("index");
}
else if (OA_Permission::isAccount(OA_ACCOUNT_MANAGER)) {
    // Show the "Preferences", "User Log" and "Channel Management" sections of the "My Account" sections
    phpAds_ShowSections(array("5.1", "5.2", "5.4", "5.7"));
}
else if (OA_Permission::isAccount(OA_ACCOUNT_TRAFFICKER) || OA_Permission::isAccount(OA_ACCOUNT_ADVERTISER)) {
    phpAds_ShowSections(array("5.1", "5.2", "5.4"));
}


//  initialize parameters
$pageName = basename($_SERVER['SCRIPT_NAME']);

$oTpl = new OA_Admin_Template('userlog-audit-detailed.html');

$oAudit = new OA_Dll_Audit();
$aAuditDetail = $oAudit->getAuditDetail($auditId);

$oTpl->assign('aAuditDetail', $aAuditDetail);

$oTpl->display();

phpAds_PageFooter();

?>
