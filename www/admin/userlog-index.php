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
$Id: userlog-index.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/lib/max/Admin_DA.php';
require_once MAX_PATH . '/lib/max/other/lib-userlog.inc.php';
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/lib/OA/Admin/Template.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/lib/OA/Dll/Audit.php';
require_once MAX_PATH . '/lib/OA/Admin/UI/Field/AuditDaySpanField.php';
require_once 'Pager/Pager.php';
require_once MAX_PATH . '/lib/OX/Translation.php';

// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN, OA_ACCOUNT_MANAGER, OA_ACCOUNT_ADVERTISER, OA_ACCOUNT_TRAFFICKER);
OA_Permission::enforceAccountPermission(OA_ACCOUNT_ADVERTISER, OA_PERM_USER_LOG_ACCESS);
OA_Permission::enforceAccountPermission(OA_ACCOUNT_TRAFFICKER, OA_PERM_USER_LOG_ACCESS);

// Register input variables
$advertiserId   = MAX_getValue('advertiserId',    0);
$campaignId     = MAX_getValue('campaignId',      0);
$publisherId    = MAX_getValue('publisherId',     0);
$zoneId         = MAX_getValue('zoneId',          0);
$startDate      = MAX_getStoredValue('period_start', null);
$endDate        = MAX_getStoredValue('period_end', null);
$periodPreset   = MAX_getValue('period_preset', 'all_events');

if (!empty($advertiserId)) {
    OA_Permission::enforceAccessToObject('clients', $advertiserId);
}
if (!empty($campaignId)) {
    OA_Permission::enforceAccessToObject('campaigns', $campaignId);
}
if (!empty($publisherId)) {
    OA_Permission::enforceAccessToObject('affiliates', $publisherId);
}
if (!empty($zoneId)) {
    OA_Permission::enforceAccessToObject('zones', $zoneId);
}


/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

phpAds_PageHeader("5.4");
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


// Paging related input variables
$listorder      = htmlspecialchars(MAX_getStoredValue('listorder', 'updated'));
$oAudit = &OA_Dal::factoryDO('audit');
$aAuditColumns = $oAudit->table();
$aColumnNamesFound = array_keys($aAuditColumns, $listorder);
if (empty($aColumnNamesFound)) {
    // Invalid column name to order by, set to default
    $listorder = 'updated';
}
$orderdirection = htmlspecialchars(MAX_getStoredValue('orderdirection', 'up'));
if (!($orderdirection == 'up' || $orderdirection == 'down')) {
    if (stristr($orderdirection, 'down')) {
        $orderdirection = 'down';
    } else {
        $orderdirection = 'up';
    }
}
$setPerPage     = MAX_getStoredValue('setPerPage',      10);
$pageID         = MAX_getStoredValue('pageID',          1);

// Setup date selector
$aPeriod = array(
    'period_preset'     => $periodPreset,
    'period_start'      => $startDate,
    'period_end'        => $endDate
);
$daySpan = new OA_Admin_UI_Audit_DaySpanField('period');
$daySpan->setValueFromArray($aPeriod);
$daySpan->enableAutoSubmit();

// Initialize parameters
$pageName = basename($_SERVER['SCRIPT_NAME']);

// Load template
$oTpl = new OA_Admin_Template('userlog-index.html');

// Get advertisers & publishers for filters
$showAdvertisers = OA_Permission::isAccount(OA_ACCOUNT_ADVERTISER, OA_ACCOUNT_MANAGER, OA_ACCOUNT_ADMIN);
$showPublishers = OA_Permission::isAccount(OA_ACCOUNT_TRAFFICKER, OA_ACCOUNT_MANAGER, OA_ACCOUNT_ADMIN);

$agencyId = OA_Permission::getAgencyId();

// Get advertisers if we show them
$aAdvertiser = $aPublisher = array();
if ($showAdvertisers) {
    if (OA_Permission::isAccount(OA_ACCOUNT_ADVERTISER)) {
        $tempAdvertiserId    = OA_Permission::getEntityId();
        $aAdvertiserList = Admin_DA::getAdvertisers(array('advertiser_id' => $tempAdvertiserId));
    } else {
        $aAdvertiserList = Admin_DA::getAdvertisers(array('agency_id' => $agencyId));
    }
    $aAdvertiser[0]  = $GLOBALS['strSelectAdvertiser'];
    foreach($aAdvertiserList as $key => $aValue) {
        $aAdvertiser[$aValue['advertiser_id']] = $aValue['name'];
    }
    $aCampaign = array();
    if (!empty($advertiserId)) {
        $campaign = Admin_DA::getCampaigns(array('client_id' => $advertiserId));
        $aCampaign[0] = $GLOBALS['strSelectPlacement'];
        foreach($campaign as $key => $aValue) {
            $aCampaign[$aValue['campaign_id']] = $aValue['campaignname'];
        }
    }
}

// Get publishers if we show them
if ($showPublishers) {
    if (OA_Permission::isAccount(OA_ACCOUNT_TRAFFICKER)) {
        $tempPublisherId    = OA_Permission::getEntityId();
        $aPublisherList = Admin_DA::getPublishers(array('publisher_id' => $tempPublisherId));
    } else {
        $aPublisherList = Admin_DA::getPublishers(array('agency_id' => $agencyId));
    }
    $aPublisher[0]  = $GLOBALS['strSelectPublisher'];
    foreach ($aPublisherList as $key => $aValue) {
        $aPublisher[$aValue['publisher_id']] = $aValue['name'];
    }
    if (!empty($publisherId)) {
        $zone = Admin_DA::getZones(array('publisher_id' => $publisherId));
        $aZone[0] = $GLOBALS['strSelectZone'];
        foreach ($zone as $key => $aValue) {
            $aZone[$aValue['zone_id']] = $aValue['name'];
        }
    }
}

