<?php
require '../../core/db.class.php';
require '../../common/function_common.php';
$db = mysql::db();
/**
 * 将模板消息json格式化
 */
function json_tempalte($openid,$templet_id,$detail_data=array(),$new_url){
    //模板消息
    $template=array(
        'touser' => $openid,  //管理员openid
        'template_id' => $templet_id, //在公众号下配置的模板id
        'url' => $new_url, //跳转链接
        'data'=> $detail_data
    );
    $json_template=json_encode($template);
    return $json_template;
}
/**
 * @param $url
 * @param array $data
 * @return mixed
 * curl请求
 */
function curl_post($url , $data=array()){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    // POST数据
    curl_setopt($ch, CURLOPT_POST, 1);
    // 把post的变量加上
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}


if(isset($_POST['userid'])&&isset($_POST['content'])){
    $userid = $_POST['userid'];
    $content = $_POST['content'];
    $res0 = $db->query("SELECT id FROM admin WHERE wxopenid='$userid'");
    $res1 = $db->query("SELECT id FROM application WHERE wxopenid='$userid'");
    $row0 = mysql_fetch_assoc($res0);
    $row1 = mysql_fetch_assoc($res1);
    if(!$row0 && !$row1){
        if(preg_match("/^品源([\x{4e00}-\x{9fa5}]{2,4})(1[3,4,5,6,7,8,9]\d{9})$/u",$content)){
            $content_temp = str_replace('品源','',$content);
            $name = preg_replace("/[0-9]*/",'',$content_temp);

            $res3 = $db->query("SELECT id FROM admin WHERE nickname='$name' AND authority=3");
            $row3 = mysql_fetch_assoc($res3);
            $time = date("Y-m-d H:i:s",time());
            if(!$row3){
                $mobile = preg_replace("/[\x{4e00}-\x{9fa5}]*/u",'',$content_temp);
                // 微信通知
                $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx37a75b8fb49a9910&secret=c6dc096467862befee66ffc0fbb3e4db';
                $html = file_get_contents($url);
                $html = json_decode($html,true);
                $access_token = $html['access_token'];  //获取access_token
        
                $userinfo = json_decode(file_get_contents('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$userid.'&lang=zh_CN'),true);
                $imgurl = $userinfo['headimgurl'];      //获取headimgurl
                
                $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
                $detail_data = array(
                    'first'=>array('value'=>urlencode('员工加入申请')),  
                    'keyword1'=>array('value'=>urlencode($name),'color'=>'#173177'),
                    'keyword2'=>array('value'=>urlencode($time),'color'=>'#173177'),
                );
                $json_template = json_tempalte('ovrxsxNdQksOXEX4tPa82fH6c7bk','-fwpCBRwrEL4--W0AKmg1beJfB39Ist7B7fFBz4de_o',$detail_data,'http://it.mkai8.com/mk_admin/#/manage?token=ovrxsxNdQksOXEX4tPa82fH6c7bk');
                $res = curl_post($url,urldecode($json_template));
                $res2 = $db->query("INSERT INTO application (telephone,wxopenid,nickname,img) VALUE ('$mobile','$userid','$name','$imgurl')");
            }else{
                // 微信通知
                $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx37a75b8fb49a9910&secret=c6dc096467862befee66ffc0fbb3e4db';
                $html = file_get_contents($url);
                $html = json_decode($html,true);
                $access_token = $html['access_token'];  //获取access_token

                $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
                $detail_data = array(
                    'first'=>array('value'=>urlencode('认证状态提醒')),  
                    'keyword1'=>array('value'=>urlencode($time),'color'=>'#173177'),
                    'keyword2'=>array('value'=>urlencode('员工加入认证失败'),'color'=>'#173177'),
                    'keyword3'=>array('value'=>urlencode('姓名已存在'),'color'=>'#173177'),
                );
                $json_template = json_tempalte($userid,'be_6Zg65q7jZtv7DySXTa66XRtCJ19pN8nCz_Skj8yA',$detail_data);
                $res = curl_post($url,urldecode($json_template));
            }
        }                           
    }
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":001}');
}