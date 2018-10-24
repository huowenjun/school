<?php

class Server
{
    private $serv;

    public function __construct()
    {
        $this->serv = new swoole_server('0.0.0.0', 9501);
        //当启动一个Swoole应用时，一共会创建2 + n + m个进程，2为一个Master进程和一个Manager进程，其中n为Worker进程数。m为TaskWorker进程数。
        //默认如果不设置，swoole底层会根据当前机器有多少CPU核数，启动对应数量的Reactor线程和Worker进程。我机器为4核的。Worker为4。TaskWorker为0。
        //下面我来设置worker_num = 10。看下启动了多少个进程

        $this->serv->set([
            'worker_num' => 4,//worker进程数
            'dispatch_mode' => 1,//数据包分发策略轮循模式，收到会轮循分配给每一个worker进程
            'deamonize' => true,//守护进程化
            'log_file' => '/phpstudy/www/swoole/error.log',
            'open_eof_check' => true,//打开buffer
            'package_eof' => "]",// 设置EOF尾部标识
            'heartbeat_check_interval' => 300,
            'heartbeat_idle_time' => 600,
        ]);

        $this->serv->on('Start', array($this, 'onStart'));//启动事件
        $this->serv->on('Connect', array($this, 'onConnect'));//连接事件
        $this->serv->on('Receive', array($this, 'onReceive'));//接收事件
        $this->serv->on('Close', array($this, 'onClose'));//关闭事件

        //master进程启动后, fork出Manager进程, 然后触发ManagerStart
        $this->serv->on('ManagerStart', function (\swoole_server $server) {
            echo "On manager start.";

        });

        //manager进程启动,启动work进程的时候调用 workid表示第几个id, 从0开始。
        $this->serv->on('WorkerStart', function ($serv, $workerId) {
            while (1) {
                $ret = swoole_process::wait();
                if ($ret) {// $ret 是个数组 code是进程退出状态码，
                    $pid = $ret['pid'];
                    echo PHP_EOL . "Worker Exit, PID=" . $pid . PHP_EOL;
                    file_put_contents('wait.txt', date('YmdHis') . '--' . $pid . PHP_EOL, FILE_APPEND);
                } else {
                    break;
                }
            }
            echo '---' . $workerId;
        });

        //当一个work进程死掉后，会触发
        $this->serv->on('WorkerStop', function () {
            echo '--stop';
        });

        //启动
        $this->serv->start();

    }

    //启动server时候会触发。
    public function onStart($serv)
    {
        echo "Start\n";
    }

    /**
     * client连接成功后触发。
     * $serv Swoole\Server对象
     * $fd 连接的文件描述符，发送数据/关闭连接时需要此参数
     * $from_id 来自哪个Reactor线程
     *
     * */
    public function onConnect($serv, $fd, $from_id)
    {
        $serv->send($fd, "Hello {$fd}!");
    }

