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
$Id: lib-userlog.inc.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

// Define usertypes
define ("phpAds_userDeliveryEngine", 1);
define ("phpAds_userMaintenance", 2);
define ("phpAds_userAdministrator", 3);
define ("phpAds_userAdvertiser", 4);
define ("phpAds_userPublisher", 5);

// Define actions
define ("phpAds_actionAdvertiserReportMailed", 0);
define ("phpAds_actionPublisherReportMailed", 1);
define ("phpAds_actionWarningMailed", 2);
define ("phpAds_actionDeactivationMailed", 3);
define ("phpAds_actionActivationMailed", 4);
define ("phpAds_actionPriorityCalculation", 10);
define ("phpAds_actionPriorityAutoTargeting", 11);
define ("phpAds_actionDeactiveCampaign", 20);
define ("phpAds_actionActiveCampaign", 21);
define ("phpAds_actionAutoClean", 30);
define ("phpAds_actionBatchStatistics", 40);

$GLOBALS['phpAds_Usertype'] = 0;

/*-------------------------------------------------------*/
/* Add an entry to the userlog                           */
/*-------------------------------------------------------*/

function phpAds_userlogAdd($action, $object, $details = '')
{
    $oDbh =& OA_DB::singleton();
    $conf = $GLOBALS['_MAX']['CONF'];
	global $phpAds_Usertype;
	if ($phpAds_Usertype != 0) {
		$usertype = $phpAds_Usertype;
		$userid   = 0;
	} else {
		$usertype = phpAds_userAdministrator;
		$userid   = 0;
	}
	$now = strtotime(OA::getNow());
    $query = "
        INSERT INTO
            ".$oDbh->quoteIdentifier($conf['table']['prefix'].$conf['table']['userlog'],true)."
            (
                timestamp,
                usertype,
                userid,
                action,
                object,
                details
            )
        VALUES
            (
                ". $oDbh->quote($now, 'integer') . ",
                ". $oDbh->quote($usertype, 'integer') .",
                ". $oDbh->quote($userid, 'integer') .",
                ". $oDbh->quote($action, 'integer') .",
                ". $oDbh->quote($object, 'integer') .",
                ". $oDbh->quote($details, 'text') . "
            )";
    $res = $oDbh->exec($query);
    if (PEAR::isError($res)) {
        return $res;
    }
    return true;
}

function phpAds_userlogSetUser ($usertype)
{
	global $phpAds_Usertype;
	$phpAds_Usertype = $usertype;
}

function phpAds_UserlogSelection($subSection, $mainSection='userlog')
{
    global
         $phpAds_TextDirection
        ,$strBanners
        ,$strCache
        ,$strChooseSection
        ,$strPriority
        ,$strSourceEdit
        ,$strStats
        ,$strStorage
        ,$strMaintenance
        ,$strCheckForUpdates
        ,$strViewPastUpdates
    ;

require_once MAX_PATH . '/lib/max/language/Loader.php';

Language_Loader::load('settings');
Language_Loader::load('default');
Language_Loader::load('userlog');

?>
<script language="JavaScript">
<!--
function audit_goto_section()
{
    s = document.audit_selection.section.selectedIndex;

    s = document.audit_selection.section.options[s].value;
    document.location = '<?php echo $mainSection; ?>-' + s + '.php';
}
// -->
</script>
<?php
    $conf =& $GLOBALS['_MAX']['CONF'];
    $pref =& $GLOBALS['_MAX']['PREF'];

    echo "<table border='0' width='100%' cellpadding='0' cellspacing='0'>";
    echo "<tr><form name='audit_selection'><td height='35'>";
    echo "<b>".$strChooseSection.":&nbsp;</b>";
    echo "<select name='section' onChange='audit_goto_section();'>";

    if (OA_Permission::isAccount(OA_ACCOUNT_ADMIN)) {
            echo "<option value='index'".($subSection == 'index' ? ' selected' : '').">". $GLOBALS['strAuditTrail'] ."</option>";
            echo "<option value='maintenance'".($subSection == 'maintenance' ? ' selected' : '').">". $GLOBALS['strMaintenanceLog'] ."</option>";
    }

    echo "</select>&nbsp;<a href='javascript:void(0)' onClick='audit_goto_section();'>";
    echo "<img src='" . OX::assetPath() . "/images/".$phpAds_TextDirection."/go_blue.gif' border='0'></a>";
    echo "</td></form></tr>";
    echo "</table>";

    phpAds_ShowBreak();
}


?>