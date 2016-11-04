<?php 
	require_once dirname(__FILE__).'/conf/ModuleConf.php';

	global $load_modules;

	for ($i=0; $i < count($load_modules); $i++) { 
		require_once $load_modules[$i];
	}
 ?>