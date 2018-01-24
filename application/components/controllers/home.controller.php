<?php

defined('ACCESS') or Error::exitApp();

class HomeController extends BaseController{

	protected $_urlAllowedMthds = array('render');

	public function render(){
	   $this->_model->attach(new GeneralView());
	   $this->_model->execute();
	}


}
