<?php

$servername = "123.57.238.217";
$username = "xptauth";
$password = "xptauth1502";
$dbname = "school";

//$conn = new mysqli($servername, $username, $password, $dbname);
//if ($conn->connect_error) {
 //   die("连接失败: " . $conn->connect_error);
//}
// 
$db = new \PDO(
                'mysql:host=123.57.238.217;dbname=school',
                //'mysql:host=127.0.0.1;dbname=xptauth',
                'xptauth',
                'xptauth1502'
            );
            $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION); 

$i=0;
while($i<=10){
	$sql="CREATE TABLE `sch_statics_".$i."`(`media_id`  int(10) NOT NULL AUTO_INCREMENT,`ntime`  varchar(40) NULL ,`adid`  varchar(10) NULL ,`region_id`  varchar(255) NULL ,`sid`  varchar(10) NULL ,`type`  varchar(30) NULL ,`mac`  varchar(255) NULL ,`mobile` varchar(255) NOT NULL,`ipaddr` varchar(255) NULL,PRIMARY KEY (`media_id`))ENGINE=MyISAM DEFAULT CHARSET=utf8";
 //$sql="DROP TABLE `sch_statics_".$i."`;";
 //echo $sql."\r\n";
 //$conn->mysql_query($sql);
 $db->exec($sql);
$i++; 
}
$db = NULL;




