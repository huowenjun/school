<?php
/*
*我的班级
*by Mengfanmin date 20170323
*/

namespace Api\Controller;

use Api\Controller\BaseController;
use Think\Controller;

class EasemobController extends BaseController
{
    private $orgName = '1152171201178499';
    private $appName = 'shuhai-anfang';
    private $clientId = 'YXA6ecX9ANZsEeepVys2kFMPEw';
    private $clientSecret = 'YXA69MLdG6oM-FXGLvStju-_qZ4ja3U';

    //获取环信管理员token
    protected function getToken()
    {
        $redis = $this->redis_instance();
        if ($token = $redis->get('token')) {
            return $token;
        } else {
            $url = 'https://a1.easemob.com/' . $this->orgName . '/' . $this->appName . '/token';
            $data = array(
                'grant_type' => "client_credentials",
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            );
            $jsonStr = json_encode($data);
            $result = $this->curlEase($url, $jsonStr, 'POST', $header = array());
            $redis->set('token', "Authorization:Bearer " . $result['access_token']);
            $redis->expire('token', $result['expires_in'] / 1000);
            return "Authorization:Bearer " . $result['access_token'];
        }
    }

    //批量注册
    public function register($userid)
    {
//        $userid = I('get.user_id', '415387');
        $where['user_id'] = array('in', $userid);
        $userList = M('User', 'think_')->where($where)->field('user_id ,name')->select();
        if (!$userList) {
            $this->error('该用户不存在');
        }
        $dataArr = array();
        foreach ($userList as $key => $value) {
            $data = array(
                'username' => $value['user_id'],
                'password' => md5('SHUHAIXINXI' . $value['user_id']),
                'nickname' => $value['name'],
            );
            $dataArr[] = $data;
        }
        $jsonStr = json_encode($dataArr);

        $url = 'https://a1.easemob.com/' . $this->orgName . '/' . $this->appName . '/users';
        $header = array($this->getToken());
        $result = $this->curlEase($url, $jsonStr, 'POST', $header);
        return ($result);
    }

    public function useradd()
    {
        ini_set('max_execution_time', '0');
        //获取平台所有用户
        $userId = M('User', 'think_')->page(1, 1000)->getField('user_id', true);
        echo M('User', 'think_')->getLastSql();
        foreach ($userId as $key => $value) {
            $this->register($value);
        }

        echo 'success';
    }

    public function useradd2()
    {
        ini_set('max_execution_time', '0');
        //获取平台所有用户
        $userId = M('User', 'think_')->page(2, 1000)->getField('user_id', true);
        echo M('User', 'think_')->getLastSql();
        foreach ($userId as $key => $value) {
            $this->register($value);
        }

        echo 'success';
    }

    public function useradd3()
    {
        ini_set('max_execution_time', '0');
        //获取平台所有用户
        $userId = M('User', 'think_')->page(3, 1000)->getField('user_id', true);
        echo M('User', 'think_')->getLastSql();
        foreach ($userId as $key => $value) {
            $this->register($value);
        }

        echo 'success';
    }

    public function useradd4()
    {
        ini_set('max_execution_time', '0');
        //获取平台所有用户
        $userId = M('User', 'think_')->page(4, 1000)->getField('user_id', true);
        echo M('User', 'think_')->getLastSql();
        foreach ($userId as $key => $value) {
            $this->register($value);
        }

        echo 'success';
    }

    public function useradd5()
    {
        ini_set('max_execution_time', '0');
        //获取平台所有用户
        $userId = M('User', 'think_')->page(5, 1000)->getField('user_id', true);
        echo M('User', 'think_')->getLastSql();
        foreach ($userId as $key => $value) {
            $this->register($value);
        }

        echo 'success';
    }

    //删除单个
    public function userdel($user_id)
    {
        $jsonStr = '';
        $url = 'https://a1.easemob.com/' . $this->orgName . '/' . $this->appName . '/users/' . $user_id;
        $header = array($this->getToken());
        $result = $this->curlEase($url, $jsonStr, 'DELETE', $header);

        return $result;
    }

    public function delLimit()
    {
        $jsonStr = '';
        $url = 'https://a1.easemob.com/' . $this->orgName . '/' . $this->appName . '/users?limit=500';
        //https://a1.easemob.com/easemob-demo/chatdemoui/users?limit=5
        $header = array($this->getToken());
        $result = $this->curlEase($url, $jsonStr, 'DELETE', $header);

        var_dump($result);die();


    }

    public function delLimit2()
    {
        $jsonStr = '';
        $url = 'https://a1.easemob.com/' . $this->orgName . '/' . $this->appName . '/users?limit=500';
        $header = array($this->getToken());
        $result = $this->curlEase($url, $jsonStr, 'DELETE', $header);

        var_dump($result);
    }

    protected function curlEase($url, $data, $type, $header)
    {
        $ch = curl_init();  // 初始化一个curl会话
        curl_setopt($ch, CURLOPT_URL, $url);
        if (count($header) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        // curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch); //执行一个cURL会话
        curl_close($ch);//关闭一个cURL会话

        return json_decode($output, true);
    }

    /*
     * 启动 redis
     */
    protected function redis_instance($host = '127.0.0.1', $port = '6379', $pwd = 'School1502')
    {
        $redis = new \Redis();
        $redis->connect($host, $port);
        $redis->auth($pwd);
        return $redis;
    }

}