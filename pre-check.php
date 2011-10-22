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
$Id: pre-check.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

/**
 * @package    OpenX
 * @author     Łukasz Wikierski <lukasz.wikierski@openx.org>
 *
 * A pre-initialisation file to check if system settings allow OpenX
 * to be run or not.
 */

require_once 'memory.php';

/**
 * A function to check system settings and display detected problems
 */
function OX_initialSystemCheck(){
    $errorUrl = 'http://www.openx.org/help/2.8/pre-init-error/';
    $installed = OX_checkSystemInstalled();
    $aErrors = array();
    $erorCode = OX_checkSystemInitialRequirements($aErrors);
    if ($erorCode !== true) {
        $imageRelativePath = "./www/admin/precheck/";
        // Do functions strpos & parse_url exist? If so, try to
        // guess the proper relative path...
        if ($erorCode != -2) {
            // Checking if URL include www or admin in path
            if (strpos($_SERVER['REQUEST_URI'], '/www/admin/') !== false) {
                $imageRelativePath = "./precheck/";
            } else if (strpos($_SERVER['REQUEST_URI'], '/www/') !== false) {
                $imageRelativePath = "./admin/precheck/";
            }
        }
        // We always trying show images in CSS
        $bodyBackground = "url('{$imageRelativePath}body_piksel.gif') repeat-x";
        $liBackground = "background: url('{$imageRelativePath}list_element.gif') no-repeat;";
        $openXLogo = "background: url('{$imageRelativePath}openx_logo.gif') no-repeat;";

        $message = '
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
            "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
            <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
            <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <meta name="Keywords" content="" />
            <meta name="Description" content="" />
            <title>php Error page</title>
<style type="text/css">
body {
    margin: 0;
    background: #fff '. $bodyBackground .';
    font-family: Arial, Helvetica, sans-serif;
    font: 12px Arial;
    color: #747474;
}
h1 {
    width:80%;
    font: 26px Arial;
    color:#000;
}
h2 {
    width:80%;
    font:12px Arial;
    color:#747474;
    margin-top:20px;
}
.error_container {
    margin-top: 80px;
    margin-left: 93px;
}
.error_list {
    width: 80%;
    padding-left: 33px;
    padding-top: 15px;
    padding-bottom:15px;
    line-height:18px;
    border-top: 1px solid #eee;
    border-bottom: 1px solid #eee;
}
ul {
    list-style-type: none;
    list-style-position: outside;
    padding:0px;
    padding-top:10px;
    margin:0px;
    margin-left:15px;

}
li {
    '.$liBackground.'
    padding-left:10px;
}
.help_link:active, .help_link:link, .help_link:visited  {
    color: #0767a8;
    text-decoration:none;
}
.help_link:hover {
    color: #0767a8;
    text-decoration:underline;
}
.logo_image {
    '.$openXLogo.'
    width:  173px;
    height: 60px;
}

</style>
            </head>
            <body>
                <div class="logo_image">&nbsp;</div>
                    <div class="error_container">
        ';
        if ($installed) {
            $message .= "
                <h1>Sorry, but OpenX cannot currently run on your machine</h1>
            ";
        } else {
            $message .= "
                <h1>Sorry, but the OpenX installer system cannot currently be started</h1>
            ";
        }
        $message .= '
            <div class="error_list">
            Detected problem';
        if (count($aErrors) > 1) {
            $message .= "s";
        }
        $message .= ":
            <ul>
        ";
        echo $message;
        foreach ($aErrors as $errorMessage) {
            echo "<li>{$errorMessage}</li>";
        }
        $message = '
            </ul>
            </div>
            <h2>Please see our <a href="'.$errorUrl.'" class="help_link" >documentation</a> for help
            with the above error';
        if (count($aErrors) > 1) {
            $message .= "s";
        }
        $message .= ".
            </h2>
            </div>
            </body>
            </html>
        ";
        echo $message;
        // Terminate execution
        exit;
    }
}

/**
 * Check (roughly) to see if the system is installed without
 * any of the usual code to check this, as running in pre-init
 * mode.
 *
 * @return bool True if the system seems to be installed, false
 *              otherwise.
 */
function OX_checkSystemInstalled()
{
    $path = @dirname(__FILE__);
    if (!@empty($path)) {
        if (@file_exists($path . '/var/UPGRADE')) {
            return false;
        } else {
            return true;
        }
    }
    return false;
}

/**
 * Check for situation when installing or using software is eithe
 * imposible, or results in unformated error output, due to system
 * configuration.
 *
 * @param &$aErrors Array of error mesages. All errors that it is possible to
 *                  detect will be set, regardless of the function return value.
 * @return bool|int True on system check OK, negative int value on detected problems.
 *                         -1 => The "function_exists" built-in function doesn't exist
 *                         -2 => At least one of the "strpos" or "parse_url" built-in
 *                               functions don't exist
 *                         -3 => One of the other required built-in functions was
 *                               detected as being disabled
 *                         -4 => The amount of memory required was too low
 *
 */
