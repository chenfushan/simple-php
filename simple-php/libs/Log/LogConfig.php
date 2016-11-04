<?php 
	date_default_timezone_set('Asia/Shanghai');
	$time = date('Y-m-d');

	//define the log path
	define("LOCAL_LOGPATH" , dirname(__FILE__).'/../../Runtime/log/log_'.$time.'.log');
 ?>