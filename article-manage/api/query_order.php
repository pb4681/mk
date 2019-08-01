<?php
header('Content-Type:text/html;charset=utf-8');
$wd = $_GET['keyword'];
$pagenum = $_GET['pagenum'];

$url = 'https://www.baidu.com/s';
echo '<script src="https://cdn.bootcss.com/jquery/3.4.0/jquery.min.js"></script>';
echo '<script>$("html").css("display","none")</script>';
echo '<div id="mk_container"></div>';
for($i=0;$i<$pagenum*10;$i+=10){
    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_URL, $url.'?wd='.$wd.'&pn='.$i.'&oq='.$wd.'&ie=uft-8&rsv_idx=1&rsv_pq=&rsv_t=');//爬取网站的地址
    curl_setopt($ch, CURLOPT_HEADER, 0);//响应头 0为false  1为true 以下均是
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Host: www.baidu.com",
    "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3",
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
    echo '<div id="mk_wrapper-'.($i/10+1).'">'.$out.'</div>';//输出结果
    echo '<div id="mk_container-'.($i/10+1).'"></div>';
    if(substr_count($out,'"label":"上海品源家具制造有限公司"')){
        echo "<script>
            $(function(){
                $('.wrapper_s,.wrapper_l').css('display','none');
                $('#mk_wrapper-".($i/10+1)." .c-container').each(function(){
                    if($(this).find('.f13 a span').text()==='品源家具'){
                        $('#mk_container-".($i/10+1)."').append('<div class=\"page\">页码：".($i/10+1)."</div>'
                            +'<div class=\"title\">标题：'+$(this).find('.t a').text()+'</div>'
                            +'<div class=\"order\">排名：'+($(this).index()+1)+'</div><hr />');
                    }
                });
            });
          </script>";
    }
}
echo "<script>
        $(function(){
            $('html').css('display','block');
        });
    </script>";
