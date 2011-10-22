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
$Id: maintenance-plugins.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/www/admin/lib-maintenance.inc.php';

// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN);

/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

phpAds_PageHeader("maintenance-index");
phpAds_MaintenanceSelection("plugins");


/*-------------------------------------------------------*/
/* Main code                                             */
/*-------------------------------------------------------*/

echo "<br />";
echo $strPluginsPrecis;
echo "<br /><br />";

phpAds_registerGlobal('action', 'returnurl');

if (!empty($action))
{
    switch ($action)
    {
        case 'rep':
            // generates brief display and detailed log
            // with debug info on plugin installations and status
            require_once(LIB_PATH.'/Extension/ExtensionCommon.php');
            $oExtensionManager = new OX_Extension_Common();
            $aPlugins = $oExtensionManager->getPluginsDiagnostics();
            $oTpl = new OA_Admin_Template('plugin-report.html');
            $oTpl->assign('aPlugins', $aPlugins['simple']);
            $oTpl->assign('aErrors', $aPlugins['errors']);
            if ($fp = fopen(MAX_PATH.'/var/plugins-report.log','w'))
            {
                fwrite($fp, "********** Display array var_dump **********\n");
                fwrite($fp, print_r($aPlugins['simple'],true));
                fwrite($fp, "\n********** Errors array var_dump: **********\n");
                fwrite($fp, print_r($aPlugins['errors'],true));
                fwrite($fp, "\n********** getPluginsDiagnostics() var_dump: **********\n");
                fwrite($fp, print_r($aPlugins['detail'],true));
                fclose($fp);
            }
            break;
        case 'pref':
            // this rebuilds the cached array that holds the text and links
            // for the account-preferences drop-down list
            require_once(LIB_PATH.'/Extension/ExtensionCommon.php');
            $oExtensionManager = new OX_Extension_Common();
            $oExtensionManager->cachePreferenceOptions();
            break;
        case 'hook':
            // this rebuilds the cached array that holds the component hook registration array
            require_once(LIB_PATH.'/Extension/ExtensionCommon.php');
            $oExtensionManager = new OX_Extension_Common();
            $oExtensionManager->cacheComponentHooks();
            break;
        case 'reg':
            // currently rewrites delivery hooks to conf
            require_once(LIB_PATH.'/Extension/ExtensionDelivery.php');
            $oExtensionManager = new OX_Extension_Delivery();
            $oExtensionManager->runTasksOnDemand();
            break;
        case 'exp':
            $oTpl = new OA_Admin_Template('plugin-export.html');
            require_once LIB_PATH.'/Plugin/PluginExport.php';
            $oExporter = new OX_PluginExport();
            $aErrors = array();
            foreach ($GLOBALS['_MAX']['CONF']['plugins'] as $name => $enabled)
            {
                $aPlugins[$name]['file'] = '';
                $aPlugins[$name]['error'] = false;
                if ($file = $oExporter->exportPlugin($name))
                {
                    $aPlugins[$name]['file'] = $file;
                }
                else
                {
                    $aPlugins[$name]['error'] = true;
                    $aErrors[] = $oExporter->aErrors;
                }
            }
            $oTpl->assign('aPlugins', $aPlugins);
            $oTpl->assign('aErrors', $aErrors);
            break;
        /*case 'dep':
            require_once LIB_PATH . '/Plugin/PluginManager.php';
            $oPluginManager = & new OX_PluginManager();
            $oPluginManager->_cacheDependencies();
            if (empty($oPluginManager->aErrors))
            {
                $oPluginManager->aMessages[] = 'No dependency problems detected';
            }
            break;*/
        default:
    }
}

phpAds_ShowBreak();
echo "<img src='" . OX::assetPath() . "/images/".$phpAds_TextDirection."/icon-undo.gif' border='0' align='absmiddle'>&nbsp;<a href='maintenance-plugins.php?action=hook'>Rebuild Component Hooks Cache</a>&nbsp;&nbsp;";
phpAds_ShowBreak();
echo "<img src='" . OX::assetPath() . "/images/".$phpAds_TextDirection."/icon-undo.gif' border='0' align='absmiddle'>&nbsp;<a href='maintenance-plugins.php?action=pref'>Rebuild Preferences List</a>&nbsp;&nbsp;";
phpAds_ShowBreak();
echo "<img src='" . OX::assetPath() . "/images/".$phpAds_TextDirection."/icon-undo.gif' border='0' align='absmiddle'>&nbsp;<a href='maintenance-plugins.php?action=reg'>Rebuild Delivery Hooks Cache</a>&nbsp;&nbsp;";
phpAds_ShowBreak();
echo "<img src='" . OX::assetPath() . "/images/".$phpAds_TextDirection."/icon-undo.gif' border='0' align='absmiddle'>&nbsp;<a href='maintenance-plugins.php?action=rep'>Plugin Report</a>&nbsp;&nbsp;";
phpAds_ShowBreak();
echo "<img src='" . OX::assetPath() . "/images/".$phpAds_TextDirection."/icon-undo.gif' border='0' align='absmiddle'>&nbsp;<a href='maintenance-plugins.php?action=exp'>Export All Plugins</a>&nbsp;&nbsp;";
phpAds_ShowBreak();

/*echo "<img src='" . OX::assetPath() . "/images/".$phpAds_TextDirection."/icon-undo.gif' border='0' align='absmiddle'>&nbsp;<a href='maintenance-plugins.php?action=dep'>Check Dependencies</a>&nbsp;&nbsp;";
phpAds_ShowBreak();*/
if ($oTpl)
{
    $oTpl->display();
}

/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

phpAds_PageFooter();

?>
