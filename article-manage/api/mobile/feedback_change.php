<?php
require '../../core/db.class.php';
require '../../config/config.php';
require '../../common/function_common.php';
header('Content-Type:application/json; charset=UTF-8');
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers:content-type');
$db = mysql::db();
require './common/safe.php';


if(isset($_POST['id'])){
    $id = inject_check($_POST['id']);
    $res = $db->query("UPDATE inquiry SET feedback=0 WHERE id=$id");
    echo json_encode(array(
        'code'=>110,
        'status'=>'success',
        'msg'=>'feedback change success'
    ));
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":001}');
}