<?php
/* *
 * func：Chuang lan sms DEMO
 * version：1.3
 * date：2016-10-28
 * comment：
 * this is a demo for send sms, you can reconstruct the code by your requirement
 */
require_once dirname(__FILE__).'/ChuanglanSmsApi.class.php';

/**
 * send sms to phone
 * @param  string $phone_number the phone number
 * @param  string $msg          the sms content
 * @return boolean              success: true, fail: false
 */
function sendSMS($phone_number, $msg)
{
	$clapi  = new ChuanglanSmsApi();
	date_default_timezone_set('Asia/Shanghai');
	$time = date('Y-m-d H:i:s');
	$result = $clapi->sendSMS($phone_number, $msg.' time:'.$time,'true');
	$result = $clapi->execResult($result);
	if($result[1]==0){
		return true;
	}else{
		return false;
	}
}

/**
 * get the sms balance
 * @return integer sms account balance
 */
function getSMSBalance()
{
	$clapi  = new ChuanglanSmsApi();
	return $clapi->queryBalance();
}
?>
