<?php 
	
	/**
	* 
	*/
	class ImagickUploader implements UploadHandler
	{
		private $_editor;
		
		function __construct($file)
		{
			# code...
			$this->_editor = new Imagick($file);
		}
	}

 ?>