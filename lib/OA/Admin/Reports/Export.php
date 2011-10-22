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
$Id: Export.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

// Include required files
require_once MAX_PATH . '/lib/OA/Admin/ExcelWriter.php';
require_once LIB_PATH . '/Extension/reports/ReportsScope.php';
require_once MAX_PATH . '/lib/OA/Admin/Menu.php';

/**
 * A class for generating reports via exporting statistics screens data.
 *
 * @package    OpenXAdmin
 * @subpackage Reports
 * @author     Andrew Hill <andrew.hill@openx.org>
 */
class OA_Admin_Reports_Export extends Plugins_ReportsScope
{

    /**
     * The stats controller with stats ready to export.
     *
     * @var OA_Admin_Statistics_Common
     */
    var $oStatsController;

    /**
     * The constructor method. Stores the stats controller with the
     * already prepared stats for display, and sets up the XLS writer.
     *
     * @param OA_Admin_Statistics_Common $oStatsController
     * @return OA_Admin_Reports_Export
     */
    function OA_Admin_Reports_Export($oStatsController)
    {
        $this->oStatsController = $oStatsController;
        // Set the Excel Report writer
        $oWriter = new OA_Admin_ExcelWriter();
        $this->useReportWriter($oWriter);
    }

    /**
     * The method to generate a plugin-style report XLS from an already
     * prepared statistics page OA_Admin_Statistics_Common object.
     */
    function export()
    {
        // Prepare the report name        
        // Get system navigation
        $oMenu = OA_Admin_Menu::singleton();
        // Get section by pageId
        $oCurrentSection = $oMenu->get($this->oStatsController->pageId);
        if ($oCurrentSection == null) {
            phpAds_Die($GLOBALS['strErrorOccurred'], 'Menu system error: <strong>' . OA_Permission::getAccountType(true) . '::' . htmlspecialchars($ID) . '</strong> not found for the current user');
        }
        // Get name
        $reportName = $oCurrentSection->getName();

        $this->_name = $reportName;
        // Prepare the output writer for generation
        $reportFileName = 'Exported Statistics - ' . $reportName;
        if (!empty($this->oStatsController->aDates['day_begin'])) {
            $oStartDate = new Date($this->oStatsController->aDates['day_begin']);
            $reportFileName .= ' from ' . $oStartDate->format($GLOBALS['date_format']);
        }
        if (!empty($this->oStatsController->aDates['day_end'])) {
            $oEndDate = new Date($this->oStatsController->aDates['day_end']);
            $reportFileName .= ' to ' . $oEndDate->format($GLOBALS['date_format']);
        }
        $reportFileName .= '.xls';
        $this->_oReportWriter->openWithFilename($reportFileName);
        // Get the header and data arrays from the same statistics controllers
        // that prepare stats for the user interface stats pages
        list($aHeaders, $aData) = $this->getHeadersAndDataFromStatsController(null, $this->oStatsController);
        // Add the worksheet
        $name = ucfirst($this->oStatsController->entity) . ' ' . ucfirst($this->oStatsController->breakdown);
        $this->createSubReport($reportName, $aHeaders, $aData);
        // Close the report writer and send the report to the user
        $this->_oReportWriter->closeAndSend();
    }

    /**
     * The local implementation of the _getReportParametersForDisplay() method
     * to return a string to display the date range of the report.
     *
     * @access private
     * @return array The array of index/value sub-headings.
     */
    function _getReportParametersForDisplay()
    {
        global $strClient, $strCampaign, $strBanner, $strAffiliate, $strZone;
        $aParams = array();
        // Deal with the possible entity types
        foreach ($this->oStatsController->aPageParams as $key => $value) {
            unset($string);
            unset($name);
            if ($key == 'client' || $key == 'clientid') {
                $string = $strClient;
                $doClients = OA_Dal::factoryDO('clients');
                $doClients->clientid = $value;
                $doClients->find();
                if ($doClients->fetch()) {
                    $aAdvertiser = $doClients->toArray();
                    $name = $aAdvertiser['clientname'];
                }
            } else if ($key == 'campaignid') {
                $string = $strCampaign;
                $doCampaigns = OA_Dal::factoryDO('campaigns');
                $doCampaigns->campaignid = $value;
                $doCampaigns->find();
                if ($doCampaigns->fetch()) {
                    $aCampaign = $doCampaigns->toArray();
                    $name = $aCampaign['campaignname'];
                }
            } else if ($key == 'bannerid') {
                $string = $strBanner;
                $doBanners = OA_Dal::factoryDO('banners');
                $doBanners->bannerid = $value;
                $doBanners->find();
                if ($doBanners->fetch()) {
                    $aBanner = $doBanners->toArray();
                    $name = $aBanner['description'];
                }
            } else if ($key == 'affiliateid') {
                $string = $strAffiliate;
                $doAffiliates = OA_Dal::factoryDO('affiliates');
                $doAffiliates->affiliateid = $value;
                $doAffiliates->find();
                if ($doAffiliates->fetch()) {
                    $aPublisher = $doAffiliates->toArray();
                    $name = $aPublisher['name'];
                }
            } else if ($key == 'zoneid') {
                $string = $strZone;
                $doZones = OA_Dal::factoryDO('zones');
                $doZones->zoneid = $value;
                $doZones->find();
                if ($doZones->fetch()) {
                    $aZone = $doZones->toArray();
                    $name = $aZone['zonename'];
                }
            }
            if (!is_null($string) && !is_null($name)) {
                $aParams[$string] = '[id' . $value . '] ' . $name;
            }
        }
        // Add the start and end dates
        if (!empty($this->oStatsController->aDates['day_begin'])) {
            $aParams['Start Date'] = $this->oStatsController->aDates['day_begin'];
        }
        if (!empty($this->oStatsController->aDates['day_end'])) {
            $aParams['End Date'] = $this->oStatsController->aDates['day_end'];
        }
        return $aParams;
    }

}

?>