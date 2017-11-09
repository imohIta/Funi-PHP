<?php
/**
* Will Handle Pagination
* Culled From Pagination Class of My previous prjts
*
*/

//namespace \Libs\Core;

defined('ACCESS') || AppError::exitApp();

class Paginator extends FuniObject{
	 protected $_currentPage;
	 protected $_perPage;
	 protected $_totalCount;
	 protected $_data;
 
	public function __construct(Array $data, $perPage=null){
		$this->_perPage = is_null($perPage) ? 20 : (int)$perPage;
		$this->_totalCount = count($data);
		$this->_currentPage = 1;
		$this->_data = $data;

	}
	 
	private function _offset($page = null){
		$page = is_null($page) ? $this->_currentPage : $page;
	 	return ($page - 1) * $this->_perPage;
	}
	 
	public function totalPages(){
		 return ceil($this->_totalCount/$this->_perPage);
	}
	public function _previousPage(){
		return $this->_currentPage - 1;
	}
	public function _nextPage(){
		return $this->_currentPage + 1;
	}
	public function hasPreviousPage(){
		return $this->_previousPage() >= 1 ? true : false;
	}
	public function hasNextPage(){
		return $this->_nextPage() <= $this->totalPages() ? true: false;
	}

	public function load($page = 1)
	{
		# code..
		$this->_currentPage = $page;
		return $output = array_slice($this->_data, $this->_offset($page), $this->_perPage);

	}

	// public function load($start = null)
	// {
	// 	# code...
	// 	$start = is_null($start) ? 0 : $start;
	// 	return $output = array_slice($this->_data, $start, $this->_perPage);
	// }

	public function next()
	{
		# code...
		$this->_currentPage++;
		return $this->load($this->_offset());
	}

	public function previous()
	{
		# code...
		$this->_currentPage--;
		return $this->load($this->_offset());
	}
	
}

/**
* 
*/
