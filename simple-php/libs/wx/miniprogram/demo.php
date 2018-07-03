<?php

require_once dirname(__FILE__)."/wxBizDataCrypt.php";


$appid = 'wx584988c195739161';
$sessionKey = "xPOsHDXuj909KAqSTypd+g==";

$encryptedData="c6Ylz67Yj00CNr+E+DlqWpOi/5fYHZO4bZjsU6RzkrhdTfzGH6lqxF1iKSKFAmChMk9nyEumAcvnGESXQ9fmlAja+QNpMT+sX6i4U2Mp9JMVV1fvYMAXw7Fd1yyvcCAP89Y6WjS6jH7jzmLv9H+AQA==";

$iv = "/t29pYS3jb8FoVt5oPG+Tw==";

$pc = new WXBizDataCrypt($appid, $sessionKey);
$errCode = $pc->decryptData($encryptedData, $iv, $data );

if ($errCode == 0) {
    print($data . "\n");
} else {
    print($errCode . "\n");
}
