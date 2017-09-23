<?php 
	/**
	* Admin class
	*/
	require_once dirname(__FILE__).'/../loadModules.php';
	class Admin
	{
		function __construct()
		{
		}

		public function login($username, $password)
		{
			$mysqli = new DB();
			$query = "select admin_name, admin_password from admin where admin_name = '".$username."' and admin_password = sha1('".$password."')";
			$res = $mysqli->executeSql($query);
			if (!$res || $mysqli->getNumRows() <= 0) {
				Log::warnLog("user login error, username=".$username.", password=".$password);
				return false;
			}
			Log::debugLog("lgoin success, username=".$username);
			return true;
		}
		
	}


 ?>