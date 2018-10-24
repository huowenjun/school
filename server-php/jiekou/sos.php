<?php


class SosMess
{

    public $db;

    // private $x_pi =3.14159265358979324 * 3000.0 / 180.0;
    public function __construct()
    {
        ini_set('date.timezone', 'Asia/Shanghai');
        //连接数据库
        try {
            $this->db = new \PDO(
                'mysql:host=123.57.238.217;dbname=school',
                'xptauth',
                'xptauth1502'
            );
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage() . chr(9) . chr(10);
            exit;
        }
    }

    /**
     * 短信发送功能
     * */
    public function sendMess($phone='',$msg=""){
        include_once './ChuanglanSmsHelper/ChuanglanSmsApi.php';

        $clapi = new \ChuanglanSmsApi();
        $result = $clapi->sendSMS($phone, $msg, 'true');
        $result = $clapi->execResult($result);
        if ($result[1] == 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 通过学生卡绑定手机号,对监护人下发Sos短信通知
     * @param $cardMobile str 学生卡绑定手机号
     * */
    public function sendSosMess($cardMobile){
        $db = $this->db;
        //获取学生id
        $stu_info = $db->query("select stu_id,stu_name from sch_student where stu_phone = '{$cardMobile}' ")->fetch(PDO::FETCH_ASSOC);
        if($stu_info){
            //获取监护人手机号
            $mobileArr =  $db->query("select a.parent_phone 
                                          from sch_student_parent a 
                                          INNER JOIN sch_student_group_access b 
                                            ON a.sp_id = b.sp_id
                                        where b.stu_id = '{$stu_info['stu_id']}' ")->fetchAll(PDO::FETCH_ASSOC);

            if($mobileArr){
                //去除重复的监护人号码
                $tmpArr = array();
                foreach($mobileArr as $value){
                    $tmpArr[] = $value['parent_phone'];
                }
                $newArr =  array_unique($tmpArr);
                //执行短信下发
                foreach($newArr as $v){
                    $this->sendMess($v,"紧急:您的孩子".$stu_info['stu_name']."于".date("Y-m-d H:i:s")."使用SOS紧急呼救,请在平安校园APP中查看详情");
                }
            }
        }
    }
}

$obj = new SosMess();
$cardPhone = $_GET['phone'];
if($cardPhone){
    $obj->sendSosMess($cardPhone);
}

?>