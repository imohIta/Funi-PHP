<?php

defined('ACCESS') or Error::exitApp();

class BaseView extends FuniObject implements SplObserver{

	//protected $_model, $_controller;

	//public function __construct(BaseController $controller){
	public function __construct(){
		//$this->_controller = $controller;
		//$this->_model = $this->_controller->get('model');
	}

	/**
	* Used to render full tmpl pages
	*/
	public final function render($tmpl = "dashboard"){
		global $registry;
		if($tmpl == ""){
		   $tmpl = "dashboard";
		}
		$path = $registry->get('config')->get('basePath') .'/application/components/views/tmpls/' . $tmpl . ".tmpl.php";
		if(!file_exists($path)){
			AppError::throwException($tmpl . ' Template Not Found');
		}
		echo $registry->get('minifier')->spitOutput($path);

	}

	/**
	* Used to display Widgets Like Error & Success Msg
	*/
	public final function display($widget, $msg, $tmpl){
		global $registry;
		$path = $registry->get('config')->get('basePath') .'/application/components/views/widgets/' . $widget . ".widget.php";
		if(!file_exists($path)){
			AppError::throwException($widget .' Widget Not Found');
		}
		ob_start();
		include $path;
		$r = ob_get_clean();

		#if tmpl has no value...that mean the call to the component is probably an ajax call
		#echo the minified templete content if so
		if(!$tmpl){
			echo $r;
		}else{
			#set the minifier templete content to a session and echo it in the calling page
			$registry->get('session')->write('formMsg', $r);
			$this->render($tmpl);
		}
	}


	public function update(SplSubject $subject){
	    $mthd = $subject->get('viewParams')['action'];
	    if($mthd == 'display'){
	    	$this->$mthd($subject->get('viewParams')['widget'],$subject->get('viewParams')['msg'], $subject->get('viewParams')['tmpl']);
	    }else{
	    	$this->$mthd($subject->get('viewParams')['tmpl']);
	    }
	}

}
