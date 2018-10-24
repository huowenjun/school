<?php
/*
*   注册
*by yumeng date 20170413
*/
namespace Api\Controller;

use Think\Controller;
use Think\Model;

class RegisterController extends Controller
{
   
    //注册
    public function  register(){
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $phone = I('post.phone');//账号
        $pwd = I('post.pwd');//密码
        $send_code = I('post.code');//验证码
        $name = I('post.name');//昵称
        $User = D('AdminUser');//用户表
        $Code = D('Code');//保存验证码表
        if (empty($phone) || empty($send_code) || empty($pwd)){
            $this->error('参数错误!');
        }
        $where1['phone'] = $phone;
        $where1['username'] = $phone;
        $where1['_logic'] = 'or';
        $user_id = $User->where($where1)->getField('user_id');
        if ($user_id){
            $this->error("该手机号已注册");
        }
        //获取 验证码 判断是否相等
        $where['phone'] = $phone;//手机号
        $time = time();//获取当前时间
        $codeInfo = $Code->field('code,create_time')->where($where)->order('id  desc')->find();
        $Tdifference = $time - strtotime($codeInfo['create_time']);//获取时间差  验证码 有效时间为3分钟
        if ($Tdifference > 180) {
            $this->error("验证码无效请重新获取");
        }
        if($send_code != $codeInfo['code']){
            $this->error("验证码错误");
        }
        $data['username'] = $phone;//账号
        $data['name'] = $phone;//昵称
        $data['phone'] = $phone;//手机号
        $data['type'] = 0;//类型
        $data['password'] = $pwd;//密码
        $data['code'] = $send_code;//验证码
        $data['create_time'] = date('Y-m-d H:i:s');
        $result = $User->add($data);
        if ($result){
            easemob_add_user($result);//友盟用户添加
            $this->success('注册成功!');
        }else{
            $this->error('注册失败!');
        }
    }

    //下发短信
    public  function getCode(){
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $User = D('AdminUser');//用户表
        $Code = D('Code');//保存验证码表
        $phone = I('post.phone');//手机号
        $send_code = random(6,1);
        $where1['phone'] = $phone;
        $where1['username'] = $phone;
        $where1['_logic'] = 'or';
        $res = $User->where($where1)->find();
        if(!empty($res)){
            $this->error("该账号已存在!");
        }
        if(empty($phone)){
            $this->error('手机号码不能为空!');
        }
        $yz =  preg_match('/^1[3578]\d{9}$/', $phone) ? true : false;
        if($yz === false){
            $this->error("手机号格式错误");
        }
        //防用户恶意请求
        if(empty($send_code)){
            $this->error('验证码生成失败,请重新请求!');
        }
        $where['phone'] = $phone;
        $res1 = $Code->where($where)->find();
        $time = time();//当前时间
        $Tdifference =  $time - strtotime($res1['create_time']);//获取时间 过后账号可再次发送验证码  86400 等于24小时
        if($Tdifference<60)
        {
            $this->error("您刚刚发送过一条验证码，如果一分钟内没有收到，您可以再次发送");
        }
        if ($Tdifference < 86400 && $res1 != '' && $res1['count'] >= 5) {
            $this->error("短信下发量超过每天限制额度");
        }
        //
        if ($Tdifference > 86400 && $res1 != '') {
            $data1['count'] = 0;
            $res1 = $Code->where($where)->save($data1);
        }
        $content = "您的验证码是：".$send_code."。请不要把验证码泄露给其他人。";
        $sendSmsInfo = sendSms($phone, $content);
         if ($sendSmsInfo === true ) {
             if($res1){
                 $data['phone'] = $phone;//账号的 ID
                 $data['count'] = $res1['count']+1;
                 $data['code'] = $send_code;//验证码
                 $data['create_time'] = date('Y-m-d H:i:s');
                 $Code->where($where)->save($data);
             }else{
                 $data['phone'] = $phone;//账号的 ID
                 $data['code'] = $send_code;//验证码
                 $data['create_time'] = date('Y-m-d H:i:s');
                 $data['count'] = 1;//验证码
                 $Code->add($data);
             }
             $this->success("发送成功");
         } else {
             $this->error("发送失败");
         }
    }

    // 获取学校信息
    public function information(){
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $SchoolInformation = D('SchoolInformation');//学校表
        $SchoolArea = D('SchoolArea');//校区表
        $region_id = I('post.region_id');//县级id
        if (empty($region_id)){
            $this->error('参数错误!');
        }else{
            $schoolInfo = $SchoolInformation->field('s_id,name')->where(array('region_id'=>$region_id))->select();
            $arr = array();
            foreach($schoolInfo as $key=>$value){
                $arr[$key]['s_id'] = $value['s_id'];
                $arr[$key]['name'] = $value['name'];
                $areaInfo = $SchoolArea->field('a_id,name')->where(array('s_id'=>$value['s_id']))->select();
                foreach($areaInfo as $key1=>$value1){
                    $arr[$key]['area'][$key1]['a_id'] = $value1['a_id'];
                    $arr[$key]['area'][$key1]['name'] = $value1['name'];
                }
            }
            $this->success($arr);
        }
    }

