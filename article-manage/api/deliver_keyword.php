<?php
require '../core/db.class.php';
require '../config/config.php';
require '../common/function_common.php';
header('Content-Type:application/json; charset=UTF-8');
$db = mysql::db();

if(isset($_SESSION['usercode'])&&$_SESSION['usercode']==='true'){
    if($_SESSION['authority']!==1 && $_SESSION['authority']!==2){
        exit('权限不足');
    }
}else{
    exit('非法操作！');
}

if(isset($_POST['id'])&&isset($_POST['cc'])&&isset($_POST['other'])){
    $id = inject_check($_POST['id']);  
    $cc = intval(inject_check($_POST['cc']));  
    $other = intval(inject_check($_POST['other']));                

    $a = array(1002,1003,1004,1005,1006,1007,1008,1009,1010,1011,1012,1013,1014,1015);
    $cur_day = strtotime(date("Y-m-d"),time());
    $before_day = strtotime(date("Y-m-d",strtotime("-365 day")));
    $time = time();

    $db->query("UPDATE keyword SET deliverid=$id,delivertime=$time WHERE (delivertime between $before_day AND $cur_day OR delivertime=0) AND isupload=1 AND siteid=1001 LIMIT $cc");
    foreach($a as $v){
        $db->query("UPDATE keyword SET deliverid=$id,delivertime=$time WHERE (delivertime between $before_day AND $cur_day OR delivertime=0) AND isupload=1 AND siteid=$v LIMIT $other");
    }

    echo '{"status":"success","msg":"article deliver success","code":212}';
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":100}');
}