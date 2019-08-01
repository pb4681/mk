<?php
require '../core/db.class.php';
require '../config/config.php';
require '../common/function_common.php';
header('Content-Type:application/json; charset=UTF-8');
$db = mysql::db();

if(isset($_SESSION['usercode'])&&$_SESSION['usercode']==='true'){
    if($_SESSION['authority']!==2&&$_SESSION['authority']!==1){
        exit('权限不足');
    }
}else{
    exit('非法操作！');
}

if(isset($_POST['data_json'])){
    $templet_json = json_decode($_POST['data_json'], true);
    if(is_array($templet_json)){
        foreach($templet_json as $k=>$v){
            $v = str_replace('div','p',$v);
            $db->query("UPDATE site_cat SET templet='$v' WHERE siteid=$k");
        }
        echo '{"status":"success","msg":"update templet success","code":203}';
    }else{
        exit('{"status":"error","msg":"missing parameters or error","code":100}');
    }
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":100}');
}