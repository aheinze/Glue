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
	public function invoke($path, $params=array()) {
		
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
            if(glue('path')->path('controller:'.$parts[0])){
              
              $parsedUri['module'] = $parts[0];
              $parts               = array_slice($parts,1);
              
              switch(count($parts)){
                case 0:
                  $parts[0] = $parsedUri['module'];
                  break;
                case 1:
                case 2:
                
                  $controllerFile = glue('path')->path('controller:'.$parsedUri['module'].'/'.$parts[0].'.php');

                  if(!$controllerFile){
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
        
            if( $controllerFile = glue('path')->path('controller:'.$parsedUri['module'].'/'.$parsedUri['controller'].'.php') ){
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
        
        $return = null;

        switch(count($parsedUri['params'])){
          case 0:
            $return = $controller->{$parsedUri['action']}();
            break;
          case 1:
            $return = $controller->{$parsedUri['action']}($parsedUri['params'][0]);
            break;
          case 2:
            $return = $controller->{$parsedUri['action']}($parsedUri['params'][0],$parsedUri['params'][1]);
            break;
          case 3:
            $return = $controller->{$parsedUri['action']}($parsedUri['params'][0],$parsedUri['params'][1],$parsedUri['params'][2]);
            break;
          default:
            $return = call_user_func_array(array(&$controller, $parsedUri['action']), $parsedUri['params']);
        }
        
        return $return;
        
	}
	
}