<?php
require '../../core/db.class.php';
require '../../config/config.php';
require '../../common/function_common.php';
header('Content-Type:application/json; charset=UTF-8');
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers:content-type');
$db = mysql::db();
require './common/safe.php';

$res0 = $db->query("SELECT nickname FROM admin WHERE wxopenid='$token'");
$row0 = mysql_fetch_assoc($res0);
$user = $row0['nickname'];

$res = $db->query("SELECT count(id) FROM inquiry WHERE staffclick=0 AND deliveruser='$user'");
if($num=mysql_fetch_row($res)){
    $rowCount = $num[0];                           
}

echo json_encode(array(
    'code'=>104,
    'data'=>array('inquiry_count'=>intval($rowCount)),
    'msg'=>'get tab number success'
));