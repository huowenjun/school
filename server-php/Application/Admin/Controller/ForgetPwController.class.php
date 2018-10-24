<?php
/*
 * 登录
 * 生成时间：2016-01-01
 * 作者：qianzhiqiang
 * 修改时间：
 * 修改备注：
 */
namespace Admin\Controller;
use Admin\Controller\BaseController;
class ForgetPwController extends BaseController{
    //put your code here
    
    public function index(){

        $this->display();
    }
    public function groud(){
        $this->display();
    }


    public function login_handle()
    {
        $userName = I('post.username');
        $passWord = I('post.password');
        $captcha = I('post.captcha','');
        if (empty($userName) || empty($passWord)) {
            $this->error('用户名或密码未填写！');
        }
        //验证码
        $verify = new \Think\Verify();
        if(!($verify->check($captcha))){
//            $this->error('验证码错误');
        }
        $isAdmin = 0;
        // 查询用户
        $map = array();
        $map['username'] = $userName;
        $userInfo = M('user','think_')->where($map)->find();
        if (empty($userInfo)) {
            if($userName == C('ADMIN_USER') && $passWord == C('ADMIN_PASSWORD')){
                $userInfo['user_id'] = 1;
                $isAdmin = 1;
            }else{
                $this->error('您输入的用户名或密码不正确！');
            }
            
        }
        if(!$isAdmin){
            if ($userInfo['status'] == 1) {
                $this->error('该用户已被禁止登录！');
            }
            if ($userInfo['password'] != md5($passWord)) {   
                $this->error('您输入的用户名或密码不正确！');
            }
        }
        
        $Admin = D('Adminuser','Logic');
        $groupInfo = $Admin->getGroup($userInfo['user_id']);
        // echo $userInfo['type'];die;
      
        if ($Admin->setLogin($userInfo,$groupInfo,$isAdmin)){
            C('CONTROLLER_LEVEL',2);
             // if ($userInfo['type'] == 4 || $userInfo['type'] == 3 ) {
             //      $this->success('登录成功',U('/Admin/Group/'));
             //   }else{
             //      $this->success('登录成功',U('/Admin/Index'));
             //   }
               $this->success('登录成功',U('/Admin/Index'));
        } else {
            $this->error($Admin->getError());
        }
    }
    
    public function logout(){
        $Admin = D('Adminuser','Logic');
        $Admin->logOut();
        $this->success('退出系统成功',U('/Admin/Login/'),1,false);
    }

    public function captcha()
    {
        $config = array(
            'fontSize' => 16, // 验证码字体大小
            'length' => 4, // 验证码位数
            'useNoise' => false,
            'useCurve' =>false,
            //'useImgBg' =>true,
        ); // 关闭验证码杂点

        $Verify = new \Think\Verify($config);
        $Verify->entry();
    }

    //修改密码
    public function edit(){
        
        $this->display();
    }

    public function edit_password(){
        $user_id = I('post.user_id');
        $ypassword = I('post.ypassword');
        $password = I('post.password');
        if (empty($user_id)) {
            $this->error('参数错误！');
        }
        if (empty($ypassword)) {
            $this->error('旧密码不能为空');
        }
        if (empty($password)) {
            $this->error('新密码不能为空');
        }
        $where['user_id'] = $user_id;
        $data['password'] = md5($password);
        $data['user_id'] = $user_id;

        //查询旧密码是否正确
        $userInfo = D('AdminUser')->where($where)->find();

        if (md5($ypassword)!=$userInfo['password']) {
            $this->error('旧密码不正确');
        }else{
            $ret = D('AdminUser')->where($where)->save($data);
        }

        if ($ret==0) {
            $this->error('密码修改失败');
        }else{
            $this->success('密码修改成功，请重新登陆',U('/Admin/Login/'),1,false);
        }

    }

    //获取用户信息
    public function get_user(){
        $user = I('get.user');
        $pass = I('get.pass');
        if (empty($user) || empty($pass)) {
            $this->error('用户名或密码未填写！');
        }
        $where['username'] = $user;
        $where['pass'] = md5($pass);
        $userInfo = D('AdminUser')->where($where)->find();
        if (empty($userInfo)) {
            $this->error('用户名或密码错误！');
        }else{//1学校管理员 2 系统用户 3 老师 4 家长
            if ($userInfo['type']==2) {
                $url = "/index.php/Admin/Login?";
                redirect($url);
            }
        }
        $this->success($userInfo); 
        
       // echo  $aa;
    }
}
