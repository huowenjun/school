<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-学生卡设置
 */

namespace Admin\Controller\StuCard;

use Admin\Controller\BaseController;

class SthCardSetController extends BaseController
{

    public function index()
    {

        $this->display();
    }

    public function redis()
    {
        $redis = new \Redis();
        $redis->connect('39.107.98.114', 6379);
        $redis->auth('School1502');
        return $redis;
    }

    /*添加入库成功后，
    * 1.连接redis
    * 2.检查imei号在对应的手表或者学生卡数组是否有数据
    * 3.拼接数据，
    * 4.存入redis
    */
    protected function addRedis($imei_id, $order)
    {
        $redis = $this->redis();
        $redis->sAdd("task:$imei_id", $order);
    }

    //获取学生基本信息
    public function get_stu_data()
    {
        $ids = $this->authRule();
        $where = "";

        $stu_id = I('get.stu_id');
        $Student = D('Student');
        $StuCardSet = D('StuCardSet');
        if ($stu_id == 0 || empty($stu_id)) {
            $this->error('参数错误！');
        }
        $stuInfo = $Student->where('stu_id = ' . $stu_id)->find();
        $list = $StuCardSet->where(array("stu_id" => $stu_id))->getField('d_type,d_value');

        if (empty($stuInfo)) {
            $this->error('没有学生信息！');
        } else {
            $stuInfos['stu_id'] = $stuInfo['stu_id'];
            $stuInfos['stu_no'] = $stuInfo['stu_no'];
            $stuInfos['imei_id'] = $stuInfo['imei_id'];
            $stuInfos['rfid_id'] = $stuInfo['rfid_id'];
            $stuInfos['stu_name'] = $stuInfo['stu_name'];
            $stuInfos['phone'] = $stuInfo['stu_phone'];
            $stuInfos['config'] = $list;
        }
        $this->success($stuInfos);
    }

