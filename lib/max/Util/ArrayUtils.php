<?php

/*
+---------------------------------------------------------------------------+
| OpenX v2.8                                             |
| ==========                            |
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
$Id: ArrayUtils.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

/**
 * Various array utilities.
 *
 */
class ArrayUtils
{
    /**
     * Searches the $aValues for the first occurence of $oValue. If the value
     * is found and its key is numeric, it is unset from the array. The array
     * is passed as reference.
     *
     * @param array $aValues
     * @param object $value
     */
    function unsetIfKeyNumeric(&$aValues, $oValue)
    {
        $key = array_search($oValue, $aValues);
        if (is_numeric($key)) {
            unset($aValues[$key]);
        }
    }
}

?>