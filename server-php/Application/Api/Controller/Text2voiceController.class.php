<?php
/*
*家庭作业
*by ruiping date 20161115
*/

namespace Api\Controller;

use Api\Controller\BaseController;
use Think\Model;

class Text2voiceController extends BaseController
{

    /*
    *语音列表添加
    */
    public function add()
    {

        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/Homework/add'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法');
        }
        $data['from_id'] = I('post.from_id');
        $data['imei'] = I('post.imei');
        $data['content'] = I('post.content');

        $data['unicode_content'] = utf8_unicode($data['content'], 1);
        $data['create_time'] = date('Y-m-d H:i:s');

        if (M('Text2voice')->create($data)) {
            $id = M('Text2voice')->add($data);

        }
        if (!$id) {
            $this->error('发送失败');
        } else {
            $this->success('发送成功');
        }
    }

    /**
     * 数据查询接口
     *
     */
    public function query()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/Homework/add'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法');
        }
        $pagesize = 20;
        $page = I('post.page',1);
        $user_id = $this->getUserId();
        $list = M('Text2voice')->field('content,create_time')
            ->where("from_id = '{$user_id}'")
            ->page($page,$pagesize)
            ->order('id desc')
            ->select();
        $count = M('Text2voice')
            ->where("from_id = '{$user_id}'")
            ->count();

        $page_szie = ceil($count/$pagesize);
        $arr['total_page'] = $page_szie;
        $arr['total_count'] = $count;
        $arr['page'] = $page;
        $arr['content'] = $list;
        $this->success($arr);
    }
}