<?php 
	require_once dirname(__FILE__).'/libs/Log/Log.class.php';
	if (!isset($_SESSION)) {
		session_start();
		Log::accessLog("start accessId : ".session_id());
	}
	//require controllers
	$controller_dir = dirname(__FILE__).'/Controller/';
	if (is_dir($controller_dir)) {
	    if ($dh = opendir($controller_dir)) {
	        while (($file = readdir($dh)) !== false) {
	        	if (preg_match('/[^.]+Controller\.class\.php/i', $file, $matchs)) {
	        		require_once $controller_dir.$file;
	        	}
	            
	        }
	        closedir($dh);
	    }
	}else{
		exit(json_encode(array('result' => false, 'data' =>"Can not find the controller dir = ".$controller_dir, 'err_code' => 404),JSON_UNESCAPED_UNICODE));
	}

	/**
	 * 对象 转 数组
	 *
	 * @param object $obj 对象
	 * @return array
	 */
	function object_to_array($obj) {
	    $obj = (array)$obj;
	    foreach ($obj as $k => $v) {
	        if (gettype($v) == 'resource') {
	            return;
	        }
	        if (gettype($v) == 'object' || gettype($v) == 'array') {
	            $obj[$k] = (array)object_to_array($v);
	        }
	    }
	 
	    return $obj;
	}

	//get class and method
	function getController($path)
	{
		$path_kv = explode("/", $path);
		$arr_size = count($path_kv);
		$path_res['class'] = $path_kv[$arr_size-2]."Controller";
		$path_res['method'] = $path_kv[$arr_size-1]."Controller";
		return $path_res;
	}

	//get parameters split by '='
	function getParameters($params)
	{
		$params_res = array();
        if (empty($params)) {
            return $params_res;
        }
		$params_kv = explode("&", $params);
		foreach ($params_kv as $param) {
			$kv = explode("=", $param);
			$params_res[$kv[0]] = $kv[1];
		}
		return $params_res;
	}
	//split request and request parameters
	function splitRequest($request)
	{
		$request = urldecode($request);
		$split_request = array();
		$request_elements = explode("?", $request);

		if (count($request_elements) == 1) {
			$split_request['request'] = $request_elements[0];
			$split_request['params'] = "";
			return $split_request;
		}else if (count($request_elements) == 2) {
			$split_request['request'] = $request_elements[0];
			$split_request['params'] = $request_elements[1];
			return $split_request;
		}else{
			return $split_request;
		}
	}

	//get the request url,and params in url
	$request = $_SERVER['REQUEST_URI'];

	$split_request = array();
	$request_params = array();
	$split_request = splitRequest($request);

	if (count($split_request) != 0) {
		$request = $split_request['request'];
		$request_params_str = $split_request['params'];
		$request_params = getParameters($request_params_str);
	}
	//get the request method : /GET /POST /PUT /DELTE
	$request_method = $_SERVER['REQUEST_METHOD'];
	// Log::infoLog("Method : ".$request_method);

	if ($request_method == 'GET') {
		$content = $request_params;
	}elseif ($request_method == 'POST') {
		$content = file_get_contents("php://input");
		if (isset($_SERVER['CONTENT_TYPE'])) {
			$request_content_type = $_SERVER['CONTENT_TYPE'];
			Log::accessLog("Content-Type : ".$request_content_type);
			if ($request_content_type == "application/x-www-form-urlencoded") {
				$content = urldecode($content);
				$content = getParameters($content);
			}elseif ($request_content_type == "application/json") {
				$content = json_decode($content, true);
			}elseif ($request_content_type == "text/xml") {
				if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
					$content = $GLOBALS['HTTP_RAW_POST_DATA'];
					$postObj = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
					//if content is xml, then just use xml
					$content = [];
					$content['xml'] = object_to_array($postObj);
				}
			}
		}else{
			Log::warnLog("Content-type is not exist");
			$content = getParameters($content);
		}
	}

	// Log::infoLog("========== SERVER ARRAY ===============");
	// Log::infoLog($_SERVER);
	// Log::infoLog("========== SERVER ARRAY ===============");
	// 
	
	$request = getController($request);

	Log::accessLog("Access Request. class : ".$request['class'].", method : " .$request['method']);
	Log::accessLog($content);

	//class must exist
	if (!class_exists($request['class'])) {
		Log::errorLog("Can not find the class = ".$request['class']);
		exit(json_encode(array('result' => false, 'data' =>"can not find the class = ".$request['class'], 'err_code' => 404),JSON_UNESCAPED_UNICODE));
	}
	//method must exist
	if (!method_exists(new $request['class'](), $request['method'])) {
		// Log::errorLog("can not find the method =".$request['method']);
		exit(json_encode(array('result' => false, 'data' =>"can not find the method, method = ". $request['method'], 'err_code' => 404),JSON_UNESCAPED_UNICODE));
	}
	Log::infoLog("Start invoke method: class = ".$request['class'].", method = ".$request['method']);
	// get the reflection method object
	$refMethod = new ReflectionMethod($request['class'],  $request['method']); 
    $params = $refMethod->getParameters(); 
    //write the params to pass
    $pass = array();
    foreach($refMethod->getParameters() as $param) 
    { 
      // @var $param ReflectionParameter 
      if(isset($content[$param->getName()])) 
      { 
        $pass[] = $content[$param->getName()]; 
      } 
      else
      { 
     	if ($param->isOptional()) {
     		$pass[] = $param->getDefaultValue(); 
     	}else{
     		Log::errorLog("The param can not be empty: param: ".$param->getName().", method: ".$request['method']);
			exit(json_encode(array('result' => false, 'data' =>"The param can not be empty : ".$param->getName(), 'err_code' => 404),JSON_UNESCAPED_UNICODE));
     	}
        
      }
    } 

    $refMethod->invokeArgs(new $request['class'](),(array)$pass);
 ?>