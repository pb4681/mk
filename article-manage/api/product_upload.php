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

if(isset($_POST['aid'])&&isset($_POST['title'])&&isset($_POST['keyword'])&&isset($_POST['imglit'])&&isset($_POST['description'])&&isset($_POST['siteid'])&&isset($_POST['site'])&&isset($_POST['catid'])&&isset($_POST['cat'])&&isset($_POST['content'])){
    $aid = inject_check($_POST['aid']);                       //文章ID
    $title = inject_check($_POST['title']);                   //标题
    $keyword = inject_check($_POST['keyword']);               //关键词
    $imglit = inject_check($_POST['imglit']);                 //缩略图url   
    $description = inject_check($_POST['description']);       //描述
    $siteid = inject_check($_POST['siteid']);                 //网站ID
    $site = inject_check($_POST['site']);                     //网站名称
    $catid = inject_check($_POST['catid']);                   //栏目ID
    $cat = inject_check($_POST['cat']);                       //栏目名称   
    $content = inject_check($_POST['content']);               //产品内容
    $userid = $_SESSION['id'];
    $username = $_SESSION['username'];
    $upload_time = time();

    if($siteid === '1001'){
        $imglit1 = 'http://image.pinyuan.cc'.$imglit;
    }

    $catid = empty($aid) ? $catid : 0;
    $content = html_replace($content);

    if($siteid!=='1013'){
        $data = request_post('http://it.mkai8.com/article-auto/api_pro_manage.php',array(
                'id'=>$aid,
                'title'=>$title,
                'keyword'=>$keyword,
                'litpic'=>$imglit1,
                'desc'=>$description,
                'site'=>$siteid,
                'column'=>$catid,
                'writer'=>$username,
                'content'=>$content
            )
        );
    }
    $data = json_decode($data);
    if($data->code===216){
        if(isset($_POST['id'])){
            $id = inject_check($_POST['id']);
            $db->query("UPDATE product SET aid=$data->data,title='$title',keyword='$keyword',description='$description',imglit='$imglit',isupload=2,siteid=$siteid,site='$site',catid=$catid,cat='$cat',content='$content',savetime=$upload_time,uploadtime=$upload_time,userid=$userid WHERE id=$id");
        }else{
            $db->query("INSERT INTO product (aid,title,keyword,description,imglit,isupload,siteid,site,catid,cat,content,savetime,uploadtime,userid) 
            VALUES ($data->data,'$title','$keyword','$description','$imglit',2,$siteid,'$site',$catid,'$cat','$content',$upload_time,$upload_time,$userid)");
        }
        echo '{"status":"success","msg":"product upload success","code":216}';
    }else{
        echo '{"status":"error","msg":"product upload fail","code":105}';
    }
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":100}');
}