function OX_checkSystemInitialRequirements(&$aErrors){

    // Variables for tracking if the test has passed or not,
    // and if not, what value to return
    $isSystemOK = true;
    $return = true;

    // The general list of built in PHP functions that are required to
    // run OpenX, apart from the functions:
    //
    //   - "function_exists"
    //   - "array_intersect"
    //   - "explode"
    //   - "ini_get"
    //   - "trim"
    //   - "parse_url"
    //   - "strpos"
    //
    // These other functions are tested separately, as they are
    // required to test for the existence of the functions in the
    // array below!
    $aRequiredFunctions = array(
        'dirname',
        'empty',
        'file_exists',
        'ini_set',
        'parse_ini_file',
        'version_compare'
    );

    // Prepare error strings, in the simplest possible way
    $errorString1 = 'The built in PHP function "';
    $errorString2 = '" is in the "disable_functions" list in your "php.ini" file.';

    // Need "function_exists" to be able to test for functions required
    // for testing what is in the "disabled_functions" list
    if (!function_exists('function_exists')) {
        $aErrors[] = $errorString1 . 'function_exists' . $errorString2;
        // Cannot detect any more errors, as function_exists is
        // needed to detect the required functions!
        return -1;
    }

    // Test for existence of "parse_url" and "strpos", which are
    // special cases required for the display of the error message
    // in the event of anything failing in this test!
    if (!function_exists('parse_url')) {
        $aErrors[] = $errorString1 . 'parse_url' . $errorString2;
        $isSystemOK = false;
        if ($return === true) {
            $return = -2;
        }
    }
    if (!function_exists('strpos')) {
        $aErrors[] = $errorString1 . 'strpos' . $errorString2;
        $isSystemOK = false;
        if ($return === true) {
            $return = -2;
        }
    }

    // Test for existence of "array_intersect", "explode", "ini_get"
    // and "trim", which are all required as part of the code to test
    // which functions are in the "disabled_functions" list below...
    if (!function_exists('array_intersect')) {
        $aErrors[] = $errorString1 . 'array_intersect' . $errorString2;
        $isSystemOK = false;
        if ($return === true) {
            $return = -3;
        }
    }
    if (!function_exists('explode')) {
        $aErrors[] = $errorString1 . 'explode' . $errorString2;
        $isSystemOK = false;
        if ($return === true) {
            $return = -3;
        }
    }
    if (!function_exists('ini_get')) {
        $aErrors[] = $errorString1 . 'ini_get' . $errorString2;
        $isSystemOK = false;
        if ($return === true) {
            $return = -3;
        }
    }
    if (!function_exists('trim')) {
        $aErrors[] = $errorString1 . 'trim' . $errorString2;
        $isSystemOK = false;
        if ($return === true) {
            $return = -3;
        }
    }

    // Test the disabled functons list with required functions list
    // defined above in $aRequiredFunctions
    $aDisabledFunctions = explode(',', ini_get('disable_functions'));
    foreach ($aDisabledFunctions as $key => $value) {
        $aDisabledFunctions[$key] = trim($value);
    }
    $aNeededFunctions = array_intersect($aDisabledFunctions, $aRequiredFunctions);
    if (count($aNeededFunctions) > 0) {
        $isSystemOK = false;
        foreach ($aNeededFunctions as $functionName) {
            $aErrors[] = $errorString1 . $functionName . $errorString2;
        }
    }

    // Check PHP version, as use of PHP < 5.1.4 will result in parse errors
    $errorMessage = "PHP version 5.1.4, or greater, was not detected.";
    if (function_exists('version_compare')) {
        $result = version_compare(phpversion(), '5.1.4', '<');
        if ($result) {
            $aErrors[] = $errorMessage;
            $isSystemOK = false;
            if ($return === true) {
                $return = -3;
            }
        }
    }

    // Check minimum memory requirements are okay (24MB)
    $minimumRequiredMemory = OX_getMinimumRequiredMemory();
    $phpMemoryLimit = OX_getMemoryLimitSizeInBytes();
    if ($phpMemoryLimit > 0 && $phpMemoryLimit < $minimumRequiredMemory) {
        // The memory limit is too low, but can it be increased?
        $memoryCanBeSet = OX_checkMemoryCanBeSet();
        if (!$memoryCanBeSet) {
            $minimumRequiredMemoryInMB = $minimumRequiredMemory / 1048576;
            $errorMessage = 'The PHP "memory_limit" value is set to less than the required minimum of ' .
                            $minimumRequiredMemoryInMB . 'MB, but because the built in PHP function "ini_set" ' .
                            'has been disabled, the memory limit cannot be automatically increased.';
            $aErrors[] = $errorMessage;
            $isSystemOK = false;
            if ($return === true) {
                $return = -4;
            }
        }
    }
    
    // Check magic_quotes_runtime and try to unset it
    $GLOBALS['original_get_magic_quotes_runtime'] = OX_getMagicQuotesRuntime();
    if ($GLOBALS['original_get_magic_quotes_runtime']) {
        ini_set('magic_quotes_runtime', 0);
        if (OX_getMagicQuotesRuntime()) {
            // try deprecated set_magic_quotes_runtime
            if (function_exists('set_magic_quotes_runtime')) {
                @set_magic_quotes_runtime(0);
            }
        }
        // check magic_quotes_runtime again, stop if still is set
        if (OX_getMagicQuotesRuntime()) {
            $aErrors[] = 'The PHP magic_quotes_runtime option is ON, and cannot be automatically turned off.';
            $isSystemOK = false;
            if ($return === true) {
                $return = -5;
            }
        }
    }

    if (!$isSystemOK) {
        return $return;
    }
    return true;
}

/**
 * Get magic_quotes_runtime settings using get_magic_quotes_runtime or if function
 * doesn't exist try to read from php.ini
 *
 * @return int Returns 0 if magic quotes gpc are off, 1 otherwise
 */
function OX_getMagicQuotesRuntime()
{
    return (function_exists('get_magic_quotes_runtime')) 
            ? get_magic_quotes_runtime() : ini_get('magic_quotes_runtime');
}

OX_initialSystemCheck();
?>