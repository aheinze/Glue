<?php

/*
	Class: PathHelper
		Helper for managing/retrieving environment paths (Thx yootheme)
*/
class PathHelper {
    
    /* base_path/url */
    public $base_path;
    public $base_url;
    
	/* paths */
    protected $_paths = array();
    
    public function __construct() {
        
        $urlparts = explode("/", $_SERVER['SCRIPT_NAME']);
        $base_url = str_replace('/'.$urlparts[count($urlparts)-1], '', $_SERVER['SCRIPT_NAME']);
        
        
        $this->base_path = dirname($_SERVER['SCRIPT_FILENAME']);
        $this->base_url  = $base_url;
    
    }
    
    /*
		Function: getPaths
			Get paths for a namespace.
			
		Parameters:
			$namespace - Namespace for path
		
		Returns:
			Array
	*/
    public function getPaths($namespace=null) {

        return $namespace ? $this->_paths[$namespace] : $this->_paths;
    }

    /*
		Function: register
			Register a path to a namespace.
			
		Parameters:
			$path - Absolute path
			$namespace - Namespace for path
		
		Returns:
			Void
	*/
	public function register($path, $namespace = 'default') {
	    
	    if (!isset($this->_paths[$namespace])) {
	        $this->_paths[$namespace] = array();
	    }

	    array_unshift($this->_paths[$namespace], $path);
	}

	/*
		Function: path
			Retrieve absolute path to a file or directory

		Parameters:
			$resource - Resource with namespace, example: "assets:js/app.js"

		Returns:
			Mixed
	*/
	public function path($resource) {
				
		// parse resource
		extract($this->_parse($resource));
		
		return $this->_find($paths, $path);
	}
	
    /*
		Function: url
			Retrieve absolute url to a file

		Parameters:
			$resource - Resource with namespace, example: "assets:js/app.js"

		Returns:
			Mixed
	*/
	public function url($resource) {

		// init vars
	    $parts = explode('?', $resource);
	    $url   = str_replace(DIRECTORY_SEPARATOR, '/', $this->path($parts[0]));

	    if ($url) {
	        
	        if (isset($parts[1])) {
	            $url .= '?'.$parts[1];    
	        }

	        $url = $this->base_url.'/'.ltrim(preg_replace('/'.preg_quote(str_replace(DIRECTORY_SEPARATOR, '/', base_path), '/').'/', '', $url, 1), '/');
	    }
	
	    return $url;
	}

	/*
		Function: files
			Retrieve list of files from resource

		Parameters:
			$resource - Resource with namespace, example: "assets:directory/"

		Returns:
			Array
	*/
	public function files($resource, $recursive = false) {
		return $this->ls($resource, 'file', $recursive);
	}

	/*
		Function: dirs
			Retrieve list of directories from resource

		Parameters:
			$resource - Resource with namespace, example: "assets:directory/"

		Returns:
			Array
	*/
	public function dirs($resource, $recursive = false) {
		return $this->ls($resource, 'dir', $recursive);
	}

	/*
		Function: ls
			Retrieve list of files or directories from resource

		Parameters:
			$resource - Resource with namespace, example: "assets:directory/"

		Returns:
			Array
	*/
	public function ls($resource, $mode = 'file', $recursive = false) {
		
		$files = array();
		$res   = $this->_parse($resource);
		
		foreach ($res['paths'] as $path) {
			foreach ($this->_list(realpath($path.'/'.$res['path']), '', $mode, $recursive) as $file) {
				if (!in_array($file, $files)) {
					$files[] = $file;
				}
			}
		}
		
		return $files;
	}
	
	/*
		Function: _parse
			Parse resource string.

		Parameters:
			$resource - Path to resource

		Returns:
			String
	*/
	protected function _parse($resource) {
	    
	    // init vars
		$parts     = explode(':', $resource, 2);
		$count     = count($parts);
		$path      = '';
		$namespace = 'default';

		// parse resource path
		if ($count == 1) {
			list($path) = $parts;
		} elseif ($count == 2) {
			list($namespace, $path) = $parts;
		}
		
		// remove heading slash or backslash
		$path = ltrim($path, "\\/");

	    // get paths for namespace, if exists
		$paths = isset($this->_paths[$namespace]) ? $this->_paths[$namespace] : array();

		return compact('namespace', 'paths', 'path');
    }

	/*
		Function: _find
			Find file or directory in paths

		Parameters:
			$paths - Paths to search in
			$file - File or directory

		Returns:
			Mixed
	*/	
	protected function _find($paths, $file) {

		$paths = (array) $paths;
		$file  = ltrim($file, "\\/");

		foreach ($paths as $path) {
			
			$fullpath = realpath("$path/$file");
			$path     = realpath($path);

			if (file_exists($fullpath) && substr($fullpath, 0, strlen($path)) == $path) {
				return $fullpath;
			}
		}

		return false;
	}

	/*
		Function: _list
			List files or directories in a path

		Parameters:
			$path - Paths to search in
			$mode - Mode 'file' or 'dir'
			$prefix - Prefix prepended to every file/directory
			$recursive - Recurse subdirectories

		Returns:
			Array
	*/	
	protected function _list($path, $prefix = '', $mode = 'file', $recursive = false) {

		$files  = array();
	    $ignore = array('.', '..', '.DS_Store', 'cgi-bin', '.svn', '.git');

		if (is_readable($path) && is_dir($path) && ($scan = scandir($path))) {
			foreach ($scan as $file) {
				
				// continue if ignore match
				if (in_array($file, $ignore)) {
					continue;
				}

	            if (is_dir($path.'/'.$file)) {

					// add dir
					if ($mode == 'dir') {
						$files[] = $prefix.$file;
					}

					// continue if not recursive
					if (!$recursive) {
						continue;
					}
					
					// read subdirectory
	            	$files = array_merge($files, $this->_list($path.'/'.$file, $prefix.$file.'/', $mode, $recursive));

				} else {
					
					// add file
					if ($mode == 'file') {
						$files[] = $prefix.$file;
					}

	            }
				
			}
		}

		return $files;
	}

}