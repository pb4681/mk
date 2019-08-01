<?php
require '../../core/db.class.php';
require '../../config/config.php';
require '../../common/function_common.php';
header('Content-Type:application/json; charset=UTF-8');
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers:content-type');
$db = mysql::db();
require './common/safe.php';

$res = $db->query("SELECT * FROM inquiry WHERE ORDER BY id DESC LIMIT 20");
$res1 = $db->query("SELECT COUNT(id) FROM inquiry WHERE staffclick=0");
if($num=mysql_fetch_row($res1)){
    $rowCount = $num[0];                           //获取记录总条数
}
$arr = array();
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
            'id'=>$row['id'],
            'date'=>$row['writedate'],
            'time'=>$row['writetime'],
            'needs'=>$row['needs'],
            'status'=>$row['status'],
            'area'=>$row['area'],
            'click'=>$row['click'] == 0 ? true : false
        )
    );
}

$icon_array = array(
    '未处理'=>'ios-bookmark-outline',
    '已联系'=>'ios-paper-plane-outline',
    '无法联系'=>'ios-alert-outline',
    '跟进中'=>'ios-text-outline',
    '已解决'=>'ios-checkmark-circle-outline',
    '未达成'=>'ios-close-circle-outline',
    '其他'=>'ios-more'
);

$new_arr_date = array();
$temp_arr = array();
$res_arr = array();
foreach($arr as $v){
    if(!in_array($v['date'],$new_arr_date)){
        array_push($new_arr_date,$v['date']);
    }
}
foreach ($new_arr_date as $v1){
    foreach ($arr as $v2) {
        if(strtotime($v2['date'])===strtotime($v1)){
            array_push($temp_arr,array(
                'dataid'=>$v2['id'],
                'title'=>$v2['needs'],
                'detailtime'=>testday($v2['date']).' '.$v2['time'],
                'area'=>$v2['area'],
                'icon'=>$icon_array[$v2['status']],
                'status'=>$v2['status'],
                'click'=>$v2['click']
            ));
        }
    }
    array_push($res_arr,array(
        'time'=>testday($v1),
        'items'=>$temp_arr
    ));
    $temp_arr = array();
}
echo json_encode(array(
    'code'=>100,
    'data'=>$res_arr,
    'count'=>intval($rowCount),
    'msg'=>'get inquiry success'
));
