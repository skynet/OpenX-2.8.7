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
$Id: CommonHistory.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

require_once MAX_PATH . '/lib/OA/Admin/Statistics/Delivery/Common.php';

/**
 * A common class that defines a common "interface" and common methods for
 * classes that display history delivery statistics.
 *
 * @package    OpenXAdmin
 * @subpackage StatisticsDelivery
 * @author     Matteo Beccati <matteo@beccati.com>
 * @author     Andrew Hill <andrew.hill@openx.org>
 */
class OA_Admin_Statistics_Delivery_CommonHistory extends OA_Admin_Statistics_Delivery_Common
{

    /**
     * The starting day of the page's report span.
     *
     * @var PEAR::Date
     */
    var $oStartDate;

    /**
     * The number of days that the page's report spans.
     *
     * @var integer
     */
    var $spanDays;

    /**
     * The number of weeks that the page's report spans.
     *
     * @var integer
     */
    var $spanWeeks;

    /**
     * The number of months that the page's report spans.
     *
     * @var integer
     */
    var $spanMonths;

    /**
     * The type of statistics breakdown to display. One of:
     *  - hour
     *  - day
     *  - week
     *  - month
     *  - dow (ie. Day of Week)
     *
     * @var string
     */
    var $statsBreakdown;

    /**
     * Enter description here...
     *
     * @var unknown_type
     */
    var $statsKey;

    /**
     * Enter description here...
     *
     * @var unknown_type
     */
    var $averageDesc;

    /**
     * PHP5-style constructor
     */
    function __construct($aParams)
    {
        // Set the output type "history" style delivery statistcs
        $this->outputType = 'deliveryHistory';

        // Get list order and direction
        $this->listOrderField     = MAX_getStoredValue('listorder', 'key');
        $this->listOrderDirection = MAX_getStoredValue('orderdirection', 'down');

        // Ensure the history class is prepared
        $this->useHistoryClass = true;

        parent::__construct($aParams);

        // Store the preferences
        $this->aPagePrefs['listorder']      = $this->listOrderField;
        $this->aPagePrefs['orderdirection'] = $this->listOrderDirection;
    }

    /**
     * PHP4-style constructor
     */
    function OA_Admin_Statistics_Delivery_CommonHistory($params)
    {
        $this->__construct($params);
    }

