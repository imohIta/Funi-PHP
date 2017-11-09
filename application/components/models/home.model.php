<?php

/**
*
*
*/
defined('ACCESS') || AppError::exitApp();

class HomeModel extends BaseModel{

	protected $_param;
	protected $_viewParams;

	public function execute(Array $options = array('action'=>'render', 'tmpl' => 'home', 'widget' => '', 'msg' => '')){
		$this->_viewParams = $options;
		$this->notify();
	}



	#end of class
}
