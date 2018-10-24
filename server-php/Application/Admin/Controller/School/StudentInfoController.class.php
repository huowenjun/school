<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-学生信息
 */

namespace Admin\Controller\School;

use Admin\Controller\BaseController;

class StudentInfoController extends BaseController
{
    /**
     *学生信息
     */

    public function index()
    {

        $this->display();
    }

    //查询数据
    public function query()
    {
        $where = "";

        $s_id = I('get.s_id');
        $a_id = I('get.a_id');
        $g_id = I('get.g_id');
        $stu_no = I('get.stu_no');
        $stu_name = I('get.stu_name');
        $imei_id = I('get.imei_id');
        $parent_name = I('get.parent_name');//家长姓名
        $phone = I('get.phone'); //联系方式
        $c_id = I('get.c_id');
        $pagesize = I("get.pagesize", $this->PAGE_SIZE);
        $page = I('get.page');
        $sort = I('get.sort', 'stu_id');
        $order = $sort . ' ' . I('get.order', 'desc');
        $Student = D('Student');
        if (!empty($s_id)) {
            $where['s_id'] = $s_id;
        }
        if (!empty($a_id)) {
            $where['a_id'] = $a_id;
        }
        if (!empty($g_id)) {
            $where['g_id'] = $g_id;
        }

        if ($stu_no != '') {
            $where['stu_no'] = array("like", "%$stu_no%");
        }
        if (!empty($c_id)) {
            $where['c_id'] = $c_id;
        }
        if ($stu_name != '') {
            $where['stu_name'] = array("like", "%$stu_name%");
        }
        if ($imei_id != '') {
            $where['imei_id'] = array("like", "%$imei_id%");
        }
        //家长姓名查询
        if ($parent_name != '') {

            $where1['name'] = array("like", "%$parent_name%");
            $u_id = '';
            $userId = D('AdminUser')->where($where1)->getField('user_id', true);
            foreach ($userId as $key => $value) {
                $u_id = $u_id . $value . ",";
            }
            $where2['u_id'] = array('IN', rtrim($u_id, ','));
            $sp_id = D('StudentParent')->where($where2)->getField('sp_id', true);
            $sp_id1 = "";
            foreach ($sp_id as $key => $value) {
                $sp_id1 = $sp_id1 . $value . ",";
            }
            $where3['sp_id'] = array('IN', rtrim($sp_id1, ','));
            $stu_id = D('StudentGroupAccess')->where($where3)->getField('stu_id', true);
            $stu_id1 = '';
            foreach ($stu_id as $key => $value) {
                $stu_id1 = $stu_id1 . $value . ",";
            }
            $sId1 = rtrim($stu_id1, ',');
        }


        //联系方式查询
        if ($phone != '') {
            $where4['phone'] = array("like", "%$phone%");
            $u_id = '';
            $userId = D('AdminUser')->where($where4)->getField('user_id', true);
            foreach ($userId as $key => $value) {
                $u_id = $u_id . $value . ",";
            }
            $where5['u_id'] = array('IN', rtrim($u_id, ','));
            $sp_id = D('StudentParent')->where($where5)->getField('sp_id', true);
            $sp_id2 = "";
            foreach ($sp_id as $key => $value) {
                $sp_id2 = $sp_id2 . $value . ",";
            }
            $where6['sp_id'] = array('IN', rtrim($sp_id2, ','));
            $stu_id = D('StudentGroupAccess')->where($where6)->getField('stu_id', true);
            $stu_id2 = '';
            foreach ($stu_id as $key => $value) {
                $stu_id2 = $stu_id2 . $value . ",";
            }
            $sId2 = rtrim($stu_id2, ',');

        }
        if ($phone != '' || $parent_name != '') {
            $a = $sId1 . "," . $sId2;
            $where['stu_id'] = array('IN', $a);
        }
        $result = $Student->queryListEX('*', $where, $order, $page, $pagesize, '');
        $this->success($result);
    }

