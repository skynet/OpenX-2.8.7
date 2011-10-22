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
$Id: AdvertiserServiceImpl.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

/**
 * @package    OpenX
 * @author     Ivan Klishch <iklishch@lohika.com>
 *
 */

// Require the base class, BaseLogonService
require_once MAX_PATH . '/www/api/v1/common/BaseServiceImpl.php';

// Require the advertiser Dll class.
require_once MAX_PATH . '/lib/OA/Dll/Advertiser.php';

/**
 * The AdvertiserServiceImpl class extends the BaseServiceImpl class to enable
 * you to add, modify, delete and search the advertiser object.
 *
 */
class AdvertiserServiceImpl extends BaseServiceImpl
{
    /**
     *
     * @var OA_Dll_Advertiser $_dllAdvertiser
     */
    var $_dllAdvertiser;

    /**
     *
     * The AdvertiserServiceImpl method is the constructor for the
     * AdvertiserServiceImpl class.
     */
    function AdvertiserServiceImpl()
    {
        $this->BaseServiceImpl();
        $this->_dllAdvertiser = new OA_Dll_Advertiser();
    }

    /**
     * This method checks if an action is valid and either returns a result
     * or an error, as appropriate.
     *
     * @access private
     *
     * @param boolean $result
     *
     * @return boolean
     */
    function _validateResult($result)
    {
        if ($result) {
            return true;
        } else {
            $this->raiseError($this->_dllAdvertiser->getLastError());
            return false;
        }
    }

    /**
     * The addAdvertiser method creates an advertiser and updates the
     * advertiser object with the advertiser ID.
     *
     * @access public
     *
     * @param string $sessionId
     * @param OA_Dll_AdvertiserInfo &$oAdvertiser <br />
     *          <b>Required properties:</b> advertiserName<br />
     *          <b>Optional properties:</b> agencyId, contactName, emailAddress, username, password<br />
     *
     * @return boolean
     */
    function addAdvertiser($sessionId, &$oAdvertiser)
    {
        if ($this->verifySession($sessionId)) {

            return $this->_validateResult($this->_dllAdvertiser->modify($oAdvertiser));

        } else {

            return false;
        }

    }
    /**
     * The modifyAdvertiser method checks if an advertiser ID exists and
     * modifies the details for the advertiser if it exists or returns an error
     * message, as appropriate.
     *
     * @access public
     *
     * @param string $sessionId
     * @param OA_Dll_AdvertiserInfo &$oAdvertiser <br />
     *          <b>Required properties:</b> advertiserId<br />
     *          <b>Optional properties:</b> agencyId, advertiserName, contactName, emailAddress, username, password<br />
     *
     * @return boolean
     */
    function modifyAdvertiser($sessionId, &$oAdvertiser)
    {
        if ($this->verifySession($sessionId)) {

            if (isset($oAdvertiser->advertiserId)) {

                return $this->_validateResult($this->_dllAdvertiser->modify($oAdvertiser));

            } else {

                $this->raiseError("Field 'advertiserId' in structure does not exists");
                return false;
            }

        } else {

            return false;
        }

    }

    /**
     * The deleteAdvertiser method checks if an advertiser exists and deletes
     * the advertiser or returns an error message, as appropriate.
     *
     * @access public
     *
     * @param string $sessionId
     * @param integer $advertiserId
     *
     * @return boolean
     */
    function deleteAdvertiser($sessionId, $advertiserId)
    {
        if ($this->verifySession($sessionId)) {

            return $this->_validateResult($this->_dllAdvertiser->delete($advertiserId));

        } else {

            return false;
        }
    }

    /**
     * The getAdvertiserDailyStatistics method returns daily statistics for an
     * advertiser for a specified period.
     *
     * @access public
     *
     * @param string $sessionId
     * @param integer $advertiserId
     * @param date $oStartDate
     * @param date $oEndDate
     * @param recordSet &$rsStatisticsData  return data
     *
     * @return boolean
     */
    function getAdvertiserDailyStatistics($sessionId, $advertiserId, $oStartDate, $oEndDate, &$rsStatisticsData)
    {
        if ($this->verifySession($sessionId)) {

            return $this->_validateResult(
                $this->_dllAdvertiser->getAdvertiserDailyStatistics(
                    $advertiserId, $oStartDate, $oEndDate, false, $rsStatisticsData));
        } else {

            return false;
        }
    }

