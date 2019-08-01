<?php
require 'config/config.php';
require 'core/db.class.php';
$db = mysql::db();

if(isset($_SESSION['usercode'])&&$_SESSION['usercode']==='true'){
    $username = $_SESSION['username'];
    $nickname = $_SESSION['nickname'];
    $authority = intval($_SESSION['authority']);
    include 'templets/default/index.html';
}else{
    header('Location:login.php');
    exit;
}