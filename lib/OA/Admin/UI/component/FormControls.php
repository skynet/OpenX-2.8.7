<?php
/**
 * A pseudo-element used for adding form controls section ie. the section
 * which contains control buttons at the bottom of the screen.
 */

require_once MAX_PATH.'/lib/pear/HTML/QuickForm/static.php';

class OA_Admin_UI_Component_FormControls 
    extends HTML_QuickForm_static
{

   /**
    * Class constructor
    * 
    * @param string $elementName    Header name
    */
    function OA_Admin_UI_Component_FormControls($elementName = null, $text = null)
    {
        $this->HTML_QuickForm_static($elementName, null, $text);
        $this->_type = 'controls';
    }


   /**
    * Accepts a renderer
    *
    * @param HTML_QuickForm_Renderer    renderer object
    */
    function accept(&$renderer)
    {
        $renderer->renderHeader($this);
    } 
} 
?>
