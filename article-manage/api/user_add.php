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

if(isset($_GET['username'])&&isset($_GET['nickname'])&&isset($_GET['password'])&&isset($_GET['authority'])){
    $username = inject_check($_GET['username']);        
    $nickname = inject_check($_GET['nickname']);   
    $password = inject_check($_GET['password']);   
    $authority = inject_check($_GET['authority']); 

    $salt = substr(md5(mt_rand(1,10000)),0,6);
    $password = md5($password.md5($salt));

    $res1 = $db->query("INSERT INTO admin (username, password, salt, nickname, authority)
                       VALUE ('$username', '$password', '$salt', '$nickname', $authority)");
    $a = $db->insert_id();
    $res2 = $db->query("SELECT * FROM admin WHERE id=$a");
    $row2 = mysql_fetch_assoc($res2);

    echo json_encode(array(
            'status'=>'success',
            'data'=>array(
                'id'=>$row2['id'],
                'username'=>$row2['username'],
                'nickname'=>$row2['nickname'],
                'authority'=>$row2['authority']
            ),
            'msg'=>'user add success',
            'code'=>209
        )
    );
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":100}');
}