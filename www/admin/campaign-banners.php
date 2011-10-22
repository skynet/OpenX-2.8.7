<?php

/*
+---------------------------------------------------------------------------+
| OpenX  v2.8                                                              |
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
$Id: campaign-banners.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/www/admin/lib-maintenance-priority.inc.php';
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/lib/OA/Dll.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/www/admin/lib-statistics.inc.php';
require_once MAX_PATH . '/www/admin/lib-gd.inc.php';
require_once MAX_PATH . '/lib/max/other/html.php';
require_once MAX_PATH . '/lib/OX/Translation.php';

// Register input variables
phpAds_registerGlobal('hideinactive', 'listorder', 'orderdirection');


// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_MANAGER, OA_ACCOUNT_ADVERTISER);
if (!empty($clientid) && !OA_Permission::hasAccessToObject('clients', $clientid)) { //check if can see given advertiser
    $page = basename($_SERVER['SCRIPT_NAME']);
    OX_Admin_Redirect::redirect($page);
}
if (!empty($campaignid) && !OA_Permission::hasAccessToObject('campaigns', $campaignid)) {
    $page = basename($_SERVER['SCRIPT_NAME']);
    OX_Admin_Redirect::redirect("$page?clientid=$clientid");
}


/*-------------------------------------------------------*/
/* Init data                                             */
/*-------------------------------------------------------*/

//get advertisers and set the current one
$aAdvertisers = getAdvertiserMap();
if (empty($clientid)) { //if it's empty
    $campaignid = null; //reset campaign id, we could derive it after we have clientid
    if ($session['prefs']['inventory_entities'][OA_Permission::getEntityId()]['clientid']) {
        //try previous one from session
        $sessionClientId = $session['prefs']['inventory_entities'][OA_Permission::getEntityId()]['clientid'];
        if (isset($aAdvertisers[$sessionClientId])) { //check if 'id' from session was not removed
            $clientid = $sessionClientId;
        }
    }
    if (empty($clientid)) { //was empty, is still empty - just pick one, no need for redirect
        $ids = array_keys($aAdvertisers);
        if (!empty($ids)) {
            $clientid = $ids[0];
        }
        else {
            $clientid = -1; //if no advertisers set to non-existent id
            $campaignid = -1; //also reset campaign id
        }
    }
}
else {
    if (!isset($aAdvertisers[$clientid])) {
        $page = basename($_SERVER['SCRIPT_NAME']);
        OX_Admin_Redirect::redirect($page);
    }
}

//get campaigns - if there was any client id derived
if ($clientid > 0) {
    $aCampaigns = getCampaignMap($clientid);
    if (empty($campaignid)) { //if it's empty
        if ($session['prefs']['inventory_entities'][OA_Permission::getEntityId()]['campaignid'][$clientid]) {
            //try previous one from session
            $sessionCampaignId = $session['prefs']['inventory_entities'][OA_Permission::getEntityId()]['campaignid'][$clientid];
            if (isset($aCampaigns[$sessionCampaignId])) { //check if 'id' from session was not removed
                $campaignid = $sessionCampaignId;
            }
        }
        if (empty($campaignid)) { //was empty, is still empty - just pick one, no need for redirect
            $ids = array_keys($aCampaigns);
            $campaignid = !empty($ids) ? $ids[0] : -1; //if no campaigns set to non-existent id
        }
    }
    else {
        if (!isset($aCampaigns[$campaignid])) {
            $page = basename($_SERVER['SCRIPT_NAME']);
            OX_Admin_Redirect::redirect("$page?clientid=$clientid");
        }
    }
}

/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

// Initialise some parameters
$pageName = basename($_SERVER['SCRIPT_NAME']);
$tabindex = 1;
$agencyId = OA_Permission::getAgencyId();
$aEntities = array('clientid' => $clientid, 'campaignid' => $campaignid);
$oTrans = new OX_Translation();

// Display navigation
$aOtherAdvertisers = Admin_DA::getAdvertisers(array('agency_id' => $agencyId));
$aOtherCampaigns = Admin_DA::getPlacements(array('advertiser_id' => $clientid));

$oHeaderModel = buildHeaderModel($aEntities);
phpAds_PageHeader(null, $oHeaderModel);


