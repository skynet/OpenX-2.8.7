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
$Id: Data_summary_zone_impression_history.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

/**
 * Table Definition for data_summary_zone_impression_history
 */
require_once 'DB_DataObjectCommon.php';

class DataObjects_Data_summary_zone_impression_history extends DB_DataObjectCommon
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'data_summary_zone_impression_history';    // table name
    public $data_summary_zone_impression_history_id;    // BIGINT(20) => openads_bigint => 129 
    public $operation_interval;              // INT(10) => openads_int => 129 
    public $operation_interval_id;           // INT(10) => openads_int => 129 
    public $interval_start;                  // DATETIME() => openads_datetime => 142 
    public $interval_end;                    // DATETIME() => openads_datetime => 142 
    public $zone_id;                         // INT(10) => openads_int => 129 
    public $forecast_impressions;            // INT(10) => openads_int => 1 
    public $actual_impressions;              // INT(10) => openads_int => 1 
    public $est;                             // SMALLINT(6) => openads_smallint => 1 

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Data_summary_zone_impression_history',$k,$v); }

    var $defaultValues = array(
                'interval_start' => '%NO_DATE_TIME%',
                'interval_end' => '%NO_DATE_TIME%',
                );

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}

?>