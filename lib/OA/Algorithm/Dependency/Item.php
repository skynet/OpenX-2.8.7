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
$Id: Item.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/


/**
 * @author Radek Maciaszek <radek.maciaszek@openx.org>
 */

class OA_Algorithm_Dependency_Item
{
    protected $id;
    protected $depends;

    function __construct($id, $depends = array())
    {
        $this->id = $id;
        $this->depends = $depends;
    }

    function getId()
    {
        return $this->id;
    }

    function getDependencies()
    {
        return $this->depends;
    }
}

?>