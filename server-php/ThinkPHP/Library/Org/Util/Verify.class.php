<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Org\Util;
class Verify {

    protected $config =	array(
        'seKey'     =>  'ThinkPHP.CN',   // 验证码加密密钥
        'codeSet'   =>  '1234567890abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY',             // 验证码字符集合
        'expire'    =>  1800,            // 验证码过期时间（s）
        'reset'     =>  true,           // 验证成功后是否重置
        'content'   => '您的验证码：{n1}，欢迎登陆WIFI，请在{n2}分钟内填写',
        'length'    =>  5,               // 验证码位数
    );

    /**
     * 架构方法 设置参数
     * @access public
     * @param  array $config 配置参数
     */
    public function __construct($config=array()){
        $this->config   =   array_merge($this->config, $config);
    }

    /**
     * 使用 $this->name 获取配置
     * @access public
     * @param  string $name 配置名称
     * @return multitype    配置值
     */
    public function __get($name) {
        return $this->config[$name];
    }

    /**
     * 设置验证码配置
     * @access public
     * @param  string $name 配置名称
     * @param  string $value 配置值
     * @return void
     */
    public function __set($name,$value){
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }

    /**
     * 检查配置
     * @access public
     * @param  string $name 配置名称
     * @return bool
     */
    public function __isset($name){
        return isset($this->config[$name]);
    }

    /**
     * 验证验证码是否正确
     * @access public
     * @param string $code 用户验证码
     * @param string $id 验证码标识
     * @return bool 用户验证码是否正确
     */
    public function check($code, $id = '') {
        $key = $this->authcode($this->seKey).$id;
        // 验证码不能为空
        $secode = session($key);
        if(empty($code) || empty($secode)) {
            return false;
        }
        // session 过期
        if(NOW_TIME - $secode['verify_time'] > $this->expire) {
            session($key, null);
            return false;
        }

        if($this->authcode(strtoupper($code)) == $secode['verify_code']) {
            $this->reset && session($key, null);
            return true;
        }

        return false;
    }

    /**
     * 输出验证码并把验证码的值保存的session中
     * 验证码保存到session的格式为： array('verify_code' => '验证码值', 'verify_time' => '验证码创建时间');
     * @access public
     * @param string $phone 电话号码
     * @param string $id 要生成验证码的标识
     * @return void
     */
    public function entry($phone,$id='') {
        if(!ctype_digit($phone)) return false;
        $code = array(); // 验证码
        for ($i = 0; $i<$this->length; $i++) {
            $code[$i] = $this->codeSet[mt_rand(0, strlen($this->codeSet)-1)];
        }
        $code = strtolower(implode('', $code));
        //发送验证码短信
        /*$message = $this->content;
        $message = str_replace('{n1}', $code, $message);
        $message = str_replace('{n2}', $this->expire/60, $message);*/
        $message = '您好，您的验证码是123456';
        $ret = $this->sendSms($phone,$message);
        if($ret) {
            // 保存验证码
            $key        =   $this->authcode($this->seKey);
            $code       =   $this->authcode($code);
            $secode     =   array();
            $secode['verify_code'] = $code; // 把校验码保存到session
            $secode['verify_time'] = NOW_TIME;  // 验证码创建时间
            session($key.$id, $secode);
            return true;
        }else{
            return false;
        }


    }

    /* 加密验证码 */
    private function authcode($str){
        $key = substr(md5($this->seKey), 5, 8);
        $str = substr(md5($str), 8, 10);
        return md5($key . $str);
    }

    private function sendSms($phone,$msg){
        vendor('ChuanglanSmsHelper.ChuanglanSmsApi');
        $clapi  = new \ChuanglanSmsApi();
        $result = $clapi->sendSMS($phone, $msg,'true');
        $result = $clapi->execResult($result);
        //var_dump($result);
        if($result[1]==0){
            return true;
        }else{
            return false;
        }
    }
    private function sendSms1($phone,$msg){

        $message = rawurlencode(mb_convert_encoding($msg, "gb2312", "utf-8"));
        $url = 'http://114.255.71.158:8061';
        $url = 'http://q.hl95.com:8061/';
        $url .= '?username=shxx&password=shxx123&message='.$msg.'&phone='.$phone.'&epid=120759&linkid=&subcode=';
        //dump($url);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $ret = curl_exec($ch);
        //var_dump($ret);
        if (curl_errno($ch)) {
            trace(curl_error($ch) , '发送短信出错', 'NOTIC', true);
            return false;
        }
        curl_close($ch);
        if($ret == '00')
            return true;
        else
            return false;
        /*
         * 返回代码	代码说明
            00	提交成功
            1	参数不完整
            2	鉴权失败（包括：用户状态不正常、密码错误、用户不存在、地址验证失败，黑户）
            3	号码数量超出50条
            4	发送失败
            5	余额不足
            6	发送内容含屏蔽词
            7	短信内容超出350个字
            72	内容被审核员屏蔽
            8	号码列表中没有合法的手机号码
            9	夜间管理，不允许一次提交超过20个号码
            ERR IP:XX.XX.XX.XX	IP验证未通过
         */
    }
    
}