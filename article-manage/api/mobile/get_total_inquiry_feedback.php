<?php
require '../../core/db.class.php';
require '../../config/config.php';
require '../../common/function_common.php';
header('Content-Type:application/json; charset=UTF-8');
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers:content-type');
$db = mysql::db();
require './common/safe.php';


$res = $db->query("SELECT * FROM inquiry WHERE feedback=1 ORDER BY id DESC");
$res1 = $db->query("SELECT COUNT(id) FROM inquiry WHERE feedback=1");
if($num=mysql_fetch_row($res1)){
    $rowCount = $num[0];                           //获取记录总条数
}
$arr = array();

$icon_array = array(
    '未处理'=>'ios-bookmark-outline',
    '已联系'=>'ios-paper-plane-outline',
    '无法联系'=>'ios-alert-outline',
    '跟进中'=>'ios-text-outline',
    '已解决'=>'ios-checkmark-circle-outline',
    '未达成'=>'ios-close-circle-outline',
    '其他'=>'ios-more'
);

function testday($d){
    if($d == date('Y-m-d')){
        return '今天';
    }else if($d === date("Y-m-d",strtotime("-1 day"))){
        return '昨天';
    }else{
        return $d;
    }
}

while($row = mysql_fetch_assoc($res)){
    array_push(
        $arr,array(
            'dataid'=>$row['id'],
            'title'=>$row['needs'],
            'detailtime'=>testday($row['writedate'].' '.$row['writetime']),
            'area'=>$row['area'],
            'icon'=>$icon_array[$row['status']],
            'status'=>$row['status']
        )
    );
}

echo json_encode(array(
    'code'=>100,
    'data'=>$arr,
    'count'=>intval($rowCount),
    'msg'=>'get inquiry success'
));
