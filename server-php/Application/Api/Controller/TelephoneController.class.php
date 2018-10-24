<?php
namespace Api\Controller;

use Api\Controller\BaseController;

class TelephoneController extends BaseController
{
    public function init(){

    }
    //设置ios语音通话device_token
    public function setTelToken()
    {
        $user_id = I("post.user_id");
        $ios_tel_token = I("post.ios_tel_token");

        $data['ios_tel_token'] = '';
        M('user', 'think_')->where("ios_tel_token = '{$ios_tel_token}'")->save($data);

        //获取旧的token
        $oldToken = M('user', 'think_')->where("user_id = '{$data['user_id']}'")->getField("ios_tel_token");
        $data['user_id'] = $user_id;
        $data['ios_tel_token'] = $ios_tel_token;

        if($oldToken != $ios_tel_token){
            //将token写入数据库
            $res = M('user', 'think_')->save($data);
        }else{
            $res = 1;
        }

        if ($res) {
            $this->success('设置成功');
        } else {
            $this->error('设置失败');
        }
    }

    //执行IOS消息推送
    public function msgPush()
    {
        set_time_limit(0);
        $user_id = I("post.user_id");//接听者的ID
        $callUserId = $this->getUserId();//拨打者的ID
        $type = I("post.type");//消息类型 语音或者视频

        if(!$user_id || !$type || !$callUserId){
            $this->error('参数错误');
        }
        //获取ios用户device_token
        $whereStr['user_id'] = $user_id;
        //接听者的info
        $user_info = M('user', 'think_')->field("name,ios_tel_token,sex,type")
                                        ->where($whereStr)
                                        ->find();
        $callUserInfo = M('user', 'think_')->field("name,ios_tel_token,sex,type")
                                            ->where("user_id = {$callUserId}")
                                            ->find();

        //ios_token信息不存在
        if (!$user_info['ios_tel_token']) {
            $this->error('ios_token信息不存在');
        } else {
            //初始化消息推送接口
            $passphrase = '199026';
            $tmpArr = array(
                'user_id' => $callUserId,//拨打者的ID
                'username' => $callUserInfo['name'],//拨打者的名字
                'sex' => $callUserInfo['sex'],
                'type' => $type,
            );
            $message = json_encode($tmpArr);
//            $message = 'My first push test!';


            $ctx = stream_context_create();
            stream_context_set_option($ctx, 'ssl', 'allow_self_signed', true);
            stream_context_set_option($ctx, 'ssl', 'verify_peer', false);
            if ($user_info['type'] == 4) {
                $crtName = '/Public/ios_push_cert/ck_p.pem';
            } else {
                $crtName = '/Public/ios_push_cert/ck_t.pem';
            }

            stream_context_set_option($ctx, 'ssl', 'local_cert', $_SERVER['DOCUMENT_ROOT'] . $crtName);//家长证书
            stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

            // Open a connection to the APNS server
            //这个为正是的发布地址
            $fp = stream_socket_client('ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
            //这个是沙盒测试地址，发布到appstore后记得修改哦
            //$fp = stream_socket_client('ssl://gateway.sandbox.push.apple.com:2195', $err,$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
//            $fp=stream_socket_client("udp://127.0.0.1:1113", $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
            if (!$fp)
                exit("Failed to connect: $err $errstr" . PHP_EOL);

//            echo 'Connected to APNS' . PHP_EOL;

            // Create the payload body
            $body['aps'] = array(
                'alert' => $message,
                'sound' => 'default'
            );

            // Encode the payload as JSON
            $payload = json_encode($body);

            // Build the binary notification
            $msg = chr(0) . pack('n', 32) . pack('H*', $user_info['ios_tel_token']) . pack('n', strlen($payload)) . $payload;

            // Send it to the server
            $result = fwrite($fp, $msg, strlen($msg));

            if (!$result)
                $this->error('拨号失败');
            else
                $this->success('拨号成功');

            // Close the connection to the server
            fclose($fp);
        }
    }
    public function test(){
        echo 222;
    }
    //消息撤回功能接口
    public function recallMsg(){
        $chatid = I('post.chatid');
        $msgInfo = M('Chatinfo')->where("chatid = '{$chatid}'")->getField('chatid');
        if($msgInfo){
            //修改消息状态
            $data['ispush'] = 2;//撤回状态
            M('Chatinfo')->where("chatid = '{$chatid}'")->save($data);
            $this->success('recall success');
        }else{
            $this->error('recall error');
        }
    }

    //接收方撤回消息处理
    public function recallMsgShow(){
        //获取当前用户的user_id
        $user_id = I('post.user_id');
        $user_type = I('post.user_type');

        //获取撤回操作,接收端未处理的消息
        $whereStr['ispush'] = 2;
        if($user_type == 3){//接收人是教师
            $whereStr['frm'] = 0;
            $whereStr['teaid'] = $user_id;
            $fieldStr = 'chatid,parid as sender_id,modify_time,recvtime';
        }elseif ($user_type == 4){//接收人是家长
            $whereStr['frm'] = 1;
            $whereStr['parid'] = $user_id;
            $fieldStr = 'chatid,teaid as sender_id,modify_time,recvtime';
        }

        $msgList = M('Chatinfo')->field($fieldStr)->where($whereStr)->select();
        if($msgList){
            //修改消息状态
            foreach ($msgList as $key=>$value){
                $data['ispush'] = 3;//撤回状态
                M('Chatinfo')->where("chatid = '{$value['chatid']}'")->save($data);
            }
        }
        $this->success($msgList);
    }
}

?>

