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

if(isset($_POST['type'])&&isset($_POST['aid'])&&isset($_POST['title'])&&isset($_POST['keyword'])&&isset($_POST['imglit'])&&isset($_POST['description'])&&isset($_POST['siteid'])&&isset($_POST['site'])&&isset($_POST['cat'])&&isset($_POST['catid'])&&isset($_POST['content'])){
    $type = inject_check($_POST['type']);                     //操作类型
    $aid = inject_check($_POST['aid']);                       //文章ID
    $title = inject_check($_POST['title']);                   //标题
    $keyword = inject_check($_POST['keyword']);               //关键词
    $imglit = inject_check($_POST['imglit']);                 //缩略图url
    $description = inject_check($_POST['description']);       //描述
    $siteid = inject_check($_POST['siteid']);                 //网站ID   
    $site = inject_check($_POST['site']);                     //网站
    $cat = inject_check($_POST['cat']);                       //栏目
    $catid = inject_check($_POST['catid']);                   //栏目ID
    $content = inject_check($_POST['content']);               //产品内容
    $content = html_replace($content);
    $userid = $_SESSION['id'];
    $save_time = time();

    $aid = empty($aid) ? 0 : $aid;
    $catid = empty($catid) ? 0 : $catid;

    if($type === 'add'){
        $db->query("INSERT INTO product (aid,title,keyword,description,imglit,isupload,siteid,site,catid,cat,content,savetime,userid) 
        VALUES ($aid,'$title','$keyword','$description','$imglit',1,$siteid,'$site',$catid,'$cat','$content',$save_time,$userid)");
    }else if($type === 'edit'){
        $id = inject_check($_POST['id']);                     //系统ID
        $db->query("UPDATE product SET aid=$aid,title='$title',keyword='$keyword',description='$description',imglit='$imglit',siteid=$siteid,
        site='$site',catid=$catid,cat='$cat',content='$content',savetime=$save_time,userid=$userid WHERE id=$id");
    }else{
        exit('{"status":"error","msg":"type error","code":111}');
    }
    echo '{"status":"success","msg":"product save success","code":217}';
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":100}');
}