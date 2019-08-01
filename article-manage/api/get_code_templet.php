<?php
require '../core/db.class.php';
require '../config/config.php';
require '../common/function_common.php';
header('Content-Type:application/json; charset=UTF-8');
$db = mysql::db();

if(isset($_SESSION['usercode'])&&$_SESSION['usercode']==='true'){
    if($_SESSION['authority']!==1&&$_SESSION['authority']!==2){
        exit('权限不足');
    }
}else{
    exit('非法操作！');
}

if(isset($_GET['siteid'])){
    $siteid = inject_check($_GET['siteid']);

    $res = $db->query("SELECT templet FROM product_templet WHERE siteid=$siteid");
    $row = mysql_fetch_assoc($res);
    echo json_encode(array(
            'status'=>'success',
            'data'=>html_replace($row['templet']),
            'msg'=>'code templet get success',
            'code'=>214
        )
    );
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":100}');
}