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
$Id: Zones.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

require_once MAX_PATH . '/lib/max/Dal/Common.php';
require_once MAX_PATH . '/lib/OA/Central/AdNetworks.php';
require_once MAX_PATH . '/lib/OA/Dal/Statistics/Zone.php';

class MAX_Dal_Admin_Zones extends MAX_Dal_Common
{
    var $table = 'zones';

    var $orderListName = array(
        'name' => 'zonename',
        'id'   => 'zoneid',
        'size' => array('width', 'height')
    );

    /**
     * Setting up only when some methods need to get categories
     *
     * @var OA_Central_AdNetworks
     */
    var $_oOaCentralAdNetworks;

	function getZoneByKeyword($keyword, $agencyId = null, $affiliateId = null)
    {
        $whereZone = is_numeric($keyword) ? " OR z.zoneid=$keyword" : '';
        $prefix = $this->getTablePrefix();
        $oDbh = OA_DB::singleton();
        $tableZ = $oDbh->quoteIdentifier($prefix.'zones',true);
        $tableA = $oDbh->quoteIdentifier($prefix.'affiliates',true);

        $query = "
        SELECT
            z.zoneid AS zoneid,
            z.zonename AS zonename,
            z.description AS description,
            a.affiliateid AS affiliateid
        FROM
            {$tableZ} AS z,
            {$tableA} AS a
        WHERE
            (
            z.affiliateid=a.affiliateid
            AND (z.zonename LIKE "  . DBC::makeLiteral('%' . $keyword . '%') . "
            OR description LIKE "  . DBC::makeLiteral('%' . $keyword . '%') . "
            $whereZone)
            )
    ";

        if($agencyId !== null) {
            $query .= " AND a.agencyid=" . DBC::makeLiteral($agencyId);
        }
        if($affiliateId !== null) {
            $query .= " AND a.affiliateid=" . DBC::makeLiteral($affiliateId);
        }

        return DBC::NewRecordSet($query);
    }

    /**
     * Gets the details to for generating invocation code.
     *
     * @param int $zoneId  the zone ID.
     * @return array  zone details to be passed into MAX_Admin_Invocation::placeInvocationForm()
     *
     * @see MAX_Admin_Invocation::placeInvocationForm()
     */
    function getZoneForInvocationForm($zoneId)
    {
        $prefix = $this->getTablePrefix();
        $oDbh = OA_DB::singleton();
        $tableZ = $oDbh->quoteIdentifier($prefix.'zones',true);
        $tableAf = $oDbh->quoteIdentifier($prefix.'affiliates',true);

        $query = "
            SELECT
                z.affiliateid,
                z.width,
                z.height,
                z.delivery,
                af.website
            FROM
                {$tableZ} AS z,
                {$tableAf} AS af
            WHERE
                z.zoneid = " . DBC::makeLiteral($zoneId) . "
            AND af.affiliateid = z.affiliateid";

        $rsZone = DBC::FindRecord($query);
        return $rsZone->toArray();
    }

    /**
     * Function returns an array of websites, and each website has (not empty!) array of zones of given category
     * All returned zones are linked to given agency.
     * Addational returned array contains iformation if zone is linked to given campaign.
     * Output can be limited to linked or avaliable (not linked) zones
     *
     * Returned array structure:
     * array (
     *   (int) affiliateId =>
     *   array (
     *     'name'            => 'website name',
     *     'oac_category_id' => 'website category ID',
     *     'category'        => 'category name',
     *     'linked'          => (boolean or null),
     *     'zones' =>
     *        array (
     *          (int) zoneId =>
     *             array ( 'name'            => 'zone name',
     *                     'oac_category_id' => 'zone category ID',
     *                     'category'        => 'category name',
     *                     'campaign_stats'  => (boolean), // true if ecp, cr, ctr are statistics calculated for given campaign
     *                     'ecpm'            => 'zone eCPM',
     *                     'cr'              => 'zone CR',
     *                     'ctr'             => 'zone CTR',
     *                     'linked'          => (boolean or null)
     *                   )
     *             ...
     *             )
     *        )
     *   )
     *   ...
     * }
     *
     * If category is null all zones are returned
     * If campaign is null isLinked is set to null as well
     *
     * @param int $agencyId   agency Id.
     * @param int $categoryId category Id.
     * @param int $campaignId campaign Id.
     * @param boolean $returnLinked true - returns linked zones to campaign, false - returns avaliable zones, null - return all zones
     * @param string $searchPhrase A part of website name or zone name
     * @param boolean $includeEmailZones false (default) - don't include Email/Newsletter zone, true - include Email/Newsletter in result
     * @return array  of websites including array of zones
     */
    function getWebsitesAndZonesListByCategory($agencyId, $categoryId = null, $campaignId = null, $returnLinked = null, $searchPhrase = null, $includeEmailZones = false)
    {
        $aZones = $this->getZonesListByCategory($agencyId, $categoryId, $campaignId, $returnLinked, $searchPhrase, $includeEmailZones);
        if (PEAR::isError($aZones)) {
            return $aZones;
        }

        // Get names for Categories and remember all zones IDs in separate array
        $this->_getNamesForCategories($aZones);

        // Convert 'flat' array of zones to 'tree like' array of websites and zones and add statistics
        $aWebsitesAndZones = array();
        foreach($aZones as $aZone) {
            if (!array_key_exists($aZone['affiliateid'], $aWebsitesAndZones)) {
                $aWebsitesAndZones[$aZone['affiliateid']] =
                    array (
                      'name'            => $aZone['affiliatename'],
                      'oac_category_id' => $aZone['affiliate_oac_category_id'],
                      'category'        => $aZone['affiliate_category'],
                      'linked'          => null
                    );
            }
            $aWebsitesAndZones[$aZone['affiliateid']]['zones'][$aZone['zoneid']] =
                   array (
                     'name'            => $aZone['zonename'],
                     'oac_category_id' => $aZone['zone_oac_category_id'],
                     'category'        => $aZone['zone_category'],
                     'campaign_stats'  => $aZone['campaign_stats'],
                     'ecpm'            => $aZone['ecpm'],
                     'cr'              => $aZone['cr'],
                     'ctr'             => $aZone['ctr'],
                     'linked'          => $aZone['islinked']
                   );
        }

        return $aWebsitesAndZones;
    }