    //获取默认值
    public function get()
    {
        $stu_id = I('get.stu_id/d', 0);
        $Student = M('Student');
        $User = M('User', 'think_');
        $stu_info = $Student->where("stu_id = '{$stu_id}'")->find();

        if ($stu_id > 0 && empty($stu_info)) {
            $this->error('非法参数');
        }
        // 查询监护人表
        $list = M('StudentGroupAccess')
            ->alias('a')
            ->field('b.*')
            ->join('__STUDENT_PARENT__ b on a.sp_id = b.sp_id')
            ->where("a.stu_id = '$stu_id'")
            ->select();

        foreach ($list as $key => $value) {
            $user_info = $User
                ->field('email,name,phone,username')
                ->where(array('user_id' => $value['u_id']))
                ->find();
            $list[$key]['email'] = $user_info['email'];
            $list[$key]['parent_name'] = $user_info['name'];
            $list[$key]['parent_phone'] = $user_info['phone'];
            $list[$key]['username'] = $user_info['username'];
        }
        $data['data'] = $stu_info;
        $data['data1'] = $list;
        $this->success($data);
    }

    //学生信息添加/编辑
    public function edit()
    {
        //学生信息部分
        $stu_id = I('post.stu_id');
        $s_id = I('post.s_id');
        $a_id = I('post.a_id');
        $g_id = I('post.g_id');
        $c_id = I('post.c_id');
        $stu_no = I('post.stu_no');
        $birth_date = I('post.birth_date');
        $card_id = I('post.card_id');//身份证号
        $sex = I('post.sex');
        $icc_id = I('post.icc_id');
        $stu_name = I('post.stu_name');
        $imei_id = I('post.imei_id');
        $stu_phone = I('post.stu_phone');
        $nfc_id = I('post.nfc_id');
        $rx_date = I('post.rx_date');
        $now = date("Y-m-d H:i:s");

        if (!$s_id || !$a_id || !$g_id || !$c_id) {
            $data = array('stu_id' => $stu_id, 'info' => '请您选择要操作的班级');
            $this->error($data);
        }
        if ($birth_date > $now) {
            $data = array('stu_id' => $stu_id, 'info' => '出生日期不能大于当前日期');
            $this->error($data);
        }
        if ($rx_date < $birth_date) {
            $data = array('stu_id' => $stu_id, 'info' => '入学日期不能小于出生日期');
            $this->error($data);
        }

        //检测设备表 imei
        if ($stu_id > 0) {//编辑
            $device_info = M('Device')->field('stu_id,status')->where("stu_id = '{$stu_id}'")->find();
        } else {//新增
            $device_info = M('Device')->field('stu_id,status')->where("imei = '{$imei_id}'")->find();
        }


        if (($device_info['stu_id'] != $stu_id) && ($device_info['status'] == 1)) {
            $data = array('stu_id' => $stu_id, 'info' => 'imei已被绑定,请联系管理员');
            $this->error($data);
        }
        //检测手机号
//        $stu_info = M('Student')->field('stu_id,stu_phone')->where("stu_phone = '{$stu_phone}'")->find();
//        if (($stu_info['stu_id'] != $stu_id) && ($stu_info['stu_id'] > 0)) {
//            $this->error('SIM已被绑定,请联系管理员');
//        }

        //学生表处理
        if ($stu_id) {
            $stu_data['stu_id'] = $stu_id;
        }
        $stu_data['s_id'] = $s_id;
        $stu_data['a_id'] = $a_id;
        $stu_data['g_id'] = $g_id;
        $stu_data['c_id'] = $c_id;
        $stu_data['stu_no'] = $stu_no;
        $stu_data['stu_name'] = $stu_name;
        if ($imei_id) {
            $stu_data['imei_id'] = $imei_id;
        }
        $stu_data['icc_id'] = $icc_id;
        $stu_data['nfc_id'] = $nfc_id;
        $stu_data['sex'] = $sex;
        $stu_data['birth_date'] = $birth_date;
        $stu_data['rx_date'] = $rx_date;
        $stu_data['card_id'] = $card_id;
        $stu_data['user_id'] = $this->getUserId();
        $stu_data['status'] = 1;
        $stu_data['stu_phone'] = $stu_phone;
        $stu_data['devicetype'] = I('post.devicetype');

        $rules = array(
            array('stu_no', 'require', '学号不能为空！'),
            array('stu_name', 'require', '姓名不能为空！'),
            array('birth_date', 'require', '出生日期不能为空！'),
            array('rx_date', 'require', '入学日期不能为空！'),
            array('imei_id', 'require', 'imei号码不能为空！'),
            array('sex', 'require', '性别信息不能为空！'),
            array('stu_phone', 'require', '学生卡手机号不能为空！'),
            array('card_id', '', '身份证号码已经存在！', 0, 'unique', 1), // 在新增的时候验证name字段是否唯一
        );
        if (!M('Student')->validate($rules)->create($stu_data)) {
            $err = M('Student')->getError();
            $data = array('stu_id' => $stu_id, 'info' => $err);
            $this->error($data);
        }
        $isCardNo = isCreditNo($card_id);//检测身份证合法性
        if (!$isCardNo) {
            $data = array('stu_id' => $stu_id, 'info' => '身份证号格式不正确');
            $this->error($data);
        }
        $stu_no_status = check_stu_no($stu_no, $stu_id, $c_id);
        if (!$stu_no_status) {
            $data = array('stu_id' => $stu_id, 'info' => '该班级学号已经存在');
            $this->error($data);
        }

        if ($stu_id) {
            $stu_data['modif_time'] = $now;
            $student_id = M('Student')->where("stu_id = '{$stu_id}'")->save($stu_data);
        } else {
            $stu_data['create_time'] = $now;
            $student_id = M('Student')->add($stu_data);
            $device_data['stu_id'] = $student_id;
            $stu_id = $student_id;
        }
        //设备表处理
        $device_data['phone'] = $stu_phone;
        $device_data['imei'] = $imei_id;
        $device_data['s_id'] = $s_id;
        $device_data['a_id'] = $a_id;
        $device_data['g_id'] = $g_id;
        $device_data['u_id'] = $this->getUserId();

        $device_data['iccid'] = $icc_id;
        $device_data['status'] = 1;
        $device_data['modif_time'] = $now;
        $device_data['devicetype'] = I('post.devicetype');
        M('Device')->startTrans();
        if ($device_info) {
            $device_id = M('Device')->where("imei = '{$imei_id}'")->save($device_data);
        } else {
            $device_id = M('Device')->add($device_data);
        }
        if (!$device_id || !$student_id) {
            M('Device')->rollback();
            $data = array('stu_id' => $stu_id, 'info' => '操作失败');
            $this->error($data);
        }
        M('Device')->commit();
        $data = array('stu_id' => $stu_id, 'info' => '操作成功');
        $this->success($data);
    }

