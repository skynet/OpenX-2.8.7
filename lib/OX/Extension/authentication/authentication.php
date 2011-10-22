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
$Id: authentication.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

require_once MAX_PATH . '/lib/max/Plugin/Translation.php';
require_once LIB_PATH . '/Plugin/Component.php';
require_once 'Date.php';
require_once MAX_PATH . '/lib/max/language/Loader.php';

Language_Loader::load('settings');

/**
 * Plugins_Authentication is an parent class for Authentication plugins
 *
 * @package    OpenXPlugin
 * @subpackage Authentication
 * @author     Radek Maciaszek <radek.maciaszek@openx.org>
 */
class Plugins_Authentication extends OX_Component
{
    /**
     * Array to keep a reference to signup errors (if any)
     *
     * @var array
     */
    var $aSignupErrors = array();

    var $aValidationErrors = array();

    /**
     * Checks if credentials are passed and whether the plugin should carry on the authentication
     *
     * @return boolean  True if credentials were passed, else false
     */
    function suppliedCredentials()
    {
        return isset($_POST['username']) || isset($_POST['password']);
    }

    /**
     * Authenticate user
     *
     * @return DataObjects_Users  returns users dataobject on success authentication
     *                            or null if user wasn't succesfully authenticated
     */
    function &authenticateUser()
    {
        $aCredentials = $this->_getCredentials();
        if (PEAR::isError($aCredentials)) {
            OA_Auth::displayError($aCredentials);
        }
        return $this->checkPassword($aCredentials['username'],
            $aCredentials['password']);
    }

        /**
     * A to get the login credential supplied as POST parameters
     *
     * Additional checks are also performed and error eventually returned
     *
     * @param bool $performCookieCheck
     * @return mixed Array on success, PEAR_Error otherwise
     */
    function _getCredentials($performCookieCheck = true)
    {
        if (empty($_POST['username']) || empty($_POST['password'])) {
            return new PEAR_Error($GLOBALS['strEnterBoth']);
        }

        if ($performCookieCheck && !isset($_COOKIE['sessionID'])) {
            return new PEAR_Error($GLOBALS['strEnableCookies']);
        }

        if ($performCookieCheck && $_COOKIE['sessionID'] != $_POST['oa_cookiecheck']) {
            return new PEAR_Error($GLOBALS['strSessionIDNotMatch']);
        }

        return array(
            'username' => MAX_commonGetPostValueUnslashed('username'),
            'password' => MAX_commonGetPostValueUnslashed('password')
        );
    }

    /**
     * A method to check a username and password
     *
     * @param string $username
     * @param string $password
     * @return mixed A DataObjects_Users instance, or false if no matching user was found
     */
    function checkPassword($username, $password)
    {
        $doUser = OA_Dal::factoryDO('users');
        $doUser->username = strtolower($username);
        $doUser->password = md5($password);
        $doUser->find();

        if ($doUser->fetch()) {
            return $doUser;
        } else {
            return false;
        }
    }

    /**
     * Cleans up the session and carry on any additional tasks required to logout the user
     *
     */
    function logout()
    {
        phpAds_SessionDataDestroy();
        $dalAgency = OA_Dal::factoryDAL('agency');
        header ("Location: " . $dalAgency->getLogoutUrl(OA_Permission::getAgencyId()));
        exit;
    }

