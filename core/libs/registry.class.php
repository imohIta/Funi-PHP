<?php
/*
* Register Classes that require a single instance
*
*/

defined('ACCESS') or AppError::exitApp();

class Registry extends FuniObject{

private $registry = array();
private static $instance;

	private function __construct(){

	}

	public function set($key, $value, $overwrite = false){
		if(is_object($value)){
			 if(!isset($this->registry[$key]) || $overwrite){
				 $this->registry[$key] = $value;
			 }else{
				 AppError::throwException('Class  with key ' . $key . '  Already Registered', '500');
				 return false;
			 }

		}

		# return object to allow method chaining
		return $this;

	}

	public function get($key, $prefix = false){
		 if(!isset($this->registry[$key])){
			 AppError::throwException('Class ( ' . $key . ' ) not Registered', '500');
		 }
		 return $this->registry[$key];
	}



	/*public static function getInstance(){
		if(self::$instance === NULL){
		   self::$instance = new Registry();
		}
		return self::$instance;
	}*/

	public static function getInstance(){
		 if(!(self::$instance instanceof self)){
			self::$instance = new self;
		 }
		 return self::$instance;
	}
}
