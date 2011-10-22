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
$Id: zone-edit.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/www/admin/lib-statistics.inc.php';
require_once MAX_PATH . '/www/admin/lib-zones.inc.php';
require_once MAX_PATH . '/www/admin/lib-size.inc.php';
require_once MAX_PATH . '/lib/max/Admin_DA.php';
require_once MAX_PATH . '/lib/max/other/html.php';
require_once MAX_PATH .'/lib/OA/Admin/UI/component/Form.php';
require_once MAX_PATH . '/lib/OA/Central/AdNetworks.php';
require_once MAX_PATH . '/lib/OA/Admin/NumberFormat.php';


// Register input variables
phpAds_registerGlobalUnslashed(
    'zonename',
    'description',
    'delivery',
    'sizetype',
    'size',
    'width',
    'height',
    'submit',
    'comments'
);

/*-------------------------------------------------------*/
/* Affiliate interface security                          */
/*-------------------------------------------------------*/

OA_Permission::enforceAccount(OA_ACCOUNT_MANAGER, OA_ACCOUNT_TRAFFICKER);
OA_Permission::enforceAccessToObject('affiliates', $affiliateid);
OA_Permission::enforceAccessToObject('zones', $zoneid, true);

if (OA_Permission::isAccount(OA_ACCOUNT_TRAFFICKER)) {
    if (!empty($zoneid)) {
        OA_Permission::enforceAllowed(OA_PERM_ZONE_EDIT);
    } else {
        OA_Permission::enforceAllowed(OA_PERM_ZONE_ADD);
    }
}

/*-------------------------------------------------------*/
/* Store preferences									 */
/*-------------------------------------------------------*/
$session['prefs']['inventory_entities'][OA_Permission::getEntityId()]['affiliateid'] = $affiliateid;
phpAds_SessionDataStore();

/*-------------------------------------------------------*/
/* Initialise data                                       */
/*-------------------------------------------------------*/
if (!empty($zoneid)) {
    $doZones = OA_Dal::factoryDO('zones');
    $doZones->zoneid = $zoneid;
    if ($doZones->find() && $doZones->fetch()) {
        $zone = $doZones->toArray();
    }

    if ($zone['width'] == -1) $zone['width'] = '*';
    if ($zone['height'] == -1) $zone['height'] = '*';
}
else {
    $doAffiliates = OA_Dal::factoryDO('affiliates');
    $doAffiliates->affiliateid = $affiliateid;

    if ($doAffiliates->find() && $doAffiliates->fetch() && $affiliate = $doAffiliates->toArray())
        $zone["zonename"] = $affiliate['name'].' - ';
    else {
        $zone["zonename"] = '';
    }

    $zone['zonename']        .= $GLOBALS['strDefault'];
    $zone['description']     = '';
    $zone['width']           = '468';
    $zone['height']          = '60';
    $zone['delivery']        = phpAds_ZoneBanner;
    $zone['comments'] = null;
}
$zone['affiliateid']     = $affiliateid;


if (isset ( $GLOBALS ['_MAX'] ['CONF'] ['plugins'] ['openXMarket'] )
    && $GLOBALS ['_MAX'] ['CONF'] ['plugins'] ['openXMarket']) {
    $oComponent = &OX_Component::factory ( 'admin', 'oxMarket', 'oxMarket' );
}


/*-------------------------------------------------------*/
/* MAIN REQUEST PROCESSING                               */
/*-------------------------------------------------------*/
//build form
$zoneForm = buildZoneForm($zone, $oComponent);

if ($zoneForm->validate()) {
    //process submitted values
    $errors = processForm($zoneForm, $oComponent);

    if(!empty($errors)) {
    }
}
//display the page - show any validation errors that may have occured
displayPage($zone, $zoneForm, $errors);


