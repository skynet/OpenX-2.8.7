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
$Id: maintenance.lang.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

// Main strings
$GLOBALS['strChooseSection']			= "Выберите раздел";


// Priority
$GLOBALS['strRecalculatePriority']		= "Пере�?читать приоритеты";
$GLOBALS['strHighPriorityCampaigns']		= "Кампании �? вы�?оким приоритетом";
$GLOBALS['strAdViewsAssigned']			= "Выделено про�?мотров";
$GLOBALS['strLowPriorityCampaigns']		= "Кампании �? низким приоритетом";
$GLOBALS['strPredictedAdViews']			= "Пред�?казано про�?мотров";
$GLOBALS['strPriorityDaysRunning']		= "Сейча�? до�?тупно {days} дней �?тати�?тики, на которой ".MAX_PRODUCT_NAME." может о�?новывать �?вои пред�?казани�?. ";
$GLOBALS['strPriorityBasedLastWeek']		= "Пред�?казани�? о�?нованы на данных по �?той и прошлой неделе. ";
$GLOBALS['strPriorityBasedYesterday']		= "Пред�?казание о�?новано на данных за вчера. ";
$GLOBALS['strPriorityNoData']			= "�?едо�?таточно данных дл�? надёжного пред�?казани�? количе�?тва показов, которые данный �?ервер �?генерирует �?егодн�?. �?азначение проритетов будет о�?новывать�?�? на �?тати�?тике, �?обираемой в реальном времени. ";
$GLOBALS['strPriorityEnoughAdViews']		= "Должно быть до�?таточно показов дл�? удовлетворени�? требований в�?ех вы�?окоприоритетных кампаний. ";
$GLOBALS['strPriorityNotEnoughAdViews']		= "�?еочевидно, будет ли �?егодн�? �?генерировано до�?таточно показов дл�? удовлетворени�? требований в�?ех вы�?окопроритетных кампаний. ";


// Banner cache
$GLOBALS['strRebuildBannerCache']		= "По�?троить к�?ш баннеров заново";
$GLOBALS['strBannerCacheExplaination']		= "\n	К�?ш баннеров �?одержит копию HTML-кода, и�?пользуемого дл�? показа баннера. И�?пользование к�?ша позвол�?ет у�?корить\n	до�?тавку баннеров, по�?кольку HTML-код не нужно генерировать дл�? каждого показа баннера. По�?кольку\n	к�?ш �?одержит жё�?тко закодированные �?�?ылки на ра�?положение и �?амих баннеров, к�?ш нужно пере�?траивать\n	при каждом перемещении на веб-�?ервере.\n";


// Zone cache
$GLOBALS['strAge']				= "Срок";
$GLOBALS['strCache']                    = "К�?ш до�?тавки";
$GLOBALS['strRebuildDeliveryCache']                     = "Обновить к�?ш баннеров";
$GLOBALS['strDeliveryCacheExplaination']                = "\n        К�?ш до�?тавки и�?пользует�?�? дл�? у�?корени�? до�?тавки баннеров. К�?ш �?одержит копию в�?ех баннеров,\n        прив�?занных к зоне/ Это �?кономит не�?колько запро�?ов к базе данных в момент фактиче�?кого показа баннера пользователю. К�?ш\n        обычно обновл�?ет�?�? по�?ле каждого изменени�? в зоне или одном из прив�?занных к ней баннеров, но, возможно, он может у�?таревать. По�?тому\n        к�?ш также обновл�?ет�?�? автоматиче�?ки каждый ча�?, или может быть обновлён вручную.\n";
$GLOBALS['strDeliveryCacheSharedMem']           = "\n        Дл�? хранени�? к�?ша до�?тавки и�?пользует�?�? раздел�?ема�? пам�?ть.\n";
$GLOBALS['strDeliveryCacheDatabase']            = "\n        Дл�? хранени�? к�?ша до�?тавки и�?пользует�?�? база данных.\n";


// Storage
$GLOBALS['strStorage']				= "Хранение";
$GLOBALS['strMoveToDirectory']			= "Переме�?тить картинки из БД в каталог";
$GLOBALS['strStorageExplaination']		= "\n	Картинки, и�?пользуемые локальными баннерами, хран�?т�?�? в базе данных или в каталоге. Е�?ли вы будете хранить картинки \n	в каталоге на ди�?ке, нагрузка на базу данных уменьшит�?�?, и �?то приведёт к у�?корению.\n";


// Storage
$GLOBALS['strStatisticsExplaination']		= "Вы включили режим <i>компактной �?тати�?тики</i>, но ваши �?тарые данные о�?тают�?�? в подробном формате. Хотите ли вы конвертировать ваши данные в компактный формат?";


