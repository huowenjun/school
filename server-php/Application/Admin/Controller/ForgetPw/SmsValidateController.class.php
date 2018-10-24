<?php
/*
 * 登录
 * 生成时间：2016-01-01
 * 作者：qianzhiqiang
 * 修改时间：
 * 修改备注：
 */
namespace Admin\Controller\ForgetPw;
use Think\Controller;
class SmsValidateController extends Controller{
    //put your code here
    
    public function index(){
        $_SESSION['time'] = time();//存储当前时间
        $this->display();
    }
   
       //下发短信
    public  function ForgotPassword(){
   
        $User = D('AdminUser');//用户表
        $Code = D('Code');//保存验证码表
        $phone = $_SESSION['phone'];//手机号
        $username = $_SESSION['username'];//账号
        $send_code = random(6,1);
        $res = $User->where(array("username"=>$username))->find();
        //防用户恶意请求
        if(empty($send_code)){
            exit(array('state'=>0));
        }
        $where['user_id'] = $res['user_id'];
        $res1 = $Code->where($where)->find();
        $time = time();//当前时间
        $Tdifference =  $time - strtotime($res1['create_time']);//获取时间 过后账号可再次发送验证码  86400 等于24小时
        if ($Tdifference < 86400 && $res1 != '' && $res1['count'] >= 5) {
            $this->error(array('state'=>1));
        }
        if ($Tdifference > 86400 && $res1 != '') {
            $data1['count'] = 0;
            $res1 = $Code->where($where)->save($data1);
        }
        $content = "您的验证码是：".$send_code."。请不要把验证码泄露给其他人。";
        $sendSmsInfo = sendSms($phone, $content);
         if ($sendSmsInfo === true ) {

             if($res1){
                 $data['code'] = $send_code;//验证码
                 $data['count'] = $res1['count']+1;
                 $data['create_time'] = date('Y-m-d H:i:s');
                 $Code->where($where)->save($data);

             }else{
                 $data['user_id'] = $res['user_id'];//账号的 ID
                 $data['code'] = $send_code;//验证码
                 $data['create_time'] = date('Y-m-d H:i:s');
                 $data['count'] = 1;//验证码
                 $Code->add($data);
             }
             $this->success(array('status'=>'1'));
         } else {
             $this->error(array('status'=>0));
         }
    }
    //获取 验证码 判断是否相等
    public  function checkCode(){
        $where['username'] = $_SESSION['username'] ;//账号
        $send_code = I('post.code');//验证码
        $Code = D('Code');//保存验证码表
        $User = D('AdminUser');//用户表
        $resinfo = $User->where($where)->find();
        $res = $Code->where(array('user_id'=>$resinfo['user_id']))->find();
        $time = time();//获取当前时间
        $res = $Code->where(array('user_id'=>$resinfo['user_id']))->find();
        $Tdifference = $time - strtotime($res['create_time']);//获取时间差  验证码 有效时间为5分钟
        if ($Tdifference > 60) {
            $this->error(array('state'=>0));//验证码有效时间过了
        }
        if($send_code != $res['code']){
            $this->error(array('state'=>1));//验证码错误
        }else{
            $this->success(array('status'=>'1','url'=>U('/Admin/ForgetPw/SetPw/index')));
        }
    }
}
