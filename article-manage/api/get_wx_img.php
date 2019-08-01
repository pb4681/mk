<?php
require '../core/db.class.php';
require '../config/config.php';
require '../common/function_common.php';
header('Content-Type:application/json; charset=UTF-8');
$db = mysql::db();

if(isset($_SESSION['usercode'])&&$_SESSION['usercode']==='true'){
    if($_SESSION['authority']!==1){
        exit('权限不足');
    }
}else{
    exit('非法操作！');
}

if(isset($_GET['id'])){
    $id = inject_check($_GET['id']);        

    $res = $db->query("SELECT * FROM admin WHERE id=$id");
    $row = mysql_fetch_assoc($res);
    $wxopenid = $row['wxopenid'];

    $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx37a75b8fb49a9910&secret=c6dc096467862befee66ffc0fbb3e4db';
    $html = file_get_contents($url);
    $html = json_decode($html,true);
    $access_token = $html['access_token'];  //获取access_token

    $userinfo = json_decode(file_get_contents('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$wxopenid.'&lang=zh_CN'),true);
    $imgurl = $userinfo['headimgurl'];      //获取headimgurl

    $res1 = $db->query("UPDATE admin SET img='$imgurl' WHERE id=$id");

    echo json_encode(array(
        'code'=>230,
        'status'=>'success',
        'msg'=>'get wx img success'
    ));
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":001}');
}