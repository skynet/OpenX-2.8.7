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
$Id: affiliate-channels.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/www/admin/lib-statistics.inc.php';
require_once MAX_PATH . '/lib/max/other/html.php';


// Register input variables
phpAds_registerGlobal ('acl', 'action', 'submit');


/*-------------------------------------------------------*/
/* Affiliate interface security                          */
/*-------------------------------------------------------*/
OA_Permission::enforceAccount(OA_ACCOUNT_MANAGER);
if (!empty($affiliateid) && !OA_Permission::hasAccessToObject('affiliates', $affiliateid)) { //check if can see given website
    $page = basename($_SERVER['SCRIPT_NAME']);
    OX_Admin_Redirect::redirect($page);
}

/*-------------------------------------------------------*/
/* Init data                                             */
/*-------------------------------------------------------*/
//get websites and set the current one
$aWebsites = getWebsiteMap();
if (empty($affiliateid)) { //if it's empty
    if ($session['prefs']['inventory_entities'][OA_Permission::getEntityId()]['affiliateid']) {
        //try previous one from session
        $sessionWebsiteId = $session['prefs']['inventory_entities'][OA_Permission::getEntityId()]['affiliateid'];
        if (isset($aWebsites[$sessionWebsiteId])) { //check if 'id' from session was not removed
            $affiliateid = $sessionWebsiteId;
        }
    }
    if (empty($affiliateid)) { //was empty, is still empty - just pick one, no need for redirect
        $ids = array_keys($aWebsites);
        $affiliateid = !empty($ids) ? $ids[0] : -1; //if no websites set to non-existent id
    }
}
else {
    if (!isset($aWebsites[$affiliateid])) { //bad id, redirect
        $page = basename($_SERVER['SCRIPT_NAME']);
        OX_Admin_Redirect::redirect($page);
    }
}


/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

$oHeaderModel = buildHeaderModel($affiliateid);
phpAds_PageHeader(null, $oHeaderModel);


/*-------------------------------------------------------*/
/* Main code                                             */
/*-------------------------------------------------------*/

require_once MAX_PATH . '/lib/OA/Admin/Template.php';

$oTpl = new OA_Admin_Template('channel-index.html');

$channels = Admin_DA::getChannels(array('publisher_id' => $affiliateid), true);
$aChannels = array();
foreach ($channels as $channelId => $channel) {
	$aChannels[$channelId] = $channel;
}

$oTpl->assign('aChannels', $aChannels);
$oTpl->assign('entityUrl', 'affiliate-channels.php');
$oTpl->assign('entityId', 'affiliateid=' . $affiliateid);
$oTpl->assign('affiliateId', $affiliateid);


/*-------------------------------------------------------*/
/* Store preferences                                     */
/*-------------------------------------------------------*/

$session['prefs']['inventory_entities'][OA_Permission::getEntityId()]['affiliateid'] = $affiliateid;
phpAds_SessionDataStore();


/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

$oTpl->display();
phpAds_PageFooter();


function buildHeaderModel($websiteId)
{
    $doAffiliates = OA_Dal::factoryDO('affiliates');
    if ($doAffiliates->get($websiteId)) {
        $aWebsite = $doAffiliates->toArray();
    }

    $websiteName = $aWebsite['name'];
    if (OA_Permission::isAccount(OA_ACCOUNT_ADMIN, OA_ACCOUNT_MANAGER)) {
        $websiteEditUrl = "affiliate-edit.php?affiliateid=$websiteId";
    }

    $builder = new OA_Admin_UI_Model_InventoryPageHeaderModelBuilder();
    $oHeaderModel = $builder->buildEntityHeader(array(
        array ('name' => $websiteName, 'url' => $websiteEditUrl,
               'id' => $websiteId, 'entities' => getWebsiteMap(),
               'htmlName' => 'affiliateid'
              ),
        array('name' => '')
    ), 'channels', 'list');

    return $oHeaderModel;
}


function getWebsiteMap()
{
    $doAffiliates = OA_Dal::factoryDO('affiliates');
    if (OA_Permission::isAccount(OA_ACCOUNT_MANAGER)) { //should this constraint be added always instead of only for managers?
        $doAffiliates->agencyid = OA_Permission::getAgencyId();
    }
    if (OA_Permission::isAccount(OA_ACCOUNT_TRAFFICKER)) {
        $doAffiliates->agencyid = OA_Permission::getEntityId();
    }
    $doAffiliates->addSessionListOrderBy($sortPageName);
    $doAffiliates->find();

    $aWebsiteMap = array();
    while ($doAffiliates->fetch() && $row = $doAffiliates->toArray()) {
        $aWebsiteMap[$row['affiliateid']] = array('name' => $row['name'],
            'url' => "affiliate-edit.php?affiliateid=".$row['affiliateid']);
    }

    return $aWebsiteMap;
}

?>
