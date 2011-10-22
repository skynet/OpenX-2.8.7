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
$Id: TrackerServiceImpl.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

/**
 * @package    OpenX
 * @author     David Keen <david.keen@openx.org>
 *
 */

require_once MAX_PATH . '/www/api/v2/common/BaseServiceImpl.php';
require_once MAX_PATH . '/lib/OA/Dll/Tracker.php';


class TrackerServiceImpl extends BaseServiceImpl
{
    private $dllTracker;


    function __construct()
    {
        parent::__construct();
        $this->dllTracker = new OA_Dll_Tracker();
    }

    /**
     * This method checks if an action is valid and either returns a result
     * or an error, as appropriate.
     *
     * @param boolean $result
     *
     * @return boolean
     */
    private function validateResult($result)
    {
        if ($result) {
            return true;
        } else {
            $this->raiseError($this->dllTracker->getLastError());
            return false;
        }
    }

    /**
     * Creates a tracker.
     *
     * @param string $sessionId
     * @param OA_Dll_TrackerInfo &$oTrackerInfo <br />
     *          <b>Required properties:</b> clientId, trackerName<br />
     *          <b>Optional properties:</b> description, status, type, linkCampaigns, variableMethod<br />
     *
     * @return boolean
     */
    public function addTracker($sessionId, &$oTrackerInfo)
    {
        if ($this->verifySession($sessionId)) {
            return $this->validateResult($this->dllTracker->modify($oTrackerInfo));
        } else {
            return false;
        }
    }

    /**
     * Modifies the details for the tracker
     *
     * @param string $sessionId
     * @param OA_Dll_TrackerInfo &$oTracker <br />
     *          <b>Required properties:</b> trackerId<br />
     *          <b>Optional properties:</b> trackerName, description, status, type, linkCampaigns, variableMethod<br />
     *
     * @return boolean
     */
    public function modifyTracker($sessionId, &$oTrackerInfo)
    {
        if ($this->verifySession($sessionId)) {
            if (isset($oTrackerInfo->trackerId)) {
                return $this->validateResult($this->dllTracker->modify($oTrackerInfo));
            } else {
                $this->raiseError("Field 'trackerId' in structure does not exist");
                return false;
            }
        } else {
            return false;
        }
    }


    /**
     *
     * @param string $sessionId
     * @param integer $trackerId
     *
     * @return boolean
     */
    public function deleteTracker($sessionId, $trackerId)
    {
        if ($this->verifySession($sessionId)) {
            return $this->validateResult($this->dllTracker->delete($trackerId));
        } else {
            return false;
        }
    }

    /**
     * Links a campaign to the given tracker ID
     *
     * @param int $trackerId the ID of the tracker to link the campaign to.
     * @param int $campaignId the ID of the campaign to link to the tracker.
     * @param int $status optional connection status type, eg MAX_CONNECTION_STATUS_APPROVED. See constants.php.
     *                    if no status given, uses the tracker's default status.
     * @return boolean true on successful link, false on error.
     */
    public function linkTrackerToCampaign($sessionId, $trackerId, $campaignId, $status = null)
    {
        if ($this->verifySession($sessionId)) {
            return $this->validateResult($this->dllTracker->linkTrackerToCampaign($trackerId, $campaignId, $status));
        } else {
            return false;
        }
    }

    public function getTracker($sessionId, $trackerId, &$oTrackerInfo)
    {
        if ($this->verifySession($sessionId)) {

            return $this->validateResult(
                $this->dllTracker->getTracker($trackerId, $oTrackerInfo));
        } else {

            return false;
        }
    }

}

?>