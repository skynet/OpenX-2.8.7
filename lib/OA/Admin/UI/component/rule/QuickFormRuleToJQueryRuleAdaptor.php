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
$Id: QuickFormRuleToJQueryRuleAdaptor.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

/**
 * A wrapper rule for HTML_QuickForm email rule. Accepts quickform rule data in the
 * for of array:
 *   array(
 *           'type'        => $type,
 *           'format'      => $format,
 *           'message'     => $message,
 *           'validation'  => $validation,
 *           'reset'       => $reset,
 *           'dependent'   => $dependent
 *       ); 
 *  
 */
interface OA_Admin_UI_Rule_QuickFormToJQueryRuleAdaptor
{
    /**
     * Returns JS method code that should be installed as a validation method 
     * to JQuery validation plugin under the Quickfor rule name
     *
     * @param array $rule
     * @return string
     */
    public function getJQueryValidationMethodCode();
    
    
    /**
     * Returns Jquery validation plugin compliant rule definition for a given quickform rule
     *
     * @param array $rule
     * @return string
     */
    public function getJQueryValidationRule($rule);

    /**
     * Returns Jquery validation plugin compliant message definition for a given quickform rule
     *
     * @param array $rule
     * @return string
     */
    public function getJQueryValidationMessage($rule);
}
?>