    //信息增加/编辑
    public function edit()
    {
        $where['stu_id'] = $data['stu_id'] = I('post.stu_id', 0, 'int');//学生id
        if (!$data['stu_id']) {//判断设置类型如喇叭音量等
            $this->error('请您先选择学生');
        }
        $where['d_type'] = $data['d_type'] = I('post.typeFalg', 0, 'string');//设置类型。如喇叭音量等
        if (!$data['d_type']) {//判断设置类型如喇叭音量等
            $this->error('设置类型不能为空');
        }
        $data['s_id'] = I('post.s_id');
        $data['a_id'] = I('post.a_id');
        $data['g_id'] = I('post.g_id');
        $data['c_id'] = I('post.c_id');
        $data['imei'] = I('post.imei');
        $StuCardSet = D('StuCardSet');
        $StuCardCommand = D('StuCardCommand');
        //获取设备绑定手机号
        $whereStr = array(
            'imei_id' => I('post.imei', '0', 'string'),
            'stu_id' => $data['stu_id'],
        );
        $stuList = D('Student')->field('stu_id,s_id,a_id,g_id,c_id,stu_no,stu_name,imei_id,sex,stu_phone,devicetype')->where($whereStr)->find();//devicetype设备类型1 is card, 2 is wetch
        if (empty($stuList['stu_phone'])) {
            $this->error('学生卡未绑定电话号码！');
        }
        $dataCard['stu_phone'] = $stuList['stu_phone'];
        $dataCard['d_type'] = $data['d_type'];
        $dataCard['imei'] = $data['imei'];
        $dataCard['status'] = 0;
        $dataCard['create_time'] = date("Y-m-d H:i:s");
        $devicetype = $stuList['devicetype'];
        $mse = "";
        if ($devicetype == 1) {//学生卡
            switch ($data['d_type']) {
                case 'whitelist': // 白名单
                    $data['d_value'] = I('post.whitelist');
                    $whiteInfo = explode(',', $data['d_value']);
                    foreach ($whiteInfo as $key => $value) {
                        $valInfo = explode(':', $value);//0姓名 1电话
                        $k = $key + 1;
                        $phone = $phone . $k . ',' . $valInfo[1] . ',' . ',';
                    }
                    $phone1 = rtrim($phone, ',');
                    //存入StuCardCommand表中
                    $dataCard['order'] = '*HQ,' . $data['imei'] . ',WLALL,' . date('His') . ',' . $phone1 . ',#';

                    $res = $StuCardCommand->add($dataCard);
                    $this->addRedis($data['imei'], $dataCard['order']);
                    break;

                case 'soundsize': // 喇叭音量
                    $data['d_value'] = I('post.soundsize');
                    //存入StuCardCommand表中
                    $dataCard['order'] = '*HQ,' . $data['imei'] . ',SPEAKER,' . date('His') . ',' . $data['d_value'] . '#';
                    $res = $StuCardCommand->add($dataCard);
                    $this->addRedis($data['imei'], $dataCard['order']);
                    break;

                case 'tealthtime': // 隐身时间(上课时间段)
                    $data['d_value'] = I('post.tealthtime');
                    $timeInfo = explode('|', $data['d_value']);
                    $tInfo = explode(',', str_replace(":", "", str_replace("-", "", $timeInfo[0])));
                    $wInfo = explode(',', $timeInfo[1]);
                    $wInfo1 = "";
                    $tInfo1 = "";
                    foreach ($wInfo as $key => $value) {
                        $k = $key + 1;
                        $val = 0;
                        if ($value == "ON") {
                            $val = 1;
                        }
                        $wInfo1 = $wInfo1 . $k . $val;
                    }
                    foreach ($tInfo as $key => $value) {
                        $tInfo1 = $tInfo1 . $value . $wInfo1 . ',';
                    }
                    $tealthInfo = rtrim($tInfo1, ',');
                    //存入StuCardCommand表中
                    $dataCard['order'] = '*HQ,' . $data['imei'] . ',CLASS,' . date('His') . ',' . $tealthInfo . '#';
                    $res = $StuCardCommand->add($dataCard);
                    $this->addRedis($data['imei'], $dataCard['order']);
                    break;

                case 'working': // 工作模式
                    $data['d_value'] = I('post.working');
                    $workInfo = explode(',', $data['d_value']);
                    //存入StuCardCommand表中
                    $dataCard['order'] = '*HQ,' . $data['imei'] . ',MODE,' . date('His') . ',' . $workInfo[0] . ',' . $workInfo[1] . '#';
                    $res = $StuCardCommand->add($dataCard);
                    $this->addRedis($data['imei'], $dataCard['order']);
                    break;

                case 'monitor': // 监听号码
                    $data['d_value'] = I('post.monitor');
                    $monitorInfo = explode(',', $data['d_value']);
                    //存入StuCardCommand表中
                    $dataCard['order'] = '*HQ,' . $data['imei'] . ',MONITOR,' . date('His') . ',' . $monitorInfo[1] . '#';
                    $res = $StuCardCommand->add($dataCard);
                    $this->addRedis($data['imei'], $dataCard['order']);
                    break;

                case 'domain': // 域名和端口
                    $data['d_value'] = I('post.domain');
                    $domainInfo = explode(',', $data['d_value']);
                    //存入StuCardCommand表中
                    $dataCard['order'] = '*HQ,' . $data['imei'] . ',S2DOMAIN,' . date('His') . ',' . $domainInfo[0] . ',' . $domainInfo[1] . ',5#';
                    $res = $StuCardCommand->add($dataCard);
                    $this->addRedis($data['imei'], $dataCard['order']);
                    break;

                case 'ipport': // IP和端口
                    $data['d_value'] = I('post.ipport');
                    $ipportInfo = explode(',', $data['d_value']);
                    $ip = str_replace(".", ",", $ipportInfo[0]);
                    $port = $ipportInfo[1];
                    //存入StuCardCommand表中
                    $dataCard['order'] = '*HQ,' . $data['imei'] . ',S23,' . date('His') . ',' . $ip . ',' . $port . ',5#';
                    $res = $StuCardCommand->add($dataCard);
                    $this->addRedis($data['imei'], $dataCard['order']);
                    break;

                case 'rfid': //RFID设置
                    $data['d_value'] = I('post.rfid');
                    // *HQ, MEID,RFID,seq,RFIDSEQ,IN_ID,OUT_ID#
                    // RFID：命令字；
                    // RFIDSEQ：天线组序号，如110043；
                    // IN_ID：进天线ID，如：112233；
                    // OUT_ID：出天线ID，如：223344；

                    //dump($data['d_value']);die;*HQ, MEID,RFID,seq,RFIDSEQ,IN_ID,OUT_ID#
                    $rfidInfo = explode(',', $data['d_value']); //0-RFIDSEQ：天线组序号   1-IN_ID：进天线ID   2-OUT_ID：出天线ID
                    //存入StuCardCommand表中
                    $dataCard['order'] = '*HQ,' . $data['imei'] . ',RFID,' . date('His') . ',' . $rfidInfo[0] . ',' . str_replace(":", "", $rfidInfo[1]) . ',' . str_replace(":", "", $rfidInfo[2]) . '#';
                    $res = $StuCardCommand->add($dataCard);
                    $this->addRedis($data['imei'], $dataCard['order']);
                    break;
                case 'rfidgps': //RFID GPS设置
                    $data['d_value'] = I('post.rfidgps');
                    $rfidInfo = explode(',', $data['d_value']); // 0天线组序号 1进id 2出id 3经度 4纬度 5半径
                    //存入StuCardCommand表中
                    $dataCard['order'] = '*HQ,'
                        . $data['imei']
                        . ',RFID,'
                        . date('His') . ','
                        . $rfidInfo[0] . ','
                        . $rfidInfo[1] . ','
                        . $rfidInfo[2] . ','
                        . $rfidInfo[3] . ','
                        . $rfidInfo[4] . ','
                        . $rfidInfo[5]
                        . '#';
                    $dataCard['d_type'] = 'rfid';
                    $data['d_type'] = 'rfid';
                    $res = $StuCardCommand->add($dataCard);
                    $this->addRedis($data['imei'], $dataCard['order']);
                    break;

                case 'callfilter': //来电过滤开关功能设置
                    $data['d_value'] = I('post.callfilter');
                    $val = 0;
                    if ($data['d_value'] == "ON") {
                        $val = 1;
                    }
                    //存入StuCardCommand表中
                    $dataCard['order'] = '*HQ,' . $data['imei'] . ',CALLFILTER,' . date('His') . ',' . $val . '#';
                    $res = $StuCardCommand->add($dataCard);
                    taskRedis($data['imei'], $dataCard['order']);
                    break;

                case 'calldisplay': //来电显示开关功能设置
                    $data['d_value'] = I('post.calldisplay');
                    $val = 0;
                    if ($data['d_value'] == "ON") {
                        $val = 1;
                    }
                    //存入StuCardCommand表中
                    $dataCard['order'] = '*HQ,' . $data['imei'] . ',CALLIDSTATUS,' . date('His') . ',' . $val . ',,#';
                    $res = $StuCardCommand->add($dataCard);
                    taskRedis($data['imei'], $dataCard['order']);
                    break;

                case 'wirelessatte': //无线考勤开关功能设置
                    $data['d_value'] = I('post.wirelessatte');
                    $val = 0;
                    if ($data['d_value'] == "ON") {
                        $val = 1;
                    }
                    //存入StuCardCommand表中
                    $dataCard['order'] = '*HQ,' . $data['imei'] . ',RFSWITCH,' . date('His') . ',' . $val . '#';
                    $res = $StuCardCommand->add($dataCard);
                    taskRedis($data['imei'], $dataCard['order']);
                    break;

                case 'stepcounter': //计步开关功能设置
                    $data['d_value'] = I('post.stepcounter');
                    $val = 0;
                    if ($data['d_value'] == "ON") {
                        $val = 1;
                    }
                    //存入StuCardCommand表中
                    $dataCard['order'] = '*HQ,' . $data['imei'] . ',STEPSWITCH,' . date('His') . ',' . $val . '#';
                    $res = $StuCardCommand->add($dataCard);
                    taskRedis($data['imei'], $dataCard['order']);
                    break;

                case 'inschool': //进校自动屏蔽开关设置
                    $data['d_value'] = I('post.inschool');
                    $val = $data['d_value'];
                    //存入StuCardCommand表中
                    $dataCard['order'] = '*HQ,' . $data['imei'] . ',CALLFUN,' . date('His') . ',' . $val . '#';
                    $res = $StuCardCommand->add($dataCard);
                    taskRedis($data['imei'], $dataCard['order']);
                    break;
            }
        } elseif ($devicetype == 2) {//手表
            switch ($data['d_type']) {
                case 'whitelist': // 白名单
                    //111:18511214006,:,:,:,:,:,:,:,:,:
                    $data['d_value'] = I('post.whitelist');
                    $whiteInfo = explode(',', $data['d_value']);
                    foreach ($whiteInfo as $key => $value) {
                        $phone_info = explode(':', $value);
                        if ($key < 3) {//SOS设置
                            $i = $key + 1;
                            $sos_info = '[3G*' . $data['imei'] . '*0010*SOS' . $i . ',' . $phone_info[1] . ']';
                            taskRedis($data['imei'], $sos_info);
                        }
                        //电话本设置 [3G*8800000015*len*PHB,号码,名字,号码,名字,号码,名字,号码,名字,号码,名字,号码,名字]
                        $phone_book_arr[] = $phone_info[1] . ',' . utf8_unicode($phone_info[0]);
                    }
                    $phone_book_str = 'PHB,' . implode(',', $phone_book_arr);
                    $phone_book_len_16 = get_len_16($phone_book_str);

                    //存入StuCardCommand表中
                    //[3G*8800000015*len*PHB,号码,名字,号码,名字,号码,名字,号码,名字,号码,名字,号码,名字]
                    $dataCard['order'] = '[3G*' . $data['imei'] . '*' . $phone_book_len_16 . '*' . $phone_book_str . ']';//电话本
                    $res = $StuCardCommand->add($dataCard);
                    taskRedis($data['imei'], $dataCard['order']);
                    break;

                case 'soundsize': // 喇叭音量
                    $this->error('您的设备暂不支持');
                    break;

                case 'tealthtime': // 隐身时间(上课时间段)[3g*5678901234*0037*SILENCETIME,21:10-7:30,21:10-7:30,21:10-7:30,21:10-7:30]
                    $data['d_value'] = I('post.tealthtime');//01:00-02:00,03:00-04:00,05:00-06:00|ON,OFF,ON,ON,OFF,OFF,ON
                    $tealthtime = explode(',', $data['d_value']);
                    $timeContent = 'SILENCETIME,' . $tealthtime[0] . ',' . $tealthtime[1] . ',' . rtrim($tealthtime[2], '|ON') . ',00:01-00:02';
                    $time_len_16 = get_len_16($timeContent);
                    $dataCard['order'] = '[3G*' . $data['imei'] . '*' . $time_len_16 . '*' . $timeContent . ']';
                    $res = $StuCardCommand->add($dataCard);
                    taskRedis($data['imei'], $dataCard['order']);
                    break;

                case 'working': // 工作模式
                    $data['d_value'] = I('post.working');

                    $workingInfo = explode(',', $data['d_value']);
                    if ($workingInfo[0] == 'm1') {//追踪模式:[3g*5678901234*0002*CR]
                        $dataCard['order'] = '[3G*' . $data['imei'] . '*0002*CR]';
                        taskRedis($data['imei'], $dataCard['order']);
                        $res = $StuCardCommand->add($dataCard);
                    }
                    if ($workingInfo[1] > 0) {//设置上报时间间隔[3g*8800000015*0009*UPLOAD,10]
                        $timeContent = 'UPLOAD,' . $workingInfo[1];
                        $time_len_16 = get_len_16($timeContent);
                        $dataCard['order'] = '[3G*' . $data['imei'] . '*' . $time_len_16 . '*' . $timeContent . ']';
                        taskRedis($data['imei'], $dataCard['order']);
                        $res = $StuCardCommand->add($dataCard);
                    }
                    //存入StuCardCommand表中
                    break;

                case 'monitor': // 监听号码 中心号码
                    $data['d_value'] = I('post.monitor');
                    $monitorInfo = explode(',', $data['d_value']);
                    //存入StuCardCommand表中 [3g*8800000015*0012*CENTER,00000000000]
                    $dataCard['order'] = '[3G*' . $data['imei'] . '*0012*CENTER,' . $monitorInfo[1] . ']';
                    $res = $StuCardCommand->add($dataCard);
                    taskRedis($data['imei'], $dataCard['order']);
                    break;

                case 'domain': // 域名和端口
                    $this->error('您的设备暂不支持');
                    break;

                case 'ipport': // IP和端口
                    $data['d_value'] = I('post.ipport');
                    $ipportInfo = explode(',', $data['d_value']);
                    $ip =  $ipportInfo[0];
                    $port = $ipportInfo[1];
                    //存入StuCardCommand表中 [3g*8800000015*0014*IP,113.81.229.9,5900]
                    $ip_content = 'IP,' . $ip . ',' . $port;
                    $ip_len_16 = get_len_16($ip_content);
                    $dataCard['order'] = '[3G*' . $data['imei'] . '*' . $ip_len_16 . '*' . $ip_content . ']';
                    $res = $StuCardCommand->add($dataCard);
                    taskRedis($data['imei'], $dataCard['order']);
                    break;

                case 'rfid': //RFID设置
                    $this->error('您的设备暂不支持');
                    break;
                case 'rfidgps': //RFID GPS设置
                    $this->error('您的设备暂不支持');
                    break;

                case 'callfilter': //来电过滤开关功能设置
                    $this->error('您的设备暂不支持');
                    break;

                case 'calldisplay': //来电显示开关功能设置
                    $this->error('您的设备暂不支持');
                    break;

                case 'wirelessatte': //无线考勤开关功能设置
                    $this->error('您的设备暂不支持');
                    break;

                case 'stepcounter': //计步开关功能设置
                    $data['d_value'] = I('post.stepcounter');
                    if ($data['d_value'] == 'ON') {
                        $num = 1;
                    } elseif ($data['d_value'] == 'OFF') {
                        $num = 0;
                    }
                    //存入StuCardCommand表中
                    $dataCard['order'] = '[3G*' . $data['imei_id'] . '*0004*PEDO,' . $num . ']';
                    taskRedis($data['imei'], $dataCard['order']);
                    $res = $StuCardCommand->add($dataCard);
                    break;

                case 'inschool': //进校自动屏蔽开关设置
                    $this->error('您的设备暂不支持');
                    break;
            }
        }

        if ($res == false) {
            $this->error($mse);
        } else {
            if (!$StuCardSet->create($data)) {
                $this->error($StuCardSet->getError());
            } else {

                $resinfo = $StuCardSet->where($where)->find();
                if (!empty($resinfo)) {
                    $ret = $StuCardSet->where($where)->save($data);
                } else {
                    $ret = $StuCardSet->add($data);
                }
                $content = '编辑成功';
                if ($ret === false) {
                    D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
                    $this->error($StuCardSet->getError());
                } else {
                    D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                    $this->success($content, U('/Admin/StuCard/SthCardSet/index'));
                }
            }
        }
    }

