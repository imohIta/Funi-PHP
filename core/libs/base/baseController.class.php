<?php

defined('ACCESS') or AppError::exitApp();

class BaseController extends FuniObject{

	//protected $_params = array();
	protected $_model;

	public function __construct(BaseModel $model){
		$this->setModel($model);
	}

	public function execute($params){

		$mthd = $params['mthd'];
		$a = array_splice($params, 1);
		//$c = $a['params'];
		foreach($a as $key => $value){
			$this->_params[$key] = $value;
		}
		//check if mthd called from url is one of the url-allowed-mthds
		//var_dump($this->_urlAllowedMthds); die;
		foreach ($this->_urlAllowedMthds as $key => $value) {
			 if($mthd == strtolower($value) || $mthd == $value){
				  $this->$mthd();
				  return;
			 }
	    }
		AppError::throwException('( Invalid URL ) Access Denied to Method ' . $mthd, '404');

		//not used becos php in_array performs a case sensitive search
		/*if(!in_array($mthd, $this->_urlAllowedMthds)){
			Error::throwException('Access Denied to Method'); //Replace msg with invalid url upon deployment
		}
		$this->$mthd();
		*/

	}

	protected function setModel(BaseModel $model)
	{
		# set Object Model to Model
		$this->_model = $model;
		return $this;
	}

}
