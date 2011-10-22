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
$Id: userlog.lang.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/


// Set translation strings

$GLOBALS['strDeliveryEngine']				= "전달유지 엔진";
$GLOBALS['strMaintenance']					= "유지보수";
$GLOBALS['strAdministrator']				= "관리자";


$GLOBALS['strUserlog'][phpAds_actionAdvertiserReportMailed] = "광고주 {id}에게 보고서를 이메일로 보냅니다.";
$GLOBALS['strUserlog'][phpAds_actionPublisherReportMailed] = "광고게시자 {id}에게 보고서를 이메일로 보냅니다.";
$GLOBALS['strUserlog'][phpAds_actionWarningMailed] = "캠페인 {id}에 대한 활성화해제를 이메일로 경고합니다.";
$GLOBALS['strUserlog'][phpAds_actionDeactivationMailed] = "캠페인 {id}에 대한 활성화해제를 이메일로 알립니다.";
$GLOBALS['strUserlog'][phpAds_actionPriorityCalculation] = "우선순위 다시 계산";
$GLOBALS['strUserlog'][phpAds_actionPriorityAutoTargeting] = "캠페인 대상 재계산";
$GLOBALS['strUserlog'][phpAds_actionDeactiveCampaign] = "캠페인 {id} 활성화해제";
$GLOBALS['strUserlog'][phpAds_actionActiveCampaign] = "캠페인 {id} 활성화";
$GLOBALS['strUserlog'][phpAds_actionAutoClean] = "데이터베이스 자동 정리";




// Note: New translations not found in original lang files but found in CSV
$GLOBALS['strAdvertiser'] = "광고주";
$GLOBALS['strPublisher'] = "광고게시자";
$GLOBALS['strDeleted'] = "삭제";
$GLOBALS['strUserlog'][phpAds_actionActivationMailed] = "캠페인 {id}에 대한 활성화해제를 이메일로 알립니다.";
?>