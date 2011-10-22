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
$Id: Factory.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

require_once MAX_PATH . '/lib/OA/Maintenance/Priority/DeliveryLimitation/Empty.php';

/**
 * A class for creating {@link OA_Maintenance_Priority_DeliveryLimitation_Common}
 * subclass objects, depending on the delivery limitation passed in.
 *
 * @static
 * @package    OpenXMaintenance
 * @subpackage Priority
 * @author     Andrew Hill <andrew.hill@openx.org>
 */
class OA_Maintenance_Priority_DeliveryLimitation_Factory
{
    static $aPlugins;

    /**
     * A factory method to return the appropriate
     * OA_Maintenance_Priority_DeliveryLimitation_Common
     * subclass object (one of OA_Maintenance_Priority_DeliveryLimitation_Date,
     * OA_Maintenance_Priority_DeliveryLimitation_Day,
     * OA_Maintenance_Priority_DeliveryLimitation_Empty or
     * OA_Maintenance_Priority_DeliveryLimitation_Hour), depending on the data
     * provided.
     *
     * @static
     * @param array $aDeliveryLimitation An array containing the details of a delivery limitation
     *                                   associated with an ad. For example:
     *                                   array(
     *                                       [ad_id]             => 1
     *                                       [logical]           => and
     *                                       [type]              => Time:Hour
     *                                       [comparison]        => ==
     *                                       [data]              => 1,7,18,23
     *                                       [executionorder]    => 1
     *                                   )
     * @return object OA_Maintenance_Priority_DeliveryLimitation_Common
     */
    function &factory($aDeliveryLimitation)
    {
        // Load plugins if not already in cache
        if (!isset(self::$aPlugins)) {
            self::$aPlugins = OX_Component::getComponents('deliveryLimitations', null, false);
        }

        // Return instance of the MPE DL class
        if (isset(self::$aPlugins[$aDeliveryLimitation['type']])) {
            return self::$aPlugins[$aDeliveryLimitation['type']]->getMpeClassInstance($aDeliveryLimitation);
        }

        // Unknown plugin? Return the empty MPE DL class
        return new OA_Maintenance_Priority_DeliveryLimitation_Empty($aDeliveryLimitation);
    }

}

?>