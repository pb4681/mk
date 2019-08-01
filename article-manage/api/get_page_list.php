<?php
require '../core/db.class.php';
require '../config/config.php';
require '../common/function_common.php';
header('Content-Type:application/json; charset=UTF-8');
$db = mysql::db();

if(isset($_SESSION['usercode'])&&$_SESSION['usercode']==='true'){
    if($_SESSION['authority']!==2&&$_SESSION['authority']!==1){
        exit('权限不足');
    }
}else{
    exit('非法操作！');
}

if(isset($_GET['page'])&&isset($_GET['limit'])&&isset($_GET['industry'])&&isset($_GET['isupload'])){
    $page = inject_check($_GET['page']);                       //当前页码
    $pagesize = inject_check($_GET['limit']);                  //每页数量
    $industry = intval(inject_check($_GET['industry']));       //行业ID
    $isupload = intval(inject_check($_GET['isupload']));       //是否上传
    
    $pre = ($page-1)*$pagesize;                        //每页初始位置
    $deliverid = $_SESSION['id'];

    $res0 = $db->query("SELECT authority FROM admin WHERE id=$deliverid");
    $row0 = mysql_fetch_assoc($res0);
    $authority = intval($row0['authority']);
    $cur_day = strtotime(date("Y-m-d",time()));
    $next_day = strtotime(date("Y-m-d",strtotime("+1 day")));
    
    if($authority===2){
        if(empty($industry)&&empty($isupload)){
            $res1 = $db->query("SELECT count(id) FROM article WHERE deliverid=$deliverid AND delivertime between $cur_day AND $next_day");
            $res2 = $db->query("SELECT * FROM article WHERE deliverid=$deliverid AND delivertime between $cur_day AND $next_day ORDER BY id LIMIT $pre,$pagesize");
        }else{
            if($industry&&!$isupload){
                $str = 'industryid='.$industry;
            }else if(!$industry&&$isupload){
                $str = 'isupload='.$isupload;
            }else if($industry&&$isupload){
                $str = 'industryid='.$industry.' AND isupload='.$isupload;
            }
            $res1 = $db->query("SELECT count(id) FROM article WHERE $str AND deliverid=$deliverid AND delivertime between $cur_day AND $next_day");
            $res2 = $db->query("SELECT * FROM article WHERE $str AND deliverid=$deliverid AND delivertime between $cur_day AND $next_day ORDER BY id LIMIT $pre,$pagesize");
        }
    }
    if($authority===1){
        if(empty($industry)&&empty($isupload)){
            $res1 = $db->query("SELECT count(id) FROM article");
            $res2 = $db->query("SELECT * FROM article ORDER BY id LIMIT $pre,$pagesize");
        }else{
            if($industry&&!$isupload){
                $str = 'industryid='.$industry;
            }else if(!$industry&&$isupload){
                $str = 'isupload='.$isupload;
            }else if($industry&&$isupload){
                $str = 'industryid='.$industry.' AND isupload='.$isupload;
            }
            $res1 = $db->query("SELECT count(id) FROM article WHERE $str");
            $res2 = $db->query("SELECT * FROM article WHERE $str ORDER BY id LIMIT $pre,$pagesize");
        }
    }

    if($row1=mysql_fetch_row($res1)){
        $rowCount = $row1[0];                           //获取记录总条数
    }
    $arr = array();
    while($rows2=mysql_fetch_assoc($res2)){
        array_push($arr,array(
                'id'=>intval($rows2['id']),
                'industryid'=>intval($rows2['industryid']),
                'title'=>$rows2['title'],
                'datetime'=>date("Y-m-d",$rows2['datetime']),
                'inserttime'=>date("Y-m-d",$rows2['inserttime']),
                'isupload'=>$rows2['isupload']
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