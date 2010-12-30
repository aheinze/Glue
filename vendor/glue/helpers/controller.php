<?php

/*
	Class: ControllerHelper
		Controller helper class. Create and manage Controllers.
*/
class ControllerHelper {


	/*
		Function: invoke
			Invoke controller class.

		Parameters:
			$path 
			$params 

		Returns:
			Void
	*/
	public function invoke($path, $params=array(), $controller_path = null) {
		
        $controller_path = $controller_path ? $controller_path : glue("path")->base_path;
        
        $parsedUri = array(
            'module'     => 'app',
            'controller' => 'app',
            'action'     => 'index',
            'params'     => $params
        );
        
        if(is_array($path)){
            
            $parsedUri = array_merge($parsedUri, $path);
        
        } else {
            $parts = explode('/', ltrim($path, '/'));
            
            //check for module
            //-----------------------------------------------------
            if(file_exists($controller_path.'/'.$parts[0])){
              
              $parsedUri['module'] = $parts[0];
              $parts               = array_slice($parts,1);
              
              switch(count($parts)){
                case 0:
                  $parts[0] = $parsedUri['module'];
                  break;
                case 1:
                case 2:
                
                  $controllerFile = $controller_path.'/'.$parsedUri['module'].'/'.$parts[0].'.php';

                  if(!file_exists($controllerFile)){
                    array_unshift($parts, $parsedUri['module']);
                  }
                  
                  break;
              }
            }
            //-----------------------------------------------------
            
            switch(count($parts)) {
                case 1:
                    if($parts[0]!=='') $parsedUri['controller'] = $parts[0];
                    break;
                case 2:
                    $parsedUri['controller'] = $parts[0];
                    $parsedUri['action']     = $parts[1];
                break;
                default:
                    $parsedUri['controller'] = $parts[0];
                    $parsedUri['action']     = $parts[1];
                    $parsedUri['params']     = array_slice($parts,2);
            }
            
        }
        
        if(!$parsedUri['controller']){
          return false;
        }

        $controllerName = $parsedUri['controller'].'Controller'; 
        
        if(!class_exists($controllerName)){
            
            $controllerFile = $controller_path.'/'.$parsedUri['module'].'/'.$parsedUri['controller'].'.php';
            
            if(file_exists($controllerFile)){
                require_once($controllerFile);   
            }
        }
       
        if(!class_exists($controllerName)){
            return false;
        }
        
        $controller = new $controllerName();
        
        if(!method_exists($controller,$parsedUri['action'])){
          return false;
        }
        
        $return = call_user_func_array(array(&$controller, $parsedUri['action']), $parsedUri['params']);
        
        return $return;
	}
	
}