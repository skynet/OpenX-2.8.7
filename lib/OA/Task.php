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
$Id: Task.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

/**
 * A abstract class, defining an interface for Task objects, to be collected
 * and run using the OA_Task_Runner calss.
 *
 * @abstract
 * @package    OpenX
 * @subpackage Tasks
 * @author     Demian Turner <demian@m3.net>
 */
class OA_Task
{

    /**
     * A abstract method that needs to be implemented in child Task classes,
     * which will be called when the task needs to be performed.
     *
     * @abstract
     * @todo This method should really return a boolean.
     */
    function run()
    {
        return;
    }

}

?>
