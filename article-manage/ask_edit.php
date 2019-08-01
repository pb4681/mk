<?php
require 'core/db.class.php';
require 'config/config.php';

if(isset($_SESSION['usercode'])&&$_SESSION['usercode']==='true'){
    if($_SESSION['authority']===1||$_SESSION['authority']===3){
        if(isset($_GET['id'])){
            $db = mysql::db();
            $id = $_GET['id'];
            $res = $db->query("SELECT * FROM inquiry WHERE id=$id");
            $row = mysql_fetch_assoc($res);
        }
        include 'templets/default/ask_edit.html';
    }else{
        header('Content-Type:text/html;charset=utf-8');
        exit('权限不足！');
    }
}else{
    header('Location:login.php');
    exit;
}