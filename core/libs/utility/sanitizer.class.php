<?php 

defined('ACCESS') || AppError::exitApp();

/**
* 
*/
class Sanitizer extends FuniObject{
	/*
	* Collects an array of required fields of a form and check weda the fields are filled
	*/
	public function checkRequiredFields(Array $requiredFields){
		
		$field_errors = array();
        
        // form validation
        //checks if all the fields in the form are filled
        foreach($requiredFields as $fieldname) {
		    if ( !isset($_POST[$fieldname]) || !$_POST[$fieldname] || (empty($_POST[$fieldname]) && !is_int($_POST[$fieldname]) ) ){ 
			   	$field_errors[] = $fieldname;  // adds to the errors array the field not set
			 }
		}

	    if(empty($field_errors)){
			return json_encode(array('status'=>'ok','msg'=>''));
		}else{
			$msg = count($field_errors);
			/*$msg .=  ' ( ';
			foreach ($field_errors as $err) {
				# code...
				$msg .= $err . ', ';
			}
			$msg = rtrim($msg, ',');
			$msg .= ' ) ';*/
			$msg .= (count($field_errors) > 1) ? ' required fields not filled' : ' required field not filled';
			return json_encode(array('status'=>'error','msg'=> $msg));
		}
	}


	public function sanitize($input, $sanitationType){
	    
		switch (strtolower($sanitationType)){
			case 'email':
				return filter_var($input, FILTER_SANITIZE_EMAIL);
				break;
			case 'int':
				return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
				break;
			case 'string': default:
				return filter_var($input, FILTER_SANITIZE_STRING);
				break;
			case 'url':
				return filter_var($input, FILTER_SANITIZE_URL);
				break;
			case 'float':
				return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT);
				break;
		}
			
    }


    public function validate($input, $sanitationType){
		switch (strtolower($sanitationType)){
			case 'email':
				return filter_var($input, FILTER_VALIDATE_EMAIL);
				break;
			case 'int':
				return filter_var($input, FILTER_VALIDATE_INT);
				break;
			case 'string': default:
				return filter_var($input, FILTER_VALIDATE_STRING);
				break;
			case 'url':
				return filter_var($input, FILTER_VALIDATE_URL);
				break;
			case 'float':
				return filter_var($input, FILTER_VALIDATE_FLOAT);
				break;
		}
	
	}

	public function filterArray(Array $data, Array $filterOptions){
		return filter_var_array($data, $filterOptions);
	}



	
# End of Class	
}

 ?>