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
$Id: AggregateBucketProcessingStrategyPgsql.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

require_once LIB_PATH . '/Extension/deliveryLog/BucketProcessingStrategy.php';
require_once MAX_PATH . '/lib/OA/DB/Distributed.php';
require_once MAX_PATH . '/lib/wact/db/db.inc.php';

/**
 * The default OX_Extension_DeliveryLog_BucketProcessingStrategy for PgSQL,
 * to migrate data from aggregate buckets from OpenX delivery (edge) servers,
 * as well as from any intermediate aggregation servers, to an upstream
 * aggregation server, or the upstream central database server.
 *
 * Should be used by any plugin component that uses the deliveryLog extension,
 * and that has aggregate bucket data that needs to be migrated when running
 * OpenX in distributed statistics mode.
 *
 * @package    OpenXExtension
 * @subpackage DeliveryLog
 * @author     David Keen <david.keen@openx.org>
 */
class OX_Extension_DeliveryLog_AggregateBucketProcessingStrategyPgsql implements OX_Extension_DeliveryLog_BucketProcessingStrategy
{

    /**
     * Process an aggregate-type bucket.  This is MySQL specific.
     *
     * @param Plugins_DeliveryLog $oBucket a reference to the using (context) object.
     * @param Date $oEnd A PEAR_Date instance, interval_start to process up to (inclusive).
     */
    public function processBucket($oBucket, $oEnd)
    {
        $sTableName = $oBucket->getBucketTableName();
        $oMainDbh =& OA_DB_Distributed::singleton();

        if (PEAR::isError($oMainDbh)) {
            MAX::raiseError($oMainDbh, MAX_ERROR_DBFAILURE, PEAR_ERROR_DIE);
        }

        OA::debug('  - Processing the ' . $sTableName . ' table for data with operation interval start equal to or before ' . $oEnd->format('%Y-%m-%d %H:%M:%S') . ' ' . $oEnd->tz->getShortName() , PEAR_LOG_INFO);

        // Select all rows with interval_start <= previous OI start.
        $rsData =& $this->getBucketTableContent($sTableName, $oEnd);
        $rowCount = $rsData->getRowCount();

        OA::debug('  - '.$rsData->getRowCount().' records found', PEAR_LOG_DEBUG);

        if ($rowCount) {
            // We can't do bulk inserts with ON DUPLICATE.
            $aExecQueries = array();
            while ($rsData->fetch()) {
                // Get first row
                $aRow = $rsData->toArray();
               // Insert or update
                $aExecQueries[] = "SELECT bucket_update_{$sTableName}(".
                    join(',', array_map(array(&$oMainDbh, 'quote'), $aRow)).
                    ")";
            }

            if (count($aExecQueries)) {
                foreach ($aExecQueries as $execQuery) {
                    $result = $oMainDbh->exec($execQuery);
                    if (PEAR::isError($result)) {
                        MAX::raiseError($result, MAX_ERROR_DBFAILURE, PEAR_ERROR_DIE);
                    }
                }
            }
        }
    }

    /**
     * A method to prune a bucket of all records up to and
     * including the timestamp given.
     *
     * @param Date $oEnd   Prune until this interval_start (inclusive).
     * @param Date $oStart Only prune before this interval_start date (inclusive)
     *                     as well. Optional.
     * @return mixed Either the number of rows pruned, or an MDB2_Error objet.
     */
    public function pruneBucket($oBucket, $oEnd, $oStart = null)
    {
        $sTableName = $oBucket->getBucketTableName();
        if (!is_null($oStart)) {
            OA::debug('  - Pruning the ' . $sTableName . ' table for data with operation interval start between ' . $oStart->format('%Y-%m-%d %H:%M:%S') . ' ' . $oStart->tz->getShortName() . ' and ' . $oEnd->format('%Y-%m-%d %H:%M:%S') . ' ' . $oEnd->tz->getShortName(), PEAR_LOG_DEBUG);
        } else {
            OA::debug('  - Pruning the ' . $sTableName . ' table for all data with operation interval start equal to or before ' . $oEnd->format('%Y-%m-%d %H:%M:%S') . ' ' . $oEnd->tz->getShortName(), PEAR_LOG_DEBUG);
        }
        $query = "
            DELETE FROM
                {$sTableName}
            WHERE
                interval_start <= " . DBC::makeLiteral($oEnd->format('%Y-%m-%d %H:%M:%S'));
        if (!is_null($oStart)) {
            $query .= "
                AND
                interval_start >= " . DBC::makeLiteral($oStart->format('%Y-%m-%d %H:%M:%S'));
        }
        $oDbh = OA_DB::singleton();
        return $oDbh->exec($query);
    }

    /**
     * A method to retrieve the table content as a recordset.
     *
     * @param string $sTableName The bucket table to process
     * @param Date $oEnd A PEAR_Date instance, ending interval_start to process.
     * @return MySqlRecordSet A recordset of the results
     */
    private function getBucketTableContent($sTableName, $oEnd)
    {
        $query = "
            SELECT
                *
            FROM
                {$sTableName}
            WHERE
                interval_start <= " . DBC::makeLiteral($oEnd->format('%Y-%m-%d %H:%M:%S'));
        $rsDataRaw = DBC::NewRecordSet($query);
        $rsDataRaw->find();

        return $rsDataRaw;
    }
}

?>