    /**
     * A static method to display a login screen
     *
     * @static
     *
     * @param string $sMessage
     * @param string $sessionID
     * @param bool $inlineLogin
     */
    function displayLogin($sMessage = '', $sessionID = 0, $inLineLogin = false)
    {
        global $strUsername, $strPassword, $strLogin, $strWelcomeTo, $strEnterUsername,
               $strNoAdminInteface, $strForgotPassword;

        $aConf = $GLOBALS['_MAX']['CONF'];
        $aPref = $GLOBALS['_MAX']['PREF'];

        @header('Cache-Control: max-age=0, no-cache, proxy-revalidate, must-revalidate');

        if (!$inLineLogin) {
            phpAds_PageHeader(phpAds_Login);
        }

        // Check environment settings
        $oSystemMgr = new OA_Environment_Manager();
        $aSysInfo = $oSystemMgr->checkSystem();

        foreach ($aSysInfo as $env => $vals) {
            $errDetails = '';
            if (is_array($vals['error']) && !empty($vals['error'])) {
                if ($env == 'PERMS') {
                    // Just note that some file/folders are unwritable and that more information can be found in the debug.log
                    OA_Admin_UI::queueMessage('Error: File permission errors detected.<br />These <em>may</em> impact the accurate delivery of your ads,<br />See the debug.log file for the list of unwritable files', 'global', 'error', 0);
                } else {
                    foreach ($vals['error'] as $key => $val) {
                        $errDetails .= '<li>' . htmlspecialchars($key) . ' &nbsp; => &nbsp; ' . htmlspecialchars($val) . '</li>';
                    }
					phpAds_Die( ' Error: ' . $err, $errDetails );
                }
            }
        }

        $oTpl = new OA_Admin_Template('login.html');

        // we build the URL of the current page to use a redirect URL after login
        // this code should work on all server configurations hence why it is a bit complicated
        // inspired by http://dev.piwik.org/svn/trunk/core/Url.php getCurrentUrl()
        $url = '';
        if( !empty($_SERVER['PATH_INFO']) ) { 
            $url = $_SERVER['PATH_INFO'];
        } else if( !empty($_SERVER['REQUEST_URI']) ) {
            if( ($pos = strpos($_SERVER['REQUEST_URI'], "?")) !== false ) {
                $url = substr($_SERVER['REQUEST_URI'], 0, $pos);
            } else {
                $url = $_SERVER['REQUEST_URI'];
            }
        }
        if(empty($url)) {
            $url = $_SERVER['SCRIPT_NAME'];
        }
        if (!empty($_SERVER['QUERY_STRING'])) {
            $url .= '?'.$_SERVER['QUERY_STRING'];
        }
        if(!empty($url)) {
	        // remove any extra slashes that would confuse the browser (see OX-5234)
	        $url = '/' . ltrim($url, '/');
        }
            
        $appName = !empty($aConf['ui']['applicationName']) ? $aConf['ui']['applicationName'] : MAX_PRODUCT_NAME;

        $oTpl->assign('uiEnabled', $aConf['ui']['enabled']);
        $oTpl->assign('formAction', $url);
        $oTpl->assign('sessionID', $sessionID);
        $oTpl->assign('appName', $appName);
        $oTpl->assign('message', $sMessage);

        $oTpl->display();

        phpAds_PageFooter();
        exit;
    }

    /**
     * A method to perform DLL level validation
     *
     * @param OA_Dll_User $oUser
     * @param OA_Dll_UserInfo $oUserInfo
     * @return boolean
     */
    function dllValidation(&$oUser, &$oUserInfo)
    {
        if (!isset($oUserInfo->userId)) {
            if (!$oUser->checkStructureRequiredStringField($oUserInfo, 'username', 64) ||
                !$oUser->checkStructureRequiredStringField($oUserInfo, 'password', 32)) {
                return false;
            }
        }

        if (isset($oUserInfo->password)) {
            // Save MD5 hash of the password
            $oUserInfo->password = md5($oUserInfo->password);
        }
        return true;
    }

    /**
     * A method to set the required template variables, if any
     *
     * @param OA_Admin_Template $oTpl
     */
    function setTemplateVariables(&$oTpl)
    {
        if (preg_match('/-user-start\.html$/', $oTpl->templateName)) {
            $oTpl->assign('fields', array(
               array(
                   'fields'    => array(
                       array(
                           'name'      => 'login',
                           'label'     => $GLOBALS['strUsernameToLink'],
                           'value'     => '',
                           'id'        => 'user-key'
                       ),
                   )
               ),
            ));
        }
    }

