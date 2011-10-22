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
$Id: Session.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

/**
 * Table Definition for session
 */
require_once 'DB_DataObjectCommon.php';

class DataObjects_Session extends DB_DataObjectCommon
{
    var $dalModelName = 'Session';
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'session';                         // table name
    public $sessionid;                       // VARCHAR(32) => openads_varchar => 130 
    public $sessiondata;                     // TEXT() => openads_text => 162 
    public $lastused;                        // DATETIME() => openads_datetime => 14 

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Session',$k,$v); }

    var $defaultValues = array(
                'sessionid' => '',
                'sessiondata' => '',
                );

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    /**
     * Table has no autoincrement/sequence so we override sequenceKey().
     *
     * @return array
     */
    function sequenceKey() {
        return array(false, false, false);
    }


    /**
     * Overrides _refreshUpdated() because the updated field is called 'lastused'.
     * This method is called on insert() and update().
     *
     */
    function _refreshUpdated()
    {
        $this->lastused = OA::getNowUTC();
    }
}

?>