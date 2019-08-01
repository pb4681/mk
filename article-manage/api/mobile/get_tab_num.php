<?php
require '../../core/db.class.php';
require '../../config/config.php';
require '../../common/function_common.php';
header('Content-Type:application/json; charset=UTF-8');
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers:content-type');
$db = mysql::db();
require './common/safe.php';

$res = $db->query("SELECT count(id) FROM inquiry WHERE click=0");
$res1 = $db->query("SELECT count(id) FROM application WHERE click=0");
$res2 = $db->query("SELECT count(id) FROM inquiry WHERE feedback=1");
if($num=mysql_fetch_row($res)){
    $rowCount = $num[0];                           
}
if($num1=mysql_fetch_row($res1)){
    $rowCount1 = $num1[0];                           
}
if($num2=mysql_fetch_row($res2)){
    $rowCount2 = $num2[0];                           
}

echo json_encode(array(
    'code'=>104,
    'data'=>array('inquiry_count'=>intval($rowCount),'application_count'=>intval($rowCount1),'feedbacknum_count'=>intval($rowCount2)),
    'msg'=>'get tab number success'
));