<?php

class GetTeacherTokenInfo{
	protected $db;
	
	public function __construct(){
		ini_set('date.timezone','Asia/Shanghai');
		 //连接数据�?
        try {
            $this->db = new \PDO(
                'mysql:host=182.92.113.203;dbname=school',
                //'mysql:host=127.0.0.1;dbname=xptauth',
                'school',
                'school1502'
            );
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage() . chr(9).chr(10);exit;
        }
	}
	
	protected  function freeDB(){
        $this->db = NULL;
    }
	
	// 上线
	public  function ProcessDataUp($data){
			$datajson=urldecode($data);
			//print $datajson;
			$arrData = json_decode(trim($datajson),true);
			if($arrData == NULL){
				echo 'parse json data error'.json_last_error_msg().chr(9).chr(10);return false;
			}
			$flag="0";
			foreach($arrData as $value) {
				//echo "=======";
				if($flag==="0"){
					$sqld="delete from sch_teachertoken where techer_mobile=\"".$value['techer_mobile']."\"";
				//	print $sqld;
					$res = $this->db->exec($sqld);
					$flag="1";
				}
				if($flag==="1"){
					$sql = "INSERT INTO `sch_teachertoken`(`device_token_teacher`,`teacher_id`,`mobtype`,`techer_mobile`) VALUES ('".$value['device_token_teacher']."','".$value['teacher_id']."','".$value['mobtype']."','".$value['techer_mobile']."')";
					//print $sql;
					$this->db->exec($sql);
					echo "{\"CODE\":\"SUCCESS\"}";
				}
			}
			
			
			$this->freeDB();
   }
	
   
   // 下线
   public  function ProcessDataDown($data){
		$sqld="delete from sch_teachertoken where device_token_teacher=\"".$data."\"";
		$this->db->exec($sqld);
		echo "{\"CODE\":\"SUCCESS\"}";
		$this->freeDB();	
   }
   
   
   
}


$type=$_GET['type'];
$data=$_GET['data'];
//echo $data;
$TeacherTokenInfo = new GetTeacherTokenInfo();
if($type==="1")
{
	$TeacherTokenInfo->ProcessDataUp($data);
}
elseif($type==="2")
{
	$TeacherTokenInfo->ProcessDataUp($data);
}
elseif($type==="3")
{
	$TeacherTokenInfo->ProcessDataDown($data);
}


