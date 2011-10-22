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
$Id$
*/
require_once MAX_PATH . '/lib/OX/Admin/UI/Controller/BaseController.php';
require_once MAX_PATH . '/lib/OX/Admin/UI/Install/Wizard.php';
require_once MAX_PATH . '/lib/OX/Admin/UI/Install/AdminLoginForm.php';
require_once MAX_PATH . '/lib/OX/Admin/UI/Install/SsoSignupForm.php';
require_once MAX_PATH . '/lib/OX/Admin/UI/Install/SkipSsoForm.php';
require_once MAX_PATH . '/lib/OX/Admin/UI/Install/SsoLoginForm.php';
require_once MAX_PATH . '/lib/OX/Admin/UI/Install/DbForm.php';
require_once MAX_PATH . '/lib/OX/Admin/UI/Install/ConfigForm.php';
require_once MAX_PATH . '/lib/OX/Admin/UI/Install/InstallUtils.php';
require_once MAX_PATH . '/lib/OX/Admin/UI/Install/SystemCheckModelBuilder.php';
require_once MAX_PATH . '/lib/OX/Admin/UI/Install/SsoErrorBuilder.php';
require_once MAX_PATH . '/lib/OX/Admin/UI/Install/InstallStatus.php';
require_once MAX_PATH . '/lib/OX/Admin/UI/SessionStorage.php';
require_once MAX_PATH . '/lib/OX/Upgrade/MarketClientFactory.php';
require_once MAX_PATH . '/lib/OX/Admin/Redirect.php';
require_once MAX_PATH . '/lib/OX/Upgrade/InstallPlugin/Controller.php';
require_once MAX_PATH . '/lib/OX/Upgrade/PostUpgradeTask/Controller.php';

// required files for header & nav
require_once MAX_PATH.'/lib/JSON/JSON.php';
require_once MAX_PATH . '/lib/max/Admin/Languages.php';
require_once MAX_PATH . '/lib/max/language/Loader.php';
require_once MAX_PATH . '/lib/OA/Upgrade/Upgrade.php';
require_once MAX_PATH . '/lib/OA/Upgrade/Login.php';
require_once MAX_PATH . '/lib/OA/Permission.php';
require_once MAX_PATH . '/lib/OA/Admin/Template.php';
require_once MAX_PATH . '/lib/OA/Admin/Option.php';
require_once MAX_PATH . '/lib/OA/Upgrade/UpgradePluginImport.php';
require_once MAX_PATH . '/lib/OX/Admin/Timezones.php';
require_once MAX_PATH . '/www/admin/lib-gui.inc.php';

/**
 * @package OX_Admin_UI
 * @subpackage Install
 * @author Bernard Lange <bernard@openx.org> 
 */