    /**
     * 接收client发过来的请求
     * $serv Swoole\Server对象
     * $fd 连接的文件描述符，发送数据/关闭连接时需要此参数
     * $from_id 来自哪个Reactor线程
     * $data 内容  [厂商*设备 ID*内容长度*内容] 内容长度为最后一个*号到]的长度
     *
     * */
    public function onReceive($serv, $fd, $from_id, $data)
    {
        if (strpos($data, '222516') !== false) {
            file_put_contents('222516.txt', date('Y-m-d H:i:s') . $data . PHP_EOL, FILE_APPEND);
        }
        try {
            //判断设备类型
            $command = array(0);
            $command_arr[1] = '';
            if (strpos($data, '*HQ') !== false) {//学生卡设备
                preg_match_all('/\*HQ(.*)#/isU', $data, $command);
                $device_type = 1;
            } elseif (strpos($data, '[3G*') !== false) {//手表设备
                preg_match_all('/^\[(.*)\*(.*)\*(.*)\*(.*)\]$/isU', $data, $command);
                $device_type = 2;
            }
            //合法的命令字串
            if ($command[0]) {
                //逻辑处理部分
                foreach ($command[0] as $key => $command_str) {
                    if ($device_type == 1) { //学生卡设备
                        $content = explode(',', $command_str);
                        $comment_type = $content[2];

                    } elseif ($device_type == 2) {//手表设备
                        $command_arr = explode('*', $command_str); //0[厂商 1设备ID 2内容长度 3内容] 内容长度为最后一个*号到]的长度
                        $content = explode(',', $command[4][0]);//
                        $comment_type = $content[0];
                    }
                    //逻辑处理部分
                    //$comment_type 协议类型 LK链路保持 UD/UD2位置上报 AL警报
                    if ($comment_type == 'LK') {//链路保持
                        $now_time = date("Y-m-d");
                        if (isset($content[1])) {
                            $redis = self::redis();
                            $redis->set('Step:' . $now_time . ':' . $command_arr[1], $content[1], 604800);
                        }
                        $reply = $command_arr[0] . '*' . $command_arr[1] . '*0002*LK]';
                        $serv->send($fd, $reply);
                    } elseif ($comment_type == 'CR') {//链路保持
                        $reply = $command_arr[0] . '*' . $command_arr[1] . '*0002*CR]';
                        $serv->send($fd, $reply);
                    } elseif ($comment_type == 'TK') {//微聊
                        //接收结果信息
                        if ($content[1] == '0') {//客户端接收失败
                        } elseif ($content[1] == '1') {//客户端接收成功
                        } else {//终端向平台发送数据
                            //平台先向终端回复结果
                            $reply = $command_arr[0] . '*' . $command_arr[1] . '*0004*TK,1]';
                            $serv->send($fd, $reply);
                            //存储amr文件
                            $amr_content = trim($command[4][0], 'TK,');
                            $this->save_amr($amr_content, $command_arr[1]);
                        }
                    } elseif ($comment_type == 'PP') {//碰碰交友
                        //检测自己是否存在好友
                        $is_pp = $this->checkPP($command_arr[1]);
                        if ($is_pp) { //自己可交友
                            //获取pp库中的记录
                            $redis = self::redis();
                            $ppkey = 'task:PP' . date('YmdHi');
                            $imeis = $redis->sMembers($ppkey);
                            $redis->expire($ppkey, 60);
                            foreach ($imeis as $imei) {
                                if ($imei != $command_arr[1]) {//好友imei
                                    $is_pp_check = $this->checkPP($imei);
                                    if (!$is_pp_check) {//好友存在好友
                                        $reply = '[3G*' . $command_arr[1] . '*0001*pp*2]';
                                        $serv->send($fd, $reply);
                                    } else {
                                        $task_content = 'PP,' . $imei;
                                        $task_len = $this->get_len_16($task_content);
                                        $reply = '[3G*' . $command_arr[1] . '*' . $task_len . '*' . $task_content . ']';
                                        $serv->send($fd, $reply);
                                        $redis->set('PP' . $command_arr[1], $imei);
                                        $redis->set('PP' . $imei, $command_arr[1]);
                                    }
                                }
                            }
                        } else {
                            $reply = '[3G*' . $command_arr[1] . '*0001*pp*1]';
                            $serv->send($fd, $reply);
                        }

                    } elseif ($comment_type == 'WT') {//天气获取
                        //获取城市
                        $weatherStr = $this->city_weather($command_arr[1]);
                        //查找城市对应的天气数据，返回拼接好的数据
                        $weatherData = "WT," . $weatherStr;
                        $len = $this->get_len_16($weatherData);

                        $reply = '[3G*' . $command_arr[1] . '*' . $len . '*' . $weatherData . ']';
                        $serv->send($fd, $reply);
                    } elseif ($comment_type == 'TK2') {//碰碰对聊
                        //接收结果信息
                        if ($content[1] == '0') {//客户端接收失败
                        } elseif ($content[1] == '1') {//客户端接收成功
                        } else {//终端向平台发送数据
                            //平台先向终端回复结果
                            $reply = $command_arr[0] . '*' . $command_arr[1] . '*0005*TK2,1]';
                            $serv->send($fd, $reply);
                            //向好友存储碰碰语音
                            $redis = self::redis();
                            $ppimei = $redis->get('PP' . $command_arr[1]);
                            $reply = '[3G*' . $ppimei . '*' . $command_arr[2] . '*' . $command_arr[3] . ']';
                            $redis->sAdd('task:' . $ppimei, $reply);
                            unset($reply);
                        }
                    } elseif ($comment_type == 'TKQ') {//终端请求录音下发
                        $reply = $command_arr[0] . '*' . $command_arr[1] . '*0003*TKQ]';
                        $serv->send($fd, $reply);
                    } elseif ($comment_type == 'TKQ2') {//终端请求好友录音下发
                        $reply = $command_arr[0] . '*' . $command_arr[1] . '*0004*TKQ2]';
                        $serv->send($fd, $reply);
                    } elseif ($comment_type == 'CONFIG') {//config 不做处理
                        //不做处理
                    } elseif ($comment_type == 'UD') {//位置上报 不回复

                    } elseif ($comment_type == 'UD2') {//盲点补传数据 不回复

                    } elseif ($comment_type == 'AL') {//警报
                        $reply = $command_arr[0] . '*' . $command_arr[1] . '*0002*AL]';
                        $serv->send($fd, $reply);
                    }

                    //位置信息存储
                    if (isset($content[3])) {
                        if (($comment_type == 'AL') || ($comment_type == 'UD') || ($comment_type == 'UD2') || ($comment_type == 'WT')) {

                            $timestamp = strtotime(substr($content[1], 4, 2) . '-'
                                    . substr($content[1], 2, 2) . '-'
                                    . substr($content[1], 0, 2) . ' '
                                    . substr($content[2], 0, 2) . ':'
                                    . substr($content[2], 2, 2) . ':'
                                    . substr($content[2], 4, 2)) + 28800;
                            $db_data['create_time'] = date('Y-m-d H:i:s', $timestamp);
                            if ($content[3] == 'A') {//GPS位置信息
                                $db_data['type'] = 0;//0:GPS 1:LBS 2:未获取位置 3:gps原始坐标
                            } elseif ($content[3] == 'V') {//基站位置
                                $db_data['type'] = 1;
                            }

                            $db_data['imei'] = $command_arr[1];
                            $db_data['latitude'] = $content[4];//纬度
                            if ($db_data['latitude'] == 0) {
                                $db_data['type'] = 1;//基站定位
                            }
                            $db_data['D'] = $content[5];
                            $db_data['longitude'] = $content[6];//经度
                            $db_data['G'] = $content[7];
                            $db_data['direction'] = $content[9];//方位角
                            $db_data['gpsnum'] = $content[11];//卫星个数
                            $db_data['signal1'] = $content[12];//电量
                            $db_data['power'] = $content[13];//电量
                            $db_data['step'] = $content[14];//步数
                            $db_data['mcc'] = $content[19];// 国家码
                            $db_data['mnc'] = $content[20];//网号
                            $db_data['lac'] = $content[21];//连接基站位置区域码
                            $db_data['cid'] = $content[22];
                            $db_data['num'] = $content[17];//基站个数
                            $db_data['ta'] = $content[18];//GSM时延

                            //处理警报信息
                            if ($comment_type == 'AL') {
                                $war_str = base_convert($content[16], 16, 2);//（终端状态）16转2进制
                                if (strlen($war_str) != 32) {
                                    $war_str = str_pad($war_str, 32, 0, STR_PAD_LEFT);
                                }
                                $war_status = str_split(strrev($war_str), 1);
                                $db_data['is_sos'] = $war_status[16];
                                $db_data['is_lo_bat'] = $war_status[17];
                                if ($war_status[18]) {
                                    $db_data['is_rail'] = 1;
                                }
                                if ($war_status[19]) {
                                    $db_data['is_rail'] = 2;
                                }
                                $db_data['is_dismantle'] = $war_status[20];
                                $db_data['is_tumble'] = $war_status[21];
                                $db_data['is_heart'] = $war_status[22];
                            }
                            include_once 'db_connect.php';
                            $model = MyPDO::getInstance('sch_trail');
                            $model->insert($db_data);
                        }
                    }
                }
                //处理平台命令
                $taskArr = $this->get_task($command_arr[1]);
                foreach ($taskArr as $orders) {
                    $serv->send($fd, $orders);
                }
                unset($taskArr);
            } else {
                //非法的命令字串
                $serv->send($fd, 'param error');
            };

            unset($db_data);
            unset($serv);
        } catch (Exception $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }

    //检测自己好友关系
    private function checkPP($imei)
    {
        $db = new \PDO('mysql:host=59.110.0.250;dbname=school', 'root', 'Shuhaixinxi1502');
        $fri_info = $db->query("select f_id from sch_student where imei_id = '{$imei}' ")->fetch(\PDO::FETCH_ASSOC);
        if ($fri_info['f_id'] > 0) {
            unset($db);
            return false;
        } else {
            //可以交好友
            $ppkey = 'task:PP' . date('YmdHi');
            $redis = self::redis();
            $redis->sAdd($ppkey, $imei);
            return true;
        }
    }

    //解密amr文件 并存储
    private function save_amr($amr_content, $imei)
    {
        $amr_content = explode('[3G', $amr_content)[0];
        $amr_content_16 = bin2hex($amr_content);//将2进制文件转换成16进制
        //执行amr协议字串替换
        $amr_true = str_replace(array('7d01', '7d02', '7d03', '7d04', '7d05'), array('7d', '5b', '5d', '2c', '2a'), $amr_content_16);
        //将16进制转成2进制并存储
        $amr_true2 = hex2bin($amr_true);
        $file_name = __DIR__ . '/Chatfile/' . date('YmdHis') . rand(11111, 99999) . '.amr';
        file_put_contents($file_name, $amr_true2);

        //数据库存储
        $create_time = time();
        $db = new \PDO('mysql:host=59.110.0.250;dbname=school', 'root', 'Shuhaixinxi1502');
        $sql = "insert into sch_chatinfo (imei,contents,create_time,type) 
                value('{$imei}','{$file_name}','{$create_time}',3)";
        $res = $db->exec($sql);
        unset($db);
        return $res;
    }

    //获取16进制长度
    //获取字串16进制长度
    private function get_len_16($str)
    {
        $str_len = strlen($str);//原始字串长度
        $str_len_16 = base_convert($str_len, 10, 16);
        if (strlen($str_len_16) < 4) {
            $str_len_16 = str_pad($str_len_16, 4, '0', STR_PAD_LEFT);
        }
        return $str_len_16;
    }

    //获取城市
    public function city_weather($imei)
    {
        $dbh = new \PDO('mysql:host=59.110.0.250;dbname=school', 'root', 'Shuhaixinxi1502');
        $sql = "select s_id from sch_student where imei_id='$imei'";
        $s_id = $dbh->query($sql)->fetch(\PDO::FETCH_ASSOC)['s_id'];
        if ($s_id) {
            $sql = "select city_id from sch_school_information where s_id='$s_id'";
            $city_id = $dbh->query($sql)->fetch(\PDO::FETCH_ASSOC)['city_id'];
            $sql = "select region_name from sch_area_region where region_id='$city_id'";
            $city = $dbh->query($sql)->fetch(\PDO::FETCH_ASSOC)['region_name'];
            $city = rtrim($city, "市县区自治区特别行政区");
        } else {
            $city = '北京';
        }

        $sql = "select * from sch_school_weather where city like '%$city%'";
        $weatherList = $dbh->query($sql)->fetch(\PDO::FETCH_ASSOC);
        unset($dbh);
        $arr = $this->ch2arr($weatherList['weather']);

        $info = "";
        foreach ($arr as $value) {
            if (strlen($value) == 3) {
                $info .= $this->unicode_encode($value, "GB2312");
            } else {
                $info .= $this->unicode_encode($value, "UCS-2");
            }
        }
        $info = $info . ",";
        $weatherInfo = "," . $weatherList['weather'];
        $num = 0;
        if (strpos($weatherInfo, "晴")) {//天气编号
            $num = "0,";
        } elseif (strpos($weatherInfo, "阴") || strpos($weatherInfo, "多云")) {
            $num = "1,";
        } elseif (strpos($weatherInfo, "雨")) {
            $num = "2,";
        } elseif (strpos($weatherInfo, "雪")) {
            $num = "3,";
        }
        $temp = $weatherList['temp'] . ",";//当前温度
        $low = explode(",", $weatherList['high_low'])[1] . ",";//最低温度
        $high = explode(",", $weatherList['high_low'])[0] . ",";//最高温度
        $city = $this->unicode_encode($weatherList['city'], "UCS-2");//城市名（unicode）
        $str = date('Y-m-d,H:i:s') . "," . $info . $num . $temp . $low . $high . $city;
        return $str;

    }

    private function ch2arr($str)
    {
        $length = mb_strlen($str, 'utf-8');
        $array = [];
        for ($i = 0; $i < $length; $i++)
            $array[] = mb_substr($str, $i, 1, 'utf-8');
        return $array;
    }

    //utf-8转码
    private function unicode_encode($name, $iconv)
    {
        $name = iconv('UTF-8', $iconv, $name);
        $len = strlen($name);
        $str = '';
        for ($i = 0; $i < $len - 1; $i = $i + 2) {
            $c = $name[$i];
            $c2 = $name[$i + 1];
            $str[$i] = base_convert(ord($c), 10, 16) . base_convert(ord($c2), 10, 16);
            if (strlen($str[$i]) < 4) {
                $str[$i] = str_pad($str[$i], 4, 0, STR_PAD_LEFT);
            }

        }
        $str = implode('', $str);
        return $str;
    }


    //获取平台命令
    private function get_task($imei)
    {
        $taskArr = array();
        if ($imei) {
            $redis = self::redis();
            $key = 'task:' . $imei;
            $taskArr = $redis->sMembers($key);
            $redis->del($key);
        }
        return $taskArr;
    }

    public static function redis()
    {
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
        $redis->auth('School1502');
        return $redis;
    }

//客户端断开触发
    public function onClose($serv, $fd, $from_id)
    {
        echo "Client {$fd} close connection\n";
    }

}

/**
 * array(1) {
 * 'en4' =>
 * string(13) "172.16.71.149"
 * }
 */


// 启动服务器
$server = new Server();