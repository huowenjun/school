<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2018/4/23
 * Time: 14:09
 */

namespace Api\Controller;

use Api\Controller\BaseController;
use Think\Controller;
use Think\Model;

class WatchController extends BaseController//BaseController
{
    //获取某一天的计步
    public function GetStepNum()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $GetImei = I('post.imei');//获取传过来的IMEI
        if (!$GetImei) {//IMEI容错
            $this->error('imei不能为空');
        }
        $GetTime = I('post.date');//获取传过来的时间
        if (!$GetTime) {//时间容错
            $this->error('时间不能为空');
        }
        // $DbTableGet = M('trail');//初始化表
        /***********  根据目标数据做数据的筛选   **************/
        //按照条件查找数据
//        $where['imei'] = $GetImei;
//        $where['create_time'] = array('like', $GetTime . '%');
//        //$DbData = $DbTableGet->where($where)->order('tr_id desc')->getField('step');//查找到最后一条数据
//        $DbData_info = $DbTableGet->field('step')->where($where)->find();
        $redis = redis_instance($host = '39.107.98.114', $port = '6379', $pwd = 'School1502');
        $DbData_info['step'] = $redis->get('Step:' . $GetTime . ':' . $GetImei);
        if (empty($DbData_info['step'])) {
            $DbData_info['step'] = 0;
            $this->success($DbData_info);
        } else {
            $this->success($DbData_info);
        }

    }

    //获取碰碰交好友的列表
    public function friendList()
    {
        //接收imei号
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $user_id = $this->getUserId();
        //通过access表查找所有相关的userid 的stuid
        $allStuInfo = M('StudentGroupAccess')->where(['user_id' => $user_id])->getField('stu_id', true);
        foreach ($allStuInfo as $key => $stu_id) {
            //一个孩子的信息
            $stu_info = M('student')->field('imei_id,photo,stu_name,f_id')->where(['stu_id' => $stu_id])->find();//单个的stu_id的f_id所有信息
            if (!$stu_info['f_id']) {
                $stu_info['f_id'] = array();
            }
            $fri_imei = explode(',', $stu_info['f_id']);

            unset($stu_info['f_id']);
            $friend_info = array();
            foreach ($fri_imei as $value) {
                $friend_info[] = M('student')->field('imei_id,photo,stu_name')->where(['imei_id' => $value])->find();
            }
            $stu_info['friList'] = $friend_info;
            if (empty($friend_info)) {
                continue;
            } else {
                $data[] = $stu_info;
            }
        }
        $this->success($data);
    }

    //删除好友
    public function delFriend()
    {
        //接收imei号
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }

        $friimei = I('post.fri_imei');//获取当前传过来的IMEI
        $myimei = I('post.my_imei');
        $flag = 1;//默认'我'与好友都在相互的列表里面
        /**********  确认我里面有好友  **********/
        $where['imei_id'] = $myimei;;
        $where['f_id'] = $friimei;
        $ret = M('student')->where($where)->find();//查看student表里面是否有myimei数据
        if (empty($ret)) {
            $this->error('您不存在好友');
        }
        /**********  查找好友里面是否有我 没有flag=0 **********/
        $where['imei_id'] = $friimei;
        $where['f_id'] = $myimei;
        $friInfo = M('student')->where($where)->find();//查看student表里面是否有myimei数据
        if (empty($friInfo)) {
            $flag = 0;
        }
        $saveInfo['f_id'] = '';
        if ($flag == 1) {//如果是相互存在的
            $map['imei_id'] = array('in', array($myimei, $friimei));
            $ret = M('student')->where($map)->save($saveInfo);
            taskRedis($friimei, "[3g*$friimei*0003*PPR]");
        } elseif ($flag == 0) {
            $whereStr['imei_id'] = $myimei;
            $ret = M('student')->where($whereStr)->save($saveInfo);
        }
        taskRedis($myimei, "[3g*$myimei*0003*PPR]");
        if (!$ret) {
            $this->error('删除失败');
        }
        $this->success('删除成功');
    }

    //设置指定的IMEI号关机
    public function SetShutDown()
    {
        //将imei 所对应的命令[3G*5678901234*0008*POWEROFF] 写入redis task:imei=>xxxx  sadd set
        //'39.107.98.114'
        //获取IMEI和关机状态
        $GetImei = I('post.imei');//获取IMEI
        if (!$GetImei) {//IMEI容错
            $this->error('imei不能为空');
        }
        $GetState = I('post.state', 1, 'int');//获取状态
        if (!$GetState) {//STATE容错
            $this->error('State不能为空');
        }
        $ret = save_command($GetImei, 'poweroff', $GetState);
        if ($ret['flag'] == 0) {
            $this->error($ret['message']);
        } else {
            $this->success('正在关机');
        }
    }

    //设置指定IMEI的闹钟时间
    public function SetAlarmTime()
    {
        //命令格式 [3g*5678901234*0018*REMIND,08:10-1-1,08:10-1-2, 08:10-1-3-0111110]
        $GetImei = I('post.imei');//获取IMEI
        if (!$GetImei) {//IMEI容错
            $this->error('imei不能为空');
        }
        $GetAlarmTime = I('post.AlarmTime');//获取时间
        if (!$GetAlarmTime) {//TIME容错
            $this->error('TIME不能为空');
        }
        $ret = save_command($GetImei, 'clock', $GetAlarmTime);
        if ($ret['flag'] == 0) {
            $this->error($ret['message']);
        } else {
            $this->success('设置闹钟成功');
        }
    }

    //返回给APP时间
    public function UpAlarmTime()
    {
        $GetImei = I('post.imei');//获取IMEI
        if (!$GetImei) {//IMEI容错
            $this->error('imei不能为空');
        }
        $StuCardSet = D('StuCardSet');//实例化数据库
        $where['imei'] = $GetImei;//获取IMEI
        $where['d_type'] = 'Clock';//获取IMEI
        $data = $StuCardSet->where($where)->getField('d_value');
        if (!$data) {
            $this->error("没有该数据");
        }
        $this->success($data);
    }

    //设置语音监护功能
    public function SetMonitor()
    {
        //[3g*8800000015*0013*MONITOR,13100010002]
        $GetImei = I('post.imei');//获取IMEI
        if (!$GetImei) {//IMEI容错
            $this->error('imei不能为空');
        }
        $GetPhoneNum = I('post.phone');//获取电话
        if (!$GetPhoneNum) {//TIME容错
            $this->error('电话号码不能为空');
        }

        $ret = save_command($GetImei, 'monitor', $GetPhoneNum);
        if ($ret['flag'] == 0) {
            $this->error($ret['message']);
        } else {
            $this->success('正在进行监听');
        }

    }
    /***********   数据导入接口  **************/
    //获取用户数据
    public function BindDevice()
    {
        $GetImei = I('post.imei_id');//获取IMEI
        if (!$GetImei) {//IMEI容错
            $this->error('imei不能为空');
        }
        //判断imei是否合法
        $deviceData = D('device')->where(['imei' => $GetImei])->getField('devicetype');
        if (!$deviceData) {
            $this->error('imei数据不合法');
        }

        $GetSataus = I('post.status', 1, 'int');//获取状态,默认绑定
        $GetId = I('post.user_id');//获取用户ID
        if (!$GetId) {//user_id容错
            $this->error('用户ID不能为空');
        }
        $GetPhone = I('post.stu_phone');//获取学生电话
        $GetName = I('post.stu_name', "宝宝");//获取学生姓名
        $Stu = M('Student');
        $StuWhere['imei_id'] = $GetImei;
        $stu_info = $Stu->field('stu_id')->where($StuWhere)->find();
        if (empty($stu_info)) {
            //将数据存入学生表里面
            $StuData['imei_id'] = $GetImei;
            $StuData['devicetype'] = $deviceData;
            $StuData['status'] = $GetSataus;
            $StuData['user_id'] = $GetId;
            $StuData['stu_phone'] = $GetPhone;
            $StuData['stu_name'] = $GetName;
            $StuData['create_time'] = date("Y-m-d H:i:s");
            $stu_id = $Stu->add($StuData);//返回主id
            $stu_info['stu_id'] = $stu_id;
        } else {//设备号已经存在
            $stu_id = $stu_info['stu_id'];
        }
        //插入gruopaccess表
        $StuGroup = M('StudentGroupAccess');
        $group_info = $StuGroup->field('id')->where("stu_id = '{$stu_id}' and user_id = '{$GetId}'")->find();
        if (!$group_info) {//不存在该数据,则进行插入
            $StuGruopData['stu_id'] = $stu_id;
            $StuGruopData['user_id'] = $GetId;
            $bool = $StuGroup->add($StuGruopData);
            easemob_add_user('', $GetImei);
            if (!$bool) {
                $this->error('网络异常');
            }
            //修改device绑定状态
            D('device')->where(['imei' => $GetImei])->save(['status' => 1]);
            S('stuinfo:' . $GetImei, NULL);//清空缓存
        } else {
            $this->error('已经绑定过该设备');
        }
        $this->success($stu_info);
    }

    //解绑
    public function UnBindDevice()
    {
        $Stu_id = I('post.stu_id');//获取学生
        if (!$Stu_id) {//IMEI容错
            $this->error('stu_id不能为空');
        }
        $User_id = I('post.user_id');//获取用户ID
        if (!$User_id) {//devicetype容错
            $this->error('User_id不能为空');
        }
        //实例化数据库
        $StuGroup = M('StudentGroupAccess');
        $where['stu_id'] = $Stu_id;
        $where['user_id'] = $User_id;
        $stu_info = $StuGroup->where($where)->find();
        if (empty($stu_info)) {
            $this->success("没有该数据");
        } else {
            $ret = $StuGroup->where($where)->delete();
            if (!$ret) {
                $this->error("没有删除任何数据");
            }

            $this->success("删除成功");
        }
    }

    //  设置sos
    public function SetSos()
    {
        $GetImei = I('post.imei');//获取IMEI
        if (!$GetImei) {//IMEI容错
            $this->error('imei不能为空');
        }
        $imeiInfo = explode(',', $GetImei);
        $GetSos = I('post.sos');//获取电话
        if (!$GetSos) {//TIME容错
            $this->error('SOS号码不能为空');
        }
        foreach ($imeiInfo as $key => $value) {
            $ret = save_command($value, 'sos', $GetSos);
            if ($ret['flag'] == 0) {
                $this->error($ret['message']);
            }
        }
        $this->success('sos设置成功');

    }

    //  设置phb
    public function SetPhb()
    {
        $GetImei = I('post.imei');//获取IMEI
        if (!$GetImei) {//IMEI容错
            $this->error('imei不能为空');
        }
        $GetPhb = I('post.phb');//获取电话
        if (!$GetPhb) {//TIME容错
            $this->error('电话本号码不能为空');
        }
        $listPhb = explode(',', $GetPhb);
        $listPhb = array_pad($listPhb, 10, ':');

        $data0_4 = array_slice($listPhb, 0, 5);
        $data5_9 = array_slice($listPhb, 5, 5);
        $phb1 = implode(',', $data0_4);
        $phb2 = implode(',', $data5_9);
        $ret = save_command($GetImei, 'phb', $phb1);
        if ($ret['flag'] == 0) {
            $this->error($ret['message']);
        }
        $ret = save_command($GetImei, 'phb2', $phb2);
        if ($ret['flag'] == 0) {
            $this->error($ret['message']);
        }
        $this->success('电话本设置成功');

    }
    public function test(){
        var_dump(easemob_add_user($user_id="",$imeiID="358839242225067"));
    }
}