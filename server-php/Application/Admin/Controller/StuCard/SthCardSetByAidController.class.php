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

class SthCardSetByAidController extends BaseController
{
    public function index()
    {
        $this->display();
    }
    public static function redis(){
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
        $redis->auth('School1502');
        return $redis;
    }
    /*添加入库成功后，
    * 1.连接redis
    * 2.检查imei号在对应的手表或者学生卡数组是否有数据
    * 3.拼接数据，
    * 4.存入redis
    */
    protected function addRedis($imei_id,$order){
        $redis = self::redis();
        $redis->sAdd("task:$imei_id",$order);
    }
    //学生卡批量设置
    public function edit()
    {
        ini_set('max_execution_time', '0');   //设置PHP.INI执行时间
        $aId               = I('post.a_id',0,'int');      //获取校区ID  139
        $typeFalg          = I('post.typeFalg',0,'string');//设置类型
        $StuCardSet        = M('StuCardSet');
        $StuCardCommand    = M('StuCardCommand');
        if (!$aId) {  //判断用户是否选择了校区
            $this->error('请您选择要操作的校区.');
        }
        if (!$typeFalg) {//判断设置类型如喇叭音量等
            $this->error('设置类型不能为空.');
        }
        //获取校区下所有绑卡学生
        $stuList = M('Student')->field('stu_id,s_id,a_id,g_id,c_id,stu_no,stu_name,imei_id,sex,stu_phone,devicetype')->where("a_id = '$aId' and imei_id > 0 and stu_phone > 0")->select();
        if (empty($stuList)) {
            $this->error('学生卡未绑定imei');
        }
//        $deviceType                  = $stuList[0]['devicetype'];//获取班级某一位学生的佩戴设备
        $dataCard['d_type']         = $typeFalg;
        $dataCard['status']         = 0;
        $dataCard['create_time']    = date("Y-m-d H:i:s");
        //开启事务处理
        $StuCardCommand->startTrans();

            foreach ($stuList as $key => $stuInfo) {
                $dataCard['stu_phone']   = $stuInfo['stu_phone'];
                $dataCard['imei']        = $stuInfo['imei_id'];
                if($stuInfo['devicetype']==1){ //1 is card
                    switch ($typeFalg) {
                        case 'soundsize': // 喇叭音量
                            $data['d_value'] = I('post.soundsize');//2,1,0,对应的大中小
                            //存入StuCardCommand表中
                            $dataCard['order'] = '*HQ,' . $stuInfo['imei_id'] . ',SPEAKER,' . date('His') . ',' . $data['d_value'] . '#';
                            break;

                        case 'tealthtime': // 上课时间段 ，隐身时间
                            //00:00-01:00,02:00-03:00,05:00-07:00|ON,OFF,ON,OFF,ON,OFF,ON
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
                            $dataCard['order'] = '*HQ,' . $stuInfo['imei_id'] . ',CLASS,' . date('His') . ',' . $tealthInfo . '#';
                            break;

                        case 'working': // 工作模式
                            $data['d_value'] = I('post.working');
                            $workInfo = explode(',', $data['d_value']);
                            //存入StuCardCommand表中
                            $dataCard['order'] = '*HQ,' . $stuInfo['imei_id'] . ',MODE,' . date('His') . ',' . $workInfo[0] . ',' . $workInfo[1] . '#';
                            break;

                        case 'monitor': // 监听号码
                            $data['d_value'] = I('post.monitor');
                            $monitorInfo = explode(',', $data['d_value']);
                            //存入StuCardCommand表中
                            $dataCard['order'] = '*HQ,' . $stuInfo['imei_id'] . ',MONITOR,' . date('His') . ',' . $monitorInfo[1] . '#';
                            break;

                        case 'domain': // 域名和端口
                            $data['d_value'] = I('post.domain');
                            $domainInfo = explode(',', $data['d_value']);
                            //存入StuCardCommand表中
                            $dataCard['order'] = '*HQ,' . $stuInfo['imei_id'] . ',S2DOMAIN,' . date('His') . ',' . $domainInfo[0] . ',' . $domainInfo[1] . ',5#';
                            break;

                        case 'ipport': // IP和端口
                            $data['d_value'] = I('post.ipport');
                            $ipportInfo = explode(',', $data['d_value']);
                            $ip = str_replace(".", ",", $ipportInfo[0]);
                            $port = $ipportInfo[1];
                            //存入StuCardCommand表中
                            $dataCard['order'] = '*HQ,' . $stuInfo['imei_id'] . ',S23,' . date('His') . ',' . $ip . ',' . $port . ',5#';
                            break;

                        case 'rfid': //RFID设置
                            $data['d_value'] = I('post.rfid');
                            //dump($data['d_value']);die;*HQ, MEID,RFID,seq,RFIDSEQ,IN_ID,OUT_ID#
                            $rfidInfo = explode(',', $data['d_value']); //0-RFIDSEQ：天线组序号   1-IN_ID：进天线ID   2-OUT_ID：出天线ID
                            //存入StuCardCommand表中
                            $dataCard['order'] = '*HQ,' . $stuInfo['imei_id'] . ',RFID,' . date('His') . ',' . $rfidInfo[0] . ',' . str_replace(":", "", $rfidInfo[1]) . ',' . str_replace(":", "", $rfidInfo[2]) . '#';
                            break;
                        case 'rfidgps': //RFID GPS设置
                            $data['d_value'] = I('post.rfidgps');
                            $rfidInfo = explode(',', $data['d_value']); // 0天线组序号 1进id 2出id 3经度 4纬度 5半径
                            //存入StuCardCommand表中
                            $dataCard['order'] = '*HQ,'
                                . $stuInfo['imei_id']
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
                            break;

                        case 'callfilter': //来电过滤开关功能设置
                            $data['d_value'] = I('post.callfilter');
                            $val = 0;
                            if ($data['d_value'] == "ON") {
                                $val = 1;
                            }
                            //存入StuCardCommand表中
                            $dataCard['order'] = '*HQ,' . $stuInfo['imei_id'] . ',CALLFILTER,' . date('His') . ',' . $val . '#';
                            break;

                        case 'calldisplay': //来电显示开关功能设置
                            $data['d_value'] = I('post.calldisplay');
                            $val = 0;
                            if ($data['d_value'] == "ON") {
                                $val = 1;
                            }
                            //存入StuCardCommand表中
                            $dataCard['order'] = '*HQ,' . $stuInfo['imei_id'] . ',CALLIDSTATUS,' . date('His') . ',' . $val . ',,#';
                            break;

                        case 'wirelessatte': //无线考勤开关功能设置
                            $data['d_value'] = I('post.wirelessatte');
                            $val = 0;
                            if ($data['d_value'] == "ON") {
                                $val = 1;
                            }
                            //存入StuCardCommand表中
                            $dataCard['order'] = '*HQ,' . $stuInfo['imei_id'] . ',RFSWITCH,' . date('His') . ',' . $val . '#';
                            break;

                        case 'stepcounter': //计步开关功能设置
                            $data['d_value'] = I('post.stepcounter');
                            $val = 0;
                            if ($data['d_value'] == "ON") {
                                $val = 1;
                            }
                            //存入StuCardCommand表中
                            $dataCard['order'] = '*HQ,' . $stuInfo['imei_id'] . ',STEPSWITCH,' . date('His') . ',' . $val . '#';
                            break;

                        case 'inschool': //进校自动屏蔽开关设置
                            $data['d_value'] = I('post.inschool');
                            $val = $data['d_value'];
                            //存入StuCardCommand表中
                            $dataCard['order'] = '*HQ,' . $stuInfo['imei_id'] . ',CALLFUN,' . date('His') . ',' . $val . '#';
                            break;
                    }
                }elseif ($stuInfo['devicetype']==2){
                    switch ($typeFalg) {
                        case 'soundsize': // 喇叭音量

                            break;
                        case 'tealthtime': // 上课时间段

                            break;
                        case 'working': // 工作模式
                            //存入StuCardCommand表中
                            $dataCard['order'] = '[3g*'.$stuInfo['imei_id'].'*0002*CR]';//[3g*5678901234*0002*CR
                            break;
                        case 'domain': // 域名和端口

                            break;
                        case 'ipport': // IP和端口	 192.22.22.22,22
                            $data['d_value'] = I('post.ipport');
                            $ipportInfo = explode(',', $data['d_value']);
                            $ip = $ipportInfo[0];
                            $port = $ipportInfo[1];
                            //存入StuCardCommand表中
                            $dataCard['order'] ='[3G*'.$stuInfo['imei_id'].'*0014*IP,'.$ip.','.$port.']';
                            break;
                        case 'rfid': //RFID设置

                            break;
                        case 'rfidgps': //RFID GPS设置

                            break;
                        case 'callfilter': //来电过滤开关功能设置

                            break;
                        case 'calldisplay': //来电显示开关功能设置

                            break;
                        case 'wirelessatte': //无线考勤开关功能设置

                            break;
                        case 'stepcounter': //计步开关功能设置 0关闭1打开
                            $data['d_value'] = I('post.stepcounter');
                            if($data['d_value']=='ON'){
                                $num=1;
                            }elseif ($data['d_value']=='OFF'){
                                $num=0;
                            }
                            //存入StuCardCommand表中
                            $dataCard['order'] = '[3G*'.$stuInfo['imei_id'].'*0004*PEDO,'.$num.']';
                            break;
                        case 'inschool': //进校自动屏蔽开关设置

                            break;

                    }
                }
                if (!$StuCardCommand->create($dataCard)) {
                    $this->error($StuCardCommand->getError());
                }

                $commandId = $StuCardCommand->add($dataCard);
                taskRedis($dataCard['imei'],$dataCard['order']);
                //学生卡设置项
                $dataCard['s_id'] = $stuInfo['s_id'];
                $dataCard['a_id'] = $stuInfo['a_id'];
                $dataCard['g_id'] = $stuInfo['g_id'];
                $dataCard['c_id'] = $stuInfo['c_id'];
                $dataCard['stu_id'] = $stuInfo['stu_id'];
                $dataCard['d_value'] = $data['d_value'];

                if (!$StuCardSet->create($dataCard)) {
                    $this->error($StuCardSet->getError());
                }

                //检测是cardSet表否设置过
                $where['stu_id'] = $stuInfo['stu_id'];
                $where['d_type'] = $typeFalg;//类型
                $setId = $StuCardSet->where($where)->getField('id');
                if ($setId) {
                    $where['id'] = $setId;
                    $cardSetId = $StuCardSet->where($where)->save($dataCard);
                } else {
                    $cardSetId = $StuCardSet->add($dataCard);
                }
                if (!$commandId || !$cardSetId) {
                    $StuCardCommand->rollback();
                    $this->error('网络延迟,稍后再试.');
                }
            }
        $StuCardCommand->commit();
        $content = '编辑成功';
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
        $this->success($content, U('/Admin/StuCard/SthCardSet/index'));
    }


