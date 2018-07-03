<?php
	function trw($callback)
	{

		if (!is_callable($callback)) {
			throw new Exception('msg', 101);
		}else{
			call_user_func($callback);
			// $result['success'] = true;
		}
	}

	try {
		$result = [];
		$name = "chen";
		trw(function () use ($name, &$result)
		{
			
			echo "name is $name";
		});
		echo json_encode($result);
		// 
		// $message = 'hello';

		// // 没有 "use"
		// $example = function () use ($message) {
		//     var_dump($message);
		// };
		// echo $example();
	} catch (Exception $e) {
		// var_dump($e);

		// echo $e->__toString();

		// echo $e->getMessage();
		// echo $e->getFile();
		// echo $e->getLine();
		// echo $e->getCode();
		echo json_encode($e->getTrace());
	}

	function echoFunc($value='')
	{
		echo "haha";
	}

	/**
	 * class
	 */
	class Haha
	{
	    /**
	     * summary
	     */
	    public function __construct()
	    {
	        
	    }

	    public static function testFunc()
	    {
	    	echoFunc();
	    }
	}

	Haha::testFunc();
?>