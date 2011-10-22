<?php
/**
 * Table Definition for account_preference_assoc
 */
require_once 'DB_DataObjectCommon.php';

class DataObjects_Account_user_permission_assoc extends DB_DataObjectCommon
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'account_user_permission_assoc';    // table name
    public $account_id;                      // MEDIUMINT(9) => openads_mediumint => 129 
    public $user_id;                         // MEDIUMINT(9) => openads_mediumint => 129 
    public $permission_id;                   // MEDIUMINT(9) => openads_mediumint => 129 
    public $is_allowed;                      // TINYINT(1) => openads_tinyint => 145 

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Account_user_permission_assoc',$k,$v); }

    var $defaultValues = array(
                'is_allowed' => 1,
                );

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    function _auditEnabled()
    {
        return true;
    }

    function _getContextId()
    {
        return 0;
    }

    function _getContext()
    {
        return 'Account User Permission Association';
    }

    /**
     * build an accounts specific audit array
     *
     * @param integer $actionid
     * @param array $aAuditFields
     */
    function _buildAuditArray($actionid, &$aAuditFields)
    {
        $aAuditFields['key_desc']     = 'Account #'.$this->account_id.' -> User #'.$this->user_id . ' -> Permission #' . $this->permission_id;
    }
}
