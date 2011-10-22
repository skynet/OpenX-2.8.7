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
$Id: migration_tables_core_601.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

require_once(MAX_PATH.'/lib/OA/Upgrade/Migration.php');

class Migration_601 extends Migration
{

    function Migration_601()
    {
        //$this->__construct();

		$this->aTaskList_constructive[] = 'beforeAddField__campaigns__viewwindow';
		$this->aTaskList_constructive[] = 'afterAddField__campaigns__viewwindow';
		$this->aTaskList_constructive[] = 'beforeAddField__campaigns__clickwindow';
		$this->aTaskList_constructive[] = 'afterAddField__campaigns__clickwindow';
		$this->aTaskList_destructive[] = 'beforeRemoveField__campaigns_trackers__viewwindow';
		$this->aTaskList_destructive[] = 'afterRemoveField__campaigns_trackers__viewwindow';
		$this->aTaskList_destructive[] = 'beforeRemoveField__campaigns_trackers__clickwindow';
		$this->aTaskList_destructive[] = 'afterRemoveField__campaigns_trackers__clickwindow';


		$this->aObjectMap['campaigns']['viewwindow'] = array('fromTable'=>'campaigns', 'fromField'=>'viewwindow');
		$this->aObjectMap['campaigns']['clickwindow'] = array('fromTable'=>'campaigns', 'fromField'=>'clickwindow');
    }



	function beforeAddField__campaigns__viewwindow()
	{
		return $this->beforeAddField('campaigns', 'viewwindow');
	}

	function afterAddField__campaigns__viewwindow()
	{
		return $this->afterAddField('campaigns', 'viewwindow');
	}

	function beforeAddField__campaigns__clickwindow()
	{
		return $this->beforeAddField('campaigns', 'clickwindow');
	}

	function afterAddField__campaigns__clickwindow()
	{
		return $this->afterAddField('campaigns', 'clickwindow');
	}

	/**
	 * Migrate the largest clickwindow value for any linked tracker-campaign
	 * into the campaigns table before dropping the field
	 *
	 * @return boolean True on sucess, false otherwise
	 */
	function beforeRemoveField__campaigns_trackers__viewwindow()
	{
        $aConf = $GLOBALS['_MAX']['CONF']['table'];
        $prefix = $aConf['prefix'];
        $tblCampaigns = $this->_getTableName('campaigns');
        $tblCampaignsTrackers = $this->_getTableName('campaigns_trackers');

        $query = "
            SELECT
                campaignid AS campaign_id,
                MAX(viewwindow) AS max_viewwindow,
                MAX(clickwindow) AS max_clickwindow
            FROM
                " . $this->oDBH->quoteIdentifier($tblCampaignsTrackers) . " AS ct
            GROUP BY
                campaign_id
        ";

        $rs = $this->oDBH->query($query);

        //check for error
        if (PEAR::isError($rs))
        {
            $this->logError($rs->getUserInfo());
            return false;
        }

        while ($aCampaignTrackers = $rs->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $updateQuery = "
                UPDATE
                    " . $this->oDBH->quoteIdentifier($tblCampaigns) . "
                SET
                    viewwindow = '{$aCampaignTrackers['max_viewwindow']}',
                    clickwindow = '{$aCampaignTrackers['max_clickwindow']}'
                WHERE
                    campaignid = '{$aCampaignTrackers['campaign_id']}'
            ";

            $this->oDBH->query($updateQuery);
        }

		return $this->beforeRemoveField('campaigns_trackers', 'viewwindow');
	}

	function afterRemoveField__campaigns_trackers__viewwindow()
	{
		return $this->afterRemoveField('campaigns_trackers', 'viewwindow');
	}

	/**
	 * Migrate the largest clickwindow value for any linked tracker-campaign
	 * into the campaigns table before dropping the field
	 *
	 * @return boolean True on sucess, false otherwise
	 */
	function beforeRemoveField__campaigns_trackers__clickwindow()
	{
		return $this->beforeRemoveField('campaigns_trackers', 'clickwindow');
	}

	function afterRemoveField__campaigns_trackers__clickwindow()
	{
		return $this->afterRemoveField('campaigns_trackers', 'clickwindow');
	}

	/**
	 * Get the name of a table
	 *
	 * @param unknown_type $table
	 */
	function _getTableName($table)
	{
	    $aConf = $GLOBALS['_MAX']['CONF']['table'];
	    return $aConf['prefix'] . ($aConf[$table] ? $aConf[$table] : $table);
	}
}

?>