class OX_Admin_UI_Install_InstallController 
    extends OX_Admin_UI_Controller_BaseController
{
    /**
     * @var OA_Upgrade
     */
    private $oUpgrader;
    private $oTranslation;
    
    /**
     * @var OX_Admin_UI_Install_InstallStatus
     */
    private $oInstallStatus;
    

    /**
     * Returns a list of action supported by this controller
     *
     * @return array an array of actions
     */
    protected function getRegisteredActions()
    {
        return array('checkSsoNameAvailable', 'check', 'configuration', 'database', 
            'error', 'finish', 'index', 'jobs','login', 'recovery', 'register', 'registerConfirm', 
            'restart', 'uptodate', 'welcome');
    }


    /**
     * Called before any action gets executed
     */
    protected function init()
    {
        // No upgrade file? No installer! Unless the user is in the last step
        if (!file_exists(MAX_PATH . '/var/UPGRADE') && 'finish' != $_REQUEST['action']) { 
            header("Location: index.php");
            exit();
        }
        @set_time_limit(0);
        
        //  load translations for installer
        Language_Loader::load('installer');
        Language_Loader::load('default');
        Language_Loader::load('settings');
        Language_Loader::load('settings-help');        

        // Setup oUpgrader
        $this->initUpgrader();
        $this->oTranslation = new OX_Translation();
        
        // clear the $session variable to prevent users pretending to be logged in.
        global $session;
        unset($session);
        $this->initInstallStatus();

        //check if market is required, if login is required (this will be used by wizard)
        $this->initStepConfig();

        //check if recovery required  
        $oRequest = $this->getRequest(); 
        if ($this->oInstallStatus->isRecovery() 
            && $oRequest->getParam('action') != 'recovery') {
            //if recovery required and not recovering already, force recovery to be started
            $oRequest->setParam('action', 'recovery');
        }
        
        parent::init();
    }

    
    protected function initUpgrader()
    {
        $this->oUpgrader = new OA_Upgrade();        
    }
    
    
    protected function initInstallStatus($forceInit = false)
    {
        $oStorage = OX_Admin_UI_Install_InstallUtils::getSessionStorage(); 
        $oStatus = $oStorage->get('installStatus');
        //initialize status if :
        // 1) it's null
        // 2) recovery was recently discovered
        // 3) upgrader reports need for recovery
        // AD1) If recovery was finished we need to give installer a chanse to 
        //      reset to new state after recovery
        // AD2) If upgrader requires recovery we need to enforce it
        if ($oStatus == null || $oStatus->isRecovery()
            || $this->oUpgrader->isRecoveryRequired() || $forceInit) {
            //ask upgrader to determine installation status, also reset any data
            //stored by wizard
            $oStatus = new OX_Admin_UI_Install_InstallStatus($this->oUpgrader);
            $oStorage->set('installStatus', $oStatus);
            $oWizard = new OX_Admin_UI_Install_Wizard($oStatus);
            $oWizard->reset();

            // Rebild component hooks to avoid problems with previous plugin installation  

            require_once(LIB_PATH.'/Extension/ExtensionCommon.php');

            $oExtensionManager = new OX_Extension_Common();

            $oExtensionManager->cacheComponentHooks();
        }
        $this->oInstallStatus = $oStatus;
    }
    
    
    /**
     * Initialises step configuration settings used by upgrade path.
     * 
     * Checks if market has been registered and stores the status in session.
     * Checks if user is logged in and stores the status in session.
     * Checks if config step needs to be shown and and stores the status in session.
     * 
     * Note that eg. if market was not registered and wizard reached
     * step when it will be registered, the value here will not change,
     * we do not want to remove the register step.
     * 
     * However, if eg. user was logged in when he started upgrader, and then the 
     * session expired, the value will be upgraded.
     * 
     * In other words: 
     * - transition from true => false is blocked
     * - transition from false => true is allowed
     *
     * @param boolean $forceInit
     */
    protected function initStepConfig($forceInit = false)
    {
        $oStorage = OX_Admin_UI_Install_InstallUtils::getSessionStorage();

        //check if market step should be shown
        $isMarketStepVisible = $oStorage->get('isMarketStepVisible');

        if (!isset($isMarketStepVisible) || !$isMarketStepVisible || $forceInit) {
             $isMarketStepVisible = OX_Dal_Market_MarketPluginTools
                    ::isRegistrationRequired();
			$oStorage->set('isMarketStepVisible', $isMarketStepVisible);
        }
        
        //check if login step should be shown
        $isLoginStepVisible = $oStorage->get('isLoginStepVisible');
        if (!isset($isLoginStepVisible) || !$isLoginStepVisible || $forceInit) {
            $isLoginStepVisible = !OA_Upgrade_Login::checkLogin();
            $oStorage->set('isLoginStepVisible', $isLoginStepVisible);
        }
        
        //check if config should be shown        
        $isConfigStepVisible = $oStorage->get('isConfigStepVisible');
        if (!isset($isConfigStepVisible) || !$isConfigStepVisible || $forceInit) {
            $aVerifyResult = OX_Admin_UI_Install_InstallUtils::checkPluginsVerified();
            $isConfigStepVisible = !$aVerifyResult['verified'];
            $oStorage->set('isConfigStepVisible', $isConfigStepVisible);
        }
   }
    
    
    public function initModel()
    {
        parent::initModel();
        
        $oStatus = $this->getInstallStatus();
        
        if ($oStatus->isRecovery()) {        
            $pageTitle = $this->oTranslation->translate('InstallStatusRecovery', 
                array(OA_VERSION)); 
        }
        else if ($oStatus->isInstall()) {
            $pageTitle = $this->oTranslation->translate('InstallStatusInstall', 
                array(OA_VERSION));
        }
        else if ($oStatus->isUpgrade()) {
            $pageTitle = $this->oTranslation->translate('InstallStatusUpgrade', 
                array(OA_VERSION));        
        }
        else if ($oStatus->isUpToDate()) {
            $pageTitle = $this->oTranslation->translate('InstallStatusUpToDate', 
                array(OA_VERSION));        
        }
        $this->setModelProperty('pageHeader', new OA_Admin_UI_Model_PageHeaderModel($pageTitle));

        $aConfig = OX_Upgrade_InstallConfig::getConfig();
        $this->setModelProperty('marketPcHost', $aConfig['marketPcHost']);
    }
    

    public function indexAction()
    {
        //restart wizard if user goes directly to index.php without action param
        $this->forward('restart');
    }
    
    
    public function checkAction()
    {
        //force init of install status, since it may change between subsequent checks 
        //eg. if old config file was detected and causing problems, user might
        //remove that file and that would mean switching from upgrade to install path
        //do it before wizard is initializes, since it relies on status
        $this->initInstallStatus(true);
        
        //since we have not specified step name, it will assume first as current 
        //which is fine since check is part of first step in both upgrade and install
        $oWizard = new OX_Admin_UI_Install_Wizard($this->getInstallStatus());
        $this->setCurrentStepIfReachable($oWizard, 'welcome');
        $oUpgrader = $this->getUpgrader();
        
            
        $aSysInfo = $oUpgrader->checkEnvironment();
        $oBuilder = new OX_Admin_UI_Install_SystemCheckModelBuilder();
        $oCheckResults = $oBuilder->processEnvironmentCheck($aSysInfo);

        //retry if there are enviroment errors
        $hasErrors = $oCheckResults->hasError();
        
        // Check for an upgrade package 
        $canInstallOrUpgrade = $oUpgrader->canUpgradeOrInstall();
        $installStatus = $oUpgrader->existing_installation_status;
        if ($installStatus == OA_STATUS_CURRENT_VERSION) {
            //no retry required if current version detected
            $canInstallOrUpgrade = true;
        }

        //has errors if system check errors found or cannot install or upgrade
        $hasErrors = $hasErrors || !$canInstallOrUpgrade;
        
        //process upgrader errors
        $aMessages = $oUpgrader->getMessages();
        $oUpgraderResults = $oBuilder->processUpgraderMessages($aMessages, $installStatus);

        
        // - do not skip if there are errors
        // - if no errrors:  
        //   * if that's GET and there are are warnings show the screen
        //   * if that's POST and there are warnings that's fine, go further
        $oRequest = $this->getRequest();
        $canSkip = !$hasErrors && ($oRequest->isPost() 
            || ($oRequest->isGet() && !$oCheckResults->hasWarning())); 
        
        if ($canSkip) {
            $oWizard->markStepAsCompleted();
            $this->redirect($oWizard->getNextStep());
        }
        
        $this->setModelProperty('aChecks', 
            array_merge($oCheckResults->getSections(), $oUpgraderResults->getSections()));
        $this->setModelProperty('oWizard', $oWizard);
        $this->setModelProperty('needsRetry', !$canSkip);
        $this->setModelProperty('loaderMessage', $GLOBALS['strSyscheckProgressMessage']);
    }
    
    
    public function welcomeAction()
    {
        $oWizard = new OX_Admin_UI_Install_Wizard($this->getInstallStatus());
        $this->setCurrentStepIfReachable($oWizard, 'welcome');            
            
        $oRequest = $this->getRequest();
        if ($oRequest->isPost() && $oRequest->getParam('action') == 'welcome') {
            $this->redirect('check');
        }
        
        $this->setModelProperty('isUpgrade', $this->getInstallStatus()->isUpgrade());
        $this->setModelProperty('oWizard', $oWizard);
        $this->setModelProperty('loaderMessage', $GLOBALS['strSyscheckProgressMessage']);
        $this->setModelProperty('oxVersion', OA_VERSION);
    }
    
    
    public function loginAction()
    {
        $oWizard = new OX_Admin_UI_Install_Wizard($this->getInstallStatus(), 'login');
        $oForm = new OX_Admin_UI_Install_AdminLoginForm($this->oTranslation, 
            $oWizard->getCurrentStep());
        
        if ($oForm->validate()) {
            if (!OA_Upgrade_Login::checkLogin()) {
                $this->setModelProperty('aMessages', 
                    array('error' => array($GLOBALS['strUsernameOrPasswordWrong'])));
            }
            else {
                $oWizard->markStepAsCompleted();
                $this->redirect($oWizard->getNextStep());
            }
        }
        
        $this->setModelProperty('form', $oForm->serialize());
        $this->setModelProperty('oWizard', $oWizard);
        $this->setModelProperty('loaderMessage', $GLOBALS['strLoginProgressMessage']);
        $this->setModelProperty('isUpgrade', $this->getInstallStatus()->isUpgrade());
    }
    
    
    public function registerAction()
    {
        $oWizard = new OX_Admin_UI_Install_Wizard($this->getInstallStatus());
        $this->setCurrentStepIfReachable($oWizard, 'register');
		if(!OX_Dal_Market_MarketPluginTools::isMarketPluginEnabledOrRegistrationRequired()) {
            $oWizard->markStepAsCompleted();
            $this->redirect($oWizard->getNextStep());
            $oWizard->markStepAsCompleted();
		}
            
        //generate hash and store in session
        $aStepData = $oWizard->getStepData();
        if (empty($aStepData)) {
            $oPlatformHashManager = new OX_Upgrade_Util_PlatformHashManager();
            $platformHash = $oPlatformHashManager->getPlatformHash();
            $aStepData = array(
                'platformHash' => $platformHash,
                'updates_signup' => 1
            );
            
            //store defaults in session
            $oWizard->setStepData($aStepData);
        }
        $oRequest = $this->getRequest();
        $skipRegistration = $oRequest->getParam('skipRegistration');
        if(!empty($skipRegistration)) {
            $oWizard->markStepAsCompleted();
            $this->redirect($oWizard->getNextStep());
            $oWizard->markStepAsCompleted();
        }
        
        $aConfig = OX_Upgrade_InstallConfig::getConfig();
        $captchaUrl = $aConfig['marketCaptchaUrl'];
        $oMarketClient = OX_Upgrade_MarketClientFactory::getMarketClient($aStepData['platformHash']);
                        
        $captchaRandom = mktime();
        $oSignupForm = new OX_Admin_UI_Install_SsoSignupForm($this->oTranslation, $oWizard->getCurrentStep(), 
            $captchaUrl, $captchaRandom, $oMarketClient, $aStepData['platformHash']);
        $oSignupForm->setDefaults($aStepData);
        $oLoginForm = new OX_Admin_UI_Install_SsoLoginForm($this->oTranslation, $oWizard->getCurrentStep(), $aStepData['platformHash']);          
        $oLoginForm->setDefaults($aStepData);
        
        $oSkipSsoForm = new OX_Admin_UI_Install_SkipSsoForm($this->oTranslation, $oWizard->getCurrentStep());

        if ($oRequest->isPost()) {
            $valid = $this->processRegisterAction($oMarketClient, $oWizard, $oSignupForm, $oLoginForm);
            
            if ($valid) {
                $oWizard->markStepAsCompleted();
                if ($oSignupForm->isSubmitted()) {
                    $this->redirect('registerConfirm');
                }
                else {
                    $this->redirect($oWizard->getNextStep());
                }
            }
            //if there were errors we need to discard old captcha random and use
            //the new on generated for this request
            if ($oSignupForm->isSubmitted()) {
                $oSignupForm->updateCaptcha($captchaRandom);
            }
        }

        
        
        $submit = $oSignupForm->isSubmitted() || $oLoginForm->isSubmitted();
        $isSignup = $oSignupForm->isSubmitted(); 
        
        $this->setModelProperty('signupForm', $oSignupForm->serialize());
        $this->setModelProperty('loginForm', $oLoginForm->serialize());
        $this->setModelProperty('skipSsoForm', $oSkipSsoForm->serialize());
        $this->setModelProperty('oxVersion', OA_VERSION);
        $this->setModelProperty('oWizard', $oWizard);
        $loadingMessage = $GLOBALS['strRegisterProgressMessage'];
        if($this->getModelProperty('showSkipSsoForm')) {
            $loadingMessage = $GLOBALS['strLoadingDatabaseConfiguration'];
        }
        $this->setModelProperty('loaderMessage', $loadingMessage);
        $this->setModelProperty('accountMode', $submit ? ($isSignup ? 'signup' : 'login') : 'signup');
        $this->setModelProperty('isUpgrade', $this->getInstallStatus()->isUpgrade());
    }
    
    
    public function registerConfirmAction()
    {
        //its the part of register step, thus same id
        $oWizard = new OX_Admin_UI_Install_Wizard($this->getInstallStatus());        
        $this->setCurrentStepIfReachable($oWizard, 'register');            

        $oRequest = $this->getRequest();
        if ($oRequest->isPost()) {
            $this->redirect($oWizard->getNextStep());
        }
        
        $this->setModelProperty('oWizard', $oWizard);
        $this->setModelProperty('isUpgrade', $this->getInstallStatus()->isUpgrade());
    }
    
    
    public function databaseAction()
    {
        $oWizard = new OX_Admin_UI_Install_Wizard($this->getInstallStatus());
        $this->setCurrentStepIfReachable($oWizard, 'database');
        $oUpgrader = $this->getUpgrader();
        $isUpgrade = $this->getInstallStatus()->isUpgrade();                    
    
        //setup form
        $aDbTypes = OX_Admin_UI_Install_InstallUtils::getSupportedDbTypes();
        $aTableTypes = OX_Admin_UI_Install_InstallUtils::getSupportedTableTypes();
        $hasZoneError = $isUpgrade && OX_Admin_UI_Install_InstallUtils::hasZoneError($oUpgrader);
        
        $oForm = new OX_Admin_UI_Install_DbForm($this->oTranslation, $oWizard->getCurrentStep(),
            $aDbTypes, $aTableTypes, $isUpgrade, $hasZoneError);
        
        //populate form with defaults from upgrader dsn
        $oUpgrader->canUpgradeOrInstall(); //need to call upgrade, otherwise no db data will be available
        $aConfig = $oUpgrader->aDsn;
        $aConfig['detectedVersion'] = $oUpgrader->getProductApplicationVersion(); 
        
        //get default socket
        if (empty($aConfig['database']['socket']) && $aConfig['database']['type'] == 'mysql') {
            $aConfig['database']['socket'] = str_replace("'", '', ini_get('mysql.default_socket'));
        }
        $oForm->populateForm($aConfig);
        
        //process the request
        if ($oForm->isSubmitted() && $oForm->validate()) {
            if ($this->processDatabaseAction($oForm, $oWizard)) {
                $oWizard->markStepAsCompleted();
                if (!$oWizard->isStep('configuration')) {
                    $this->redirect('jobs');
                }
                else {
                    $this->redirect($oWizard->getNextStep());
                }
            }
        }
        
        $loaderMessage = $isUpgrade 
            ? $GLOBALS['strDbProgressMessageUpgrade'] 
            : $GLOBALS['strDbProgressMessageInstall'];
        
        $this->setModelProperty('isUpgrade', $isUpgrade);
        $this->setModelProperty('loaderMessage', $loaderMessage);
        $this->setModelProperty('form', $oForm->serialize());
        $this->setModelProperty('oWizard', $oWizard);
    }
    
    
    public function configurationAction()
    {
        $oWizard = new OX_Admin_UI_Install_Wizard($this->getInstallStatus());        
        $this->setCurrentStepIfReachable($oWizard, 'configuration');                        
        $oUpgrader = $this->getUpgrader();
        $isUpgrade = $this->getInstallStatus()->isUpgrade();
            
        //setup form
        $aPluginsVerifyResult = OX_Admin_UI_Install_InstallUtils::checkPluginsVerified(); 
        $prevPathRequired = !$aPluginsVerifyResult['verified'];
        $oLanguage = new MAX_Admin_Languages();
        $aLanguages = $oLanguage->AvailableLanguages();
        $aTimezones = OX_Admin_Timezones::AvailableTimezones(true);
        $oForm = new OX_Admin_UI_Install_ConfigForm($this->oTranslation, $oWizard->getCurrentStep(),
             $aLanguages, $aTimezones, $isUpgrade, $prevPathRequired);

        $aStepData = $oWizard->getStepData();
        
        //setup defaults
        if ($this->getRequest()->isGet() && empty($aStepData)) {
            $aConfig = $oUpgrader->getConfig();
            if ($prevPathRequired) {
                $aConfig['previousInstallationPath'] = $aPluginsVerifyResult['path'];
            }
            $aStepData['config'] = $aConfig;
            
            //admin part
            $aStepData['prefs'] = array();           
            $aStepData['prefs']['timezone'] = OX_Admin_Timezones::getTimezone();
            
            $aStepData['admin'] = array();
            $aStepData['admin']['language'] = 'en';
              //take admin email from registration step data 
            $registerStepData = $oWizard->getStepData('register');
            $aStepData['admin']['email'] = $registerStepData['email'];
        }

        //populate form
        $oForm->populateForm($aStepData);

        //process if install
        if ($oForm->isSubmitted () && $oForm->validate ()) {
            if ($this->processConfigurationAction($oForm, $oWizard, $isUpgrade)) {
                $aConfig = $oForm->populateConfig();
                $oWizard->setStepData($aConfig);
                $oWizard->markStepAsCompleted();
                $this->redirect('jobs');
            }
        }
        
        
        $this->setModelProperty('form', $oForm->serialize());
        $this->setModelProperty('oWizard', $oWizard);
        $this->setModelProperty('isUpgrade', $isUpgrade);
        $this->setModelProperty('loaderMessage', $GLOBALS['strConfigureProgressMessage']);        
        $this->setModelProperty('isUpgrade', $isUpgrade);
    }
    
    
    public function jobsAction()
    {
        $oWizard = new OX_Admin_UI_Install_Wizard($this->getInstallStatus());  
        if ($oWizard->isStep('configuration')) {
            //this will be visible under config wizard entry
            $current = 'configuration';
        }
        else {
            $current = 'database';
        }
        $this->setCurrentStepIfReachable($oWizard, $current);
                        
        $oUpgrader = $this->getUpgrader();
        $isUpgrade = $this->getInstallStatus()->isUpgrade();
        $oRequest = $this->getRequest();
        $oStorage = OX_Admin_UI_Install_InstallUtils::getSessionStorage();
        if ($oRequest->isGet()) {
            $oStorage->set('aJobStatuses', null);
        }
        
        if ($oRequest->isPost()) {
           $aJobErrors = $oRequest->getParam('jobError');
           //check if there were any PHP errors when executing jobs
           //these are kind of errros which task and plugin runners might be
           //unable to catch and report in session eg. fatal error, timeout etc. 
            if (!empty($aJobErrors)) { 
                //push any errors through session so they can be presented in next step
                $aJobStatuses = $oStorage->get('aJobStatuses');
                foreach ($aJobErrors as $id => $errMessage) {
                    $aJobStatuses[$id]['errors'][] = $errMessage;
                    list($type, $name) = explode(':', $id);
                    $aJobStatuses[$id]['name'] = $name;
                    $aJobStatuses[$id]['type'] = $type;
                }
                
                $oStorage->set('aJobStatuses', $aJobStatuses);
            }
            $this->redirect($oWizard->getNextStep());
        }
        
        
        // Perform auto-login at this stage, so that the install-plugin can verify
        if ($this->getInstallStatus()->isInstall()) {
            OA_Upgrade_Login::autoLogin();
        }
        
        // Use current url as base path for calling install-plugin
        $baseInstallUrl = $this->getRequest()->getBaseUrl();
        
        //collect tasks
        $aUrls = OX_Upgrade_InstallPlugin_Controller::getTasksUrls($baseInstallUrl);
        $aUrls = array_merge($aUrls, 
            OX_Upgrade_PostUpgradeTask_Controller::getTasksUrls($baseInstallUrl, $oUpgrader));

        $json = new Services_JSON();
        $jsonJobs = $json->encode($aUrls);

        $this->setModelProperty('oWizard', $oWizard);
        $this->setModelProperty('isUpgrade', $isUpgrade);
        $this->setModelProperty('jobs', $jsonJobs);
    }
    
    
    public function finishAction()
    {
        $oWizard = new OX_Admin_UI_Install_Wizard($this->getInstallStatus());
        $this->setCurrentStepIfReachable($oWizard, 'finish');        
        
        $oUpgrader = $this->getUpgrader();
        $isUpgrade = $this->getInstallStatus()->isUpgrade();        
        //finalize - mark OpenX as installed, clear files etc.
        $this->finalizeInstallation();
        
        $oStorage = OX_Admin_UI_Install_InstallUtils::getSessionStorage();
        //collect only job statuses with errors
        $aJobStatuses = $oStorage->get('aJobStatuses');
        $aStatuses = array(); 
        if ($aJobStatuses) {
            foreach ($aJobStatuses as $jobId => $aJobStatus) { //check session for job statuses
                if (!empty($aJobStatus['errors'])) {
                    $aStatuses[$jobId] = $aJobStatus;
                }
            }
        }
        
        $oRequest = $this->getRequest();
        if ($oRequest->isPost()) {
            $this->resetInstaller();
            
            global $installerIsUpgrade;
            $installerIsUpgrade = $isUpgrade;
            // Execute any components which have registered at the afterLogin hook
            $aPlugins = OX_Component::getListOfRegisteredComponentsForHook('afterLogin');
            foreach ($aPlugins as $i => $id) {
                if ($obj = OX_Component::factoryByComponentIdentifier($id)) {
                    $obj->afterLogin();
                }
            }
            require_once LIB_PATH . '/Admin/Redirect.php';

            if(defined('OX_MARKET_LIB_PATH')) {
                // if the market plugin was installed and hasn't yet been linked to PC (failed connectivity)
                // we link to the Market info page after the install
                require_once OX_MARKET_LIB_PATH . '/OX/oxMarket/Dal/Installer.php';
                $oMarketComponent = OX_Component::factory('admin', 'oxMarket');
    			if($oMarketComponent
    			    && OX_oxMarket_Dal_Installer::isRegistrationRequired()) {
    			    OX_Admin_Redirect::redirect('plugins/' . $oMarketComponent->group . '/market-info.php');
    			}
            }
            OX_Admin_Redirect::redirect('advertiser-index.php');
        }
        
        $logPath = str_replace('/', DIRECTORY_SEPARATOR, $oUpgrader->getLogFileName());
        
        $this->setModelProperty('logPath', $logPath);
        $this->setModelProperty('oWizard', $oWizard);
        $this->setModelProperty('aStatuses', $aStatuses);
        $this->setModelProperty('isUpgrade', $isUpgrade);
    }
    
    
    public function uptodateAction()
    {
        $this->finalizeInstallation();
                
        if ($this->getRequest()->isPost()) {
            $this->resetInstaller();
            require_once LIB_PATH . '/Admin/Redirect.php';
            OX_Admin_Redirect::redirect('advertiser-index.php');
        }
    }    
    
    
    public function recoveryAction() 
    {   
        $oRequest = $this->getRequest();
        $oUpgrader = $this->getUpgrader();     
        
        //nothing to do, go away
        if (!$oUpgrader->isRecoveryRequired()) {
            $this->forward('restart');
            return;
        }
        
        if ($oRequest->isPost() && $_POST['action'] == 'recovery') {
            $recoverySuccess = $oUpgrader->recoverUpgrade();
            if ($recoverySuccess) { //succes, restart wizard
                $this->forward('restart');
                return;
            }
            else {  //report errors
                $aMessages = OX_Admin_UI_Install_InstallUtils
                    ::getMessagesWithType($oUpgrader->getMessages());
                $this->setModelProperty('aMessages', $aMessages);
            }
        }
        
        //check if we were forwarded to recovery, maybe we already have
        //some messages or the recovery itself failed
        $aMessages = OX_Admin_UI_Install_InstallUtils
            ::getMessagesWithType($oUpgrader->getMessages());
        $this->setModelProperty('aMessages', $aMessages);
    }

    
    public function errorAction()
    {
        $oUpgrader = $this->getUpgrader(); 
        $oUpgrader->canUpgradeOrInstall();
        $aMessages = OX_Admin_UI_Install_InstallUtils::getMessagesWithType($oUpgrader->getMessages());
        $this->setModelProperty('aMessages', $aMessages);
        
        if ($this->getRequest()->isPost()) {
            $this->forward('restart');
            return;
        }
    }
    
    
    public function restartAction()
    {
        $this->resetInstaller();
        
        //now reread the current install status and upgrade wizard accordingly
        $this->initUpgrader();
        $this->initInstallStatus(true);
        
        $this->initStepConfig(true);
        $oWizard = new OX_Admin_UI_Install_Wizard($this->getInstallStatus());
        
        $this->redirect($oWizard->getFirstStep());
    }
    
    
    public function checkSsoNameAvailableAction()
    {
        $this->disableLayout();
        $this->noViewScript();
        
        $userName = $this->getRequest()->getParam('userName');
        
        try {
            $oMarketClient = OX_Upgrade_MarketClientFactory::getMarketClient();
            $oMarketClient->isSsoUserNameAvailable($userName);
    
            $result = $oMarketClient->isSsoUserNameAvailable($userName) 
                ? "available" : "taken";
        }
        catch (Exception $exc) {
            header("HTTP/1.0 500 Server Error (Code: ".$exc->getCode().")");
            $this->getUpgrader()->getLogger()->logError(
                'Error during retrieving custom content: ('.$exc->getCode().')'.$exc->getMessage());
        }
        echo $result;
    }
    
    
    /**
     * Marks OpenX as installed, removes any TASKS file. Removes UPGRADE file.
     *
     */
    protected function finalizeInstallation()
    {
        $oUpgrader = $this->getUpgrader();
        $oUpgrader->setOpenadsInstalledOn();
        OX_Upgrade_PostUpgradeTask_Controller::cleanUpTaskListFile($oUpgrader);

        $upgradeFileRemoved = $oUpgrader->removeUpgradeTriggerFile();
        if (!$upgradeFileRemoved) {
            $this->setModelProperty('aMessages', 
                    array('error' => array($GLOBALS['strOaUpToDateCantRemove'])));
        }        
    }
    
    
    /**
     * Reinitializes wizard objects, clears session, removes cookies
     */
    protected function resetInstaller()
    {
        $oWizard = new OX_Admin_UI_Install_Wizard($this->getInstallStatus());            
        $oWizard->reset();
        $oStorage = OX_Admin_UI_Install_InstallUtils::getSessionStorage();
        $oStorage->destroy();
        $this->oInstallStatus = null;        
    }
    
    
    /**
     * Attempts to set the given step as current. Before doing that, checks 
     * if current step  can be reached, ie.
     * - if it is a valid wizard step 
     * - if all previous steps have been marked as reached. 
     * 
     * If step is not a valid step it will redirect to first wizard step.
     * 
     * If step is valid, but is not reachable yet, it will redirect to
     * first unreached step.
     *
     * @param OX_Admin_UI_Install_Wizard $oWizard
     */
    protected function setCurrentStepIfReachable($oWizard, $stepId)
    {
        //check if given step is valid, if not, direct user to index
        if (!$oWizard->isStep($stepId)) {
            $this->redirect($oWizard->getFirstStep());
        }
        
        $oWizard->setCurrentStep($stepId);
        $reachable = $oWizard->checkStepReachable();

        //reachable is fine, check if not secured
        if ($reachable) {
            $aMeta = $oWizard->getStepMeta();
            if ($aMeta['secured'] == true && !OA_Upgrade_Login::checkLogin()) {
                $this->redirect('login');
            }
            
            return true;
        }
        else {
            //if step is not reachable check the last one marked as completed
            //and redirect user to the next one
            $lastCompleted = $oWizard->getLastCompletedStep();
            if ($lastCompleted != null) {
                $forwardStep = $oWizard->getNextStep($lastCompleted);
            }
    
            if ($forwardStep == null) {
                $forwardStep = $oWizard->getFirstStep();             
            }
            
            $this->redirect($forwardStep);
        }
    }
    
    
    /**
     * Process user input and create SSO account or bind existing one to PC
     * @param OX_Dal_Market_RegistrationClient $oMarkerClient
     * @param OX_Admin_UI_Install_Wizard $oWizard
     * @param OX_Admin_UI_Install_SsoSignupForm $oSignupForm
     * @param OX_Admin_UI_Install_SsoLoginForm $oLoginForm
     */
    protected function processRegisterAction(OX_Dal_Market_RegistrationClient $oMarketClient, 
        OX_Admin_UI_Install_Wizard $oWizard, OX_Admin_UI_Install_SsoSignupForm $oSignupForm, 
        OX_Admin_UI_Install_SsoLoginForm $oLoginForm)
    {
        $isSuccess = false;
        
        try {
			if($this->getInstallStatus()->isUpgrade()) {
				$oMarketComponent = OX_Component::factory('admin', 'oxMarket');
				$oMarketClient = $oMarketComponent->getPublisherConsoleApiClient();
			}
            if ($oSignupForm->validate()) {
                $aData = $oSignupForm->populateAccountData();
                $pcAccountData = $oMarketClient->createAccount($aData['email'], $aData['username'], 
                    $aData['password'], $aData['captcha'], $aData['captchaRandom']);
                $isSuccess = true; 
            }
            else if ($oLoginForm->validate()) {
                $aData = $oLoginForm->populateAccountData();
			    $pcAccountData = $oMarketClient->createAccountBySsoCred($aData['username'], $aData['password']);
			    
                $isSuccess = true;
            }
			if($this->getInstallStatus()->isUpgrade()) {
			    $oMarketComponent->removeRegisterNotification();
			}
            $aStepData = array_merge($aData, $pcAccountData);
            unset($aStepData['username']); //unset sensitive data              
            unset($aStepData['password']);
            
            $oWizard->setStepData($aStepData);
        }
        catch (Exception $exc) {
            $this->getUpgrader()->getLogger()->logError(
                'Error during Market signup: ('.$exc->getCode().')'.$exc->getMessage());
            $oBuilder = new OX_Admin_UI_Install_SsoErrorBuilder($aConfig['publisherSupportEmail']);
            $message = $oBuilder->getErrorMessage($exc); 
            
			// for "acceptable" error codes (invalid captcha, user already taken, etc.) we do not show the "Skip the registration" button
            $acceptableErrorCodes = $oBuilder->getAcceptableErrorCodes();
            $errorCode = $exc->getCode();
            if(!in_array($errorCode, $acceptableErrorCodes)) {
                $this->setModelProperty('showSkipSsoForm', true);
            }
            $this->setModelProperty('aMessages', 
                array('error' => array($message)));
        }
        
        
        return $isSuccess;
    }    
    
    
    /**
     * Process input from user and creates/upgrades DB etc....
     *
     * @param OA_Admin_UI_Component_Form $oForm

     * @param OX_Admin_UI_Install_Wizard $oWizard
     */
    protected function processDatabaseAction($oForm, $oWizard)
    {
        $oUpgrader = $this->getUpgrader();
        $upgraderSuccess = false;
        
        $aDbConfig = $oForm->populateDbConfig();
        if ($oUpgrader->canUpgradeOrInstall()) {
            $installStatus = $oUpgrader->existing_installation_status;
            define('DISABLE_ALL_EMAILS', 1);
            OA_Permission::switchToSystemProcessUser('Installer');
    
            if ($installStatus == OA_STATUS_NOT_INSTALLED) {
                
                if ($oUpgrader->install($aDbConfig)) {
                    $message = $GLOBALS['strDBInstallSuccess']; 
                    $upgraderSuccess = true;
                }
            }
            else {
                if ($oUpgrader->upgrade($oUpgrader->package_file)) {
                    // Timezone support - hack
                    if ($oUpgrader->versionInitialSchema['tables_core'] < 538 
                        && empty($aDbConfig['noTzAlert'])) {
                        OA_Dal_ApplicationVariables::set('utc_update', OA::getNowUTC());
                    }
    
                    // Clear the menu cache to built a new one with the new settings
                    OA_Admin_Menu::_clearCache(OA_ACCOUNT_ADMIN);
                    OA_Admin_Menu::_clearCache(OA_ACCOUNT_MANAGER);
                    OA_Admin_Menu::_clearCache(OA_ACCOUNT_ADVERTISER);
                    OA_Admin_Menu::_clearCache(OA_ACCOUNT_TRAFFICKER);
                    OA_Admin_Menu::singleton();
                    
                    $message = $GLOBALS['strDBUpgradeSuccess'];
                    $upgraderSuccess = true;
                }
            }
            OA_Permission::switchToSystemProcessUser(); //get back to normal user previously logged in
        }
        else if ($oUpgrader->existing_installation_status == OA_STATUS_CURRENT_VERSION) {
            $upgraderSuccess = true; //rare but can occur if DB has been installed and user revisits the screen
        }
        
        $dbSuccess = $upgraderSuccess && !$oUpgrader->oLogger->errorExists; 
        if ($dbSuccess) { //show success status

            // Store market registration data

            $registerStepData = $oWizard->getStepData('register');

            OX_Dal_Market_MarketPluginTools::storeMarketAccountAssocData(

                $registerStepData['accountUuid'], $registerStepData['apiKey']);

            OA_Admin_UI::getInstance()->queueMessage($message, 'global', 'info');
        }
        else { //sth went wrong, display messages from upgrader 
            $aMessages = OX_Admin_UI_Install_InstallUtils::getMessagesWithType($oUpgrader->getMessages());
            $this->setModelProperty('aMessages', $aMessages);
        }
        
        return $dbSuccess;
    }
    
    
    /**
     * Process input from user and creates admin saves path in config file
     *
     * @param OA_Admin_UI_Component_Form $oForm
     * @param OX_Admin_UI_Install_Wizard $oWizard
     */
    protected function processConfigurationAction($oForm, $oWizard, $isUpgrade)
    {
        if ($isUpgrade) {
            $aConfig['config'] = $this->getUpgrader()->getConfig();            
            $aUpgradeConfig = $oForm->populateConfig(); //not much here, just previous path
            $previousInstallationPath = $aUpgradeConfig['config']['previousInstallationPath']; 
        }
        else {
            $aConfig = $oForm->populateConfig();
        }
        
        $oUpgrader = $this->getUpgrader();
        $isUpgrade = $this->getInstallStatus()->isUpgrade();
        $registerStepData = $oWizard->getStepData('register');
        $configStepSuccess = true;
        $syncEnabled = true;
        
        //1) Import any plugins present from the previous install
        $path = isset($previousInstallationPath) 
            ? $previousInstallationPath : '';
        $path = get_magic_quotes_gpc() ? stripslashes($path) : $path;
        if ($path && ($path != MAX_PATH)) {
            $importOK = OX_Admin_UI_Install_InstallUtils::importPlugins($path);
            if (!$importOK) {
                $errMessage = $GLOBALS['strPathToPreviousError'];
                $configStepSuccess = false;
            }
        }
        
        //2) Save config (if previous task was fine)
        if ($configStepSuccess) {         
            if ($oUpgrader->saveConfig($aConfig['config']) 
                && $oUpgrader->putSyncSettings($syncEnabled, $registerStepData['platformHash'])) {
                $configStepSuccess = true;
                if (!OX_Admin_UI_Install_InstallUtils::checkFolderPermissions($aConfig['config']['store']['webDir'])) {
                    $errMessage = $GLOBALS['strImageDirLockedDetected'];
                    $configStepSuccess = false;
                } 
            }
            else {
                if ($isUpgrade) {
                    $errMessage = $GLOBALS['strUnableUpdateConfFile'];
                } 
                else {
                    $errMessage = $GLOBALS['strUnableCreateConfFile'];
                }
            }
        }
        
        //3) config saved, go ahead and save admin when new install
        if ($configStepSuccess && !$isUpgrade) {
            OA_Permission::switchToSystemProcessUser('Installer');
            // we set the default from: in OpenX emails to the administrator's email
            if (empty($GLOBALS['_MAX']['CONF']['email']['fromAddress'])) {
                $oUpgrader->oConfiguration->setValue('email', 'fromAddress', $aConfig['admin']['email']);
                $oUpgrader->oConfiguration->writeConfig(true);
            }
            // Save admin credentials
            $adminSaved = $oUpgrader->putAdmin($aConfig['admin'], $aConfig['prefs']);
            if (!$adminSaved) {
                $errMessage = $GLOBALS['strUnableToCreateAdmin'];
                $configStepSuccess = false;
            }
            OA_Permission::switchToSystemProcessUser(); //get back to normal user previously logged in
        }
        
        //4) Register admin for updates
        if ($configStepSuccess && !$isUpgrade) {
            //register admin email for updates (need to take value from register step for that)
            require_once MAX_PATH . '/lib/max/other/lib-io.inc.php';
            require_once MAX_PATH . '/lib/OA/Sync.php';
            // The opt box is ticked...
            if ($registerStepData['updates_signup']) {
                OA_Dal_ApplicationVariables::set('sync_registered_email', $aConfig['admin']['email']);
                    $oSync = new OA_Sync();
                    // Ignore errors, if any
                    OA::disableErrorHandling();
                    $oSync->checkForUpdates();
                    OA::enableErrorHandling();
            }
            else {
                // The opt button is unticked, clear any existing sync_registered_email
                OA_Dal_ApplicationVariables::delete('sync_registered_email', false);
            }
        }
        
        $configStepSuccess = $configStepSuccess && empty($errMessage); 
        
        if ($errMessage) {
            $this->setModelProperty('aMessages', array('error' => array($errMessage)));
        }
        
        return $configStepSuccess;
    }
    
    
    /**
     * Returns internal instance of OA_Upgrade upgrader class
     *
     * @return OA_Upgrade
     */
    protected function getUpgrader()
    {
        return $this->oUpgrader;
    }
    
    
    /**
     * Returns internal instance of InstallStatus holder
     *
     * @return InstallStatus
     */
    protected function getInstallStatus()
    {
        return $this->oInstallStatus;
    }    
    
}
?>