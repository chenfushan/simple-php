<?php 
	if (!isset($_SESSION)) {
		session_start();
	}
	require_once dirname(__FILE__).'/DbConfig.php';
	$controller_dir = dirname(__FILE__).'/Controller/';
	if (is_dir($controller_dir)) {
	    if ($dh = opendir($controller_dir)) {
	        while (($file = readdir($dh)) !== false) {
	        	if (preg_match('/[^.]+Controller\.class\.php/i', $file, $matchs)) {
	        		require_once $controller_dir.$file;
	        		// echo $controller_dir.$file.PHP_EOL;
	        	}
	            
	        }
	        closedir($dh);
	    }
	}else{
		exit(json_encode(array('result' => false, 'data' =>"can not find the controller dir = ".$controller_dir, 'err_code' => 404),JSON_UNESCAPED_UNICODE));
	}

	function getController($path)
	{
		$path_kv = explode("/", $path);
		$arr_size = count($path_kv);
		$path_res['class'] = $path_kv[$arr_size-2]."Controller";
		$path_res['method'] = $path_kv[$arr_size-1]."Controller";
		return $path_res;
	}

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

	//get the request url, get the controller and action
	// Log::infoLog("receive request");
	$request = $_SERVER['REQUEST_URI'];
	$request = getController($request);
	//get the post data
	$content = file_get_contents("php://input");
	$content = urldecode($content);
	$content = getParameters($content);
	//class must exist
	if (!class_exists($request['class'])) {
		// Log::errorLog("can not find the class =".$request['class']);
		exit(json_encode(array('result' => false, 'data' =>"can not find the class = ".$request['class'], 'err_code' => 404),JSON_UNESCAPED_UNICODE));
	}
	//method must exist
	if (!method_exists(new $request['class'](), $request['method'])) {
		// Log::errorLog("can not find the method =".$request['method']);
		exit(json_encode(array('result' => false, 'data' =>"can not find the method, method = ". $request['method'], 'err_code' => 404),JSON_UNESCAPED_UNICODE));
	}
	// Log::infoLog("start invoke method, class=".$request['class'].", method = ".$request['method']);
	// get the reflection method object
	$refMethod = new ReflectionMethod($request['class'],  $request['method']); 
    $params = $refMethod->getParameters(); 
    //write the params to pass
    $pass = array();
    foreach($refMethod->getParameters() as $param) 
    { 
      /* @var $param ReflectionParameter */ 

      if(isset($content[$param->getName()])) 
      { 
        $pass[] = $content[$param->getName()]; 
      } 
      else
      { 
     	if ($param->isOptional()) {
     		$pass[] = $param->getDefaultValue(); 
     	}else{
     		// Log::errorLog("the param can not be empty, param: ".$param->getName().", method: ".$request['method']);
			exit(json_encode(array('result' => false, 'data' =>"the param can not be empty : ".$param->getName(), 'err_code' => 404),JSON_UNESCAPED_UNICODE));
     	}
        
      }
    } 

    $refMethod->invokeArgs(new $request['class'](),(array)$pass);
    
 ?>