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

if(isset($_POST['id'])){
    $id = inject_check($_POST['id']);        

    $db->query("DELETE FROM admin WHERE id=$id");
    echo '{"status":"success","msg":"user delete success","code":210}';
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":100}');
}