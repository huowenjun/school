<?php
/*
 * 验证账号
 * 生成时间：2016-01-01
 * 作者：qianzhiqiang
 * 修改时间：
 * 修改备注：
 */
namespace Admin\Controller\ForgetPw;
use Think\Controller;
class AccountValidateController extends Controller{
    //put your code here
    
    public function index(){

        $this->display();
    }
    //账号验证
    public function Theverification(){
        $username = I('post.username');//用户名
        $code = I('post.code');//验证码
        $where['username'] = $username;
        $User = D('AdminUser');//用户表
        $resinfo = $User->where($where)->find();
        if (empty($resinfo)) {
            $this->error(array('state'=>'2'));
        }
        $verify = new \Think\Verify();
        if(!($verify->check($code))){
           $this->error(array('state'=>'3'));
        }else{
           $_SESSION['username'] = $username;//存储username
           $this->success(array('state'=>'1','url'=>U('/Admin/ForgetPw/SelectCell/index')));
        }

    }
    //生成验证码
    public function captcha()
    {
        $config = array(
            'fontSize' => 14, // 验证码字体大小
            'length' => 4, // 验证码位数
            'useNoise' => false,
            'useCurve' =>false,
            'imageW'  =>100,
            'imageH'=>30,
            //'useImgBg' =>true,
        ); // 关闭验证码杂点
        $Verify = new \Think\Verify($config);
        $Verify->entry();
    }


   
    
}
