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
class SetPwController extends Controller{
    //put your code here
    
    public function index(){
        $_SESSION['time'] = time();//存储当前时间
        $this->display();
    }
     //重设密码
    public function   PasswordReset(){

        $Code = D('Code');//保存验证码表
        $User = D('AdminUser');//用户表
        $password = I('post.password');//新密码
        $confirmpassword = I('post.confirmpassword');//确认密码
        $username = $_SESSION['username'];//用户名
       
        if($confirmpassword != $password ){
            $this->error(array('state'=>0));//'新密码与确认密码不相符'
        }
        $resinfo =  $User->where(array("username"=>$username))->find();
        if ($resinfo['password'] == md5($password)) {
            $this->error(array('state'=>1));
            # code...
        }
        $data['password'] = md5($password);
        $res =  $User->where(array("username"=>$username))->save($data);
        if ($res === false) {
            $content = "密码修改失败";
            D('SystemLog')->writeLog("/Admin/ForgetPw/SetPw/PasswordReset", $content, $resinfo['user_id'], $resinfo['type'], $resinfo['type'], 1, 2);
        } else {
            $content = "密码修改成功";
            $Code->where(array("user_id"=>$res['user_id']))->delete();
            D('SystemLog')->writeLog("/Admin/ForgetPw/SetPw/PasswordReset", $content, $resinfo['user_id'], $resinfo['type'], $resinfo['type'], 1, 1);
            $this->success(array('status'=>'1','url'=>U('/Admin/ForgetPw/ForgetSucc/index')));
        }
    }
}
