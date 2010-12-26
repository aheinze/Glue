<?php

/*
	Class: UtilsHelper
		Utils helper class. Misc helper functions.
*/
class UtilsHelper {
  
	/*
		Function: isFile
			Is string a file path?

		Parameters:
			$fileName 
			$forceCheck 

		Returns:
			Boolean
	*/
    public static function isFile($fileName,$forceCheck=false){
        
        if ($fileName{0} === DS || $fileName{1} === ':'){
          return (!$forceCheck) ? true:is_file($fileName);
        }

        return false;
    }
    
	/*
		Function: am
            Merge arrays recursive
			(c) CakePhp function am()

		Parameters:
			Misc

		Returns:
			Array
	*/
    public static function am() {
        $r = array();
        $args = func_get_args();
        foreach ($args as $a) {
            if (!is_array($a)) {
                $a = array($a);
            }
            $r = array_merge($r, $a);
        }
        return $r;
    }
    
	/*
		Function: is_callback
			Is var a callback?

		Parameters:
			$var

		Returns:
			Boolean
	*/
    public static function is_callback($var){
      
        if(is_null($var)) return false;

        if(is_array($var) && count($var) == 2) {
          $var = array_values($var);
          if ((!is_string($var[0]) && !is_object($var[0])) || (is_string($var[0]) && !class_exists($var[0]))) {
              return false;
          }
          $isObj = is_object($var[0]);
          $class = new ReflectionClass($isObj ? get_class($var[0]) : $var[0]);
          if ($class->isAbstract()) {
              return false;
          }
          try {
              $method = $class->getMethod($var[1]);
              if (!$method->isPublic() || $method->isAbstract()) {
                  return false;
              }
              if (!$isObj && !$method->isStatic()) {
                  return false;
              }
          } catch (ReflectionException $e) {
              return false;
          }
          return true;
        } elseif (is_string($var) && function_exists($var)) {
          return true;
        } elseif (is_callable($var)) {
          return true;
        }
        return false;
    }

	/*
		Function: array2Obj
			Converts array to object.

		Parameters:
			$array
			$class

		Returns:
			Object
	*/
    public static function array2Obj($array, $class='stdClass'){

        $object = new $class;

        foreach ($array as $key => $value){
            if (is_array($value)){
                $value = arr::to_object($value, $class);
            }
            $object->{$key} = $value;
        }

        return $object;
    }

}