    //恢复出厂设置
    public function get_reset_config()
    {
        $imei = I('get.imei', 0, "string");
        $stuInfo = D('student')->field('stu_phone,devicetype')->where(array('imei_id' => $imei))->find();

        if (empty($stuInfo['stu_phone'])) {
            $this->error('学生卡未绑定电话号码！');
        }
        if ($stuInfo['devicetype'] == 1) {
            $command = '*HQ,' . $imei . ',S25,' . date('His') . '#';
        } elseif ($stuInfo['devicetype'] == 2) {
            //[3g*8800000015*0007*FACTORY]
            $command = '[3G*' . $imei . '0007*FACTORY]';
        }
        $data['stu_phone'] = $stuInfo['stu_phone'];
        $data['order'] = $command;
        $data['d_type'] = 'S25';
        $data['imei'] = $imei;
        $data['create_time'] = date("Y-m-d H:i:s");
        $data['msg_type'] = 1;
        $res = D('StuCardCommand')->add($data);
        taskRedis($data['imei'], $command);
        if (!$res) {
            $this->error('设备恢复失败');
        } else {
            $this->success('设备恢复成功');
        }
    }

    //重启终端
    public function get_restart_config()
    {
        $imei = I('get.imei', 0, 'string');
        $stuInfo = D('student')->field('stu_phone,devicetype')->where(array('imei_id' => $imei))->find();
        if (empty($stuInfo['stu_phone'])) {
            $this->error('学生卡未绑定电话号码！');
        }
        if ($stuInfo['devicetype'] == 1) {
            $command = '*HQ,' . $imei . ',R1,' . date('His') . '#';
        } elseif ($stuInfo['devicetype'] == 2) {
            $command = '[3G*' . $imei . '*0005*RESET]';
        }
        $data['stu_phone'] = $stuInfo['stu_phone'];
        $data['order'] = $command;
        $data['d_type'] = 'R1';
        $data['imei'] = $imei;
        $data['create_time'] = date("Y-m-d H:i:s");
        $data['msg_type'] = 1;
        $res = D('StuCardCommand')->add($data);
        taskRedis($imei, $command);
        if ($res == false) {
            $this->error('重启终端失败');
        } else {
            $this->success('重启终端成功');
        }
    }

    //树形菜单 支叶为学生
    public function get_tree_sth()
    {
        /*
        *如果是家长登录，直接显示学生信息
        */
        $Student = D('Student');
        $this->authRule();
        if ($this->getType() == 3) {
            $stuInfo = $Student->getInfo($stu_id);
        }

        $schoolInfo = D('SchoolInformation');

    }
}