<?php

/*
	Class: AuthHelper
		Helper for managing auth
*/
class AuthHelper {

    /* config */
    protected $config = array(
        'key'     => 'Auth',
        'enabled' => true
    );

	/*
		Function: login
			Set user data.

		Parameters:
			$userData

		Returns:
			Void
	*/
    public function login($userData){
        glue('session')->write($this->config['key'],$userData);
    }

	/*
		Function: logout
			Logout user.

		Returns:
			Void
	*/
    public function logout(){
        glue('session')->delete($this->config['key']);
    }

	/*
		Function: isVerified
			Check auth.

		Returns:
			Boolean
	*/
    public function isVerified(){
        return (glue('session')->read($this->config['key'])) ? true: false;
    }

	/*
		Function: get
			Get user data.

		Parameters:
			$path

		Returns:
			Misc
	*/
    public function get($path=''){
        return (empty($path)) ? glue('session')->read($this->config['key']) : glue('session')->read($this->config['key'].'.'.$path);
    }

	/*
		Function: set
			Set user data.

		Parameters:
			$config

		Returns:
			Void
	*/
    public function set($config){
        $this->config = glue('utils')->am($this->config, $config);
    }


	/*
		Function: enable
			Enable auth.

		Returns:
			Void
	*/ 
    public function enable(){
        $this->config['enabled'] = true;
    }

	/*
		Function: disable
			Disable auth.

		Returns:
			Void
	*/ 
    public function disable(){
        $this->config['enabled'] = false;
    }

	/*
		Function: check
			Check auth.

		Parameters:
			$callback

		Returns:
			Void
	*/
    public function check($callback=false){
        if(!$callback){
          if(!$this->isVerified() && $this->config['enabled']){
            call_user_func_array($this->config['login_action'], array());
          }
        }else{
          if(glue('utils')->is_callback($callback)){
              call_user_func_array($callback, array());
          }
        }
    }
  
}