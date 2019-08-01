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

    echo json_encode(array(
            'status'=>'success',
            'data'=>array(
                'id'=>$row['id'],
                'username'=>$row['username'],
                'nickname'=>$row['nickname'],
                'imgurl'=>$row['img'],
                'authority'=>$row['authority']
            ),
            'msg'=>'get user info success',
            'code'=>211
        )
    );
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":100}');
}