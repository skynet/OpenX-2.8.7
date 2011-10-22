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
$Id: Translation.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

require_once MAX_PATH . '/lib/max/Admin/Languages.php';

/**
 * MAX_Plugin_Translation - plugin translation system.
 *
 * @package    OpenXPlugin
 * @author     Radek Maciaszek <radek@m3.net>
 */
class MAX_Plugin_Translation
{

    /**
     * Include plugin translation file. The method is trying to include
     * plugin translation file first for language saved in preferences and
     * if this operation failed for language from global config
     * This method could be called automatically by translate() method
     * lazy initialization
     *
     * @access public
     * @static
     * @param string $module   Module name
     * @param string $package  Package name
     * @see translate()
     * @todo TODOLANG The CONF fallback below will fail
     *
     * @return boolean         True on success else false
     *
     */
    function init($module, $package = null) {
        if (isset($GLOBALS['_MAX']['PLUGIN_TRANSLATION'][$module][$package])) {
            // Already included
            return true;
        }
        if (!empty($GLOBALS['_MAX']['PREF']['language'])) {
            $language = $GLOBALS['_MAX']['PREF']['language'];
        } elseif (!empty($GLOBALS['_MAX']['CONF']['max']['language'])) {
            $language = $GLOBALS['_MAX']['CONF']['max']['language'];
        } else {
            $language = 'en';
        }

        if (MAX_Plugin_Translation::includePluginLanguageFile($module, $package, $language)) {
            return true;
        } else {
            // Check extension path for language files (temporary added as new translation system isn't ready yet)
            if ($package === null) {
                $path = OX_EXTENSIONS_PATH . '/' . $module . '/_lang/';
            } else {
                $path = OX_EXTENSIONS_PATH . '/' . $module . '/' . $package . '/_lang/';
            }
            if (MAX_Plugin_Translation::includePluginLanguageFile($module, $package, $language, $path)) {
                return true;
            }
        }
    }

    /**
     * Include plugin (package) language file and assign the translation
     * to global plugin translation variable
     *
     * @access public
     * @static
     * @param string $module    Module name
     * @param string $package   Package name
     * @param string $language  Language
     * @param string $path      We could also pass the language path (used mainly for testing)
     *
     * @return boolean  True if file and translation exists else false
     *
     */
    function includePluginLanguageFile($module, $package, $language, $path = null)
    {
        // Required for lazy initialization
        if ($package === null && !isset($GLOBALS['_MAX']['PLUGIN_TRANSLATION'][$module])) {
            $GLOBALS['_MAX']['PLUGIN_TRANSLATION'][$module] = array();
        } else if (!isset($GLOBALS['_MAX']['PLUGIN_TRANSLATION'][$module][$package])) {
            $GLOBALS['_MAX']['PLUGIN_TRANSLATION'][$module][$package] = array();
        }

        if ($path === null) {
            if ($package === null) {
                $path = MAX_PATH . '/plugins/' . $module . '/_lang/';
            } else {
                $path = MAX_PATH . '/plugins/' . $module . '/' . $package . '/_lang/';
            }
        }
        // Load up the english translation if available
        if (is_readable($path . 'en.php')) {
            include $path . 'en.php';
            //  If current module is not the default openads module
            if (isset($words)) {
                if ($package === null) {
                    $GLOBALS['_MAX']['PLUGIN_TRANSLATION'][$module] = $words;
                } else {
                    $GLOBALS['_MAX']['PLUGIN_TRANSLATION'][$module][$package] = $words;
                }
            }
            unset($words);
        }

        if (is_readable($path . $language . '.php')) {
            include $path . $language . '.php';
            //  If current module is not the default openads module
            if (isset($words)) {
                if ($package === null) {
                    $GLOBALS['_MAX']['PLUGIN_TRANSLATION'][$module] = array_merge($GLOBALS['_MAX']['PLUGIN_TRANSLATION'][$module], $words);
                } else {
                    $GLOBALS['_MAX']['PLUGIN_TRANSLATION'][$module][$package] = array_merge($GLOBALS['_MAX']['PLUGIN_TRANSLATION'][$module][$package], $words);
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Translates source text into target language.
     *
     * @access  public
     * @static
     * @param   string  $key Translation term
     * @param   string  $extension extension name
     * @param   string  $group group name
     *
     * @return  string translated text
     */
    function translate($key, $extension, $group = null)
    {
        MAX_Plugin_Translation::lazyInit($extension, $group);

        // First try and get a translation from the specific group...
        if (isset($GLOBALS['_MAX']['PLUGIN_TRANSLATION'][$extension][$group][$key])) {
            return $GLOBALS['_MAX']['PLUGIN_TRANSLATION'][$extension][$group][$key];
        // If there is no specific translation fall back to the extension...
        // Check it is not an array in case the key is the same as the group name.
        } elseif (isset($GLOBALS['_MAX']['PLUGIN_TRANSLATION'][$extension][$key])
            && !is_array($GLOBALS['_MAX']['PLUGIN_TRANSLATION'][$extension][$key])) {
            return $GLOBALS['_MAX']['PLUGIN_TRANSLATION'][$extension][$key];
        // If all else fails, give up and return the un-translated string
        } else {
            return $key;
        }
    }

    /**
     * Initialize translation files
     *
     * @param string $module
     * @param string $package
     */
    function lazyInit($module, $package)
    {
        if (!isset($GLOBALS['_MAX']['PLUGIN_TRANSLATION'][$module])) {
            MAX_Plugin_Translation::init($module, $package);
        }
        if (!isset($GLOBALS['_MAX']['PLUGIN_TRANSLATION'][$module][$package])) {
            MAX_Plugin_Translation::init($module, $package);
        }
    }

    /**
     * This method register all strings in global scope $GLOBALS so
     * all string can be used with general templates method
     * OA_Admin_Template->_function_t
     *
     * Warning: as this method register all translation string in global scope
     * (as global variables) consider this a hack.
     * However as we do not have any other global translation solution
     * yet it is the only possibility for now.
     *
     * @param string $module
     * @param string $package
     */
    function registerInGlobalScope($module, $package)
    {
        MAX_Plugin_Translation::lazyInit($module, $package);
        if (isset($GLOBALS['_MAX']['PLUGIN_TRANSLATION'][$module][$package])) {
            foreach ($GLOBALS['_MAX']['PLUGIN_TRANSLATION'][$module][$package] as
                $key => $translation) {
                    if (is_string($key)) {
                        $GLOBALS['str'.$key] = $translation;
                    }
            }
        }
        if (isset($GLOBALS['_MAX']['PLUGIN_TRANSLATION'][$module])) {
            foreach ($GLOBALS['_MAX']['PLUGIN_TRANSLATION'][$module][$package] as
                $key => $translation) {
                    if (is_string($key)) {
                        $GLOBALS['str'.$key] = $translation;
                    }
            }
        }
    }
}

?>
