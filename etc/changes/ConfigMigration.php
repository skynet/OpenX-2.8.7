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
$Id: ConfigMigration.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

require_once MAX_PATH.'/lib/max/Plugin.php';
require_once MAX_PATH.'/lib/OA/Upgrade/Configuration.php';
require_once MAX_PATH.'/lib/max/FileScanner.php';

/**
 * Indicates all modules
 *
 */
define('OA_PLUGINS_ALL_MODULES', 1);

/**
 * Migrates configuration
 *
 */
class ConfigMigration
{

	/**
	 * Following method merges geotargeting plugins config
	 * into global config file. By doing that we are improving
	 * delivery performance for scripts using geotargeting data
	 * by more than 15%.
	 *
	 * @return boolean  True on success else false
	 */
    function mergeConfigWith($section, $mergeWithConf)
    {
        $config = new OA_Admin_Settings();
        $config->bulkSettingChange($section, $mergeWithConf);
        return $config->writeConfigChange();
    }

    /**
     * Returns geotargeting config
     *
     * @return array  Specific plugin configuration
     */
    function getGeotargetingConfig()
    {
        return $this->getPluginsConfigByType('geotargeting');
    }

    /**
     * Returns plugin config by its module merged with specifig package
     * config. Package type is read from module config.
     *
     * @param string $module
     * @return array
     */
    function getPluginsConfigByType($module)
    {
    	if(isset($GLOBALS['_MAX']['CONF'][$module])) {
        	// Make sure config is always read from ini file
        	unset($GLOBALS['_MAX']['CONF'][$module]);
        }
    	$conf = MAX_Plugin::getConfig($module);
        $aConfig = MAX_Plugin::getConfig($module, $conf['type']);
        if (is_array($aConfig)) {
            $conf = array_merge($conf, $aConfig);
        }
        return $conf;
    }

    /**
     * Change the plugins config name affix (used to change all plugins affixes)
     * For example from *.ini to *.php
     *
     * @param string $oldAffix
     * @param string $newAffix
     * @return boolean  True on success else false
     */
    function renamePluginsConfigAffix($oldAffix, $newAffix)
    {
        $aFiles = $this->getPluginsConfigFiles(OA_PLUGINS_ALL_MODULES, $oldAffix);
        foreach ($aFiles as $oldFileName) {
            $newFileName = str_replace('.conf.'.$oldAffix, '.conf.'.$newAffix, $oldFileName);
            if(!rename($oldFileName, $newFileName)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Reads list of files from plugins with affix (ini)
     *
     * @param string $affix
     * @return array
     */
    function getPluginsConfigFiles($module = OA_PLUGINS_ALL_MODULES, $affix = 'php')
    {
        $oFileScanner = new MAX_FileScanner();
        $oFileScanner->addFileTypes(array($affix));
        $readFromDir = MAX_PATH.'/var/plugins/config';
        if($module != OA_PLUGINS_ALL_MODULES) {
            $readFromDir .= '/'.$module;
        }
        $oFileScanner->addDir($readFromDir, $recursive = true);
        return $oFileScanner->getAllFiles();
    }

    /**
     * Removes geotargeting folder and all its subfolders - as it is not needed anymore
     * after moving its configuration to main folder
     *
     */
    function deleteGeotargetingConfigFiles()
    {
        $geotargetingDir = MAX_PATH.'/var/plugins/config/geotargeting';
        if (System::rm(array('-rf', $geotargetingDir)) !== true) {
            return false;
        }
        return true;
    }

}

?>