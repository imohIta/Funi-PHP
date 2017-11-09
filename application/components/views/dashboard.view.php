<?php
/**
*
*
*/
defined('ACCESS') || AppError::exitApp();

class DashBoardView extends BaseView{
	
	public function update(SplSubject $subject){
	    $mthd = $subject->get('viewParams')['action'];
	    if($mthd == 'display'){
	    	$this->$mthd($subject->get('viewParams')['widget'],$subject->get('viewParams')['msg']);
	    }else{
	    	$this->$mthd($subject->get('viewParams')['tmpl']);
	    }
	}

#B35954
	
}