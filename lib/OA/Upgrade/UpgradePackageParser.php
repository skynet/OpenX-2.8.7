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
$Id: UpgradePackageParser.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

require_once 'XML/Parser.php';

/**
 * OpenX Schema Management Utility.
 *
 * Parses an XML schema file
 *
 * @author     Monique Szpak <monique.szpak@openx.org>
 * @package OpenX
 * @category Upgrade
 */
class OA_UpgradePackageParser extends XML_Parser
{
    var $aPackage       = array('db_pkgs' => array(), 'product'=>'oa');
    var $DBPkg_version  = '';
    var $DBPkg_stamp    = '';
    var $DBPkg_schema   = '';
    var $DBPkg_prescript = '';
    var $DBPkg_postscript = '';
    var $aDBPkgs        = array('files'=>array());
    var $aSchemas       = array();
    var $aFiles         = array();

    var $elements   = array();
    var $element    = '';
    var $count      = 0;
    var $error;

//    function __construct()
//    {
//        // force ISO-8859-1 due to different defaults for PHP4 and PHP5
//        // todo: this probably needs to be investigated some more andcleaned up
//        parent::XML_Parser('ISO-8859-1');
//    }

    function OA_UpgradePackageParser()
    {
        parent::XML_Parser('ISO-8859-1');
        //$this->__construct();
    }

    function startHandler($xp, $element, $attribs)
    {
        $this->elements[$this->count++] = strtolower($element);
        $this->element = implode('-', $this->elements);

        switch ($this->element) {
        case 'upgrade-database-package':
            $this->DBPkg_version = '';
            $this->DBPkg_stamp = '';
            $this->DBPkg_schema = '';
            $this->DBPkg_prescript = '';
            $this->DBPkg_postscript = '';
            $this->aDBPkgs = array();
            $this->aDBPkgList = array();
//            $this->aFiles = array();
//            $this->aPackage = array();
//            $this->aSchemas = array();
//            $this->aFiles = array();
            break;
          default:
            break;
        }
    }

    function endHandler($xp, $element)
    {
        switch ($this->element) {

        case 'upgrade-database-package':
            $this->aPackage['db_pkgs'][] = array(
                                                 'version' => $this->DBPkg_version,
                                                 'stamp' => $this->DBPkg_stamp,
                                                 'schema' => $this->DBPkg_schema,
                                                 'prescript' => $this->DBPkg_prescript,
                                                 'postscript' => $this->DBPkg_postscript,
                                                 'files'=>$this->aDBPkgs
                                                 );
            break;
        case 'upgrade-database':
            $this->aPackage['db_pkg_list'][$this->DBPkg_schema] = $this->aSchemas;
            break;
        }
        unset($this->elements[--$this->count]);
        $this->element = implode('-', $this->elements);
    }

    function &raiseError($msg = null, $xmlecode = 0, $xp = null, $ecode = -1)
    {
        if (is_null($this->error)) {
            $error = '';
            if (is_resource($msg)) {
                $error.= 'Parser error: '.xml_error_string(xml_get_error_code($msg));
                $xp = $msg;
            } else {
                $error.= 'Parser error: '.$msg;
                if (!is_resource($xp)) {
                    $xp = $this->parser;
                }
            }
            if ($error_string = xml_error_string($xmlecode)) {
                $error.= ' - '.$error_string;
            }
            if (is_resource($xp)) {
                $byte = @xml_get_current_byte_index($xp);
                $line = @xml_get_current_line_number($xp);
                $column = @xml_get_current_column_number($xp);
                $error.= " - Byte: $byte; Line: $line; Col: $column";
            }
            $error.= "\n";
            $this->error =& PEAR::raiseError($ecode, null, null, $error);
        }
        return $this->error;
    }

    function cdataHandler($xp, $data)
    {

        switch ($this->element)
        {
            case 'upgrade-database-package':
                $this->DBPkg_name = $data;
                break;
            case 'upgrade-database-package-file':
                $this->aDBPkgs[] = $data;
                break;
            case 'upgrade-database-package-prescript':
                $this->DBPkg_prescript = $data;
                break;
            case 'upgrade-database-package-postscript':
                $this->DBPkg_postscript = $data;
                break;
            case 'upgrade-database-package-version':
                $this->DBPkg_version = $data;
                if ($data)
                {
                    $this->aSchemas[] = $this->DBPkg_version;
                }
                break;
            case 'upgrade-database-package-stamp':
                $this->DBPkg_stamp = $data;
                break;
            case 'upgrade-database-package-schema':
                $this->DBPkg_schema = $data;
                break;
            case 'upgrade-name':
                $this->aPackage['name'] = $data;
                break;
            case 'upgrade-type':
                if ($data=='plugin')
                {
                    $this->aPackage['product'] = $this->aPackage['name'];
                }
                break;
            case 'upgrade-creationdate':
                $this->aPackage['creationDate'] = $data;
                break;
            case 'upgrade-author':
                $this->aPackage['author'] = $data;
                break;
            case 'upgrade-authoremail':
                $this->aPackage['authorEmail'] = $data;
                break;
            case 'upgrade-authorurl':
                $this->aPackage['authorUrl'] = $data;
                break;
            case 'upgrade-license':
                $this->aPackage['license'] = $data;
                break;
            case 'upgrade-description':
                $this->aPackage['description'] = $data;
                break;
            case 'upgrade-versionfrom':
                $this->aPackage['versionFrom'] = $data;
                break;
            case 'upgrade-versionto':
                $this->aPackage['versionTo'] = $data;
                break;
            case 'upgrade-prescript':
                $this->aPackage['prescript'] = $data;
                break;
            case 'upgrade-postscript':
                $this->aPackage['postscript'] = $data;
                break;
        }
    }

}

?>
