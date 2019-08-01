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

if(isset($_POST['id'])&&isset($_POST['title'])&&isset($_POST['keyword'])&&isset($_POST['digest'])&&isset($_POST['content'])&&isset($_POST['siteid'])&&isset($_POST['column'])&&isset($_POST['writer'])&&isset($_POST['source'])&&isset($_POST['isadd'])){
    $id = inject_check($_POST['id']);                         //文章ID
    $title = inject_check($_POST['title']);                   //标题
    $keyword = inject_check($_POST['keyword']);               //关键词
    $digest = inject_check($_POST['digest']);                 //摘要
    $content = inject_check($_POST['content']);               //内容
    $siteid = inject_check($_POST['siteid']);                 //网站ID
    $column = inject_check($_POST['column']);                 //分类
    $writer = inject_check($_POST['writer']);                 //作者
    $source = inject_check($_POST['source']);                 //来源
    $isadd = inject_check($_POST['isadd']);                   //是否添加结尾模板
    $userid = $_SESSION['id'];

    $litpic=array(
        '1001'=>112,'1002'=>112,'1003'=>72,'1004'=>47,'1005'=>47,'1006'=>53,'1007'=>55,
        '1008'=>64,'1009'=>99,'1010'=>49,'1011'=>39,'1012'=>28,'1013'=>49,'1014'=>51,'1015'=>20
    );
    $site_val = array(
        '1001'=>'cc','1002'=>'cc','1003'=>'china','1004'=>'children','1005'=>'school','1006'=>'hospital','1007'=>'library',
        '1008'=>'office','1009'=>'apartment','1010'=>'restaurant','1011'=>'hotel','1012'=>'shelf','1013'=>'zmshoo',
        '1014'=>'cleaner','1015'=>'water'
    );
    $site_des = array(
        '1001'=>array('start'=>'上海办公家具品源小编为您精选：'.$title.'。','end'=>''),
        '1002'=>array('start'=>'上海办公设备品源小编为您精选：'.$title.'。','end'=>''),
        '1003'=>array('start'=>'导语：'.$title.'。','end'=>'今天品源家具为大家介绍一下。'),
        '1004'=>array('start'=>'导语：'.$title.'。','end'=>'品源幼儿园家具为大家介绍一下。'),
        '1005'=>array('start'=>'导语：'.$title.'。','end'=>'今天品源学校家具给大家分享一下。'),
        '1006'=>array('start'=>'导语：'.$title.'。','end'=>'品源医用家具为大家介绍一下。'),
        '1007'=>array('start'=>'导语：'.$title.'。','end'=>'今天品源图书馆家具为大家介绍一下。'),
        '1008'=>array('start'=>'导语：'.$title.'。','end'=>'品源公装小编推荐：'.$title.'。'),
        '1009'=>array('start'=>'导语：'.$title.'。','end'=>'品源公寓家具带大家一起来看看吧。'),
        '1010'=>array('start'=>'导语：'.$title.'。','end'=>'今天品源餐厅家具就带大家了解一下。'),
        '1011'=>array('start'=>'导语：'.$title.'。','end'=>'今天品源酒店家具为大家介绍一下。'),
        '1012'=>array('start'=>'导语：'.$title.'。','end'=>'今天品源货架带大家了解一下。'),
        '1013'=>array('start'=>'导语：'.$title.'。','end'=>'下面智美优品为大家介绍一下。'),
        '1014'=>array('start'=>'导语：'.$title.'。','end'=>'下面智美优品新风系统带大家了解一下。'),
        '1015'=>array('start'=>'导语：'.$title.'。','end'=>'今天智美净水就带大家了解一下。'),
    );
    $site_kw = array(
        '1001'=>'','1002'=>'','1003'=>',上海家具厂','1004'=>',幼儿园家具','1005'=>',学校家具','1006'=>',医院家具','1007'=>',图书馆家具',
        '1008'=>',办公室装修','1009'=>',公寓家具','1010'=>',餐厅家具','1011'=>',酒店家具','1012'=>',上海货架厂','1013'=>',空气能热水器',
        '1014'=>',新风净化系统','1015'=>',净水器'
    );
 
    $pic_len = $litpic[$siteid];
    $pic_index = mt_rand(1,$pic_len);

    if($isadd==='是'){
        $res = $db->query("SELECT * FROM site_cat WHERE siteid=$siteid");
        $row = mysql_fetch_assoc($res);
        $templet = $row['templet'];
    }else{
        $templet = '';
    }

    $upload_time = time();
    $keyword = $keyword.$site_kw[$siteid];
    $digest = $site_des[$siteid]['start'].$digest.$site_des[$siteid]['end'];
    $reg = "/<p ([\s\S]*?)>/i";
    $reg1 = "/<span ([\s\S]*?)>/i";
    $content = preg_replace($reg, "<p>", html_replace($content));
    $content = preg_replace($reg1, "<span>", $content);

    if($siteid!=='1001'&&$siteid!=='1003'){
        $content = '<p>'.$digest.'</p>'.$content;
    }

    if($siteid!=='1013'){
        $data = request_post('http://it.mkai8.com/article-auto/api_manage.php',array(
                'site'=>$siteid,
                'litpic'=>'/uploads/new_module/images/'.$site_val[$siteid].'/'.$pic_index.'.jpg',
                'writer'=>$writer,
                'source'=>$source,
                'column'=>$column,
                'title'=>$title,
                'keyword'=>$keyword,
                'desc'=>$digest,
                'content'=>str_replace('</p><p>','</p><p>&nbsp;</p><p>',$content).'<p>&nbsp;</p>'.$templet
            )
        );
    }else{
        $data = request_post('http://www.zmshoo.com/new_module/api/api_manage.php',array(
                'site'=>$siteid,
                'litpic'=>'uploads/new_module/images/'.$site_val[$siteid].'/'.$pic_index.'.jpg',
                'writer'=>$writer,
                'column'=>$column,
                'title'=>$title,
                'keyword'=>$keyword,
                'desc'=>$digest,
                'content'=>str_replace('</p><p>','</p><p>&nbsp;</p><p>',$content).'<p>&nbsp;</p>'.$templet
            )
        );
    }
    $data = json_decode($data);
    if($data->code===207){
        $db->query("UPDATE article SET title='$title',keyword='$keyword',digest='$digest',content='$content',uploadtime=$upload_time,isupload=2,userid=$userid WHERE id=$id");
        echo '{"status":"success","msg":"article upload success","code":207}';
    }else{
        echo '{"status":"error","msg":"article upload fail","code":104}';
    }
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":100}');
}