    //恢复出厂设置
    public function get_reset_config()
    {
        ini_set('max_execution_time', '0');
        $aId            = I('get.a_id');
        $StuCardCommand = D('StuCardCommand');
        if (!$aId) {
            $this->error('请您选择要操作的校区.');
        }
        //获取校区下所有绑卡学生
        $stuList = M('Student')
                ->field('stu_id,s_id,a_id,g_id,c_id,stu_no,stu_name,imei_id,sex,stu_phone,devicetype')
                ->where("a_id = '$aId' and imei_id > 0 and stu_phone > 0")
                ->select();

        if (empty($stuList)) {
            $this->error('学生卡未绑定imei');
        }
//        $deviceType                  = $stuList[0]['devicetype'];//获取班级某一位学生的佩戴设备

        $data['create_time'] = date("Y-m-d H:i:s");
        $data['d_type']      = 'S25';
        //开启事务处理
        $StuCardCommand->startTrans();

            foreach ($stuList as $key => $value) {
                if($value['deviceType']==1){
                    $command = '*HQ,' . $value['imei_id'] . ',S25,' . date('His') . '#';
                    $data['stu_phone'] = $value['stu_phone'];
                    $data['order'] = $command;
                    $data['imei'] = $value['imei_id'];
                    $commandId = $StuCardCommand->add($data);
                    taskRedis($value['imei_id'], $command);
                    if (!$commandId) {
                        $StuCardCommand->rollback();
                        $this->error('网络延迟,稍后再试.');
                    }
                }
                elseif ($value['deviceType']==2){
                    $command = '[3G*'.$value['imei_id'].'0007*FACTORY]';
                    $data['stu_phone'] = $value['stu_phone'];
                    $data['order'] = $command;
                    $data['imei'] = $value['imei_id'];
                    $commandId = $StuCardCommand->add($data);
                    //数据入redis
                    taskRedis($value['imei_id'], $command);
                    if (!$commandId) {
                        $StuCardCommand->rollback();
                        $this->error('网络延迟,稍后再试.');
                    }
                }
            }
        $StuCardCommand->commit();
        $this->success('设备恢复成功');
    }

