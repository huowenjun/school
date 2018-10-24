<?php
class Push{
    protected $name = NUll;//推送名称
    protected $contents = array();//消息详情
    protected $pushObj;//推送对象

    public function __construct($name)
    {
        $this->name = $name;
        require_once(dirname(__FILE__) . '/' .$name.'/'.$name.'.class.php');
    }
    /**
     * 获取推送key及secret
     * @param $msg_client str 客户端类型 Upush-android-teacher
     * */
    private function get_push_key($msg_client)
    {
        switch ($msg_client) {
            case 'UPush-android-teacher':
//                $key = '58215e1a4ad1563bfb002c6f';
//                $secret = 'hbyngcsuzpzhkxl6tltappr6uwcbuia3';
                $key = '5857421404e20546ee002512';
                $secret = '23ap5tyrq1w0cschsh8t2g4s0dykabuy';
                break;
            case 'UPush-android-parent':
                $key = '5857421404e20546ee002512';
                $secret = '23ap5tyrq1w0cschsh8t2g4s0dykabuy';
                break;
            case 'UPush-ios-teacher':
//                $key = '58525c2f5312dd1b9f001282';
//                $secret = 'kzxnysetsopzdqcbbkdm89tkt1jzywq2';
                $key = '58801aea65b6d67a5200072d';
                $secret = 'afaonzqw9qy3hmkvnr4efoatzhv8rpxi';
                break;
            case 'UPush-ios-parent':
                $key = '58801aea65b6d67a5200072d';
                $secret = 'afaonzqw9qy3hmkvnr4efoatzhv8rpxi';
                break;

            case 'MIPush-android-teacher':
//                $key = 'your app packagename';
//                $secret = 'c1uamUGm0xvglT+5yE0gAQ==';
                $key = 'your app packagename';
                $secret = 'A0cT9QlJT9kD2KTuKeqmgA==';
                break;
            case 'MIPush-android-parent':
                $key = 'your app packagename';
                $secret = 'A0cT9QlJT9kD2KTuKeqmgA==';
                break;

            case 'HWPush-android-teacher':
//                $key = '100050509';
//                $secret = '7ac025af7e4b72360f3d0717d212f848';
                $key = '100050369';
                $secret = 'f6db70503452ce80a4d24d48cd98a4e6';
                break;
            case 'HWPush-android-parent':
                $key = '100050369';
                $secret = 'f6db70503452ce80a4d24d48cd98a4e6';
                break;

            case 'MZPush-android-teacher':
//                $key = '111066';
//                $secret = 'd06e9c139f6f4c5fbdd8516a577e80fc';
                $key = '111065';
                $secret = '53227d8e468b4ba7b54255f3a1650d37';
                break;
            case 'MZPush-android-parent':
                $key = '111065';
                $secret = '53227d8e468b4ba7b54255f3a1650d37';
                break;
        }
        $data = array(
            'key' => $key,
            'secret' => $secret
        );
        return $data;
    }


    //android家长端推送
    public function sendAndroidPushP($tokenArr,$contents){
        $msgType = $this->name.'-android-parent';
        $keys = $this->get_push_key($msgType);
        $pushObj = new $this->name($keys['key'],$keys['secret']);

        foreach($tokenArr as $key=>$value){
            $pushObj->sendAndroidUnicast($value,$contents);
        }
    }

    //Android教师端推送
    public function sendAndroidPushT($tokenArr,$contents){
        $msgType = $this->name.'-android-teacher';
        $keys = $this->get_push_key($msgType);
        $pushObj = new $this->name($keys['key'],$keys['secret']);

        foreach($tokenArr as $key=>$value){
            $pushObj->sendAndroidUnicast($value,$contents);
        }
    }

    //ios家长端推送
    public function sendIosPushP($tokenArr,$contents){
        $msgType = $this->name.'-ios-parent';
        $keys = $this->get_push_key($msgType);
        $pushObj = new $this->name($keys['key'],$keys['secret']);
        foreach($tokenArr as $key=>$value){
            $pushObj->sendIosUnicast($value,$contents);
        }
    }

    //ios教师端推送
    public function sendIosPushT($tokenArr,$contents){
        $msgType = $this->name.'-ios-teacher';
        $keys = $this->get_push_key($msgType);
        $pushObj = new $this->name($keys['key'],$keys['secret']);

        foreach($tokenArr as $key=>$value){
            $pushObj->sendIosUnicast($value,$contents);
        }
    }
}

?>