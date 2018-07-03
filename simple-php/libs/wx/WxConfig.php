<?php 
	$WxConfig = array(
		'appid' => 'wx_appid_get_from_document',
		'secret' => 'wx_secret_get_from_document',
		'mchId' => 'wx_mch_id',
		'mchKey' => 'wx_mch_key',

		'templateSendUrl' => 'https://api.weixin.qq.com/cgi-bin/message/template/send?',
		'createButtonUrl' => 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=',
		'prePayUrl' => 'https://api.mch.weixin.qq.com/pay/unifiedorder',
		'refundUrl' => 'https://api.mch.weixin.qq.com/secapi/pay/refund',
		'miniprogramTemplateSendUrl' => 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=',
		'payForOpenidUrl' => 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers',
		'miniprogramQRCodeUrl' => 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token='
	);




 ?>