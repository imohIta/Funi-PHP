<?php
//block direct access
defined('ACCESS') or die('Access Denied!');

/**
* Create Custom Session for the Application that extends Core\libs\Session
* and pass as the argument and test if it works
*/
use core\libs\Session as Session;

class Application extends FuniObject{

	protected $_autoloader, $_session;
	public static $_instance = NULL;

	private function __construct(AutoLoader $autoloader, Session $session, Router $router){

		//assign autoloader Object
		$this->_autoloader = $autoloader;

		//assign session object
		$this->_session = $session;

		//initialize App -- Enable Class Autoloading
		$this->_initialize();

		//assign router object
		$this->_router = $router;

		//set App Env
		$this->_setAppEnv();

		# return object to allow method chaining
		return $this;
	}

	public static function getInstance(AutoLoader $autoloader, Session $session, Router $router){
		if(!(self::$_instance instanceof self)){
			self::$_instance = new self($autoloader, $session, $router);
	    }
	    return self::$_instance;
	}

	private function _initialize(){
		//Invoke Autoloading
		if(function_exists('spl_autoload_register')){ //check if version of php supports spl_autoload_register
			spl_autoload_register(array( $this->_autoloader, 'autoload'));
		}else{
			function __autoload( $classname ) {
				$this->_autoloader->autoload($classname);
			}
		}

		# return object to allow method chaining
		return $this;
	}

	private function _setAppEnv(){
		//set Application Environment
		//if ($_SERVER['APPLICATION_ENV'] == 'development') {
			 error_reporting(E_ALL);
			 ini_set("display_errors", 1);
		 //}

		 # return object to allow method chaining
 		return $this;
	}

	public function registerClass($key, $class){
		if(!is_object($class)){
			Error::throwException('Only Object Can be Registered');
		}
		$this->_registry->set($key, $class);

		# return object to allow method chaining
		return $this;
	}


	public function boot(){
		global $registry;

		# check if they is an installer file
		if(file_exists(PATH . '/install/index.php')){



			# redirect to installer
			$registry->get('uri')->redirect('http://' . $_SERVER['HTTP_HOST'] . '/' . $registry->get('config')->get('appTitle') . '/install/index');


		}else{ # if application has been installed

			# initialize router
			$this->_router->initialize();

			# route url
			$this->_router->route();
		}

		# return object to allow method chaining
		return $this;

	}

}
