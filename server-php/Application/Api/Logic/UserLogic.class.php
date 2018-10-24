<?php
namespace Api\Logic;
use Common\Model\AdminUserModel;
class UserLogic extends AdminUserModel {

    /**
     * 登录用户
     * @param int $userId ID
     * @return bool 登录状态
     */
    public function setLogin($userInfo,$admin=0)
    {
        if(!$admin){
            // 更新登录信息
            $data = array(
                'user_id' => $userInfo['user_id'],
                'last_time' => date('Y-m-d H:i:s'),
                'ip' => get_client_ip(),
            );
            //写入系统记录
            $this->save($data);
            // 设置cookie
            $auth = array(
                'user_id' => $userInfo['user_id'],
                'admin' => $admin,
                'username' => $userInfo['username'],
                'name' => $userInfo['name'],
                'api_id'=>$userInfo['api_id'],
                'security_key'=>$userInfo['security_key'],
                'type'=>$userInfo['type'],
                'system_model' =>$userInfo['system_model'],
                'time'=>$userInfo['time']
            );
        }else{
            // 设置cookie
            $auth = array(
                'user_id' => -1,
                'admin' => $admin,
                'username' => 'superadmin',
                'name' => '',
                'api_id'=>$userInfo['api_id'],
                'security_key'=>'',
                'type'=>'',
                'system_model' =>$userInfo['system_model'],
                'time'=>$userInfo['time']
            );
        }
        session('admin_user', $auth);
        session('admin_user_sign', data_auth_sign($auth));
        F('admin_user_'.$userInfo['user_id'],get_client_ip());
        return true;
    }

    /**
     * 注销当前用户
     * @return void
     */
    public function logOut(){
        $user = session('admin_user');
        if($user){
            F('admin_user_'.$user['user_id'],NULL);
        }
        session('admin_user', null);
        session('admin_user_sign', null);
    }
    
}