<?php

class RequestHelper {
    
    /* mimeTypes */
    public static $mimeTypes = array(
        'asc'   => 'text/plain',
        'au'    => 'audio/basic',
        'avi'   => 'video/x-msvideo',
        'bin'   => 'application/octet-stream',
        'class' => 'application/octet-stream',
        'css'   => 'text/css',
        'csv'	=> 'application/vnd.ms-excel',
        'doc'   => 'application/msword',
        'dll'   => 'application/octet-stream',
        'dvi'   => 'application/x-dvi',
        'exe'   => 'application/octet-stream',
        'htm'   => 'text/html',
        'html'  => 'text/html',
        'json'  => 'application/json',
        'js'    => 'application/x-javascript',
        'txt'   => 'text/plain',
        'bmp'   => 'image/bmp',
        'rss'   => 'application/rss+xml',
        'atom'  => 'application/atom+xml',
        'gif'   => 'image/gif',
        'jpeg'  => 'image/jpeg',
        'jpg'   => 'image/jpeg',
        'jpe'   => 'image/jpeg',
        'png'   => 'image/png',
        'ico'   => 'image/vnd.microsoft.icon',
        'mpeg'  => 'video/mpeg',
        'mpg'   => 'video/mpeg',
        'mpe'   => 'video/mpeg',
        'qt'    => 'video/quicktime',
        'mov'   => 'video/quicktime',
        'wmv'   => 'video/x-ms-wmv',
        'mp2'   => 'audio/mpeg',
        'mp3'   => 'audio/mpeg',
        'rm'    => 'audio/x-pn-realaudio',
        'ram'   => 'audio/x-pn-realaudio',
        'rpm'   => 'audio/x-pn-realaudio-plugin',
        'ra'    => 'audio/x-realaudio',
        'wav'   => 'audio/x-wav',
        'zip'   => 'application/zip',
        'pdf'   => 'application/pdf',
        'xls'   => 'application/vnd.ms-excel',
        'ppt'   => 'application/vnd.ms-powerpoint',
        'wbxml' => 'application/vnd.wap.wbxml',
        'wmlc'  => 'application/vnd.wap.wmlc',
        'wmlsc' => 'application/vnd.wap.wmlscriptc',
        'spl'   => 'application/x-futuresplash',
        'gtar'  => 'application/x-gtar',
        'gzip'  => 'application/x-gzip',
        'swf'   => 'application/x-shockwave-flash',
        'tar'   => 'application/x-tar',
        'xhtml' => 'application/xhtml+xml',
        'snd'   => 'audio/basic',
        'midi'  => 'audio/midi',
        'mid'   => 'audio/midi',
        'm3u'   => 'audio/x-mpegurl',
        'tiff'  => 'image/tiff',
        'tif'   => 'image/tiff',
        'rtf'   => 'text/rtf',
        'wml'   => 'text/vnd.wap.wml',
        'wmls'  => 'text/vnd.wap.wmlscript',
        'xsl'   => 'text/xml',
        'xml'   => 'text/xml'
    );

    /* mobileDevices */
    public static $mobileDevices = array(
        "midp","240x320","blackberry","netfront","nokia","panasonic","portalmmm","sharp","sie-","sonyericsson",
        "symbian","windows ce","benq","mda","mot-","opera mini","philips","pocket pc","sagem","samsung",
        "sda","sgh-","vodafone","xda","iphone", "ipod","android"
    );
	
	/* statusCodes */
	public static $statusCodes = array(
		// Informational 1xx
		100 => 'Continue',
		101 => 'Switching Protocols',
		// Successful 2xx
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		// Redirection 3xx
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		// Client Error 4xx
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Request Range Not Satisfiable',
		417 => 'Expectation Failed',
		// Server Error 5xx
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported'
	);

	/*
		Function: is
			Checks type.

		Parameters:
			$type
        
        Returns:
			Boolean
	*/
    public static function is($type){

        switch(strtolower($type)){
          case 'ajax':
            return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
            break;
          
          case 'mobile':
            return preg_match('/(' . implode('|',self::$mobileDevices). ')/i',strtolower($_SERVER['HTTP_USER_AGENT']));
            break;
          
          case 'post':
            return (strtolower($_SERVER['REQUEST_METHOD']) == 'post');
            break;
          
          case 'get':
            return (strtolower($_SERVER['REQUEST_METHOD']) == 'get');
            break;
            
          case 'put':
            return (strtolower($_SERVER['REQUEST_METHOD']) == 'put');
            break;
            
          case 'delete':
            return (strtolower($_SERVER['REQUEST_METHOD']) == 'delete');
            break;
            
          case 'ssl':
            return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            break;
        }
        
        return false;
    }

	/*
		Function: getClientIp
			Get ClientIp

		Returns:
			Misc
	*/
    public static function getClientIp(){

        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
            // Use the forwarded IP address, typically set when the
            // client is using a proxy server.
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])){
            // Use the forwarded IP address, typically set when the
            // client is using a proxy server.
            return $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (isset($_SERVER['REMOTE_ADDR'])){
            // The remote IP address
            return $_SERVER['REMOTE_ADDR'];
        }
    }

	/*
		Function: getMimeType
			Gets MimeType by value.

		Parameters:
			$val

		Returns:
			String
	*/
    function getMimeType($val) {
     
        $extension = strtolower(array_pop(explode('.',$val)));

        return (isset(self::$mimeTypes[$extension]) ? self::$mimeTypes[$extension]:'text/html');
    }
	
	/*
		Function: get
			Retrieve a value from a request variable  (Thx yootheme)

		Parameters:
			$var - Variable name (hash:name)
			$type - Variable type (string, int, float, bool, array)
			$default - Default value

		Returns:
			Mixed
	*/	
    public function get($var, $type, $default = null) {
		
		$hash  = 'request';
		
		// parse variable name
		if (strpos($var, ':') !== false) {
            list($hash, $name) = explode(':', $var);
        }
		
		// get hash array, if name is empty
		if (!isset($name)) {
			return $this->_hash($hash);
		}
		
		// access a array value ?
		if (strpos($name, '.') !== false) {

			$parts = explode('.', $name);
			$array = $this->_get(array_shift($parts), $default, $hash, $type);

			foreach ($parts as $part) {

				if (!is_array($array) || !isset($array[$part])) {
					return $default;
				}

				$array =& $array[$part];
			}

			return $array;
		}

		return $this->_get($name, $default, $hash, $type);
    }
	
	/*
		Function: _get
			Get variable from http request.

		Returns:
			Mixed
	*/	
	protected function _get($name, $default = null, $hash = 'default', $type = 'none') {
		
		$input = $this->_hash($hash);
		$var   = (isset($input[$name]) && $input[$name] !== null) ? $input[$name] : $default;

		if (in_array($type, array('string', 'int', 'float', 'bool', 'array'))) {
			settype($var, $type);
		}

		return $var;
	}

	/*
		Function: _hash
			Get hash from http request.

		Returns:
			Mixed
	*/	
	protected function _hash($hash) {

		switch (strtoupper($hash)) {
			case 'GET' :
				$input = $_GET;
				break;
			case 'POST' :
				$input = $_POST;
				break;
			case 'COOKIE' :
				$input = $_COOKIE;
				break;
			default:
				$input = $_REQUEST;
				break;
		}

		return $input;
	}

}