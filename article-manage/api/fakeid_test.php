<?php
require '../core/db.class.php';
require '../config/config.php';
require '../common/function_common.php';
header('Content-Type:text/html; charset=UTF-8');
$db = mysql::db();

if(isset($_SESSION['usercode'])&&$_SESSION['usercode']==='true'){
    if($_SESSION['authority']!==1){
        exit('权限不足');
    }
}else{
    exit('非法操作！');
}

if(isset($_GET['fakeid64'])){
    $fakeid64 = inject_check($_GET['fakeid64']);
    $fakeid = base64_decode($fakeid64);

    $res = $db->query("SELECT id,offset FROM article WHERE fakeid='$fakeid' ORDER BY id DESC LIMIT 1");
    if($row=mysql_fetch_assoc($res)){
        $offset = $row['offset'];
        echo json_encode(array(
                'status'=>'success',
                'data'=>$offset,
                'msg'=>'fakeid is exist',
                'code'=>209
            )
        );
    }else{
        echo '{"status":"success","msg":"fakeid not exist","code":210}';
    }
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":100}');
}