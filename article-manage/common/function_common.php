<?php
/*获得客户端真实的IP地址*/
function getip() {
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
        $ip = getenv("HTTP_CLIENT_IP");
    } else
        if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } else
            if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
                $ip = getenv("REMOTE_ADDR");
            } else
                if (isset ($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")) {
                    $ip = $_SERVER['REMOTE_ADDR'];
                } else {
                    $ip = "unknown";
                }
    return ($ip);
}
/*防注入*/
function inject_check($value)
{
    // 去除斜杠
    if (get_magic_quotes_gpc())
    {
    $value = stripslashes(htmlspecialchars($value));
    }
    if (!is_numeric($value))
    {
    $value = mysql_real_escape_string(htmlspecialchars($value));
    }
    return $value;
}
/**
 * 正则匹配
 * @param  $reg    正则表达式
 * @param  $str    原字符串
 * @param  $bool   布尔值
 * @return string  处理后的字符串或数组
 */
function get_content($reg,$str,$bool)
{
$result = array();
preg_match_all($reg, $str, $result, PREG_SET_ORDER);
if($bool){
    return $result[0][0];
}
return $result;
}
/**
 * 指定位置插入字符串
 * @param $str  原字符串
 * @param $i    插入位置
 * @param $substr 插入字符串
 * @return string 处理后的字符串
 */
function insertToStr($str, $i, $substr){
    //指定插入位置前的字符串
    $startstr="";
    for($j=0; $j<$i; $j++){
        $startstr .= $str[$j];
    }
    //指定插入位置后的字符串
    $laststr="";
    for ($j=$i; $j<strlen($str); $j++){
        $laststr .= $str[$j];
    }
    //将插入位置前，要插入的，插入位置后三个字符串拼接起来
    $str = $startstr . $substr . $laststr;
    //返回结果
    return $str;
}
// 转义><
function html_replace($str){
    $str = str_replace('amp;','',$str);
    $str = str_replace('&lt;','<',$str);
    $str = str_replace('&gt;','>',$str);
    $str = str_replace('&quot;','"',$str);
    $str = str_replace('\n','<br />',$str);
    return $str;
}
/**
 * 模拟post进行url请求
 * @param string $url
 * @param array $post_data
 */
function request_post($url = '', $post_data = array()) {
    if (empty($url) || empty($post_data)) {
        return false;
    }
    
    $o = "";
    foreach ( $post_data as $k => $v ) 
    { 
        $o.= "$k=" . urlencode( $v ). "&" ;
    }
    $post_data = substr($o,0,-1);

    $postUrl = $url;
    $curlPost = $post_data;
    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($ch);//运行curl
    curl_close($ch);
    
    return $data;
}
/**
 * 统计字符串长度
 * @param string $str 要检测的字符串
 * @param string $charset 编码格式
 */
 function sstrlen($str,$charset) {
    $n = 0; $p = 0; $c = '';
    $len = strlen($str);
    if($charset == 'utf-8') {
      for($i = 0; $i < $len; $i++) {
        $c = ord($str{$i});
        if($c > 252) {
          $p = 5;
        } elseif($c > 248) {
          $p = 4;
        } elseif($c > 240) {
          $p = 3;
        } elseif($c > 224) {
          $p = 2;
        } elseif($c > 192) {
          $p = 1;
        } else {
          $p = 0;
        }
        $i+=$p;$n++;
      }
    } else {
      for($i = 0; $i < $len; $i++) {
        $c = ord($str{$i});
        if($c > 127) {
          $p = 1;
        } else {
          $p = 0;
      }
        $i+=$p;$n++;
      }
    }
    return $n;
}

