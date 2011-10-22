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
$Id: translation.php 28570 2008-11-06 16:21:37Z chris.nutting $
*/



// Note: New translations not found in original lang files but found in CSV
$GLOBALS['strDeliveryLimitations'] = "發布限制";
$GLOBALS['strChooseSection'] = "選擇章節";
$GLOBALS['strRecalculatePriority'] = "重新計算優先級";
$GLOBALS['strCheckBannerCache'] = "檢查廣告緩存";
$GLOBALS['strBannerCacheErrorsFound'] = "經查，數據庫廣告緩存發現錯誤。在手工修正這些錯誤之前，這些廣告將無法正常運行。";
$GLOBALS['strBannerCacheOK'] = "未發現錯誤，您的數據庫廣告緩存已是最新的";
$GLOBALS['strBannerCacheDifferencesFound'] = "經查，數據庫廣告緩存不是最新的，需要重建。點擊這裡自動更新緩存。";
$GLOBALS['strBannerCacheRebuildButton'] = "重構";
$GLOBALS['strRebuildDeliveryCache'] = "重構數據庫廣告緩存";
$GLOBALS['strBannerCacheExplaination'] = "廣告數據庫緩存的作用是加速廣告的投放\n 該緩存需要在以下情況下更新：\n          <ul> \n                  <li>您升級了OpenX</li>\n                   <li>您將OpenX遷移到一個新的伺服器上</li>\n          </ul>";
$GLOBALS['strCache'] = "發布緩存";
$GLOBALS['strAge'] = "年齡";
$GLOBALS['strDeliveryCacheSharedMem'] = "共享內存目前正被發布緩存佔用";
$GLOBALS['strDeliveryCacheDatabase'] = "數據正在存儲發布緩存";
$GLOBALS['strDeliveryCacheFiles'] = "發布緩存正在存儲到你伺服器上的多個文件 ";
$GLOBALS['strStorage'] = "存儲";
$GLOBALS['strMoveToDirectory'] = "將圖片從數據庫中移動到目錄下 ";
$GLOBALS['strStorageExplaination'] = "圖片文件可存儲在數據庫或文件系統中。存儲在文件系統中將比存儲在數據庫中效率更高。";
$GLOBALS['strSearchingUpdates'] = "查找更新，請稍候……";
$GLOBALS['strAvailableUpdates'] = "提供的更新";
$GLOBALS['strDownloadZip'] = "下載 (.zip)";
$GLOBALS['strDownloadGZip'] = "下載 (.tar.gz)";
$GLOBALS['strUpdateAlert'] = "". MAX_PRODUCT_NAME ." 新版本已發布。                                                           \n \n 您希望了解更多關於新版本的資訊嗎？? ";
$GLOBALS['strUpdateAlertSecurity'] = "". MAX_PRODUCT_NAME ." 新版本已發布。                                                                    \n \n 由於提供了很多安全方面的修改? 所以強烈建議您更新到新版本。 ";
$GLOBALS['strNoNewVersionAvailable'] = "您". MAX_PRODUCT_NAME ."的版本已是最新的。 ";
$GLOBALS['strNewVersionAvailable'] = "<b>". MAX_PRODUCT_NAME ."的新版本已經發布。 </b><br />由於修改一些已知的問題及增加了一些新功能。所以建議您安裝這個更新。如果您希望進一步了解相關細心，請參閱文件中的相關文檔。\n ";
$GLOBALS['strSecurityUpdate'] = "<b>由於涉及若干個安全更新，所以強烈建議您升級。</b> \n 您現在的". MAX_PRODUCT_NAME ."版本，可能因為攻擊而變得不可靠。如果希望了解進一步的資訊，請參閱文件中的相關文檔。 ";
$GLOBALS['strNotAbleToCheck'] = "<b>由於您伺服器上沒有XML引申，所以". MAX_PRODUCT_NAME ."無法查找是否有新的更新提供。</b>";
$GLOBALS['strForUpdatesLookOnWebsite'] = "如果您希望知道是否有新的版本提供，請查閱我們的網站。";
$GLOBALS['strClickToVisitWebsite'] = "點擊訪問官方網站";
$GLOBALS['strCurrentlyUsing'] = "你正在使用的";
$GLOBALS['strRunningOn'] = "運行的";
$GLOBALS['strAndPlain'] = "與";
$GLOBALS['strStatisticsExplaination'] = "您已經啟用了<i>緊縮統計</i>, 但是您的報表還是詳細格式.您是否願意把現有的詳細格式轉換為緊縮格式?";
$GLOBALS['strBannerCacheFixed'] = "成功完成廣告數據庫緩存重構, 數據庫緩存已經更新.";
$GLOBALS['strEncodingConvert'] = "轉換";
?>