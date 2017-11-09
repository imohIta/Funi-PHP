<?php


defined('ACCESS') || AppError::exitApp();



class Cropper extends \FuniObject{


    protected $_img;


    public function __construct($file)
    {
        # code...
        try {

    		$this->_img = new Imagick($file);

    	} catch (Exception $e) {

    		AppError::throwException('Error Occured ' . $e->getMessage(), '500');

    	}

    }

    public function crop(Array $dimensions, $savePath = '')
    {
        $dimensions = ( object ) $dimensions;
        # code...
        $this->_img->crop($dimension->width, $dimension->height, $dimension->x, $dimension->y);

        if($savePath != ''){
            $this->save($savePath);
        }
    }

    public function save($savePath)
    {
        # code...
        $this->_img->writeImage($savePath);
    }

}
