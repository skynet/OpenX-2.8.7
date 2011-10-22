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
$Id: ParserBase.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

require_once 'XML/Parser.php';

/**
 * Parses an XML plugin install file
 *
 * @package OpenXPlugin
 * @author  Monique Szpak <monique.szpak@openx.org>
 */
class OX_ParserBase extends XML_Parser
{
    var $aPlugin = array();
    var $aInstall   = array();
    var $aUninstall = array();
    var $aConf      = array();
    var $aSettings  = array();
    var $aPrefs     = array();
    var $aFiles     = array();
    var $aFile      = array();
    var $aSyscheck  = array();
    var $aDbms      = array();
    var $aPhp       = array();
    var $aDepends   = array();

    var $aData      = array();

    var $aAllFiles  = array();

    var $elements = array();
    var $element = '';
    var $count = 0;
    var $error;

    function __construct()
    {
        // force ISO-8859-1 due to different defaults for PHP4 and PHP5
        // todo: this probably needs to be investigated some more andcleaned up
        parent::XML_Parser('ISO-8859-1');
    }

    function OX_ParserBase()
    {
        $this->__construct();
    }

    private function _initArray()
    {
        $this->aSettings  = array();
        $this->aPrefs     = array();
        $this->aFiles     = array();
        $this->aFile      = array();
        $this->aDbms      = array();
        $this->aPhp       = array();
        $this->aDepends   = array();
        $this->aConf      = array(
                              'option'      =>'',
                              'settings'    =>array(),
                              'preferences' =>array(),
                               );
        $this->aSyscheck  = array(
                              'php'         =>array(),
                              'dbms'        =>array(),
                              'depends'     =>array(),
                               );
        $this->aInstall   = array(
                                'conf'       =>array(),
                                'syscheck'   =>array(),
                                'files'      =>array(),
                                'prescript'  =>'',
                                'postscript' =>'',
                                );
        $this->aUninstall   = array(
                                'prescript'  =>'',
                                'postscript' =>'',
                                );
        $this->aPlugin = array(
                             'name'         => '',
                             'creationdate' => '',
                             'author'       => '',
                             'authoremail'  => '',
                             'authorurl'    => '',
                             'license'      => '',
                             'description'  => '',
                             'version'      => '',
                             'oxversion'    => '',
                             'extends'      => '',
                             'install'      => array(),
                             'upgrade'      => array(),
                             'uninstall'    => array(),
                             );
    }

    private function _assignArray()
    {
        $this->aConf['settings']    = $this->aSettings;
        $this->aConf['preferences'] = $this->aPrefs;

        $this->aSyscheck['dbms']    = $this->aDbms;
        $this->aSyscheck['php']     = $this->aPhp;
        $this->aSyscheck['depends'] = $this->aDepends;

        $this->aInstall['conf']     = $this->aConf;
        $this->aInstall['syscheck'] = $this->aSyscheck;
        $this->aInstall['files']    = $this->aFiles;
        $this->aPlugin['install']   = $this->aInstall;
        $this->aPlugin['uninstall'] = $this->aUninstall;
        $this->aPlugin['upgrade']   = @$this->aUpgrade;
        $this->aPlugin['allfiles']  = $this->aAllFiles;
    }

    function startHandler($xp, $element, $attribs)
    {
        $this->elements[$this->count++] = strtolower($element);
        $this->element = implode('-', $this->elements);

        switch ($this->element)
        {
            case 'plugin':
                $this->_initArray();
                break;

            case 'plugin-install-files-file':
                $this->aData = array();
                foreach ($attribs AS $k => $v)
                {
                    $this->aData[strtolower($k)] = $v;
                }
                break;
            case 'plugin-install-syscheck-depends-plugin':
                $this->aData = array();
                foreach ($attribs AS $k => $v)
                {
                    $this->aData[strtolower($k)] = $v;
                }
                break;
            case 'plugin-install-syscheck-dbms':
                $this->aData = array();
                break;
            case 'plugin-install-syscheck-php-setting':
                $this->aData = array();
                foreach ($attribs AS $k => $v)
                {
                    $this->aData[strtolower($k)] = $v;
                }
                break;
        }
    }