/*-------------------------------------------------------*/
/* Build form                                            */
/*-------------------------------------------------------*/
function buildZoneForm($zone, $oComponent = null)
{
    global $conf;
    $newZone = empty($zone['zoneid']);    
    // Initialise Ad  Networks
    $oAdNetworks = new OA_Central_AdNetworks();

    $form = new OA_Admin_UI_Component_Form("zoneform", "POST", $_SERVER['SCRIPT_NAME']);
    $form->forceClientValidation(true);
    $form->addElement('hidden', 'zoneid', $zone['zoneid']);
    $form->addElement('hidden', 'affiliateid', $zone['affiliateid']);

    $form->addElement('header', 'zone_basic_info', $GLOBALS['strBasicInformation']);
    $form->addElement('text', 'zonename', $GLOBALS['strName']);
    $form->addElement('text', 'description', $GLOBALS['strDescription']);
    $form->addElement('select', 'oac_category_id', $GLOBALS['strCategory'], $oAdNetworks->getCategoriesSelect());

    //zone type group
    $zoneTypes[] = $form->createElement('radio', 'delivery', '',
        "<img src='".OX::assetPath()."/images/icon-zone.gif' align='absmiddle'>&nbsp;".$GLOBALS['strBannerButtonRectangle'],
        phpAds_ZoneBanner, array('id' => 'delivery-b',
            'onClick' => 'phpAds_formEnableSize();',
            'onChange' => 'oa_hide("warning_change_zone_type");'));
    if ($conf['oxInvocationTags']['isAllowedAdlayer'] || $zone['delivery'] == phpAds_ZoneInterstitial) {
        $zoneTypes[] = $form->createElement('radio', 'delivery', '',
            "<img src='".OX::assetPath()."/images/icon-interstitial.gif' align='absmiddle'>&nbsp;".$GLOBALS['strInterstitial'],
            phpAds_ZoneInterstitial, array('id' => 'delivery-i',
                'onClick' => 'phpAds_formEnableSize();',
                'onChange' => 'oa_hide("warning_change_zone_type");'));
    }
    if ($conf['oxInvocationTags']['isAllowedPopup'] || $zone['delivery'] == phpAds_ZonePopup) {
        $zoneTypes[] = $form->createElement('radio', 'delivery', '',
            "<img src='".OX::assetPath()."/images/icon-popup.gif' align='absmiddle'>&nbsp;".$GLOBALS['strPopup'],
            phpAds_ZonePopup, array('id' => 'delivery-p',
                'onClick' => 'phpAds_formEnableSize();',
                'onChange' => 'oa_hide("warning_change_zone_type");'));
    }
    $zoneTypes[] = $form->createElement('radio', 'delivery', '',
        "<img src='".OX::assetPath()."/images/icon-textzone.gif' align='absmiddle'>&nbsp;".$GLOBALS['strTextAdZone'],
        phpAds_ZoneText, array('id' => 'delivery-t', 'onClick' => 'phpAds_formDisableSize();',
            'onChange' => 'oa_hide("warning_change_zone_type");'));
    $zoneTypes[] = $form->createElement('radio', 'delivery', '',
        "<img src='".OX::assetPath()."/images/icon-zone-email.gif' align='absmiddle'>&nbsp;".$GLOBALS['strEmailAdZone'],
        MAX_ZoneEmail, array('id' => 'delivery-e', 'onClick' => 'phpAds_formEnableSize();',
            'onChange' => 'oa_hide("warning_change_zone_type");'));
    if (!empty($conf['allowedBanners']['video'])) {
        $zoneTypes[] = $form->createElement('radio', 'delivery', '',
            "<img src='".OX::assetPath()."/images/icon-zone-video-instream.png' align='absmiddle'>&nbsp;".$GLOBALS['strZoneVideoInstream'],
            OX_ZoneVideoInstream, array('id' => 'delivery-vi', 'onClick' => 'phpAds_formDisableSize();',
                'onChange' => 'oa_hide("warning_change_zone_type");'));
        $zoneTypes[] = $form->createElement('radio', 'delivery', '',
            "<img src='".OX::assetPath()."/images/icon-zone-video-overlay.png' align='absmiddle'>&nbsp;".$GLOBALS['strZoneVideoOverlay'],
            OX_ZoneVideoOverlay, array('id' => 'delivery-vo', 'onClick' => 'phpAds_formDisableSize();',
                'onChange' => 'oa_hide("warning_change_zone_type");'));
    }
    $form->addGroup($zoneTypes, 'zone_types', $GLOBALS['strZoneType'], "<br/>");

    //size
    global $phpAds_IAB;
    switch ($zone['delivery']) {
        case phpAds_ZoneText:
        case OX_ZoneVideoInstream:
        case OX_ZoneVideoOverlay:
            $sizeDisabled = true;
            $zone['width'] = '*';
            $zone['height'] = '*';
        break;
        default:
            $sizeDisabled = false;
        break;

    }

    $aDefaultSize['radio'] = $form->createElement('radio', 'sizetype', '', '',
        'default', array('id' => 'size-d'));
    foreach (array_keys($phpAds_IAB) as $key)
    {
        $iabSizes[$phpAds_IAB[$key]['width']."x".$phpAds_IAB[$key]['height']] =
            $GLOBALS['strIab'][$key];
    }
    $iabSizes['-'] = $GLOBALS['strCustom'];
    $aDefaultSize['select'] = $form->createElement('select', 'size', null, $iabSizes,
        array('onchange' => 'phpAds_formSelectSize(this); oa_sizeChangeUpdateMessage("warning_change_zone_size");', 'class' => 'medium'));


    $aCustomSize['radio'] = $form->createElement('radio', 'sizetype', '', '', 'custom',
        array('id' => 'size-c'));

    $aCustomSize['width'] = $form->createElement('text', 'width', $GLOBALS['strWidth'].':',
        array('onkeydown' => 'phpAds_formEditSize();',
            'onChange' => 'oa_sizeChangeUpdateMessage("warning_change_zone_size");'));
    $aCustomSize['width']->setSize(5);
    $aCustomSize['height'] = $form->createElement('text', 'height', $GLOBALS['strHeight'].':',
        array('onkeydown' => 'phpAds_formEditSize();',
            'onChange' => 'oa_sizeChangeUpdateMessage("warning_change_zone_size");'));
    $aCustomSize['height']->setSize(5);

    $sizeTypes['default'] = $form->createElement('group', 'defaultSizeG', null, $aDefaultSize, null, false);
    $sizeTypes['custom'] = $form->createElement('group', 'customSizeG', null, $aCustomSize, null, false);

    //disable fields if necessary
    if ($sizeDisabled) {
        $aDefaultSize['radio']->setAttribute('disabled', $sizeDisabled);
        $aDefaultSize['select']->setAttribute('disabled', $sizeDisabled);
        $aCustomSize['radio']->setAttribute('disabled', $sizeDisabled);
        $aCustomSize['width']->setAttribute('disabled', $sizeDisabled);
        $aCustomSize['height']->setAttribute('disabled', $sizeDisabled);
    }

    $form->addGroup($sizeTypes, 'size_types', $GLOBALS['strSize'], "<br/>");

    $form->addElement('textarea', 'comments', $GLOBALS['strComments']);
    
    if ($oComponent && method_exists($oComponent, 'extendZoneForm')) {
        $oComponent->extendZoneForm($form, $zone, $newZone);
    }
    
    $form->addElement('controls', 'form-controls');
    $form->addElement('submit', 'submit', $GLOBALS['strSaveChanges']);


    //validation rules
    $translation = new OX_Translation();
    $urlRequiredMsg = $translation->translate($GLOBALS['strXRequiredField'], array($GLOBALS['strName']));
    $form->addRule('zonename', $urlRequiredMsg, 'required');


    /*
    TODO
    max_formSetRequirements('width', '<?php echo addslashes($strWidth); ?>', true, 'number*');
    max_formSetRequirements('height', '<?php echo addslashes($strHeight); ?>', true, 'number*');
    */

    //set form values
    $form->setDefaults($zone);

        //sizes radio
    if (phpAds_sizeExists ($zone['width'], $zone['height'])) {
        $size = $zone['width']."x".$zone['height'];
        $sizeType = 'default';
    }
    else {
        $size = "-";
        $sizeType = 'custom';
    }
    $form->setDefaults(array('size' => $size, 'sizetype' => $sizeType));


    return $form;
}


