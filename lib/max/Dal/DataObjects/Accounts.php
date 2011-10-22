<?php
/**
 * Table Definition for accounts
 */
require_once 'DB_DataObjectCommon.php';
require_once MAX_PATH . '/lib/OA/Dal/ApplicationVariables.php';

class DataObjects_Accounts extends DB_DataObjectCommon
{
    var $onDeleteCascade = true;
    var $onDeleteCascadeSkip = array(
        'audit', 'users'
    );
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'accounts';                        // table name
    public $account_id;                      // MEDIUMINT(9) => openads_mediumint => 129 
    public $account_type;                    // VARCHAR(16) => openads_varchar => 130 
    public $account_name;                    // VARCHAR(255) => openads_varchar => 2 
    public $m2m_password;                    // VARCHAR(32) => openads_varchar => 2 
    public $m2m_ticket;                      // VARCHAR(32) => openads_varchar => 2 

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('DataObjects_Accounts',$k,$v); }

    var $defaultValues = array(
                'account_type' => '',
                );

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    /**
     * Handle all necessary operations when a new account is created
     *
     * @see DB_DataObject::insert()
     */
    function insert()
    {
        $result = parent::insert();

        if ($this->account_type == OA_ACCOUNT_ADMIN && $result) {
            OA_Dal_ApplicationVariables::set('admin_account_id', $result);
        }

        return $result;
    }

    /**
     * Delete all referenced records
     *
     * Although it's a public access to this method it shouldn't be called outside
     * this class. The only reason it's not private is because it needs to be executed
     * on new objects.
     *
     * @return boolean  True on success else false
     * @access public
     **/
    function deleteCascade($linkedRefs, $primaryKey, $parentid)
    {
        $result = parent::deleteCascade($linkedRefs, $primaryKey, $parentid);
        // Delete users or link them to other accounts 
        if ($result) {
            return $this->_relinkOrDeleteUsers();
        }
        return $result;
    }
    
    /**
     * Returns ADMIN account ID
     *
     */
    function getAdminAccountId()
    {
        return OA_Dal_ApplicationVariables::get('admin_account_id');
    }

    function _auditEnabled()
    {
        return true;
    }

    function _getContextId()
    {
        return $this->account_id;
    }

    function _getContext()
    {
        return 'Account';
    }

    /**
     * build an accounts specific audit array
     *
     * @param integer $actionid
     * @param array $aAuditFields
     */
    function _buildAuditArray($actionid, &$aAuditFields)
    {
        // Do not log the M2M password and ticket in the audit record
        unset($aAuditFields['m2m_password']);
        unset($aAuditFields['m2m_ticket']);

        if (count($aAuditFields)) {
            $aAuditFields['key_desc']     = $this->account_name;
        } else {
            // No need to log if nothing was changed, apart from M2M fields
        }
    }
    
    /**
     * On cascade deleting check if linked users should be deleted
     * or can be relinked to other account
     *
     * @return bool True on succes False on error
     */
    function _relinkOrDeleteUsers()
    {
        $doUsers = OA_Dal::factoryDO('users');
        $doUsers->default_account_id = $this->account_id;
        $doUsers->find();
        if (PEAR::isError($doUsers)) {
            return false;
        }
        while ($doUsers->fetch()) {
            if ($doUsers->countLinkedAccounts() == 0) {
                // Delete user without accounts
                $doUserDelete = OA_Dal::factoryDO('users');
                $doUserDelete->user_id = $doUsers->user_id;
                $doUserDelete->delete();
            } else {
                // Relink user to oldest account (lowest account ID)
                $aAccountsIds = $doUsers->getLinkedAccountsIds();
                sort($aAccountsIds);
                reset($aAccountsIds);
                $doUserUpdate = clone($doUsers);
                $doUserUpdate->default_account_id = current($aAccountsIds);
                $doUserUpdate->update();
            }
        }
        return true;
    }
}
