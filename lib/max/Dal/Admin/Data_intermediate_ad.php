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
$Id: Data_intermediate_ad.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

require_once MAX_PATH . '/lib/max/Dal/Common.php';

class MAX_Dal_Admin_Data_intermediate_ad extends MAX_Dal_Common
{
    var $table = 'data_intermediate_ad';

    /**
     * A method to determine the number of impressions, clicks and conversions
     * delivered by a given campaign to date.
     *
     * Can also determine the delivery information up to a given operation
     * interval end date.
     *
     * @param integer    $campaignId The campaign ID.
     * @param PEAR::Date $oDate      An optional date. If present, limits
     *                               delivery information to that which is
     *                               in or before this maximum possible
     *                               operation interval end date.
     * @return MDB2Record
     */
	function getDeliveredByCampaign($campaignId, $oDate = null)
    {
        $prefix = $this->getTablePrefix();
        $oDbh = OA_DB::singleton();
        $tableB = $oDbh->quoteIdentifier($prefix.'banners',true);
        $tableD = $oDbh->quoteIdentifier($prefix.'data_intermediate_ad',true);
        $query = "
            SELECT
                SUM(dia.impressions) AS impressions_delivered,
                SUM(dia.clicks) AS clicks_delivered,
                SUM(dia.conversions) AS conversions_delivered
            FROM
                {$tableB} AS b,
                {$tableD} AS dia
            WHERE
                b.campaignid = " . DBC::makeLiteral($campaignId) . "
                AND
                b.bannerid = dia.ad_id";
        if (!is_null($oDate)) {
            $query .= "
                AND
                dia.interval_end <= '" . $oDate->format('%Y-%m-%d %H:%M:%S') . "'";
        }
        return DBC::FindRecord($query);
    }

    /**
     * A method to determine the number of impressions, clicks and conversions
     * delivered by a given ecpm campaign to date.
     *
     * Can also determine the delivery information up to a given operation
     * interval end date.
     *
     * @param integer    $agencyId The agency ID.
     * @param PEAR::Date $oDate      Limits delivery information to that which is
     *                               after this date.
     * @param integer    $priority Campaign priority (by default eCPM priority).
     * @return array
     */
	function getDeliveredEcpmCampainImpressionsByAgency($agencyId, $oDate, $priority = null)
    {
        $prefix = $this->getTablePrefix();
        $oDbh = OA_DB::singleton();
        if (is_null($priority)) {
            $priority = DataObjects_Campaigns::PRIORITY_ECPM;
        }
        $query = "
            SELECT
                c.campaignid AS campaignid,
                SUM(dia.impressions) AS impressions_delivered
            FROM
                {$oDbh->quoteIdentifier($prefix.'clients',true)} AS cl,
                {$oDbh->quoteIdentifier($prefix.'campaigns',true)} AS c,
                {$oDbh->quoteIdentifier($prefix.'banners',true)} AS b,
                {$oDbh->quoteIdentifier($prefix.'data_intermediate_ad',true)} AS dia
            WHERE
                cl.agencyid = " . DBC::makeLiteral($agencyId) . "
                AND c.status = ".OA_ENTITY_STATUS_RUNNING."
                AND c.priority = ".$priority."
                AND cl.clientid = c.clientid
                AND b.bannerid = dia.ad_id
                AND b.campaignid = c.campaignid
                AND dia.interval_end >= '" . $oDate->format('%Y-%m-%d %H:%M:%S') . "'
            GROUP BY
                c.campaignid";
        $rs = DBC::NewRecordSet($query);
        if (PEAR::isError($rs)) {
            return false;
        }
        return $rs->getAll(array(), 'campaignid');
    }

    /**
     * TODO: Should we refactor this method in more general one?
     * (maybe by creating common abstract class for all summary tables?)
     *
     * @param string $operation  Either + or -
     * @param int $basketValue
     * @param int $numItems
     * @param int $ad_id
     * @param int $creative_id
     * @param int $zone_id
     * @param strin $day
     * @param string $hour
     * @return unknown
     */
	function addConversion($operation, $basketValue, $numItems,
	                       $ad_id, $creative_id, $zone_id, $day, $hour,
	                       $table = null)
    {
        $prefix = $this->getTablePrefix();
        if ($operation != '-') {
            $operation = '+';
        }
        if ($table == null) {
            $table = $this->table;
        }
        $oDbh = OA_DB::singleton();
        $table = $oDbh->quoteIdentifier($prefix.$table,true);
        $query = '
            UPDATE '.$table
                .' SET conversions=conversions'.$operation.'1
                    , total_basket_value=total_basket_value'.$operation.DBC::makeLiteral($basketValue).'
                    , total_num_items=total_num_items'.$operation.DBC::makeLiteral($numItems).'
                    , updated = \''. OA::getNow() .'\'
                WHERE
                       ad_id       = '.DBC::makeLiteral($ad_id).'
                   AND creative_id = '.DBC::makeLiteral($creative_id).'
                   AND zone_id     = '.DBC::makeLiteral($zone_id).'
                   AND date_time   = '.DBC::makeLiteral(sprintf("%s %02d:00:00", $day, $hour));

        return DBC::execute($query);
    }
}

?>
