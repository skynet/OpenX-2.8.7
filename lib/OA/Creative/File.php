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
$Id: File.php 62345 2010-09-14 21:16:38Z chris.nutting $
*/

require_once MAX_PATH . '/lib/OA/Creative.php';
require_once MAX_PATH . '/www/admin/lib-storage.inc.php';

/**
 * A class to deal with uploaded creatives
 *
 */
class OA_Creative_File extends OA_Creative
{
    var $fileName;

    /**
     * Class constructor
     *
     * @param string $fileName
     * @return OA_Creative_File
     */
    function OA_Creative_File($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     * A method to load the creative from a file
     *
     * @param string $filePath
     * @return mixed True on success, PEAR_Error otherwise
     */
    function loadFile($filePath)
    {
        if (($this->content = @file_get_contents($filePath)) === false) {
            return new PEAR_Error("Cannot load image file");
        }

        return $this->readCreativeDetails($filePath);
    }

    /**
     * A method to load the creative from a file
     *
     * @param string $filePath
     * @param array  $aTypes Optional parameters of allowed types (@see getimagesize)
     * @return mixed True on success, PEAR_Error otherwise
     */
    function readCreativeDetails($filePath, $aTypes = null)
    {
        if (!isset($aTypes)) {
            $aTypes = array(
                IMAGETYPE_GIF  => 'gif',
                IMAGETYPE_PNG  => 'png',
                IMAGETYPE_JPEG => 'jpeg',
            );
        }

        if (!($aImage = @getimagesize($filePath))) {
            return new PEAR_Error("Unrecognized image file format");
        }

        if (!isset($aTypes[$aImage[2]])) {
            return new PEAR_Error('Unsupported image format');
        }

        $this->contentType = $aTypes[$aImage[2]];
        $this->width   = $aImage[0];
        $this->height  = $aImage[1];

        return true;
    }

    function getFileDetails()
    {
        return array(
            'filename'      => $this->fileName,
            'width'         => $this->width,
            'height'        => $this->height,
            'contenttype'   => $this->contentType,
            'pluginversion' => 0,
            'editswf'       => false
        );
    }

    function store($type)
    {
        $this->fileName = phpAds_ImageStore($type, $this->fileName, $this->content);
    }

    function getContentTypeByExtension($alt = false)
    {
        return OA_Creative_File::staticGetContentTypeByExtension(
            $this->fileName, $alt);
    }
    
    function staticGetContentTypeByExtension($fileName, $alt = false)
    {
        $contentType = '';

        $ext = substr($fileName, strrpos($fileName, '.') + 1);
        switch (strtolower($ext)) {
            case 'jpeg': $contentType = 'jpeg'; break;
            case 'jpg':  $contentType = 'jpeg'; break;
            case 'png':  $contentType = 'png';  break;
            case 'gif':  $contentType = 'gif';  break;
            case 'swf':  $contentType = $alt ? '' : 'swf';  break;
            case 'dcr':  $contentType = $alt ? '' : 'dcr';  break;
            case 'rpm':  $contentType = $alt ? '' : 'rpm';  break;
            case 'mov':  $contentType = $alt ? '' : 'mov';  break;
        }
        return $contentType;
    }

    /**
     * A factory method to load a cretive from a file
     *
     * @param string $filePath
     * @param string $fileName If empty, it will be generated from filePath
     * @return OA_Creative_File
     */
    function &factory($filePath, $fileName = '')
    {
        if (!@is_readable($filePath)) {
            return new PEAR_Error('Could not read the file: '.$filePath);
        }

        if (empty($fileName)) {
            $fileName = basename($filePath);
        }

        $validImageExtensions = 'png|svg|gif|jpg|jpeg|jpe|tif|tiff|ppm|bmp|rle|dib|tga|pcz|wbmp|wbm';
        if (preg_match('/\.swf$/i', $fileName)) {
            $type = 'Swf';
        } elseif (preg_match('/\.(?:dcr|rpm|mov)$/i', $fileName)) {
            $type = 'RichMedia';
        } elseif (preg_match('/\.('.$validImageExtensions.')$/i', $fileName)) {
            $type = 'Image';
        } else {
            return new PEAR_Error('The uploaded file does not have a valid extension. 
            The file must be an image file (JPG, PNG, GIF, etc.) or a SWF.');
        }

        require_once(MAX_PATH . '/lib/OA/Creative/File/' . $type . '.php');
        $className = 'OA_Creative_File_'.$type;

        $oCreative = new $className($fileName);

        $result = $oCreative->loadFile($filePath);
        if (PEAR::isError($result)) {
            return $result;
        }

        return $oCreative;
    }

    /**
     * A factory method to load a cretive from a variable
     *
     * @param string $fileName
     * @param string $content
     * @return OA_Creative_File
     */
    function &factoryString($fileName, $content)
    {
        // Create temp file
        $filePath = tempnam(MAX_PATH . '/var', 'oa_creative_');
        // Open temp file
        if (!($fp = @fopen($filePath, 'w'))) {
            return new PEAR_Error('Could not open the file: '.$filePath);
        }
        // Write to temp file
        if (!@fwrite($fp, $content)) {
            return new PEAR_Error('Could not write to file: '.$filePath);
        }

        // Get new instance
        $oCreative = &OA_Creative_File::factory($filePath, $fileName);

        // Cleanup
        @fclose($fp);
        @unlink($filePath);

        return $oCreative;
    }

    /**
     * A factory method to load a cretive from an uploaded file
     *
     * @todo: improve checks
     *
     * @param string $variableName
     * @return OA_Creative_File
     */
    function &factoryUploadedFile($variableName) {
        if (!empty($_FILES[$variableName]['error'])) {
            $aErrors = array(
                UPLOAD_ERR_INI_SIZE   => "file size exceeds PHP max allowed size",
                UPLOAD_ERR_FORM_SIZE  => "file size exceeds form max allowed size",
                UPLOAD_ERR_PARTIAL    => "partial upload",
                UPLOAD_ERR_NO_FILE    => "no file uploaded",
                UPLOAD_ERR_NO_TMP_DIR => "temp directory not available"
            );
            if (isset($aErrors[$_FILES[$variableName]['error']])) {
                $message = $aErrors[$_FILES[$variableName]['error']];
            } else {
                $message = 'Error code: '.$_FILES[$variableName]['error'];
            }
            return new PEAR_Error('An error occured dealing with the file upload: '.$message);
        }
        if (!isset($_FILES[$variableName]['tmp_name']) || !is_uploaded_file($_FILES[$variableName]['tmp_name'])) {
            return new PEAR_Error('Could not find the uploaded file: '.$variableName);
        }
        if (!isset($_FILES[$variableName]['name'])) {
            return new PEAR_Error('Could not find the uploaded file name: '.$variableName);
        }
        $fileName = basename($_FILES[$variableName]['name']);
        $filePath = $_FILES[$variableName]['tmp_name'];
        if (!@is_readable($filePath)) {
            // The uploaded file is not directly readable, we should use a file from the var folder instead
            $tmpName = tempnam(MAX_PATH . '/var', 'oa_creative_');
            if ($tmpName === false) {
                return new PEAR_Error('Cannot create a temporary file: '.$filePath);
            }
            // Move uploaded file to a temporary file
            if (!@move_uploaded_file($filePath, $tmpName)) {
                // Cleanup
                @unlink($tmpName);
                return new PEAR_Error('Could not move the uploaded file to: '.$tmpName);
            }
            $filePath = $tmpName;
        }

        // Get new instance
        $oCreative = &OA_Creative_File::factory($filePath, $fileName);

        // Cleanup
        if (isset($tmpName)) {
            @unlink($tmpName);
        }

        return $oCreative;
    }
}

?>