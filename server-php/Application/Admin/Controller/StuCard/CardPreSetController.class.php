<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-学校天线组设置
 */

namespace Admin\Controller\StuCard;

use Admin\Controller\BaseController;

class CardPreSetController extends BaseController
{

    public function index()
    {

        $this->display();
    }

    //获取学生基本信息
    public function get_stu_data()
    {
        $ids = $this->authRule();
        $where = "";
        if ($this->getType() == 1) {
            $where['s_id'] = $ids['s_id'];
        } elseif ($this->getType() == 3) {
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
        } elseif ($this->getType() == 4) {
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
            $where['stu_id'] = array('IN', $ids['stu_id']);
        } else {

        }
        $stu_id = I('get.stu_id');
        $Student = D('Student');
        $StuCardSet = D('StuCardSet');
        if ($stu_id == 0 || empty($stu_id)) {
            $this->error('参数错误！');
        }
        $stuInfo = $Student->getInfo($stu_id);
        $res = D('DeviceManage')->where(array("stu_id" => $stu_id))->find();
        $list = $StuCardSet->where(array("stu_id" => $stu_id))->getField('d_type,d_value');

        if (empty($stuInfo)) {
            $this->error('没有学生信息！');
        } else {
            $stuInfos['stu_id'] = $stuInfo['stu_id'];
            $stuInfos['stu_no'] = $stuInfo['stu_no'];
            $stuInfos['imei_id'] = $res['imei'];
            $stuInfos['rfid_id'] = $res['rfid'];
            $stuInfos['stu_name'] = $stuInfo['stu_name'];
            $stuInfos['phone'] = $res['phone'];
            $stuInfos['config'] = $list;
        }

        // $date['data1']=$stuInfos;
        // $date['data2']=$list;
        $this->success($stuInfos);
    }
    
