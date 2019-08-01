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
    $res = $db->query("DELETE FROM admin WHERE id=$id");
    
    echo json_encode(array(
        'code'=>108,
        'status'=>'success',
        'msg'=>'delete user success'
    ));
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":100}');
}