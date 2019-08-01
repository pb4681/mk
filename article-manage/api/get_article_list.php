<?php
require '../config/config.php';
header('Content-Type:text/html; charset=UTF-8');

if(isset($_SESSION['usercode'])&&$_SESSION['usercode']==='true'){
    if($_SESSION['authority']!==1){
        exit('权限不足');
    }
}else{
    exit('非法操作！');
}

$url = $_GET['url'];
$offset = $_GET['offset'];
$ch = curl_init();//初始化curl
curl_setopt($ch, CURLOPT_URL, $url.'&offset='.$offset);//爬取网站的地址
curl_setopt($ch, CURLOPT_HEADER, 0);//响应头 0为false  1为true 以下均是
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
"Host: mp.weixin.qq.com",
"Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8",
"Accept-Language: zh-CN,zh;q=0.9",
"Accept-Encoding: gzip, deflate, br",
"Connection: keep-alive",
"Pragma:no-cache",
"Cache-Control:no-cache",
"Upgrade-Insecure-Requests:1",
"User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36"));
curl_setopt($ch, CURLOPT_ENCODING, "gzip");//关键步骤，解压http的gzip格式
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//返回值
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);//跳过https验证
$out = curl_exec($ch);//执行并获取结果
curl_close($ch);//释放curl连接
echo $out;//输出结果