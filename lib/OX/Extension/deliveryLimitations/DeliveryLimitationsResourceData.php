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
$Id: DeliveryLimitationsResourceData.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

require_once MAX_PATH . '/plugins/deliveryLimitations/DeliveryLimitationsArrayData.php';
require_once MAX_PATH . '/lib/max/Plugin/Translation.php';

/**
 * A base class for delivery limitations plugins which uses a resource array
 * to limit the delivery.
 *
 * @package    OpenXPlugin
 * @subpackage DeliveryLimitations
 * @author     Andrzej Swedrzynski <andrzej@m3.net>
 */
class Plugins_DeliveryLimitations_ResourceData extends Plugins_DeliveryLimitations_ArrayData
{
    function init($data)
    {
        parent::init($data);
        $this->setAValues(array_keys($this->res));
    }
}
?>