    /**
     * Function returns an array of zones of given category linked to given agency.
     * Additionally returned array contains information  if zone is linked to given campaign.
     * Output can be limited to linked or avaliable (not linked) zones
     *
     * If given category matches to website category all zones from this website are returned
     *
     * Returned array structure:
     * array (
     *   0 => array (
     *          'zoneid'                    => (int) zoneId
     *          'zonename'                  => 'zone name'
     *          'zone_oac_category_id'      => (int) zone's oac category Id
     *          'affiliateid'               => (int) affiliateId (website Id)
     *          'affiliate_oac_category_id' => (int) website's oac category Id
     *          'affiliatename'             => 'website name'
     *          'islinked'                  => (boolean or null) iformation if zone is linked to given campaign
     *        )
     *   ...
     * )
     *
     * If category is null all zones are returned
     * If campaign is null isLinked is set to null
     *
     * @param int $agencyId   agency Id.
     * @param int $categoryId category Id. if categoryId is -1 then returns zones without category
     * @param int $campaignId campaign Id.
     * @param boolean $returnLinked true - returns linked zones to campaign, false - returns avaliable zones, null - return all zones
     * @param string $searchPhrase A part of website name or zone name
     * @param boolean $includeEmailZones false (default)- don't include Email/Newsletter zone, true - include Email/Newsletter in result
     * @return array of zones
     */
    function getZonesListByCategory($agencyId, $categoryId = null, $campaignId = null, $returnLinked = null, $searchPhrase = null, $includeEmailZones = false)
    {
        if (empty($agencyId) ||
            (is_null($campaignId) && $returnLinked === true) ) {
            return array();
        }

        $aQuery= $this->_prepareGetZonesByCategoryQuery($agencyId, $categoryId, $campaignId, $returnLinked, $searchPhrase, $includeEmailZones);
        $query = $aQuery['select'].$aQuery['from'].$aQuery['where'].$aQuery['order by'];

        $rsZones = DBC::NewRecordSet($query);
        if (PEAR::isError($rsZones)) {
            return $rsZones;
        }

        $aZones = $rsZones->getAll();
        // If campaignId wasn't given we leave null values in islinked row
        if (!empty($campaignId)) {
            // Change null values in islinked row to false and others values to true
            foreach ($aZones as $key => $aZone) {
                if (is_null($aZone['islinked'])) {
                    $aZones[$key]['islinked'] = false;
                } else {
                    $aZones[$key]['islinked'] = true;
                }
            }
        }

        // for Market campaigns, we need to remove all zones that are not IAB sized        
		// or zones that are not Banner type
        $doCampaign = OA_Dal::factoryDO('campaigns');
        $doCampaign->campaignid = $campaignId;
        $doCampaign->find();
        $doCampaign->fetch();
        if($doCampaign->type == DataObjects_Campaigns::CAMPAIGN_TYPE_MARKET_CONTRACT) {
            
            $invalidIds = array();
            $oComponent = &OX_Component::factory ( 'admin', 'oxMarket', 'oxMarket');

            $allowedIabSizes = $oComponent->getPublisherConsoleApiClient()->getCreativeSizes();
            foreach($aZones as $id => $zone) {
                $zoneSizeKey = $zone['width'] . 'x' . $zone['height'];
                if( (!isset($allowedIabSizes[$zoneSizeKey])
                    && $zoneSizeKey != '-1x-1')
                       || $zone['type'] != phpAds_ZoneBanner
                       ) {
                    $invalidIds[] = $id;
                }
            }
            foreach($invalidIds as $invalidId) {
                unset($aZones[$invalidId]);
            }
        }
        
        return $aZones;
    }