/*-------------------------------------------------------*/
/* Get preferences                                       */
/*-------------------------------------------------------*/

if (!isset($hideinactive)) {
    if (isset($session['prefs']['campaign-banners.php'][$campaignid]['hideinactive'])) {
        $hideinactive = $session['prefs']['campaign-banners.php'][$campaignid]['hideinactive'];
    } else {
        $pref =& $GLOBALS['_MAX']['PREF'];
        $hideinactive = ($pref['ui_hide_inactive'] == true);
    }
}

if (!isset($listorder)) {
    if (isset($session['prefs']['campaign-banners.php'][$campaignid]['listorder'])) {
        $listorder = $session['prefs']['campaign-banners.php'][$campaignid]['listorder'];
    } else {
        $listorder = '';
    }
}

if (!isset($orderdirection)) {
    if (isset($session['prefs']['campaign-banners.php'][$campaignid]['orderdirection'])) {
        $orderdirection = $session['prefs']['campaign-banners.php'][$campaignid]['orderdirection'];
    } else {
        $orderdirection = '';
    }
}


/*-------------------------------------------------------*/
/* Main code                                             */
/*-------------------------------------------------------*/

require_once MAX_PATH . '/lib/OA/Admin/Template.php';

$oTpl = new OA_Admin_Template('banner-index.html');


$doBanners = OA_Dal::factoryDO('banners');
$doBanners->campaignid = $campaignid;
$doBanners->addListorderBy($listorder, $orderdirection);
$doBanners->selectAdd('storagetype AS type');
$doBanners->find();

$countActive = 0;

while ($doBanners->fetch() && $row = $doBanners->toArray()) {
    $banners[$row['bannerid']] = $row;
	$banners[$row['bannerid']]['active'] = $banners[$row['bannerid']]["status"] == OA_ENTITY_STATUS_RUNNING;

    $banners[$row['bannerid']]['description'] = $strUntitled;
    if (isset($banners[$row['bannerid']]['alt']) && $banners[$row['bannerid']]['alt'] != '') {
		$banners[$row['bannerid']]['description'] = $banners[$row['bannerid']]['alt'];
    }

    // mask banner name if anonymous campaign
    $campaign_details = Admin_DA::getPlacement($row['campaignid']);
    $campaignAnonymous = $campaign_details['anonymous'] == 't' ? true : false;
    $banners[$row['bannerid']]['description'] = MAX_getAdName($row['description'], null, null, $campaignAnonymous, $row['bannerid']);

    $banners[$row['bannerid']]['expand'] = 0;
    if ($row['status'] == OA_ENTITY_STATUS_RUNNING) {
        $countActive++;
    }
}

$aCount = array(
    'banners'        => 0,
    'banners_hidden' => 0,
);


// Figure out which banners are inactive,
$bannersHidden = 0;
if (isset($banners) && is_array($banners) && count($banners) > 0) {
    reset ($banners);
    while (list ($key, $banner) = each ($banners)) {
		$aCount['banners']++;
        if (($hideinactive == true) && ($banner['status'] != OA_ENTITY_STATUS_RUNNING)) {
            $bannersHidden++;
			$aCount['banners_hidden']++;
            unset($banners[$key]);
        }
    }
}

$oTpl->assign('clientId', $clientid);
$oTpl->assign('campaignId', $campaignid);
$oTpl->assign('aBanners', $banners);
$oTpl->assign('aCount', $aCount);
$oTpl->assign('hideinactive', $hideinactive);
$oTpl->assign('listorder', $listorder);
$oTpl->assign('orderdirection', $orderdirection);
$oTpl->assign('isManager', OA_Permission::isAccount(OA_ACCOUNT_MANAGER));

$oTpl->assign('canACL', !OA_Permission::isAccount(OA_ACCOUNT_ADVERTISER));
$oTpl->assign('canEdit', !OA_Permission::isAccount(OA_ACCOUNT_ADVERTISER) || OA_Permission::hasPermission(OA_PERM_BANNER_EDIT));
$oTpl->assign('canActivate', !OA_Permission::isAccount(OA_ACCOUNT_ADVERTISER) || OA_Permission::hasPermission(OA_PERM_BANNER_ACTIVATE));
$oTpl->assign('canDeactivate', !OA_Permission::isAccount(OA_ACCOUNT_ADVERTISER) || OA_Permission::hasPermission(OA_PERM_BANNER_DEACTIVATE));
$oTpl->assign('canDelete', !OA_Permission::isAccount(OA_ACCOUNT_ADVERTISER));


