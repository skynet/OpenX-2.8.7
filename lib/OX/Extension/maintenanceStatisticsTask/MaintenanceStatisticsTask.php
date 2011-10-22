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
$Id: MaintenanceStatisticsTask.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/
require_once LIB_PATH . '/Plugin/Component.php';
require_once MAX_PATH . '/lib/OA/Task/Runner.php';

/**
 * Plugins_MaintenanceStatisticsTask is an abstract class for every maintenanceStatisticsTask plugin
 *
 * @package    OpenXPlugin
 * @subpackage MaintenaceStatisticsTask
 * @author     Lukasz Wikierski <lukasz.wikierski@openx.org>
 * @abstract
 */
abstract class Plugins_MaintenanceStatisticsTask extends OX_Component
{
    /**
     * Constructor method
     */
    function __construct($extension, $group, $component) {
    }

    /**
     * Method returns OX_Maintenance_Statistics_Task 
     * to run in the Maintenance Statistics Engine
     * Implements hook 'addMaintenanceStatisticsTask'
     * 
     * @abstract 
     * @return OX_Maintenance_Statistics_Task
     */
    abstract function addMaintenanceStatisticsTask();

    /**
     * Returns the class name of the task this task should run after or replace.
     * To add to the end of the task list, return null.
     *
     * @return string the name of the task to run after or replace.
     */
    public function getExistingClassName()
    {
        return null;
    }

    /**
     * Whether the task should replace the class specified in getExistingClassName.
     * Use class constants defined in OA_Task_Runner.
     *
     * @return integer -1 if the task should run before the specified class,
     *                 0 if the task should replace the specified class,
     *                 1 if the task should run after the specified class.
     */
    public function getOrder()
    {
        return OA_Task_Runner::TASK_ORDER_AFTER;
    }

    /**
     * Run before the MSE tasks.
     *
     * @return boolean true on success, false on failure.
     */
    public function beforeMse()
    {
        return true;
    }

    /**
     * Run after the MSE tasks.
     *
     * @return boolean true on successm false on failure.
     */
    public function afterMse()
    {
        return true;
    }
}

?>
