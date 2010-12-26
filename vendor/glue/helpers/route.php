<?php

/*
	Class: RouteHelper
		Route helper class. Create and manage Routes.
*/
class RouteHelper {

	/* path */
	public $path;
    
	/* routes */
	protected static $_routes = array();

	/*
		Function: __construct
			Constructor.

		Returns:
			Void
	*/
    public function __construct() {
        
        $url_path = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        
        if(!strlen($url_path) && glue('path')->base_url != rtrim($_SERVER['REQUEST_URI'], '/')) {
            
            $url_path = str_replace(glue('path')->base_url, '', $_SERVER['REQUEST_URI']);
        }
        
        // ensure that the URL starts with a /
        if ('/' !== substr($url_path, 0, 1)) {
            $url_path = '/'.$url_path;
        }

        // remove the query string
        if (false !== $pos = strpos($url_path, '?')) {
            $url_path = substr($url_path, 0, $pos);
        }
        
        $this->path = $url_path;
        
        self::$_routes['404'] = function() {
        
            echo 'Path not found.';
        };   
        
        self::$_routes['/'] = function() {
        
            echo 'Route for <strong>/</strong> not found.';
        };
        
    }
    
	/*
		Function: bind
			Bind a function to a route.

		Parameters:
			$path - Route path
			$callback - Function callback
			$condition - Boolean condition

		Returns:
			Void
	*/
	public function bind($path, $callback, $condition = true) {
		
        if (!$condition) return;
        
		if (!isset(self::$_routes[$path])) {
			self::$_routes[$path] = array();
		}
		
		self::$_routes[$path] = $callback;
	}
    
	/*
		Function: get
			Bind a GET function to a route.

		Parameters:
			$path - Route path
			$callback - Function callback
			$condition - Boolean condition

		Returns:
			Void
	*/
	public function get($path, $callback, $condition = true) {
		
        if (!(glue('request')->is('get') && $condition)) return;
        
		$this->bind($path, $callback, $condition);
	}
    
	/*
		Function: post
			Bind a POST function to a route.

		Parameters:
			$path - Route path
			$callback - Function callback
			$condition - Boolean condition

		Returns:
			Void
	*/
	public function post($path, $callback, $condition = true) {
		
        if (!(glue('request')->is('post') && $condition)) return;
        
		$this->bind($path, $callback, $condition);
	}
    
	/*
		Function: put
			Bind a PUT function to a route.

		Parameters:
			$path - Route path
			$callback - Function callback
			$condition - Boolean condition

		Returns:
			Void
	*/
	public function put($path, $callback, $condition = true) {
		
        if (!(glue('request')->is('put') && $condition)) return;
        
		$this->bind($path, $callback, $condition);
	}
    
	/*
		Function: deletedelete
			Bind a DELETE function to a route.

		Parameters:
			$path - Route path
			$callback - Function callback
			$condition - Boolean condition

		Returns:
			Void
	*/
	public function delete($path, $callback, $condition = true) {
		
        if (!(glue('request')->is('delete') && $condition)) return;
        
		$this->bind($path, $callback, $condition);
	}

	/*
		Function: dispatch
			Dispatch Route

		Parameters:
			$route - Route name
			$parameters - Function arguments

		Returns:
			Misc
 	*/
	public function dispatch($echo = true) {
        
        $path   = $this->path;        
        $found  = false;
        $params = array();
        
        if (isset(self::$_routes[$path])) {
            
            $found = $this->render($path, $params);

        } else {
                
            foreach (self::$_routes as $route => $callback) {
                
                $params = array();
                
                /* e.g. #\.html$#  */
                if(substr($route,0,1)=='#' && substr($route,-1)=='#'){
                    
                    if(preg_match($route,$path, $matches)){
                        $params[':captures'] = array_slice($matches, 1);
                        $found = $this->render($route, $params);
                        break;
                    }
                }
                
                /* e.g. /admin/*  */
                if(strpos($route, '*') !== false){
                    
                    $pattern = '#'.str_replace('\*', '(.*)', preg_quote($route, '#')).'#';
                    
                    if(preg_match($pattern, $path, $matches)){
                    
                        $params[':splat'] = array_slice($matches, 1);
                        $found = $this->render($route, $params);
                        break;
                    }
                }
                
                /* e.g. /admin/:id  */
                if(strpos($route, ':') !== false){
                    
                    $parts_p = explode('/', $path);
                    $parts_r = explode('/', $route);
                    
                    if(count($parts_p) == count($parts_r)){
                        
                        $matched = true;
                        
                        foreach($parts_r as $index => $part){
                            if(substr($part,0,1)==':') {
                                $params[substr($part,1)] = $parts_p[$index];
                                continue;
                            }
                            
                            if($parts_p[$index] != $parts_r[$index]) {
                                $matched = false;
                                break;
                            }
                        }
                        
                        if($matched){
                            $found = $this->render($route, $params);;
                            break;
                        }
                    }
                }
                
            }         
        }
		
		// return content 
		
		$output = $found ?  $found : $this->render('404', $params);
		
		if($echo) {
			echo $output;
		}
		
        return $output;
	}
    
	/*
		Function: render
			Bind a function to a route.

		Parameters:
			$route - Route path
			$params - Array params

		Returns:
			String
	*/
    public function render($route, $params = array()) {
        
        $output = false;
        
        if(isset(self::$_routes[$route])) {
            
            ob_start();      
            
			$ret = call_user_func(self::$_routes[$route], $params);
            $output = ob_get_clean();

			if( !is_null($ret) ){
				return $ret;
			}
			
        }
        
        return $output;
    }
    
	/*
		Function: url
			Build valid glue url.

		Parameters:
			$path - Route path

		Returns:
			String
	*/
    public function url($path) {
    
        return Glue::instance()->helper('path')->base_url.$path;
    }
    
	/*
		Function: reroute
			Redirect to another route.

		Parameters:
			$path - Route path

		Returns:
			Void
	*/
    public function reroute($path) {
    
        if (strpos($path,'://') === false) {
          if(substr($path,0,1)!='/'){
            $path = '/'.$path;
          }
          $path = Glue::instance()->helper('path')->base_url.$path;
        }

        header('Location: '.$path);
        exit;
    }	
}