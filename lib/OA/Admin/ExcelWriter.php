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
$Id: ExcelWriter.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

require_once MAX_PATH . '/lib/max/Plugin/Common.php';
require_once 'Spreadsheet/Excel/Writer.php';

/**
 * MS Excel output provider for for OpenX reports
 *
 * @package OpenXAdmin
 *
 * @TODO Clean up code, document, and make generic for OSS release.
 */
class OA_Admin_ExcelWriter
{
    /**
     * ???
     *
     * @var Spreadsheet_Excel_Writer
     */
    var $_workbook;

    var $_formats;
    var $_worksheet;
    var $_currentRow;

    function &_getExcelWriter()
    {
        return $this->_workbook;
    }

    function _setExcelWriter(&$writer)
    {
        $this->_workbook =& $writer;
    }

    /**
     * Is this code ever used?
     * @deprecated
     */
    function addWorksheet($name = '')
    {
        $workbook =& $this->_getExcelWriter();
        $worksheet = $workbook->addWorksheet($name);
        $worksheet->setInputEncoding('UTF-8');
        return $worksheet;
    }

    function getFormat($aFormat)
    {
        if (!is_array($aFormat)) {
            return null;
        }
        sort($aFormat);
        $formatKey = implode('.',$aFormat);
        if (empty($formatKey)) {
            return null;
        }
        if (empty($this->formats[$formatKey])) {
            $workbook =& $this->_getExcelWriter();
            $oFormat =&  $workbook->addFormat();
            foreach ($aFormat as $format) {
                switch ($format) {
                    case 'h1' :
                        $oFormat->setFgColor(44);
                        $oFormat->setPattern(1);
                        $oFormat->setSize(14);
                        $oFormat->setBold();
                        break;
                    case 'h2' :
                        $oFormat->setFgColor('silver');
                        $oFormat->setPattern(1);
                        $oFormat->setBold();
                        break;
                    case 'text-center' :
                        $oFormat->setHAlign('center');
                        break;
                    case 'fg-red' :
                        $oFormat->setFgColor('red');
                        break;
                    case 'warning':
                        $oFormat->setColor('red');
                        $oFormat->setBold();
                        break;
                    case 'number' :
                        // We need to show a dash if a 'number' value is zero
                        // We're not using $GLOBALS['excel_integer_formatting']
                        $oFormat->setNumFormat('#,##0;-#,##0;-');
                        break;
                    case 'percent' :
                        // We prefer to see a dash instead of 0.000%
                        // Use no. of decimal places specified in preferences
                        $oFormat->setNumFormat($this->getPercentageDecimalFormat());
                        break;
                    case 'text':
                        break;
                    case 'id':
                        $oFormat->setNumFormat('###0');
                        break;
                    case 'percent0' :
                        $oFormat->setNumFormat('0%');
                        break;
                    case 'currency' :
                        $oFormat->setNumFormat('"�"#,##0.00');
                        break;
                    case 'decimal' :
                        $oFormat->setNumFormat($GLOBALS['excel_decimal_formatting']);
                        break;
                    case 'timespan' :
                        $oFormat->setNumFormat('d"d" hh:mm:ss');
                        break;
                    case 'date' :
                        $oFormat->setNumFormat('d-mmm-yy');
                        break;
                    case 'datetime' :
                        $oFormat->setNumFormat('d-mmm-yy hh:mm:ss');
                        break;
                    case 'border-top' :
                        $oFormat->setTop(2);
                        break;
                    case 'border-left' :
                        $oFormat->setLeft(2);
                        break;
                    case 'border-right' :
                        $oFormat->setRight(2);
                        break;
                    case 'border-bottom' :
                        $oFormat->setBottom(2);
                        break;
                }
            }

            $this->formats[$formatKey] = $oFormat;
        }

        return $this->formats[$formatKey];
    }

    function createReportWorksheet($name, $reportTitle, $aReportParameters, $aReportWarnings, $firstColSize = 8)
    {
        $workbook =& $this->_getExcelWriter();
        if (is_null($workbook)) {
            return;
        }
        $worksheet =&  $workbook->addWorksheet($name);
        $worksheet->setInputEncoding('UTF-8');

        // Insert m3 bitmap
        // Note that a scale of (1, 0.8) is specified to work around an underlying scale bug
        //$worksheet->insertBitmap(0,0,MAX_PATH . '/www/admin/images/m3.bmp', 5, 3, 1, 0.8);

        $row = 1;
        // Write Report Title
        $worksheet->write($row, 1, $reportTitle, $this->getFormat(array('h1', 'border-top','border-left')));
        $worksheet->write($row, 2, '', $this->getFormat(array('h1','border-top','border-right')));
        $row++;

        // Write Report Parameters
        $numItems = count($aReportParameters);
        $count = 0;
        foreach ($aReportParameters as $header => $value) {
            $count++;
            $bottomFormat = ($count == $numItems) ? array('border-bottom') : array();
            $worksheet->write($row, 1, $header, $this->getFormat(array_merge($bottomFormat, array('h2', 'border-left'))));
            $worksheet->write($row, 2, $value, $this->getFormat(array_merge($bottomFormat, array('border-right'))));
            $row++;
        }
        $worksheet->setColumn(0,0,$firstColSize);
        $row += 2;

        // Write Report Warnings
        if (count($aReportWarnings)) {
            foreach ($aReportWarnings as $value) {
                $worksheet->write($row, 1, $value, $this->getFormat(array('warning')));
                $row++;
            }
            $row += 2;
        }

        $this->_worksheet[$name] =& $worksheet;
        $this->_currentRow[$name] = $row;
    }

