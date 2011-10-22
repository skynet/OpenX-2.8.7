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
$Id: zone-advanced.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/lib/max/Admin/Invocation.php';
require_once MAX_PATH . '/lib/max/other/html.php';
require_once MAX_PATH . '/lib/max/other/capping/lib-capping.inc.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/www/admin/lib-append.inc.php';
require_once MAX_PATH . '/www/admin/lib-statistics.inc.php';
require_once MAX_PATH . '/www/admin/lib-size.inc.php';
require_once MAX_PATH . '/www/admin/lib-zones.inc.php';
require_once MAX_PATH .'/lib/OA/Admin/UI/component/Form.php';
require_once MAX_PATH . '/lib/OA/Admin/Template.php';


// Register input variables
phpAds_registerGlobal (
     'append'
    ,'forceappend'
    ,'appendid'
    ,'appendsave'
    ,'appendtype'
    ,'chaintype'
    ,'chainzone'
    ,'prepend'
);


/*-------------------------------------------------------*/
/* Security check                                        */
/*-------------------------------------------------------*/

OA_Permission::enforceAccount(OA_ACCOUNT_MANAGER, OA_ACCOUNT_TRAFFICKER);
OA_Permission::enforceAccessToObject('affiliates', $affiliateid);
OA_Permission::enforceAccessToObject('zones', $zoneid);

if (OA_Permission::isAccount(OA_ACCOUNT_TRAFFICKER)) {
    OA_Permission::enforceAllowed(OA_PERM_ZONE_EDIT);
}

/*-------------------------------------------------------*/
/* Store preferences									 */
/*-------------------------------------------------------*/
$session['prefs']['inventory_entities'][OA_Permission::getEntityId()]['affiliateid'] = $affiliateid;
phpAds_SessionDataStore();

/*-------------------------------------------------------*/
/* Initialise data                                    */
/*-------------------------------------------------------*/
$doZones = OA_Dal::factoryDO('zones');
if ($doZones->get($zoneid)) {
    $aZone = $doZones->toArray();
}
// Determine appendtype
if (isset($appendtype)) {
    $aZone['appendtype'] = $appendtype;
}
else {
    $aZone['appendtype'] = phpAds_ZoneAppendRaw;
}
//extract chainzone
if (ereg("^zone:([0-9]+)$", $aZone['chain'], $regs)) {
    $aZone['chainzone'] = $regs[1];
}
else {
    $aZone['chainzone'] = '';
}

if (isset ( $GLOBALS ['_MAX'] ['CONF'] ['plugins'] ['openXMarket'] )
    && $GLOBALS ['_MAX'] ['CONF'] ['plugins'] ['openXMarket']) {
    $oComponent = &OX_Component::factory ( 'admin', 'oxMarket', 'oxMarket' );
}


/*-------------------------------------------------------*/
/* MAIN REQUEST PROCESSING                               */
/*-------------------------------------------------------*/
//build zone adv form
$zoneForm = buildZoneForm($aZone, $oComponent);

if ($zoneForm->validate()) {
    //process submitted values
    processForm($aZone, $zoneForm, $oComponent);
}
else { //either validation failed or form was not submitted, display the form
    displayPage($aZone, $zoneForm);
}

/*-------------------------------------------------------*/
/* Build form                                            */
/*-------------------------------------------------------*/
function buildZoneForm($aZone, $oComponent = null)
{
    $form = new OA_Admin_UI_Component_Form("zoneform", "POST", $_SERVER['SCRIPT_NAME']);
    $form->forceClientValidation(true);

    $form->addElement('hidden', 'zoneid', $aZone['zoneid']);
    $form->addElement('hidden', 'affiliateid', $aZone['affiliateid']);

    buildChainSettingsFormSection($form, $aZone);
    
    if ($oComponent && method_exists($oComponent, 'extendZoneAdvancedForm')) {
        $oComponent->extendZoneAdvancedForm($form, $aZone);
    }    
    buildDeliveryCappingFormSection($form, $GLOBALS['strCappingZone'], $aZone);
    buildAppendFormSection($form, $aZone);
    buildAlgorithmFormSection($form, $aZone);

    //we want submit to be the last element in its own separate section
    $form->addElement('controls', 'form-controls');
    $form->addElement('submit', 'submit', $GLOBALS['strSaveChanges']);


    //set form  values
    $form->setDefaults($aZone);
    $form->setDefaults(array('chaintype' => ($aZone['chain'] == '' ? 0 : 1)));

    //appendinterstitial i appendpopup
    if ($appendid == $k)

    return $form;
}


