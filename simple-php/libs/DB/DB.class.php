<?php 
	require_once dirname(__FILE__).'/DbConfig.php';
	require_once dirname(__FILE__).'/DBUtils.php';
	require_once dirname(__FILE__).'/../Log/Log.class.php';
	/**
	* Database class connect
	*/
	class DB
	{
		private static $mysqli;
		private $affect_rows;
		private $query_res;
		private $query_num_rows;
		private $errno;
		function __construct()
		{
			/* connect the mysql */
			self::$mysqli = new mysqli(HOST, USER, PASSWORD, DBNAME);
			self::$mysqli->set_charset('utf8');
			/* check connection */
			if (self::$mysqli->connect_errno) {
			    Log::errorLog("Connect DB failed: .".$mysqli->connect_error);
			    exit();
			}
		}
		/**
		 * if you want to connect other Database, use this function
		 * @param  string $HOST     database host
		 * @param  string $USER     database user name
		 * @param  string $PASSWORD database password
		 * @param  string $DBNAME   database name
		 * @return boolean          true or false
		 */
		public function connect($HOST, $USER, $PASSWORD, $DBNAME)
		{
			/* connect the mysql */
			self::$mysqli = new mysqli($HOST, $USER, $PASSWORD, $DBNAME);
			self::$mysqli->set_charset('utf8');
			/* check connection */
			if (self::$mysqli->connect_errno) {
			    // Log::errorLog("Connect DB failed: .".$mysqli->connect_error);
			    return false;
			}
			return true;
		}

		/**
		 * close the database connection
		 * @return 0 no matter if success, return 0
		 */
		public function close()
		{
			self::$mysqli->close();
			return 0;
		}

		/**
		 * get the last insert id
		 * @return int id
		 */
		public function getLastInsertId()
		{
			return self::$mysqli->insert_id;
		}

		/**
		 * if execute sql errno, you can get errno from this function
		 * @return int err code
		 */
		public function getErrno()
		{
			return $this->errno;
		}

		/**
		 * get the sql query result
		 * @return dataset the database query result
		 */
		public function getRes()
		{
			return $this->query_res;
		}
		/**
		 * free the query result dataset
		 * @return 0 no matter if free success, return 0
		 */
		public function freeRes()
		{
			$query_res->free();
			return 0;
		}

		/**
		 * execute the sql
		 * @param  string $query the query sql
		 * @return boolean       execute success return true otherwise return false
		 */
		public function executeSql($query, &$errno = 0)
		{
			/* if use log module, cancel the comment */
			// Log::debugLog("exec the query: ".$query);
			$res = self::$mysqli->query($query);
			if (!$res) {
				Log::errorLog("execute sql error: ".$mysqli->error ." ". $mysqli->errno);
				Log::errorLog($query);
				$errno = self::$mysqli->errno;
				$this->errno = $errno;
				return false;
			}
			$this->query_res = $res;
			$this->affect_rows = self::$mysqli->affected_rows;
			return true;
		}

		/**
		 * get the result num rows last execute sql statement
		 * @return int the result count rows
		 */
		public function getNumRows()
		{
			return $this->query_res->num_rows;;
		}

		/**
		 * get the affect rows last execute sql ( do not support transaction)
		 * @return int the affect rows number
		 */
		public function getAffectedRows()
		{
			return $this->affect_rows;
		}

		/**
		 * start a transction for mysql
		 * @param  array $sqls the sql arrays
		 * @return boolean       if execute the transaction error it will rollback
		 */
		public function transaction($sqls, &$errno=0)
		{
			if (!is_array($sqls)) {
				// Log::debugLog("sqls is not a array, can not start as a transaction");
				return false;
			}
		 	if (!self::$mysqli->query('BEGIN')) {
		 	 	// Log::errorLog("start transaction fail");
		 	 	return false;
		 	 }
			foreach ($sqls as $sql) {
				$res = self::$mysqli->query($sql);
				if (!$res) {
					Log::errorLog("execute sql error: ".$mysqli->error ." ". $mysqli->errno);
					Log::errorLog($query);
					$errno = self::$mysqli->errno;
					$this->errno = $errno;
					if(!self::$mysqli->query('ROLLBACK')){
						Log::errorLog("ROLLBACK fail");
						return false;
					}
					// Log::debugLog("ROLLBACK success!");
					return false;
				}
			}
			$res = self::$mysqli->query('COMMIT');
			if (!$res) {
				// Log::errorLog("commit error");
				// if commit error, should send error sms to you phone
				foreach ($sqls as $sql) {
					// Log::infoLog("sql:".$sql);
				}
				// Log::debugLog("commit success");
				return false;
			}
			return true;
		}

		/**
		 * transfer the result to array
		 * @return array the execute result
		 */
		public function resToArray()
		{
			$resArray = array();
	        $count = 0;
	        while ($row = $this->query_res->fetch_assoc()) {
	            $resArray[$count] = $row;
	            $count++;
	        }
	        return $resArray;
		}

		/**
		 * if the result is one and only one row result.
		 * this function can just get the array[0]
		 * if the result is empty, return a empty array
		 * @return array 
		 */
		public function resToObject()
		{
			$resArray = array();
			$count = 0;
			while ($row = $this->query_res->fetch_assoc()) {
				$resArray[$count] =$row;
				$count++;
			}

			if ($count > 0) {
				return $resArray[0];
			}else{
				return array();
			}
		}
	}

 ?>