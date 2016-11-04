<?php 
	/**
	 * check the input args, transfer the input arg to safe model
	 * @param  string $inp the input string
	 * @return string      safe model input data
	 */
	function check_input($inp) { 
	    if(is_array($inp)) 
	        return array_map(__METHOD__, $inp); 

	    if(!empty($inp) && is_string($inp)) { 
	        return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp); 
	    } 

	    return $inp; 
	}

 ?>