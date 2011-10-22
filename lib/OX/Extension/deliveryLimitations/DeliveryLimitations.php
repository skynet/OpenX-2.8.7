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
$Id: DeliveryLimitations.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

require_once MAX_PATH . '/lib/max/Plugin/Common.php';
require_once MAX_PATH . '/lib/max/Plugin/Translation.php';
require_once MAX_PATH . '/lib/max/Delivery/limitations.delivery.php';
require_once MAX_PATH . '/lib/OA/Maintenance/Priority/DeliveryLimitation/Empty.php';
require_once LIB_PATH . '/Plugin/Component.php';

/**
 * Plugins_DeliveryLimitations is an abstract class for every Delivery limitation plugin.  Note that
 * subclasses of this plugin need to build an 'evaluation' function, which is named of the form:
 * 'MAX_check{Type}_{Plugin}.
 *
 * {Type} is the group which the plugin belongs to (e.g. Time, Geo, etc.), and is also the subfolder
 * under 'deliveryLimitations' where the plugin lives.
 *
 * {Plugin} is the actual name of the plugin.  The plugin file name is also called {Plugin}.plugin.php.
 *
 * @abstract
 * @package    OpenXPlugin
 * @subpackage DeliveryLimitations
 * @author     Chris Nutting <chris@m3.net>
 */
class Plugins_DeliveryLimitations extends OX_Component
{

    var $ad_id;
    var $logical;
    var $type;
    var $comparison;
    var $data = '';
    var $executionorder;
    var $count;
    var $res;
    var $columnName = '';
    var $nameEnglish = '';
    var $defaultComparison = '==';

    /**
     * An array list of operations available for this type of plugin.
     *
     * @var array
     */
    var $aOperations;

    function Plugins_DeliveryLimitations()
    {
        $this->aOperations = MAX_limitationsGetAOperationsForString($this);
    }

    /**
     * Initialise this plugin
     */
    function init($data)
    {
        if (!is_null($data)) {
            foreach ($data as $name => $value) {
                $this->$name = $value;
            }
        }
        $this->displayName = $this->getName();
        $this->res = $this->_getRes();
    }

    function _getRes()
    {
        $file = MAX_PATH.$GLOBALS['_MAX']['CONF']['pluginPaths']['plugins']."/deliveryLimitations/{$this->group}/{$this->component}.res.inc.php";
        if (is_readable($file)) {
            // Use include here, not require_once, so that the $res array will be created every time,
            // even if the plugin is initialised more than once
            include $file;
            return $res;
        }
        return array();
    }

    /**
     * Returns the localized name of the plugin. The method is based
     * on the data stored in nameEnglish member variable.
     *
     * @return string Localized name of the plugin.
     */
    function getName()
    {
        return MAX_Plugin_Translation::translate(
            $this->nameEnglish, $this->extension, $this->group);
    }

    /**
     * Evaluates whether the person or agency can use this plugin (e.g. if they have the right permissions)
     *
     * @abstract
     * @return boolean
     */
    function isAllowed($page = false)
    {
        return true;
    }

    /**
     * Method to check input data
     *
     * @param array $data Most important to check is $data['data'] field
     * @return bool|string true or error message
     */
    function checkInputData($data)
    {
//        if (!($data['data'] && !is_array($data['data']) && trim($data['data'] != ''))) {
//            return MAX_Plugin_Translation::translate($this->group.' - '.$this->getName().': Please provide a non-empty limitation parameters', $this->extension, $this->group);
//        }
        return true;
    }

    /**
     * Echos the HTML to display this limitation
     *
     * @return void
     */
    function display()
    {
        global $tabindex;
        if ($this->executionorder > 0) {
            echo "<tr><td colspan='4'><img src='" . OX::assetPath() . "/images/break-el.gif' width='100%' height='1'></td></tr>";
        }

        $bgcolor = $this->executionorder % 2 == 0 ? "#E6E6E6" : "#FFFFFF";

        echo "<tr height='35' bgcolor='$bgcolor'>";
        echo "<td width='100'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
        if ($this->executionorder == 0) {
            echo "<input type='hidden' name='acl[{$this->executionorder}][logical]' value='and'>&nbsp;";
        } else {
            echo "<select name='acl[{$this->executionorder}][logical]' tabindex='".($tabindex++)."'>";
            echo "<option value='or' " . (($this->logical == 'or') ? 'selected' : '') . ">{$GLOBALS['strOR']}</option>";
            echo "<option value='and' " . (($this->logical == 'and') ? 'selected' : '') . ">{$GLOBALS['strAND']}</option>";
            echo "</select>";
        }
        echo "</td><td width='130'>";
		echo "<table cellpadding='2'><tr><td><img src='" . OX::assetPath() . "/images/icon-acl.gif' align='absmiddle'>&nbsp;</td><td>{$this->displayName}</td></tr></table>";
		echo "<input type='hidden' name='acl[{$this->executionorder}][type]' value='{$this->type}'>";
		echo "<input type='hidden' name='acl[{$this->executionorder}][executionorder]' value='{$this->executionorder}'>";
		echo "</td><td >";

        $this->displayComparison();

        echo "</td>";
        // Show buttons
		echo "<td align='{$GLOBALS['phpAds_TextAlignRight']}'>";
		echo "<input type='image' name='action[del][{$this->executionorder}]' value='{$this->executionorder}' src='" . OX::assetPath() . "/images/icon-recycle.gif' border='0' align='absmiddle' alt='{$GLOBALS['strDelete']}'>";
		echo "&nbsp;&nbsp;";
		echo "<img src='" . OX::assetPath() . "/images/break-el.gif' width='1' height='35'>";
		echo "&nbsp;&nbsp;";

		if ($this->executionorder && $this->executionorder < $this->count)
			echo "<input type='image' name='action[up][{$this->executionorder}]' src='" . OX::assetPath() . "/images/triangle-u.gif' border='0' alt='{$GLOBALS['strUp']}' align='absmiddle'>";
		else
			echo "<img src='" . OX::assetPath() . "/images/triangle-u-d.gif' alt='{$GLOBALS['strUp']}' align='absmiddle'>";

		if ($this->executionorder < $this->count - 1) {
			echo "<input type='image' name='action[down][{$this->executionorder}]' src='" . OX::assetPath() . "/images/triangle-d.gif' border='0' alt='{$GLOBALS['strDown']}' align='absmiddle'>";
		} else {
			echo "<img src='" . OX::assetPath() . "/images/triangle-d-d.gif' alt='{$GLOBALS['strDown']}' align='absmiddle'>";
		}

		echo "&nbsp;&nbsp;</td></tr>";
		echo "<tr bgcolor='$bgcolor'><td>&nbsp;</td><td>&nbsp;</td><td colspan='2'>";

        $this->displayData();
        echo "<br /><br /></td></tr>";

        //if (!isset($acl[$key]['type']) || $acl[$key]['type'] != $previous_type && $previous_type != '')
        //echo "<tr><td height='1' colspan='4' bgcolor='#888888'><img src='" . OX::assetPath() . "/images/break.gif' height='1' width='100%'></td></tr>";

    }

