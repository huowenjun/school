<?php
/**
 * author : rookiejin <mrjnamei@gmail.com>
 * createTime : 2018/1/4 10:26
 * description: php_oop.php - swoole-demo
 * 该代码是一份干净的tcp server 事件回调，
 * 没有任何对事件回调的业务处理 .
 * 以该代码为基准，后面的demo都在此基础上修改 .
 */

class Server
{

    /**
     * @var \swoole_server
     */
    public $server;

    /**
     * 配置项
     * @var $config array
     */
    public $config;

    /**
     * @var \Server
     */
    public static $_worker;

    /**
     * 存储pid文件的位置
     */
    public $pidFile;

    /**
     * worker 进程的数量
     * @var $worker_num
     */
    public $worker_num;

    /**
     * 当前进程的worker_id
     * @var $worker_id
     */
    public $worker_id;

    /**
     * task 进程数 + worker 进程数 = 总的服务进程
     * 给其他的进程发送消息:
     * for($i = 0 ; $i < $count ; $i ++) {
     *    if($i == $this->worker_id)  continue;表示是该进程
     *    $this->server->sendMessage($i , $data);
     * }
     * task 进程的数量
     * @var $task_num
     */
    public $task_num;

    /**
     * Server constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->server = new swoole_server($config ['host'], $config ['port']);
        $this->config = $config;
        $this->serverConfig();
        self::$_worker = &$this; // 引用
    }

    public function serverConfig()
    {
        $this->server->set($this->config['server']);
    }

    public function start()
    {
        // Server启动在主进程的主线程回调此函数
        $this->server->on("start", [$this, "onSwooleStart"]);
        // 此事件在Server正常结束时发生
        $this->server->on("shutDown", [$this, "onSwooleShutDown"]);
        //事件在Worker进程/Task进程启动时发生。这里创建的对象可以在进程生命周期内使用。
        $this->server->on("workerStart", [$this, "onSwooleWorkerStart"]);
        //  此事件在worker进程终止时发生。在此函数中可以回收worker进程申请的各类资源。
        $this->server->on("workerStop", [$this, "onSwooleWorkerStop"]);
        // worker 向task_worker进程投递任务触发
        $this->server->on("task", [$this, "onSwooleTask"]);
        // task_worker 返回值传给worker进程时触发
        $this->server->on("finish", [$this, "onSwooleFinish"]);
        // 当工作进程收到由 sendMessage 发送的管道消息时会触发onPipeMessage事件
        $this->server->on("pipeMessage", [$this, "onSwoolePipeMessage"]);
        // 当worker/task_worker进程发生异常后会在Manager进程内回调此函数
        $this->server->on("workerError", [$this, "onSwooleWrokerError"]);
        // 当管理进程启动时调用它，函数原型：
        $this->server->on("managerStart", [$this, "onSwooleManagerStart"]);
        // onManagerStop
        $this->server->on("managerStop", [$this, "onSwooleManagerStop"]);
        // 有新的连接进入时，在worker进程中回调。
        $this->server->on("connect", [$this, 'onSwooleConnect']);
        // 接收到数据时回调此函数，发生在worker进程中
        $this->server->on("receive", [$this, 'onSwooleReceive']);
        //CP客户端连接关闭后，在worker进程中回调此函数。函数原型：
        $this->server->on("close", [$this, "onSwooleClose"]);
        $this->server->start();
    }

    /**
     * @warning 进程隔离
     * 该步骤一般用于存储进程的 master_pid 和 manager_pid 到文件中
     * 本例子存储的位置是 __DIR__ . "/tmp/" 下面
     * 可以用 kill -15 master_pid 发送信号给进程关闭服务器，并且触发下面的onSwooleShutDown事件
     * @param $server
     */
    public function onSwooleStart($server)
    {
        $this->setProcessName('SwooleMaster');
        $debug = debug_backtrace();
        $this->pidFile = __DIR__ . "/temp/" . str_replace("/", "_", $debug[count($debug) - 1] ["file"] . ".pid");
        $pid = [$server->master_pid, $server->manager_pid];
        file_put_contents($this->pidFile, implode(",", $pid));
    }

    /**
     * @param $server
     * 已关闭所有Reactor线程、HeartbeatCheck线程、UdpRecv线程
     * 已关闭所有Worker进程、Task进程、User进程
     * 已close所有TCP/UDP/UnixSocket监听端口
     * 已关闭主Reactor
     * @warning
     * 强制kill进程不会回调onShutdown，如kill -9
     * 需要使用kill -15来发送SIGTREM信号到主进程才能按照正常的流程终止
     * 在命令行中使用Ctrl+C中断程序会立即停止，底层不会回调onShutdown
     */
    public function onSwooleShutDown($server)
    {
        echo "shutdown\n";
    }

