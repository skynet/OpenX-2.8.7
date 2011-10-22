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
$Id:$
*/

/**
 * @package    OpenXDll
 * @author     Ivan Klishch <iklishch@lohika.com>
 *
 * This file describes the AdvertiserInfo class.
 *
 */

// Require the base info class.
require_once 'Info.php';

/**
 *  The AdvertiserInfo class extends the base Info class and contains information about advertisers.
 *
 */

class OA_Dll_AdvertiserInfo extends OA_Info
{

    /**
     * The advertiserID variable is the unique ID for the advertiser.
     *
     * @var integer $advertiserId
     */
    var $advertiserId;

    /**
     * This field contains the ID of the agency account.
     *
     * @var integer $accountId
     */
    var $accountId;

    /**
     * The agencyID variable is the ID of the agency to which the advertiser is associated.
     *
     * @var integer $agencyId
     */
    var $agencyId;

    /**
     * The advertiserName variable is the name of the advertiser.
     *
     * @var string $advertiserName
     */
    var $advertiserName;

    /**
     * The contactName variable is the name of the contact.
     *
     * @var string $contactName
     */
    var $contactName;

    /**
     * The emailAddress variable is the email address for the contact.
     *
     * @var string $emailAddress
     */
    var $emailAddress;

    /**
     * This field provides any additional comments to be stored.
     *
     * @var string $comments
     */
    var $comments;

    function getFieldsTypes()
    {
        return array(
                    'advertiserId' => 'integer',
                    'accountId' => 'integer',
                    'agencyId' => 'integer',
                    'advertiserName' => 'string',
                    'contactName' => 'string',
                    'emailAddress' => 'string',
                    'comments' => 'string',
                );
    }
}

?>