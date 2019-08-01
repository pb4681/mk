<?php
require 'core/db.class.php';
require 'config/config.php';
$db = mysql::db();

if(isset($_SESSION['usercode'])&&$_SESSION['usercode']==='true'){
    if($_SESSION['authority']===1){
        $res = $db->query("SELECT * FROM admin");
        $arr = array();
        while($row=mysql_fetch_assoc($res)){
            array_push($arr,array(
                    'id'=>$row['id'],
                    'username'=>$row['username'],
                    'nickname'=>$row['nickname'],
                    'imgurl'=>$row['img'],
                    'authority'=>$row['authority'],
                    'lastlogin'=>$row['lastlogin'],
                    'ip'=>$row['ip']
                )
            );
        }
        include 'templets/default/user_manage.html';
    }else{
        header('Content-Type:text/html;charset=utf-8');
        exit('权限不足！');
    }
}else{
    header('Location:login.php');
    exit;
}