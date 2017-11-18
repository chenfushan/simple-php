<?php 
	if (!isset($_SESSION)) {
		session_start();
	}
	require_once dirname(__FILE__).'/Controller.class.php';
	require_once dirname(__FILE__).'/../loadModules.php';
	require_once dirname(__FILE__).'/../conf/CalculateConf.php';
	require_once dirname(__FILE__).'/../biz/Rate.class.php';
	/**
	* index controller
	*/
	class IndexController extends Controller
	{	
		private static $rate;
		function __construct()
		{
			self::$rate = new Rate();
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

		public function getRate($pairs)
		{
			date_default_timezone_set('Asia/Shanghai');
			$url = QUOTESHOST."pairs=".$pairs."&api_key=".APIKEY;
			$res = file_get_contents($url);
			$jsonRes = json_decode($res, true);
			foreach ($jsonRes as $row) {
				$res = self::$rate->updateRate($row);
				if (!$res) {
					Log::errorLog("update rate error.");
					continue;
				}
			}
			return true;
		}

		public function getPairListController()
		{
			$url = SYMBOLS."api_key=".APIKEY;
			$res = file_get_contents($url);
			$jsonRes = json_decode($res, true);
			$pairs = "";
			$index = 1;
			foreach ($jsonRes as $row) {
				if ($index % 5 == 0) {
					$pairs .= $row;
					$this->getRate($pairs);
					$pairs = "";
				}else{
					$pairs = $pairs.$row.",";
				}
				$index++;
			}
			if ($pairs != "") {
				$this->getRate($pairs);
			}
			
			return true;
		}

		public function updateRateList()
		{
			
		}
	}
 ?>