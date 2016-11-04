<?php 
	require_once dirname(__FILE__).'/LogConfig.php';

	/**
	* log for caca
	*/
	class Log
	{
		public static function setFilePath()
		{
			$log_file = fopen(LOCAL_LOGPATH, "a+");
			return $log_file;
		}
		public static function setContent($content, $file="", $line="", $log_level="")
		{
			date_default_timezone_set('Asia/Shanghai');
		    $time = date('Y-m-d H:i:s');
		    //get name from path
		    if ($file != "") {
		    	$file = basename($file);
		    }
		    $content = $time . " ".$log_level." :FILE: ".$file.", LINE: ".$line.", log: ".$content.PHP_EOL;
		    return $content;
		}
		public static function closeFile($file = "")
		{
			if ($file == "") {
				return true;
			}else{
				fclose($file);
				return true;
			}
		}
		public static function infoLog($content = "")
		{
			if(is_array($content)){
				$content = implode(',', $content);
			}
			$log_file = Log::setFilePath();
			if (!$log_file) {
				return false;
			}
			$callerInfo =debug_backtrace();
		    if (isset($callerInfo[0])) {
		    	$file = end(explode('/', $callerInfo[0]['file']));
		    	$line = $callerInfo[0]['line'];
		    }
			$log_con = Log::setContent($content, $file, $line, "[ InfoLog ]");
			fwrite($log_file, $log_con);
			return Log::closeFile($log_file);
		}
		public static function debugLog($content = "")
		{
			if(is_array($content)){
				$content = implode(',', $content);
			}
			$log_file = Log::setFilePath();
			if (!$log_file) {
				return false;
			}
			$callerInfo =debug_backtrace();
		    if (isset($callerInfo[0])) {
		    	$file = end(explode('/', $callerInfo[0]['file']));
		    	$line = $callerInfo[0]['line'];
		    }
			$log_con = Log::setContent($content, $file, $line, "[ DebugLog ]");
			fwrite($log_file, $log_con);
			return Log::closeFile($log_file);
		}
		public static function errorLog($content = "")
		{
			if(is_array($content)){
				$content = implode(',', $content);
			}
			$log_file = Log::setFilePath();
			if (!$log_file) {
				return false;
			}
			$callerInfo =debug_backtrace();
		    if (isset($callerInfo[0])) {
		    	$file = end(explode('/', $callerInfo[0]['file']));
		    	$line = $callerInfo[0]['line'];
		    }
			$log_con = Log::setContent($file, $line, $content, "[ ErrorLog ]");
			fwrite($log_file, $log_con);
			return Log::closeFile($log_file);
		}
		public static function warnLog($content = "")
		{
			if(is_array($content)){
				$content = implode(',', $content);
			}
			$log_file = Log::setFilePath();
			if (!$log_file) {
				return false;
			}
			$callerInfo =debug_backtrace();
		    if (isset($callerInfo[0])) {
		    	$file = end(explode('/', $callerInfo[0]['file']));
		    	$line = $callerInfo[0]['line'];
		    }
			$log_con = Log::setContent($file, $line, $content, "[ WarnLog ]");
			fwrite($log_file, $log_con);
			return Log::closeFile($log_file);
		}
	}

 ?>