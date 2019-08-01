<?php
require '../../core/db.class.php';
require '../../config/config.php';
require '../../common/function_common.php';
header('Content-Type:application/json; charset=UTF-8');
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers:content-type');
$db = mysql::db();
require './common/safe.php';

if(isset($_GET['token'])){
    $wxopenid = inject_check($_GET['token']);
    $res = $db->query("SELECT telephone,wxopenid,nickname,img FROM admin WHERE wxopenid='$wxopenid'");
    $row = mysql_fetch_assoc($res);
    echo json_encode(array(
        'code'=>109,
        'data'=>array(
            'mobile'=>$row['telephone'],
            'wxopenid'=>$row['wxopenid'],
            'nickname'=>$row['nickname'],
            'img'=>$row['img'],
        ),
        'msg'=>'get me info success'
    ));
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":001}');
}