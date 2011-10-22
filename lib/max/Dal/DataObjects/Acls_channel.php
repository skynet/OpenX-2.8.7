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
$Id: Acls_channel.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

/**
 * Table Definition for acls_channel
 */
require_once 'DB_DataObjectCommon.php';

class DataObjects_Acls_channel extends DB_DataObjectCommon
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'acls_channel';                    // table name
    public $channelid;                       // MEDIUMINT(9) => openads_mediumint => 129 
    public $logical;                         // VARCHAR(3) => openads_varchar => 130 
    public $type;                            // VARCHAR(255) => openads_varchar => 130 
    public $comparison;                      // CHAR(2) => openads_char => 130 
    public $data;                            // TEXT() => openads_text => 162 
    public $executionorder;                  // INT(10) => openads_int => 129 

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Acls_channel',$k,$v); }

    var $defaultValues = array(
                'channelid' => 0,
                'logical' => 'and',
                'type' => '',
                'comparison' => '==',
                'data' => '',
                'executionorder' => 0,
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
     * Duplicate a channels acls
     *
     * @param int $origChannelId    channel id of acls to copy
     * @param int $newChannelId     channel id to assign copy of original
     *                              channel acls
     * @return boolean              true on success or if no acls exist else
     *                              false  is returned
     */
    function duplicate($origChannelId, $newChannelId)
    {
        $this->channelid = $origChannelId;
        if ($this->find()) {
            while ($this->fetch()) {
                //  copy the current acl, change the channel id, and insert
                $oNewChannelAcl = clone($this);
                $oNewChannelAcl->channelid = $newChannelId;
                $result = $oNewChannelAcl->insert();

                if (PEAR::isError($result)) {
                    return false;
                }
            }
        }
        return true;
    }
}

?>