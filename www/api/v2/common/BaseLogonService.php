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
$Id: BaseLogonService.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

/**
 * @package    OpenX
 * @author     Andriy Petlyovanyy <apetlyovanyy@lohika.com>
 *
 */

// Require the XMLRPC classes
require_once MAX_PATH . '/www/api/v2/xmlrpc/LogonServiceImpl.php';


/**
 * Base Logon Service
 *
 */
class BaseLogonService
{
    /**
     * Reference to logon Service implementation.
     *
     * @var LogonServiceImpl $logonServiceImp
     */
    var $logonServiceImp;

    /**
     * This method initialises Service implementation object field.
     *
     */
    function BaseLogonService()
    {
        $this->logonServiceImp = new LogonServiceImpl();
    }
}


?>