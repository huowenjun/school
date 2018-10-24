<?php
/*
 * 后台admin登录控制器
 * 生成时间：2016-01-01
 * 作者：qianzhiqiang
 * 修改时间：2018.6.26
 * 修改备注：
 */
namespace Admin\Controller;
use Admin\Controller\BaseController;
use Admin\Model\UserModel;

class LoginController extends BaseController{

    public function index(){

        $this->display();
    }

    public function groud(){
        $this->display();
    }

    /*
     * 登录接口
     * index.php/Admin/Login/login_handle
     * post 请求
     * 参数：
     *      username
     *      password
     */
    public function login_handle()
    {
        $userName           =   I('post.username');//admin_name
        $passWord           =   I('post.password');//admin_pwd
        $system_model = I('post.system_model',2);//1-android 0-ios 2-pc
        if (empty($userName) || empty($passWord)) {
            $this->error('用户名或密码未填写！');
        }
        $map['_complex'] = [
            'username'=>$userName,
            'phone'=>$userName,
            '_logic'=>'or'
        ];
        $userM = new UserModel();
        $userInfo = $userM->lookup($map);// 查询用户
        if($userInfo){
            if($userInfo['password']==md5($passWord)){
                $Admin = D('Adminuser','Logic');
                $userInfo['system_model'] = $system_model;
                if ($Admin->setLogin($userInfo)){//记录session
                    C('CONTROLLER_LEVEL',2);
                    $this->success('登录成功',U('/Admin/Index'));
                } else {
                    $this->error($Admin->getError());
                }
            }else{
                $this->error('用户或密码错误');
            }
        }else{
            $this->error('用户或密码错误');
        }
    }

    /*
     * 退出接口
     * index.php/Admin/Login/logout
     * get 请求
     */
    public function logout(){
        $Admin = D('Adminuser','Logic');
        $Admin->logOut();//删除session数据
        $this->success('退出系统成功',U('/Admin/Login/'),1,false);
    }

    //修改密码
    public function edit(){
        
        $this->display();
    }

    /*
     * 修改密码接口
     * index.php/Admin/Login/edit_password
     * post 请求
     * 参数：
     *      user_id   用户id
     *      ypassword  原密码
     *      password   新密码
     */
    public function edit_password(){
        $user_id = I('post.user_id');
        $oldPassword = I('post.ypassword');
        $newPassword = I('post.password');
        if (empty($user_id)) {
            $this->error('参数错误！');
        }
        if (empty($oldPassword)) {
            $this->error('旧密码不能为空');
        }
        if (empty($newPassword)) {
            $this->error('新密码不能为空');
        }
        $where['user_id'] = $user_id;
        $data['password'] = md5($newPassword);
        $data['modif_time'] = date('Y-m-d H:i:s');
        //查询旧密码是否正确
        $userM = new UserModel();
        $userInfo = $userM->lookup(['user_id'=>$user_id]);// 查询用户
        if (md5($oldPassword)!=$userInfo['password']) {
            $this->error('旧密码不正确');
        }else{
            $bool = $userM->modify($where,$data);
            if ($bool==0) {
                $this->error('密码修改失败');
            }else{
                $this->success('密码修改成功，请重新登陆',U('/Admin/Login/'),1,false);
            }
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
    }
}
