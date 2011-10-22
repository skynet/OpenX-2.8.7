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
$Id$
*/

require_once MAX_PATH . '/lib/OX/Plugin/PluginExport.php';

/**
 * This class deals with importing plugin files from one location to another
 *
 * It extends the export class to make use of the plugin-XML parsing and file collecting routines
 *
 */
class OX_UpgradePluginImport extends OX_PluginExport
{
    public $aDataObjectFiles = array();
    public $aMissingFiles    = array();
    public $basePath; // The path to look for file in
    public $destPath = MAX_PATH; // The path to look to place files in

    /**
     * Initialise the plugin
     *
     * @param string $name  The name of the plugin to init
     * @return boolean Init result
     */
    function init($name)
    {
        return true;
    }

    /**
     * This method takes the plugin name, and looks for the files declared in the XML
     *
     * @param string $name The name of the plugin to prepare
     * @return boolean The preparation result
     */
    function prepare($name)
    {
        $this->clean();

        $this->oPluginManager   = new OX_PluginManager();
        $this->oPluginManager->basePath = $this->basePath;

        if (!$this->oPluginManager->_parsePackage($name))
        {
            $this->aErrors = $this->oPluginManager->aErrors;
            return false;
        }
        $this->aPlugin = &$this->oPluginManager->aParse['package'];
        if (!$this->oPluginManager->_parseComponentGroups($this->aPlugin['install']['contents']))
        {
            $this->aErrors = $this->oPluginManager->aErrors;
            return false;
        }
        $this->aGroups = &$this->oPluginManager->aParse['plugins'];
        $this->_compileContents($name);

        // Check if any of the registered files are DataObject files
        foreach ($this->aFileList as $file) {
            if (preg_match('#.*etc/DataObjects/.*\.php$#', $file)) {
                $this->aDataObjectFiles[] = basename($file);
            }
        }
        return true;
    }

    /**
     * This method takes the file-list from the (potential) old path
     * and
     *
     * @param unknown_type $name
     * @return unknown
     */
    function import($name)
    {
        $sucess = true;
        if ($this->prepare($name)) {
            foreach ($this->aFileList as $file) {
                $file = substr($file, strlen($this->basePath));
                $sourceFile = $this->basePath . $file;
                // If the sourceFile can't be found, try looking in /extensions/ instead
                if (!file_exists($sourceFile)) {
                    $sourceFile = str_replace(DIRECTORY_SEPARATOR, '/', $sourceFile);
                    $sourceFile = str_replace('/plugins/', '/extensions/', $sourceFile);
                }
                // Ensure that the destination file is placed in /plugins/
                $destFile = str_replace(DIRECTORY_SEPARATOR, '/', $this->destPath . $file);
                $destFile = str_replace('/extensions/', '/plugins/', $destFile);
                $this->_makeDirectory(dirname($destFile));
                @copy($sourceFile, $destFile);
                // Deal with deliveryLimitation plugins which may reference phpSniff from core not the plugin
                if ($name == 'openXDeliveryLimitations' && stristr($file, '/Client/')) {
                    $contents = file_get_contents($this->destPath . '/' . $file);
                    $contents = preg_replace('#^(require_once MAX_PATH \. \'/lib/phpSniff/phpSniff.class.php\';)$#m', "// \$1\nclass phpSniff { } \n", $contents);
                    file_put_contents($this->destPath . '/' . $file, $contents);
                }
            }
        } else {
            $sucess = false;
        }
        return $sucess;
    }

    function verifyAll($aPlugins = array(), $checkDataObjects = true)
    {
        $sucess = true;
        $this->aDataObjectFiles = array();
        $this->_log('Starting file-check for plugins...');
        foreach ($aPlugins as $plugin => $enabled) {
            if (!$this->verify($plugin)) {
                $sucess = false;
            }
        }
        foreach ($this->aDataObjectFiles as $file) {
            if ($checkDataObjects && !file_exists($this->destPath . '/var/plugins/DataObjects/' . $file)) {
                $this->_log("Plugin DataObject files: Unable to locate file: " . $file);
                $sucess = false;
            }
        }
        $this->_log('Finished file-check for plugins');
        return $sucess;
    }

    /**
     * This method verifies that all the files declared in the plugin's XML are present under MAX_PATH
     *
     * @param string $name The name of the plugin to be checked
     * @return boolean  True if all declared files can be found, false otherwise
     */
    function verify($name)
    {
        $sucess = true;
        if ($this->prepare($name)) {
            foreach ($this->aFileList as $file) {
                $filename = $this->basePath . substr($file, strlen($this->basePath));
                if (!file_exists($filename)) {
                    // Check for a pre 2.7.31 path (extensions not plugins)
                    $filename = str_replace(DIRECTORY_SEPARATOR, '/', $filename);
                    $filename = str_replace('/plugins/', '/extensions/', $filename);
                    if (!file_exists($filename)) {
                        $this->_log("Plugin: {$name} - Unable to locate file: " . $filename);
                        $sucess = false;
                    }
                }
            }
        } else {
            $this->_log("Plugin: {$name} - Unable to locate XML files");
            $sucess = false;
        }
        return $sucess;
    }

    /**
     * write a message to the logfile
     *
     * @param string $message
     */
    function _log($message)
    {
        $this->aMessages[] = $message;
        if(empty($this->logFile)) {
            $this->logFile = MAX_PATH . '/var/install.log';
        }
        $log = fopen($this->logFile, 'a');
        if($log) {
	        fwrite($log, "{$message}\n");
	        fclose($log);
        }
    }
}

?>
