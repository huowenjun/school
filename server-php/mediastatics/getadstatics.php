<?php

$servername = "123.57.238.217";
$username = "xptauth";
$password = "xptauth1502";
$dbname = "school";

class GetStatics
{
    protected $db;

    public function __construct()
    {
        ini_set('date.timezone', 'Asia/Shanghai');
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
            echo 'Connection failed: ' . $e->getMessage() . chr(9) . chr(10);
            exit;
        }
    }

    protected function freeDB()
    {
        $this->db = NULL;
    }

    public function get_hash_table($table, $code, $s = 10)
    {
        $hash = sprintf("%u", crc32($code));
        //echo $hash;
        $hash1 = intval(fmod($hash, $s));
        return $table . "_" . $hash1;
    }

    public function GetAdHistory($adid, $type)
    {
        $sqlbanner = "select * from sch_banner where id=\"" . $adid . "\"";
        $res = $this->db->query($sqlbanner)->fetch(PDO::FETCH_ASSOC);//获取广告详情

        if ($res['is_effect'] === "1") {
            if ($res['type'] === "1" && $type === "1") {
                //	echo $row[7];
                return $res['view_count'];
            } elseif ($res['type'] === "2" && $type === "2") {
                //	echo $row[8];
                return $res['click_count'];
            } elseif ($res['type'] === "0") {
                return "infinite";
            } else {
                return "ERR";
            }
        } else {
            return "ERR";
        }
    }

    public function ProcessData($ntime, $adid, $regionid, $sid, $type, $mac, $mobile, $ip, $rand)
    {
        $hashtab = $this->get_hash_table("sch_statics", $rand);
        $sqlinsert = "insert into " . $hashtab . " (ntime,adid,region_id,sid,type,mac,mobile,ipaddr) VALUES ('" . $ntime . "','" . $adid . "','" . $regionid . "','" . $sid . "','" . $type . "','" . $mac . "','" . $mobile . "','" . $ip . "')";
        $this->db->exec($sqlinsert);
        if ($type === "1") {
            $sqlslct = "select * from sch_statics_uion where adid=\"" . $adid . "\" and type=\"" . $type . "\"";
            $row1 = $this->db->query($sqlslct);
            $number = $row1->rowCount();
            $resul = $this->GetAdHistory($adid, $type);
            if ($resul === "infinite") {
                echo "SUCCESS";
            } elseif ($resul !== "ERR") {
                if (intval($resul) >= intval($number)) {
                    echo "SUCCESS";
                } elseif (intval($resul) < intval($number)) {
                    $sqlupdt = "update sch_banner set is_effect=\"0\" where id=\"" . $adid . "\"";
                    $this->db->query($sqlupdt);
                    echo "ERROR";
                }

            } else {
                echo "ERROR";
            }
        } elseif ($type === "2") {
            $sqlslct = "select * from sch_statics_uion where adid=\"" . $adid . "\" and type=\"" . $type . "\"";
            $row1 = $this->db->query($sqlslct);
            $number = $row1->rowCount();
            $resul = $this->GetAdHistory($adid, $type);

            if ($resul === "infinite") {
                echo "SUCCESS";
            } elseif ($resul !== "ERR") {
                if (intval($resul) >= intval($number)) {
                    echo "SUCCESS";
                } elseif (intval($resul) < intval($number)) {
                    $sqlupdt = "update sch_banner set is_effect=\"0\" where id=\"" . $adid . "\"";
                    $this->db->query($sqlupdt);
                    echo "ERROR";
                }
            } else {
                echo "ERROR";
            }
        }

        //	echo $sqlinsert;
        //	$this->db->exec($sqlinsert);
        //echo "SUCCESS";
        $this->freeDB();
    }
}


//$getst = new GetStatics();
//$getst->GetAdHistory("5","1");

$ip = $_SERVER["REMOTE_ADDR"];
$adid=$_GET['adid'];
$regionid=$_GET['region_id'];
$sid=$_GET['sid'];
$type=$_GET['type'];
$mac=$_GET['mac'];
$mobile=$_GET['mobile'];
$ntime=$_GET['ntime'];
$token=$_GET['token'];
$rand=$_GET['rand'];

$chktoken=$adid.$regionid.$sid."school.xinpingtai.com".$ntime.$rand;
//echo $chktoken."\r\n";
$chkmd5 = md5($chktoken);
if($chkmd5 === $token){
    $getst = new GetStatics();
    $getst->ProcessData($ntime,$adid,$regionid,$sid,$type,$mac,$mobile,$ip,$rand);
}
else{
    echo "TOKEN ERROR";
}





