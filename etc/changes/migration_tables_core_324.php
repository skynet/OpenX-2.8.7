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
$Id: migration_tables_core_324.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

require_once(MAX_PATH.'/lib/OA/Upgrade/Migration.php');

class Migration_324 extends Migration
{

    function Migration_324()
    {
        //$this->__construct();

		$this->aTaskList_constructive[] = 'beforeAddField__banners__acl_plugins';
		$this->aTaskList_constructive[] = 'afterAddField__banners__acl_plugins';
		$this->aTaskList_constructive[] = 'beforeAddField__banners__comments';
		$this->aTaskList_constructive[] = 'afterAddField__banners__comments';
		$this->aTaskList_constructive[] = 'beforeAddField__banners__updated';
		$this->aTaskList_constructive[] = 'afterAddField__banners__updated';
		$this->aTaskList_constructive[] = 'beforeAddField__banners__acls_updated';
		$this->aTaskList_constructive[] = 'afterAddField__banners__acls_updated';


		$this->aObjectMap['banners']['acl_plugins'] = array('fromTable'=>'banners', 'fromField'=>'acl_plugins');
		$this->aObjectMap['banners']['comments'] = array('fromTable'=>'banners', 'fromField'=>'comments');
		$this->aObjectMap['banners']['updated'] = array('fromTable'=>'banners', 'fromField'=>'updated');
		$this->aObjectMap['banners']['acls_updated'] = array('fromTable'=>'banners', 'fromField'=>'acls_updated');
    }



	function beforeAddField__banners__acl_plugins()
	{
		return $this->beforeAddField('banners', 'acl_plugins');
	}

	function afterAddField__banners__acl_plugins()
	{
		return $this->afterAddField('banners', 'acl_plugins');
	}

	function beforeAddField__banners__comments()
	{
		return $this->beforeAddField('banners', 'comments');
	}

	function afterAddField__banners__comments()
	{
		return $this->afterAddField('banners', 'comments');
	}

	function beforeAddField__banners__updated()
	{
		return $this->beforeAddField('banners', 'updated');
	}

	function afterAddField__banners__updated()
	{
		return $this->afterAddField('banners', 'updated');
	}

	function beforeAddField__banners__acls_updated()
	{
		return $this->beforeAddField('banners', 'acls_updated');
	}

	function afterAddField__banners__acls_updated()
	{
		return $this->afterAddField('banners', 'acls_updated') && $this->migrateData();
	}

	function migrateData()
	{
	    return $this->migrateAcls();
	}

	var $aAclsTypes = array(
        'weekday' => 'Time:Day',
        'time' => 'Time:Hour',
        'date' => 'Time:Date',
        'clientip' => 'Client:Ip',
        'domain' => 'Client:Domain',
        'language' => 'Client:Language',
        'continent' => 'Geo:Continent',
        'country' => 'Geo:Country',
        'browser' => 'Client:Useragent',
        'os' => 'Client:Useragent',
        'useragent' => 'Client:Useragent',
        'referer' => 'Site:Referingpage',
        'source' => 'Site:Source'
    );

    var $aPlugins = array();


	function migrateAcls()
	{
	    $tableAcls = $this->getPrefix() . "acls";
	    $sql = "SELECT * FROM $tableAcls";
	    $rsAcls = DBC::NewRecordSet($sql);
	    if (!$rsAcls->find()) {
	        return false;
	    }
	    $aUpdates = array();
	    while ($rsAcls->fetch()) {
	        $bannerid = $rsAcls->get('bannerid');
	        $executionorder = $rsAcls->get('executionorder');
	        $oldType = $rsAcls->get('type');
	        if (!isset($this->aAclsTypes[$oldType])) {
	            $this->_logError("Unknown acls type: $oldType");
	            return false;
	        }
	        $type = $this->aAclsTypes[$oldType];
	        $oldComparison = $rsAcls->get('comparison');
	        $oldData = $rsAcls->get('data');

	        $oPlugin = &$this->_getDeliveryLimitationPlugin($type);
	        if (!$oPlugin) {
	            $this->_logError("Can't find code for delivery limitation plugin: $type.");
	            return false;
	        }

	        $aNewAclsData = $oPlugin->getUpgradeFromEarly($oldComparison, $oldData);

	        $comparison = $aNewAclsData['op'];
	        $data = $aNewAclsData['data'];
	        $aUpdates []= "UPDATE $tableAcls SET type = '$type', comparison = '$comparison', data = '$data'
	        WHERE bannerid = $bannerid
	        AND executionorder = $executionorder";
	    }

	    foreach($aUpdates as $update) {
	        $result = $this->oDBH->exec($update);
	        if (PEAR::isError($result)) {
	            $this->_logError("Couldn't execute update: $update");
	            return false;
	        }
	    }

	    return true;
	}

    /**
     * A private method to instantiate a delivery limitation plugin object.
     *
     * @param string $sType The delivery limitation plugin package and name,
     *                      separated with a colon ":". For example, "Geo:Country".
     * @return
     */
    function _getDeliveryLimitationPlugin($sType)
    {
        if (isset($this->aPlugins[$sType])) {
            return $this->aPlugins[$sType];
        }

        list($package, $name) = explode(':', $sType);
        $className = 'Upgrade_DeliveryLimitations_' . ucfirst($package) . '_' . ucfirst($name);
        $this->aPlugins[$sType] = new $className();
        return $this->aPlugins[$sType];
    }

}

?>