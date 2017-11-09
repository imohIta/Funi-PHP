<?php
//namespace follows directory pattern
namespace core\libs;

defined('ACCESS') || AppError::exitApp();



class Session extends \FuniObject{
	// Id generated for current session
	protected $_id;

	// Session name
	protected $_name;

	// Cookie lifetime
	protected $_lifetime;

	// Cookie path
	protected $_path;

	// Cookie domain
	protected $_domain;

	// Cookie is sent over SSL
	protected $_secure;

	// Cookie is httponly
	protected $_httponly;

	// Singleton Session instance
	protected static $objectCount = 0;

	//
	public function __construct(array $options)
	{
		if(self::$objectCount == 0){
			self::$objectCount++;
			// Set session name or use Framework Default
			$this->_name = isset($options['name']) ? $options['name'] : 'Funi1.0';

			// Set session cookie params
			$this->_lifetime = isset($options['lifetime']) ? $options['lifetime'] : ini_get('session.cookie_lifetime');
			$this->_path = isset($options['path']) ? $options['lifetime'] : ini_get('session.cookie_path');
			$this->_domain = isset($options['domain']) ? $options['domain'] : ini_get('session.cookie_domain');
			$this->_secure = (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off')) ? true : false;
			$this->_httponly = isset($options['httponly']) ? $options['httponly'] : ini_get('session.cookie_httponly');
		}else{
			AppError::throwException("Use instance of class from Factory");
		}
	}


	// Starts session.
	//
	// Explicitly destroys old session and
	// creates a new one
	public function start()
	{
		// Ensure that session_start() is called once
		if(session_id())
		{
			$this->destroy();
			//session_regenerate_id(true);
		}

		// Register cookie params
		session_set_cookie_params($this->_lifetime, $this->_path, $this->_domain, $this->_secure, $this->_httponly);

		// Set session name
		session_name(md5($this->_name));

		// Starts session and stores session id
		// in session_id() function by either getting one from
		// previous request or creating a new one.
		session_start();

		// Store the session id
		$this->_id = session_id();

		# return object to allow method chaining
		return $this;

	}


	//
	public final function write($key, $value){
		$new_key = sha1($key);

		if($value === NULL && isset($_SESSION[$new_key])){
			unset($_SESSION[$new_key]);
		}else{
        	$_SESSION[$new_key] = $value;
		}

		# return object to allow method chaining
		return $this;
    }

	public function writeAssoc($sessName, $key, $value){
		$new_key = sha1($sessName);
		if(isset($_SESSION[$new_key])){
			if(!$key || $key == ' '){
				$_SESSION[$new_key][] = $value;
			}else{
				$_SESSION[$new_key][$key] = $value;
			}
		}

		# return object to allow method chaining
		return $this;
	}


	public function deleteAssoc($sessName, $key, $return = false){
		$new_key = sha1($sessName);
		if(isset($_SESSION[$new_key])){
			unset($_SESSION[$new_key][$key]);
		}
		if($return){
			return $_SESSION[$new_key];
		}

		# return object to allow method chaining
		return $this;
	}

	public final function read($key){
		 $new_key = sha1($key);
		 if(!isset($_SESSION[$new_key])){
			return NULL;
		 }
		 return $_SESSION[$new_key];
	}

	//
	public function getName(){
		return $this->_name;
	}

	//
	public function destroy(){
		// Kill the session completely
		if(isset($_COOKIE[session_name()]))
		{
			$cookie = session_get_cookie_params();
			$name = $this->_name ? $this->_name : session_name();
			setcookie($name, '', time() - 42000, $cookie['path'], $cookie['domain']);
		}

		session_destroy();
		session_unset();
	}
}
