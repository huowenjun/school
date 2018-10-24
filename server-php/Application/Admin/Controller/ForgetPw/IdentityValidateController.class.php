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
class IdentityValidateController extends Controller{
    //put your code here
    
    public function index(){
        $_SESSION['time'] = time();//存储当前时间
        $this->display();
    }
   //验证手机号是否有效
    public function VerifyPhone(){
       
        $phone = I('post.phone');//手机号
        $username = $_SESSION['username'];//用户名
        $radioval = I('post.radioval');//0 代表主负责人 1 代表副负责人
        $where['username'] = $username;
        $User = D('AdminUser');//用户表
        $Teacher = D('Teacher');//教师表
        $School = D('SchoolInformation');//学校表
        $resinfo = $User->where($where)->find();
        if (empty($resinfo['phone'])) {
            $resinfo = $Teacher->where(array('user_name'=>$username))->find();
            if (empty($resinfo['phone'])) {
                $resinfo = $School->where(array('user_name'=>$username))->find();
                if ($radioval == 0) {
                    if ($phone == $resinfo['main_phone'] ) {
                        $_SESSION['phone'] =$phone;//存储手机号 phone
                        $this->success(array('state'=>'1','url'=>U('/Admin/ForgetPw/SmsValidate/index')));
                     }else{
                         $this->error(array('state'=>'0'));
                      }
                }elseif($radioval == 1){
                    if ($phone == $resinfo['sub_phone']) {
                        $_SESSION['phone'] =$phone;
                        $this->success(array('state'=>'1','url'=>U('/Admin/ForgetPw/SmsValidate/index')));
                     }else{
                        $this->error(array('state'=>'0'));
                     }
                 }
            }
               
        }
        if ($phone==$resinfo['phone']) {
             $_SESSION['phone'] =$phone;
             $this->success(array('state'=>'1','url'=>U('/Admin/ForgetPw/SmsValidate/index')));
        }else{
             $this->error(array('state'=>'0'));
        }
    }
  
}
