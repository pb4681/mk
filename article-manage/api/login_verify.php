<?php
require '../core/db.class.php';
require '../config/config.php';
require '../common/function_common.php';
header('Content-Type:application/json; charset=UTF-8');
$db = mysql::db();

if(isset($_POST['username'])&&isset($_POST['password'])&&isset($_POST['code'])){
    $username = inject_check($_POST['username']); 
    $password = inject_check($_POST['password']);
    $code = $_POST['code'];
    if($code!==$_SESSION['idtcode']){
        exit('{"status":"error","msg":"valid_code is not correct","code":101}');
    }else{
        $res1 = $db->query("SELECT * FROM `admin` WHERE username='$username'");
        if($row1=mysql_fetch_assoc($res1)){
            $salt = md5($row1['salt']);
            $pwd = md5($password.$salt);
            $res2 = $db->query("SELECT * FROM `admin` WHERE username='$username' AND password='$pwd'");
            if($row2=mysql_fetch_assoc($res2)){
                $ip = getip();
                $lastlogin = time();
                $id = $row2['id'];
                $db->query("UPDATE `admin` SET lastlogin='$lastlogin',ip='$ip' WHERE username='$username'");
                $_SESSION['usercode'] = 'true';
                $_SESSION['username'] = $username;
                $_SESSION['nickname'] = $row2['nickname'];
                $_SESSION['id'] = $id;
                $_SESSION['authority'] = intval($row2['authority']);
                echo '{"status":"success","msg":"login success","code":203}';
            }else{
                exit('{"status":"error","msg":"password error","code":103}');
            }
        }else{
            exit('{"status":"error","msg":"username error","code":102}');
        }
    }
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":100}');
}