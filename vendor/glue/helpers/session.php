<?php

/*
	Class: SessionHelper
		Session helper class. Manage sessions.
*/
class SessionHelper {

    /*
        Function: init
            Init session.

        Parameters:
            $name

        Returns:
            Void
    */
    public function init($name = 'gluesession'){
        session_name($name);
        session_start();
    }

    /*
        Function: write
            Write session value.

        Parameters:
            $key
            $default

        Returns:
            Misc
    */
    public function write($key, &$value){

        $keys = explode('.',$key);

        switch(count($keys)){
          
          case 1:
            $_SESSION[$keys[0]] = $value;
            break;
          
          case 2:
            $_SESSION[$keys[0]][$keys[1]] = $value;
            break;
          
          case 3:
            $_SESSION[$keys[0]][$keys[1]][$keys[2]] = $value;
            break;
            
          case 4:
            $_SESSION[$keys[0]][$keys[1]][$keys[2]][$keys[3]] = $value;
            break;
        }

    }
    
    /*
        Function: read
            Read session value.

        Parameters:
            $key
            $default

        Returns:
            Misc
    */
    public function read($key, $default=null){

        $keys = explode('.',$key);

        switch(count($keys)){
          
          case 1:
            if(isset($_SESSION[$keys[0]])){
              return $_SESSION[$keys[0]];
            }
            break;
          
          case 2:
            if(isset($_SESSION[$keys[0]][$keys[1]])){  
              return $_SESSION[$keys[0]][$keys[1]];
            }
            break;
          
          case 3:
            if(isset($_SESSION[$keys[0]][$keys[1]][$keys[2]])){
              return $_SESSION[$keys[0]][$keys[1]][$keys[2]];
            }
            break;
            
          case 4:
            if(isset($_SESSION[$keys[0]][$keys[1]][$keys[2]][$keys[3]])){
              return $_SESSION[$keys[0]][$keys[1]][$keys[2]][$keys[3]];
            }
            break;
        }

        return $default;
    }
    
    /*
        Function: delete
            Delete config value.

        Parameters:
            $key

        Returns:
            Void
    */
    public function delete($key){
        $keys = explode('.',$key);
           
        switch(count($keys)){
          
          case 1:
            if(isset($_SESSION[$keys[0]])){
              unset($_SESSION[$keys[0]]);
            }
            break;
          
          case 2:
            if(isset($_SESSION[$keys[0]][$keys[1]])){  
              unset($_SESSION[$keys[0]][$keys[1]]);
            }
            break;
          
          case 3:
            if(isset($_SESSION[$keys[0]][$keys[1]][$keys[2]])){
              unset($_SESSION[$keys[0]][$keys[1]][$keys[2]]);
            }
            break;
            
          case 4:
            if(isset($_SESSION[$keys[0]][$keys[1]][$keys[2]][$keys[3]])){
              unset($_SESSION[$keys[0]][$keys[1]][$keys[2]][$keys[3]]);
            }
            break;
        }
    }

    /*
        Function: destroy
            Destroy session.

        Returns:
            Void
    */
    public function destroy(){
        session_destroy();
    }
	
}