    /**
     * Build user details array fields required by user access (edit) pages
     *
     * @param array $userData  Array containing users data (see users table)
     * @return array  Array formatted for use in template object as in user access pages
     */
    function getUserDetailsFields($userData)
    {
        $userExists = !empty($userData['user_id']);
        $userDetailsFields = array();
        $oLanguages = new MAX_Admin_Languages();
        $aLanguages = $oLanguages->AvailableLanguages();

        $userDetailsFields[] = array(
                'name'      => 'login',
                'label'     => $GLOBALS['strUsername'],
                'value'     => $userData['username'],
                'freezed'   => !empty($userData['user_id'])
            );
        $userDetailsFields[] = array(
                'name'      => 'passwd',
                'label'     => $GLOBALS['strPassword'],
                'type'      => 'password',
                'value'     => '',
                'hidden'   => !empty($userData['user_id'])
            );
        $userDetailsFields[] = array(
                'name'      => 'passwd2',
                'label'     => $GLOBALS['strPasswordRepeat'],
                'type'      => 'password',
                'value'     => '',
                'hidden'   => !empty($userData['user_id'])
            );
        $userDetailsFields[] = array(
                'name'      => 'contact_name',
                'label'     => $GLOBALS['strContactName'],
                'value'     => $userData['contact_name'],
                'freezed'   => $userExists
            );
        $userDetailsFields[] = array(
                'name'      => 'email_address',
                'label'     => $GLOBALS['strEMail'],
                'value'     => $userData['email_address'],
                'freezed'   => $userExists
            );
        $userDetailsFields[] = array(
                'type'      => 'select',
                'name'      => 'language',
                'label'     => $GLOBALS['strLanguage'],
                'options'   => $aLanguages,
                'value'     => (!empty($userData['language'])) ? $userData['language'] : $GLOBALS['_MAX']['PREF']['language'],
                'disabled'   => $userExists
            );

        return $userDetailsFields;
    }

    function getMatchingUserId($email, $login)
    {
        $doUsers = OA_Dal::factoryDO('users');
        return $doUsers->getUserIdByProperty('username', $login);
    }

    /**
     * Validates user's email address
     *
     * @param string $email
     * @return array  Array containing error strings or empty
     *                array if no validation errors were found
     */
    function validateUsersEmail($email)
    {
        if (!$this->isValidEmail($email)) {
            $this->addValidationError($GLOBALS['strInvalidEmail']);
        }
    }

    /**
     * Returns true if email address is valid else false
     *
     * @param string $email
     * @return boolean
     */
    function isValidEmail($email)
    {
        return eregi("^[a-zA-Z0-9]+[_a-zA-Z0-9-]*(\.[_a-z0-9-]+)*@[a-z??????0-9]+"
                ."(-[a-z??????0-9]+)*(\.[a-z??????0-9-]+)*(\.[a-z]{2,6})$", $email);
    }

    function saveUser($userid, $login, $password, $contactName,
        $emailAddress, $language, $accountId)
    {
        $doUsers = OA_Dal::factoryDO('users');
        $doUsers->loadByProperty('user_id', $userid);

        return $this->saveUserDo($doUsers, $login, $password, $contactName,
        $emailAddress, $language, $accountId);
    }

    /**
     * Method used in user access pages. Either creates new user if
     * necessary or update existing one.
     *
     * @param DB_DataObject_Users $doUsers  Users dataobject with any preset variables
     * @param string $login  User name
     * @param string $password  Password
     * @param string $contactName  Contact name
     * @param string $emailAddress  Email address
     * @param integer $accountId  a
     * @return integer  User ID or false on error
     */
    function saveUserDo(&$doUsers, $login, $password, $contactName,
        $emailAddress, $language, $accountId)
    {
        $doUsers->contact_name = $contactName;
        $doUsers->email_address = $emailAddress;
        $doUsers->language = $language;
        if ($doUsers->user_id) {
            $doUsers->update();
            return $doUsers->user_id;
        } else {
            $doUsers->default_account_id = $accountId;
            $doUsers->username = $login;
            $doUsers->password = md5($password);
            return $doUsers->insert();
        }
    }

