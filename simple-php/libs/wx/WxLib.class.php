<?php 
	require_once dirname(__FILE__).'/WxConfig.php';
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
		public function getOpenid($code, &$openid)
		{
			global $WxConfig;
			$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=". $WxConfig['appid'] ."&secret=". $WxConfig['secret'] ."&code=" . $code . "&grant_type=authorization_code";
			$data = json_decode(file_get_contents($url1), true);
			if (isset($data['errcode'])) {
				return false;
			}
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