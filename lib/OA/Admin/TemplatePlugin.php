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
$Id: TemplatePlugin.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

if(!defined('SMARTY_DIR')) {
    define('SMARTY_DIR', MAX_PATH . '/lib/smarty/');
}

require_once MAX_PATH . '/lib/smarty/Smarty.class.php';
require_once MAX_PATH . '/lib/OA/Dll.php';
require_once MAX_PATH . '/lib/pear/Date.php';

/**
 * A UI templating class.
 *
 * @package    OpenadsAdmin
 * @author     Monique Szpak <monique.szpak@openads.org>
 */
class OA_Plugin_Template
    extends OA_Admin_Template
{
    /**
     * @var string
     */
    var $templateName;

    /**
     * @var string
     */
    var $cacheId;

    /**
     * @var int
     */
    var $_tabIndex = 0;

    function OA_Plugin_Template($templateName, $adminGroupName)
    {
        $this->init($templateName, $adminGroupName);
    }


    function init($templateName, $adminGroupName)
    {
        parent::init($templateName);

        //since previous version was using relative path and $adminGroupName was
        //ignored (and thus could be incorect and cannot be relied on), for backward compatibility check if absolute path is correct
        //if not use relative one
        $pluginBaseDir = $this->get_template_vars('pluginBaseDir'); //with trailing /
        $pluginTemplateDir = $this->get_template_vars('pluginTemplateDir'); //with trailing /
        
        $absoluteTemplateDir = $pluginBaseDir.$adminGroupName.$pluginTemplateDir;
        
        $this->template_dir = is_dir($absoluteTemplateDir) 
            ? $absoluteTemplateDir : $pluginTemplateDir;
    }
}

?>