    /**
     * Function returns number of zones of given category linked to given agency.
     * Output can be limited to linked or avaliable (not linked) zones
     * If category is null counts all zones
     *
     * @param int $agencyId   agency Id.
     * @param int $categoryId category Id. if categoryId is -1 then counts zones without category
     * @param int $campaignId campaign Id.
     * @param boolean $returnLinked true - counts linked zones, false - counts avaliable zones, null - counts all zones
     * @param string $searchPhrase A part of website name or zone name
     * @param boolean $includeEmailZones false (default)- don't include Email/Newsletter zone, true - include Email/Newsletter in result
     * @return int number of zones thats match to given conditions
     */
    function countZones($agencyId, $categoryId = null, $campaignId = null, $returnLinked = null, $searchPhrase = null, $includeEmailZones = false) {
        if (empty($agencyId) ||
            (is_null($campaignId) && $returnLinked === true) ) {
            return 0;
        }
        $aQuery= $this->_prepareGetZonesByCategoryQuery($agencyId, $categoryId, $campaignId, $returnLinked, $searchPhrase, $includeEmailZones);
        $aQuery['select'] = "
            Select
                count(z.zoneid) as zones";
        $query = $aQuery['select'].$aQuery['from'].$aQuery['where'];
        $rsZones = DBC::NewRecordSet($query);
        if (PEAR::isError($rsZones)) {
            return $rsZones;
        }
        $aZones = $rsZones->getAll();
        return $aZones[0];
    }
    /**
     * Bulid query for getZonesListByCategory
     * this function is also used by countZones
     *
     * @param int $agencyId   agency Id.
     * @param int $categoryId category Id. if categoryId is -1 then query will return zones without category
     * @param int $campaignId campaign Id.
     * @param boolean $returnLinked true - query will return linked zones to campaign, false - query will return avaliable zones, null - return all zones
     * @param string $searchPhrase A part of website name or zone name
     * @param boolean $includeEmailZones false (default)- don't include Email/Newsletter zone, true - include Email/Newsletter in result
     * @return array of strings - Returned query is divided to part (keys in array) 'select', 'from', 'where', 'order by'
     * @see getZonesListByCategory
     */
    function _prepareGetZonesByCategoryQuery($agencyId, $categoryId = null, $campaignId = null, $returnLinked = null, $searchPhrase = null, $includeEmailZones = false)
    {
        if (!empty($categoryId)) {
            $aCategories = $this->_getParentAndSubCategoriesIds($categoryId);
        }
        $prefix = $this->getTablePrefix();
        $aQuery['select']= "
            SELECT
                z.zoneid,
                z.zonename,
                z.oac_category_id as zone_oac_category_id,
                a.affiliateid,
                z.width as width,
                z.height as height,
                z.delivery as type,
                a.oac_category_id as affiliate_oac_category_id,
                a.name as affiliatename";
        $aQuery['from'] = "
            FROM
                {$prefix}zones AS z
                JOIN {$prefix}affiliates AS a ON (z.affiliateid = a.affiliateid)";

        $aQuery['where'] = "
            WHERE
                a.agencyid = " . DBC::makeLiteral($agencyId);

        if (!$includeEmailZones) {
            $aQuery['where'] .= "
                AND
                z.delivery <> " . MAX_ZoneEmail;
        }
        if (!empty($categoryId)) {
            if ($categoryId == -1) {
                $aQuery['where'] .= "
                    AND z.oac_category_id IS NULL
                    ";
            } else {
                $aQuery['where'] .= "
                    AND
                    ( z.oac_category_id IN (" . implode(",",$aCategories) . ")
                      OR
                      a.oac_category_id IN (" . implode(",",$aCategories) . ")
                    )";
            }
        }
        if (!empty($searchPhrase)) {
            $aQuery['where'] .= "
                AND
                ( UPPER(z.zonename) like(UPPER(" . DBC::makeLiteral("%".$searchPhrase."%") . "))
                  OR
                  UPPER(a.name) like(UPPER(" . DBC::makeLiteral("%".$searchPhrase."%") . "))
                )";
        }

        if (!empty($campaignId)) {
            $aQuery['from'] .= "
                LEFT JOIN {$prefix}placement_zone_assoc AS pza
                    ON ( z.zoneid = pza.zone_id
                         AND
                         pza.placement_id = " . DBC::makeLiteral($campaignId) . "
                       )";
            $aQuery['select'] .= ",
                pza.placement_id AS islinked";
            if ($returnLinked === true) {
                $aQuery['where'] .= "
                    AND pza.placement_id IS NOT NULL
                    ";
            } elseif ($returnLinked === false) {
                $aQuery['where'] .= "
                    AND pza.placement_id IS NULL
                    ";
            }
        } else {
            $aQuery['select'] .= ",
                null AS islinked";
        }

        $aQuery['order by'] .= "
            ORDER BY a.name, z.zonename
            ";
        return $aQuery;
    }


