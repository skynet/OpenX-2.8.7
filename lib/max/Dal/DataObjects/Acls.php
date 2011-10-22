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
$Id: Acls.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

/**
 * Table Definition for acls
 */
require_once 'DB_DataObjectCommon.php';

class DataObjects_Acls extends DB_DataObjectCommon
{
    var $onDeleteCascade = true;
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'acls';                            // table name
    public $bannerid;                        // MEDIUMINT(9) => openads_mediumint => 129 
    public $logical;                         // VARCHAR(3) => openads_varchar => 130 
    public $type;                            // VARCHAR(255) => openads_varchar => 130 
    public $comparison;                      // CHAR(2) => openads_char => 130 
    public $data;                            // TEXT() => openads_text => 162 
    public $executionorder;                  // INT(10) => openads_int => 129 

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Acls',$k,$v); }

    var $defaultValues = array(
                'bannerid' => 0,
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

    function _auditEnabled()
    {
        return true;
    }

    function _getContextId()
    {
        return $this->bannerid;
    }

    function _getContext()
    {
        return 'Delivery Limitation';
    }

    /**
     * A method to return an array of account IDs of the account(s) that
     * should "own" any audit trail entries for this entity type; these
     * are NOT related to the account ID of the currently active account
     * (which is performing some kind of action on the entity), but is
     * instead related to the type of entity, and where in the account
     * heirrachy the entity is located.
     *
     * @return array An array containing up to three indexes:
     *                  - "OA_ACCOUNT_ADMIN" or "OA_ACCOUNT_MANAGER":
     *                      Contains the account ID of the manager account
     *                      that needs to be able to see the audit trail
     *                      entry, or, the admin account, if the entity
     *                      is a special case where only the admin account
     *                      should see the entry.
     *                  - "OA_ACCOUNT_ADVERTISER":
     *                      Contains the account ID of the advertiser account
     *                      that needs to be able to see the audit trail
     *                      entry, if such an account exists.
     *                  - "OA_ACCOUNT_TRAFFICKER":
     *                      Contains the account ID of the trafficker account
     *                      that needs to be able to see the audit trail
     *                      entry, if such an account exists.
     */
    function getOwningAccountIds()
    {
        // Delivery limitations don't have an account_id, get it from
        // the parent banner (stored in the "banners" table) using
        // the "bannerid" key
        return parent::getOwningAccountIds('banners', 'bannerid');
    }

    /**
     * build an acls specific audit array
     *
     * @param integer $actionid
     * @param array $aAuditFields
     */
    function _buildAuditArray($actionid, &$aAuditFields)
    {
        $aAuditFields['key_desc']     = $this->type;
        switch ($actionid)
        {
            case OA_AUDIT_ACTION_UPDATE:
                        $aAuditFields['bannerid'] = $this->bannerid;
                        break;
        }
    }

}

?>