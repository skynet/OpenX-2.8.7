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
$Id: advertiser-index.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/www/admin/lib-maintenance-priority.inc.php';
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/lib/OA/Dll.php';
require_once MAX_PATH . '/lib/OX/Util/Utils.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/www/admin/lib-statistics.inc.php';
require_once MAX_PATH . '/lib/OX/Admin/UI/ViewHooks.php';

function _isBannerAssignedToCampaign($aBannerData)
{
    return $aBannerData['campaignid'] > 0;
}

// Register input variables
phpAds_registerGlobal('hideinactive', 'listorder', 'orderdirection');

// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_MANAGER);


/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

phpAds_PageHeader(null, buildHeaderModel());


/*-------------------------------------------------------*/
/* Get preferences                                       */
/*-------------------------------------------------------*/

if (!isset($hideinactive)) {
    if (isset($session['prefs']['advertiser-index.php']['hideinactive'])) {
        $hideinactive = $session['prefs']['advertiser-index.php']['hideinactive'];
    } else {
        $pref = &$GLOBALS['_MAX']['PREF'];
        $hideinactive = ($pref['ui_hide_inactive'] == true);
    }
}

if (!isset($listorder)) {
    if (isset($session['prefs']['advertiser-index.php']['listorder'])) {
        $listorder = $session['prefs']['advertiser-index.php']['listorder'];
    } else {
        $listorder = '';
    }
}

if (!isset($orderdirection)) {
    if (isset($session['prefs']['advertiser-index.php']['orderdirection'])) {
        $orderdirection = $session['prefs']['advertiser-index.php']['orderdirection'];
    } else {
        $orderdirection = '';
    }
}


/*-------------------------------------------------------*/
/* Main code                                             */
/*-------------------------------------------------------*/

require_once MAX_PATH . '/lib/OA/Admin/Template.php';

$oTpl = new OA_Admin_Template('advertiser-index.html');

// Get clients & campaigns and build the tree
// XXX: Now that the two are next to each other, some silliness
//      is quite visible -- retrieving all items /then/ retrieving a count.
// TODO: This looks like a perfect candidate for object "polymorphism"
$dalClients = OA_Dal::factoryDAL('clients');
$dalCampaigns = OA_Dal::factoryDAL('campaigns');
$dalBanners = OA_Dal::factoryDAL('banners');

$campaigns = array();
$banners = array();

$oComponent = &OX_Component::factory ( 'admin', 'oxMarket', 'oxMarket');
$aInludeSystemTypes = array();
//TODO well, hardcoded reference to market plugin again, it would be better
//to ask plugins for additional types to include via hook.
if (isset($oComponent) && $oComponent->enabled) {
    $aInludeSystemTypes = array(DataObjects_Clients::ADVERTISER_TYPE_MARKET);
}

if (OA_Permission::isAccount(OA_ACCOUNT_ADMIN)) {
    $clients = $dalClients->getAllAdvertisers($listorder, $orderdirection, 
        null, $aInludeSystemTypes);
    if ($hideinactive) {
        $campaigns = $dalCampaigns->getAllCampaigns($listorder, $orderdirection);
        $banners = $dalBanners->getAllBanners($listorder, $orderdirection);
    }
} 
elseif (OA_Permission::isAccount(OA_ACCOUNT_MANAGER)) {
    $agency_id = OA_Permission::getEntityId();
    $clients = $dalClients->getAllAdvertisersForAgency($agency_id, $listorder, 
        $orderdirection, $aInludeSystemTypes);
    if ($hideinactive) {
        $campaigns = $dalCampaigns->getAllCampaignsUnderAgency($agency_id, $listorder, 
            $orderdirection);
        $banners = $dalBanners->getAllBannersUnderAgency($agency_id, $listorder, 
            $orderdirection);
        foreach ($banners as &$banner) {
            $banner['status'] = $banner['active'];
        }
    }
}

$aCount = array(
    'advertisers'        => count($clients),
    'advertisers_hidden' => 0
);


if ($hideinactive && !empty($clients) && !empty($campaigns) && 
    !empty($banners)) {

    // Build Tree
    foreach ($banners as $bkey => $banner) {
        if (_isBannerAssignedToCampaign($banner) && 
            (OA_ENTITY_STATUS_RUNNING == $banner['status'])) {

            $campaigns[$banner['campaignid']]['has_active_banners'] = true; 
        }
    }
            
    foreach ($campaigns as $ckey => $campaign) {
        if ((OA_ENTITY_STATUS_RUNNING == $campaign['status']) && 
            array_key_exists('has_active_banners', $campaign)) {
                
            $clients[$campaign['clientid']]['has_active_campaigns'] = 
                true;
        }
    }
    
    foreach (array_keys($clients) as $clientid) {
        $client = &$clients[$clientid];
        
        if (!array_key_exists('has_active_campaigns', $client)
            // we do not hide the Market advertiser
            && $client['type'] != DataObjects_Clients::ADVERTISER_TYPE_MARKET) {
            unset($clients[$clientid]);
            $aCount['advertisers_hidden']++;
        } 
    }
}

$itemsPerPage = 250;
$oPager = OX_buildPager($clients, $itemsPerPage);
$oTopPager = OX_buildPager($clients, $itemsPerPage, false);
list($itemsFrom, $itemsTo) = $oPager->getOffsetByPageId();
$clients =  array_slice($clients, $itemsFrom - 1, $itemsPerPage, true);

$oTpl->assign('pager', $oPager);
$oTpl->assign('topPager', $oTopPager);

$oTpl->assign('aAdvertisers', $clients);
$oTpl->assign('aCount', $aCount);
$oTpl->assign('hideinactive', $hideinactive);
$oTpl->assign('listorder', $listorder);
$oTpl->assign('orderdirection', $orderdirection);
$oTpl->assign('MARKET_TYPE', DataObjects_Clients::ADVERTISER_TYPE_MARKET);



/*-------------------------------------------------------*/
/* Store preferences                                     */
/*-------------------------------------------------------*/

$session['prefs']['advertiser-index.php']['hideinactive'] = $hideinactive;
$session['prefs']['advertiser-index.php']['listorder'] = $listorder;
$session['prefs']['advertiser-index.php']['orderdirection'] = $orderdirection;
phpAds_SessionDataStore();


/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/
OX_Admin_UI_ViewHooks::registerPageView($oTpl, 'advertiser-index');

$oTpl->display();
phpAds_PageFooter();


function buildHeaderModel()
{
    $builder = new OA_Admin_UI_Model_InventoryPageHeaderModelBuilder();
    return $builder->buildEntityHeader(array(), 'advertisers', 'list');
}

?>