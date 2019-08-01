<?php
if(isset($_POST['token'])||isset($_GET['token'])){
    $token = isset($_POST['token']) ? inject_check($_POST['token']) : inject_check($_GET['token']);
    $res = $db->query("SELECT id FROM admin where wxopenid='$token'");
    $row = mysql_fetch_assoc($res);
    if(!$row||empty($token)){
        exit('{"status":"error","msg":"token error","code":002}');
    }
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":001}');
}