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
$Id: affiliate-user.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/www/admin/lib-statistics.inc.php';
require_once MAX_PATH . '/lib/OA/Session.php';
require_once MAX_PATH . '/lib/OA/Admin/UI/UserAccess.php';
require_once MAX_PATH . '/lib/max/other/html.php';


// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_MANAGER, OA_ACCOUNT_TRAFFICKER);
OA_Permission::enforceAccountPermission(OA_ACCOUNT_TRAFFICKER, OA_PERM_SUPER_ACCOUNT);
OA_Permission::enforceAccessToObject('affiliates', $affiliateid);

/*-------------------------------------------------------*/
/* Store preferences									 */
/*-------------------------------------------------------*/
$session['prefs']['inventory_entities'][OA_Permission::getEntityId()]['affiliateid'] = $affiliateid;
phpAds_SessionDataStore();


$userAccess = new OA_Admin_UI_UserAccess();
$userAccess->init();

function OA_headerNavigation()
{
	global $affiliateid;
    phpAds_PageHeader("affiliate-access");
    MAX_displayWebsiteBreadcrumbs($affiliateid);
}
$userAccess->setNavigationHeaderCallback('OA_headerNavigation');

function OA_footerNavigation()
{
    echo "
    <script language='JavaScript'>
    <!--
    ";
    if (OA_Permission::isAccount(OA_ACCOUNT_MANAGER)) {
        echo "function MMM_cascadePermissionsChange()
        {
            var e = findObj('permissions_".OA_PERM_ZONE_EDIT."');
            var a = findObj('permissions_".OA_PERM_ZONE_ADD."');
            var d = findObj('permissions_".OA_PERM_ZONE_DELETE."');

            a.disabled = d.disabled = !e.checked;
            if (!e.checked) {
                a.checked = d.checked = false;
            }
        }
        MMM_cascadePermissionsChange();
        //-->";
    }
    echo "</script>";
}
$userAccess->setNavigationFooterCallback('OA_footerNavigation');

$accountId = OA_Permission::getAccountIdForEntity('affiliates', $affiliateid);
$userAccess->setAccountId($accountId);

$userAccess->setPagePrefix('affiliate');


$aAllowedPermissions = array();
if (OA_Permission::isAccount(OA_ACCOUNT_MANAGER) ||
        OA_Permission::hasPermission(OA_PERM_SUPER_ACCOUNT, $accountId)) {
    $aAllowedPermissions[OA_PERM_SUPER_ACCOUNT] = array($strAllowCreateAccounts, false);
}
$aAllowedPermissions[OA_PERM_ZONE_EDIT]       = array($strAllowAffiliateModifyZones,  false,
                                                      'MMM_cascadePermissionsChange()');
$aAllowedPermissions[OA_PERM_ZONE_ADD]        = array($strAllowAffiliateAddZone,      true, false);
$aAllowedPermissions[OA_PERM_ZONE_DELETE]     = array($strAllowAffiliateDeleteZone,   true, false);
$aAllowedPermissions[OA_PERM_ZONE_LINK]       = array($strAllowAffiliateLinkBanners,  false, false);
$aAllowedPermissions[OA_PERM_ZONE_INVOCATION] = array($strAllowAffiliateGenerateCode, false, false);
$aAllowedPermissions[OA_PERM_USER_LOG_ACCESS] = array($strAllowAuditTrailAccess, false, false);
$userAccess->setAllowedPermissions($aAllowedPermissions);


$userAccess->setHiddenFields(array('affiliateid' => $affiliateid));
$userAccess->setRedirectUrl('affiliate-access.php?affiliateid='.$affiliateid);
$userAccess->setBackUrl('affiliate-user-start.php?affiliateid='.$affiliateid);

$userAccess->process();

?>
