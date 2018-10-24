<?php
namespace Admin\Model;

use Think\Model;

class UserModel extends Model{
    protected $trueTableName='think_user';

    public function lookup($arr){
        $userInfo = D('user')->field('user_id,status,password,username,name,phone,api_id,security_key,type')->where($arr)->find(); // 查询用户
        return $userInfo;
    }
    public function modify($where,$data){
        $bool = D('user')->where($where)->save($data);//修改数据
        return $bool;
    }
    public function addUser($data){
        $id = D('user')->add($data);
        return $id;
    }
}