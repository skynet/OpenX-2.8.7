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
$Id: postscript_openads_upgrade_2.0.11_to_2.3.32_beta.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

$className = 'OA_UpgradePostscript_2_0_11';


class OA_UpgradePostscript_2_0_11
{
    var $oUpgrade;

    function OA_UpgradePostscript_2_0_11()
    {

    }

    function execute($aParams)
    {
        $this->oUpgrade = & $aParams[0];
        if (!$this->configPan())
        {
            return false;
        }
        $this->oUpgrade->addPostUpgradeTask('Rebuild_Banner_Cache');
        $this->oUpgrade->addPostUpgradeTask('Maintenance_Priority');
        $this->oUpgrade->addPostUpgradeTask('Recompile_Acls');
        return true;
    }

    function configPan()
    {
        if (!$this->oUpgrade->oConfiguration->putNewConfigFile())
        {
            $this->oUpgrade->oLogger->logError('Installation failed to create the configuration file');
            return false;
        }
        $aConfig = $this->oUpgrade->oPAN->aConfig;
        $aConfig['table'] = $GLOBALS['_MAX']['CONF']['table'];
        $this->oUpgrade->oConfiguration->setupConfigPan($aConfig);
        $this->oUpgrade->oConfiguration->writeConfig();
        if (!$this->oUpgrade->oConfiguration->oSettings->backupConfig(MAX_PATH.'/var/'.$this->oUpgrade->oPAN->fileCfg))
        {
            $this->oUpgrade->oLogger->logError('Failed to rename your old configuration file (non-critical, you should delete or rename /var/config.inc.php yourself)');
            $this->oUpgrade->message = 'Failed to rename your old configuration file (non-critical, you should delete or rename /var/config.inc.php yourself)';
        }
        if (file_exists(MAX_PATH.'/var/'.$this->oUpgrade->oPAN->fileCfg))
        {
            unlink(MAX_PATH.'/var/'.$this->oUpgrade->oPAN->fileCfg);
        }
        $this->oUpgrade->oLogger->log('Removed old application version');
        return true;
    }

}

?>
