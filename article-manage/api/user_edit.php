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

if(isset($_POST['id'])&&isset($_POST['username'])&&isset($_POST['nickname'])&&isset($_POST['password'])&&isset($_POST['authority'])){
    $id = inject_check($_POST['id']);   
    $username = inject_check($_POST['username']);        
    $nickname = inject_check($_POST['nickname']);   
    $password = inject_check($_POST['password']);   
    $authority = inject_check($_POST['authority']);  
    
    if(empty($password)){
        $db->query("UPDATE admin SET username='$username',nickname='$nickname',authority=$authority WHERE id=$id");
    }else{
        $res = $db->query("SELECT salt FROM admin WHERE id=$id");
        $row = mysql_fetch_assoc($res);
        $salt = $row['salt'];
        $password = md5($password.md5($salt));
        $db->query("UPDATE admin SET username='$username',nickname='$nickname',password='$password',authority=$authority WHERE id=$id");
    }
    
    echo '{"status":"success","msg":"user edit success","code":212}';
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":100}');
}