    /**
      * Adds category names to array of zones ($aZones)
      *
      * @param array &$aZones array of zones - result of getZonesListByCategory
      */
    function _getNamesForCategories(&$aZones)
    {
        if (is_null($this->_oOaCentralAdNetworks)) {
            $this->_oOaCentralAdNetworks = new OA_Central_AdNetworks();
        }
        $aCategories = $this->_oOaCentralAdNetworks->getCategoriesFlat();
        foreach ($aZones as $k => $aZone) {
            $aZones[$k]['zone_category']      = $aCategories[$aZone['zone_oac_category_id']];
            $aZones[$k]['affiliate_category'] = $aCategories[$aZone['affiliate_oac_category_id']];
        }
    }

     /**
      * Adds statistics data to array of websites and zones ($aWebsitesAndZones)
      *
      * @param array &$aZones array of websites and zones - result of getWebsitesAndZonesListByCategory
      * @param int $campaignId campaign Id.
      */
     function mergeStatistics(&$aWebsitesAndZones, $campaignId)
     {
        // Get list of zones IDs
        $aZonesIDs = array();
        $aZonesWebsite = array();
        foreach ($aWebsitesAndZones as $websiteId => $aWebsite) {
            foreach (array_keys($aWebsite['zones']) as $zoneId) {
                $aZonesIDs[] = $zoneId;
                $aZonesWebsite[$zoneId] = $websiteId;
            }

        }
        // Get statistics for zones for this campaign
        $oOaDalStatisticsZone = new OA_Dal_Statistics_Zone();
        $aZoneCampaignStatistics = $oOaDalStatisticsZone->getZonesPerformanceStatistics($aZonesIDs, $campaignId);
        if (isset($campaignId)) {
            // If there are zones that have no statistics for campaign, calculate overall statistics
            $aZoneWithMissingStatsIds = array();
            foreach ($aZonesIDs as $zoneId) {
                if (!isset($aZoneCampaignStatistics[$zoneId]['CTR']) &&
                    !isset($aZoneCampaignStatistics[$zoneId]['CR']) &&
                    !isset($aZoneCampaignStatistics[$zoneId]['eCPM'])
                   ) {
                    $aZoneWithMissingStatsIds[] = $zoneId;
                }
            }
            $aZoneGlobalStatistics = $oOaDalStatisticsZone->getZonesPerformanceStatistics($aZoneWithMissingStatsIds);
        } else {
            // If campaign ID isn't given it means, that overall statistics was calculated
            $aZoneGlobalStatistics   = $aZoneCampaignStatistics;
            $aZoneCampaignStatistics = array();
        }

        foreach ($aZonesWebsite as $zoneId => $websiteId) {
            if (isset($aZoneGlobalStatistics[$zoneId])) {
                $aWebsitesAndZones[$websiteId]['zones'][$zoneId]['campaign_stats'] = false;
                $aWebsitesAndZones[$websiteId]['zones'][$zoneId]['ecpm'] = $aZoneGlobalStatistics[$zoneId]['eCPM'];
                $aWebsitesAndZones[$websiteId]['zones'][$zoneId]['cr']   = $aZoneGlobalStatistics[$zoneId]['CR'];
                $aWebsitesAndZones[$websiteId]['zones'][$zoneId]['ctr']  = $aZoneGlobalStatistics[$zoneId]['CTR'];
            } else {
                $aWebsitesAndZones[$websiteId]['zones'][$zoneId]['campaign_stats'] = true;
                $aWebsitesAndZones[$websiteId]['zones'][$zoneId]['ecpm'] = $aZoneCampaignStatistics[$zoneId]['eCPM'];
                $aWebsitesAndZones[$websiteId]['zones'][$zoneId]['cr']   = $aZoneCampaignStatistics[$zoneId]['CR'];
                $aWebsitesAndZones[$websiteId]['zones'][$zoneId]['ctr']  = $aZoneCampaignStatistics[$zoneId]['CTR'];
            }
        }
     }

    /**
     * Method gets sub categories IDs and add ID of parent category
     *
     * @return array Array of categories IDs
     */
    function _getParentAndSubCategoriesIds($categoryId)
    {
        if (is_null($this->_oOaCentralAdNetworks)) {
            $this->_oOaCentralAdNetworks = new OA_Central_AdNetworks();
        }
        $aCategories = $this->_oOaCentralAdNetworks->getSubCategoriesIds($categoryId);
        if ($aCategories == false) {
            $aCategories = array();
        }
        $aCategories[] = $categoryId;
        return $aCategories;
    }