/*-------------------------------------------------------*/
/* Process submitted form                                */
/*-------------------------------------------------------*/
/**
 * Processes submit values of zone form
 *
 * @param OA_Admin_UI_Component_Form $form form to process
 * @return An array of Pear::Error objects if any
 */
function processForm($form, $oComponent = null)
{
    $aFields = $form->exportValues();

    switch ($aFields['delivery']) {
        case phpAds_ZoneText:
            $aFields['width'] = $aFields['height'] = 0;
            break;
        case OX_ZoneVideoOverlay:
            $aFields['width'] = $aFields['height'] = -2;
            break;
        case OX_ZoneVideoInstream:
            $aFields['width'] = $aFields['height'] = -3;
            break;
        default:
            if ($aFields['sizetype'] == 'custom') {
                if (isset($aFields['width']) && $aFields['width'] == '*') {
                    $aFields['width'] = -1;
                }
                if (isset($aFields['height']) && $aFields['height'] == '*') {
                    $aFields['height'] = -1;
                }
            } else {
                list($aFields['width'], $aFields['height']) = explode ('x', $aFields['size']);
            }
        break;
    }

    if (!(is_numeric($aFields['oac_category_id'])) || ($aFields['oac_category_id'] <= 0)) {
            $aFields['oac_category_id'] = 'NULL';
    }

    if (empty($errors)) {

        // Edit
        if (!empty($aFields['zoneid']))
        {
            // before we commit any changes to db, store whether the size has changed
            $aZone = Admin_DA::getZone($aFields['zoneid']);
            $size_changed = ($aFields['width'] != $aZone['width'] || $aFields['height'] != $aZone['height']) ? true : false;
            $type_changed = ($aFields['delivery'] != $aZone['delivery']) ? true : false;

            $doZones = OA_Dal::factoryDO('zones');
            $doZones->zonename = $aFields['zonename'];
            $doZones->description = $aFields['description'];
            $doZones->width = $aFields['width'];
            $doZones->height = $aFields['height'];
            $doZones->comments = $aFields['comments'];
            $doZones->delivery = $aFields['delivery'];
            if ($aFields['delivery'] != phpAds_ZoneText && $aFields['delivery'] != phpAds_ZoneBanner) {
                $doZones->prepend = '';
                $doZones->append = '';
            }

            $doZones->oac_category_id  = $aFields['oac_category_id'];
            $doZones->zoneid = $aFields['zoneid'];
            $doZones->update();

            // Ad  Networks
            $doPublisher = OA_Dal::factoryDO('affiliates');
            $doPublisher->get($aFields['affiliateid']);
            $anWebsiteId = $doPublisher->as_website_id;
            if ($anWebsiteId) {
            	$oAdNetworks = new OA_Central_AdNetworks();
                $doZones->get($aFields['zoneid']);
    			$oAdNetworks->updateZone($doZones, $anWebsiteId);
            }

            // Reset append codes which called this zone
            $doZones = OA_Dal::factoryDO('zones');
            $doZones->appendtype = phpAds_ZoneAppendZone;

            if (OA_Permission::isAccount(OA_ACCOUNT_MANAGER))
            {
                $doZones->addReferenceFilter('agency', OA_Permission::getEntityId());
            }
            elseif (OA_Permission::isAccount(OA_ACCOUNT_TRAFFICKER))
            {
                  $doZones->addReferenceFilter('affiliates', OA_Permission::getEntityId());
            }
            $doZones->find();

            while ($doZones->fetch() && $row = $doZones->toArray())
            {
                $append = phpAds_ZoneParseAppendCode($row['append']);

                if ($append[0]['zoneid'] == $aFields['zoneid'])
                {
                    $doZonesClone = clone($doZones);
                    $doZonesClone->appendtype = phpAds_ZoneAppendRaw;
                    $doZonesClone->append = '';
                    $doZonesClone->update();
                }
            }

            if ($type_changed && $aFields['delivery'] == MAX_ZoneEmail) {
                // Unlink all campaigns/banners linked to this zone
                $aPlacementZones = Admin_DA::getPlacementZones(array('zone_id' => $aFields['zoneid']), true, 'placement_id');
                if (!empty($aPlacementZones)) {
                    foreach ($aPlacementZones as $placementId => $aPlacementZone) {
                        Admin_DA::deletePlacementZones(array('zone_id' => $aFields['zoneid'], 'placement_id' => $placementId));
                    }
                }
                $aAdZones = Admin_DA::getAdZones(array('zone_id' => $aFields['zoneid']), false, 'ad_id');
                if (!empty($aAdZones)) {
                    foreach ($aAdZones as $adId => $aAdZone) {
                        Admin_DA::deleteAdZones(array('zone_id' => $aFields['zoneid'], 'ad_id' => $adId));
                    }
                }
            }
            else if ($size_changed) {
                $aZone = Admin_DA::getZone($aFields['zoneid']);

                // Loop through all appended banners and make sure that they still fit...
                $aAds = Admin_DA::getAdZones(array('zone_id' => $aFields['zoneid']), false, 'ad_id');
                if (!empty($aAds)) {
                 foreach ($aAds as $adId => $aAd) {
                    $aAd = Admin_DA::getAd($adId);
                        if ( (($aZone['type'] == phpAds_ZoneText) && ($aAd['type'] != 'txt'))
                        || (($aAd['width'] != $aZone['width']) && ($aZone['width'] != -1))
                        || (($aAd['height'] != $aZone['height']) && ($aZone['height'] != -1)) ) {
                            Admin_DA::deleteAdZones(array('zone_id' => $aFields['zoneid'], 'ad_id' => $adId));
                        }
                    }
                }

                // Check if any campaigns linked to this zone have ads that now fit.
                // If so, link them to the zone.
                $aPlacementZones = Admin_DA::getPlacementZones(array('zone_id' => $aFields['zoneid']), true);
                if (!empty($aPlacementZones)) {
                    foreach($aPlacementZones as $aPlacementZone) {
                    // get ads in this campaign
                    $aAds = Admin_DA::getAds(array('placement_id' => $aPlacementZone['placement_id']), true);
                        foreach ($aAds as $adId => $aAd) {
                            Admin_DA::addAdZone(array('zone_id' => $aFields['zoneid'], 'ad_id' => $adId));
                        }
                    }
                }
            }
            if ($oComponent && method_exists($oComponent, 'processZoneForm')) {
                $oComponent->processZoneForm($aFields);
            }

            // Queue confirmation message
            $translation = new OX_Translation();
            $translated_message = $translation->translate ( $GLOBALS['strZoneHasBeenUpdated'],
                array(
                MAX::constructURL(MAX_URL_ADMIN, "zone-edit.php?affiliateid=".$aFields['affiliateid']."&zoneid=".$aFields['zoneid']),
                htmlspecialchars($aFields['zonename'])
                ));
            OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);

            OX_Admin_Redirect::redirect("zone-edit.php?affiliateid=".$aFields['affiliateid']."&zoneid=".$aFields['zoneid']);
        }
        // Add
        else
        {
            $doZones = OA_Dal::factoryDO('zones');
            $doZones->affiliateid = $aFields['affiliateid'];
            $doZones->zonename = $aFields['zonename'];
            $doZones->zonetype = phpAds_ZoneCampaign;
            $doZones->description = $aFields['description'];
            $doZones->comments = $aFields['comments'];
            $doZones->width = $aFields['width'];
            $doZones->height = $aFields['height'];
            $doZones->delivery = $aFields['delivery'];
            $doZones->oac_category_id  = $aFields['oac_category_id'];

            // The following fields are NOT NULL but do not get values set in the form.
            // Should these fields be changed to NULL in the schema or should they have a default value?
            $doZones->category = '';
            $doZones->ad_selection = '';
            $doZones->chain = '';
            $doZones->prepend = '';
            $doZones->append = '';

            $doZones->show_capped_no_cookie = 0;

            $aFields['zoneid'] = $doZones->insert();

            // Ad  Networks
            $doPublisher = OA_Dal::factoryDO('affiliates');
            $doPublisher->get($aFields['affiliateid']);
            $anWebsiteId = $doPublisher->as_website_id;
            if ($anWebsiteId) {
            	$oAdNetworks = new OA_Central_AdNetworks();
    			$oAdNetworks->updateZone($doZones, $anWebsiteId);
            }
            
            if ($oComponent && method_exists($oComponent, 'processZoneForm')) {
                $oComponent->processZoneForm($aFields);
            }
            
            // Queue confirmation message
            $translation = new OX_Translation ();
            $translated_message = $translation->translate ( $GLOBALS['strZoneHasBeenAdded'], array(
                MAX::constructURL(MAX_URL_ADMIN, 'zone-edit.php?affiliateid=' .  $aFields['affiliateid'] . '&zoneid=' . $aFields['zoneid']),
                htmlspecialchars($aFields['zonename'])
            ));
            OA_Admin_UI::queueMessage($translated_message, 'local', 'confirm', 0);

            OX_Admin_Redirect::redirect("affiliate-zones.php?affiliateid=".$aFields['affiliateid']);
        }
    }

    return $errors;
}


/*-------------------------------------------------------*/
/* Display page                                          */
/*-------------------------------------------------------*/
function displayPage($zone, $form, $zoneErrors = null)
{
    //header and breadcrumbs
    $pageName = basename($_SERVER['SCRIPT_NAME']);
    $agencyId = OA_Permission::getAgencyId();
    $aEntities = array('affiliateid' => $zone['affiliateid'], 'zoneid' => $zone['zoneid']);

    $aOtherPublishers = Admin_DA::getPublishers(array('agency_id' => $agencyId));
    $aOtherZones = Admin_DA::getZones(array('publisher_id' => $zone['affiliateid']));
    MAX_displayNavigationZone($pageName, $aOtherPublishers, $aOtherZones, $aEntities);

    //get template and display form
    $oTpl = new OA_Admin_Template('zone-edit.html');
    $oTpl->assign('zoneid', $zone['zoneid']);
    $oTpl->assign('zoneHeight', $zone["height"]);
    $oTpl->assign('zoneWidth', $zone["width"]);

    $oTpl->assign('zoneErrors', $zoneErrors);
    $oTpl->assign('form', $form->serialize());

    $oTpl->display();

    //footer
    phpAds_PageFooter();
}

?>
