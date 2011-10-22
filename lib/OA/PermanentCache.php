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
$Id: PermanentCache.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

require_once 'Cache/Lite.php';

/**
 * A class to read and save permanent cache data, stored in /etc
 *
 * It features a predictable cache file name and automatic (un)serialising
 * and zlib (de)compression
 *
 * @author     Matteo Beccati <matteo.beccati@openx.org>
 */
class OA_PermanentCache
{
    /**
     * @var Cache_Lite
     */
    var $oCache;

    /**
     * @var string
     */
    var $cachePath;

    /**
     * Class constructor
     *
     * @param string $cachePath The cache path
     *
     * @return OA_PermanentCache
     */
    function OA_PermanentCache($cachePath = null)
    {
        $this->cachePath = is_null($cachePath) ? MAX_PATH . '/etc/permanentcache/' : $cachePath;
        if (substr($cachePath, -1) != '/') {
            $this->cachePath .= '/';
        }
        $this->oCache = new Cache_Lite(array(
            'cacheDir'                      => $this->cachePath,
            'fileNameProtection'            => false,
            'lifeTime'                      => null,
            'readControlType'               => 'md5',
            'dontCacheWhenTheResultIsFalse' => true
        ));
    }

    /**
     * A method to get the permanent cache content
     *
     * @param string $cacheName The name of the original file we are retrieving
     * @return mixed The cache content or FALSE in case of cache miss
     */
    function get($cacheName)
    {
        if (extension_loaded('zlib')) {
            $id    = $this->_getId($cacheName);
            $group = $this->_getGroup($cacheName);

            if ($result = $this->oCache->get($id, $group, true)) {
                return unserialize(gzuncompress($result));
            }
        }

        return false;
    }

    /**
     * A method to save the permanent cache content. The content will be serialized and
     * compressed to save space
     *
     * @param mixed  $data     The content to save
     * @param string $cacheName The name of the original file we are storing
     * @return bool True if the cache was correctly saved
     */
    function save($data, $cacheName)
    {
        if (is_writable($this->cachePath) && extension_loaded('zlib')) {
            $id    = $this->_getId($cacheName);
            $group = $this->_getGroup($cacheName);
            return $this->oCache->save(gzcompress(serialize($data), 9), $id, $group);
        }

        return false;
    }

    /**
     * A method to remove a cache file
     *
     * @param string $cacheName The name of the original file
     * @return bool True if the cache was deleted
     */
    function remove($cacheName)
    {
        $id    = $this->_getId($cacheName);
        $group = $this->_getGroup($cacheName);
        return $this->oCache->remove($id, $group);
    }

    /**
     * Private method to generate the Cache_Lite cache ID from a file name
     *
     * @param string $cacheName The name of the original file
     * @return string The cache ID (the base file name without extension)
     */
    function _getId($cacheName)
    {
        // Deal with class::method style cache names
        $cacheName = str_replace('::', '/', $cacheName);

        // Strip extension
        $cacheName = preg_replace('/\.[^.]+?$/', '', $cacheName);

        $IdName = strtolower(basename($cacheName));
        return preg_replace('/[^a-z0-9]/i', '-', $IdName).'.bin';
    }

    /**
     * Private method to generate the Cache_Lite cache group from a file name
     *
     * @param string $cacheName The name of the original file
     * @return string The cache group (generated using the file path, or 'default')
     */
    function _getGroup($cacheName)
    {
        // Deal with class::method style cache names
        $cacheName = str_replace('::', '/', $cacheName);

        // Strip MAX_PATH
        if (strpos($cacheName, MAX_PATH) === 0) {
            $cacheName = substr($cacheName, strlen(MAX_PATH) + 1);
        }

        $groupName = strtolower(dirname($cacheName));
        if (!empty($groupName)) {
            return preg_replace('/[^a-z0-9]/i', '-', $groupName);
        }

        return 'default';
    }
}

?>
