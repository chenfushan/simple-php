<?php 
	date_default_timezone_set('Asia/Shanghai');
	$time = date('Y-m-d');

	//define the log path
	define("LOCAL_LOGPATH" , dirname(__FILE__).'/../../Runtime/log/log_'.$time.'.log');
	//define the access log path
	define("ACCESS_LOGPATH", dirname(__FILE__).'/../../Runtime/log/access_'.$time.'.log');
	//define the common error log path
	define('ERROR_LOGPATH', dirname(__FILE__).'/../../Runtime/log/error_'.$time.'.log');;
	
 ?>