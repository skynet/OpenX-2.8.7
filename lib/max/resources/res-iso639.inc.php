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
$Id: res-iso639.inc.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

// This is by no means a complete list of all iso639-1 codes, but rather
// an unofficial list used by most browsers. If you have corrections or
// additions to this list, please send them to niels@creatype.nl

$phpAds_ISO639['af'] = 'Afrikaans';
$phpAds_ISO639['sq'] = 'Albanian';
$phpAds_ISO639['eu'] = 'Basque';
$phpAds_ISO639['bg'] = 'Bulgarian';
$phpAds_ISO639['be'] = 'Byelorussian';
$phpAds_ISO639['ca'] = 'Catalan';
$phpAds_ISO639['zh'] = 'Chinese';
$phpAds_ISO639['zh-cn'] = '- Chinese/China';
$phpAds_ISO639['zh-tw'] = '- Chinese/Taiwan';
$phpAds_ISO639['hr'] = 'Croatian';
$phpAds_ISO639['cs'] = 'Czech';
$phpAds_ISO639['da'] = 'Danish';
$phpAds_ISO639['nl'] = 'Dutch';
$phpAds_ISO639['nl-be'] = '- Dutch/Belgium';
$phpAds_ISO639['en'] = 'English';
$phpAds_ISO639['en-gb'] = '- English/United Kingdom';
$phpAds_ISO639['en-us'] = '- English/United States';
$phpAds_ISO639['fo'] = 'Faeroese';
$phpAds_ISO639['fi'] = 'Finnish';
$phpAds_ISO639['fr'] = 'French';
$phpAds_ISO639['fr-be'] = '- French/Belgium';
$phpAds_ISO639['fr-ca'] = '- French/Canada';
$phpAds_ISO639['fr-fr'] = '- French/France';
$phpAds_ISO639['fr-ch'] = '- French/Switzerland';
$phpAds_ISO639['gl'] = 'Galician';
$phpAds_ISO639['de'] = 'German';
$phpAds_ISO639['de-au'] = '- German/Austria';
$phpAds_ISO639['de-de'] = '- German/Germany';
$phpAds_ISO639['de-ch'] = '- German/Switzerland';
$phpAds_ISO639['el'] = 'Greek';
$phpAds_ISO639['hu'] = 'Hungarian';
$phpAds_ISO639['is'] = 'Icelandic';
$phpAds_ISO639['id'] = 'Indonesian';
$phpAds_ISO639['ga'] = 'Irish';
$phpAds_ISO639['it'] = 'Italian';
$phpAds_ISO639['ja'] = 'Japanese';
$phpAds_ISO639['ko'] = 'Korean';
$phpAds_ISO639['mk'] = 'Macedonian';
$phpAds_ISO639['no'] = 'Norwegian';
$phpAds_ISO639['pl'] = 'Polish';
$phpAds_ISO639['pt'] = 'Portuguese';
$phpAds_ISO639['pt-br'] = '- Portuguese/Brazil';
$phpAds_ISO639['ro'] = 'Romanian';
$phpAds_ISO639['ru'] = 'Russian';
$phpAds_ISO639['gd'] = 'Scots Gaelic';
$phpAds_ISO639['sr'] = 'Serbian';
$phpAds_ISO639['sk'] = 'Slovak';
$phpAds_ISO639['sl'] = 'Slovenian';
$phpAds_ISO639['es'] = 'Spanish';
$phpAds_ISO639['es-ar'] = '- Spanish/Argentina';
$phpAds_ISO639['es-co'] = '- Spanish/Colombia';
$phpAds_ISO639['es-mx'] = '- Spanish/Mexico';
$phpAds_ISO639['es-es'] = '- Spanish/Spain';
$phpAds_ISO639['sv'] = 'Swedish';
$phpAds_ISO639['tr'] = 'Turkish';
$phpAds_ISO639['uk'] = 'Ukrainian';
$phpAds_ISO639['bs'] = 'Bosnian';

// Load localized strings
if (file_exists(MAX_PATH.'/lib/max/language/'.$pref['language'].'/res-iso639.lang.php'))
	@include(MAX_PATH.'/lib/max/language/'.$pref['language'].'/res-iso639.lang.php');

?>
