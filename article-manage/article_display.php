<?php
require 'core/db.class.php';
require 'config/config.php';
$db = mysql::db();

if(isset($_SESSION['usercode'])&&$_SESSION['usercode']==='true'){
    if($_SESSION['authority']===1||$_SESSION['authority']===2){
        $authority = intval($_SESSION['authority']);
        $res = $db->query("SELECT count(id) FROM keyword WHERE siteid='1001' AND isupload=1");
        if($row = mysql_fetch_row($res)){
            $ccNum = $row[0];
        }
        $str = '';
        $arr = array(
            '1002'=>'设备',
            '1003'=>'家具厂',
            '1004'=>'儿童',
            '1005'=>'学校',
            '1006'=>'医院',
            '1007'=>'图书馆',
            '1008'=>'装修',
            '1009'=>'公寓',
            '1010'=>'餐厅',
            '1011'=>'酒店',
            '1012'=>'零售',
            '1013'=>'智美',
            '1014'=>'新风',
            '1015'=>'净水'
        );
        for($i=1003;$i<1016;$i++){
            $res_new = $db->query("SELECT count(id) FROM keyword WHERE siteid='$i' AND isupload=1");
            if($row_new = mysql_fetch_row($res_new)){
                $otherNum = $row_new[0];
                if($otherNum < 3){
                    $str .= $arr[$i].',';
                }
            }
        }
        include 'templets/default/article_display.html';
    }else{
        header('Content-Type:text/html;charset=utf-8');
        exit('权限不足！');
    }
}else{
    header('Location:login.php');
    exit;
}
    
