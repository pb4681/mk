<?php
require '../core/db.class.php';
require '../config/config.php';
require '../common/function_common.php';
header('Content-Type:application/json; charset=UTF-8');
$db = mysql::db();

/**
 * 将模板消息json格式化
 */
 function json_tempalte($openid,$detail_data=array(),$new_url){
    //模板消息
    $template=array(
        'touser' => 'ovrxsxNdQksOXEX4tPa82fH6c7bk',  //管理员openid
        'template_id' => 'UVLyIR6Wt2kWik_tq44Gcor-gb3g_9rnasJ_54jdLqw', //在公众号下配置的模板id
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

if(isset($_SESSION['usercode'])&&$_SESSION['usercode']==='true'){
    if($_SESSION['authority']!==3&&$_SESSION['authority']!==1){
        exit('权限不足');
    }
}else{
    exit('非法操作！');
}

if(isset($_POST['type'])&&isset($_POST['date'])&&isset($_POST['time'])&&isset($_POST['name'])
&&isset($_POST['phone'])&&isset($_POST['qq'])&&isset($_POST['needs'])&&isset($_POST['source'])
&&isset($_POST['searchwords'])&&isset($_POST['area'])&&isset($_POST['openpage'])&&isset($_POST['path'])
&&isset($_POST['useragent'])&&isset($_POST['chatway'])){
    $type = inject_check($_POST['type']);                     //操作类型
    $date = inject_check($_POST['date']);                     //日期
    $time = inject_check($_POST['time']);                     //时间
    $name = inject_check($_POST['name']);                     //名字
    $phone = inject_check($_POST['phone']);                   //电话
    $qq = inject_check($_POST['qq']);                         //QQ
    $needs = inject_check($_POST['needs']);                   //需求   
    $source = inject_check($_POST['source']);                 //渠道来源
    $searchwords = inject_check($_POST['searchwords']);       //搜索词
    $area = inject_check($_POST['area']);                     //地域
    $openpage = inject_check($_POST['openpage']);             //初始页面
    $path = inject_check($_POST['path']);                     //访问路径
    $useragent = inject_check($_POST['useragent']);           //设备类型
    $chatway = inject_check($_POST['chatway']);               //沟通方式

    $savetime = time();

    if($type === 'add'){
        $db->query("INSERT INTO inquiry (writedate,writetime,name,phone,qq,needs,source,searchwords,area,openpage,path,useragent,status,chatway,savetime) 
        VALUES ('$date','$time','$name','$phone','$qq','$needs','$source','$searchwords','$area','$openpage','$path','$useragent','未处理','$chatway',$savetime)");
    }else if($type === 'edit'){
        $id = inject_check($_POST['id']);                     //系统ID
        $db->query("UPDATE inquiry SET writedate='$date',writetime='$time',name='$name',phone='$phone',qq='$qq',needs='$needs',source='$source',searchwords='$searchwords',area='$area',openpage='$openpage',path='$path',useragent='$useragent',chatway='$chatway',savetime='$savetime' WHERE id=$id");
    }

    // 微信通知
    $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx37a75b8fb49a9910&secret=c6dc096467862befee66ffc0fbb3e4db';
    $html = file_get_contents($url);
    $html = json_decode($html,true);
    $access_token = $html['access_token'];  //获取access_token

    $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$access_token;
    $detail_data = array(
        'first'=>array('value'=>urlencode('有新的询盘，请及时处理')),  
        'keyword1'=>array('value'=>urlencode($name),'color'=>'#173177'),
        'keyword2'=>array('value'=>urlencode($phone),'color'=>'#173177'),
        'keyword3'=>array('value'=>urlencode($date.' '.$time),'color'=>'#173177'),
    );
    $json_template = json_tempalte($userid,$detail_data,'http://it.mkai8.com/mk_admin/#/?token=ovrxsxNdQksOXEX4tPa82fH6c7bk');
    $res = curl_post($url,urldecode($json_template));

    echo '{"status":"success","msg":"inquiry save success","code":224}';
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":100}');
}