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

if(isset($_GET['page'])&&isset($_GET['limit'])&&isset($_GET['siteid'])&&isset($_GET['isupload'])){
    $page = inject_check($_GET['page']);                       //当前页码
    $pagesize = inject_check($_GET['limit']);                  //每页数量
    $siteid = intval(inject_check($_GET['siteid']));           //网站ID
    $isupload = intval(inject_check($_GET['isupload']));             //类型
	$cat = intval(inject_check($_GET['cat']));//栏目
    $pre = ($page-1)*$pagesize;                        //每页初始位置
    $userid = $_SESSION['id'];
	
    $res0 = $db->query("SELECT authority FROM admin WHERE id=$userid");
    $row0 = mysql_fetch_assoc($res0);
    $authority = intval($row0['authority']);
    
    if($authority===2){
        if(empty($siteid)&&empty($isupload)){
            $res1 = $db->query("SELECT count(id) FROM product WHERE userid=$userid");
            $res2 = $db->query("SELECT * FROM product WHERE userid=$userid ORDER BY id DESC LIMIT $pre,$pagesize");
        }else{
            if($siteid&&!$isupload){
                $str = 'siteid='.$siteid;
            }else if(!$siteid&&$isupload){
                $str = 'isupload='.$isupload;
            }else if($siteid&&$isupload){
                $str = 'siteid='.$siteid.' AND isupload='.$isupload;
            }
            $res1 = $db->query("SELECT count(id) FROM product WHERE $str AND userid=$userid");
            $res2 = $db->query("SELECT * FROM product WHERE $str AND userid=$userid ORDER BY id DESC LIMIT $pre,$pagesize");
        }
    }
    if($authority===1){
        if(empty($siteid)&&empty($isupload)){
            $res1 = $db->query("SELECT count(id) FROM product");
            $res2 = $db->query("SELECT * FROM product ORDER BY id DESC LIMIT $pre,$pagesize");
        }else{
            if($siteid&&!$isupload){
                $str = 'siteid='.$siteid;
            }else if(!$siteid&&$isupload){
                $str = 'isupload='.$isupload;
            }else if($siteid&&$isupload&&$cat){
                $str = 'siteid='.$siteid.' AND isupload='.$isupload.' AND catid='.$cat;
            }
            $res1 = $db->query("SELECT count(id) FROM product WHERE $str");
            $res2 = $db->query("SELECT * FROM product WHERE $str ORDER BY id DESC LIMIT $pre,$pagesize");
        }
    }

    if($row1=mysql_fetch_row($res1)){
        $rowCount = $row1[0];                           //获取记录总条数
    }
    $arr = array();
    while($rows2=mysql_fetch_assoc($res2)){
        array_push($arr,array(
                'id'=>intval($rows2['id']),
                'aid'=>intval($rows2['aid']) === 0 ? '无' : intval($rows2['aid']),
                'title'=>$rows2['title'],
                'isupload'=>$rows2['isupload'],
                'siteid'=>$rows2['siteid'],
                'site'=>$rows2['site'],
                'cat'=>$rows2['cat'],
                'uploadtime'=>date("Y-m-d",$rows2['uploadtime']) === '1970-01-01' ? '无' : date("Y-m-d",$rows2['uploadtime'])
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