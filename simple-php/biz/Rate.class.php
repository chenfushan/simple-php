<?php 
	/**
	* Admin class
	*/
	require_once dirname(__FILE__).'/../loadModules.php';
	class Rate
	{
		private static $mysqli;
		
		function __construct()
		{
			self::$mysqli = new DB();
		}

		public function updateRate($rate)
		{
			$query = "select symbol from rate where symbol = '".$rate['symbol']."';";
			$res = self::$mysqli->executeSql($query);
			if (!$res) {
				Log::errorLog("select rate error. symbol=".$rate['symbol']);
			}
			if (self::$mysqli->getNumRows() > 0) {
				$query = "update rate set price = '".$rate['price']."', bid = '".$rate['bid']."', ask='".$rate['ask']."', time_stamp='".$rate['timestamp']."', modify_time=now();";
			}else{
				$query = "insert into rate(symbol, price, bid, ask, time_stamp, modify_time, create_time) values('".$rate['symbol']."', '".$rate['price']."', '".$rate['bid']."', '".$rate['ask']."', '".$rate['timestamp']."', now(), now());";
			}
			$res = self::$mysqli->executeSql($query);
			if (!$res) {
				Log::errorLog("update rate error. symbol=".$rate['symbol']);
			}
			return true;
		}

		public function getRateBySymbol($symbol, &$rate)
		{
			$query = "select symbol, price, bid, ask, time_stamp, modify_time, create_time from rate where symbol='".$symbol."';";
			$res = self::$mysqli->executeSql($query);
			if (!$res) {
				Log::warnLog("get rate error. symbol=".$symbol);
				return false;
			}
			$rate = self::$mysqli->resToObject();
			return true;
		}

		public function getRateByPairs($pairs, &$rate)
		{
			//$pairs = "'CHN', 'USD'";
			$query = "select symbol, price, timestamp, modify_time from rate where symbol in (".$pairs.");";
			$res = self::$mysqli->executeSql($query);
			if (!$res) {
				Log::warnLog("get rate error. symbol".$pairs);
				return false;
			}
			$rate = self::$mysqli->resToArray();
			return true;
		}

	}


 ?>