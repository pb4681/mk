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

if(isset($_POST['id'])&&isset($_POST['title'])&&isset($_POST['keyword'])&&isset($_POST['digest'])&&isset($_POST['content'])){
    $id = inject_check($_POST['id']);                 //文章ID
    $title = inject_check($_POST['title']);           //标题
    $keyword = inject_check($_POST['keyword']);       //关键词
    $digest = inject_check($_POST['digest']);         //摘要
    $content = inject_check($_POST['content']);       //内容

    $db->query("UPDATE article SET title='$title',keyword='$keyword',digest='$digest',content='$content' WHERE id=$id");
    echo '{"status":"success","msg":"article edit success","code":206}';
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":100}');
}