    /**
     * Batch linking list of zones to campaign
     *
     * @param array $aZonesIds array of zones IDs
     * @param int $campaignId  the campaign ID.
     * @return int number of linked zones , -1 if invalid parameters was detected, PEAR:Errors on DB errors
     */
    function linkZonesToCampaign($aZonesIds, $campaignId)
    {
        // Check realm of given zones and campaign
        $checkResult = $this->_checkZonesRealm($aZonesIds, $campaignId);
        if ($checkResult == false) {
            return -1;
        } elseif (PEAR::isError($checkResult)) {
            MAX::raiseError($checkResult,MAX_ERROR_DBFAILURE);
            return -1;
        }

        // Call sql queries to link zones and banners to campaign
        $linkedZones = $this->_linkZonesToCampaign($aZonesIds, $campaignId);
        if (PEAR::isError($linkedZones)) {
                return $linkedZones;
        }
        $linkedBanners = $this->_linkZonesToCampaignsBannersOrSingleBanner($aZonesIds, $campaignId);
        if (PEAR::isError($linkedBanners)) {
                return $linkedBanners;
        }

        return $linkedZones;
    }

    /**
     * Batch linking list of zones to banner
     *
     * @param array $aZonesIds array of zones IDs
     * @param int $bannerId  the banner ID.
     * @return int number of linked zones , -1 if invalid parameters was detected, PEAR:Errors on DB errors
     */
    function linkZonesToBanner($aZonesIds, $bannerId)
    {
        // Check realm of given zones and campaign
        $checkResult = $this->_checkZonesRealm($aZonesIds, null, $bannerId);
        if ($checkResult == false) {
            return -1;
        } elseif (PEAR::isError($checkResult)) {
            MAX::raiseError($checkResult,MAX_ERROR_DBFAILURE);
            return -1;
        }

        // Call sql queries to link zones to banners
        $linkedZones = $this->_linkZonesToCampaignsBannersOrSingleBanner($aZonesIds, null, $bannerId);
        if (PEAR::isError($linkedZones)) {
                return $linkedZones;
        }

        return $linkedZones;
    }

    /**
     * Batch linking list of zones to campaign
     * This is a sub-function of linkZonesToCampaigns.
     *
     * Function don't link zones to campaign if:
     *  -link already exists
     *  -zone has type = Email
     *
     * @param array $aZonesIds array of zones IDs
     * @param int $campaignId  the campaign ID.
     * @return int number of linked zones
     */
    function _linkZonesToCampaign($aZonesIds, $campaignId) {
        $prefix = $this->getTablePrefix();
        $fromWhereClause =
            " FROM
                {$prefix}campaigns AS c
                CROSS JOIN
                {$prefix}zones AS z
                LEFT JOIN {$prefix}placement_zone_assoc AS pza ON (pza.zone_id = z.zoneid AND pza.placement_id = c.campaignid)
            WHERE
                c.campaignid = " . DBC::makeLiteral($campaignId) . "
                AND
                z.zoneid IN (" . implode(",",$aZonesIds) . ")
                AND
                z.delivery <> " . MAX_ZoneEmail ."
                AND
                pza.placement_zone_assoc_id IS NULL";

        $fastLinking = !$GLOBALS['_MAX']['CONF']['audit']['enabledForZoneLinking'];
        if ($fastLinking) {
            $query = "INSERT INTO {$prefix}placement_zone_assoc (placement_id, zone_id)
                      SELECT c.campaignid, z.zoneid
                      $fromWhereClause";
            return $this->oDbh->exec($query);
        }
        else
        {
            $query = "
                SELECT c.campaignid AS campaignid,
                       z.zoneid AS zoneid
               $fromWhereClause
            ";
            $rsCampZones = DBC::NewRecordSet($query);
            if (PEAR::isError($rsCampZones)) {
                return $rsCampZones;
            }
            $aCampZones = $rsCampZones->getAll();
            $doPlacementZoneAssoc = OA_Dal::factoryDO('placement_zone_assoc');
            foreach($aCampZones as $aCampZone) {
                $doPlacementZoneAssoc->zone_id      = $aCampZone['zoneid'];
                $doPlacementZoneAssoc->placement_id = $aCampZone['campaignid'];
                $doPlacementZoneAssoc->insert();
            }
            return count($aCampZones);
        }
    }

