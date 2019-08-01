<?php
require '../../core/db.class.php';
require '../../config/config.php';
require '../../common/function_common.php';
header('Content-Type:application/json; charset=UTF-8');
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers:content-type');
$db = mysql::db();
require './common/safe.php';

/**
 * 将模板消息json格式化
 */
 function json_tempalte($openid,$detail_data=array(),$new_url){
    //模板消息
    $template=array(
        'touser' => $openid,  //员工openid
        'template_id' => 'TPZaUA5XTL0L-JxPN1U_HDQcNvyy3zeldBGfk40YeQ0', //在公众号下配置的模板id
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

if(isset($_POST['id'])){
    $id = inject_check($_POST['id']);
    $res = $db->query("UPDATE application SET isagree=1 WHERE id=$id");
    $res1 = $db->query("SELECT telephone,wxopenid,nickname,img FROM application WHERE id=$id");
    $row1 = mysql_fetch_assoc($res1);

    $mobile = $row1['telephone'];
    $wxopenid = $row1['wxopenid'];
    $nickname = $row1['nickname'];
    $img = $row1['img'];

    $res0 = $db->query("SELECT id FROM admin WHERE wxopenid='$wxopenid'");
    $row0 = mysql_fetch_assoc($res0);
    if(!$row0){
        $res2 = $db->query("INSERT INTO admin (telephone,wxopenid,nickname,img,authority) 
        VALUE ('$mobile','$wxopenid','$nickname','$img',3)");

        
        echo json_encode(array(
            'code'=>105,
            'data'=>array(
                'id'=>$db->insert_id(),
                'mobile'=>$mobile,
                'nickname'=>$nickname,
                'img'=>$img
            ),
            'msg'=>'apply user success'
        ));

        // 微信通知
        $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx37a75b8fb49a9910&secret=c6dc096467862befee66ffc0fbb3e4db';
        $html = file_get_contents($url);
        $html = json_decode($html,true);
        $access_token = $html['access_token'];  //获取access_token

        $time = date('Y-m-d H:i:s',time());
        
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
        $detail_data = array(
            'first'=>array('value'=>urlencode('您好，您已成功加入品源信息管理系统！')),  
            'keyword1'=>array('value'=>urlencode('工程师'),'color'=>'#173177'),
            'keyword2'=>array('value'=>urlencode($mobile),'color'=>'#173177'),
            'keyword3'=>array('value'=>urlencode($time),'color'=>'#173177'),
        );
        $json_template = json_tempalte($wxopenid,$detail_data,'http://it.mkai8.com/mk_user/#/?token='.$wxopenid);
        $res = curl_post($url,urldecode($json_template));
    }
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":100}');
}