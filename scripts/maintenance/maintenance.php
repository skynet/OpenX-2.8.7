#!/usr/bin/php -q
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
$Id: maintenance.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

/**
 * A script file to run the Maintenance Statistics Engine and the
 * Maintenance Priority Engine processes.
 *
 * @package    OpenXMaintenance
 * @subpackage Tools
 * @author     Andrew Hill <andrew.hill@openx.org>
 */

// Set the current path
// Done this way so that it works in CLI PHP
$path = dirname(__FILE__);

// Require the timezone class, and get the system timezone,
// storing in a global variable
global $serverTimezone;
require_once $path . '/../../lib/OX/Admin/Timezones.php';
$serverTimezone = OX_Admin_Timezones::getTimezone();

// Require the initialisation file
require_once $path . '/../../init.php';

// Set longer time out, and ignore user abort
if (!ini_get('safe_mode')) {
    @set_time_limit($conf['maintenance']['timeLimitScripts']);
    @ignore_user_abort(true);
}

// Required files
require_once MAX_PATH . '/lib/Max.php';

require_once OX_PATH . '/lib/OX.php';
require_once LIB_PATH . '/Maintenance.php';

$oMaint = new OX_Maintenance();
$oMaint->run();

// Update scheduled maintenance last run record
$oMaint->updateLastRun(true);

?>
