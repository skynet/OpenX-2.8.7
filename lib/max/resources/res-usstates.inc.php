<?php

/*
+---------------------------------------------------------------------------+
| OpenX  v2.8                                                              |
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
$Id: res-usstates.inc.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/


// This is by no means a complete list of all iso3166 codes, but rather
// an unofficial list used by most browsers. If you have corrections or
// additions to this list, please send them to niels@creatype.nl

$phpAds_USStates['AL'] = "Alabama";
$phpAds_USStates['AK'] = "Alaska";
$phpAds_USStates['AZ'] = "Arizona";
$phpAds_USStates['AR'] = "Arkansas";
$phpAds_USStates['CA'] = "California";
$phpAds_USStates['CO'] = "Colorado";
$phpAds_USStates['CT'] = "Connecticut";
$phpAds_USStates['DE'] = "Delaware";
$phpAds_USStates['DC'] = "Dist. of Columbia";
$phpAds_USStates['FL'] = "Florida";
$phpAds_USStates['GA'] = "Georgia";
$phpAds_USStates['GU'] = "Guam";
$phpAds_USStates['HI'] = "Hawaii";
$phpAds_USStates['ID'] = "Idaho";
$phpAds_USStates['IL'] = "Illinois";
$phpAds_USStates['IN'] = "Indiana";
$phpAds_USStates['IA'] = "Iowa";
$phpAds_USStates['KS'] = "Kansas";
$phpAds_USStates['KY'] = "Kentucky";
$phpAds_USStates['LA'] = "Louisiana";
$phpAds_USStates['ME'] = "Maine";
$phpAds_USStates['MD'] = "Maryland";
$phpAds_USStates['MA'] = "Massachusetts";
$phpAds_USStates['MI'] = "Michigan";
$phpAds_USStates['MN'] = "Minnesota";
$phpAds_USStates['MS'] = "Mississippi";
$phpAds_USStates['MO'] = "Missouri";
$phpAds_USStates['MT'] = "Montana";
$phpAds_USStates['NE'] = "Nebraska";
$phpAds_USStates['NV'] = "Nevada";
$phpAds_USStates['NH'] = "New Hampshire";
$phpAds_USStates['NJ'] = "New Jersey";
$phpAds_USStates['NM'] = "New Mexico";
$phpAds_USStates['NY'] = "New York";
$phpAds_USStates['NC'] = "North Carolina";
$phpAds_USStates['ND'] = "North Dakota";
$phpAds_USStates['OH'] = "Ohio";
$phpAds_USStates['OK'] = "Oklahoma";
$phpAds_USStates['OR'] = "Oregon";
$phpAds_USStates['PA'] = "Pennsylvania";
$phpAds_USStates['PR'] = "Puerto Rico";
$phpAds_USStates['RI'] = "Rhode Island";
$phpAds_USStates['SC'] = "South Carolina";
$phpAds_USStates['SD'] = "South Dakota";
$phpAds_USStates['TN'] = "Tennessee";
$phpAds_USStates['TX'] = "Texas";
$phpAds_USStates['UT'] = "Utah";
$phpAds_USStates['VT'] = "Vermont";
$phpAds_USStates['VA'] = "Virginia";
$phpAds_USStates['VI'] = "Virgin Islands";
$phpAds_USStates['WA'] = "Washington";
$phpAds_USStates['WV'] = "West Virginia";
$phpAds_USStates['WI'] = "Wisconsin";
$phpAds_USStates['WY'] = "Wyoming";

// Load localized strings
if (file_exists(MAX_PATH.'/lib/max/language/'.$pref['language'].'/res-usstates.lang.php'))
	@include(MAX_PATH.'/lib/max/language/'.$pref['language'].'/res-usstates.lang.php');

?>
