<?php

/*
+---------------------------------------------------------------------------+
| OpenX  v2.8                                                              |
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
$Id: FileScanner.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

/**
 * A class used to read the files from choosen directory which match
 * the specific criteria (ereg or just file extension). First version was
 * created as a university project (php loc counter).
 *
 * The idea of this class was taken from: http://sourceforge.net/projects/phpduploc
 *
 * @package    Max
 * @author     Radek Maciaszek <radek@m3.net>
 */
class MAX_FileScanner
{
	var $_files;
	var $_allowedFileMask;
	var $_allowedFileTypes;

	var $_lastMatch;
	var $_sorted;

	/**
	 * Constructor
	 */
	function MAX_FileScanner()
	{
		$this->_allowedFileTypes = array();
		$this->_allowedFileMask = null; // eg: '^([a-zA-Z0-9\-]*)\.plugin\.php$'
		$this->reset();
	}

	/**
	 * Reset array of files
	 *
	 */
	function reset()
	{
		$this->_files = array();
		$this->_lastMatch = null;
	}

	/**
	 * Add specific file to the array of files
	 *
	 * @param string $file  File name
	 *
	 */
	function addFile($file)
	{
		$this->_sorted = false;
		if ($this->isAllowedFile($file)) {
		    if (!in_array($file, $this->_files)) {
		        $key = $this->buildKey($file);
		        if (empty($key)) {
		            $this->_files[] = $file;
		        } else {
		            $this->_files[$key] = $file;
		        }
		    }
		}
	}

	/**
	 * Read entire folder and add all the files
	 *
	 * @param string $dir                 Folder name
	 * @param integer|boolean $recursive  If true add also subdirectories
	 *                                    If integer - how deep, how many levels
	 *
	 */
	function addDir($dir, $recursive = false)
	{
        if ($recursive) {
		    return $this->_addRecursiveDir($dir, $recursive);
		}
	    if ($handle = opendir($dir)) {
            while ($file = readdir($handle)) {
                if (is_dir($dir.'/'.$file)) {
                    continue;
                }
                $this->addFile($dir.'/'.$file);
            }
            closedir($handle);
        }
	}

	/**
	 * Read recursively entire directory and subdirectories and add
	 * every file to the pool of files.
	 *
	 * @param string $dir      Directory name
	 * @param integer|boolean  How many subdirectories (levels) read, how deep
	 *
	 */
	function _addRecursiveDir($dir, $recursive = true)
	{
	    if ($recursive !== true) {
		    if ($recursive < 0) {
		        return;
		    }
		    $recursive--;
		}
		// Don't try and scan non-dirs :)
		if (!is_dir($dir)) {
		    return;
		}
	    if ($handle = opendir($dir)) {
            while ($file = readdir($handle)) {
                if (is_dir($dir.'/'.$file) && $file != '.' && $file != '..') {
                    $this->_addRecursiveDir($dir.'/'.$file, $recursive);
                    continue;
                }
                $this->addFile($dir.'/'.$file);
            }
            closedir($handle);
        }
	}

	/**
	 * Return list of files
	 *
	 */
	function getAllFiles()
	{
		if (!$this->_sorted) {
			$this->_sorted = true;
			if (!empty($this->_allowedFileMask)) {
				asort($this->_files, SORT_STRING);
	    	} else {
				sort($this->_files, SORT_STRING);
	    	}
		}
		return $this->_files;
	}

	/**
	 * Set new file mask
	 *
	 * @param string $fileMask  New file mask
	 *
	 */
	function setFileMask($fileMask)
	{
	    $this->_allowedFileMask = $fileMask;
	}

	/**
	 * Add possible file extensions to fileTypes array
	 *
	 * @param array $fileTypes  Array of new types
	 *
	 * @return boolean True if array of existings file types was modified else false
	 */
	function addFileTypes($fileTypes)
	{
		if (!is_array($fileTypes)) {
		    $fileTypes = array($fileTypes);
		}
	    $modified = false;
        if (is_array($fileTypes)) {
		    foreach ($fileTypes as $fileType) {
		        if (!in_array($fileType, $this->_allowedFileTypes)) {
		            $this->_allowedFileTypes[] = $fileType;
		            $modified = true;
		        }
		    }
		}
		return $modified;
	}

	/**
	 * Check if a file is allowed. Check extension of file and if file matching the file mask
	 *
	 * @param string $fileName  File name
	 *
	 * @return boolean  True if file name match the criteria else false
	 */
	function isAllowedFile($fileName)
	{
	    if (!empty($this->_allowedFileTypes)) {
	        // Check extension
    	    $ext = $this->getFileExtension($fileName);
            // Check if uploaded file is of valid type
            if (!in_array(strtolower($ext), $this->_allowedFileTypes)) {
                return false;
            }
	    }
        // Check if file name is allowed
        if (!empty($this->_allowedFileMask)) {
        	$matches = null;
            if (!ereg($this->_allowedFileMask, $fileName, $matches)) {
                return false;
            } else {
                $this->_lastMatch = $matches;
            }
	    }
	    return true;
	}

	/**
	 * Return extension of file
	 *
	 * @param string $fileName  Name of the file
	 *
	 * @return string  File extension
	 * @static
	 */
	function getFileExtension($fileName)
	{
        return substr($fileName, strrpos($fileName, '.')+1, strlen($fileName));
	}

	/**
	 * Return file name
	 *
	 * @param string $fileName  Name of the file
	 *
	 * @return string  File name
	 * @static
	 */
	function getFileName($fileName)
	{
	   return substr($fileName, strrpos($fileName, '/')+1, strlen($fileName));
	}

	/**
	 * Check if a file is allowed. Check extension of file and if file matching the file mask
	 *
	 * TODO: add configuration to this method - now building the key is hardcoded
	 *
	 * @param string $fileName  File name
	 *
	 * @return string  Key, this is package name and plugin name
	 */
	function buildKey($fileName)
	{
	    if (empty($this->_allowedFileMask)) {
    	    return null;
    	}
	    if (!empty($this->_lastMatch)) {
    	    $matches = $this->_lastMatch;
    	} else {
    		$matches = null;
    	    ereg($this->_allowedFileMask, $fileName, $matches);
    	}
	    if (is_array($matches) && count($matches) == 4) {
            $key = $matches[2].':'.$matches[3];
            return $key;
        }
        return null;
	}

}

?>