    //信息增加/编辑
    public function edit()
    {
        $where['stu_id'] = I('post.stu_id');
        $where['d_type'] = I('post.typeFalg');//类型
        $data['s_id'] = I('post.s_id');
        $data['a_id'] = I('post.a_id');
        $data['g_id'] = I('post.g_id');
        $data['c_id'] = I('post.c_id');
        $data['imei'] = I('post.imei');
        $StuCardSet = D('StuCardSet');
        $StuCardCommand = D('StuCardCommand');
        $re = $StuCardSet->where($where)->find();
        $data['d_type'] = I('post.typeFalg');
        $data['stu_id'] = I('post.stu_id');
        //获取设备绑定手机号
        $whereStr = array(
            'imei_id' => I('post.imei'),
            'stu_id' => I('post.stu_id'),
        );
        $deviceInfo = D('Student')->where($whereStr)->find();
        $deviceInfo['phone'] = $deviceInfo['stu_phone'];
        if (empty($deviceInfo['phone'])) {
            $this->error('学生卡未绑定电话号码！');
        }


        $whereCommand['stu_phone'] = $deviceInfo['phone'];
        $whereCommand['d_type'] = $data['d_type'];
        $commandInfo = $StuCardCommand->where($whereCommand)->find();

        $dataCard['stu_phone'] = $deviceInfo['phone'];
        $dataCard['d_type'] = $data['d_type'];
        $dataCard['imei'] = $data['imei'];
        $dataCard['status'] = 0;
        $dataCard['create_time'] = date("Y-m-d H:i:s");

        $mse = "";
        $sendSmsInfo = 1;
        switch ($data['d_type']) {
            case 'whitelist': // 白名单
                $data['d_value'] = I('post.whitelist');
                $whiteInfo = explode(',', $data['d_value']);
                foreach ($whiteInfo as $key => $value) {
                    $valInfo = explode(':', $value);//0姓名 1电话
                    $k = $key + 1;
                    $phone = $phone . $k . ',' . $valInfo[1] . ',' . ',';
                    //$sendSmsInfo = $this->sendSms($deviceInfo['phone'],'*HQ,'.$data['imei'].',WHITELIST,'.date('His').','.$phone.'#');
                }
                $phone1 = rtrim($phone, ',');
                //存入StuCardCommand表中
                $dataCard['order'] = '*HQ,' . $data['imei'] . ',WLALL,' . date('His') . ',' . $phone1 . ',#';
                $res = $StuCardCommand->add($dataCard);
                break;

            case 'soundsize': // 喇叭音量
                $data['d_value'] = I('post.soundsize');
                //存入StuCardCommand表中
                $dataCard['order'] = '*HQ,' . $data['imei'] . ',SPEAKER,' . date('His') . ',' . $data['d_value'] . '#';
                $res = $StuCardCommand->add($dataCard);
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
                break;

            case 'working': // 工作模式
                $data['d_value'] = I('post.working');
                $workInfo = explode(',', $data['d_value']);
                //存入StuCardCommand表中
                $dataCard['order'] = '*HQ,' . $data['imei'] . ',MODE,' . date('His') . ',' . $workInfo[0] . ',' . $workInfo[1] . '#';
                $res = $StuCardCommand->add($dataCard);
                break;

            case 'monitor': // 监听号码
                $data['d_value'] = I('post.monitor');
                $monitorInfo = explode(',', $data['d_value']);
                //存入StuCardCommand表中
                $dataCard['order'] = '*HQ,' . $data['imei'] . ',MONITOR,' . date('His') . ',' . $monitorInfo[1] . '#';
                $res = $StuCardCommand->add($dataCard);
                break;

            case 'domain': // 域名和端口
                $data['d_value'] = I('post.domain');
                $domainInfo = explode(',', $data['d_value']);
                //存入StuCardCommand表中
                $dataCard['order'] = '*HQ,' . $data['imei'] . ',S2DOMAIN,' . date('His') . ',' . $domainInfo[0] . ',' . $domainInfo[1] . ',5#';
                $res = $StuCardCommand->add($dataCard);
                break;

            case 'ipport': // IP和端口
                $data['d_value'] = I('post.ipport');
                $ipportInfo = explode(',', $data['d_value']);
                $ip = str_replace(".", ",", $ipportInfo[0]);
                $port = $ipportInfo[1];
                //存入StuCardCommand表中
                $dataCard['order'] = '*HQ,' . $data['imei'] . ',S23,' . date('His') . ',' . $ip . ',' . $port . ',5#';
                $res = $StuCardCommand->add($dataCard);
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
                break;

            case 'inschool': //进校自动屏蔽开关设置
                $data['d_value'] = I('post.inschool');
                $val = $data['d_value'];
                //存入StuCardCommand表中
                $dataCard['order'] = '*HQ,' . $data['imei'] . ',CALLFUN,' . date('His') . ',' . $val . '#';
                $res = $StuCardCommand->add($dataCard);
                break;
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
        $imei = I('get.imei');
        $deviceInfo = D('DeviceManage')->where(array('imei' => $imei))->find();
        if (empty($deviceInfo['phone'])) {
            $this->error('学生卡未绑定电话号码！');
        }
        $command = '*HQ,' . $imei . ',S25,' . date('His') . '#';
//        $sendSmsInfo = $this->sendSms($deviceInfo['phone'], $command);
//        if ($sendSmsInfo == false) {
//            $data['status'] = 2;
//        } else {
//            $data['status'] = 1;
//        }
        $data['stu_phone'] = $deviceInfo['phone'];
        $data['order'] = $command;
        $data['d_type'] = 'S25';
        $data['imei'] = $imei;
        $data['create_time'] = date("Y-m-d H:i:s");
        $data['msg_type'] = 1;
        $res = D('StuCardCommand')->add($data);

        if ($res == false) {
            $this->error('设备恢复失败');
        } else {
            $this->success('设备恢复成功');
        }
    }

    //重启终端
    public function get_restart_config()
    {
        $imei = I('get.imei');
        $deviceInfo = D('DeviceManage')->where(array('imei' => $imei))->find();
        if (empty($deviceInfo['phone'])) {
            $this->error('学生卡未绑定电话号码！');
        }
        $command = '*HQ,' . $imei . ',R1,' . date('His') . '#';
//        $sendSmsInfo = $this->sendSms($deviceInfo['phone'], $command);
//        if ($sendSmsInfo == false) {
//            $data['status'] = 2;
//        } else {
//            $data['status'] = 1;
//        }
        $data['stu_phone'] = $deviceInfo['phone'];
        $data['order'] = $command;
        $data['d_type'] = 'R1';
        $data['imei'] = $imei;
        $data['create_time'] = date("Y-m-d H:i:s");
        $data['msg_type'] = 1;
        $res = D('StuCardCommand')->add($data);

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