<?php 
	if (!isset($_SESSION)) {
		session_start();
	}
	require_once dirname(__FILE__).'/Controller.class.php';
	require_once dirname(__FILE__).'/../loadModules.php';
	/**
	* index controller
	*/
	class IndexController extends Controller
	{	
		private static $rate;
		function __construct()
		{
		}
		/**
		 * @param  string
		 * @param  num
		 * @return boolean
		 */
		public function indexController($name)
		{
			$this->ajaxReturn("request name is: ". $name);
		}
	}
 ?>