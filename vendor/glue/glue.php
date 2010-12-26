<?php
    
require_once(__DIR__.'/helpers/path.php');

/*
	Class: Glue
		Glue class. Main Class.
*/    
class Glue {
    
    /* instance */
	protected static $_instance;
    
    /* helpers */
	protected $_helpers = array();
    
	/*
		Function: __construct
			Constructor.

		Returns:
			Void
	*/
    public function __construct() {
        
        $this->_helpers['path'] = $path = new PathHelper();
        
        $path->register(__DIR__ , 'glue');
        $path->register(__DIR__.'/helpers', 'helpers');
    }
    
	/*
		Function: instance
			Get instance.

		Returns:
			Object
	*/
    public static function instance() {

		// add instance, if not exists
		if (!isset(self::$_instance)) {
			self::$_instance = new Glue();
		}

		return self::$_instance;
	}
    
	/*
		Function: helper
			Get glue helper.

		Returns:
			Object
	*/
    public function helper($name) {
        
        if (!isset($this->_helpers[$name])) {
            
            $namespace = 'helpers';
            $h         = $name;
            
            if (strpos($name, ":") !== false) {
                list($namespace, $h) = explode(":", $name, 2);
            }
            
            $class = $h.'Helper';
            
            // autoload helper class
			if (!class_exists($class) && ($file = $this->helper('path')->path("$namespace:$h.php"))) {
                require_once($file);
			}
            
            $this->_helpers[$name] = new $class($this);
        }
        
        return $this->_helpers[$name];
    }
    
    /*
		Function: addIncludePath
			Register include path

		Parameters:
			$path - Include path

		Returns:
			Void
	*/	
	public static function addIncludePath($path){

		if(is_array($path)){
		  foreach($path as $p){
			set_include_path(get_include_path() . PATH_SEPARATOR . $p);
		  }
		}else{
		  set_include_path(get_include_path() . PATH_SEPARATOR . $path);
		}
	}
    
    /*
		Function: __get
			Retrieve a helper

		Parameters:
			$name - Helper name

		Returns:
			Mixed
	*/	
	public function __get($name) {
		return $this->helper($name);
	}
}

/*
    Function: glue
        Glue helper function.
        
	Parameters:
		$helper - Helper name

    Returns:
        Object
*/
function glue($helper = null) {
    return $helper ? Glue::instance()->helper($helper) : Glue::instance();
}

function gurl($path) {
    return Glue::instance()->helper('route')->url($path);
}

/* SHORTCUTS */

if (!function_exists('R')) {
	function R($type, $path, $callback, $condition = true) {
		
		$type = strtolower($type);
		
		if(!in_array($type, array('get', 'post', 'delete', 'put'))) {
			$type = 'bind';
		}
		
		return Glue::instance()->helper('route')->{$type}($path, $callback, $condition);
	}
}

if (!function_exists('D')) {
	function D($source = 'default') {
		return Glue::instance()->helper('pdo')->src($source);
	}
}

if (!function_exists('req')) {
	function req($var, $type, $default = null) {
		return Glue::instance()->helper('request')->get($var, $type, $default);
	}
}

if (!function_exists('p')) {
	function p($path) {
		return Glue::instance()->helper('path')->path($path);
	}
}

if (!function_exists('tpl')) {
	function tpl($template, $slots = array()) {
		echo Glue::instance()->helper('template')->render($template, $slots);
	}
}

if (!function_exists('auth')) {
	function auth($userData = false) {
		
        if ($userData) {
            return Glue::instance()->helper('auth')->login($userData);
        }
        
        return Glue::instance()->helper('auth')->isVerified();
	}
}