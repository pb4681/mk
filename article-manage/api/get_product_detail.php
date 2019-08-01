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

if(isset($_GET['id'])){
    $id = inject_check($_GET['id']);
    $res = $db->query("SELECT * FROM product WHERE id=$id");
    $row = mysql_fetch_assoc($res);
    $arr = array(
        'aid'=>empty($row['aid']) ? '' : $row['aid'],
        'title'=>$row['title'],
        'keyword'=>$row['keyword'],
        'description'=>$row['description'],
        'imglit'=>$row['imglit'],
        'site'=>$row['site'],
        'siteid'=>$row['siteid'],
        'cat'=>$row['cat'],
        'catid'=>empty($row['catid']) ? '' : $row['catid'],
        'content'=>html_replace($row['content'])
    );
    echo json_encode(array(
            'status'=>'success',
            'data'=>$arr,
            'msg'=>'get product details success',
            'code'=>218
        )
    );
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":100}');
}
