<?php


class Lbs2gps
{

    public $db;

    // private $x_pi =3.14159265358979324 * 3000.0 / 180.0;
    public function __construct()
    {
        ini_set('date.timezone', 'Asia/Shanghai');
        //连接数据库
        try {
            $this->db = new \PDO(
                'mysql:host=59.110.42.149;dbname=shool2',
                'root',
                'root'
            );
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage() . chr(9) . chr(10);
            exit;
        }
    }

    /**
     * 获取type=1的位置信息
     *
     * */
    public function setPlaceInfo()
    {
        $db = $this->db;
        //获取type=1所有结果集
        $info = $db->query("select tr_id,mnc,lac,cid from sch_trail 
                                            where type=1 order by tr_id desc limit 500  ")
            ->fetchAll(PDO::FETCH_ASSOC);

        foreach ($info as $key => $value) {
            $value['mnc'] ;//00联通定位
            //在位置库表lbs2gps中查询经纬度信息
            $rows = $db->query("select id, longti,lati from sch_lbs2gps 
                          where mnc = '{$value['mnc']}' and lac = '{$value['lac']}' and cid = '{$value['cid']}'  
                      ")->fetch(PDO::FETCH_ASSOC);

            //未取到经纬度数据
            if (!$rows) {
                //调用lbs基站接口
                $place = $this->getLongtiLati($value);

                //未获取位置信息
                if ($place['stats'] == 0 || $place['longti'] == '') {
                    //修改trail表数据
                    $affectRow = $db->exec("update  sch_trail set `type` = 2 
                                          where tr_id =  '{$value['tr_id']}' ");


                }else{
                    //以获取位置信息
                    //将位置信息存入lbs2gps
                    $lastId = $db->exec("insert into sch_lbs2gps set
                                mnc = '{$value['mnc']}',
                                mcc =   '460',
                                lac =  '{$value['lac']}',
                                cid =  '{$value['cid']}',
                                longti =  '{$place['longti']}',
                                lati =   '{$place['lati']}',
                                addr =   '{$place['addr']}'  ");


                    $rows = array(
                        'longti'=>$place['longti'],
                        'lati'=>$place['lati'],
                    );
                    $affectRow = $db->exec("update  sch_trail set longitude = '{$rows['longti']}',latitude = '{$rows['lati']}',type='0' 
                                          where tr_id =  '{$value['tr_id']}' ");
                }

            }else{
                //修改trail表数据
                $affectRow = $db->exec("update  sch_trail set longitude = '{$rows['longti']}',latitude = '{$rows['lati']}',type='0' 
                                          where tr_id =  '{$value['tr_id']}' ");
            }

        }

    }

    /**
     * 获取lbs经纬度信息
     * */
    public function getLongtiLati($arr = array())
    {
        $arr['time'] = date('Y-m-d H:i:s');
        $logStr = json_encode($arr)."\n";
        file_put_contents('lbs2gps.txt',$logStr,FILE_APPEND);
        $url = "http://v.juhe.cn/cell/get";
        // 	移动基站：0 联通基站:1
        if($arr['mnc'] == '00'){//联通定位
            $mnc = 1;
        }else{
            $mnc = 0;
        }
        $data = "mnc=$mnc&lac=" . $arr['lac'] . "&cell=" . $arr['cid'] . "&hex=&dtype=&callback=&key=5347083b74940637bc995086397029b0";
        $res = $this->reqURL($url, $data, "POST");

        if ($res['resultcode'] == 200) {
            $place['stats'] = 1;
            $place['longti'] = $res['result']['data'][0]['LNG'];
            $place['lati'] = $res['result']['data'][0]['LAT'];
            $place['addr'] = $res['result']['data'][0]['ADDRESS'];
        } else {
            $place['stats'] = 0;
            $place['reason'] = '位置接口调用失败:' . $res['reason'];
        }

        return $place;
    }

    public function reqURL($url, $data = null, $type = 'POST')
    {
        $ch = curl_init();  // 初始化一个curl会话

        $header = "Accept-Charset: utf-8";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        // curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch); //执行一个cURL会话
        curl_close($ch);//关闭一个cURL会话
        return json_decode($output, true); //对 JSON 格式的字符串进行编码
    }


}

$obj = new Lbs2gps();
$obj->setPlaceInfo();


?>
