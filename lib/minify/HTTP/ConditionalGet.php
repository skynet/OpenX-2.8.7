<?php
/**
 * Class HTTP_ConditionalGet  
 * @package Minify
 * @subpackage HTTP
 */

/**
 * Implement conditional GET via a timestamp or hash of content
 *
 * E.g. Content from DB with update time:
 * <code>
 * list($updateTime, $content) = getDbUpdateAndContent();
 * $cg = new HTTP_ConditionalGet(array(
 *     'lastModifiedTime' => $updateTime
 * ));
 * $cg->sendHeaders();
 * if ($cg->cacheIsValid) {
 *     exit();
 * }
 * echo $content;
 * </code>
 *
 * E.g. Content from DB with no update time:
 * <code>
 * $content = getContentFromDB();
 * $cg = new HTTP_ConditionalGet(array(
 *     'contentHash' => md5($content)
 * ));
 * $cg->sendHeaders();
 * if ($cg->cacheIsValid) {
 *     exit();
 * }
 * echo $content;
 * </code>
 * 
 * E.g. Static content with some static includes:
 * <code>
 * // before content
 * $cg = new HTTP_ConditionalGet(array(
 *     'lastUpdateTime' => max(
 *         filemtime(__FILE__)
 *         ,filemtime('/path/to/header.inc')
 *         ,filemtime('/path/to/footer.inc')
 *     )
 * ));
 * $cg->sendHeaders();
 * if ($cg->cacheIsValid) {
 *     exit();
 * }
 * </code>
 * @package Minify
 * @subpackage HTTP
 * @author Stephen Clay <steve@mrclay.org>
 */
class HTTP_ConditionalGet {

    /**
     * Does the client have a valid copy of the requested resource?
     * 
     * You'll want to check this after instantiating the object. If true, do
     * not send content, just call sendHeaders() if you haven't already.
     *
     * @var bool
     */
    public $cacheIsValid = null;

    /**
     * @param array $spec options
     * 
     * 'isPublic': (bool) if true, the Cache-Control header will contain 
     * "public", allowing proxy caches to cache the content. Otherwise
     * "private" will be sent, allowing only browsers to cache. (default false)
     * 
     * 'lastModifiedTime': (int) if given, both ETag AND Last-Modified headers
     * will be sent with content. This is recommended.
     *
     * 'eTag': (string) if given, this will be used as the ETag header rather
     * than values based on lastModifiedTime or contentHash.
     * 
     * 'contentHash': (string) if given, only the ETag header can be sent with
     * content (only HTTP1.1 clients can conditionally GET). The given string 
     * should be short with no quote characters and always change when the 
     * resource changes (recommend md5()). This is not needed/used if 
     * lastModifiedTime is given.
     * 
     * 'invalidate': (bool) if true, the client cache will be considered invalid
     * without testing. Effectively this disables conditional GET. 
     * (default false)
     * 
     * 'maxAge': (int) if given, this will set the Cache-Control max-age in 
     * seconds, and also set the Expires header to the equivalent GMT date. 
     * After the max-age period has passed, the browser will again send a 
     * conditional GET to revalidate its cache.
     * 
     * @return null
     */
    public function __construct($spec) {
        $scope = (isset($spec['isPublic']) && $spec['isPublic'])
            ? 'public'
            : 'private';
        $maxAge = 0;
        // backwards compatibility (can be removed later)
        if (isset($spec['setExpires']) 
            && is_numeric($spec['setExpires'])
            && ! isset($spec['maxAge'])) {
            $spec['maxAge'] = $spec['setExpires'] - $_SERVER['REQUEST_TIME'];
        }
        if (isset($spec['maxAge'])) {
            $maxAge = $spec['maxAge'];
            $this->_headers['Expires'] = self::gmtDate(
                $_SERVER['REQUEST_TIME'] + $spec['maxAge'] 
            );
        }
    	if (isset($spec['lastModifiedTime'])) {
    		$this->_setLastModified($spec['lastModifiedTime']);
    		if (isset($spec['eTag'])) { // Use it
    			$this->_setEtag($spec['eTag'], $scope);
    		} else { // base both headers on time
    			$this->_setEtag($spec['lastModifiedTime'], $scope);
    		}
    	} elseif (isset($spec['eTag'])) { // Use it
    		$this->_setEtag($spec['eTag'], $scope);
    	} elseif (isset($spec['contentHash'])) { // Use the hash as the ETag
    		$this->_setEtag($spec['contentHash'], $scope);
    	}
        $this->_headers['Cache-Control'] = "max-age={$maxAge}, {$scope}, must-revalidate";
        // invalidate cache if disabled, otherwise check
        $this->cacheIsValid = (isset($spec['invalidate']) && $spec['invalidate'])
            ? false
            : $this->_isCacheValid();
    }
    
