<?php
/**
*
*
*/
defined('ACCESS') || AppError::exitApp();

class Includer extends FuniObject{

	protected $_uriPath;
	protected $_tmplPath;
	protected $_header;
	protected $_sidebar;
	protected $_footer;


	public function __construct(Config $config, Array $options = array()){

		//$this->_uriPath = str_replace('public', '', $config->get('baseUri'));
		$this->_uriPath = $config->get('baseUri') . $config->get('ds');

		if(isset($options['tmplPath'])){

			//check if baspath already in tmplPath Passed
			$this->_tmplPath = (strpos($options['tmplPath'], $config->get('basePath')) === false) ? $config->get('basePath') . $config->get('ds') . $options['tmplPath'] . $config->get('ds') : $options['tmplPath'];

		}else{
			$this->_tmplPath = NULL;
		}

		$this->_header = isset($options['header']) ? $options['header'] : NULL;
		$this->_sidebar = isset($options['sidebar']) ? $options['sidebar'] : NULL;
		$this->_footer = isset($options['footer']) ? $options['footer'] : NULL;

		
	}


	/**
	* Render Part of the file passed as part..eg header
	* @param - Part : part of the tmpl to be rendered ..e.g header, sidebar
	* @param - Array dependecies ... css, js or any other dependent file to be included in Parts
	*/
	public function render($part, Array $options){
		$key = '_' . $part;


		//check if the filepath part to be rendered is available
		if(property_exists(__CLASS__, $key) && isset($this->{$key})){
			//check if file exist
			if(file_exists($this->_tmplPath . $this->{$key} . '.tmpl.php')){
			   //render Part
			   $action = '_render' . ucfirst($part);
			   if(strtolower($part) == 'sidebar'){
			   		$this->$action();
			   }else{
					$this->$action($options);
			   }

			}else{
				Error::throwException( $part . ' Template File Does not Exist');
		    }
		}

		# return object to allow method chaining
		return $this;
	}

	private function _renderHeader(Array $options){
		global $registry;

		$cj = $this->_build($options);
		$css = $cj['css'];
		$js = $cj['js'];
		include $this->_tmplPath . $this->_header . '.tmpl.php';

		# return object to allow method chaining
		return $this;

	}

	private function _renderSidebar(){
		global $registry;
		include $this->_tmplPath . $this->_sidebar . '.tmpl.php';

		# return object to allow method chaining
		return $this;
	}

	private function _renderFooter(Array $options){
		global $registry;

		$cj = $this->_build($options);
		$css = $cj['css'];
		$js = $cj['js'];
		include $this->_tmplPath . $this->_footer . '.tmpl.php';

		# return object to allow method chaining
		return $this;
	}

	private function _build(Array $options){
		global $registry;
		$cssFiles = ''; $jsFiles = '';

		# debugging
		/*var_dump($options);
		echo '<br/>.......';*/


		$cssPath = $this->_uriPath .'css/' . $registry->get('router')->get('component') . ucfirst($registry->get('router')->get('params')['mthd']) .'.css';


		$jsPath = $this->_uriPath .'js/' . $registry->get('router')->get('component') . ucfirst($registry->get('router')->get('params')['mthd']) .'.js';


		//check if minified css version for this component already exist
		//if(!file_exists($cssPath)){
			if(isset($options['css'])){
				foreach($options['css'] as $css){
					//get css file, minify it and append it to a new css File

					//file_put_contents($cssPath, $registry->get('minifier')->minify(file_get_contents($this->_uriPath .'assets/css/' . $css)) , FILE_APPEND);

					$cssFiles .= "<link href='" . $this->_uriPath ."assets/css/" . $css . "' rel='stylesheet' type='text/css' />";
				}

			}
			//echo $cssFiles; die;

		//}


		//$css = '<link href="' . $cssPath . '" rel="stylesheet" type="text/css" />';

		if(!file_exists($jsPath)){
			if(isset($options['js'])){
				foreach($options['js'] as $js){

					//file_put_contents($jsPath, $registry->get('minifier')->minifyJs(file_get_contents($this->_uriPath .'js/' . $js)) , FILE_APPEND);

					$jsFiles .= "<script src='" . $this->_uriPath ."assets/js/" . $js . "' type='text/javascript'></script>";

				}
			}
		}
		//$js .= '<script src="' . $this->jsPath .'" type="text/javascript"></script>';

		return array('css'=>$cssFiles, 'js'=>$jsFiles);


	}

	public function renderWidget($widget)
	{
		global $registry;
		$path = str_replace('parts', 'widgets', $this->_tmplPath);
		include $path . '/' . $widget . '.widget.php';

		# return object to allow method chaining
		return $this;
	}



}
