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
$Id: agency-index.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

// Require the initialisation file
require_once '../../init.php';

// Required files
require_once MAX_PATH . '/www/admin/lib-maintenance-priority.inc.php';
require_once MAX_PATH . '/lib/OA/Dal.php';
require_once MAX_PATH . '/www/admin/config.php';
require_once MAX_PATH . '/www/admin/lib-statistics.inc.php';

// Register input variables
phpAds_registerGlobal('expand', 'collapse', 'hideinactive', 'listorder',
                      'orderdirection');

// Security check
OA_Permission::enforceAccount(OA_ACCOUNT_ADMIN);

/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/
addPageTools();
phpAds_PageHeader("4.1");
phpAds_ShowSections(array("4.1", "4.3", "4.4"));


/*-------------------------------------------------------*/
/* Get preferences                                       */
/*-------------------------------------------------------*/

if (!isset($hideinactive))
{
    if (isset($session['prefs']['agency-index.php']['hideinactive'])) {
        $hideinactive = $session['prefs']['agency-index.php']['hideinactive'];
    } else {
        $pref = &$GLOBALS['_MAX']['PREF'];
        $hideinactive = ($pref['ui_hide_inactive'] == true);
    }
}

if (!isset($listorder))
{
    if (isset($session['prefs']['agency-index.php']['listorder'])) {
        $listorder = $session['prefs']['agency-index.php']['listorder'];
    } else {
        $listorder = '';
    }
}

if (!isset($orderdirection))
{
    if (isset($session['prefs']['agency-index.php']['orderdirection'])) {
        $orderdirection = $session['prefs']['agency-index.php']['orderdirection'];
    } else {
        $orderdirection = '';
    }
}

if (isset($session['prefs']['agency-index.php']['nodes'])) {
    $node_array = explode (",", $session['prefs']['agency-index.php']['nodes']);
} else {
    $node_array = array();
}


/*-------------------------------------------------------*/
/* Main code                                             */
/*-------------------------------------------------------*/

// Get agencies & campaign and build the tree
$dalAgency = OA_Dal::factoryDAL('agency');
$aManagers = $dalAgency->getAllManagers($listorder, $orderdirection);

foreach ($aManagers as $k => $v) {
    $aManagers[$k]['expand'] = 0;
    $aManagers[$k]['count'] = 0;
    $aManagers[$k]['hideinactive'] = 0;
}

echo "\t\t\t\t<table border='0' width='100%' cellpadding='0' cellspacing='0'>\n";

echo "\t\t\t\t<tr height='25'>\n";
echo "\t\t\t\t\t<td height='25' width='40%'>\n";
echo "\t\t\t\t\t\t<b>&nbsp;&nbsp;<a href='agency-index.php?listorder=name'>".$GLOBALS['strName']."</a>";

if (($listorder == "name") || ($listorder == ""))
{
    if  (($orderdirection == "") || ($orderdirection == "down"))
    {
        echo " <a href='agency-index.php?orderdirection=up'>";
        echo "<img src='" . OX::assetPath() . "/images/caret-ds.gif' border='0' alt='' title=''>";
    }
    else
    {
        echo " <a href='agency-index.php?orderdirection=down'>";
        echo "<img src='" . OX::assetPath() . "/images/caret-u.gif' border='0' alt='' title=''>";
    }
    echo "</a>";
}

echo "</b>\n";
echo "\t\t\t\t\t</td>\n";
echo "\t\t\t\t\t<td height='25'>\n";
echo "\t\t\t\t\t\t<b><a href='agency-index.php?listorder=id'>".$GLOBALS['strID']."</a>";

if ($listorder == "id")
{
    if  (($orderdirection == "") || ($orderdirection == "down"))
    {
        echo " <a href='agency-index.php?orderdirection=up'>";
        echo "<img src='" . OX::assetPath() . "/images/caret-ds.gif' border='0' alt='' title=''>";
    }
    else
    {
        echo " <a href='agency-index.php?orderdirection=down'>";
        echo "<img src='" . OX::assetPath() . "/images/caret-u.gif' border='0' alt='' title=''>";
    }
    echo "</a>";
}

echo "</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;\n";
echo "\t\t\t\t\t</td>\n";
echo "\t\t\t\t\t<td height='25'>&nbsp;</td>\n";
echo "\t\t\t\t\t<td height='25'>&nbsp;</td>\n";
echo "\t\t\t\t\t<td height='25'>&nbsp;</td>\n";
echo "\t\t\t\t\t<td height='25'>&nbsp;</td>\n";
echo "\t\t\t\t</tr>\n";

echo "\t\t\t\t<tr height='1'>\n";
echo "\t\t\t\t\t<td colspan='6' bgcolor='#888888'><img src='" . OX::assetPath() . "/images/break.gif' height='1' width='100%'></td>\n";
echo "\t\t\t\t</tr>\n";


