<?php

namespace Api\Controller;

use Think\Controller;

class ChatInfoController extends Controller
{
    //消息上传
    public function uploadMsg()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $imei = I('post.imei');
        if ($imei) {
            $data['imei'] = $imei;
        } else {
            $this->error('imei号不能为空');
        }
        $data['user_id'] = I('post.user_id');
        $data['create_time'] = time();
        $data['ispush'] = 1;
        $data['type'] = I('post.type');//消息类型 1文字 2图片 3语音

        if ($data['type'] == 1) {//文字
            $data['contents'] = I('post.contents');//原始消息
        } else {
            //文件上传
            $upload_info = upload_file();

            if ($upload_info['status'] == 0) {
                $this->error($upload_info['info']);
            }
            $data['contents'] = $upload_info['info'];
        }
        $chat_id = M('Chatinfo')->add($data);
        if ($chat_id) {
            //TCP命令存储
            $redis = redis_instance('39.107.98.114');
            if ($data['type'] == 3) {//amr音频
                $task = $this->encry_amr($_SERVER['DOCUMENT_ROOT'] . $upload_info['info'], $imei);
            } elseif ($data['type'] == 1) {//文字
                $amr_true2 = $data['contents'];
                $amr_true2 = utf8_unicode($amr_true2);
                $task_content = 'MESSAGE,' . $amr_true2;
                $task_len = get_len_16($task_content);
                $task = '[3G*' . $imei . '*' . $task_len . '*' . $task_content . ']';
            }
            $redis->sAdd('task:' . $imei, $task);
            $this->success('发送成功');
        }
    }

    //返回微聊数据
    public function getUnReadChatMsg()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $imei = I('post.imei');
        if (!$imei) {
            $this->error('非法数据');
        }
        $dataOne = D('chatinfo')
            ->field('id,imei,user_id,contents,create_time,type')
            ->where(['imei' => $imei, 'ispush' => 0])
            ->find();
        if ($dataOne) {
            $bool = D('chatinfo')->where(['id' => $dataOne['id']])->save(['ispush' => 1]);
            if ($bool) {
                $dataOne['create_time'] = date('Y-m-d H:i:s', $dataOne['create_time']);
                if ($dataOne['type'] == '3') {
                    if ($dataOne['user_id'] == '0') {//手表语音文件
                        $dataOne['contents'] = 'http://39.107.98.114/swoole/' . strstr($dataOne['contents'],'Chatfile');
                    } else {//APP语音文件
                        $dataOne['contents'] = 'http://school.xinpingtai.com' . $dataOne['contents'];
                    }
                }

                $this->success($dataOne);
            } else {
                $this->error('网络异常');
            }
        } else {
            $this->error('暂无数据');
        }
    }

    //加密amr文件 [CS*YYYYYYYYYY*LEN*TK,AMR 格式音频数据]
    private function encry_amr($file_name = '666.amr', $imei)
    {
        $amr_content = file_get_contents($file_name);
        $amr_content_16 = bin2hex($amr_content);//将2进制文件转换成16进制
        //执行amr协议字串替换
        $amr_content_16_ARR = str_split($amr_content_16, 2);
        $available = '';
        foreach ($amr_content_16_ARR as $amr) {
            $available .= str_replace(array('7d', '5b', '5d', '2c', '2a'), array('7d01', '7d02', '7d03', '7d04', '7d05'), $amr);
        }

        //将16进制转成2进制并存储
        $amr_true2 = hex2bin($available);
        $task_content = 'TK,' . $amr_true2;
        $task_len = get_len_16($task_content);
        $task = '[3G*' . $imei . '*' . $task_len . '*' . $task_content . ']';
        return $task;
    }
}