    /**
     * Returns array of errors which happened during sigup
     *
     * @return array
     */
    function getSignupErrors()
    {
        return $this->aSignupErrors;
    }

    /**
     * Adds an error message to signup errors array
     *
     * @param string $errorMessage
     */
    function addSignupError($error)
    {
        if (PEAR::isError($error)) {
            $errorMessage = $error->getMessage();
        } else {
            $errorMessage = $error;
        }
        if (!in_array($errorMessage, $this->aSignupErrors)) {
            $this->aSignupErrors[] = $errorMessage;
        }
    }

    /**
     * Returns array of errors which happened during sigup
     *
     * @return array
     */
    function getValidationErrors()
    {
        return $this->aValidationErrors;
    }

    /**
     * Adds an error message to validation errors array
     *
     * @param string $aValidationErrors
     */
    function addValidationError($error)
    {
        $this->aValidationErrors[] = $error;
    }

    /**
     * A method to change a user password
     *
     * @param DataObjects_Users $doUsers
     * @param string $newPassword
     * @param string $oldPassword
     * @return mixed True on success, PEAR_Error otherwise
     */
    function changePassword(&$doUsers, $newPassword, $oldPassword)
    {
        $doUsers->password = md5($newPassword);
        return true;
    }

    /**
     * A method to set a new user password
     *
     * @param string $userId
     * @param string $newPassword
     * @return mixed True on success, PEAR_Error otherwise
     */
    function setNewPassword($userId, $newPassword)
    {
        $doUsers = OA_Dal::staticGetDO('users', $userId);
        if (!$doUsers) {
            return false;
        }
        $doUsers->password = md5($newPassword);
        return $doUsers->update();
    }

    /**
     * A method to change a user email
     *
     * @param DataObjects_Users $doUsers
     * @param string $emailAddress
     * @param string $password
     * @return bool
     */
    function changeEmail(&$doUsers, $emailAddress, $password)
    {
        $doUsers->email_address = $emailAddress;
        $doUsers->email_updated = $doUsers->formatDate(new Date());
        return true;
    }

    /**
     * Delete unverified accounts. Used by cas
     *
     * @param OA_Maintenance $oMaintenance
     * @return boolean
     */
    function deleteUnverifiedUsers(&$oMaintenance)
    {
        return true;
    }

    // These were pulled straight from the internal class...
        /**
     * Validates user login - required for linking new users
     *
     * @param string $login
     */
    function validateUsersLogin($login)
    {
        if (empty($login)) {
            $this->addValidationError($GLOBALS['strInvalidUsername']);
        } elseif (OA_Permission::userNameExists($login)) {
            $this->addValidationError($GLOBALS['strDuplicateClientName']);
        }
    }

    /**
     * Validates user password - required for linking new users
     *
     * @param string $password
     * @return array  Array containing error strings or empty
     *                array if no validation errors were found
     */
    function validateUsersPassword($password)
    {
        if (!strlen($password) || strstr("\\", $password)) {
            $this->addValidationError($GLOBALS['strInvalidPassword']);
        }
    }

    function validateUsersPasswords($password1, $password2)
    {
        if ($password1 != $password2) {
            $this->addValidationError($GLOBALS['strNotSamePasswords']);
        }
    }

    /**
     * Validates user data - required for linking new users
     *
     * @param string $login
     * @param string $password
     * @return array  Array containing error strings or empty
     *                array if no validation errors were found
     */
    function validateUsersData($data)
    {
        if (empty($data['userid'])) {
            $this->validateUsersLogin($data['login']);
            $this->validateUsersPasswords($data['passwd'], $data['passwd2']);
            $this->validateUsersPassword($data['passwd']);
        }
        $this->validateUsersEmail($data['email_address']);

        return $this->getValidationErrors();
    }
}

?>