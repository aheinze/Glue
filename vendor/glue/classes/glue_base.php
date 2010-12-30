<?php

/*
	Class: GlueBase
		GlueBase class.
*/    
class GlueBase {
    
    /* instance */
	protected static $_instance;
    
    /*
		Function: __construct
			Constructor.

		Returns:
			Void
	*/
    public function __construct() {

        $reflection = new ReflectionClass(get_class($this)); 
                
        foreach($reflection->getMethods() as $method) {
            
            if(!in_array($method->name, array("__construct", "run", "bind")) && $doccomment = $method->getDocComment()) {
                
                preg_match('/@(get|post|put|delete|bind) (.*)/mi', $doccomment, $route);
                
                if(!empty($route)) {
                    $m = strtolower($route[1]);
                    glue("route")->{$m}(trim($route[2]), array($this, $method->name));                    
                }
            }
        }
    }
    
    
    /*
		Function: bind
			Bind class routes.

		Returns:
			Void
	*/
    public static function bind() {

		// add instance, if not exists
		if (!isset(self::$_instance)) {
            
            $classname = get_called_class();
        
			self::$_instance = new $classname;
		}
	}
    
	/*
		Function: run
			Run instance.

		Returns:
			Void
	*/
    public static function run() {

		// add instance, if not exists
		if (!isset(self::$_instance)) {
            
            $classname = get_called_class();
        
			$classname::bind();
		}
        
        glue("route")->dispatch();
	}
    
}