<?php

class GetTokenInfo
{
    protected $db;

    public function __construct()
    {
        ini_set('date.timezone', 'Asia/Shanghai');
        //连接数据库
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

    // 上线
    public function ProcessDataUp($data)
    {
        //print $data;
        $arrData = json_decode(trim($data), true);
        if ($arrData == NULL) {
            echo 'parse json data error' . json_last_error_msg() . chr(9) . chr(10);
            return false;
        }
        foreach ($arrData as $value) {
            $chktoken = $value['devicetoken'] . $value['mobilenumber'] . "shuhaixinxi";
            $chkmd5 = md5($chktoken);
            if ($chkmd5 === $value['token']) {
                //删除同一设备token信息
                //$sql = "delete from sch_chattoken_parent where parentid = '{$value['parentid']}'";
                $tokensql = "delete from sch_msgpush_token where user_id = '{$value['parentid']}' ";
                //$this->db->exec($sql);
                $this->db->exec($tokensql);

                $sql1 = "delete from sch_msgpush_token where device_token = '{$value['devicetoken']}'";
                $this->db->exec($sql1);
                $time = date('Y-m-d H:i:s');
                //$sql2 = "insert into sch_chattoken_parent (devicetoken,mobiletype,mobilenum,parentid)
                //VALUES ('" . $value['devicetoken'] . "','" . $value['mobtype'] . "','" . $value['mobilenumber'] . "','" . $value['parentid'] . "')";
                $sql2 = "insert into sch_msgpush_token (device_token,system_model,user_name,user_id,create_time,user_type) 
                          VALUES ('{$value['devicetoken']}',
                                  '{$value['mobtype']}',
                                  '{$value['mobilenumber']}',
                                  '{$value['parentid']}',
                                  '{$time}','4')";


                $flag = "0";
                try {
                    $res = $this->db->exec($sql2);
                } catch (Exception $e) {
                    //echo $res;
                    echo "{\"CODE\":\"NOCHANGE\"}";
                    $flag = "1";
                    //print("process data  Caught exception�?.$e->getMessage());
                }
                if ($flag === "0") {
                    echo "{\"CODE\":\"SUCCESS\"}";
                } else {
                    echo "{\"CODE\":\"FAILED\"}";
                }

                //echo $res;
            } else {
                echo "{\"CODE\":\"FAILED\"}";
            }

        }

        $this->freeDB();
    }


}


$data = $_GET['data'];
//echo $data;
$TokenInfo = new GetTokenInfo();
$TokenInfo->ProcessDataUp($data);
