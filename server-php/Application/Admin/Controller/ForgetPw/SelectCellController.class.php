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
class SelectCellController extends Controller{
    //put your code here
    
    public function index(){
        $_SESSION['time'] = time();//存储当前时间
        $this->display();
    }
    //提供手机号
    public  function PhoneNumber(){
        $username = $_SESSION['username'];//用户名
        $where['username'] = $username;
        $User = D('AdminUser');//用户表
        $Teacher = D('Teacher');//教师表
        $School = D('SchoolInformation');//学校表
        $resinfo = $User->where($where)->find();
        if (empty($resinfo['phone'])) {
            $resinfo = $Teacher->where(array('user_name'=>$username))->find();
            if (empty($resinfo['phone'])) {
                $resinfo = $School->where(array('user_name'=>$username))->find();
                $phone1 = substr($resinfo['main_phone'],0,3);
                $phone2 = substr($resinfo['main_phone'],8,3);
                $phone3 = substr($resinfo['sub_phone'],0,3);
                $phone4 = substr($resinfo['sub_phone'],8,3);
                $data['phone'] = $phone1.'*****'.$phone2.','.$phone3.'*****'.$phone4;
                $this->success($data);
            }
        }
        $phone1 = substr($resinfo['phone'],0,3);
        $phone2 = substr($resinfo['phone'],8,3);
        $data['phone'] = $phone1.'*****'.$phone2;
        $this->success($data);
    }
       //超时
    public  function Timeout(){
        $time = time();//点击确认提示
        $Timediff = $time - $_SESSION['time'];
        //超过两分钟未操作 提示报错
        if ($Timediff >120) {
            $this->success(array('timeout'=>1));
        }
    }

}
