<?php
/*
*   添加第二监护人
*by yumeng date 20170413
*/
namespace Api\Controller;

use Think\Controller;
use Think\Model;

class ForgetPwdController extends Controller
{

    //判断用户是否存在
    public function  CheckUser(){
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $username = I('post.username');//账号
        $User = D('AdminUser');//用户表
        if (empty($username)) {
            $this->error('用户名不能为空');
        }
        $res = $User->where(array("username"=>$username))->find();
        if(empty($res)){
            $this->error("该账号不存在");
        }else{
            $this->success("成功");
        }

    }

    //下发短信
    public  function ForgotPassword(){
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $User = D('AdminUser');//用户表
        $Code = D('Code');//保存验证码表
        //$phone = I('post.phone');//手机号
        $username = I('post.username');//账号
        $send_code = random(6,1);
        $yz =  preg_match('/^1[3578]\d{9}$/', $username) ? true : false;
        if($yz === false){
            $this->error("手机号格式错误");
        }
        $res = $User->where(array("username"=>$username))->find();
        if(empty($res)){
            $this->error("用户信息不存在");
        }
        $user_id = $res['user_id'];//获取用户ID


        //防用户恶意请求
        if(empty($send_code)){
            exit('请求超时，请刷新页面后重试');
        }
        $where['phone'] = $username;
        $res1 = $Code->where($where)->find();
        $time = time();//当前时间
        $Tdifference =  $time - strtotime($res1['create_time']);//获取时间 过后账号可再次发送验证码  86400 等于24小时
        if ($Tdifference < 86400 && $res1 != '' && $res1['count'] >= 5) {
            $this->error("短信下发量超过每天限制额度");
        }
        //
        if ($Tdifference > 86400 && $res1 != '') {
            $data1['count'] = 0;
            $res1 = $Code->where($where)->save($data1);
        }
        $content = "您的验证码是：".$send_code."。请不要把验证码泄露给其他人。";
        $sendSmsInfo = sendSms($username, $content);
        if ($sendSmsInfo === true ) {
            if($res1){
                $data['count'] = $res1['count']+1;
                $data['code'] = $send_code;//验证码
                $data['create_time'] = date('Y-m-d H:i:s');
                $Code->where($where)->save($data);
            }else{
                $data['user_id'] = $user_id;//账号的 ID
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
    //获取 验证码 判断是否相等
    public  function CheckCode(){
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $where['username'] = I('post.username');//账号
        $send_code = I('post.code');//验证码
        $Code = D('Code');//保存验证码表
        $time = time();//获取当前时间
        $res = $Code->where(array('phone'=>$where['username']))->find();
        $Tdifference = $time - strtotime($res['create_time']);//获取时间差  验证码 有效时间为60秒
        if ($Tdifference > 60) {
            $this->error("验证码无效请重新获取");
        }
        if($send_code != $res['code']){
            $this->error("验证码错误");
        }else{
            $this->success("验证码正确");
        }
    }
    //重设密码
    public function PasswordReset(){
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $Code = D('Code');//保存验证码表
        $User = D('AdminUser');//用户表
        $pass = I('post.pass');//新密码
        $password = I('post.password');//确认密码
        $username = I('post.username');//用户名
        if(empty($password) || empty($pass)){
            $this->error("密码不能为空");
        }
        if($pass != $password ){
            $this->error('新密码与确认密码不相符');
        }
        $resinfo = $User->where(array("username"=>$username))->find();
        if ($resinfo['password'] == md5($password)) {
            $this->error("新密码不能与原密码相同");
        }
        $data['password'] = md5($password);
        $res =  $User->where(array("username"=>$username))->save($data);
        if ($res === false) {
            $content = "密码修改失败";
            D('SystemLog')->writeLog("/Api/ForgetPwd/PasswordReset", $content, $resinfo['user_id'], $resinfo['type'], $resinfo['type'], 1, 2);
            $this->error("密码修改失败");
        } else {
            $content = "密码修改成功";
            $Code->where(array("user_id"=>$res['user_id']))->delete();
            D('SystemLog')->writeLog("/Api/ForgetPwd/PasswordReset", $content, $resinfo['user_id'], $resinfo['type'], $resinfo['type'], 1, 1);
            $this->success($content);//U('/api/login/index')
        }
    }

}