// Product Updates
$GLOBALS['strSearchingUpdates']			= "Производит�?�? пои�?к обновлений. Пожалуй�?та, подождите...";
$GLOBALS['strAvailableUpdates']			= "До�?тупные обновлени�?";
$GLOBALS['strDownloadZip']			= "Скачать (.zip)";
$GLOBALS['strDownloadGZip']			= "Скачать (.tar.gz)";

$GLOBALS['strUpdateAlert']			= "До�?тупна нова�? вер�?и�? ". MAX_PRODUCT_NAME ."                              \n\nХотите узнать больше \nоб �?том обновлении?";
$GLOBALS['strUpdateAlertSecurity']		= "До�?тупна нова�? вер�?и�? ". MAX_PRODUCT_NAME ."                                \n\nРекомендует�?�? произве�?ти обновление \nкак можно �?корее, так как �?та \nвер�?и�? �?одержит одно или не�?колько и�?правлений, отно�?�?щих�?�? к безопа�?но�?ти.";

$GLOBALS['strUpdateServerDown']			= "\n    По неизве�?тной причине невозможно получить информацию <br />\n	о возможных обновлени�?х. Пожалуй�?та, попытайте�?ь позднее.\n";

$GLOBALS['strNoNewVersionAvailable']		= "\n	Ваша вер�?и�? ". MAX_PRODUCT_NAME ." не требует обновлени�?. �?икаких обновлений в на�?то�?щее врем�? нет.\n";

$GLOBALS['strNewVersionAvailable']		= "\n	<b>До�?тупна нова�? вер�?и�? </b><br /> Рекомендует�?�? у�?тановить �?то обновление,\n	по�?кольку оно может и�?править некоторые �?уще�?твующие проблемы и добавить новую функционально�?ть. За дополнительной\n	информацией об обновлении обратите�?ь к документации, включенной в указанные ниже файлы.\n";

$GLOBALS['strSecurityUpdate']			= "\n	<b>�?а�?то�?тельно рекомендует�?�? у�?тановить �?то обновление как можно �?корее, по�?кольку оно �?одержит не�?колько\n	и�?правлений, �?в�?занных �? безопа�?но�?тью.</b> Вер�?и�? , которую вы �?ейча�? и�?пользуете, может быть \n	подвержена определённым атакам, и, веро�?тно, небезопа�?на. За дополнительной\n	информацией об обновлении обратите�?ь к документации, включённой в указанные ниже файлы.\n";

$GLOBALS['strNotAbleToCheck']                   = "\n        <b>По�?кольку модуль поддержки XML не у�?тановлен на вашем �?ервере,  ". MAX_PRODUCT_NAME ." не может\n    проверить наличие более �?вежей вер�?ии.</b>\n";

$GLOBALS['strForUpdatesLookOnWebsite']  = "\n        Е�?ли вы хотите узнать, нет ли более новой вер�?ии, по�?етите наш веб-�?айт.\n";

$GLOBALS['strClickToVisitWebsite']              = "\n        Щёлкните зде�?ь, чтобы по�?етить наш веб-�?айт\n";


// Stats conversion
$GLOBALS['strConverting']			= "Преобразование";
$GLOBALS['strConvertingStats']			= "Преобразовываем �?тати�?тики...";
$GLOBALS['strConvertStats']			= "Преобразовать �?тати�?тику";
$GLOBALS['strConvertAdViews']			= "Показы преобразованы,";
$GLOBALS['strConvertAdClicks']			= "Клики преобразованы...";
$GLOBALS['strConvertNothing']			= "�?ечего преобразовывать...";
$GLOBALS['strConvertFinished']			= "Закончено...";

$GLOBALS['strConvertExplaination']		= "\n	Вы �?ейча�? и�?пользуете компактный формат хранени�? вашей �?тати�?тики, но у ва�? в�?ё еще е�?ть <br />\n	некоторые данные в ра�?ширенном формате. До тех пор пока ра�?ширенна�? �?тати�?тика не будет  <br />\n	преобразована в компактный формат, она не будет и�?пользовать�?�? при про�?мотре �?тих �?траниц.  <br />\n	Перед преобразованием �?тати�?тики, �?делайте резервную копию базы данных!  <br />\n	Вы хотите преобразовать вашу ра�?ширенную �?тати�?тику в новый компактный формат? <br />\n";

$GLOBALS['strConvertingExplaination']		= "\n	В�?�? о�?тавша�?�?�? ра�?ширенна�? �?тати�?тика �?ейча�? преобразует�?�? в компактный формат. <br />\n	В зави�?имо�?ти от того, �?колько показов �?охранено в ра�?ширенном формате, �?то может зан�?ть  <br />\n	не�?колько минут. Пожалуй�?та, подождите окончани�? преобразовани�?, прежде чем вы перейдёте на другие <br />\n	�?траницыpages. �?иже вы увидите журнал в�?ех изменений, произвёденных в базе данных. <br />\n";

