<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/5 0005
 * Time: 13:39
 */

class Easemob
{

    private $orgName;
    private $appName;
    private $clientID;
    private $clientSecret;
    private static $_instance;

    private function __construct()
    {
        $this->orgName = '1152171201178499';
        $this->appName = 'shuhai-anfang';
        $this->clientID = 'YXA6ecX9ANZsEeepVys2kFMPEw';
        $this->clientSecret = 'YXA69MLdG6oM-FXGLvStju-_qZ4ja3U';
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    public static function getInstance()
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /*
     * 发送消息
     * $data array(
     *  file 文字 图片 语音
     *  type 消息类型 1文本 2图片 3语音
     *  imei 唯一)
     */
    public function sendMseeage($data)
    {
        $jsonStr = $this->processingData($data);
        $url = 'https://a1.easemob.com/' . $this->orgName . '/' . $this->appName . '/messages';
        $result = $this->curlEase($url, $jsonStr, 'POST', $header = array($this->getToken()));
        return $result;
    }

    /*
     * 处理数据
     */
    protected function processingData($data)
    {
        if ($data['type'] == 2 || $data['type'] == 3) {//图片，语音先上传服务器
            $fileArr = $this->fileUpload(file_get_contents($data['file']));
            if ($data['type'] == 2) {
                $jsonData['msg'] = [
                    'type' => 'img',
                    'url' => $fileArr['uuid'],
                    'filename' => basename($data['file']),
                    'secret' => $fileArr['share-secret'],
                    'size' => [
                        'width' => getimagesize($data['file'])[0],
                        'height' => getimagesize($data['file'])[1]
                    ],
                ];
            } elseif ($data['type'] == 3) {
                $jsonData['msg'] = [
                    'type' => 'audio',
                    'url' => $fileArr['uuid'],
                    'filename' => basename($data['file']),
                    'length' => 10,
                    'secret' => $fileArr['share-secret']
                ];
            }
        } else {//文本
            $jsonData['msg'] = [
                'type' => 'txt',
                'msg' => $data['file']
            ];
        }
        $jsonData['target_type'] = "users";
        $db = new PDO('mysql:host=59.110.0.250;dbname=school', 'root', 'Shuhaixinxi1502');
        $userIdArr = $db->query('select user_id from sch_student_group_access where stu_id = (select stu_id from sch_student where imei_id=' . $data['imei'] . ')')->fetchAll(PDO::FETCH_ASSOC);
        $strId = [];
        foreach ($userIdArr as $key => $value) {
            $strId[$key] = $value['user_id'];
        }
        $jsonData['target'] = $strId;
        $jsonData['from'] = $data['imei'];
        return json_encode($jsonData);

    }

    /*
     * 文件上传
     * filepath 文件路径
     */
    protected function fileUpload($filePath)
    {
        $url = 'https://a1.easemob.com/' . $this->orgName . '/' . $this->appName . '/chatfiles';
        $data = array(
            'file' => $filePath,
        );
        $header = array('Content-type: multipart/form-data', $this->getToken(), "restrict-access:true");
        $result = $this->curlEase($url, $data, 'POST', $header);
        return $result['entities'][0];
    }

    /*
     * 获取token
     * {
          "access_token":"YWMtWY779DgJEeS2h9OR7fw4QgAAAUmO4Qukwd9cfJSpkWHiOa7MCSk0MrkVIco",
          "expires_in":5184000,
          "application":"c03b3e30-046a-11e4-8ed1-5701cdaaa0e4"
        }
     */
    protected function getToken()
    {
        $redis = $this->redis_instance();
        if ($token = $redis->get('token')) {
            return $token;
        } else {
            $url = 'https://a1.easemob.com/' . $this->orgName . '/' . $this->appName . '/token';
            $data = array(
                'grant_type' => "client_credentials",
                'client_id' => $this->clientID,
                'client_secret' => $this->clientSecret,
            );
            $jsonStr = json_encode($data);
            $result = $this->curlEase($url, $jsonStr, 'POST', $header = array());

            $redis->set('token', "Authorization:Bearer " . $result['access_token']);
            $redis->expire('token', $result['expires_in'] / 1000);
            return "Authorization:Bearer " . $result['access_token'];
        }
    }

    /*
     * curl 请求
     * $url 跳转路由
     *  $data json数据
     *  type请求方法
     *  $getToken请求token
     */
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