    //监护人添加/编辑
    public function partentEdit()
    {
        $sp_id = I('post.sp_id');
        $stu_id = I('post.stu_id');
        $user_id = I('post.u_id');
        $relation = I('post.relation');
        $address = I('post.address');
        $work_unit = I('post.work_unit');
        $family_tel = I('post.family_tel');
        $parent_name = I('post.parent_name');
        $email = I('post.email');
        $parent_phone = I('post.parent_phone');
        $now = date('Y-m-d H:i:s');

        if (!$sp_id) {//新增
            if (!$parent_phone) {
                $data = array('sp_id' => $sp_id, 'info' => '手机号码不能为空');
                $this->error($data);
            }
        }
        if (!$parent_name) {
            $data = array('sp_id' => $sp_id, 'info' => '监护人姓名不能为空');
            $this->error($data);
        }

        $user_data['name'] = $parent_name;
        $user_data['password'] = md5(123456);
        $user_data['email'] = $email;
        $user_data['ip'] = $_SERVER['REMOTE_ADDR'];
        $user_data['status'] = 0;
        $user_data['type'] = 4;
        $user_data['group_id'] = 4;
        if ($parent_phone) {
            $user_data['username'] = $parent_phone;
            $user_data['phone'] = $parent_phone;
        }
        if ($relation == 1 || $relation == 3 || $relation == 5) {
            $user_data['sex'] = 2;
        } else {
            $user_data['sex'] = 1;
        }
        //检测用户是否存在
        if ($sp_id && $user_id) {
            $user_info = M("user", 'think_')->field('user_id,type')->where("user_id = '{$user_id}' and type=4")->find();
        } else {
            $user_info = M("user", 'think_')->field('user_id,type')->where("username = '{$parent_phone}' and type=4")->find();
        }

        if ($user_info && ($sp_id > 0) && ($user_info['user_id'] != $user_id)) {
            $data = array('sp_id' => $sp_id, 'info' => '手机号已被注册使用');
            $this->error($data);
        }
        $parent_data['relation'] = $relation;
        $parent_data['address'] = $address;
        $parent_data['work_unit'] = $work_unit;
        $parent_data['family_tel'] = $family_tel;
        M("user", 'think_')->startTrans();
        if (!$user_info && !$user_id) {
            if (!$stu_id) {
                M("user", 'think_')->rollback();
                $data = array('sp_id' => $sp_id, 'info' => '请您先录入学生信息');
                $this->error($data);
            }
            //用户表
            $user_data['create_time'] = $now;
            $user_id = M("user", 'think_')->add($user_data);
            //监护人表
            $parent_data['u_id'] = $user_id;
            $parent_id = M('StudentParent')->add($parent_data);
            //关联表
            $group_data['stu_id'] = $stu_id;
            $group_data['sp_id'] = $parent_id;
            $group_data['user_id'] = $user_id;
            $sp_id = $parent_id;
            $group_id = M('StudentGroupAccess')->add($group_data);
            if ($user_id && $parent_id && $group_id) {
                //添加视频通讯录 BY:Meng Fanmin 2017.12.21
                if ($_SERVER['SERVER_ADDR'] == C('SERVERADDR')) {
                    video_add_user($user_id);
                    easemob_add_user($user_id);
                }
            }

        } else {
            $user_data['modif_time'] = $now;
            $user_id = M("user", 'think_')->where("user_id = '{$user_info['user_id']}'")->save($user_data);
            M('StudentParent')->where("u_id = '{$user_info['user_id']}'")->save($parent_data);
            $parent_id = M('StudentParent')->where("u_id = '{$user_info['user_id']}'")->getField('sp_id');
            $group_add = M('StudentGroupAccess')->where("stu_id = '{$stu_id}' and sp_id = '{$parent_id}'")->getField('id');
            if (!$group_add) {
                $group_data['stu_id'] = $stu_id;
                $group_data['user_id'] = $user_info['user_id'];
                $group_data['sp_id'] = $parent_id;
                $group_id = M('StudentGroupAccess')->add($group_data);
            } else {
                $group_id = 1;
            }


        }
        if (!$user_id || !$parent_id || !$group_id) {
            M("user", 'think_')->rollback();
            $data = array('sp_id' => $sp_id, 'info' => '操作失败,请稍后再试');
            $this->error($data);
        }
        M("user", 'think_')->commit();
        $data = array('sp_id' => $sp_id, 'info' => '操作成功');
        $this->success($data);
    }

