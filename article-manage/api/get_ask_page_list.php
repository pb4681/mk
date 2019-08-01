<?php
require '../core/db.class.php';
require '../config/config.php';
require '../common/function_common.php';
header('Content-Type:application/json; charset=UTF-8');
$db = mysql::db();

if(isset($_SESSION['usercode'])&&$_SESSION['usercode']==='true'){
    if($_SESSION['authority']!==3&&$_SESSION['authority']!==1){
        exit('权限不足');
    }
}else{
    exit('非法操作！');
}

if(isset($_GET['page'])&&isset($_GET['limit'])&&isset($_GET['daterange'])&&isset($_GET['inquiry'])){
    $page = inject_check($_GET['page']);                       //当前页码
    $pagesize = inject_check($_GET['limit']);                  //每页数量
    $daterange = inject_check($_GET['daterange']);             //月份跨度
    $inquiry = inject_check($_GET['inquiry']);                 //询盘状态
    
    $daterange_array = explode(' - ',$daterange);
    $startdate = $daterange_array[0];                          //初始日期
    $enddate = $daterange_array[1];                            //结束日期

    $pre = ($page-1)*$pagesize;                                //每页初始位置
    $deliverid = $_SESSION['id'];

    $res0 = $db->query("SELECT authority FROM admin WHERE id=$deliverid");
    $row0 = mysql_fetch_assoc($res0);
    $authority = intval($row0['authority']);
    
    if($authority===3){
        if(empty($daterange)&&empty($inquiry)){
            $res1 = $db->query("SELECT count(id) FROM inquiry WHERE deliverid=$deliverid");
            $res2 = $db->query("SELECT * FROM inquiry WHERE deliverid=$deliverid ORDER BY id LIMIT $pre,$pagesize");
        }else{
            if($daterange&&!$inquiry){
                $str = "deliverid=$deliverid AND writedate between '$startdate' AND '$enddate'";
            }else if(!$daterange&&$inquiry){
                $str = "deliverid=$deliverid AND status='$inquiry'";
            }else if($daterange&&$inquiry){
                $str = "deliverid=$deliverid AND status='$inquiry' AND writedate between '$startdate' AND '$enddate'";
            }
            $res1 = $db->query("SELECT count(id) FROM inquiry WHERE str");
            $res2 = $db->query("SELECT * FROM inquiry WHERE $str ORDER BY id LIMIT $pre,$pagesize");
        }
    }
    if($authority===1){
        if(empty($daterange)&&empty($inquiry)){
            $res1 = $db->query("SELECT count(id) FROM inquiry");
            $res2 = $db->query("SELECT * FROM inquiry ORDER BY id LIMIT $pre,$pagesize");
        }else{
            if($daterange&&!$inquiry){
                $str = "writedate between '$startdate' AND '$enddate'";
            }else if(!$daterange&&$inquiry){
                $str = "status='$inquiry'";
            }else if($daterange&&$inquiry){
                $str = "status='$inquiry' AND writedate between '$startdate' AND '$enddate'";
            }
            $res1 = $db->query("SELECT count(id) FROM inquiry WHERE $str");
            $res2 = $db->query("SELECT * FROM inquiry WHERE $str ORDER BY id LIMIT $pre,$pagesize");
        }
    }

    if($row1=mysql_fetch_row($res1)){
        $rowCount = $row1[0];                           //获取记录总条数
    }
    $arr = array();
    while($rows2=mysql_fetch_assoc($res2)){
        array_push($arr,array(
                'id'=>intval($rows2['id']),
                'date'=>$rows2['writedate'],
                'time'=>$rows2['writetime'],
                'name'=>$rows2['name'],
                'phone'=>$rows2['phone'],
                'qq'=>$rows2['qq'],
                'needs'=>$rows2['needs'],
                'source'=>$rows2['source'],
                'searchwords'=>$rows2['searchwords'],
                'area'=>$rows2['area'],
                'openpage'=>$rows2['openpage'],
                'path'=>$rows2['path'],
                'useragent'=>$rows2['useragent'],
                'chatway'=>$rows2['chatway'],
                'person'=>$rows2['deliveruser'],
                'status'=>$rows2['status']
            )
        );
    }
    echo json_encode(array(
            'code'=>0,
            'count'=>intval($rowCount),
            'data'=>$arr,
            'msg'=>'get page success'
        )
    );
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":100}');
}