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
$Id: Template.php 16124 2008-02-11 18:16:06Z andrew.hill@openads.org $
*/

require_once MAX_PATH . '/lib/OA/Preferences.php';
require_once 'Zend/Translate.php';

/**
 * This class provides a translation mechanism which can be used throughout
 * the application, the translation memories are loaded from the application
 *
 * @todo This is just wrapping the old GLOBALS array. Need to plug in a proper i18n library.
 */
class OX_Translation
{
    /**
     * Boolean class property to control if the returned string should have HTML special characters escaped.
     *
     * @var boolean $htmlSpecialChars
     */
    var $htmlSpecialChars = false;

    /**
     * The output language to translate strings into
     *
     * @var string The language code for the selected language
     */
    var $locale = 'en_US';

    var $zTrans = false;

    var $debug = false;

    /**
     * Constructor class
     *
     * @param string $transPath The (optional) path to look for .mo translation resources
     * @return OX_Translation
     */
    function OX_Translation($transPath = null)
    {
        if (isset($GLOBALS['_MAX']['PREF']['language'])) {
            $this->locale = $GLOBALS['_MAX']['PREF']['language'];
        }

        if (!is_null($transPath)) {
            $transFile = MAX_PATH . $transPath . '/' . $this->locale . '.mo';
            if (@is_readable($transFile)) {
                $this->zTrans = new Zend_Translate('gettext', $transFile, $this->locale);
            } elseif (@is_readable(MAX_PATH . $transPath . '/en.mo')) {
                $this->zTrans = new Zend_Translate('gettext', MAX_PATH . $transPath . '/en.mo', 'en');
            }
        }
    }

    /**
     * This method looks up a translation string from the available translation memories
     * It will grow to include wrappers to _gettext or any other translation system that
     * we decide to employ
     *
     * @param string $sString The string (or code-key) to be translated
     * @param array $aValues An array of values to be substituted in the translated string (via sprintf)
     * @param mixed $pluralVar Type of variable controls action:
     *              boolean: Simple true/false control of whether the string should be in pluralized form
     *              string/int: Key of the plural var(s) in the $aValues array
     *              array: Array of string/int keys in the $aValues array
     *
     * @return string The translated string
     */
    function translate($string, $aValues = array(), $pluralVar = false)
    {
        if ($this->zTrans) {
            $return = $this->zTrans->_($string);
        } elseif (!empty($GLOBALS['str' . $string])) {
            $return = $GLOBALS['str' . $string];
        } else {
            $return = $string;
        }

        // If substitution variables have been provided
        if (!empty($aValues)) {
            $return = vsprintf($return, $aValues);
        }
        $return = ($this->htmlSpecialChars) ? htmlspecialchars($return) : $return;

        // For debugging add strike tags
        if ($this->debug) {
            $return = '<strike>' . $return . '</strike>';
        }

        return $return;
    }
}

?>
