<?php 
	 define('LOG', dirname(__FILE__).'/../libs/DB/DB.class.php');
	 define('DB', dirname(__FILE__).'/../libs/Log/Log.class.php');
	 define('MEMCACHE', dirname(__FILE__).'/../libs/memcache/Memcache.php');
	 define('WX', dirname(__FILE__).'/../libs/wx/WxLib.class.php');
	 define('SMS', dirname(__FILE__).'/../libs/sms/SMS.php');

	 /**
	  * the modules load
	  * @var array
	  */
	 $load_modules = array(
		LOG,
		DB,
		MEMCACHE,
		WX,
		SMS);
 ?>