    /**
     * The final "child" implementation of the parental abstract method,
     * to test if the appropriate data array is empty, or not.
     *
     * @see OA_Admin_Statistics_Common::_isEmptyResultArray()
     *
     * @access private
     * @return boolean True on empty, false if at least one row of data.
     */
    function _isEmptyResultArray()
    {
        if (!is_array($this->aStatsData)) {
            return true;
        }
        foreach($this->aStatsData as $aRecord) {
            if (
                $aRecord['sum_requests'] != '-' ||
                $aRecord['sum_views']    != '-' ||
                $aRecord['sum_clicks']   != '-'
            ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Fetch and decorates the history stats using the specified parameters
     *
     * @param array  $aParams Query parameters
     * @param string $link    Optional link for the leftmost column content
     */
    function prepare(&$aParams, $link = '')
    {
        parent::prepare(&$aParams);
        
        // Set the span requirements
        // Disable this for now, since these queries can be very slow
        // $this->oHistory->getSpan($this, $aParams);

        // Get the historical stats
        $aStats = $this->getHistory($aParams, $link);

        if ($this->noStatsAvailable) {
            $this->aStatsData = array();
            return;
        }

        if ($this->disablePager) {
            $use_pager = false;
        } elseif ($this->statsBreakdown == 'week') {
            $per_page  = 4;
            $use_pager = count($stats) > $per_page;
        } elseif ($this->aGlobalPrefs['period_preset'] == 'this_month' || $this->aGlobalPrefs['period_preset'] == 'last_month') {
            // Do not use pager when showing last or current month
            $use_pager = false;
        } elseif ($this->statsBreakdown == 'hour' || $this->statsBreakdown == 'dow') {
            $use_pager = false;
        } else {
            $per_page  = 15;
            $use_pager = count($stats) > $per_page;
        }

        if ($use_pager) {
            $params = array(
                'itemData' => $stats,
                'perPage' => htmlspecialchars(MAX_getStoredValue('setPerPage', $per_page)),
                'delta' => 8,
                'append' => true,
                'clearIfVoid' => false,
                'urlVar' => 'page',
                'useSessions' => false,
                'mode'  => 'Jumping'
            );

            if ($params['perPage'] % $per_page || $params['perPage'] > $per_page * 4) {
                // Reset the perPage and the request parameters when not matching the available values
                $params['perPage']      = $per_page;
                $_REQUEST['setPerPage'] = $per_page;
            }

            $pager =& Pager::factory($params);
            $this->aStatsData = $pager->getPageData();
            $this->pagerLinks = $pager->getLinks();
            $this->pagerLinks = $this->pagerLinks['all'];

            $this->pagerSelect = preg_replace('/(<select.*?)(>)/i', '$1 onchange="this.form.submit()"$2',
                $pager->getPerPageSelectBox($per_page, $per_page * 4, $per_page));
        } else {
            $this->aStatsData = $aStats;
            $this->pagerLinks = false;
            $this->pagerSelect = false;
        }

        $this->aPagePrefs['setPerPage'] = $params['perPage'];

        // Format the rows appropriately for output
        $this->oHistory->formatRows($this->aStatsData, $this);
    }

    function getColspan()
    {
        return count($this->aColumns) + 1;
    }

    /**
     * Fetch the history stats using the specified parameters
     *
     * @param array  $aParams Query parameters
     * @param string $link    Optional link for the leftmost column content
     */
    function getHistory($aParams, $link = '')
    {
        $oNow = new Date();
        $aParams['tz'] = $oNow->tz->getID();

        $method = $this->oHistory->setBreakdownInfo($this);

        // Add plugin aParams
        $pluginParams = array();
        foreach ($this->aPlugins as $oPlugin) {
            $oPlugin->addQueryParams($pluginParams);
        }

        $aStats = Admin_DA::fromCache($method, $aParams + $this->aDates + $pluginParams);

        // Merge plugin additional $oPlugin
        foreach ($this->aPlugins as $oPlugin) {
            $oPlugin->mergeData($aStats, $this->aEmptyRow, $method, $aParams + $this->aDates);
        }

        if (count($aStats) == 0) {
            $this->noStatsAvailable = true;
            return $aStats;
        }

        // Fill unused plugins columns
        foreach (array_keys($aStats) as $k) {
            $aStats[$k] += $this->aEmptyRow;
        }

        // Set some of the variables that used to be set by getSpan
        if (!empty($aStats)) {
            $dates = array_keys ($aStats);

            // assumes first row has earliest date
            $firstDate = new Date ($dates[0]);

            // are these steps really needed?
            $firstDate->setTZbyID('UTC');
            $firstDate->convertTZ($oNow->tz);
            $firstDate->setHour(0);
            $firstDate->setMinute(0);
            $firstDate->setSecond(0);

            if (empty($oCaller->aDates)) {
                $oCaller->aDates['day_begin'] = $firstDate->format('%Y-%m-%d');
                $oCaller->aDates['day_end']   = $oNow->format('%Y-%m-%d');
            }

            $this->oStartDate = new Date($firstDate);
        }

        $aDates = $this->oHistory->getDatesArray($this->aDates, $this->statsBreakdown, $this->oStartDate);
        $this->oHistory->fillGapsAndLink($aStats, $aDates, $this, $link);

        if (!in_array($this->listOrderField, array_merge(array($this->statsBreakdown), array_keys($this->aColumns)))) {
            $this->listOrderField = $this->statsBreakdown;
            $this->listOrderDirection = $this->statsBreakdown == 'hour' || $this->statsBreakdown == 'dow' ? 'up' : 'down';
        }

        // If required, re-format the data in the weekly breakdown format
        if ($this->statsBreakdown == 'week') {
            $this->oHistory->prepareWeekBreakdown($aStats, $this);
        }

        MAX_sortArray($aStats, $this->listOrderField, $this->listOrderDirection == 'up');

        // Summarise the values into a the totals array, & format
        $this->_summariseTotalsAndFormat($aStats, true);

        return $aStats;
    }

    /**
     * Exports stats data to an array
     *
     * The array will look like:
     *
     * Array (
     *     'headers' => Array ( 0 => 'Col1', 1 => 'Col2', ... )
     *     'formats' => Array ( 0 => 'date', 1 => 'default', ... )
     *     'data'    => Array (
     *         0 => Array ( 0 => '2006-08-03', 1 => '5', ...),
     *         ...
     *     )
     * )
     *
     * @param array Stats array
     */
    function exportArray()
    {
        $parent = parent::exportArray();

        switch ($this->statsBreakdown) {
            case 'day':
                $key_format = 'date';
                break;
            case 'hour':
                $key_format = 'time';
                break;
            default:
                $key_format = 'text';
                break;
        }

        $headers = array_merge(array($this->statsKey), $parent['headers']);
        $formats = array_merge(array($key_format), $parent['formats']);
        $data    = array();

        $headers[] = $this->statsKey;

        foreach ($this->aStatsData as $h) {
            $row = array();
            if ($this->statsBreakdown == 'week') {
                $row[] = $h['week'];
            } else {
                $row[] = $h['date_f'];
            }
            foreach (array_keys($this->aColumns) as $ck) {
                if ($this->showColumn($ck)) {
                    $row[] = $h[$ck];
                }
            }

            $data[] = $row;
        }

        return array(
            'headers' => $headers,
            'formats' => $formats,
            'data'    => $data
        );
    }
}

?>
