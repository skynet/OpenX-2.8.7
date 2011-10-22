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
$Id: Cache.php 44885 2009-10-16 14:18:40Z bernard.lange $
*/

require_once 'Cache/Lite.php';

/**
 * A generic class to easily use Cache Lite
 *
 */
class OA_Cache
{
    /**
     * @var Cache_Lite
     */
    var $oCache;

    /**
     * @var string
     */
    var $id;

    /**
     * @var string
     */
    var $group;

    /**
     * Class constructor
     *
     * @param string $id
     * @param string $group
     * @param int $lifeTime
     * @param string $cacheDir // can be used to read cache backups from different directory
     * @return OA_Cache
     */
    function OA_Cache($id, $group, $lifeTime = null, $cacheDir = null)
    {
        if (!isset($cacheDir)) {
            $cacheDir = MAX_PATH . '/var/cache/';
        }
        
        $this->oCache = &new Cache_Lite(array(
            'cacheDir'                      => $cacheDir,
            'lifeTime'                      => $lifeTime,
            'readControlType'               => 'md5',
            'automaticSerialization'        => true
            //'dontCacheWhenTheResultIsFalse' => true, - this property does not exist
        ));

        $this->id    = $id;
        $this->group = OX_getHostName() . ((!empty($group)) ? '_' . $group : '');
    }

    /**
     * A method to load the cache contents
     *
     * @return mixed
     */
    function load($doNotTestCacheValidity = true)
    {
        return $this->oCache->get($this->id, $this->group, $doNotTestCacheValidity);
    }

    /**
     * A method to save the cache contents
     *
     * @param mixed $cache
     * @return boolean
     */
    function save($cache)
    {
        return $this->oCache->save($cache, $this->id, $this->group);
    }

    function clear()
    {
        return $this->oCache->remove($this->id, $this->group);
    }

    function setFileNameProtection($value=true)
    {
        $this->oCache->_fileNameProtection = $value;
    }

}

?>