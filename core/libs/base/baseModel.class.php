<?php

defined('ACCESS') or Error::exitApp();

class BaseModel extends FuniObject implements SplSubject{
	
	protected $_view;
	protected $_observers = array();
	protected $_listerners = array();
	
	public final function attach(SplObserver $observer){
		unset($this->_observers);
		$this->_observers[] = $observer;
	}
	
	public final function detach(SplObserver $observer){
	  $key = array_search($observer, $this->_observers);
      if($key !== false){
          unset($this->_observers[$key]);
       }
    
	}
	
	public final function notify(){
	  foreach($this->_observers as $obs) {
        $obs->update($this);
      }
	}
	

	public function addEventListener($event, $listener, $listenerAction){
		
		$this->_listerners[strtolower($event)][] = array($listener, $listenerAction);
		
	}

	public function triggerEvent($eventName, $param){
		if(isset($this->_listerners[strtolower($eventName)])){
			foreach($this->_listerners[strtolower($eventName)] as $event){
				call_user_func(array($event[0], $event[1]), $param);
			}
		}
	}
}