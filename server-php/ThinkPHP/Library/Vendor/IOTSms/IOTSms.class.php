<?php
include_once(dirname(__FILE__) . "/phpdemo_func.php");

/**
 *发送物联网卡短信
 * 连接失败的常见问题
1：php.ini 配置中   default_socket_timeout的值不能为零
2：php.ini 配置中启用 php_soap.dll、php_xmlrpc.dll、php_curl.dll、php_openssl.dll
  */

class IOTSms
{

    private $svr_url = 'http://121.41.17.233:7860/sms?wsdl';   // 服务器接口路径
    private $spid = '';    // 账号
    private $password = '';    // 密码
    private $accessCode = '';    // 接入码
    private $soap = '';    // 操作对象

    public function __construct($spid = '660008', $password = 'nm4zWd', $accessCode = '106489910201540008')
    {
        $this->spid = $spid;
        $this->password = $password;
        $this->accessCode = $accessCode;
        $this->soap = new SoapClient($this->svr_url);

    }
    /**
     * 下发物联网卡短信
     * 多个号码使用,号分割  如 18511214006,18511214005
     * */
    public function sendIOTMsg($mobile='',$content='')
    {
        //spid:账号,password:密码,accessCode:下发号码,content:下发内容,mobileString:号码列表
        $result = $this->soap->Submit($this->spid, $this->password, $this->accessCode, $content, $mobile);
        return $result;
    }

    /**
     * 查询账户余额
     *
     * */
    public function getMsgBalance(){
        $result = $this->soap->QueryBalance($this->spid, $this->password);
        return $result;
    }
    /**
     * 获取回复短信息
     *
     * */
    public function getMsgReply(){
        $result = $this->soap->QueryMo($this->spid, $this->password);
        file_put_contents('msgtxt.txt',$result);
        $msgArr = explode("\n", $result);
        array_shift($msgArr);
        foreach ($msgArr as $key => $value) {
            if($value != ''){
                $msgInfo = explode('#@#',$value);
                $data['mobile'] = $msgInfo[4];
                $data['content'] = $msgInfo[5];
                $data['replay_time'] = $msgInfo[6];

                M('CardCommandReply')->create();
                $res = M('CardCommandReply')->add($data);
            }
        }
        return $result;
    }
    /**
     * 获取短信状态
     *
     * */
    public function getMsgStatus(){
        $result = $this->soap->QueryReport($this->spid, $this->password);
        return $result;
    }
}





?>