    function endHandler($xp, $element)
    {
        switch ($this->element)
        {
            case 'plugin':
                $this->_assignArray();
                break;
            case 'plugin-install-files-file':
                $this->aFiles[] = $this->aData;
                $this->aAllFiles[] = $this->aData;
                break;
            case 'plugin-install-syscheck-depends-plugin':
                $this->aDepends[]  = $this->aData;
                break;
            case 'plugin-install-syscheck-dbms':
                $this->aDbms[]  = $this->aData;
                break;
            case 'plugin-install-syscheck-php-setting':
                $this->aPhp[] = $this->aData;
                break;
            case 'plugin-install-prescript':
                $this->aAllFiles[] = array('name'=>$this->aInstall['prescript'], 'path'=>OX_PLUGIN_GROUPPATH.'/etc/');
                break;
            case 'plugin-install-postscript':
                $this->aAllFiles[] = array('name'=>$this->aInstall['postscript'], 'path'=>OX_PLUGIN_GROUPPATH.'/etc/');
                break;
            case 'plugin-uninstall-prescript':
                $this->aAllFiles[] = array('name'=>$this->aUninstall['prescript'], 'path'=>OX_PLUGIN_GROUPPATH.'/etc/');
                break;
            case 'plugin-uninstall-postscript':
                $this->aAllFiles[] = array('name'=>$this->aUninstall['postscript'], 'path'=>OX_PLUGIN_GROUPPATH.'/etc/');
                break;
        }

        unset($this->elements[--$this->count]);
        $this->element = implode('-', $this->elements);
    }

    function cdataHandler($xp, $data)
    {
        switch ($this->element)
        {
            case 'plugin-install-files-file':
                @$this->aData['name'] .= $data;
                break;
            case 'plugin-install-syscheck-depends-plugin':
                @$this->aData['name'] .= $data;
                break;
            case 'plugin-install-syscheck-php-setting':
                @$this->aData['value'] .= $data;
                break;
            case 'plugin-install-syscheck-dbms-name':
                @$this->aData['name'] .= $data;
                break;
            case 'plugin-install-syscheck-dbms-supported':
                @$this->aData['supported'] .= $data;
                break;
            case 'plugin-install-syscheck-dbms-version':
                @$this->aData['version'] .= $data;
                break;
            case 'plugin-install-syscheck-dbms-engine':
                @$this->aData['engine'][] .= $data;
                break;
            case 'plugin-install-syscheck-dbms-grant':
                @$this->aData['grant'][] .= $data;
                break;
            case 'plugin-install-prescript':
                @$this->aInstall['prescript'] .= $data;
                break;
            case 'plugin-install-postscript':
                @$this->aInstall['postscript'] .= $data;
                break;
            case 'plugin-uninstall-prescript':
                @$this->aUninstall['prescript'] .= $data;
                break;
            case 'plugin-uninstall-postscript':
                @$this->aUninstall['postscript'] .= $data;
                break;
            case 'plugin-version':
                // Fix potential issues with lowercase RC's
                @$this->aPlugin['version'] = preg_replace('/rc([0-9]+)$/', 'RC$1', $data);
                break;
            case 'plugin-name':
            case 'plugin-creationdate':
            case 'plugin-author':
            case 'plugin-authoremail':
            case 'plugin-authorurl':
            case 'plugin-license':
            case 'plugin-oxversion':
            case 'plugin-extends':
            case 'plugin-description':
                @$this->aPlugin[str_replace('plugin-','',$this->element)] .= $data;
                break;
        }
    }

    function raiseError($msg = null, $xmlecode = 0, $xp = null, $ecode = OX_PLUGIN_ERROR_PARSE)
    {
		if (is_null($this->error))
        {
            $error = '';
            if (is_resource($msg))
            {
                $error.= 'Parser error: '.xml_error_string(xml_get_error_code($msg));
                $xp = $msg;
            }

            else
            {
                $error.= 'Parser error: '.$msg;
                if (!is_resource($xp)) {
                    $xp = $this->parser;
                }
            }
            if (is_resource($xp))
            {
                $byte = @xml_get_current_byte_index($xp);
                $line = @xml_get_current_line_number($xp);
                $column = @xml_get_current_column_number($xp);
                $error.= " - Byte: $byte; Line: $line; Col: $column";
            }
            $error.= "\n";
            $mode = 0;
            $options = 0;
            $userinfo = '';

            $this->error = PEAR::raiseError($error, $code, $mode, $options, $userinfo);
			return $this->error;
        }
        return $this->error;
    }
}

?>
