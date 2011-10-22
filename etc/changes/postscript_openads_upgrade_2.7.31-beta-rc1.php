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
$Id: postscript_openads_upgrade_2.7.31-beta-rc1.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

$className = 'OA_UpgradePostscript_2_7_31_beta_rc1';


class OA_UpgradePostscript_2_7_31_beta_rc1
{
    /**
     * @var OA_Upgrade
     */
    var $oUpgrade;

    function OA_UpgradePostscript_2_7_31_beta_rc11()
    {

    }

    function execute($aParams)
    {
        $this->oUpgrade =& $aParams[0];
        $this->oUpgrade->oConfiguration->aConfig = $GLOBALS['_MAX']['CONF'];
        // Change the pluginPaths values from /extensions/ to /plugins/
        $this->oUpgrade->oConfiguration->aConfig['pluginPaths']['plugins'] = '/plugins/';
        $this->oUpgrade->oConfiguration->aConfig['pluginPaths']['packages'] = '/plugins/etc/';
        unset($this->oUpgrade->oConfiguration->aConfig['pluginPaths']['extensions']);
        
        // Also change the check for updates server which may have been previously set to localhost
        $this->oUpgrade->oConfiguration->aConfig['pluginUpdatesServer'] = array(
            'protocol'  => 'http',
            'host'      => 'code.openx.org',
            'path'      => '/openx/plugin-updates',
            'httpPort'  => '80',
        );
        $this->oUpgrade->oConfiguration->writeConfig();
        $this->oUpgrade->oLogger->logOnly("Renamed [pluginPaths]extensions to [pluginPaths]plugins");
        return true;
    }
}