    /**
     * Batch linking list of zones to campaign's banners or a specific banner
     * This is a sub-function of linkZonesToCampaigns and linkZonesToBanner.
     *
     * Banners are linked when:
     *  - text text banner and text zone (ignore width/height)
     *  - link non text banners when matching width/height to non text zone
     * Don't link banners to zone if that link already exists
     * Don't link Email zones
     *
     * @param array $aZonesIds array of zones IDs
     * @param int $campaignId  the campaign ID.
     * @param int $bannerId    the banner ID.
     * @return int number of linked banners
     */
    function _linkZonesToCampaignsBannersOrSingleBanner($aZonesIds, $campaignId, $bannerId = null) {
        $prefix = $this->getTablePrefix();
        
        $rsEmailZones = DBC::NewRecordSet("SELECT zoneid FROM {$prefix}zones WHERE delivery = " . MAX_ZoneEmail . " AND zoneid IN (" . implode(',', $aZonesIds) . ")");
        $aEmailZoneIds = $rsEmailZones->getAll();

        $fastLinking = !$GLOBALS['_MAX']['CONF']['audit']['enabledForZoneLinking'];
        $fromWhereClause =
            " FROM
                {$prefix}banners AS b
                CROSS JOIN
                {$prefix}zones AS z
                LEFT JOIN {$prefix}ad_zone_assoc AS aza ON (aza.ad_id = b.bannerid AND aza.zone_id = z.zoneid)
            WHERE";
        if (!empty($campaignId)) {
            $fromWhereClause .= "
                b.campaignid = " . DBC::makeLiteral($campaignId) . "
                AND";
            
            foreach ($aEmailZoneIds as $zoneId) {
                $okToLink = Admin_DA::_checkEmailZoneAdAssoc($zoneId, $campaignId);
                if (PEAR::isError($okToLink)) {
                    $aZonesIds = array_diff($aZonesIds, array($zoneId));
                } 
            }
        }
        if (!empty($bannerId)) {
            $fromWhereClause .= "
                b.bannerid = " . DBC::makeLiteral($bannerId) . "
                AND";
            
            // Remove any zoneids which this banner cannot be linked to due to email zone restrictions
            foreach ($aEmailZoneIds as $zoneId) {
                $aAd = Admin_DA::getAd($bannerId);
                $okToLink = Admin_DA::_checkEmailZoneAdAssoc($zoneId, $aAd['placement_id']);
                if (PEAR::isError($okToLink)) {
                    $aZonesIds = array_diff($aZonesIds, array($zoneId));
                } 
            }
        }
        
        $fromWhereClause .= "
                z.zoneid IN (" . implode(",",$aZonesIds) . ")
                AND
                (
                    (
                        b.storagetype = 'txt'
                        AND
                        z.delivery = " . phpAds_ZoneText . "
                    )
                    OR
                    (
                        z.delivery <> " . phpAds_ZoneText . "
                        AND
                        b.storagetype <> 'txt'
                        AND
                        (
                          (
                            ( z.width = -1
                              OR
                              z.width = b.width
                            )
                            AND
                            ( z.height = -1
                              OR
                              z.height = b. height
                            )
                          )
                          OR
                          (
                            b.height = -1 AND b.width = -1
                          )
                        )
                    )
                )
                AND
                aza.ad_zone_assoc_id IS NULL
        ";
        
        // if only one zone is selected and this zone is an email zone
        // we only link it if it was not previously linked to any banner (email zones can be linked to one banner only)        

        if ($fastLinking) {
            $query = "INSERT INTO {$prefix}ad_zone_assoc (zone_id, ad_id, priority_factor)
                SELECT z.zoneid, b.bannerid, 1
                $fromWhereClause
            ";
            return $this->oDbh->exec($query);
        }
        else {
            $query = "
                SELECT z.zoneid AS zoneid,
                       b.bannerid AS bannerid
                $fromWhereClause
            ";
            $rsAdZones = DBC::NewRecordSet($query);
            if (PEAR::isError($rsAdZones)) {
                return $rsAdZones;
            }
            $aAdZones = $rsAdZones->getAll();
            $doAdZoneAssoc = OA_Dal::factoryDO('ad_zone_assoc');
            foreach($aAdZones as $aAdZone) {
                $doAdZoneAssoc->zone_id = $aAdZone['zoneid'];
                $doAdZoneAssoc->ad_id   = $aAdZone['bannerid'];
                $doAdZoneAssoc->priority_factor = 1;
                $doAdZoneAssoc->insert();
            }
            return count($aAdZones);
        }
    }

    /**
     * Check if given zones are under the same agency as given campaign or banner
     *
     * @param array $aZonesIds array of zones IDs
     * @param int $campaignId  the campaign ID.
     * @param int $bannerId    the banner ID.
     * @return boolean true if all zones are in the same realm as banner, false otherwise
     */
    function _checkZonesRealm($aZonesIds, $campaignId = null, $bannerId = null)
    {
        if (!is_array($aZonesIds) || count($aZonesIds) == 0) {
            return false;
        }
        if (empty($campaignId) && empty($bannerId)) {
            return false;
        }

        $doZones      = OA_Dal::factoryDO('zones');
        $doAffiliates = OA_Dal::factoryDO('affiliates');
        $doAgency     = OA_Dal::factoryDO('agency');
        $doCampaigns  = OA_Dal::factoryDO('campaigns');
        $doClients    = OA_Dal::factoryDO('clients');

        if (!empty($bannerId)) {
            $doBanners    = OA_Dal::factoryDO('banners');
            $doBanners->bannerid = (int)$bannerId;
            $doCampaigns->joinAdd($doBanners);
        }
        if (!empty($campaignId)) {
            $doCampaigns->campaignid = (int)$campaignId;
        }
        $doClients->joinAdd($doCampaigns);
        $doAgency->joinAdd($doClients);
        $doAffiliates->joinAdd($doAgency);
        $doZones->joinAdd($doAffiliates);
        $doZones->whereAdd("zoneid IN (" . implode(',', $aZonesIds) . ")");
        $doZones->selectAdd();
        $doZones->selectAdd('count( zoneid ) as zones');
        $doZones->groupBy($doAgency->tableName() . '.agencyid');

        $doZones->find();
        if ($doZones->fetch()=== false) {
            return false;
        }
        $aZonesCount = $doZones->toArray();
        if ($aZonesCount['zones'] != count($aZonesIds)) {
            return false;
        }
        return true;
    }

