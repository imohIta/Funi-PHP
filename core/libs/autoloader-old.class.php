<?php
//block direct access
//defined('ACCESS') or die('Access Denied!');

class AutoLoader extends FuniObject{
	
	protected $_paths = array();
	protected $_config;
	
	public function __construct(Array $dirs = array(), Config $config){
		$this->_config = $config;
		foreach($dirs as $dir){
			$this->setPath($dir);	
		}
	}
	
	/**
	* Load path to search for a file during autoloading
	* @params - receive a directory and add the directory and all its sub/sub-sub... directories to the path
	*
	*/
	public function setPath($d){
		if(!in_array($this->_config->get('basePath') . $this->_config->get('ds') . $d, $this->_paths)){
			$this->_paths[] = $this->_config->get('basePath') . $this->_config->get('ds') . $d; 
		}
		//list all files & directories in the directory
		$dirs = scandir($this->_config->get('basePath') . $this->_config->get('ds') . $d);
		foreach($dirs as $dir){
			if($dir != '.' && $dir != '..'){ //the first two elements of scandir array is . and .. | do not use these two
				
				if(is_dir($this->_config->get('basePath') . $this->_config->get('ds') . $d . $this->_config->get('ds') . $dir)) { 
				    
					//append the current directory to the path array
					$this->_paths[] = $this->_config->get('basePath') . $this->_config->get('ds') . $d . $this->_config->get('ds') . $dir;
					
					//recall the function...passing the current directory as arguement  
					$this->setPath($d.$this->_config->get('ds').$dir); 
				}
			}
        }
	//return $p;
	}
	
	public function autoload($classname){
		
		//echo $classname;
		
		$this->_requireMVCFile($classname, 'Model');
		$this->_requireMVCFile($classname, 'Controller');
		$this->_requireMVCFile($classname, 'View');
		
		//Check if Class contain Namespace
		if(strpos($classname, '\\') !== false){
			$parts = explode('\\', $classname);
			$path = implode($this->_config->get('ds'), $parts);
			$this->_requireFile(strtolower($path), '', $this->_config->get('basePath') . $this->_config->get('ds'));
		}else{
			foreach($this->_paths as $path){
				$this->_requireFile($path . $this->_config->get('ds'), $classname);
			}
	   }
	}
	
	private function _requireFile($path, $classname, $basePath=''){
		if(file_exists($basePath . $path . strtolower($classname) . '.class.php')){
			 require_once $basePath . $path . strtolower($classname) . '.class.php';
			 return;
		}
		if(file_exists($basePath . $path . strtolower($classname) . '.php')){
			 require_once $basePath . $path . strtolower($classname) . '.php';
			 return;
		 }		
	}
	
	/*
	* See Conventions.txt for More info on MVC files naming conventions
	*
	* This Function Checks if the file is a model, view or controller file
	* e.g DisplayModel
	* converts the classname ( DisplayModel ) to display.model and requires the file
	*/
	private function _requireMVCFile($classname, $fileType){
		//check if class is a controller, Model or view
		if(stripos($classname, $fileType) !== false){
			//get the file name after extracting ( controller | View | Method )
			$name = stristr($classname, $fileType, true); 
			$cname = strtolower($name) . '.' . $fileType;
			foreach($this->_paths as $path){
				$this->_requireFile($path . $this->_config->get('ds'), $cname);
			}
			return;
		}	
	}

//End of Class
}






