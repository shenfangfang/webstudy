<?php

    /**
     * 生成mysql数据字典
     */
    header ( "Content-type: text/html; charset=utf-8" );
     
    // 配置数据库
    $dbserver = "localhost";
    $dbusername = "root";
    $dbpassword = "root";
    $database = @$_GET['db']?$_GET['db']:'mydb';
     
    // 其他配置
    $title = '数据字典';
     
    $mysql_conn = @mysql_connect ( "$dbserver", "$dbusername", "$dbpassword" ) or die ( "错误！MYSQL连接失败." );
    mysql_select_db ( $database, $mysql_conn ) or die ( "错误！选择的数据库不存在." );;
    mysql_query ( 'SET NAMES utf8', $mysql_conn );
    $table_result = mysql_query ( 'show tables', $mysql_conn );
    // 取得所有的表名
    while ( $row = mysql_fetch_array ( $table_result ) ) {
        $tables [] ['TABLE_NAME'] = $row [0];
    }
     
    // 循环取得所有表的备注及表中列消息
    foreach ( $tables as $k => $v ) {
        $sql = 'SELECT * FROM ';
        $sql .= 'INFORMATION_SCHEMA.TABLES ';
        $sql .= 'WHERE ';
        $sql .= "table_name = '{$v['TABLE_NAME']}'  AND table_schema = '{$database}'";
        $table_result = mysql_query ( $sql, $mysql_conn );
        while ( $t = mysql_fetch_array ( $table_result ) ) {
            $tables [$k] ['TABLE_COMMENT'] = $t ['TABLE_COMMENT'];
        }
         
        $sql = 'SELECT * FROM ';
        $sql .= 'INFORMATION_SCHEMA.COLUMNS ';
        $sql .= 'WHERE ';
        $sql .= "table_name = '{$v['TABLE_NAME']}' AND table_schema = '{$database}'";
         
        $fields = array ();
        $field_result = mysql_query ( $sql, $mysql_conn );
        while ( $t = mysql_fetch_array ( $field_result ) ) {
            $fields [] = $t;
        }
        $tables [$k] ['COLUMN'] = $fields;
    }
    mysql_close ( $mysql_conn );
     
    $html = '';
    // 循环所有表
    foreach ( $tables as $k => $v ) {
        $html .= '<table  border="1" cellspacing="0" cellpadding="0" align="center">';
        $html .= '<caption>' . $v ['TABLE_NAME'] . '  ' . $v ['TABLE_COMMENT'] . '</caption>';
        $html .= '<tbody><tr><th>字段名</th><th>数据类型</th><th>默认值</th>
        <th>允许非空</th>
        <th>自动递增</th><th>备注</th></tr>';
        $html .= '';
         
        foreach ( $v ['COLUMN'] as $f ) {
            $html .= '<tr><td class="c1">' . $f ['COLUMN_NAME'] . '</td>';
            $html .= '<td class="c2">' . $f ['COLUMN_TYPE'] . '</td>';
            $html .= '<td class="c3">' . $f ['COLUMN_DEFAULT'] . '</td>';
            $html .= '<td class="c4">' . $f ['IS_NULLABLE'] . '</td>';
            $html .= '<td class="c5">' . ($f ['EXTRA'] == 'auto_increment' ? '是' : '&nbsp;') . '</td>';
            $html .= '<td class="c6">' . $f ['COLUMN_COMMENT'] . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table></p>';
    }
 
    echo '<html>
            <meta charset="gbk">
            <title>自动生成数据字典</title>
            <style>
                body,td,th {font-family:"宋体"; font-size:12px;}  
                table{border-collapse:collapse;border:1px solid #CCC;background:#efefef;}  
                table caption{text-align:left; background-color:#ffd; line-height:2em; font-size:14px; font-weight:bold; color:#040;}  
                table th{text-align:left; height:26px; font-weight:normal;font-size:12px; border:1px solid #CCC;}  
                table td{height:20px; font-size:12px; border:1px solid #CCC;background-color:#fff;}  
                .c1{ width: 120px;}  
                .c2{ width: 120px;}  
                .c3{ width: 70px;}  
                .c4{ width: 80px;}  
                .c5{ width: 80px;}  
                .c6{ width: 270px;}
            </style>
            <body bgcolor="#ffd">';
    echo '<h2 style="text-align:center;color:blue;">'.$title.' -- '.$database.'</h2>';
    echo $html;
    echo '</body></html>';
 
?>