<?php
require '../../core/db.class.php';
require '../../config/config.php';
require '../../common/function_common.php';
header('Content-Type:application/json; charset=UTF-8');
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers:content-type');
$db = mysql::db();
require './common/safe.php';

function testday($d){
    if($d == date('Y-m-d')){
        return '今天';
    }else if($d === date("Y-m-d",strtotime("-1 day"))){
        return '昨天';
    }else{
        return $d;
    }
}

if(isset($_GET['id'])){
    $id = inject_check($_GET['id']);
    $res = $db->query("SELECT * FROM inquiry WHERE id=$id");
    $res1 = $db->query("UPDATE inquiry SET staffclick=1 WHERE id=$id");
    $row = mysql_fetch_assoc($res);
    echo json_encode(array(
        'code'=>101,
        'data'=>array(
            'time'=>testday($row['writedate']).' '.$row['writetime'],
            'needs'=>$row['needs'],
            'name'=>$row['name'],
            'phone'=>$row['phone'],
            'area'=>$row['area'],
            'qq'=>$row['qq'],
            'area'=>$row['area'],
            'searchwords'=>$row['searchwords'],
            'deliveruser'=>$row['deliveruser']
        ),
        'msg'=>'get inquiry info success'
    ));
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":001}');
}