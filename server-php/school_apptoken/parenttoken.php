<?php

class GetTokenInfo{
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
			$arrData = json_decode(trim($data),true);
			if($arrData == NULL){
				echo 'parse json data error'.json_last_error_msg().chr(9).chr(10);return false;
			}
			foreach($arrData as $value) {
				$chktoken=$value['imei'].$value['devicetoken'].$value['mobilenumber']."shuhaixinxi".$value['mobmac'];
				$chkmd5 = md5($chktoken);
				//echo "\n".$chkmd5."/n";
				//echo "{\"CODE\":\"SUCCESS\"}";
				if($chkmd5 === $value['token']){
					$sql1="update sch_parenttoken set devicetoken=\"".$value['devicetoken']."\",mobtype=\"".$value['mobtype']."\",mobilenumber=\"".$value['mobilenumber']."\",mobmac=\"".$value['mobmac']."\",mobinfo=\"".$value['mobinfo']."\",ntime=\"".$value['ntime']."\" where imei=\"".$value['imei']."\" and stuname=\"".$value['stuname']."\"";
					//echo $sql1;
					$res = $this->db->exec($sql1);
					//echo "===".$res."===";
					if($res=="0"){
						//echo "########";
						$sql2="insert into sch_parenttoken (imei,devicetoken,mobtype,mobilenumber,mobmac,mobinfo,ntime,stuname) VALUES ('".$value['imei']."','".$value['devicetoken']."','".$value['mobtype']."','".$value['mobilenumber']."','".$value['mobmac']."','".$value['mobinfo']."','".$value['ntime']."','".$value['stuname']."')";
						//echo $sql2;
						$flag="0";
						try{
							$res = $this->db->exec($sql2);
						}catch(Exception $e){
							//echo $res;
							echo "{\"CODE\":\"NOCHANGE\"}";
							$flag="1";
							//print("process data  Caught exception�?.$e->getMessage());
						}
						if($flag==="0"){
							echo "{\"CODE\":\"SUCCESS\"}";
						}		
					}
					else{
						echo "{\"CODE\":\"SUCCESS\"}";
					}
					//echo $res;	
				}
				else{
					echo "{\"CODE\":\"FAILED\"}";
				}
				
			}
			
			$this->freeDB();
   }
	
	// 切换账号
   public  function ProcessDataChange($data){
			$arrData = json_decode(trim($data),true);
			if($arrData == NULL){
				echo 'parse json data error'.json_last_error_msg().chr(9).chr(10);return false;
			}  
			$flag ="0";
			foreach($arrData as $value) {
				$chktoken=$value['imei'].$value['devicetoken'].$value['mobilenumber']."shuhaixinxi".$value['mobmac'];
				$chkmd5 = md5($chktoken);
				//echo $chkmd5."===".md5($chktoken);
				if($chkmd5 === $value['token']){
					//echo "%%%%%";
					if($flag==="0"){
						$sqld="delete from sch_parenttoken where devicetoken=\"".$value['devicetoken']."\"";
						$this->db->exec($sqld);
						$flag="1";
					}
					if($flag==="1"){
						$sql2="insert into sch_parenttoken (imei,devicetoken,mobtype,mobilenumber,mobmac,mobinfo,ntime,stuname) VALUES ('".$value['imei']."','".$value['devicetoken']."','".$value['mobtype']."','".$value['mobilenumber']."','".$value['mobmac']."','".$value['mobinfo']."','".$value['ntime']."','".$value['stuname']."')";
						//echo $sql2;
						$flag1="0";
						try{
							$res = $this->db->exec($sql2);
							//if($res==="1"){
							////	echo "{\"CODE\":\"SUCCESS\"}";
							//}
						}catch(Exception $e){
							echo "{\"CODE\":\"NOCHANGE\"}";
							$flag1="1";
							//$flag="1";
						}
						if($flag1==="0"){
							echo "{\"CODE\":\"SUCCESS\"}";
						}	
					}
					
				}
				else{
					echo "{\"CODE\":\"FAILED\"}";
				}
			}
		$this->freeDB();
   }
   
   // 下线
   public  function ProcessDataDown($data){
		$arrData = json_decode(trim($data),true);
		if($arrData == NULL){
				echo 'parse json data error'.json_last_error_msg().chr(9).chr(10);return false;
		}
		foreach($arrData as $value) {
			$chktoken=$value['imei'].$value['devicetoken'].$value['mobilenumber']."shuhaixinxi".$value['mobmac'];
			$chkmd5 = md5($chktoken);
			if($chkmd5 === $value['token']){
				$sqld="delete from sch_parenttoken where devicetoken=\"".$value['devicetoken']."\" AND imei=\"".$value['imei']."\"";
				$this->db->exec($sqld);
				echo "{\"CODE\":\"SUCCESS\"}";
			}
			
		}

		$this->freeDB();	
   }
   
   
   
}


$type=$_GET['type'];
$data=$_GET['data'];
//echo $data;
$TokenInfo = new GetTokenInfo();
if($type==="1")
{
	$TokenInfo->ProcessDataUp($data);
}
elseif($type==="2")
{
	$TokenInfo->ProcessDataChange($data);
}
elseif($type==="3")
{
	$TokenInfo->ProcessDataDown($data);
}


