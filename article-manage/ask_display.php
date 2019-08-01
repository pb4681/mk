<?php
require 'core/db.class.php';
require 'config/config.php';

if(isset($_SESSION['usercode'])&&$_SESSION['usercode']==='true'){
    if($_SESSION['authority']===1||$_SESSION['authority']===3){
        include 'templets/default/ask_display.html';
    }else{
        header('Content-Type:text/html;charset=utf-8');
        exit('权限不足！');
    }
}else{
    header('Location:login.php');
    exit;
}