    /**
     * The getAdvertiserCampaignStatistics method returns campaign statistics
     * for an advertiser for a specified period.
     *
     * @access public
     *
     * @param string $sessionId
     * @param integer $advertiserId
     * @param date $oStartDate
     * @param date $oEndDate
     * @param recordSet &$rsStatisticsData  return data
     *
     * @return boolean
     */
    function getAdvertiserCampaignStatistics($sessionId, $advertiserId, $oStartDate, $oEndDate, &$rsStatisticsData)
    {
        if ($this->verifySession($sessionId)) {

            return $this->_validateResult(
                $this->_dllAdvertiser->getAdvertiserCampaignStatistics(
                    $advertiserId, $oStartDate, $oEndDate, false, $rsStatisticsData));
        } else {

            return false;
        }
    }

    /**
     * The getAdvertiserBannerStatistics method returns banner statistics for
     * an advertiser for a specified period.
     *
     * @access public
     *
     * @param string $sessionId
     * @param integer $advertiserId
     * @param date $oStartDate
     * @param date $oEndDate
     * @param recordSet &$rsStatisticsData  return data
     *
     * @return boolean
     */
    function getAdvertiserBannerStatistics($sessionId, $advertiserId, $oStartDate, $oEndDate, &$rsStatisticsData)
    {
        if ($this->verifySession($sessionId)) {

            return $this->_validateResult(
                $this->_dllAdvertiser->getAdvertiserBannerStatistics(
                    $advertiserId, $oStartDate, $oEndDate, false, $rsStatisticsData));
        } else {

            return false;
        }
    }

    /**
     * The getAdvertiserPublisherStatistics method returns publisher
     * statistics for an advertiser for a specified period.
     *
     * @access public
     *
     * @param string $sessionId
     * @param integer $advertiserId
     * @param date $oStartDate
     * @param date $oEndDate
     * @param recordSet &$rsStatisticsData  return data
     *
     * @return boolean
     */
    function getAdvertiserPublisherStatistics($sessionId, $advertiserId, $oStartDate, $oEndDate, &$rsStatisticsData)
    {
        if ($this->verifySession($sessionId)) {

            return $this->_validateResult(
                $this->_dllAdvertiser->getAdvertiserPublisherStatistics(
                    $advertiserId, $oStartDate, $oEndDate, false, $rsStatisticsData));
        } else {

            return false;
        }
    }

    /**
     * The getAdvertiserZoneStatistics method returns zone statistics for an
     * advertiser for a specified period.
     *
     * @access public
     *
     * @param string $sessionId
     * @param integer $advertiserId
     * @param date $oStartDate
     * @param date $oEndDate
     * @param recordSet &$rsStatisticsData  return data
     *
     * @return boolean
     */
    function getAdvertiserZoneStatistics($sessionId, $advertiserId, $oStartDate, $oEndDate, &$rsStatisticsData)
    {
        if ($this->verifySession($sessionId)) {

            return $this->_validateResult(
                $this->_dllAdvertiser->getAdvertiserZoneStatistics(
                    $advertiserId, $oStartDate, $oEndDate, false, $rsStatisticsData));
        } else {

            return false;
        }
    }

    /**
     * The getAdvertiser method returns the advertiser details for a specified advertiser.
     *
     * @access public
     *
     * @param string $sessionId
     * @param integer $advertiserId
     * @param OA_Dll_AdvertiserInfo &$oAdvertiser
     *
     * @return boolean
     */
    function getAdvertiser($sessionId, $advertiserId, &$oAdvertiser)
    {
        if ($this->verifySession($sessionId)) {

            return $this->_validateResult(
                $this->_dllAdvertiser->getAdvertiser($advertiserId, $oAdvertiser));
        } else {

            return false;
        }
    }

    /**
     * The getAdvertiserListByAgencyId method returns a list of advertisers
     * for a specified agency.
     *
     * @access public
     *
     * @param string $sessionId
     * @param integer $agencyId
     * @param array &$aAdvertiserList  Array of OA_Dll_AdvertiserInfo classes
     *
     * @return boolean
     */
    function getAdvertiserListByAgencyId($sessionId, $agencyId, &$aAdvertiserList)
    {
        if ($this->verifySession($sessionId)) {

            return $this->_validateResult(
                $this->_dllAdvertiser->getAdvertiserListByAgencyId($agencyId,
                                                    $aAdvertiserList));
        } else {

            return false;
        }
    }

}


?>