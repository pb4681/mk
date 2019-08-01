<?php
require '../core/db.class.php';
require '../config/config.php';
require '../common/function_common.php';
header('Content-Type:text/html; charset=UTF-8');
$db = mysql::db();

if(isset($_SESSION['usercode'])&&$_SESSION['usercode']==='true'){
    if($_SESSION['authority']!==1){
        exit('权限不足');
    }
}else{
    exit('非法操作！');
}

if(isset($_POST['fakeid'])&&isset($_POST['industry'])&&isset($_POST['aid'])&&isset($_POST['title'])&&isset($_POST['digest'])&&isset($_POST['datetime'])&&isset($_POST['content_url'])&&isset($_POST['offset'])){
    $fakeid = inject_check($_POST['fakeid']);
    $industry = inject_check($_POST['industry']);
    $aid = inject_check($_POST['aid']);
    $title = inject_check($_POST['title']);
    $digest = inject_check($_POST['digest']);
    $datetime = inject_check($_POST['datetime']);
    $url = insertToStr($_POST['content_url'],4,'s');
    $offset = inject_check($_POST['offset']);
    
    $cur_day = strtotime(date("Y-m-d"),time());
    $next_day = strtotime(date("Y-m-d",strtotime("+1 day")));

    $res = $db->query("SELECT count(id) FROM article WHERE inserttime between $cur_day AND $next_day AND industryid=$industry");
    if($row=mysql_fetch_row($res)){
        $rowCount = $row[0];                           //获取记录总条数
    }
    if($industry==='1001'){
        if($rowCount>=1000){
            exit('{"status":"warning","msg":"articles today enough","code":300}');
        }
    }else{
        if($rowCount>=200){
            exit('{"status":"warning","msg":"articles today enough","code":300}');
        }
    }

    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_URL, $url);//爬取网站的地址
    curl_setopt($ch, CURLOPT_HEADER, 0);//响应头 0为false  1为true 以下均是
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
    "Accept-Language: zh-CN,zh;q=0.9",
    "Accept-Encoding: gzip, deflate, br",
    "Upgrade-Insecure-Requests: 1",
    "Connection: keep-alive",
    "Pragma:no-cache",
    "Cache-Control:no-cache",
    "Host:mp.weixin.qq.com",
    "User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36"));
    curl_setopt($ch, CURLOPT_ENCODING, "gzip");//关键步骤，解压http的gzip格式
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//返回值
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);//跳过https验证
    $out = curl_exec($ch);//执行并获取结果
    curl_close($ch);//释放curl连接
    $arr_dom = array("/<span ([\s\S]*?)>/i","/<span>/i","/<\/span>/i","/<strong ([\s\S]*?)>/i","/<strong>/i","/<\/strong>/i",
                     "/<em ([\s\S]*?)>/i","/<em>/i","/<\/em>/i","/<i ([\s\S]*?)>/i","/<i>/i","/<\/i>/i","/<br>/i","/<br \/>/i",
                     "/<hr ([\s\S]*?)>/i","/<hr>/i","/<a ([\s\S]*?)>/i","/<a>/i","/<\/a>/i");    //过滤标签
    $temp_str = get_content('/<div class="rich_media_content " id="js_content">([\s\S]*?)<script nonce=/i',$out,true);
    foreach($arr_dom as $v){
        $temp_str = preg_replace($v, '', $temp_str);
    }    
    $temp_arr = get_content('/(?:>)([\s\S]*?)(?:<)/u',$temp_str,false);
    $content = array();
    $content_str = '';
    foreach($temp_arr as $v){
        if(preg_match('/[\x{4e00}-\x{9fa5}]/u', $v[1])>0){
            if(sstrlen($v[1],'utf-8')<40){
                continue;
            }else{  
                array_push($content,'<p>'.$v[1].'</p>');
                $content_str .= $v[1];
            }
        }
    }

    if(array_key_exists(2,$content)){
        $digest = preg_replace('/<p>/i','',$content[2]);
        $digest = preg_replace('/<\/p>/i','',$digest);
    }

    $content = join('',$content);     //文章主体内容格式化
    $content = addslashes($content);  //引号处理

    $p_content = substr_count($content,'<p>');    //总段落数
    $w_content = sstrlen($content_str,'utf-8');   //总字数

    if($p_content>5 && $w_content>500){
        $content = preg_replace('/(1[3,4,5,6,7,8,9]\d{9})|(1[3,4,5,6,7,8,9]\d-\d{4}-\d{4})/','137-6165-8093',$content);   //替换手机号
        $content = preg_replace('/www.[a-z0-9]+.[a-z0-9]+/i','www.pinyuan.cc/',$content);    //替换网址
        $inserttime = time();
        $result = $db->query("INSERT INTO article (industryid, fakeid, aid, title, digest, content, datetime, inserttime, isupload, offset) 
                            VALUES ($industry, '$fakeid', $aid, '$title', '$digest', '$content', $datetime, $inserttime, 1, $offset)");
        echo '{"status":"success","msg":"article insert success","code":200}';
    }else{
        echo '{"status":"fail","msg":"article insert fail","code":200}';
    }
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":100}');
}

