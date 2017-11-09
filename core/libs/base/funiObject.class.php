<?php
/*
* Framework Base Class
* All Classes Extend this Class
*/

defined('ACCESS') or Error::exitApp();

class FuniObject{
	
	public function set($key, $value, $prefix = true){
	   $key = ($prefix) ? '_'.trim($key,'_') : trim($key,'_');
	   if(property_exists(__CLASS__, $key)){
			$this->{$key} = $value;   	
		}
	}
	
	public function get($key, $prefix = true){
		 $key = ($prefix) ? '_'.trim($key,'_') : trim($key,'_');
		 if(isset($this->{$key})){
			 return $this->{$key};
		 }	
		 return NULL;
	}
	
}