$GLOBALS['strConvertFinishedExplaination']  	= "\n	Преобразование о�?тававшей�?�? ра�?ширенной �?тати�?тики было у�?пешным и в�?е данные <br />\n	должны быть теперь до�?тупны. �?иже вы можете увидеть журнал в�?ех изменений, <br />\n	произведённых в базе данных.<br />\n";




// Note: new translatiosn not found in original lang files but found in CSV
$GLOBALS['strCheckBannerCache'] = "Проверить к�?ш баннеров";
$GLOBALS['strBannerCacheErrorsFound'] = "В проце�?�?е проверки к�?ша баннеров были обнаружены ошибки. До ручного и�?правлени�? �?тих ошибок баннеры показывать�?�? не будут.";
$GLOBALS['strBannerCacheOK'] = "В проце�?�?е проверки к�?ша баннеров ошибок не обнаружено.";
$GLOBALS['strBannerCacheDifferencesFound'] = "В проце�?�?е проверки к�?ша баннеров обнаружено у�?таревание к�?ша. �?ажмите на �?�?ылку дл�? автоматиче�?кого обновлени�? к�?ша.";
$GLOBALS['strBannerCacheRebuildButton'] = "Обновить";
$GLOBALS['strDeliveryCacheFiles'] = "\n        Дл�? хранени�? к�?ша до�?тавки и�?пользуют�?�? файлы на �?ервере.\n";
$GLOBALS['strCurrentlyUsing'] = "В на�?то�?щее врем�? вы и�?пользуете";
$GLOBALS['strRunningOn'] = "запущенную на";
$GLOBALS['strAndPlain'] = "и";
$GLOBALS['strBannerCacheFixed'] = "�?втоматиче�?кое обновление кеша произведено у�?пешно. Кеш баннеров работает нормально.";


