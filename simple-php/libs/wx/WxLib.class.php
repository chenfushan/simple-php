<?php 
	require_once dirname(__FILE__).'/WxConfig.php';
	require_once dirname(__FILE__).'../memcache/Memcache.php';
	require_once dirname(__FILE__).'/miniprogram/wxBizDataCrypt.php';
	/**
	* WeChat Library
	* You must set the right appid and secret in WxConfig.php
	*/
	class WxLib
	{	
		/**
		 * private construct : can't instance the WxLib object
		 */
		private function __construct() {
		}

		/**
		 * get openid in OAuth2.0 type
		 * @param  string $appid  wx public account appid
		 * @param  string $secret wx public account secret
		 * @param  string $code   the code get from oauth2
		 * @param  string $openid the openid get from wx
		 * @return bool          if success return true, else return false
		 */
		public static function getOpenid($code, &$openid)
		{
			global $WxConfig;
			$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=". $WxConfig['appid'] ."&secret=". $WxConfig['secret'] ."&code=" . $code . "&grant_type=authorization_code";
			$data = json_decode(file_get_contents($url), true);
			if (isset($data['errcode'])) {
				return false;
			}
			$openid = $data['openid'];
			return true;
		}

		/**
		 * get mini program encrypted data content by code
		 * @param  [type] $session_key          wx session key get by wx.login
		 * @param  [type] $encryptedData get by wx.getShareInfo
		 * @param  [type] $iv            get by wx.getShareInfo
		 * @param  [type] &$data         decode information
		 * @param  [type] &$errCode      decode error code
		 * @return [type]                bool
		 */
		public static function getEncryptedDataContent($session_key, $encryptedData, $iv, &$data, &$errCode)
		{
			$name = ['session_key' => $session_key, 'encryptedData' => $encryptedData, 'iv' => $iv];
			Log::debugLog($name);
			global $WxConfig;
			$errCode = 0;
			$pc = new WXBizDataCrypt($WxConfig['appid'], $session_key);
			$errCode = $pc->decryptData($encryptedData, $iv, $jsonData );
			
			if ($errCode == 0) {
				$data = json_decode($jsonData, true);
				Log::debugLog($data);
			    return true;
			} else {
			    return false;
			}
		}

		/**
		 * mini program get openid interface
		 * @param  string $code         grant code get by client
		 * @param  string &$openid      user openid
		 * @param  string &$session_key session
		 * @return bool               true/false
		 */
		public static function getOpenidMiniProgram($code, &$openid, &$session_key = "")
		{
			global $WxConfig;
			$url = "https://api.weixin.qq.com/sns/jscode2session?appid=". $WxConfig['appid'] ."&secret=". $WxConfig['secret'] ."&js_code=".$code."&grant_type=authorization_code";
			$data = json_decode(file_get_contents($url), true);
			if (isset($data['errcode'])) {
				return false;
			}

			$openid = $data['openid'];
			$session_key = $data['session_key'];
			return true;
		}

		/**
		 * return the weixin global access token.
		 * @return global_access_token [WeChat platform access token]
		 */
		public static function getGlobalAccessToken(&$globalAccessToken)
		{
			global $WxConfig;
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $WxConfig['appid'] ."&secret=" . $WxConfig['secret'];
			$globalAccessTokenJson = json_decode(file_get_contents($url), true);
			if (isset($globalAccessTokenJson['errcode'])) {
				return false;
			}
			$globalAccessToken = $globalAccessTokenJson['access_token'];
			return true;
		}

		/**
		 * 	create a rand string length=32
		 */
		public static  function createNoncestr( $length = 32 ) 
		{
			$chars = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";  
			$str ="";
			for ( $i = 0; $i < $length; $i++ )  {  
				$str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
			}  
			return $str;
		}

		/**
		 * [createOutTradeNo description]
		 * @param  boolean $withLine [description]
		 * @return [type]            [description]
		 */
		public static function createOutTradeNo($withLine = true)
		{
			date_default_timezone_set('Asia/Shanghai');
			list($s1, $s2) = explode(' ', microtime()); 
		    $timestamp = (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
		    if ($withLine) {
				$curDate = date("Ymd-His");
		    }else{
		    	$curDate = date("YmdHis");
		    }
			$millisecond = $timestamp%1000;
			$randStr = self::createNoncestr(10);
			return $curDate.$millisecond.$randStr;
		}

		/**
		 * miniprogram pre pay
		 * example : 
		 * outTradeNo = self::createOutTradeNo()
		 * totalFee = cent
		 * body = body
		 * @param  [type] $body       [description]
		 * @param  [type] $totalFee   [description]
		 * @param  [type] $notifyUrl  [description]
		 * @param  [type] $outTradeNo [description]
		 * @param  [type] $openid     [description]
		 * @return [type]             [description]
		 */
		public static function miniprogramPrePay($body, $totalFee, $notifyUrl, $outTradeNo, $openid)
		{
			global $WxConfig;
			$prePayUrl = $WxConfig['prePayUrl'];

			$obj = array();
			$obj['appid']              = $WxConfig['appid'];
			$obj['mch_id']             = $WxConfig['mchId'];
			$obj['nonce_str']			= self::createNoncestr();
			$obj['body']				= $body;
			$obj['trade_type']			= "JSAPI";
			$obj['total_fee']			= $totalFee;//单位为分
			$obj['notify_url']			= $notifyUrl;
			$obj['spbill_create_ip']   = $_SERVER['REMOTE_ADDR'];
			$obj['out_trade_no']		= $outTradeNo;
			$obj['openid']				= $openid;
			//get sign str
			$sign = self::miniprogramSignArray($obj);

			$obj['sign']				= $sign;
			//transfer array to xml
			$xmlData = Util::arrayToXml($obj);
			//post xml without ssl cert
			$res = self::HttpsRequest($prePayUrl, $xmlData);
			$arrRes = Util::xml2array($res);
			return $arrRes;
		}

		/**
		 * 	serialize for array
		 */
		public static function miniprogramSerialize($paraMap)
		{
			$buff = "";
			ksort($paraMap);
			foreach ($paraMap as $k => $v)
			{
				//$buff .= strtolower($k) . "=" . $v . "&";
				$buff .= $k . "=" . $v . "&";
			}
			$reqPar = '';
			if (strlen($buff) > 0) 
			{
				$reqPar = substr($buff, 0, strlen($buff)-1);
			}
			return $reqPar;
		}

		/**
		 * sign for array
		 * @param  array  $params [description]
		 * @return [type]         [description]
		 */
		public static function miniprogramSignArray(array $params)
		{
			global $WxConfig;
			//sort by dict
			ksort($params);
			$serializeStr = self::miniprogramSerialize($params);
			//sign：add string KEY
			$tempStr = $serializeStr."&key=".$WxConfig['mchKey'];
			$signStr = md5($tempStr);
			$signStr = strtoupper($signStr);
			return $signStr;
		}

		/**
		 * mini program refund trade no
		 * @param  [type] $outTradeNo  [description]
		 * @param  [type] $outRefundNo [description]
		 * @param  [type] $totalFee    [description]
		 * @param  [type] $refundFee   [description]
		 * @return [type]              [description]
		 */
		public function miniprogramRefund($outTradeNo, $outRefundNo, $totalFee, $refundFee)
		{
			global $WxConfig;
			$refundUrl = $WxConfig['refundUrl'];

			$obj = array();
			$obj['appid']              = $WxConfig['appid'];
			$obj['mch_id']             = $WxConfig['mchId'];
			$obj['nonce_str']			= self::createNoncestr();
			$obj['refund_fee']			= $refundFee;
			$obj['total_fee']			= $totalFee;//单位为分
			$obj['out_trade_no']		= $outTradeNo;
			$obj['out_refund_no']		= $outRefundNo;

			//get sign str
			$sign = self::miniprogramSignArray($obj);

			$obj['sign']				= $sign;
			//transfer array to xml
			$xmlData = Util::arrayToXml($obj);

			$res = self::curl_post_ssl($refundUrl, $xmlData);
			return $res;
		}

		/**
		 * business pay for openid
		 * @param  [type] $partnerTradeNo [description]
		 * @param  [type] $openid         [description]
		 * @param  [type] $amount         [description]
		 * @return [type]                 [description]
		 */
		public static function payForOpenid($partnerTradeNo, $openid, $amount, $desc, &$arrRes, &$withdrawOrder)
		{
			global $WxConfig;
			$payForOpenidUrl = $WxConfig['payForOpenidUrl'];

			$obj = array();
			$obj['mch_appid']          = $WxConfig['appid'];
			$obj['mchid']             = $WxConfig['mchId'];
			$obj['nonce_str']			= self::createNoncestr();
			$obj['spbill_create_ip']   = $_SERVER['REMOTE_ADDR'];
			$obj['partner_trade_no']	= $partnerTradeNo;
			$obj['openid']				= $openid;
			$obj['check_name']			= 'NO_CHECK';
			$obj['amount']				= $amount; //单位分
			$obj['desc']				= $desc;
			//get sign str
			$sign = self::miniprogramSignArray($obj);

			$obj['sign']				= $sign;
			//transfer array to xml
			$xmlData = Util::arrayToXml($obj);

			$withdrawOrder = $obj;
			//post xml without ssl cert
			$res = self::curl_post_ssl($payForOpenidUrl, $xmlData);
			if (!$res) {
				return false;
			}
			$arrRes = Util::xml2array($res);
			return true;
		}

		/**
		 * query global access token by memcache
		 * @param  [type] &$globalAccessToken [description]
		 * @return [type]                     [description]
		 */
		public static function queryGlobalAccessTokenByMem(&$globalAccessToken)
		{
			global $WxConfig;
			$key = $WxConfig['appid'].'_accessToken';
			$ak = getMemcacheValue($key);
			if (!$ak) {
				$res = self::getGlobalAccessToken($globalAccessToken);
				if (!$res) {
					return false;
				}
				setMemcacheValue($key, $globalAccessToken, 7000);
				return $globalAccessToken;
			}
			$globalAccessToken = $ak;
			return true;
		}

		/**
		 * return the weixin jsapi ticket, example return:
		 * {
		 *	"errcode":0,
		 *	"errmsg":"ok",
		 *	"ticket":"bxLdikRXVbTPdHSM05e5u5sUoXNKd8-41ZO3MhKoyN5OfkWITDGgnr2fwJ0m9E8NYzWKVZvdVtaUgWvsdshFKA",
		 *	"expires_in":7200
		 *	}
		 *	the js_ticket expired time is : 7200s and it can only get the ticket 2k times one day. so you should save in cache ( like memcache )
		 * @param  string $globalAccessToken wechat global access token
		 * @return string                    the jsapi ticket
		 */
		public static function getJsapiTicket($globalAccessToken)
		{
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=" . $globalAccessToken . "&type=jsapi";
			$jsapiTicketJson = json_decode(file_get_contents($url), true);
			$jsapiTicket = $jsapiTicketJson['ticket'];
			return $jsapiTicket;
		}

		/**
		 * construct a template message content
		 * @param  object $TemplateData a object contains template info
		 * @return string                 template message string
		 */
		private static function constructTemplate($TemplateData)
		{
			if (!is_array($TemplateData)) {
				return false;
			}
			$json = '{
	           "touser":"' 		. $TemplateData['touser'] 		.'",
	           "template_id":"'	. $TemplateData['template_id']	.'",
	           "url":'			. $TemplateData['url']			.'",
	           "topcolor":"'	. $TemplateData['topcolor']	 	.'",
	           "data":{'		. $TemplateData['data']	 		.'}
	        }';
	        return $json;
		}

		/**
		 * send template message
		 * @param  string $globalAccessToken WeChat global access token
		 * @param  string $json              template message string
		 * @return TemplateData              {"errcode": 0, "errmsg":"ok", "msgid":200228332 }
		 */
		public static function sendTemplateMsg($TemplateData, $globalAccessToken, &$result)
		{
			if (!is_array($TemplateData) || !is_string($globalAccessToken)) {
				return false;
			}
			global $WxConfig;
			$json = self::constructTemplate($TemplateData);
			$result = self::HttpsRequest($WxConfig['templateSendUrl'].$globalAccessToken, $json);
			return true;
		}

		/**
		 * signature in wechat
		 * @param  string $jsapiTicket jsapi ticket
		 * @param  string $nonceStr    string ( define by yourself , any str is ok)
		 * @param  string $url         request url (send by ajax, get from web browser)
		 * @return array               the signature str and other content
		 */
		public function signature($jsapiTicket, $nonceStr, $url)
		{
			$timestamp = time();
			$signatureStr = sprintf("jsapi_ticket=%s&noncestr=%s&timestamp=%s&url=%s", $jsapiTicket, $nonceStr, $timestamp, $url);
			$signature = sha1($signatureStr);
			$result = array();
			$result['signature'] = $signature;
			$result['timestamp'] = $timestamp;
			$result['jsapi_ticket'] = $jsapiTicket;
			$result['url'] = $url;
			$result['noncestr'] = $nonceStr;
			return $result;
		}

		/**
		 * [miniprogramQRCode description]
		 * @param  [type] $accessToken [description]
		 * @param  [type] $scene       [description]
		 * @param  [type] $page        [description]
		 * @param  [type] $width       [description]
		 * @return [type]              [description]
		 */
		public static function miniprogramQRCode($accessToken, $scene, $page, $width)
		{
			global $WxConfig;
			$QRCodeUrl = $WxConfig['miniprogramQRCodeUrl'];

			$data = array(
		        "scene"=>$scene,
		        "page"=>$page,
		        "width"=>$width
		    );
		    $url = $QRCodeUrl.$accessToken;
		    $res = self::HttpsRequest($url,json_encode($data));
		    return $res;
		}

		/**
		 * create button for wechat public account
		 * @param  string $buttonJson json format string
		 * @param  string $globalAccessToken access token
		 * @return json             json encode result
		 */
		public function createButton($buttonJson, $globalAccessToken)
		{
			global $WxConfig;
			$result = self::HttpsRequest($WxConfig['createButtonUrl'].$globalAccessToken, $buttonJson);
			return $result;
		}

		/*
		* 请确保您的libcurl版本是否支持双向认证，版本高于7.20.1
		* 使用微信商户证书
		*/
		public static function curl_post_ssl($url, $vars, $second=30,$aHeader=array())
		{
			$ch = curl_init();
			//超时时间
			curl_setopt($ch,CURLOPT_TIMEOUT,$second);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
			//这里设置代理，如果有的话
			//curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');
			//curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
			curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
			
			//以下两种方式需选择一种
			
			//第一种方法，cert 与 key 分别属于两个.pem文件
			//默认格式为PEM，可以注释
			curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLCERT,dirname(__FILE__).'/cert/apiclient_cert.pem');
			//默认格式为PEM，可以注释
			curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
			curl_setopt($ch,CURLOPT_SSLKEY,dirname(__FILE__).'/cert/apiclient_key.pem');

			curl_setopt($ch, CURLOPT_CAINFO,dirname(__FILE__).'/cert'.DIRECTORY_SEPARATOR.'rootca.pem');

			curl_setopt($ch, CURLOPT_SSLCERTPASSWD, '1497821432');
			
			//第二种方式，两个文件合成一个.pem文件
			// curl_setopt($ch,CURLOPT_SSLCERT,getcwd().'/all.pem');
		 
			if( count($aHeader) >= 1 ){
				curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
			}
		 
			curl_setopt($ch,CURLOPT_POST, 1);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
			$data = curl_exec($ch);
			if($data){
				curl_close($ch);
				return $data;
			}
			else { 
				$error = curl_errno($ch);
				echo "call faild, errorCode:$error\n"; 
				curl_close($ch);
				return false;
			}
		}

		/**
		 * send http request to url
		 * @param string $url  request url
		 * @param string $data send data
		 */
		private static function HttpsRequest($url, $data = null) {
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
			if (!empty($data)) {
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			}
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($curl);
			curl_close($curl);
			return $output;
		}
	}
 ?>