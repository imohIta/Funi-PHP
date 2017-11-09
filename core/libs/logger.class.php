<?php

namespace core\libs;
//use core\libs\Database;


defined('ACCESS') || AppError::exitApp();

/*
* Handle all Logging
* Will receive the option of either a file or Database as Store
*
*/

class Logger extends \FuniObject{

	protected $_db;

	public function __construct(Database $db = null)
	{
		# code...
		global $registry;

		if(!is_null($db)){
			$this->set('db', $db);
		}

		
	}



}
