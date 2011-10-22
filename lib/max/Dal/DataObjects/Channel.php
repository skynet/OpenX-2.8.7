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
$Id: Channel.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

/**
 * Table Definition for channel
 */

require_once MAX_PATH . '/lib/max/other/lib-acl.inc.php';
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once 'DB_DataObjectCommon.php';

class DataObjects_Channel extends DB_DataObjectCommon
{
    var $onDeleteCascade = true;
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'channel';                         // table name
    public $channelid;                       // MEDIUMINT(9) => openads_mediumint => 129 
    public $agencyid;                        // MEDIUMINT(9) => openads_mediumint => 129 
    public $affiliateid;                     // MEDIUMINT(9) => openads_mediumint => 129 
    public $name;                            // VARCHAR(255) => openads_varchar => 2 
    public $description;                     // VARCHAR(255) => openads_varchar => 2 
    public $compiledlimitation;              // TEXT() => openads_text => 162 
    public $acl_plugins;                     // TEXT() => openads_text => 34 
    public $active;                          // SMALLINT(1) => openads_smallint => 17 
    public $comments;                        // TEXT() => openads_text => 34 
    public $updated;                         // DATETIME() => openads_datetime => 142 
    public $acls_updated;                    // DATETIME() => openads_datetime => 142 

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Channel',$k,$v); }

    var $defaultValues = array(
                'agencyid' => 0,
                'affiliateid' => 0,
                'compiledlimitation' => '',
                'updated' => '%DATE_TIME%',
                'acls_updated' => '%NO_DATE_TIME%',
                );

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function delete($useWhere = false, $cascade = true, $parentid = null)
    {
    	// Find acls which use this channels
    	$dalAcls = OA_Dal::factoryDAL('acls');
    	$rsChannel = $dalAcls->getAclsByDataValueType($this->channelid, 'Site:Channel');
    	$rsChannel->reset();
    	while ($rsChannel->next()) {
    	    // Get the IDs of the banner that's using this channel
    	    $bannerId = $rsChannel->get('bannerid');

    	    // Get the remaining channels the banner will use, if any
    		$aChannelIds = explode(',', $rsChannel->get('data'));
    		$aChannelIds = array_diff($aChannelIds, array($this->channelid));

    		// Prepare to update the banner's limitations in the "acls" table
    		$doAcls = DB_DataObject::factory('acls');
    		$doAcls->init();
    		$doAcls->bannerid = $bannerId;
    		$doAcls->executionorder = $rsChannel->get('executionorder');
    		if (!empty($aChannelIds)) {
	    		$doAcls->data = implode(',', $aChannelIds);
	    		$doAcls->update();
    		} else {
    			$doAcls->delete();
    		}

    		// Re-compile the banner's limitations
            $aAcls = array();
    		$doAcls = DB_DataObject::factory('acls');
    		$doAcls->init();
    		$doAcls->bannerid = $bannerId;
    		$doAcls->orderBy('executionorder');
            $doAcls->find();
            while ($doAcls->fetch()) {
                $aData = $doAcls->toArray();
                $deliveryLimitationPlugin = OX_Component::factoryByComponentIdentifier('deliveryLimitations:'.$aData['type']);
                if ($deliveryLimitationPlugin)
                {
                    $deliveryLimitationPlugin->init($aData);
                    if ($deliveryLimitationPlugin->isAllowed($page)) {
                        $aAcls[$aData['executionorder']] = $aData;
                    }
                }
            }
            $sLimitation = MAX_AclGetCompiled($aAcls, $page);
            // TODO: it should be done inside plugins instead, there is no need to slash the data
            $sLimitation = (!get_magic_quotes_runtime()) ? stripslashes($sLimitation) : $sLimitation;
            $doBanners = OA_Dal::factoryDO('banners');
            $doBanners->bannerid = $bannerId;
            $doBanners->find();
            $doBanners->fetch();
            $doBanners->acl_plugins = MAX_AclGetPlugins($aAcls);
            $doBanners->acls_updated = OA::getNow();
            $doBanners->compiledlimitation = $sLimitation;
            $doBanners->update();
    	}

    	return parent::delete($useWhere, $cascade, $parentid);
    }

    function duplicate($channelId)
    {
        //  Populate $this with channel data
        $this->get($channelId);

        // Prepare a new name for the channel
        $this->name = $GLOBALS['strCopyOf'] . ' ' . $this->name;

        // Duplicate channel
        $this->channelid = null;
        $newChannelId = $this->insert();

        // Duplicate channel's acls
        $result = OA_Dal::staticDuplicate('acls_channel', $channelId, $newChannelId);

        return $newChannelId;
    }


    function _auditEnabled()
    {
        return true;
    }

     function _getContextId()
    {
        return $this->channelid;
    }

    function _getContext()
    {
        return 'Channel';
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
        // A channel can be "owned" by a manager account, or
        // by an advertiser account
        if (!empty($this->affiliateid)) {
            // The channel is owned by an advertiser account, but
            // channels don't have an account_id, so get it from the
            // parent advertiser account (stored in the "affiliates"
            // table) using the "affiliateid" key
            return parent::getOwningAccountIds('affiliates', 'affiliateid');
        }
        // The channel is owned by a manager account, but
        // channels don't have an account_id, so get it from the
        // parent manager account (stored in the "agency" table) using
        // the "agencyid" key
        return parent::getOwningAccountIds('agency', 'agencyid');
    }

    /**
     * build a client specific audit array
     *
     * @param integer $actionid
     * @param array $aAuditFields
     */
    function _buildAuditArray($actionid, &$aAuditFields)
    {
        $aAuditFields['key_desc']   = $this->name;
        switch ($actionid)
        {
            case OA_AUDIT_ACTION_UPDATE:
                        $aAuditFields['affiliateid'] = $this->affiliateid;
                        break;
        }
    }

}

?>