if (!isset($aManagers) || !is_array($aManagers) || count($aManagers) == 0)
{
    echo "\t\t\t\t<tr height='25' bgcolor='#F6F6F6'>\n";
    echo "\t\t\t\t\t<td height='25' colspan='5'>&nbsp;&nbsp;".$strNoAgencies."</td>\n";
    echo "\t\t\t\t</tr>\n";

    echo "\t\t\t\t<tr>\n";
    echo "\t\t\t\t\t<td colspan='6' bgcolor='#888888'><img src='" . OX::assetPath() . "/images/break.gif' height='1' width='100%'></td>\n";
    echo "\t\t\t\t<tr>\n";
}
else
{
    $i=0;
    foreach (array_keys($aManagers) as $key)
    {
        $agency = $aManagers[$key];

        echo "\t\t\t\t<tr height='25' ".($i%2==0?"bgcolor='#F6F6F6'":"").">\n";

        // Icon & name
        echo "\t\t\t\t\t<td height='25'>\n";
        echo "\t\t\t\t\t\t<img src='" . OX::assetPath() . "/images/spacer.gif' height='16' width='16' align='absmiddle'>\n";

        echo "\t\t\t\t\t\t<img src='" . OX::assetPath() . "/images/icon-advertiser.gif' align='absmiddle'>\n";
        echo "\t\t\t\t\t\t<a href='agency-edit.php?agencyid=".$agency['agencyid']."'>".htmlspecialchars($agency['name'])."</a>\n";
        echo "\t\t\t\t\t</td>\n";

        // ID
        echo "\t\t\t\t\t<td height='25'>".$agency['agencyid']."</td>\n";

        echo "\t\t\t\t\t<td height='25'>&nbsp;</td>\n";
        echo "\t\t\t\t\t<td height='25'>&nbsp;</td>\n";

        // Button - Channels
        echo "<td height='25'>";
        if (OA_Permission::hasAccess($agency['account_id'])) {
            echo "<a href='account-switch.php?account_id={$agency['account_id']}'>";
            echo $strSwitchAccount ."</a>&nbsp;&nbsp;";
        } else {
            echo "&nbsp;";
        }

        echo "</td>";

        // Delete
        echo "\t\t\t\t\t<td height='25'>";
        echo "<img src='" . OX::assetPath() . "/images/icon-recycle.gif' border='0' align='absmiddle' alt='$strDelete'>&nbsp;<a href='agency-delete.php?agencyid=".$agency['agencyid']."&returnurl=agency-index.php'".phpAds_DelConfirm($strConfirmDeleteAgency).">$strDelete</a>&nbsp;&nbsp;&nbsp;&nbsp;";
        echo "</td>\n";

        echo "\t\t\t\t</tr>\n";

        echo "\t\t\t\t<tr height='1'>\n";
        echo "\t\t\t\t\t<td colspan='6' bgcolor='#888888'><img src='" . OX::assetPath() . "/images/break.gif' height='1' width='100%'></td>\n";
        echo "\t\t\t\t</tr>\n";
        $i++;
    }
}

echo "\t\t\t\t<tr>\n";
echo "\t\t\t\t\t<td height='25' colspan='4' align='".$phpAds_TextAlignLeft."' nowrap>";

if ($hideinactive == true)
{
    echo "&nbsp;&nbsp;<img src='" . OX::assetPath() . "/images/icon-activate.gif' align='absmiddle' border='0'>";
    echo "&nbsp;<a href='agency-index.php?hideinactive=0'>".$strShowAll."</a>";
    echo "&nbsp;&nbsp;|&nbsp;&nbsp;" . $strInactiveAgenciesHidden;
}
else
{
    echo "&nbsp;&nbsp;<img src='" . OX::assetPath() . "/images/icon-hideinactivate.gif' align='absmiddle' border='0'>";
    echo "&nbsp;<a href='agency-index.php?hideinactive=1'>".$strHideInactiveAgencies."</a>";
}

echo "</td>\n";

echo "<td></td>\n";
echo "\t\t\t\t</tr>\n";

echo "\t\t\t\t</table>\n";


// total number of agencies
$agencyCount = count($aManagers);

echo "\t\t\t\t<br /><br /><br /><br />\n";
echo "\t\t\t\t<table width='100%' border='0' align='center' cellspacing='0' cellpadding='0'>\n";
echo "\t\t\t\t<tr>\n";
echo "\t\t\t\t\t<td height='25' colspan='3'>&nbsp;&nbsp;<b>".$strOverall."</b></td>\n";
echo "\t\t\t\t</tr>\n";
echo "\t\t\t\t<tr height='1'>\n";
echo "\t\t\t\t\t<td colspan='4' bgcolor='#888888'><img src='" . OX::assetPath() . "/images/break.gif' height='1' width='100%'></td>\n";
echo "\t\t\t\t</tr>\n";

echo "\t\t\t\t<tr>\n";
echo "\t\t\t\t\t<td height='25'>&nbsp;&nbsp;".$strTotalAgencies.": <b>" . $agencyCount . "</b></td>\n";
echo "\t\t\t\t\t<td height='25' colspan='2'></td>\n";
echo "\t\t\t\t</tr>\n";

echo "\t\t\t\t<tr height='1'>\n";
echo "\t\t\t\t\t<td colspan='3' bgcolor='#888888'><img src='" . OX::assetPath() . "/images/break.gif' height='1' width='100%'></td>\n";
echo "\t\t\t\t</tr>\n";

echo "\t\t\t\t</table>\n";
echo "\t\t\t\t<br /><br />\n";

/*-------------------------------------------------------*/
/* Store preferences                                     */
/*-------------------------------------------------------*/

$session['prefs']['agency-index.php']['hideinactive']   = $hideinactive;
$session['prefs']['agency-index.php']['listorder']      = $listorder;
$session['prefs']['agency-index.php']['orderdirection'] = $orderdirection;
$session['prefs']['agency-index.php']['nodes']          = implode (",", $node_array);

phpAds_SessionDataStore();

/*-------------------------------------------------------*/
/* HTML framework                                        */
/*-------------------------------------------------------*/

phpAds_PageFooter();

function addPageTools()
{
    addPageLinkTool($GLOBALS['strAddAgency_Key'], "agency-edit.php", "iconAdvertiserAdd", $GLOBALS["keyAddNew"] );
}

?>
