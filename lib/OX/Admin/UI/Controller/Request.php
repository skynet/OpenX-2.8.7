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
$Id$
*/

/**
 * A simple HTTP request object for use with Base_Controller. Modelled slightly
 * after Zend's much more extensive Http request object.
 * Reuses pieces of code from the aformentioned Zend class.
 *
 * @package OX_Admin_UI
 * @subpackage Controller
 * @author Bernard Lange <bernard@openx.org> 
 */
class OX_Admin_UI_Controller_Request
{
    /**
     * Instance parameters
     * @var array
     */
    protected $_params = array();
    

    /**
     * Request base url
     *
     * @var string
     */
    protected $baseUrl;

    
    /**
     * REQUEST_URI
     *
     * @var unknown_type
     */
    protected $requestUri;
    
    
    
    /**
     * Set a user-specified parameter. 
     *
     * @param string $key
     * @param mixed $value
     */
    public function setParam(string $key, $value)
    {
        if ((null === $value) && isset($this->_params[$key])) {
            unset($this->_params[$key]);
        } 
        elseif (null !== $value) {
            $this->_params[$key] = $value;
        }

        return $this;
    }   
     

    /**
     * Retrieves a parameter from the instance. Priority is in the order of
     * user-specified parameters (see {@link setParam()}), $_GET, $_POST. If a
     * parameter matching the $key is not found, if $default has not been given 
     * null is returned, otherwise $default.
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed
     */
    public function getParam($keyName, $default = null)
    {
        if (isset($this->_params[$keyName])) {
            return $this->_params[$keyName];
        } 
        elseif (isset($_GET[$keyName])) {
            return $_GET[$keyName];
        } 
        elseif (isset($_POST[$keyName])) {
            return $_POST[$keyName];
        }

        return $default;
    }
    

    /**
     * Retrieves a merged array of parameters, with precedence of user-defined
     * params (see {@link setParam()}), $_GET, $POST (i.e., values in the
     * user-defined params will take precedence over all others).
     *
     * @return array
     */
    public function getParams()
    {
        $return = $this->_params;
        if (isset($_GET) && is_array($_GET)) {
            $return += $_GET;
        }
        if (isset($_POST) && is_array($_POST)) {
            $return += $_POST;
        }
        return $return;
    }
    

    /**
     * Set one or more parameters. Parameters are set as user-defined instance
     *  parameters, using the keys specified in the array.
     *
     * @param array $params
     */
    public function setParams(array $params)
    {
        foreach ($params as $key => $value) {
            $this->setParam($key, $value);
        }
    }


    /**
     * Return the method by which the request was made
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->getServer('REQUEST_METHOD');
    }
    

    /**
     * Was the request made by POST?
     *
     * @return boolean
     */
    public function isPost()
    {
        if ('POST' == $this->getMethod()) {
            return true;
        }

        return false;
    }
    

    /**
     * Was the request made by GET?
     *
     * @return boolean
     */
    public function isGet()
    {
        if ('GET' == $this->getMethod()) {
            return true;
        }

        return false;
    }
    
    
    /**
     * Retrieve a member of the $_SERVER superglobal
     *
     * If no $key is passed, returns the entire $_SERVER array.
     *
     * @param string $key
     * @param mixed $default Default value to use if key not found
     * @return mixed Returns null if key does not exist
     */
    public function getServer($key = null, $default = null)
    {
        if (null === $key) {
            return $_SERVER;
        }

        return (isset($_SERVER[$key])) ? $_SERVER[$key] : $default;
    }

    
    /**
     * Returns base url for the request.
     * 
     * @return string
     */
    public function getBaseUrl()
    {
        if ($this->baseUrl == null) {
            $baseUrl = 'http'.((isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] == "on")) ? 's' : '').'://';
            $baseUrl .= OX_getHostNameWithPort().substr($_SERVER['REQUEST_URI'],0,strrpos($_SERVER['REQUEST_URI'], '/')+1);
            
            $this->baseUrl = $baseUrl; 
        }
        
        return $this->baseUrl;
    }
     

    /**
     * Returns the REQUEST_URI taking into account
     * platform differences between Apache and IIS
     *
     * @return string
     */
    public function getRequestUri()
    {
        if ($this->requestUri == null) {
            $requestUri = null;
            
            if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // check this first so IIS will catch
                $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
            } 
            elseif (isset($_SERVER['REQUEST_URI'])) {
                $requestUri = $_SERVER['REQUEST_URI'];
            } 
            elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
                $requestUri = $_SERVER['ORIG_PATH_INFO'];
                if (!empty($_SERVER['QUERY_STRING'])) {
                    $requestUri .= '?' . $_SERVER['QUERY_STRING'];
                }
            } 
                
            $this->requestUri = $requestUri;
        }

        return $this->requestUri;
    }
    
}

?>