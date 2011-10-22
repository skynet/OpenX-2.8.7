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
$Id: JQueryValidationRuleBuilder.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

class OA_Admin_UI_Rule_JQueryValidationRuleBuilder
{
    private $oAdaptorRegistry;
    
    public function __construct()
    {
        $this->oAdaptorRegistry = OA_Admin_UI_Rule_JQueryRuleAdaptorRegistry::singleton();
    }
    
    protected function getAdaptorRegistry()
    {
        return $this->oAdaptorRegistry;
    }
    
    
    /**
     * Return a JS rule definition applicable for JQuery validation plugin
     * http://bassistance.de/jquery-plugins/jquery-plugin-validation/
     * based on active client rules.
     * Please not that this does not take into account some properties of the
     * rule like. eg. 'reset' and "dependent" at the moment. 
     * 
     * $rules parameter array contains quickform rule data entries in a form:
     *   array(
     *           'type'        => $type,
     *           'format'      => $format,
     *           'message'     => $message,
     *           'validation'  => $validation,
     *           'reset'       => $reset,
     *           'dependent'   => $dependent
     *       );
     * 
     * Example input entry for field "name" which is "required" and must be 
     * min 6 characters long :
     * 'name' => array(
     *   array(
     *           'type'        => "required",
     *           'format'      => null,
     *           'message'     => "Name is required",
     *           'validation'  => "client",
     *           'reset'       => false,
     *           'dependent'   => null
     *       ),
     *   array(
     *           'type'        => "minlegth",
     *           'format'      => 6,
     *           'message'     => "Name must be at least 6 characters long",
     *           'validation'  => "client",
     *           'reset'       => false,
     *           'dependent'   => null
     *       ),
     * );
     * 
     * @param map $rules <elementName> => <elementRules> array map 
     */
    public function getJQueryValidationRulesScript($rules)
    {
        $registry = $this->getAdaptorRegistry();        
        $rulesText .= "rules: {\n";
        $messagesText .= "messages: {\n";
        $rulesCount = count($rules);
        $i = 1;
        foreach($rules as $elementName => $elementRules) {
            $rulesText.= " \"$elementName\": {\n";
            $messagesText.= " \"$elementName\": {\n";
            $j = 1;
            $elementRules = array_filter($elementRules, array($this, 'filterNonSupported'));
            $elemRulesCount = count($elementRules);
            foreach ($elementRules as $rule) {
                $ruleAdaptor = $registry->getJQueryRuleAdaptor($rule['type']);
                $rulesText .= "  ".$ruleAdaptor->getJQueryValidationRule($rule);
                $messagesText .= "  ".$ruleAdaptor->getJQueryValidationMessage($rule);
                if ($j < $elemRulesCount) {
                    $rulesText .= ",\n";
                    $messagesText .= ",\n";
                }
                $j++;
            }
            $rulesText.= " }";
            $messagesText.= " }";
            if ($i < $rulesCount) {
                $rulesText.= ",\n";
                $messagesText.= ",\n"; //close element array
            }
            $i++;
        }
        $rulesText .= "},\n"; //close rules array
        $messagesText .= "}\n"; //close messages array
        
        return $rulesText."\n".$messagesText;
    }
    
    
    protected function filterNonSupported($aRule)
    {
        $registry = $this->getAdaptorRegistry();
        $ruleAdaptor = $registry->getJQueryRuleAdaptor($aRule['type']);
        return !empty($ruleAdaptor);
    }
    
    
    /**
     * Return a JS JQuery validator custom validation method installation script
     * http://bassistance.de/jquery-plugins/jquery-plugin-validation/
     * based on active client rules.
     * 
     * $rules parameter array contains quickform rule data entries in a form:
     *   array(
     *           'type'        => $type,
     *           'format'      => $format,
     *           'message'     => $message,
     *           'validation'  => $validation,
     *           'reset'       => $reset,
     *           'dependent'   => $dependent
     *       );
     * 
     * Output script is build as follows 
     * If appriopriate JQuery adapter is registered and it returns a custom method code:
     * - Add $.validator.methods
     * - Add rule name taken from rule type
     * - Add equals 
     * - Add function definition from appriopriate JQuery adapter.
     *  
     * "validator.methods.".$rule['type'] = 
     *      $registry->getJQueryRuleAdaptor($rule['type'])->getJQueryValidationMethodCode();
     * 
     * Example output for registered JQuery-enabled 'unique' rule:
     * $.validator.methods.unique = function(value, element, otherValuesArr) {
     *      return $.inArray(value, otherValuesArr) == -1;
     * };
     * 
     * @param map $rules <elementName> => <elementRules> array map 
     */
    public function getJQueryValidationMethodsScript($rules)
    {
        $registry = OA_Admin_UI_Rule_JQueryRuleAdaptorRegistry::singleton();        
        foreach($rules as $elementName => $rules) {
            foreach ($rules as $rule) {
                $ruleAdaptor = $registry->getJQueryRuleAdaptor($rule['type']);
                if (!empty($ruleAdaptor)) {
                    $ruleMethod = $ruleAdaptor->getJQueryValidationMethodCode($rule);
                    if (!empty($ruleMethod)) {
                        $methodsText .= "$.validator.methods.".$rule['type']." = ";
                        $methodsText .= $ruleMethod.";\n";
                    }
                }
            }
;           $methodsText.= "\n"; 
        }
        
        return $methodsText;
    }    
    
    
    
    /**
     * Return Jquery validation plugin compliant rule definition for a given quickform rule
     *
     * @param unknown_type $rule
     * @return unknown
     */
    private function getJQueryValidationRule($rule)
    {
        $type = $rule['type'];
        $adaptor = $this->quickFormRuleNameToAdaptorMap[$type];

        return $adaptor->getJQueryValidationRule($rule);
    }
    
    
    private function getJQueryValidationMessage($rule)
    {
        $type = $rule['type'];
        $adaptor = $this->quickFormRuleNameToAdaptorMap[$type];

        return $adaptor->getJQueryValidationMessage($rule);
    }
}
?>
