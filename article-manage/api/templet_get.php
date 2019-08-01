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

$res = $db->query("SELECT * FROM site_cat");
$arr = array();
while($row = mysql_fetch_assoc($res)){
    array_push($arr,array(
            'siteid'=>$row['siteid'],
            'templet'=>$row['templet']
        )
    );
}
echo json_encode(array(
        'status'=>'success',
        'data'=>$arr,
        'msg'=>'get templet success',
        'code'=>'204'
    )
);  