    /**
     * @warning 进程隔离
     * 该函数具有进程隔离性 ,
     * {$this} 对象从 swoole_server->start() 开始前设置的属性全部继承
     * {$this} 对象在 onSwooleStart,onSwooleManagerStart中设置的对象属于不同的进程中.
     * 因此这里的pidFile虽然在onSwooleStart中设置了，但是是不同的进程，所以找不到该值.
     * @param \swoole_server $server
     * @param int $worker_id
     */
    public function onSwooleWorkerStart(swoole_server $server, $worker_id)
    {
        if ($this->isTaskProcess($server)) {
            $this->setProcessName('SwooleTask');
        } else {
            $this->setProcessName('SwooleWorker');
        }
        $debug = debug_backtrace();
        $this->pidFile = __DIR__ . "/temp/" . str_replace("/", "_", $debug[count($debug) - 1] ["file"] . ".pid");
        file_put_contents($this->pidFile, ",{$worker_id}", FILE_APPEND);
    }

    public function onSwooleWorkerStop($server, $worker_id)
    {
        echo "#worker exited {$worker_id}\n";
    }

    /**
     * @warning 进程隔离 在task_worker进程内被调用
     * worker进程可以使用swoole_server_task函数向task_worker进程投递新的任务
     * $task_id和$src_worker_id组合起来才是全局唯一的，不同的worker进程投递的任务ID可能会有相同
     * 函数执行时遇到致命错误退出，或者被外部进程强制kill，当前的任务会被丢弃，但不会影响其他正在排队的Task
     * @param $server
     * @param $task_id 是任务ID 由swoole扩展内自动生成，用于区分不同的任务
     * @param $src_worker_id 来自于哪个worker进程
     * @param $data 是任务的内容
     * @return mixed $data
     */
    public function onSwooleTask($server, $task_id, $src_worker_id, $data)
    {
        return $data;
    }

    public function onSwooleFinish()
    {

    }

    /**
     * 当工作进程收到由 sendMessage 发送的管道消息时会触发onPipeMessage事件。worker/task进程都可能会触发onPipeMessage事件。
     * @param $server
     * @param $src_worker_id 消息来自哪个Worker进程
     * @param $message 消息内容，可以是任意PHP类型
     */
    public function onSwoolePipeMessage($server, $src_worker_id, $message)
    {

    }

    /**
     * worker进程发送错误的错误处理回调 .
     * 记录日志等操作
     * 此函数主要用于报警和监控，一旦发现Worker进程异常退出，那么很有可能是遇到了致命错误或者进程CoreDump。通过记录日志或者发送报警的信息来提示开发者进行相应的处理。
     * @param $server
     * @param $worker_id 是异常进程的编号
     * @param $worker_pid  是异常进程的ID
     * @param $exit_code  退出的状态码，范围是 1 ～255
     * @param $signal 进程退出的信号
     */
    public function onSwooleWrokerError($server, $worker_id, $worker_pid, $exit_code, $signal)
    {
        echo "#workerError:{$worker_id}\n{$signal}";
    }

    /**
     *
     */
    public function onSwooleManagerStart()
    {
        $this->setProcessName('SwooleManager');
    }

    /**
     * @param $server
     */
    public function onSwooleManagerStop($server)
    {
        echo "#managerstop\n";
    }

    /**
     * 客户端连接
     * onConnect/onClose这2个回调发生在worker进程内，而不是主进程。
     * UDP协议下只有onReceive事件，没有onConnect/onClose事件
     * @param $server
     * @param $fd
     * @param $reactorId
     */
    public function onSwooleConnect($server, $fd, $reactorId)
    {
        echo "#connected\n";
    }

    /**
     * @param $server server对象
     * @param $fd 文件描述符
     * @param $reactorId reactor线程id
     */
    public function onSwooleReceive($serv, $fd, $reactorId,$data)
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

    /**
     * 连接断开，广播业务需要从redis | memcached | 内存 中删除该fd
     * @param $server
     * @param $fd
     * @param $reactorId
     */
    public function onSwooleClose($server, $fd, $reactorId)
    {
        echo "#swooleClosed\n";
    }

    public function setProcessName($name)
    {
        if (function_exists('cli_set_process_title')) {
            @cli_set_process_title($name);
        } else {
            @swoole_set_process_name($name);
        }
    }

    /**
     * 返回真说明该进程是task进程
     * @param $server
     * @return bool
     */
    public function isTaskProcess($server)
    {
        return $server->taskworker === true;
    }

    /**
     * main 运行入口方法
     */
    public static function main()
    {
        self::$_worker->start();
    }


    /*
     * 自定义函数部分
     * */
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
}

$config = ['server' =>
                ['worker_num' => 4,
                 "task_worker_num" => "20",
                'deamonize' => true,//守护进程化
                'log_file' => '/phpstudy/www/swoole/error2-1.log',
                'open_eof_check' => true,//打开buffer
                'package_eof' => "]",// 设置EOF尾部标识
                'heartbeat_check_interval' => 300,
                'heartbeat_idle_time' => 600,
                 "dispatch_mode" => 3],
            'host' => '0.0.0.0',
            'port' => 9501];
$server = new Server($config);
Server::main();