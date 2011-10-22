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
$Id: banner-delete.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/www/admin/lib-storage.inc.php';
require_once MAX_PATH . '/www/admin/lib-zones.inc.php';
require_once MAX_PATH . '/www/admin/lib-statistics.inc.php';
require_once MAX_PATH . '/lib/OA/Maintenance/Priority.php';

// Register input variables
phpAds_registerGlobal ('returnurl');

// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_MANAGER);
OA_Permission::enforceAccessToObject('clients',   $clientid);
OA_Permission::enforceAccessToObject('campaigns', $campaignid);

/*-------------------------------------------------------*/
/* Main code                                             */
/*-------------------------------------------------------*/

if (!empty($bannerid)) {
    $ids = explode(',', $bannerid);
    while (list(,$bannerid) = each($ids)) {
        $doBanners = OA_Dal::factoryDO('banners');
        $doBanners->bannerid = $bannerid;
        if ($doBanners->get($bannerid)) {
            $aBanner = $doBanners->toArray();
        }

        $doBanners->delete();
    }

    // Queue confirmation message
    $translation = new OX_Translation ();
    
    if (count($ids) == 1) {
        $translated_message = $translation->translate ($GLOBALS['strBannerHasBeenDeleted'], array(
            htmlspecialchars($aBanner['description'])
        ));
    } else {
        $translated_message = $translation->translate ($GLOBALS['strBannersHaveBeenDeleted']);
    }
    
    OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);} 

// Run the Maintenance Priority Engine process
OA_Maintenance_Priority::scheduleRun();

// Rebuild cache
// include_once MAX_PATH . '/lib/max/deliverycache/cache-'.$conf['delivery']['cache'].'.inc.php';
// phpAds_cacheDelete();

if (empty($returnurl)) {
    $returnurl = 'campaign-banners.php';
}

header("Location: ".$returnurl."?clientid=".$clientid."&campaignid=".$campaignid);

?>