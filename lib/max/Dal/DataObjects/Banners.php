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
$Id: Banners.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

/**
 * Table Definition for banners
 */
require_once 'DB_DataObjectCommon.php';
include_once MAX_PATH . '/www/admin/lib-banner.inc.php';
include_once MAX_PATH . '/www/admin/lib-storage.inc.php';

class DataObjects_Banners extends DB_DataObjectCommon
{
    var $onDeleteCascade = true;
    var $refreshUpdatedFieldIfExists = true;
    
    /*
     * Define Market banner ext_bannertype field value
     */
    const BANNER_TYPE_MARKET = 'market-optin-banner';
    
     
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'banners';                         // table name
    public $bannerid;                        // MEDIUMINT(9) => openads_mediumint => 129
    public $campaignid;                      // MEDIUMINT(9) => openads_mediumint => 129
    public $contenttype;                     // ENUM('gif','jpeg','png','html','swf','dcr','rpm','mov','txt') => openads_enum => 130
    public $pluginversion;                   // MEDIUMINT(9) => openads_mediumint => 129
    public $storagetype;                     // ENUM('sql','web','url','html','network','txt') => openads_enum => 130
    public $filename;                        // VARCHAR(255) => openads_varchar => 130
    public $imageurl;                        // VARCHAR(255) => openads_varchar => 130
    public $htmltemplate;                    // TEXT() => openads_text => 162
    public $htmlcache;                       // TEXT() => openads_text => 162
    public $width;                           // SMALLINT(6) => openads_smallint => 129
    public $height;                          // SMALLINT(6) => openads_smallint => 129
    public $weight;                          // TINYINT(4) => openads_tinyint => 129
    public $seq;                             // TINYINT(4) => openads_tinyint => 129
    public $target;                          // VARCHAR(16) => openads_varchar => 130
    public $url;                             // TEXT() => openads_text => 162
    public $alt;                             // VARCHAR(255) => openads_varchar => 130
    public $statustext;                      // VARCHAR(255) => openads_varchar => 130
    public $bannertext;                      // TEXT() => openads_text => 162
    public $description;                     // VARCHAR(255) => openads_varchar => 130
    public $adserver;                        // VARCHAR(255) => openads_varchar => 130
    public $block;                           // INT(11) => openads_int => 129
    public $capping;                         // INT(11) => openads_int => 129
    public $session_capping;                 // INT(11) => openads_int => 129
    public $compiledlimitation;              // TEXT() => openads_text => 162
    public $acl_plugins;                     // TEXT() => openads_text => 34
    public $append;                          // TEXT() => openads_text => 162
    public $bannertype;                      // TINYINT(4) => openads_tinyint => 129
    public $alt_filename;                    // VARCHAR(255) => openads_varchar => 130
    public $alt_imageurl;                    // VARCHAR(255) => openads_varchar => 130
    public $alt_contenttype;                 // ENUM('gif','jpeg','png') => openads_enum => 130
    public $comments;                        // TEXT() => openads_text => 34
    public $updated;                         // DATETIME() => openads_datetime => 142
    public $acls_updated;                    // DATETIME() => openads_datetime => 142
    public $keyword;                         // VARCHAR(255) => openads_varchar => 130
    public $transparent;                     // TINYINT(1) => openads_tinyint => 145
    public $parameters;                      // TEXT() => openads_text => 34
    public $an_banner_id;                    // INT(11) => openads_int => 1
    public $as_banner_id;                    // INT(11) => openads_int => 1
    public $status;                          // INT(11) => openads_int => 129
    public $ad_direct_status;                // TINYINT(4) => openads_tinyint => 129
    public $ad_direct_rejection_reason_id;    // TINYINT(4) => openads_tinyint => 129
    public $ext_bannertype;                  // VARCHAR(255) => openads_varchar => 2
    public $prepend;                         // TEXT() => openads_text => 162

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Banners',$k,$v); }

    var $defaultValues = array(
                'campaignid' => 0,
                'contenttype' => 'gif',
                'pluginversion' => 0,
                'storagetype' => 'sql',
                'filename' => '',
                'imageurl' => '',
                'htmltemplate' => '',
                'htmlcache' => '',
                'width' => 0,
                'height' => 0,
                'weight' => 1,
                'seq' => 0,
                'target' => '',
                'url' => '',
                'alt' => '',
                'statustext' => '',
                'bannertext' => '',
                'description' => '',
                'adserver' => '',
                'block' => 0,
                'capping' => 0,
                'session_capping' => 0,
                'compiledlimitation' => '',
                'append' => '',
                'bannertype' => 0,
                'alt_filename' => '',
                'alt_imageurl' => '',
                'alt_contenttype' => 'gif',
                'updated' => '%DATE_TIME%',
                'acls_updated' => '%NO_DATE_TIME%',
                'keyword' => '',
                'transparent' => 0,
                'status' => 0,
                'ad_direct_status' => 0,
                'ad_direct_rejection_reason_id' => 0,
                'prepend' => '',
                );

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function delete($useWhere = false, $cascade = true, $parentid = null)
    {
    	$doBanner = clone($this);
    	$doBanner->find();
    	while ($doBanner->fetch()) {
    	    $this->deleteBannerFile($doBanner->storagetype, $doBanner->filename);
    	}
    	return parent::delete($useWhere, $cascade, $parentid);
    }

