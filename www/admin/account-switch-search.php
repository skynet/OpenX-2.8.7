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
$Id: account-switch-search.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

/**
 * A script to provide search results for the account switcher component.
 *
 * Currently, the script takes two GET parameters:
 *   - "q"     -- The string user typed in the seach box
 *   - "limit" -- The number of search results to be returned.
 */

if (empty($_GET["q"])) {
    $q = '';
} else {
    $q = strtolower($_GET["q"]);
}

// Require the initialisation file
require_once '../../init.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/lib/OA/Admin/Template.php';
require_once MAX_PATH . '/lib/OA/Admin/UI/AccountSwitch.php';

$oTpl = new OA_Admin_Template('account-switch-search.html');
OA_Admin_UI_AccountSwitch::assignModel($oTpl, $q);
$oTpl->display();

?>