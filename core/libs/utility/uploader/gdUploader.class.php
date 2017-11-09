<?php 
	
	/**
	* 
	*/
	class GdUploader implements UploadHandler
	{
		private $_uploadPath;
		private $_file;
		private $_allowedTypes = array('image/jpg','image/jpeg','image/png','image/gif');
		private $_type;
		private $_mimeType;
		
		public function __construct($tmpFile){
			$this->_file = $tmpFile;
			$this->getFileType();

		}

		private function _getFileType(){
			$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
			$this->_mimeType =  finfo_file($finfo, $filename);
			finfo_close($finfo);
			list($this->_type, $others) = explode('/', $this->_mimeType);
		}

		public function setAllowedTypes(Array $types){
			unset($this->_allowedTypes);
			foreach ($types as $type) {
				$this->_allowedTypes[] = $type;
			}
		}

		public function setUploadpath($path, $appendBasePath = true){
			global $registry;
			$this->_uploadPath = ($appendBasePath) ? $registry->get('config')->get('basePath') . '/' . $path : $path;
		}

		/**
		* @param uploadOptions['quality'] : Quality of the desired output image
		* @param uploadOptions['imageFormat'] : Format of the desired output image
		*
		*/
		public function uploadFile(Array $uploadOptions = array()){
			$quality = (isset($uploadOptions['quality'])) ? $uploadOptions['quality'] : 40;
			$imageFormat = (isset($uploadOptions['imageFormat'])) ? $uploadOptions['imageFormat'] ? 'jpeg';
			
			//Make Sure
			if (is_empty($this->_allowedTypes)) {
				Error::throwException('Allowed Upload types not Set');
			}
			
			//If Upload file type is not supported
			if (!in_array($this->_file, $this->_allowedTypes)) {
				Error::throwException('Unsupported File type')
			}

			//check if upload path isset
			if (!isset($this->_uploadPath)) {
				Error::throwException('Upload Path noe set');
			}
            
            //upload File
            $simg = imagecreatefromstring(file_get_contents($this->_file));
            list($w, $h) = getimagesize($this->_file); 

            //make sure the file height is not bigger than 1024
            $nh = ($h > 1024) ? 1024 : $h;
            $nw=( $w / $h ) * $nh;

			if($nw > 1024){
				$nw = 1024;
				$nh =( $h / $w ) * $nw;
			}


			//crop image to desired size
			$dimg = imagecreatetruecolor($nw, $nh); 
			$wm = $w/$nw; 
			$hm = $h/$nh; 
			$h_height = $nh/2; 
			$w_height = $nw/2;     
			if($w > $h) {
				$adjusted_width = $w / $hm;
				$half_width = $adjusted_width / 2;
				$int_width = $half_width - $w_height;
				imagecopyresampled($dimg,$simg,-$int_width,0,0,0,$adjusted_width,$nh,$w,$h);
			}elseif(($w <$h) || ($w == $h)) {
				$adjusted_height = $h / $wm;
				$half_height = $adjusted_height / 2;
				$int_height = $half_height - $h_height;
				imagecopyresampled($dimg,$simg,0,-$int_height,0,0,$nw,$adjusted_height,$w,$h);
			}else{
				imagecopyresampled($dimg,$simg,0,0,0,0,$nw,$nh,$w,$h);
			}

			$filename = uniqid();
			

 			switch(strtolower($imageFormat){

 				case 'gif':
 					$dest = $this->_uploadPath . '/' . $filename . '.gif';
 					imagegif($dimg, $dest, $quality);
 					break;

				case 'jpeg': case 'jpg':
					
					//enable lazy loading
					imageinterlace($dimg, true);
					$dest = $this->_uploadPath . '/' . $filename . '.jpg';
					imagejpeg($dimg, $dest, $quality);
					break;
			    
			    case 'png':
			    	$dest = $this->_uploadPath . '/' . $filename . '.png';
			    	imagepng($dimg, $dest, $quality);
			    	break;

		    } 
 
			//destroy image vars
			imagedestroy($simg);
			imagedestroy($dimg);

			return json_encode(array('success' => true, 'imageUrl' => $dest, 'imageName' => $filename));

		}

		public function crop(Array $dimensions){

		}

		public function thumbnail(Array $dimensions){
			$width = isset($dimensions['width']) ? $width : NULL;
		}

	}

 ?>