    /**
     * A method to delete the banner file.
     *
     * @param string $storageType The storage type of the banner to delete
     * @param string $fileName    The name of the banner file to be deleted
     */
    function deleteBannerFile($storageType, $fileName) {
        if ($storageType == 'web') {
            $doBanner = OA_Dal::factoryDO('banners');
            $doBanner->filename = $fileName;
            $doBanner->find();
            // If there is only a banner using the same file delete the file
            if ($doBanner->getRowCount() == 1) {
                phpAds_ImageDelete ($storageType, $fileName);
            }
        } else {
            phpAds_ImageDelete ($storageType, $fileName);
        }
    }

    /**
     * Duplicates the banner.
     * @param string $new_campaignId only when the banner is
     *        duplicated as consequence of a campaign duplication
     * @return int  the new bannerid
     *
     */
    function duplicate($new_campaignId = null)
    {
        // unset the bannerId
        $old_adId = $this->bannerid;
        unset($this->bannerid);

        $this->description = $GLOBALS['strCopyOf'] . ' ' . $this->description;
        if ($new_campaignId != null) {
        	$this->campaignid = $new_campaignId;
        }

        // Set the filename
        // We want to rename column 'storagetype' to 'type' so...
        if ($this->storagetype == 'web' || $this->storagetype == 'sql') {
            $this->filename = $this->_imageDuplicate($this->storagetype, $this->filename);
        } elseif ($this->type == 'web' || $this->type == 'sql') {
            $this->filename = $this->_imageDuplicate($this->type, $this->filename);
        }

        // Insert the new banner and get the ID
        $new_adId = $this->insert(false);

        // Copy ACLs and capping
        MAX_AclCopy(basename($_SERVER['SCRIPT_NAME']), $old_adId, $new_adId);

        // Duplicate and ad-zone associations
        MAX_duplicateAdZones($old_adId, $new_adId);

        // Return the new bannerId
        return $new_adId;
    }

    function insert($autoLinkMatchingZones = true)
    {
        $this->_rebuildCache();
        $id = parent::insert();
        if ($id) {
            // add default zone
            $aVariables = array('ad_id' => $id, 'zone_id' => 0);
            Admin_DA::addAdZone($aVariables);
            if ($autoLinkMatchingZones) {
                MAX_addDefaultPlacementZones($id, $this->campaignid);
            }
        }
        return $id;
    }

    function _rebuildCache()
    {
        if (!is_null($this->htmltemplate)) {
            $this->htmlcache = phpAds_getBannerCache($this->toArray());
        }
    }


    /**
     * Automatically refreshes HTML cache in addition to normal
     * update() call.
     *
     * @see DB_DataObject::update()
     * @param object $dataObject
     * @return boolean
     * @access public
     */
    function update($dataObject = false)
    {
        $this->_rebuildCache();
        return parent::update($dataObject);
    }

    /**
     * Wrapper for phpAds_ImageDuplicate
     *
     * @access private
     */
    function _imageDuplicate($storagetype, $filename)
    {
        return phpAds_ImageDuplicate($storagetype, $filename);
    }

    function _auditEnabled()
    {
        return true;
    }

    function _getContextId()
    {
        return $this->bannerid;
    }

    function _getContext()
    {
        return 'Banner';
    }

    /**
     * A method to return an array of account IDs of the account(s) that
     * should "own" any audit trail entries for this entity type; these
     * are NOT related to the account ID of the currently active account
     * (which is performing some kind of action on the entity), but is
     * instead related to the type of entity, and where in the account
     * heirrachy the entity is located.
     *
     * @return array An array containing up to three indexes:
     *                  - "OA_ACCOUNT_ADMIN" or "OA_ACCOUNT_MANAGER":
     *                      Contains the account ID of the manager account
     *                      that needs to be able to see the audit trail
     *                      entry, or, the admin account, if the entity
     *                      is a special case where only the admin account
     *                      should see the entry.
     *                  - "OA_ACCOUNT_ADVERTISER":
     *                      Contains the account ID of the advertiser account
     *                      that needs to be able to see the audit trail
     *                      entry, if such an account exists.
     *                  - "OA_ACCOUNT_TRAFFICKER":
     *                      Contains the account ID of the trafficker account
     *                      that needs to be able to see the audit trail
     *                      entry, if such an account exists.
     */
    function getOwningAccountIds()
    {
        // Banners don't have an account_id, get it from the parent
        // campaign (stored in the "campaigns" table) using the
        // "campaignid" key
        return parent::getOwningAccountIds('campaigns', 'campaignid');
    }

    /**
     * build a campaign specific audit array
     *
     * @param integer $actionid
     * @param array $aAuditFields
     */
    function _buildAuditArray($actionid, &$aAuditFields)
    {
        $aAuditFields['key_desc']   = $this->description;
        switch ($actionid)
        {
            case OA_AUDIT_ACTION_UPDATE:
                        $aAuditFields['campaignid']    = $this->campaignid;
                        break;
        }
    }

}

?>