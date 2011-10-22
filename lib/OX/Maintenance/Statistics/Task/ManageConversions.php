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
$Id: ManageConversions.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

require_once MAX_PATH . '/lib/OA.php';
require_once MAX_PATH . '/lib/OA/ServiceLocator.php';

require_once LIB_PATH . '/Maintenance/Statistics/Task.php';

/**
 * The MSE process task class that manages (migrates) conversions, as
 * required, during the MSE run.
 *
 * @TODO Deprecate, when conversion data is no longer required in the
 *       old format intermediate and summary tables.
 *
 * @package    OpenXMaintenance
 * @subpackage Statistics
 * @author     Andrew Hill <andrew.hill@openx.org>
 */
class OX_Maintenance_Statistics_Task_ManageConversions extends OX_Maintenance_Statistics_Task
{

    /**
     * The constructor method.
     *
     * @return OX_Maintenance_Statistics_Task_ManageConversions
     */
    function OX_Maintenance_Statistics_Task_ManageConversions()
    {
        parent::OX_Maintenance_Statistics_Task();
    }

    /**
     * The implementation of the OA_Task::run() method that performs
     * the required task of managing conversions.
     */
    function run()
    {
        if ($this->oController->updateIntermediate) {

            // Preapre the start date for the management of conversions
            $oStartDate = new Date();
            $oStartDate->copy($this->oController->oLastDateIntermediate);
            $oStartDate->addSeconds(1);

            // Get the MSE DAL to perform the conversion management
            $oServiceLocator =& OA_ServiceLocator::instance();
            $oDal =& $oServiceLocator->get('OX_Dal_Maintenance_Statistics');

            // Manage conversions
            $oDal->manageConversions($oStartDate, $this->oController->oUpdateIntermediateToDate);

        }
    }

}

?>