function buildChainSettingsFormSection($form, $aZone)
{
    $form->addElement('header', 'header_chain', $GLOBALS['strChainSettings']);

    $chainGroup[] = $form->createElement('radio', 'chaintype', null,
        $GLOBALS['strZoneStopDelivery'], 0, array('id' => 'chaintype-s'));
    $chainGroup[] = $form->createElement('radio', 'chaintype', null,
        $GLOBALS['strZoneOtherZone'], 1, array('id' => 'chaintype-z'));
    $chainGroup[] =$form->createElement('select', 'chainzone', _getChainZonesImage($aZone),
        _getChainZones($aZone), array('id'=> 'chainzone', 'class' => 'medium'));
    $form->addDecorator('chainzone', 'tag', array('attributes' => array('id' => 'chain-zone-select',
            'class' => $aZone['chain']=='' ? 'hide' : '')));

    $form->addGroup($chainGroup, 'g_chain', $GLOBALS['strZoneNoDelivery'], array("<BR>", '', ''));
}


function buildAppendFormSection($form, $aZone)
{
    if ($aZone['delivery'] == phpAds_ZoneBanner || $aZone['delivery'] == phpAds_ZoneText) {
        $form->addElement('header', 'header_append', $GLOBALS['strAppendSettings']);
        $form->addElement('hidden', 'appendsave', 1);
        $form->addElement('hidden', 'appendtype', phpAds_ZoneAppendRaw);

        $form->addElement('advcheckbox', 'forceappend', null,
            $GLOBALS['strZoneAppendNoBanner'], null, array("f", "t"));
    }

    if ($aZone['delivery'] == phpAds_ZoneBanner) {
        $form->addElement('textarea', 'prepend', $GLOBALS['strZonePrependHTML'],
            array('class' => 'code x-large'));
        $form->addElement('textarea', 'append', $GLOBALS['strZoneAppend'],
            array('class' => 'code x-large'));
    }
    elseif ($aZone['delivery'] == phpAds_ZoneText ) {
        // It isn't possible to append other banners to text zones, but
        // it is possible to prepend and append regular HTML code for
        // determining the layout of the text ad zone
        $form->addElement('textarea', 'prepend', $GLOBALS['strZonePrependHTML'],
                array('class' => 'code x-large'));

        $form->addElement('textarea', 'append', $GLOBALS['strZoneAppend'],
                array('class' => 'code x-large'));
    }
}

function buildAlgorithmFormSection($form, $aZone)
{
    $aAlgorithmPlugins = OX_Component::getComponents('deliveryAdSelect');
    if (!empty($aAlgorithmPlugins) && is_array($aAlgorithmPlugins)) {
        // Add the 'Default' (internal) algorithm to the list
        $aItems = array('none' => 'Default (internal)');
        foreach ($aAlgorithmPlugins as $oAlgorithmPlugin) {
            // Only include components which implement the onDemand adselect hook function
            // This is not the cleanest way to do it :( but it works :)
            $aInfo = $oAlgorithmPlugin->parseComponentIdentifier($oAlgorithmPlugin->getComponentIdentifier());
            if (function_exists('Plugin_' . implode('_', $aInfo) . '_Delivery' . '_adSelect')) {
                $aItems[$oAlgorithmPlugin->getComponentIdentifier()] = $oAlgorithmPlugin->getName();
            }
        }
        // Only display the select box if at least one alternative algorithm is provided
        if (count($aItems) === 1) {
            $form->addElement('hidden', 'ext_adselection', 'none');
            return;
        }
        $form->addElement('header', 'header_algorithm', 'Ad selection algorithm');
        $form->addElement('select', 'ext_adselection', 'Plugin to use for ad selection in this zone', $aItems);
    }
}


