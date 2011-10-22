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
$Id: maintenance-distributed.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

/**
 * A script file to run the Maintenance Distributed Engine
 */

// Require the initialisation file
// Done differently from elsewhere so that it works in CLI MacOS X
$path = dirname(__FILE__);
require_once $path . '/../../init.php';

// Required files
require_once MAX_PATH . '/lib/Max.php';
require_once MAX_PATH . '/lib/OX/Maintenance/Distributed.php';

require_once OX_PATH . '/lib/OX.php';

OX_Maintenance_Distributed::run();

?>
