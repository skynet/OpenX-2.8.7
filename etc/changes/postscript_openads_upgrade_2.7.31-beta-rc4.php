<?php

/*
+---------------------------------------------------------------------------+
| OpenX v2.8                                             |
| ==========                            |
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
$Id: postscript_openads_upgrade_2.7.31-beta-rc4.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

$className = 'OA_UpgradePostscript_2_7_31_beta_rc4';

require_once MAX_PATH . '/lib/OA/DB/Table.php';
require_once MAX_PATH . '/lib/OA/Upgrade/UpgradeLogger.php';

class OA_UpgradePostscript_2_7_31_beta_rc4
{
    /**
     * @var OA_Upgrade
     */
    var $oUpgrade;

    /**
     * @var MDB2_Driver_Common
     */
    var $oDbh;

    /**
     * DB table prefix
     *
     * @var string
     */
    var $prefix;
    var $tblPreferences;

    function OA_UpgradePostscript_2_7_31_beta_rc4()
    {

    }

    function execute($aParams)
    {
        $this->oUpgrade = & $aParams[0];

        $this->oDbh = &OA_DB::singleton();
        $aConf = $GLOBALS['_MAX']['CONF']['table'];
        $this->prefix = $aConf['prefix'];
        $this->tblPreferences = $aConf['prefix'].($aConf['preferences'] ? $aConf['preferences'] : 'preferences');

        $query = "INSERT INTO ".$this->oDbh->quoteIdentifier($this->tblPreferences,true)."
                  (preference_name, account_type)
                 VALUES('contract_ecpm_enabled', 'MANAGER')";
        $ret = $this->oDbh->query($query);
        //check for error
        if (PEAR::isError($ret))
        {
            $this->logError($ret->getUserInfo());
            return false;
        }

        $this->logOnly("Added 'contract_ecpm_enabled' preference to 'MANAGER' account");
        return true;
    }

    function logOnly($msg)
    {
        $this->oUpgrade->oLogger->logOnly($msg);
    }


    function logError($msg)
    {
        $this->oUpgrade->oLogger->logError($msg);
    }

}