    /**
     * Echos the HTML to display the comparison operator for this limitation
     *
     * @return void
     */
    function displayComparison()
    {
        global $tabindex;
        echo "<select name='acl[{$this->executionorder}][comparison]' tabindex='".($tabindex++)."'>";
        foreach($this->aOperations as $sOperator => $sDescription) {
            echo "<option value='$sOperator' " . (($this->comparison == $sOperator) ? 'selected' : '') . ">$sDescription</option>";
        }
		echo "</select>";
    }

    /**
     * Echos the HTML to display the data on the 'delivery limitations' screen for this limitation
     *
     * @return void
     */
    function displayData()
    {
    	global $tabindex;
        echo "<input type='text' size='40' name='acl[{$this->executionorder}][data]' value=\"".htmlspecialchars(isset($this->data) ? $this->data : "")."\" tabindex='".($tabindex++)."'>";
    }

    /**
     * A private method to "flatten" a delivery limitation into the string format that is
     * saved to the database (either in the acls, acls_channel or banners table, when part
     * of a compiled limitation string).
     *
     * By default, simply returns the data exactly as is, as the default plugin data format
     * is a string itself.
     *
     * Should be overridden in any delivery limitation plugin where data is input from or
     * displayed in the UI in anything other than string format, or where the string data
     * needs to be interpreted in any way to be usable in the delivery limitation.
     *
     * @access private
     * @param mixed $data An optional, expanded form delivery limitation.
     * @return string The delivery limitation in flattened format.
     */
    function _flattenData($data = null)
    {
        if (is_null($data)) {
            $data = $this->data;
        }
        return $data;
    }

    /**
     * A private method to "expand" a delivery limitation from the string format that
     * is saved in the database (ie. in the acls or acls_channel table) into its
     * "expanded" form.
     *
     * By default, simply returns the data exactly as is, as the default plugin data format
     * is a string itself.
     *
     * Should be overridden in any delivery limitation plugin where data is input from or
     * displayed in the UI in anything other than string format, or where the string data
     * needs to be interpreted in any way to be useable in the delivery limitation.
     *
     * @access private
     * @param string $data An optional, flat form delivery limitation data string.
     * @return mixed The delivery limitation data in expanded format.
     */
    function _expandData($data = null)
    {
        if (is_null($data)) {
            $data = $this->data;
        }
        return $data;
    }

    /**
     * A private method to pre-compile limitaions.
     *
     * Will generally be overridden by delivery limitations with special requirements (eg. converting
     * the data stored in the database for the limitation from "as entered" data into lowercase, etc.).
     *
     * @access private
     * @param string $sData An internal representation of the limitation data
     *                     for this plugin as a string.
     * @return string The delivery limitation in pre-compiled form, with any changes to the format
     *                of the data stored in the database having been made, ready to be used for
     *                either compiling the limitation into final form, or converting the limitation
     *                into SQL form.
     */
    function _preCompile($sData) {
        return MAX_limitationsGetPreprocessedString($sData);
    }

    /**
     * Returns the compiledlimitation string for this limitation.  The compiledlimitation variable is
     * then used for functions which need to evaluate whether the banner can be shown without the need
     * to loop through every single limitation.
     *
     * @return string The delivery limitation in compiled form.
     */
    function compile()
    {
        return $this->compileData($this->data);
    }

    /**
     * The same as compile(), but works on arbitrary data.
     *
     * @param string $data
     */
    function compileData($data)
    {
        $result = 'MAX_check' . ucfirst($this->group) . '_' . $this->component . "('{$data}', '{$this->comparison}')";
        return MAX_limitationsGetQuotedString($result);
    }

    /**
     * Returns a data in its basic form, so it can be saved to the database.
     *
     * @return string The data.
     */
    function getData()
    {
        return $this->data;
    }

    /**
     * A method to return an instance to be used by the MPE
     *
     * @param unknown_type $aDeliveryLimitation
     */
    function getMpeClassInstance($aDeliveryLimitation)
    {
        return new OA_Maintenance_Priority_DeliveryLimitation_Empty($aDeliveryLimitation);
    }
}

?>