    /**
     * Batch unlinking zones from campaign
     *
     * @param array $aZonesIds array of zones IDs
     * @param int $campaignId  the campaign ID.
     * @return int number of unlinked zones, -1 on parameters error, PEAR:Errors on DB errors
     */
    function unlinkZonesFromCampaign($aZonesIds, $campaignId)
    {
        if (!is_array($aZonesIds)) {
            return -1;
        } else if(count($aZonesIds) == 0){
            return 0;
        }
        $prefix = $this->getTablePrefix();

        $doBanner = OA_Dal::factoryDO('banners');
        $doBanner->campaignid=$campaignId;
        $doBanner->find();
        $aBannersIds = array();
        while ($doBanner->fetch()) {
            $aBannersIds[] = $doBanner->bannerid;
        }

        $fastLinking = !$GLOBALS['_MAX']['CONF']['audit']['enabledForZoneLinking'];
        if ($fastLinking) {
            if (count($aBannersIds)!=0) {
                // Delete ad_zone_assoc
               $query = "
                   DELETE
                   FROM {$prefix}ad_zone_assoc
                   WHERE
                       ad_id IN (" . implode(',', $aBannersIds) . ")
                       AND
                       zone_id IN (" . implode(",",$aZonesIds) . ")
               ";

               $unlinkedBanners = $this->oDbh->exec($query);
               if (PEAR::isError($unlinkedBanners)) {
                  return $unlinkedBanners;
               }
            }

           // Delete placement_zone_assoc
           $query = "
               DELETE
               FROM {$prefix}placement_zone_assoc
               WHERE
                   placement_id = " . DBC::makeLiteral($campaignId) . "
                   AND
                   zone_id IN (" . implode(",",$aZonesIds) . ")
           ";
           return $this->oDbh->exec($query);
        }
        else { //slow - uses audit trail
            if (count($aBannersIds)!=0) {
                // Do a iteration to add all deleted ad_zone_assoc to audit log
                // it doesn't log all deleted rows when using
                // $doAdZoneAssoc->addWhere(
                //      ad_id IN (" . implode(',', $aBannersIds) . ")
                //      AND
                //      zone_id IN (" . implode(",",$aZonesIds) . ")
                //
                $doAdZoneAssocEmpty = OA_Dal::factoryDO('ad_zone_assoc');
                foreach ($aBannersIds as $bannerId) {
                    foreach ($aZonesIds as $zonesId) {
                        $doAdZoneAssoc = clone($doAdZoneAssocEmpty);  // Every delete have to be done on separate object
                        $doAdZoneAssoc->zone_id = $zonesId;
                        $doAdZoneAssoc->ad_id   = $bannerId;
                        $doAdZoneAssoc->delete();
                    }
                }
            }
            $doPlacementZoneAssocEmpty = OA_Dal::factoryDO('placement_zone_assoc');
            foreach ($aZonesIds as $zonesId) {
                $doPlacementZoneAssoc = clone($doPlacementZoneAssocEmpty);  // Every delete have to be done on separate object
                $doPlacementZoneAssoc->zone_id      = $zonesId;
                $doPlacementZoneAssoc->placement_id = $campaignId;
                $doPlacementZoneAssoc->delete();
            }

            return count($aZonesIds);
        }

    }

    /**
     * Batch unlinking zones from banner
     *
     * @param array $aZonesIds array of zones IDs
     * @param int $bannerId  the banner ID.
     * @return int number of unlinked zones, -1 on parameters error, PEAR:Errors on DB errors
     */
    function unlinkZonesFromBanner($aZonesIds, $bannerId)
    {
        if (!is_array($aZonesIds)) {
            return -1;
        } else if(count($aZonesIds) == 0){
            return 0;
        }
        $prefix = $this->getTablePrefix();

        $fastLinking = !$GLOBALS['_MAX']['CONF']['audit']['enabledForZoneLinking'];
        if ($fastLinking) {
            // Delete ad_zone_assoc
           $query = "
               DELETE
               FROM {$prefix}ad_zone_assoc
               WHERE
                   ad_id = " . DBC::makeLiteral($bannerId) . "
                   AND
                   zone_id IN (" . implode(",",$aZonesIds) . ")
           ";

           return $this->oDbh->exec($query);
        }
        else { //slow - uses audit trail
            // Do a iteration to add all deleted ad_zone_assoc to audit log
            // it doesn't log all deleted rows when using
            // $doAdZoneAssoc->addWhere(
            //      ad_id IN (" . implode(',', $aBannersIds) . ")
            //      AND
            //      zone_id IN (" . implode(",",$aZonesIds) . ")
            //
            $doAdZoneAssocEmpty = OA_Dal::factoryDO('ad_zone_assoc');
            foreach ($aZonesIds as $zonesId) {
                $doAdZoneAssoc = clone($doAdZoneAssocEmpty);  // Every delete have to be done on separate object
                $doAdZoneAssoc->zone_id = $zonesId;
                $doAdZoneAssoc->ad_id   = $bannerId;
                $doAdZoneAssoc->delete();
            }

            return count($aZonesIds);
        }

    }

