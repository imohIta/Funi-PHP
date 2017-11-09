<?php

defined('ACCESS') || AppError::exitApp();

/**
*
*/
class Uri extends FuniObject
{
	private $_config;

	 public function __construct($config = null)
	{
		# code...
		$this->_config = $config;

	}

	public function redirect($dest = '')
	{
		# code...
		$dest = ($dest == '') ? $this->_config->get('baseUri') : $dest;
		header('Location: ' . $dest);
		exit;
	}

}
