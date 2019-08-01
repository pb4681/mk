<?php
require '../common/function_common.php';

//获得参数 signature nonce token timestamp echostr
$nonce     = $_GET['nonce'];
$token     = '689ce2b0339a0782dbd7f8142332c355';
$timestamp = $_GET['timestamp'];
$echostr   = $_GET['echostr'];
$signature = $_GET['signature'];
//形成数组，然后按字典序排序
$array = array();
$array = array($nonce, $timestamp, $token);
sort($array);
//拼接成字符串,sha1加密 ，然后与signature进行校验
$str = sha1( implode( $array ) );
if( $str == $signature && $echostr ){   
    //第一次接入weixin api接口的时候
    echo  $echostr;
    exit;
}else{
    $xml = file_get_contents('php://input');
    if($xml!=''){
        $value_array = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $userid = $value_array['FromUserName'];
        $content = $value_array['Content'];
        $status = $value_array['Status'];
        $fp = fopen('user.txt','w');
        fwrite($fp,$xml);
        fclose($fp);
        if($status!=='success'){
                $data = request_post('http://it.mkai8.com/article-manage/api/mobile/application.php',array(
                    'userid'=>$userid,
                    'content'=>$content
                )
            );
        }
    }
}