    /**
     * Method returns unique categories IDs used in zones
     *
     * @param array $aZonesIds array of zones ID
     * @param boolean $includeWebsitesCategories true if method should return website categories for selected zones
     * @return array The categories Ids array or false on error or PEAR::Error on DB Error
     */
    function getCategoriesIdsFromZones($aZonesIds = null, $includeWebsitesCategories = true)
    {
        if (!is_array($aZonesIds)) {
            return false;
        }
        if (count($aZonesIds) == 0) {
            return array();
        }

        // Query database for oac_categories
        $prefix = $this->getTablePrefix();
        $query = "
            SELECT
                DISTINCT z.oac_category_id AS category
            FROM
                {$prefix}zones AS z
            WHERE
                z.zoneid IN (" . implode(",", $aZonesIds) . ")
                AND
                z.oac_category_id IS NOT NULL
            ";
        if ($includeWebsitesCategories == true) {
            $query .= "
              UNION
                SELECT
                    DISTINCT af.oac_category_id
                FROM
                    {$prefix}zones AS z,
                    {$prefix}affiliates AS af
                WHERE
                    z.zoneid IN (" . implode(",", $aZonesIds) . ")
                    AND
                    af.affiliateid = z.affiliateid
                    AND
                    af.oac_category_id IS NOT NULL
                ";
        }

        $rsZonesCategories = DBC::NewRecordSet($query);
        if (PEAR::isError($rsZonesCategories)) {
            return $rsZonesCategories;
        }
        return $rsZonesCategories->getAll();
    }

    /**
     * Method returns unique categories IDs used in zones
     *
     * @param array $aWebsiteAndZones array returned by getWebsitesAndZonesListByCategory
     * @param boolean $includeWebsitesCategories true if method should return website categories for selected zones
     * @return array The categories Ids array or false on error or PEAR::Error on DB Error
     */
    function getCategoriesIdsFromWebsitesAndZones($aWebsiteAndZones = null, $includeWebsitesCategories = true) {
        $aCategoriesIds = array();
        if(is_array($aWebsiteAndZones)){
            foreach ($aWebsiteAndZones as $aWebsite) {
                if ($includeWebsitesCategories === true && !is_null($aWebsite['oac_category_id'])) {
                    $aCategoriesIds[$aWebsite['oac_category_id']] = $aWebsite['oac_category_id'];
                }
                if(array_key_exists('zones', $aWebsite)) {
                    foreach ($aWebsite['zones'] as $aZone) {
                        if (!is_null($aZone['oac_category_id'])) {
                            $aCategoriesIds[$aZone['oac_category_id']] = $aZone['oac_category_id'];
                        }
                    }
                }
            }
        }
        return $aCategoriesIds;
    }

    /**
     * Method checked if zone linked to active campaign
     *
     * @param int $zoneId
     * @return boolean  true if zone is connect to active campaign, false otherwise
     */
    function checkZoneLinkedToActiveCampaign($zoneId)
    {
        $doAdZone   = OA_Dal::factoryDO('ad_zone_assoc');
        $doBanner   = OA_Dal::factoryDO('banners');
        $doCampaign = OA_Dal::factoryDO('campaigns');
        $doCampaign->whereAdd($doCampaign->tableName() . ".status <> ".OA_ENTITY_STATUS_EXPIRED);
        $doCampaign->whereAdd($doCampaign->tableName() . ".status <> ".OA_ENTITY_STATUS_REJECTED);
        $doAdZone->zone_id = $zoneId;
        $doBanner->joinAdd($doCampaign);
        $doAdZone->joinAdd($doBanner);

        $result = $doAdZone->count();

        $doPlacementZone = OA_Dal::factoryDO('placement_zone_assoc');
        $doCampaign      = OA_Dal::factoryDO('campaigns');
        $doCampaign->whereAdd("status <> ".OA_ENTITY_STATUS_EXPIRED);
        $doCampaign->whereAdd("status <> ".OA_ENTITY_STATUS_REJECTED);
        $doPlacementZone->zone_id = $zoneId;
        $doPlacementZone->joinAdd($doCampaign);

        $result += $doPlacementZone->count();

        if ($result > 0) {
            return true;
        }
        return false;
    }
}

?>
