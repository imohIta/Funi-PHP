<?php
/*
* Interface that all Uploader Classes must Implement
*
*/

defined('ACCESS') || AppError::exitApp();

interface UploadHandler{

	public function UploadFile();	
	
}