/*-------------------------------------------------------*/
/* Process submitted form                                */
/*-------------------------------------------------------*/
function processForm($aZone, $form, $oComponent = null)
{
    $aFields = $form->exportValues();

    if (empty($aFields['zoneid'])) {
        return;
    }

    $doZones = OA_Dal::factoryDO('zones');
    $doZones->get($aFields['zoneid']);

    // Determine chain
    if ($aFields['chaintype'] == '1' && $aFields['chainzone'] != '') {
        $chain = 'zone:'.$aFields['chainzone'];
    }
    else {
        $chain = '';
    }
    $doZones->chain = $chain;

    if (!isset($aFields['prepend'])) {
        $aFields['prepend'] = '';
    }
    $aFields['prepend'] = MAX_commonGetValueUnslashed('prepend');
    $doZones->prepend = $aFields['prepend'];

    // Do not save append until not finished with zone appending, if present
    if (!empty($aFields['appendsave']))
    {
        if (!isset($aFields['append'])) {
            $aFields['append'] = '';
        }
        if (!isset($aFields['appendtype'])) {
            $aFields['appendtype'] = phpAds_ZoneAppendZone;
        }
        $aFields['append'] = MAX_commonGetValueUnslashed('append');

        $doZones->append = $aFields['append'];
        $doZones->appendtype = $aFields['appendtype'];
    }

    if (isset($aFields['forceappend'])) {
        $doZones->forceappend = $aFields['forceappend'];
    }

    $block = _initCappingVariables($aFields['time'], $aFields['capping'], $aFields['session_capping']);

    // Set adselection PCI if required
    if (isset($aFields['ext_adselection'])) {
        $doZones->ext_adselection = ($aFields['ext_adselection'] == 'none') ? OX_DATAOBJECT_NULL : $aFields['ext_adselection'];
    }

    $doZones->block = $block;
    $doZones->capping = $aFields['capping'];
    $doZones->session_capping = $aFields['session_capping'];
    if ($aFields['show_capped_no_cookie'] != 1) {
        $aFields['show_capped_no_cookie'] = 0;
    }
    $doZones->show_capped_no_cookie = $aFields['show_capped_no_cookie'];
    $doZones->update();

    // Queue confirmation message
    $translation = new OX_Translation ();
    $translated_message = $translation->translate ( $GLOBALS['strZoneAdvancedHasBeenUpdated'], array(
        MAX::constructURL(MAX_URL_ADMIN, 'zone-edit.php?affiliateid=' .  $aFields['affiliateid'] . '&zoneid=' . $aFields['zoneid']),
        htmlspecialchars($doZones->zonename)
    ));
    OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);

    // Rebuild Cache
    // require_once MAX_PATH . '/lib/max/deliverycache/cache-'.$conf['delivery']['cache'].'.inc.php';
    // phpAds_cacheDelete('what=zone:'.$zoneid);

    $oUI = OA_Admin_UI::getInstance();
    OX_Admin_Redirect::redirect("zone-advanced.php?affiliateid=".$aFields['affiliateid']."&zoneid=".$aFields['zoneid']);
}


/*-------------------------------------------------------*/
/* Display page                                          */
/*-------------------------------------------------------*/
function displayPage($aZone, $form)
{
    $pageName = basename($_SERVER['SCRIPT_NAME']);
    $agencyId = OA_Permission::getAgencyId();
    $aEntities = array('affiliateid' => $aZone['affiliateid'], 'zoneid' => $aZone['zoneid']);

    $aOtherPublishers = Admin_DA::getPublishers(array('agency_id' => $agencyId));
    $aOtherZones = Admin_DA::getZones(array('publisher_id' => $aZone['affiliateid']));
    MAX_displayNavigationZone($pageName, $aOtherPublishers, $aOtherZones, $aEntities);


    //get template and display form
    $oTpl = new OA_Admin_Template('zone-advanced.html');
    $oTpl->assign('form', $form->serialize());
    $oTpl->display();


    _echoDeliveryCappingJs();
    //footer
    phpAds_PageFooter();
}