    //教师 绑定学校接口
    public function bindingSchool(){
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $AdminUser = D('AdminUser');
        $Teacher = D('Teacher');
        $user_id = I('post.user_id');//用户ID
        $s_id = I('post.s_id');//学校ID
        $a_id = I('post.a_id');//校区ID
        $d_id = I('post.d_id');//部门ID
        $education = I('post.education');//学历
        if (empty($user_id) || empty($s_id) || empty($a_id) || empty($d_id) || empty($education)){
            $this->error('参数错误!');
        }else{
            $userInfo = $AdminUser->where(array('user_id'=>$user_id))->find();
            $data['u_id'] = $user_id;
            $data['s_id'] = $s_id;
            $data['a_id'] = $a_id;
            $data['education'] = $education;//学历
            $data['d_id'] = $d_id;//部门
            $data['user_name'] = $userInfo['username'];//登录名
            $data['password'] = $userInfo['password'];//密码
            $data['phone'] = $userInfo['phone'];//手机号
            $data['sex'] = $userInfo['sex'];//性别
            $data['name'] = $userInfo['name'];//昵称
            $data['email'] = $userInfo['email'];//邮箱
            $data['valid'] = 3;//待审核
            $data['create_time'] = date('Y-m-d H:i:s');//创建时间
            $ret = $Teacher->add($data);
            if ($ret){
                $data1['type'] = 3;
                $res = $AdminUser->where(array('user_id'=>$user_id))->save($data1);
                if ($res){
                    $this->success('绑定成功!');
                }else{
                    $this->error('绑定失败!');
                }
            }
        }
    }

    //家长 绑定孩子接口
    public function bindingStudent(){
        $student = D('Student');
        $studentParent = D('StudentParent');
        $studentGroupAccess = D('StudentGroupAccess');
        $user_id = I('post.user_id');//用户ID
        $relation = I('post.relation');//关系
        $s_id = I('post.s_id');//学校ID
        $a_id = I('post.a_id');//校区ID
        $g_id = I('post.g_id');//年级ID
        $c_id = I('post.c_id');//班级ID
        $stu_no = I('post.stu_no');//学号
        $stu_name = I('post.stu_name');//学生姓名
        $sex = I('post.sex');//性别
        $card_id = I('post.card_id');//身份证号
        $birth_date = I('post.birth_date');//出生日期
        $rx_date = I('post.rx_date');//入学日期
        if (empty($_POST)){
            $this->error('参数错误!');
        }else{
            //添加学生信息
            $student->startTrans();
            $data['s_id'] = $s_id;
            $data['a_id'] = $a_id;
            $data['g_id'] = $g_id;
            $data['c_id'] = $c_id;
            $data['stu_no'] = $stu_no;
            $data['stu_name'] = $stu_name;
            $data['sex'] = $sex;
            $data['card_id'] = $card_id;
            $data['birth_date'] = $birth_date;
            $data['rx_date'] = $rx_date;
            $ret = $student->add($data);
            if($ret){
                $data1['u_id'] = $user_id;
                $data1['relation'] = $relation;
                $res = $studentParent->add($data1);
                if ($res){
                    $data2['sp_id'] = $res;
                    $data2['stu_id'] = $ret;
                    $re = $studentGroupAccess->add($data2);
                    if ($re){
                        $data3['type'] = 4;
                        $r = D('AdminUser')->where(array('user_id'=>$user_id))->save($data3);
                        if ($r){
                            $student->commit();
                            $this->success('绑定成功!');
                        }else{
                            $this->error('绑定失败!');
                            $student->rollback();
                        }
                    }else{
                        $this->error('绑定失败!');
                        $student->rollback();
                    }
                }else{
                    $this->error('绑定失败!');
                    $student->rollback();
                }
            }else{
                $this->error('绑定失败!');
                $student->rollback();
            }
        }
    }

    //申请学校
    public function applyForSchool(){
        $school = D('SchoolInformation');
        $schoolArea = D('schoolArea');
        $region_id = I('post.region_id');//省县ID
        $s_name = I('post.name');//学校名
        $a_name = I('post.name');//校区名
        if (empty($region_id) || empty($s_name) || empty($a_name)){
            $this->error('参数错误!');
        }else{
            $data['region_id'] = $region_id;
            $data['name'] = $s_name;
            $ret = $school->add($data);
            if ($ret){
                $data1['s_id'] = $ret;
                $data1['name'] = $a_name;
                $res = $schoolArea->add($data1);
                $this->success('申请成功!');
            }
        }
    }
}