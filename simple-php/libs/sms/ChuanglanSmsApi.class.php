<?php 

require_once dirname(__FILE__)."/ChuanglanSmsConf.php";
class ChuanglanSmsApi {
	
	/**
	 * send sms
	 *
	 * @param string $mobile phone_number
	 * @param string $msg message content
	 * @param string $needstatus if need status report
	 * @param string $extno   extern number
	 */
	public function sendSMS( $mobile, $msg, $needstatus = 'false', $extno = '') {
		global $chuanglan_config;
		//chuanglan interface parameters
		$postArr = array (
				          'account' => $chuanglan_config['api_account'],
				          'pswd' => $chuanglan_config['api_password'],
				          'msg' => $msg,
				          'mobile' => $mobile,
				          'needstatus' => $needstatus,
				          'extno' => $extno
                     );
		
		$result = $this->curlPost( $chuanglan_config['api_send_url'] , $postArr);
		return $result;
	}
	
	/**
	 * query the sms balance
	 *
	 *  query address
	 */
	public function queryBalance() {
		global $chuanglan_config;
		//查询参数
		$postArr = array ( 
		          'account' => $chuanglan_config['api_account'],
		          'pswd' => $chuanglan_config['api_password'],
		);
		$result = $this->curlPost($chuanglan_config['api_balance_query_url'], $postArr);
		return $result;
	}

	/**
	 * trans the result
	 */
	public function execResult($result){
		$result=preg_split("/[,\r\n]/",$result);
		return $result;
	}

	/**
	 * send http request by curl
	 * @param string $url  request url
	 * @param array $postFields request parameters
	 * @return mixed
	 */
	private function curlPost($url,$postFields){
		$postFields = http_build_query($postFields);
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postFields );
		$result = curl_exec ( $ch );
		curl_close ( $ch );
		return $result;
	}
	
	//magic get function
	public function __get($name){
		return $this->$name;
	}
	
	//magic set function
	public function __set($name,$value){
		$this->$name=$value;
	}
}

 ?>