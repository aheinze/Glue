<?php

/*
	Class: ConfigHelper
		Helper for managing config data
*/
class ConfigHelper{
    
    /* collection */
    protected static $_collection;
  
    /*
        Function: read
            Read config value.

        Parameters:
            $key
            $default

        Returns:
            Misc
    */
    public static function read($key, $default=null){

        $keys = explode('.',$key);

        switch(count($keys)){
          
          case 1:
            if(isset(self::$_collection[$keys[0]])){
              return self::$_collection[$keys[0]];
            }
            break;
          
          case 2:
            if(isset(self::$_collection[$keys[0]][$keys[1]])){  
              return self::$_collection[$keys[0]][$keys[1]];
            }
            break;
          
          case 3:
            if(isset(self::$_collection[$keys[0]][$keys[1]][$keys[2]])){
              return self::$_collection[$keys[0]][$keys[1]][$keys[2]];
            }
            break;
            
          case 4:
            if(isset(self::$_collection[$keys[0]][$keys[1]][$keys[2]][$keys[3]])){
              return self::$_collection[$keys[0]][$keys[1]][$keys[2]][$keys[3]];
            }
            break;
            
          case 5:
            if(isset(self::$_collection[$keys[0]][$keys[1]][$keys[2]][$keys[3]][$keys[4]])){
              return self::$_collection[$keys[0]][$keys[1]][$keys[2]][$keys[3]][$keys[4]];
            }
            break;
        }

        return $default;
    }
  
    /*
        Function: write
            Write config value.

        Parameters:
            $key
            $value

        Returns:
            Void
    */
    public static function write($key,$value){

        $keys = explode('.',$key);

        switch(count($keys)){
          
          case 1:
            self::$_collection[$keys[0]] = $value;
            break;
          
          case 2:
            self::$_collection[$keys[0]][$keys[1]] = $value;
            break;
          
          case 3:
            self::$_collection[$keys[0]][$keys[1]][$keys[2]] = $value;
            break;
            
          case 4:
            self::$_collection[$keys[0]][$keys[1]][$keys[2]][$keys[3]] = $value;
            break;
            
          case 5:
            self::$_collection[$keys[0]][$keys[1]][$keys[2]][$keys[3]][$keys[4]] = $value;
            break;
            
          default:
            return false;
        }

        return true;

    }
  
    /*
        Function: delete
            Delete config value.

        Parameters:
            $key

        Returns:
            Void
    */
    public static function delete($key){
        
        $keys = explode('.',$key);
           
        switch(count($keys)){
          
          case 1:
            if(isset(self::$_collection[$keys[0]])){
              unset(self::$_collection[$keys[0]]);
            }
            break;
          
          case 2:
            if(isset(self::$_collection[$keys[0]][$keys[1]])){  
              unset(self::$_collection[$keys[0]][$keys[1]]);
            }
            break;
          
          case 3:
            if(isset(self::$_collection[$keys[0]][$keys[1]][$keys[2]])){
              unset(self::$_collection[$keys[0]][$keys[1]][$keys[2]]);
            }
            break;
            
          case 4:
            if(isset(self::$_collection[$keys[0]][$keys[1]][$keys[2]][$keys[3]])){
              unset(self::$_collection[$keys[0]][$keys[1]][$keys[2]][$keys[3]]);
            }
            break;
            
          case 5:
            if(isset(self::$_collection[$keys[0]][$keys[1]][$keys[2]][$keys[3]][$keys[4]])){
               unset(self::$_collection[$keys[0]][$keys[1]][$keys[2]][$keys[3]][$keys[4]]);
            }
            break;
            
          default:
            return false;
        }

        return true;
    }
}