// Note: New translations not found in original lang files but found in CSV
$GLOBALS['strEncoding'] = "Кодировка";
$GLOBALS['strEncodingExplaination'] = "". MAX_PRODUCT_NAME ." �?охран�?ет данные в кодировке UTF-8 format.<br />Мы попытаем�?�? конвертировать ваши данные автоматиче�?ки.<br />Е�?ли по�?ле обновлени�? вы обнаружите поврежденные данные, и вы знаете и�?ходную кодировку �?тих данных, вы можете и�?пользовать �?тот ин�?трумент дл�? конвертации ваших данных в UTF-8";
$GLOBALS['strEncodingConvertFrom'] = "И�?ходна�? кодировка:";
$GLOBALS['strEncodingConvert'] = "Преобразовать";
$GLOBALS['strEncodingConvertTest'] = "Те�?тировать преобразование";
$GLOBALS['strConvertThese'] = "Е�?ли вы продолжите, �?ледующие данные будут изменены";
$GLOBALS['strAppendCodes'] = "Добавить коды";
$GLOBALS['strScheduledMaintenanceHasntRun'] = "<b>Запланированное об�?луживание не запу�?кало�?ь в по�?ледний ча�?. Возможно, об�?луживание не на�?троено или на�?троено некорректно.</b>";
$GLOBALS['strAutoMantenaceEnabledAndHasntRun'] = "�?втоматиче�?кое об�?луживание разрешено, но ни разу не запу�?кало�?ь. Дл�? лучшей производительно�?ти рекомендует�?�? на�?троить <a href='". OX_PRODUCT_DOCSURL ."/maintenance' target='_blank'>запланированное об�?луживание</a>.";
$GLOBALS['strAutoMantenaceDisabledAndHasntRun'] = "�?втоматиче�?кое об�?луживание запрещено. Дл�? лучшей производительно�?ти рекомендует�?�? на�?троить <a href='". OX_PRODUCT_DOCSURL ."/maintenance' target='_blank'>запланированное об�?луживание</a>.";
$GLOBALS['strAutoMantenaceEnabledAndRunning'] = "�?втоматиче�?кое об�?луживание разрешено, и работает. Дл�? лучшей производительно�?ти рекомендует�?�? на�?троить <a href='". OX_PRODUCT_DOCSURL ."/maintenance' target='_blank'>запланированное об�?луживание</a>.";
$GLOBALS['strAutoMantenaceDisabledAndRunning'] = "�?втоматиче�?кое об�?луживание было отключено. Дл�? лучшей производительно�?ти рекомендует�?�? на�?троить <a href='". OX_PRODUCT_DOCSURL ."/maintenance' target='_blank'>запланированное об�?луживание</a>.";
$GLOBALS['strScheduledMantenaceRunning'] = "<b>Запланированное об�?луживание работает корректно.</b>";
$GLOBALS['strAutomaticMaintenanceHasRun'] = "<b>�?втоматиче�?кое об�?луживание работает корректно.</b>";
$GLOBALS['strAutoMantenaceEnabled'] = "�?втоматиче�?кое об�?луживание было включено. Дл�? лучшей производительно�?ти рекомендует�?�? на�?троить <a href='account-settings-maintenance.php'>запланированное об�?луживание</a>.";
$GLOBALS['strAutoMaintenanceDisabled'] = "�?втоматиче�?кое об�?луживание было отключено.";
$GLOBALS['strAutoMaintenanceEnabled'] = "�?втоматиче�?кое об�?луживание было включено. Дл�? лучшей производительно�?ти рекомендует�?�? на�?троить <a href='http://". OX_PRODUCT_DOCSURL ."/maintenance' target='_blank'>запланированное об�?луживание</a>.";
$GLOBALS['strCheckACLs'] = "Проверить права до�?тупа";
$GLOBALS['strScheduledMaintenance'] = "Запланированное об�?луживание работает корректно.";
$GLOBALS['strAutoMaintenanceEnabledNotTriggered'] = "�?втоматиче�?кое об�?луживание на�?троено, но не запу�?кало�?ь. �?втоматиче�?кое об�?луживание запу�?кает�?�? только когда ". MAX_PRODUCT_NAME ." показывает баннеры.";
$GLOBALS['strAutoMaintenanceBestPerformance'] = "Дл�? лучшей производительно�?ти рекомендует�?�? на�?троить <a href='". OX_PRODUCT_DOCSURL ."/maintenance.html' target='_blank'>Запланированное об�?луживание</a>";
$GLOBALS['strAutoMaintenanceEnabledWilltTrigger'] = "�?втоматиче�?кое об�?луживание включено и будет запу�?кать�?�? каждый ча�?";
$GLOBALS['strAutoMaintenanceDisabledMaintenanceRan'] = "Дл�? корректной работы ". MAX_PRODUCT_NAME ." необходимо на�?троить �?втоматиче�?кое об�?луживание или Запланированное об�?луживание.";
$GLOBALS['strAutoMaintenanceDisabledNotTriggered'] = "Дл�? корректной работы ". MAX_PRODUCT_NAME ." необходимо на�?троить �?втоматиче�?кое об�?луживание или Запланированное об�?луживание.";
$GLOBALS['strAllBannerChannelCompiled'] = "В�?е ограничени�? каналов и баннеров были пере�?читаны";
$GLOBALS['strBannerChannelResult'] = "�?иже приведены результаты проверки ограничений баннеров и каналов";
$GLOBALS['strChannelCompiledLimitationsValid'] = "В�?е ограничени�? канала корректны";
$GLOBALS['strBannerCompiledLimitationsValid'] = "В�?е ограничени�? баннера корректны";
$GLOBALS['strErrorsFound'] = "�?айдены ошибки";
$GLOBALS['strRepairCompiledLimitations'] = "Были найдены не�?оответ�?тви�?, которые вы можете и�?править нажав кнопку ниже.<br />";
$GLOBALS['strRecompile'] = "Пере�?читать";
$GLOBALS['strAppendCodesDesc'] = "При некоторых об�?то�?тель�?твах механизм до�?тавки может некорректно добавл�?ть коды трекеров, и�?пользуйте �?ледующие �?�?ылки дл�? проверки кодов в БД.";
$GLOBALS['strCheckAppendCodes'] = "Проверить коды";
$GLOBALS['strAppendCodesRecompiled'] = "В�?е коды были пере�?читаны";
$GLOBALS['strAppendCodesResult'] = "Результаты пере�?чета кодов";
$GLOBALS['strAppendCodesValid'] = "В�?е коды корректны";
$GLOBALS['strRepairAppenedCodes'] = "Были найдены некоторые не�?овпадени�?. Дл�? их коррекции нажмите пожалуй�?та кнопку ниже.";
$GLOBALS['strScheduledMaintenanceNotRun'] = "Запланированное об�?луживание не запу�?кало�?ь в течение по�?леднего ча�?а. Возможно, оно на�?троено некорректно.";
$GLOBALS['strDeliveryEngineDisagreeNotice'] = "При некоторых об�?то�?тель�?твах механизм до�?тавки может некорректно работать �? правами до�?тупа к баннерам и каналам, и�?пользуйте �?ледующие �?�?ылки дл�? проверки прав до�?тупа в БД.";
$GLOBALS['strServerCommunicationError'] = "<b>Св�?зи �? �?ервером обновлений нет, по�?тому ".MAX_PRODUCT_NAME." не в �?о�?то�?нии проверить, до�?тупна ли нова�? вер�?и�? в данный момент. Пожалуй�?та, повторите попытку позже.</b>";
?>