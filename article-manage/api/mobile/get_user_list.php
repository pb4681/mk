<?php
require '../../core/db.class.php';
require '../../config/config.php';
require '../../common/function_common.php';
header('Content-Type:application/json; charset=UTF-8');
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers:content-type');
$db = mysql::db();
require './common/safe.php';

$res = $db->query("SELECT id,telephone,nickname,img FROM admin WHERE authority=3");
$arr = array();
while($row = mysql_fetch_assoc($res))
{
    array_push($arr,array(
        'id'=>$row['id'],
        'mobile'=>$row['telephone'],
        'img'=>$row['img'],
        'nickname'=>$row['nickname']
    ));
}
echo json_encode(array(
    'code'=>102,
    'data'=>$arr,
    'msg'=>'get user list success'
));