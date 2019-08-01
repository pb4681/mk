<?php
require 'config/config.php';

if(isset($_SESSION['usercode'])&&$_SESSION['usercode']==='true'){
    include 'templets/default/query_order.html';
}else{
    header('Location:login.php');
    exit;
}