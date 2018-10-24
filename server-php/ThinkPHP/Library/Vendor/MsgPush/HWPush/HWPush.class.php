<?php

class HWPush
{
    private $tokenStr = NULL;
    private $appid = NULL;

    public function __construct($appid = '', $secret = '')
    {
        $this->appid = $appid;
        $this->tokenStr = 'grant_type=client_credentials&client_secret=' . $secret . '&client_id=' . $appid;
    }

    //获取token
    public function get_access_token()
    {
        if (!S($this->appid)) {
            $url = 'https://login.vmall.com/oauth2/token';
            $data = $this->tokenStr;
            $res = $this->curl_post($data, $url);

            if ($res['access_token']) {
                S($this->appid, $res['access_token'], $res['expires_in']);
            }
        }

        return (S($this->appid));
    }

    public function curl_post($data, $url)
    {
        $headers = array(
            "Content-type: application/x-www-form-urlencoded"
        );
        $host = array("Host: login.vmall.com");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $host);
        $result = curl_exec($ch);
        curl_close($ch);
        if ($result == NULL) {
            return 0;
        }
        return json_decode($result, true);
    }

    //发送Push消息
    public function sendAndroidUnicast($tikenArr, $content)
    {
        $url = 'https://api.push.hicloud.com/pushsend.do?nsp_ctx=' . urlencode('{"ver":"1", "appId":"' . $this->appid . '"}');
        $access_token = $this->get_access_token();
        if($content['user_type'] == 3){
            $intentStr = 'shuhai://com.xptschool.parent/'.$content['activity'].'_t?id='.$content['id'];
        }else{
            $intentStr = 'shuhai://com.xptschool.parent/'.$content['activity'].'_p?id='.$content['id'];
        }

        $pushData = array(
            'hps' => array(
                'msg' => array(
                    'type' => 3,//消息类型 1 透传异步消息 3 系统通知栏异步消息
                    'body' => array(//消息内容
                        'content' => $content['title'],
                        'title' => $content['title'],
                    ),
                    'action' => array(
                        'type' => 1,
                        'param' => array(
                            'intent' => $intentStr
                        ),
                    ),
                ),
            ),
        );
        if (!is_array($tikenArr)) {
            $tmp = array();
            $tmp[] = $tikenArr;
            $tikenArr = $tmp;
        }

        $payload = json_encode($pushData);
        $token_list = json_encode($tikenArr);

        $data = array(
            'access_token' => $access_token,
            'nsp_ts' => time(),
            'nsp_svc' => 'openpush.message.api.send',
            'device_token_list' => $token_list,
            'payload' => $payload,
        );
        $res = $this->curl_post($data, $url);

        if ($res['msg'] != 'success') {
            return $res;
        }
    }


}

?>