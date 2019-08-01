<?php
require '../config/config.php';
require '../common/function_common.php';
header('Content-Type:application/json; charset=UTF-8');

if(isset($_SESSION['usercode'])&&$_SESSION['usercode']==='true'){
    if($_SESSION['authority']!==2&&$_SESSION['authority']!==1){
        exit('权限不足');
    }
}else{
    exit('非法操作！');
}

if(isset($_POST['keywords'])&&isset($_POST['siteid'])&&isset($_POST['site'])){  
    $servername = "localhost";
    $username = "root";
    $password = "Mk4008877@";
    
    // 创建连接
    $conn2 = mysql_connect($servername, $username, $password, 'mk_manage');
    mysql_select_db('mk_manage', $conn2);
    // 检测连接
    if (!$conn2) {
        die('Could not connect: ' . mysql_error()); 
    } 

    $keywords = inject_check($_POST['keywords']);       //关键词内容
    $siteid = inject_check($_POST['siteid']);           //网站ID
    $site = inject_check($_POST['site']);               //网站名称
    $kw_arr = explode(',',$keywords);
    $kw_arr = array_filter($kw_arr);
    $time = time();

    switch ($siteid)
    {
    case '1001':
        $dbname = 'dedecmsv57gbksp2';
        $prefix = 'pinyuan_';
        break;
    case '1002':
        $dbname = 'dedecmsv57gbksp2';
        $prefix = 'pinyuan_';
        break;
    case '1003':
        $dbname = 'chinapinyuan';
        $prefix = 'pinyuan_';
        break;
    case '1004':
        $dbname = 'soft-pinyuan';
        $prefix = 'soft_';
        break;
    case '1005':
        $dbname = 'school_pinyuan';
        $prefix = 'school_';
        break;
    case '1006':
        $dbname = 'hospital_pinyuan';
        $prefix = 'hospital_';
        break;
    case '1007':
        $dbname = 'library_pinyuan';
        $prefix = 'library_';
        break;
    case '1008':
        $dbname = 'office-pinyuan';
        $prefix = 'office_pinyuan';
        break;
    case '1009':
        $dbname = 'apartment_pinyuan';
        $prefix = 'apartment_';
        break;
    case '1010':
        $dbname = 'restaurant_pinyuan';
        $prefix = 'restaurant_';
        break;
    case '1011':
        $dbname = 'hotel_pinyuan';
        $prefix = 'hotel_';
        break;
    case '1012':
        $dbname = 'shelf_pinyuan';
        $prefix = 'shelf_';
        break;
    case '1014':
        $dbname = 'cleaner_zmshoo';
        $prefix = 'clear_';
        break;
    case '1015':
        $dbname = 'water_zmshoo';
        $prefix = 'water_';
        break;
    }

    // 创建连接
    $conn1 = mysql_connect($servername, $username, $password, $dbname);
    mysql_select_db($dbname, $conn1);
    // 检测连接
    if (!$conn1) {
        die('Could not connect: ' . mysql_error()); 
    } 

    $archives = $prefix.'archives';

    foreach($kw_arr as $v){
        $res = mysql_query("SELECT * FROM `{$archives}` WHERE title='$v'",$conn1);
        $row = mysql_fetch_row($res);
        if(!$row){
            mysql_query("INSERT INTO keyword (siteid,site,keyword,isupload,importtime) VALUE ($siteid,'$site','$v',1,$time)",$conn2);
        }
    }

    echo '{"status":"success","msg":"keywords save success","code":219}';
}else{
    exit('{"status":"error","msg":"missing parameters or error","code":100}');
}