$oTrans = new OX_Translation();

$aParams = array(
    'order'         => $orderdirection,
    'listorder'     => $listorder,
    'start_date'    => $startDate,
    'end_date'      => $endDate,
    'prevImg'       => '<< ' . $oTrans->translate('Back'),
    'nextImg'       => $oTrans->translate('Next') . ' >>'
);

// Only pass advertiser or website props if we show related checkboxes
if ($showAdvertisers) {
    $aParams['advertiser_id']= $advertiserId;
    $aParams['campaign_id'] = $campaignId;
}
if ($showPublishers) {
    $aParams['publisher_id']  = $publisherId;
    $aParams['zone_id'] = $zoneId;
}


// Account security
if (OA_Permission::isAccount(OA_ACCOUNT_MANAGER)) {
    $aParams['account_id'] = OA_Permission::getAccountId();
}
if (OA_Permission::isAccount(OA_ACCOUNT_ADVERTISER)) {
    $aParams['advertiser_account_id'] = OA_Permission::getAccountId();
}
if (OA_Permission::isAccount(OA_ACCOUNT_TRAFFICKER)) {
    $aParams['website_account_id'] = OA_Permission::getAccountId();
}

$oUserlog = new OA_Dll_Audit();
$aAuditData = $oUserlog->getAuditLog($aParams);

$aParams['totalItems'] = count($aAuditData);

if (!isset($pageID) || $pageID == 1) {
    $aParams['startRecord'] = 0;
} else {
    $aParams['startRecord'] = ($pageID * $setPerPage) - $setPerPage;
}

if ($aParams['startRecord'] > $aParams['totalItems']) {
    $aParams['startRecord'] = 0;
}

$aParams['perPage'] = MAX_getStoredValue('setPerPage', 10);

// Retrieve audit details
$aAuditData = $oUserlog->getAuditLog($aParams);

$pager = & Pager::factory($aParams);
$per_page = $pager->_perPage;
$pager->history = $pager->getPageData();
$pager->pagerLinks = $pager->getLinks();

$pager->pagerLinks = $pager->pagerLinks['all'];
$pager->pagerSelect = preg_replace('/(<select.*?)(>)/i', '$1 onchange="submitForm()" id="setPerPage"$2', $pager->getPerPageSelectBox(10, 100, 10));

// Build column header link params
$aAllowdParams = array('advertiserId', 'campaignId', 'publisherId', 'zoneId');
foreach ($aAllowdParams as $key) {
    if (!empty($$key)) {
        $aUrlParam[$key] = "$key=".$$key;
    }
}

$aUrlParam['listorder']         = "listorder=$listorder";
$aUrlParam['$orderdirection']   = ($orderdirection == 'down') ? "orderdirection=up" : "orderdirection=down";

$urlParam = implode('&', $aUrlParam);

// Replace context with translation
foreach ($aAuditData as $key => $aValue) {
    $k = 'str'. str_replace(' ', '', $aValue['context']);
    if (!empty($GLOBALS[$k])) {
        $aAuditData[$key]['context'] = $GLOBALS[$k];
    }
}

// Assign vars to template
$oTpl->assign('showAdvertisers', $showAdvertisers);
$oTpl->assign('showPublishers',  $showPublishers);

if ($showAdvertisers) {
    $oTpl->assign('aAdvertiser',        $aAdvertiser);
    $oTpl->assign('aCampaign',          $aCampaign);
}
if ($showPublishers) {
    $oTpl->assign('aPublisher',         $aPublisher);
    $oTpl->assign('aZone',              $aZone);
}

$oTpl->assign('aAuditEnabled',      OA::getConfigOption('audit', 'enabled', false));
$oTpl->assign('aAuditData',         $aAuditData);
$oTpl->assign('aPeriodPreset',      $aPeriodPreset);
$oTpl->assign('context',            $context);
$oTpl->assign('advertiserId',       $advertiserId);
$oTpl->assign('campaignId',         $campaignId);
$oTpl->assign('publisherId',        $publisherId);
$oTpl->assign('zoneId',             $zoneId);
$oTpl->assign('urlParam',           $urlParam);
$oTpl->assign('listorder',          $listorder);
$oTpl->assign('orderdirection',     $orderdirection);
$oTpl->assign('setPerPage',         $setPerPage);
$oTpl->assign('pager',              $pager);
$oTpl->assign('daySpan',            $daySpan);

// Display page
$oTpl->display();

//  display footer
phpAds_PageFooter();

// Store filter variables in session
$session['prefs'][$pageName]['advertiserId']    = $advertiserId;
$session['prefs'][$pageName]['campaignId']      = $campaignId;
$session['prefs'][$pageName]['publisherId']     = $publisherId;
$session['prefs'][$pageName]['zoneId']          = $zoneId;
$session['prefs'][$pageName]['period_preset']   = $periodPreset;
$seesion['prefs'][$pageName]['setPerPage']      = $setPerPage;
$session['prefs'][$pageName]['listorder']       = $listorder;
$session['prefs'][$pageName]['orderdirection']  = $orderdirection;

phpAds_SessionDataStore();

?>
