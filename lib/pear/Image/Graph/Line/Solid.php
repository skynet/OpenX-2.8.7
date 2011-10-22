<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Image_Graph - PEAR PHP OO Graph Rendering Utility.
 *
 * PHP versions 4 and 5
 *
 * LICENSE: This library is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation; either version 2.1 of the License, or (at your
 * option) any later version. This library is distributed in the hope that it
 * will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser
 * General Public License for more details. You should have received a copy of
 * the GNU Lesser General Public License along with this library; if not, write
 * to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 * 02111-1307 USA
 *
 * @category   Images
 * @package    Image_Graph
 * @subpackage Line
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @copyright  Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    CVS: $Id: Solid.php 47481 2009-12-15 20:29:37Z chris.nutting $
 * @link       http://pear.php.net/package/Image_Graph
 */

/**
 * Include file Image/Graph/Common.php
 */
require_once 'Image/Graph/Common.php';

/**
 * Simple colored line style.
 *
 * Use a color for line style.
 *
 * @category   Images
 * @package    Image_Graph
 * @subpackage Line
 * @author     Jesper Veggerby <pear.nosey@veggerby.dk>
 * @copyright  Copyright (C) 2003, 2004 Jesper Veggerby Hansen
 * @license    http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @version    Release: 0.7.2
 * @link       http://pear.php.net/package/Image_Graph
 */
class Image_Graph_Line_Solid extends Image_Graph_Common
{

    /**
     * The thickness of the line (requires GD 2)
     * @var int
     * @access private
     */
    var $_thickness = 1;

    /**
     * The color of the line
     * @var mixed
     * @access private
     */
    var $_color;

    /**
     * Image_Graph_SolidLine [Constructor]
     *
     * @param mixed $color The color of the line
     */
    function Image_Graph_Line_Solid($color)
    {
        parent::__construct();
        $this->_color = $color;
    }

    /**
     * Set the thickness of the linestyle
     *
     * @param int $thickness The line width in pixels
     */
    function setThickness($thickness)
    {
        $this->_thickness = $thickness;
    }

    /**
     * Gets the line style of the element
     *
     * @return int A GD linestyle representing the line style
     * @see Image_Graph_Line
     * @access private
     */
    function _getLineStyle()
    {
        return array(
            'color' => $this->_color,
            'thickness' => $this->_thickness
        );
    }

}

?>