    //监护人 删除
    public function partentDel()
    {
        $stu_id = I('post.stu_id');
        $sp_id = I('post.sp_id');
        $user_id = M('StudentParent')->where("sp_id = '{$sp_id}'")->getField('u_id');
        if (!$user_id || !$sp_id) {
            $this->error('请选择要删除监护人');
        }
        $partent_id = M('StudentParent')->where("u_id = '{$user_id}'")->getField('sp_id');
        if ($partent_id != $sp_id) {
            $this->error('非法操作');
        }
        M("user", 'think_')->startTrans();
        $whereStr['sp_id'] = $sp_id;
        $parent_id = M('StudentParent')->where($whereStr)->delete();
        $whereStr['stu_id'] = $stu_id;
        $group_id = M('StudentGroupAccess')->where($whereStr)->delete();
        $user_id = M("user", 'think_')->where("user_id = '{$user_id}'")->delete();

        if (!$parent_id || !$group_id || !$user_id) {
            M("user", 'think_')->rollback();
            $this->error('操作失败');
        }

        M("user", 'think_')->commit();
        $this->success('操作成功');
    }

    //删除
    public function del()
    {
        $stu_id = I('post.id');//学生 ID
        $arrUids = explode(',', $stu_id);
        if (empty($arrUids)) {
            $this->error('参数错误');
        }
        $StudentParent = D('StudentParent');//家长表
        $Student = D('Student');
        $okCount = 0;//学生表
        foreach ($arrUids as $k => $v) {
            $where['stu_id'] = $v;
            $sthInfo = D('StudentGroupAccess')->where($where)->getField("sp_id", true);
            $ret = $Student->where($where)->delete();
            //查询外链主键表  得到监护人ID
            $sp_id = "";
            foreach ($sthInfo as $key => $value) {
                $sp_id = $sp_id . $value . ",";
            }
            $where1['sp_id'] = array('IN', rtrim($sp_id, ','));
            if ($ret) {
                $resInfo = D('StudentGroupAccess')->where($where1)->getField("stu_id", true);
                $studentParenInfo = $StudentParent->where($where1)->getField('u_id', true);
                $sp_id1 = "";
                foreach ($resInfo as $key => $value) {
                    $sp_id1 = $sp_id1 . $value . ",";
                }

                $u_id = "";
                foreach ($studentParenInfo as $key => $value) {
                    $u_id = $u_id . $value . ",";
                }
                $where3['stu_id'] = array('IN', rtrim($sp_id1, ','));
                $student1 = $Student->where($where3)->select();//判断是否还有学生！ 没有了 则删除用户表中的数据的用表
                if (empty($student1)) {
                    $where2['user_id'] = array('IN', rtrim($u_id, ','));
                    $StudentParent->where($where1)->delete();
                    $StudentGroupAccess = D('StudentGroupAccess')->where($where)->delete();
                    $res = D('AdminUser')->where($where2)->delete();
                    $where3['send_user'] = array('IN', rtrim($u_id, ','));
                    $OnlineQu = D('OnlineQu')->where($where3)->delete();//删除 学生关联 在线提问信息 家长不存在才删除在线提问信息记录
                    $Mymessage = D('Mymessage')->where($where2)->delete();   //删除 我的消息表中的数据
                }
                $Reward = D('Reward')->where($where)->delete();//删除 学生奖励信息奖励 php
                $ExamAllResult = M('ExamAllResults')->where($where)->delete(); //删除学生的统考成绩
                $ExamResults = D('ExamResults')->where($where)->delete();//删除学生的单考考成绩
                $Remark = D('Remark')->where($where)->delete(); //删除学生 评语信息
                $Attendance = D('Attendance')->where($where)->delete(); //删除学生 考勤信息
                $StuCardSet = D('StuCardSet')->where($where)->delete(); //删除学生 学生卡信息
                $Leave = M('Leave')->where($where)->delete(); //删除学生 学生请假信息

                //把绑定的设备解绑
                $data['status'] = '0';
                $data['stu_id'] = '0';
                $data['phone'] = '0';
                $res1 = D('Device')->where($where)->save($data);
            }
            if ($ret == 1) {
                $okCount++;  //处理成功记录数
            }
        }
        //写log
        $content = "删除" . $okCount . "条信息";
        $state = $okCount > 0 ? 1 : 2;
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, $state);
        $this->success('删除成功' . $okCount . '条记录');
    }

    //获取未被绑定的设备
    public function inguiry_unit()
    {
        $s_id = I('get.s_id');//学校ID
        $imei = I('get.imei');//imei号
        $where['stu_id'] = 0;
        $where['status'] = 0;
        if (!empty($imei)) {
            $where['imei'] = array("like", "%$imei%");
        }
        if (!empty($s_id)) {
            $where['s_id'] = $s_id;
        }
        $pagesize = I("get.pagesize", $this->PAGE_SIZE);

        $page = I('get.page');
        $sort = I('get.sort', 'dc_id');
        $order = $sort . ' ' . I('get.order', 'desc');
        $Device = D('DeviceManage');
        $result = $Device->queryListEX('*', $where, $order, $page, $pagesize, '');

        $this->success($result);
    }

    /**
     * 学生信息导入
     */
    public function import()
    {
        $file_url = I('param.file_url');
        $file_url = $_SERVER['DOCUMENT_ROOT'] . $file_url;
        $stu_data = excel_load($file_url);
        if (!$stu_data) {
            $this->error('导入失败,excel未填写数据');
        }
        $arr_leng = count($stu_data[0]);
        $phone_leng = strlen(trim($stu_data[0][10]));
        if ($phone_leng != 11) {
            $this->error('第10列手机号码格式不正确');
        }
        if (($arr_leng != 13) || ($phone_leng != 11)) {
            $this->error('模板文件不正确,请重新下载正确的模板文件');
        }
        $s_id = I('get.s_id');
        $a_id = I('get.a_id');
        $g_id = I('get.g_id');
        $c_id = I('get.c_id');
        $now = date("Y-m-d H:i:s");
        $ip = $_SERVER['REMOTE_ADDR'];
        M('Device')->startTrans();
        foreach ($stu_data as $key => $value) {
            if ($value[8] == '男') {
                $value[8] = 1;
            } else {
                $value[8] = 0;
            }

            $value['s_id'] = $s_id;
            $value['a_id'] = $a_id;
            $value['g_id'] = $g_id;
            $value['c_id'] = $c_id;
            $value['now'] = $now;
            $value['ip'] = $ip;
            //学生卡及设备处理
            $stu_and_device = $this->add_studentAnddevice($value);
            if ($stu_and_device['status'] == 0) {
                M('Device')->rollback();
                $this->error($stu_and_device['info']);
            }

            //监护人信息
            $value['student_id'] = $stu_and_device['student_id'];
            $stu_partent = $this->add_partent($value);
            if ($stu_partent['status'] == 0) {
                M('Device')->rollback();
                $this->error($stu_partent['info']);
            }
        }
        M('Device')->commit();
        $this->success('批量操作成功');
    }

    //批量导入处理学生及设备信息
    private function add_studentAnddevice($data = array())
    {
        //学生信息部分
        $stu_name = $data[0];
        $stu_no = $data[1];
        $stu_phone = $data[2];
        $imei_id = $data[3];
        $nfc_id = $data[4];
        $birth_date = $data[5];
        $rx_date = $data[6];
        $card_id = $data[7];//身份证号
        $sex = $data[8];

        $s_id = $data['s_id'];
        $a_id = $data['a_id'];
        $g_id = $data['g_id'];
        $c_id = $data['c_id'];

        $erro = array('status' => 0);
        $succ = array('status' => 1);
        if (!$data['s_id'] || !$data['a_id'] || !$data['g_id'] || !$data['c_id']) {
            $erro['info'] = $stu_name . '请您选择要操作的班级';
            return $erro;
        }
        if ($birth_date > $data['now']) {
            $erro['info'] = $stu_name . '出生日期不能大于当前日期';
            return $erro;
        }
        if ($rx_date < $birth_date) {
            $erro['info'] = $stu_name . '入学日期不能小于出生日期';
            return $erro;
        }

        //检测设备表 imei
        $device_info = M('Device')->field('stu_id,status')->where("imei = '{$imei_id}'")->find();

        if ($device_info['status'] == 1) {
            $erro['info'] = $stu_name . 'imei已被绑定,请联系管理员';
            return $erro;
        }

        //学生表处理
        $stu_data['s_id'] = $s_id;
        $stu_data['a_id'] = $a_id;
        $stu_data['g_id'] = $g_id;
        $stu_data['c_id'] = $c_id;
        $stu_data['stu_no'] = $stu_no;
        $stu_data['stu_name'] = $stu_name;
        $stu_data['imei_id'] = $imei_id;
        $stu_data['nfc_id'] = $nfc_id;
        $stu_data['sex'] = $sex;
        $stu_data['birth_date'] = $birth_date;
        $stu_data['rx_date'] = $rx_date;
        $stu_data['card_id'] = $card_id;
        $stu_data['user_id'] = $this->getUserId();
        $stu_data['status'] = 1;
        $stu_data['stu_phone'] = $stu_phone;
        $stu_data['create_time'] = $data['now'];
        $stu_data['modif_time'] = $data['now'];

        $rules = array(
            array('stu_no', 'require', '学号不能为空！'),
            array('stu_name', 'require', '姓名不能为空！'),
            array('birth_date', 'require', '出生日期不能为空！'),
            array('rx_date', 'require', '入学日期不能为空！'),
            array('imei_id', 'require', 'imei号码不能为空！'),
            array('sex', 'require', '性别信息不能为空！'),
            array('stu_phone', 'require', '学生卡手机号不能为空！'),
           // array('card_id', '', '身份证号码已经存在！', 0, 'unique', 1), // 在新增的时候验证name字段是否唯一
        );
        if (!M('Student')->validate($rules)->create($stu_data)) {
            $err = M('Student')->getError();
            $erro['info'] = $stu_name . $err;
            return $erro;
        }
        $isCardNo = isCreditNo($card_id);//检测身份证合法性
        if (!$isCardNo) {
//            $erro['info'] = '身份证号码不正确!';
//            return $erro;
        }

        $student_id = M('Student')->add($stu_data);
        $device_data['stu_id'] = $student_id;
        //设备表处理
        $device_data['phone'] = $stu_phone;
        $device_data['imei'] = $imei_id;
        $device_data['s_id'] = $s_id;
        $device_data['a_id'] = $a_id;
        $device_data['g_id'] = $g_id;
        $device_data['u_id'] = $this->getUserId();
        $device_data['status'] = 1;
        $device_data['modif_time'] = $data['now'];

        if ($device_info) {
            $device_id = M('Device')->where("imei = '{$imei_id}'")->save($device_data);
        } else {
            $device_id = M('Device')->add($device_data);
        }
        if (!$device_id || !$student_id) {
            $erro['info'] = $stu_name . '操作失败';
            return $erro;
        }
        $succ['student_id'] = $student_id;
        $succ['device_id'] = $device_id;
        return $succ;
    }

    //批量导入处理监护人信息
    private function add_partent($data = array())
    {
        $stu_name = $data[0];
        $parent_name = $data[9];
        $parent_phone = $data[10];
        $email = $data[11];
        $address = $data[12];
        $student_id = $data['student_id'];
        if (!$parent_phone) {
            $erro['info'] = $stu_name . '监护人手机号码不能为空';
            return $erro;
        }
        $user_data['name'] = $parent_name;
        $user_data['password'] = md5(123456);
        if($email){
            $user_data['email'] = $email;
        }
        $user_data['ip'] = $data['ip'];
        $user_data['status'] = 0;
        $user_data['type'] = 4;
        $user_data['group_id'] = 4;
        $erro = array('status' => 0);
        $succ = array('status' => 1);
        if ($parent_phone) {
            $user_data['username'] = $parent_phone;
            $user_data['phone'] = $parent_phone;
        }

        //检测用户是否存在
        $user_info = M("user", 'think_')->field('user_id,type')->where("username = '{$parent_phone}' and type=4")->find();
        if ($address) {
            $parent_data['address'] = $address;
        }
        if (!$user_info) {
            //用户表
            $user_data['create_time'] = $data['now'];
            $user_data['modif_time'] = $data['now'];
            $user_id = M("user", 'think_')->add($user_data);
            //监护人表
            $parent_data['u_id'] = $user_id;
            $parent_data['modif_time'] = $user_id;
            $parent_id = M('StudentParent')->add($parent_data);

            //关联表
            $group_data['stu_id'] = $student_id;
            $group_data['sp_id'] = $parent_id;
            $group_data['user_id'] = $user_id;
            $group_id = M('StudentGroupAccess')->add($group_data);
            if ($user_id && $parent_id && $group_id) {
                //添加视频通讯录 BY:Meng Fanmin 2017.12.21
                if ($_SERVER['SERVER_ADDR'] == C('SERVERADDR')) {
                    video_add_user($user_id);
                    easemob_add_user($user_id);
                }
            }

        } else {
            $user_id = $user_info['user_id'];
            //关联表
            $parent_id = M('StudentParent')->where("u_id = '{$user_id}'")->getField('sp_id');
            $group_data['stu_id'] = $student_id;
            $group_data['sp_id'] = $parent_id;
            $group_data['user_id'] = $user_id;
            $group_id = M('StudentGroupAccess')->add($group_data);
        }

        if (!$user_id || !$parent_id || !$group_id) {
            $erro['info'] = $stu_name . '操作失败,请稍后再试';
            return $erro;
        }
        return $succ;
    }


    //学生 升学机制
    public function Upgrade()
    {

        $stu_id = I('post.stu_id'); //学生 stu_id
        $s_id = I('post.s_id'); //学校 s_id
        $a_id = I('post.a_id'); //校区 a_id
        $g_id = I('post.g_id'); //要升向的年级
        $c_id = I('post.c_id'); //要升向的班级
        if (!empty($s_id)) {
            $data['s_id'] = $s_id;
            # code...
        }
        $data['a_id'] = $a_id;
        $data['g_id'] = $g_id;
        $data['c_id'] = $c_id;
        $Student = D('Student'); //学生表
        $stuInfo = explode(',', $stu_id);
        // 开启事物   有一条数据不正常 该次升学 失败
//                $Student->startTrans();
        $state = '';
        foreach ($stuInfo as $key => $value) {

            $where['stu_id'] = $value;
            // $getinfo = $Student->where($where)->getField('g_id',false);
            /*
            // 获取第一条数据 然后判断传过来的所有数据是否是在同一个年级下的  <--  一次只能传一个年级下的所有班级 不能同时传多个年级-->
            $where1['g_id'] = $stuInfo[0];
            $StuInfo = $Student->where($where1)->find();
            */

            // 判断 要升级的 是否 不同的年级
            $getInfo = $Student->where($where)->find();
            if ($getInfo['g_id'] == $g_id) {
                $this->error('请选择不同年级下的班级升级');

            }
            $res = $Student->where($where)->save($data);
            # code...
        }


//                $Student->commit();
//                $Student->rollback();

        $content = "升级成功";
        if ($res === false) {
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
            $this->error($Student->getError());
        } else {
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
            $this->success($content, U('/Admin/School/Student/index'));
        }


    }
}