    /**
     * Get array of output headers to be sent
     * 
     * In the case of 304 responses, this array will only contain the response
     * code header: array('_responseCode' => 'HTTP/1.0 304 Not Modified')
     * 
     * Otherwise something like: 
     * <code>
     * array(
     *     'Cache-Control' => 'max-age=0, public, must-revalidate'
     *     ,'ETag' => '"foobar"'
     * )
     * </code>
     *
     * @return array 
     */
    public function getHeaders() {
        return $this->_headers;
    }

    /**
     * Set the Content-Length header in bytes
     * 
     * With most PHP configs, as long as you don't flush() output, this method
     * is not needed and PHP will buffer all output and set Content-Length for 
     * you. Otherwise you'll want to call this to let the client know up front.
     * 
     * @param int $bytes
     * 
     * @return int copy of input $bytes
     */
    public function setContentLength($bytes) {
        return $this->_headers['Content-Length'] = $bytes;
    }

    /**
     * Send headers
     * 
     * @see getHeaders()
     * 
     * Note this doesn't "clear" the headers. Calling sendHeaders() will
     * call header() again (but probably have not effect) and getHeaders() will
     * still return the headers.
     *
     * @return null
     */
    public function sendHeaders() {
        $headers = $this->_headers;
        if (array_key_exists('_responseCode', $headers)) {
            header($headers['_responseCode']);
            unset($headers['_responseCode']);
        }
        foreach ($headers as $name => $val) {
            header($name . ': ' . $val);
        }
    }
    
    /**
     * Get a GMT formatted date for use in HTTP headers
     * 
     * <code>
     * header('Expires: ' . HTTP_ConditionalGet::gmtdate($time));
     * </code>  
     *
     * @param int $time unix timestamp
     * 
     * @return string
     */
    public static function gmtDate($time) {
        return gmdate('D, d M Y H:i:s \G\M\T', $time);
    }
    
    protected $_headers = array();
    protected $_lmTime = null;
    protected $_etag = null;
    
    protected function _setEtag($hash, $scope) {
        $this->_etag = '"' . $hash
            . substr($scope, 0, 3)
            . '"';
        $this->_headers['ETag'] = $this->_etag;
    }

    protected function _setLastModified($time) {
        $this->_lmTime = (int)$time;
        $this->_headers['Last-Modified'] = self::gmtDate($time);
    }

    /**
     * Determine validity of client cache and queue 304 header if valid
     */
    protected function _isCacheValid()
    {
        if (null === $this->_etag) {
            // lmTime is copied to ETag, so this condition implies that the
            // client received neither ETag nor Last-Modified, so can't 
            // possibly has a valid cache.
            return false;
        }
        $isValid = ($this->resourceMatchedEtag() || $this->resourceNotModified());
        if ($isValid) {
            $this->_headers['_responseCode'] = 'HTTP/1.0 304 Not Modified';
        }
        return $isValid;
    }

    protected function resourceMatchedEtag() {
        if (!isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
            return false;
        }
        $cachedEtagList = get_magic_quotes_gpc()
            ? stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])
            : $_SERVER['HTTP_IF_NONE_MATCH'];
        $cachedEtags = explode(',', $cachedEtagList);
        foreach ($cachedEtags as $cachedEtag) {
            if (trim($cachedEtag) == $this->_etag) {
                return true;
            }
        }
        return false;
    }

    protected function resourceNotModified() {
        if (!isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            return false;
        }
        $ifModifiedSince = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
        if (false !== ($semicolon = strrpos($ifModifiedSince, ';'))) {
            // IE has tacked on extra data to this header, strip it
            $ifModifiedSince = substr($ifModifiedSince, 0, $semicolon);
        }
        return ($ifModifiedSince == self::gmtDate($this->_lmTime));
    }
}