/*-------------------------------------------------------*/
/* Store preferences                                     */
/*-------------------------------------------------------*/

$session['prefs']['campaign-banners.php'][$campaignid]['hideinactive'] = $hideinactive;
$session['prefs']['campaign-banners.php'][$campaignid]['listorder'] = $listorder;
$session['prefs']['campaign-banners.php'][$campaignid]['orderdirection'] = $orderdirection;
$session['prefs']['inventory_entities'][OA_Permission::getEntityId()]['clientid'] = $clientid;
$session['prefs']['inventory_entities'][OA_Permission::getEntityId()]['campaignid'][$clientid] = $campaignid;
phpAds_SessionDataStore();


/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

$oTpl->display();

phpAds_PageFooter();

function buildHeaderModel($aEntities)
{
    global $phpAds_TextDirection;
    $aConf = $GLOBALS['_MAX']['CONF'];

    $advertiserId = $aEntities['clientid'];
    $campaignId = $aEntities['campaignid'];
    $agencyId = OA_Permission::getAgencyId();

    $entityString = _getEntityString($aEntities);
    $aOtherEntities = $aEntities;
    unset($aOtherEntities['campaignid']);
    $otherEntityString = _getEntityString($aOtherEntities);

    $advertiser = phpAds_getClientDetails ($advertiserId);
    $advertiserName = $advertiser ['clientname'];
    $campaignDetails = Admin_DA::getPlacement($campaignId);
    $campaignName = $campaignDetails['name'];
    if (!OA_Permission::isAccount(OA_ACCOUNT_ADVERTISER)) {
        $campaignEditUrl = "campaign-edit.php?clientid=$advertiserId&campaignid=$campaignId";
    }

    $builder = new OA_Admin_UI_Model_InventoryPageHeaderModelBuilder();
    $oHeaderModel = $builder->buildEntityHeader(array(
        array ('name' => $advertiserName, 'url' => '',
               'id' => $advertiserId, 'entities' => getAdvertiserMap($agencyId),
               'htmlName' => 'clientid'
              ),
        array ('name' => $campaignName, 'url' => $campaignEditUrl,
               'id' => $campaignId, 'entities' => getCampaignMap($advertiserId),
               'htmlName' => 'campaignid'
              ),
        array('name' => '')
    ), 'banners', 'list');

    return $oHeaderModel;
}


function getAdvertiserMap()
{
    $aAdvertisers = array();
    $dalClients = OA_Dal::factoryDAL('clients');
    if (OA_Permission::isAccount(OA_ACCOUNT_MANAGER)) {
        $agency_id = OA_Permission::getEntityId();
        $aAdvertisers = $dalClients->getAllAdvertisersForAgency($agency_id);
    }
    else if (OA_Permission::isAccount(OA_ACCOUNT_ADVERTISER)) {
        $advertiserId = OA_Permission::getEntityId();
        $aAdvertiser = $dalClients->getAdvertiserDetails($advertiserId);
        $aAdvertisers[$advertiserId] = $aAdvertiser;
    }

    //TODO do we need to filter out system entities here, or will the DAO do that?
    $aAdvertiserMap = array();
    foreach ($aAdvertisers as $clientid => $aClient) {
        $aAdvertiserMap[$clientid] = array('name' => $aClient['clientname'],
            'url' => "advertiser-campaigns.php?clientid=".$clientid);
    }

    return $aAdvertiserMap;
}


function getCampaignMap($advertiserId)
{
    $aCampaigns = Admin_DA::getPlacements(array('advertiser_id' => $advertiserId));

    $aCampaignMap = array();
    foreach ($aCampaigns as $campaignId => $aCampaign) {
        $campaignName = $aCampaign['name'];
        // mask campaign name if anonymous campaign
        $campaign_details = Admin_DA::getPlacement($campaignId);
        $campaignName = MAX_getPlacementName($campaign_details);
        $aCampaignMap[$campaignId] = array('name' => $campaignName);
    }

    return $aCampaignMap;
}

?>