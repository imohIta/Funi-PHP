<?php
/**
*
* Contain Core app Utility function
*
*/

//namespace \Libs\Core;

defined('ACCESS') || AppError::exitApp();

class Minifier{

	public final function minify($page){
			$page = preg_replace('/^\s+|\n|\r|\s+$/m', '', $page);
			return $page;
	}
	public final function minifyJs($js_path){
		$fp = fopen($js_path, 'r');
		while(($line = fgets($fp)) != false){
			$result .= preg_replace('/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\)\/\/.*))/', '', $line);
		}
		fclose($fp);
		return $this->minify($result);
	}
	
	public final function spitOutput($path){
		global $registry;
	    ob_start();
		include $path; 
		$response = ob_get_clean();
		return $this->minify($response);	
	}
	
	
}