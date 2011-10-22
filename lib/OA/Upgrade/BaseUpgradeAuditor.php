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
/**
 * OpenX Upgrade Audit Class
 *
 * @author     Matthieu Aubry <matthieu.aubry@openx.org>
 *
 * $Id: BaseUpgradeAuditor.php 62345 2010-09-14 21:16:38Z chris.nutting $
 *
 */

class OA_BaseUpgradeAuditor
{

    // needs to be defined in the child class
    var $action_table_xml_filename;

    var $logTable   = '';

	function OA_BaseUpgradeAuditor()
	{
	}

	function init(&$oDbh='', $oLogger='')
	{
	    if ($oDbh)
	    {
            $this->oDbh = $oDbh;
	    }
	    else
	    {
            $this->oDbh = OA_DB::singleton();
	    }
        $this->prefix = $GLOBALS['_MAX']['CONF']['table']['prefix'];
        // so that this class can log to the caller's log
        // and write it's own log if necessary (testing)
        if ($oLogger)
        {
            $this->oLogger= $oLogger;
        }
        return $this->_checkCreateAuditTable();
	}

	function getLogTableName()
	{
	    return $this->oDbh->quoteIdentifier($this->prefix.$this->logTable,true);
	}

	/**
     * audit actions taken
     *
     * @param array $aParams
     * @return boolean
     */
    function logAuditAction($aParams=array())
    {
        $aParams = $this->_escapeParams($aParams);
        $columns = implode(",", array_keys($this->aParams)).','.implode(",", array_keys($aParams));
        $values  = implode(",", array_values($this->aParams)).','.implode(",", array_values($aParams));
        $table = $this->getLogTableName();
        $query = "INSERT INTO {$table} ({$columns}, updated) VALUES ({$values}, '". OA::getNow() ."')";
        $auditId = $this->getNextUpgradeActionId();
        $result = $this->oDbh->exec($query);
        if ($this->isPearError($result, "error inserting {$this->prefix}{$this->logTable}"))
        {
            return false;
        }
        return $auditId;
    }

    function updateAuditAction($aParams=array())
    {
        $id = (isset($aParams['id']) ? $aParams['id'] : $this->getUpgradeActionId());
        unset($aParams['id']);
        if (!$id)
        {
            $this->logError('upgrade_action_id is empty');
            return false;
        }
        $aParams = $this->_escapeParams($aParams);

        $values = '';
        foreach ($aParams AS $k => $v)
        {
            $values.= "{$k}={$v},";
        }
        $values.= "updated='".OA::getNow()."'";
        $table = $this->getLogTableName();
        $query = "UPDATE {$table} SET {$values} WHERE upgrade_action_id={$id}";
        $result = $this->oDbh->exec($query);

        if ($this->isPearError($result, "error inserting {$this->prefix}{$this->logTable}"))
        {
            return false;
        }
        return true;
    }

    function setKeyParams($aParams='')
    {
        $this->aParams = $this->_escapeParams($aParams);
    }

    function getNextUpgradeActionId()
    {
        return true;
    }

    /**
     * the action_table_name table must exist for all upgrade events
     * currently the schema is stored in a separate xml file which is not part of an upgrade pkg
     * eventually this table schema should be merged into the core tables schema
     *
     * @return boolean
     */
    function _createAuditTable()
    {
        $xmlfile = MAX_PATH.$this->action_table_xml_filename;

        $oTable = new OA_DB_Table();
        $oTable->init($xmlfile);
        return $oTable->createTable($this->logTable);
    }

    function _checkCreateAuditTable()
    {
        $this->aDBTables = OA_DB_Table::listOATablesCaseSensitive();
        if (!in_array($this->prefix.$this->logTable, $this->aDBTables))
        {
            $this->log('creating '.$this->logTable.' audit table');
            if (!$this->_createAuditTable())
            {
                $this->logError('failed to create '.$this->logTable.' audit table');
                return false;
            }
            $this->log('successfully created '.$this->logTable.' audit table');
        }
        return true;
    }

    function _escapeParams($aParams)
    {
        foreach ($aParams AS $k => $v)
        {
            $aParams[$k] = $this->oDbh->quote($v);
        }
        return $aParams;
    }

}
?>
