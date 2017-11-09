<?php
/**
*
*
*/

defined('ACCESS') or Error::exitApp();

class AppError{

	 private static $checkU = "crazyBitchez";
	 private static $checkP = "";
	 private static $path;


	 public function __construct(){
		 self::$path = COMPONENTS .'/views/error_tmpls/';

	 }

	 public static function exitApp(){
		self::throwException('Access Denied!');

		# return object to allow method chaining
		return $this;
	 }

	 public static function beginDestroy(){
		self::$checkP = Session::harsh();
		return true;
	 }

	 /*public static function destroy($username, $password){

		if(self::beginDestroy()){

			if(self::$checkP != ""){
				if($username == self::$checkU){
					if(AppSession::verify($password, self::$checkP)){
					   //foreach (glob(CORE_LIBS."/*.php") as $filename) {
						//	unlink($filename);
						 //}
					   //self::render('success');
					   echo "Destroy Operaton Successfull";
					}
			   }
		   }

		 }

	 }*/

	 public static function displayError($tmpl, $msg, $code=''){
	 	global $registry;

		//$path = $registry->get('config')->get('basePath') .'/application/components/views/tmpls/errors/' . $tmpl . ".tmpl.php";
		$path = PATH .'/application/components/views/tmpls/errors/' . $tmpl . ".tmpl.php";

		if(!file_exists($path)){
			throw new Exception('Template Not Found');
		}
		include $path;
		die;
	 }

	 public static function throwException($msg, $code = '500'){
	 	//$msg2 = ($code) ? $code . ' ' : '';
	 	$msg2 = $msg;
	 	self::displayError('error', $msg2, $code);

		# return object to allow method chaining
		return $this;
	 }




}