    //重启终端
    public function get_restart_config()
    {
        ini_set('max_execution_time', '0');
        $aId            = I('get.a_id');
        $StuCardCommand = D('StuCardCommand');
        if (!$aId) {
            $this->error('请您选择要操作的校区.');
        }
        //获取校区下所有绑卡学生
        $stuList = M('Student')
                ->field('stu_id,s_id,a_id,g_id,c_id,stu_no,stu_name,imei_id,sex,stu_phone,devicetype')
                ->where("a_id = '$aId' and imei_id > 0 and stu_phone > 0")
                ->select();

        if (empty($stuList)) {
            $this->error('学生卡未绑定imei');
        }
//        $deviceType                  = $stuList[0]['devicetype'];//获取班级某一位学生的佩戴设备
        $data['create_time']        = date("Y-m-d H:i:s");
        $data['d_type']             = 'R1';
        //开启事务处理
        $StuCardCommand->startTrans();

            foreach ($stuList as $key => $value) {
                if($value['deviceType']==1){
                    $command = '*HQ,' . $value['imei_id'] . ',R1,' . date('His') . '#';
                    $data['order'] = $command;
                    $data['stu_phone'] = $value['stu_phone'];
                    $data['imei'] = $value['imei_id'];
                    $commandId = $StuCardCommand->add($data);
                    //数据入redis
                    taskRedis($value['imei_id'], $command);
                    if (!$commandId) {
                        $StuCardCommand->rollback();
                        $this->error('网络延迟,稍后再试.');
                    }
                }elseif ($value['deviceType']==2){
                    $command = '[3G*'.$value['imei_id'].'*0005*RESET]';
                    $data['order'] = $command;
                    $data['stu_phone'] = $value['stu_phone'];
                    $data['imei'] = $value['imei_id'];
                    $commandId = $StuCardCommand->add($data);
                    taskRedis($value['imei_id'], $command);
                    if (!$commandId) {
                        $StuCardCommand->rollback();
                        $this->error('网络延迟,稍后再试.');
                    }
                }

            }


        $StuCardCommand->commit();
        $this->success('重启终端成功');
    }

    //获取学生卡设置项的值
    public function get_stu_data()
    {
        $aId = I('get.a_id');
        if (!$aId) {
            $this->error('请您选择要操作的校区.');
        }
        $StuCardSet = M('StuCardSet');
        if (!$aId) {
            $this->error('请选择要操作的校区！');
        }

        $list = $StuCardSet->where(array("a_id" => $aId))->getField('d_type,d_value');

        if (empty($list)) {
            $this->error('没有学生信息！');
        } else {
            $stuInfos['a_id'] = $aId;
            $stuInfos['config'] = $list;
        }
        $this->success($stuInfos);
    }
}