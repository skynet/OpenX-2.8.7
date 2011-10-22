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
$Id: Data_intermediate_ad_variable_value.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

/**
 * Table Definition for data_intermediate_ad_variable_value
 */
require_once 'DB_DataObjectCommon.php';

class DataObjects_Data_intermediate_ad_variable_value extends DB_DataObjectCommon
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'data_intermediate_ad_variable_value';    // table name
    public $data_intermediate_ad_variable_value_id;    // BIGINT(20) => openads_bigint => 129 
    public $data_intermediate_ad_connection_id;    // BIGINT(20) => openads_bigint => 129 
    public $tracker_variable_id;             // INT(11) => openads_int => 129 
    public $value;                           // VARCHAR(50) => openads_varchar => 2 

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Data_intermediate_ad_variable_value',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}

?>