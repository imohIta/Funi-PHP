<?php
//block direct access
defined('ACCESS') or die('Access Denied!');

class Config{

	private $appTitle;
    
	private $basePath = '';
	private $baseUri = '';
	private $ds = '/';
	
	//Db Settings
	private $dbHost = 'localhost';
	private $dbName = '';
	private $dbUser = '';
	private $dbPwd = '';
	
	public function __construct(Array $options){
		foreach($options as $key => $value){
			$this->{$key} = $value;	
		}
	}
	
	public function set($key, $value){
		 $this->{$key} = $value; 
	}
	
	public function get($key){
		 //$key = (strpos($key, '_') === false) ? '_'.$key : $key;
		 if(!property_exists(__CLASS__, $key)){
			 throw new Exception( '( ' . $key . ' ) Property Does not Exist');
		 }	
		 return $this->{$key};
	}
	
	
	
}