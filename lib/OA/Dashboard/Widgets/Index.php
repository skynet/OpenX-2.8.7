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
$Id: Index.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

require_once MAX_PATH . '/lib/OA/Dashboard/Widget.php';
require_once MAX_PATH . '/lib/OA/Central/Dashboard.php';

/**
 * A class to display the dashboard iframe container
 *
 */
class OA_Dashboard_Widget_Index extends OA_Dashboard_Widget
{
    /**
     * A method to launch and display the widget
     *
     */
    function display()
    {
        $aConf = $GLOBALS['_MAX']['CONF'];

        phpAds_PageHeader(null, new OA_Admin_UI_Model_PageHeaderModel(), '', false, false);

        $oTpl = new OA_Admin_Template('dashboard/main.html');

        if (!$aConf['ui']['dashboardEnabled'] || !$aConf['sync']['checkForUpdates']) {
            $dashboardUrl = MAX::constructURL(MAX_URL_ADMIN, 'dashboard.php?widget=Disabled');
        } else {
            $m2mTicket = OA_Dal_Central_M2M::getM2MTicket(OA_Permission::getAccountId());
            if (empty($m2mTicket)) {
                $dashboardUrl = MAX::constructURL(MAX_URL_ADMIN, 'dashboard.php?widget=Reload');
            } else {
                $dashboardUrl = $this->buildDashboardUrl($m2mTicket, null, '&amp;');
            }
        }

        $oTpl->assign('dashboardURL', $dashboardUrl);

        $oTpl->display();

        phpAds_PageFooter('', true);
    }
}

?>