    function createReportSection($name, $reportDataTitle, $aReportDataHeaders, $aReportData, $colSize = 25, $addFormat = null)
    {
        // Get the worksheet
        $aSearch = array('{row}','{column0}','{column1}','{column2}','{column3}','{column4}','{column5}','{column6}','{column7}','{column8}','{column9}','{column10}','{column11}','{column12}','{column13}','{column14}','{column15}','{column16}','{column17}','{column18}','{column19}','{column20}','{column21}','{column22}','{column23}','{column24}');
        $aReplace = array('','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $worksheet =& $this->_worksheet[$name];
        $row = $this->_currentRow[$name];

        // Write Report Data Headers
        $col = 1;
        $numItems = count($aReportDataHeaders);
        $aColumnFormats = array();
        foreach ($aReportDataHeaders as $header => $format) {
            $aFormat = array('h2', 'text-center');
            if($col == 1) {
                $aFormat[] = 'border-left';
            }
            if ($col == $numItems) {
                $aFormat[] = 'border-right';
            }
            $worksheet->write($row+1, $col, $header, $this->getFormat($aFormat));
            $aColumnFormats[$col] = $format;
            $col++;
        }

        // Determine how many columns are actually used in this worksheet
        $columnCount = $col > $columnCount ? $col - 1 : $columnCount;

        // Set column widths
        $worksheet->setColumn(1,$columnCount,$colSize);

        // Write the report data title
        for ($col=1; $col<=$columnCount; $col++) {
            $value = '';
            $aFormat = array('h1','border-top');
            if ($col == 1) {
                $value = $reportDataTitle;
                $aFormat[] = 'border-left';
            }
            if ($col == $columnCount) {
                $aFormat[] = 'border-right';
            }
            $worksheet->write($row, $col, $value, $this->getFormat($aFormat));
        }
        $row += 2;
        // Write the report data
        $firstRow = $row;
        $lastRow = $row + count($aReportData)-1;
        if ($lastRow < $firstRow) { // There was no data
            for ($col=1; $col<=$columnCount; $col++) {
                $value = '';
                $aFormat = array('border-bottom');
                if ($col == 1) {
                    $value = 'There is no data to display.';
                    $aFormat[] = 'border-left';
                }
                if ($col == $columnCount) {
                    $aFormat[] = 'border-right';
                }
                $worksheet->write($row, $col, $value, $this->getFormat($aFormat));
            }
            $row++;
        } else {
            foreach ($aReportData as $k => $aReportDataRow) {

                $col = 1;

                foreach ($aReportDataRow as $value) {
                    $aFormat = array($aColumnFormats[$col]);
                    if ($col == 1) {
                        $aFormat[] = 'border-left';
                    }
                    if ($col == $columnCount) {
                        $aFormat[] = 'border-right';
                    }
                    if ($row == $lastRow) {
                        $aFormat[] = 'border-bottom';
                    }
                    if ($value === false) {
                        // We prefer our unset values to be visible
                        $value = '-';
                    }

                    //additional field formating
                    if($addFormat[$col][$k]) {
                        $aFormat[] = $addFormat[$col][$k];
                    }

                    if (substr($value,0,1) == '=') {
                        $aReplace[0] = $row+1;
                        $value = str_replace($aSearch, $aReplace, $value);

                        $err = $worksheet->writeFormula($row, $col++, $value, $this->getFormat($aFormat));
                    } else {
                        $worksheet->write($row, $col++, $value, $this->getFormat($aFormat));
                    }
                }
                $row++;
            }
        }

        $row += 2;
        $this->_currentRow[$name] = $row;
    }

    function getPercentageDecimalFormat()
    {
        for ($cnt = 0 ; $cnt < $GLOBALS['pref']['ui_percentage_decimals']; $cnt++) {
            $strPercentageDecimalPlaces .= '0';
        }
        $strPercentageDecimalFormat = '#,##0.'.$strPercentageDecimalPlaces.'%;-#,##0.'.$strPercentageDecimalPlaces.'%;-';

        return $strPercentageDecimalFormat;
    }

    function convertToDate($value)
    {
        if (!empty($value)) {
            $value = strtotime($value);
            $value = ($value + date('Z', $value) + 86400 * 25569) / 86400;
        }

        return $value;
    }

    function openWithFilename($filename)
    {
        require_once 'Spreadsheet/Excel/Writer.php';

        $workbook = new Spreadsheet_Excel_Writer();
        $workbook->setVersion(8, 'UTF-8');
        $workbook->setTempDir(MAX_PATH .'/var/cache');
        $this->_setExcelWriter($workbook);
        $workbook->send($filename);
    }

    function closeAndSend()
    {
        $workbook =& $this->_getExcelWriter();
        if (!is_null($workbook) && count($workbook->worksheets())>0) {
        	$workbook->close();
    	}
    }

}
?>
