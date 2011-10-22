<?php
/**
 * A pseudo-element used for adding break between the fields without the header
 */

require_once MAX_PATH.'/lib/pear/HTML/QuickForm/static.php';

class OA_Admin_UI_Component_FormBreak 
    extends HTML_QuickForm_static
{

   /**
    * Class constructor
    * 
    * @param string $elementName    Header name
    */
    function OA_Admin_UI_Component_FormBreak($elementName = null, $text = null)
    {
        $this->HTML_QuickForm_static($elementName, null, $text);
        $this->_type = 'break';
    }


   /**
    * Accepts a renderer
    *
    * @param HTML_QuickForm_Renderer    renderer object
    */
    function accept(&$renderer)
    {
        $renderer->renderElement($this);
    } 
} 
?>
