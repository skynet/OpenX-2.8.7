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
$Id: tracker-modify.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/www/admin/lib-storage.inc.php';
require_once MAX_PATH . '/www/admin/lib-zones.inc.php';

// Register input variables
phpAds_registerGlobal (
     'duplicate'
    ,'moveto'
    ,'returnurl'
);


// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_MANAGER);
OA_Permission::enforceAccessToObject('clients', $clientid);
OA_Permission::enforceAccessToObject('trackers', $trackerid);

/*-------------------------------------------------------*/
/* Main code                                             */
/*-------------------------------------------------------*/

if (!empty($trackerid))
{
    if (!empty($moveto))
    {
        // Delete any campaign-tracker links
        $doCampaign_trackers = OA_Dal::factoryDO('campaigns_trackers');
        $doCampaign_trackers->trackerid = $trackerid;
        $doCampaign_trackers->delete();

        // Move the tracker
        $doTrackers = OA_Dal::factoryDO('trackers');
        if ($doTrackers->get($trackerid)) {
            $doTrackers->clientid = $moveto;
            $doTrackers->update();
            
            // Queue confirmation message
            $trackerName = $doTrackers->trackername;
            $doClients = OA_Dal::factoryDO('clients');
            if ($doClients->get($moveto)) {
                $advertiserName = $doClients->clientname;
            }    
            $translation = new OX_Translation();
            $translated_message = $translation->translate ( $GLOBALS['strTrackerHasBeenMoved'],
                array(htmlspecialchars($trackerName), htmlspecialchars($advertiserName))
            );
            OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
        }

        Header ("Location: ".$returnurl."?clientid=".$moveto."&trackerid=".$trackerid);
        exit;
    }
    elseif (isset($duplicate) && $duplicate == 'true')
    {
        $doTrackers = OA_Dal::factoryDO('trackers');
        if ($doTrackers->get($trackerid))
        {
            $oldName = $doTrackers->trackername; 
            $new_trackerid = $doTrackers->duplicate();
            
            if ($doTrackers->get($new_trackerid)) {
                $newName = $doTrackers->trackername;
            }
            
            // Queue confirmation message
            $translation = new OX_Translation();
            $translated_message = $translation->translate ( $GLOBALS['strTrackerHasBeenDuplicated'],
                array(MAX::constructURL(MAX_URL_ADMIN, "tracker-edit.php?clientid=$clientid&trackerid=$trackerid"), 
                    htmlspecialchars($oldName),
                    MAX::constructURL(MAX_URL_ADMIN, "tracker-edit.php?clientid=$clientid&trackerid=$new_trackerid"), 
                    htmlspecialchars($newName))
            );
            OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);
            
            
            Header ("Location: ".$returnurl."?clientid=".$clientid."&trackerid=".$new_trackerid);
            exit;
        }
    }
}

Header ("Location: ".$returnurl."?clientid=".$clientid."&trackerid=".$trackerid);

?>