function _getChainZones($aZone)
{
    // Get list of zones to link to
    $doZones = OA_Dal::factoryDO('zones');

    $allowothersizes = $aZone['delivery'] == phpAds_ZoneInterstitial
        || $aZone['delivery'] == phpAds_ZonePopup;
    if ($aZone['width'] != -1 && !$allowothersizes) {
        $doZones->width = $aZone['width'];
    }
    if ($aZone['height'] != -1 && !$allowothersizes) {
        $doZones->height = $aZone['height'];
    }
    $doZones->delivery = $aZone['delivery'];
    $doZones->whereAdd('zoneid <> '.$aZone['zoneid']);
    // Limit the list of zones to the appropriate list
    if (OA_Permission::isAccount(OA_ACCOUNT_MANAGER)) {
        $doAffiliates = OA_Dal::factoryDO('affiliates');
        $doAffiliates->agencyid = OA_Permission::getAgencyId();
        $doZones->joinAdd($doAffiliates);
    }
    else {
        $doZones->whereAdd('affiliateid = ' . $aZone['affiliateid']);
    }
    $doZones->find();

    $aChainZones = array();
    while ($doZones->fetch() && $row = $doZones->toArray()) {
        $aChainZones[$row['zoneid']] = $row['zonename'];
    }

    return $aChainZones;
}


function _getChainZonesImage($aZone)
{
    switch ($aZone['delivery']) {
        case phpAds_ZoneBanner : {
            $imageName = '/images/icon-zone.gif';
            break;
        }

        case phpAds_ZoneInterstitial : {
            $imageName = '/images/icon-interstitial.gif';
            break;
        }

        case phpAds_ZonePopup : {
            $imageName = '/images/icon-popup.gif';
            break;
        }

        case phpAds_ZoneText : {
            $imageName = '/images/icon-textzone.gif';
            break;
        }

        default: $imageName = '';
    }

    if ($imageName) {
        $image = "<img src='".OX::assetPath()."$imageName' align='absmiddle'>";
    }

    return $image;
}


function _getAppendZones($aZone)
{
    // Get list of zones to link to
    $doZones = OA_Dal::factoryDO('zones');

    $allowothersizes = $aZone['delivery'] == phpAds_ZoneInterstitial || $aZone['delivery'] == phpAds_ZonePopup;
    if ($aZone['width'] != -1 && !$allowothersizes) {
        $doZones->width = $aZone['width'];
    }
    if ($aZone['height'] != -1 && !$allowothersizes) {
        $doZones->height = $aZone['height'];
    }
    $doZones->delivery = $aZone['delivery'];
    $doZones->whereAdd('zoneid <> '.$aZone['zoneid']);
    $doZones->find();

    $available = array(phpAds_ZonePopup => array(), phpAds_ZoneInterstitial => array());
    while ($doZones->fetch() && $row = $doZones->toArray()) {
        $available[$row['delivery']][$row['zoneid']] = $row['zonename'];
    }

    return $available;
}


function _getAppendTypes($aZone)
{
    $aTypes = array();
    $available = _getAppendZones($aZone);

    // Appendtype choices
    $aTypes[phpAds_ZoneAppendRaw] = $GLOBALS['strZoneAppendHTMLCode'];
    if (count($available[phpAds_ZonePopup]) || count($available[phpAds_ZoneInterstitial])) {
        $aTypes[phpAds_ZoneAppendZone] = $GLOBALS['strZoneAppendZoneSelection'];
    }

    return $aTypes;
}
?>