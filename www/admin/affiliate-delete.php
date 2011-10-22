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
$Id: affiliate-delete.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/


// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/www/admin/lib-zones.inc.php';
require_once MAX_PATH . '/lib/OA/Central/AdNetworks.php';

// Register input variables
phpAds_registerGlobal ('returnurl');

// Initialise Ad  Networks
$oAdNetworks = new OA_Central_AdNetworks();

// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_MANAGER);

/*-------------------------------------------------------*/
/* Main code                                             */
/*-------------------------------------------------------*/

if (!empty($affiliateid)) {
    $ids = explode(',', $affiliateid);
    while (list(,$affiliateid) = each($ids)) {

        // Security check
        OA_Permission::enforceAccessToObject('affiliates', $affiliateid);
    
        $doAffiliates = OA_Dal::factoryDO('affiliates');
        $doAffiliates->affiliateid = $affiliateid;
        if ($doAffiliates->get($affiliateid)) {
            $aAffiliate = $doAffiliates->toArray();
        }

        // User unsubscribed from adnetworks
        $oacWebsiteId = $doAffiliates->as_website_id;
        $aPublisher = array(
            array(
                    'id'            => $affiliateid,
                    'an_website_id' => $oacWebsiteId,
                )
            );
        $oAdNetworks->unsubscribeWebsites($aPublisher);
        
        $doAffiliates->delete();
    }
    
    // Queue confirmation message
    $translation = new OX_Translation ();
    
    if (count($ids) == 1) {
        $translated_message = $translation->translate ( $GLOBALS['strWebsiteHasBeenDeleted'], array(
            htmlspecialchars($aAffiliate['name'])
        ));
    } else {
        $translated_message = $translation->translate ( $GLOBALS['strWebsitesHaveBeenDeleted']);
    }
    
    OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
}

if (empty($returnurl))
    $returnurl = 'website-index.php';

Header("Location: ".$returnurl);

?>