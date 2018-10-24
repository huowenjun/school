<?php

namespace Api\Controller;

use \Think\Controller;

class UserMessageController extends Controller
{
    //获取消息列表
    public function getMessageList()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $m_type = I('post.m_type');
        if (!$m_type) {
            $this->error('参数错误');
        }
        $page = I('post.page', 1);
        $pagesize = I('post.pagesize', 20);
        $mymessage = M('Mymessage');
        $whereStr['m_type'] = $m_type;
        $whereStr['is_effect'] = 1;
        $list = $mymessage
            ->field('m_id,title,describe,release_time,images_path')
            ->where($whereStr)
            ->select();
        $count = $mymessage
            ->where($whereStr)
            ->count();

        $data = getPageList($list, $count, $page, $pagesize);
        $this->success($data);
    }

    //获取通知类消息列表
    public function getNotifyList()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $user_id = I('post.user_id');
        $m_type = I('post.m_type');
        if (!$m_type || !$user_id) {
            $this->error('参数错误');
        }
        $page = I('post.page', 1);
        $pagesize = I('post.pagesize', 20);
        $mymessage = M('Mymessage');
        $whereStr['to_user'] = $user_id;
        $whereStr['m_type'] = $m_type;
        $whereStr['is_effect'] = 1;
        $list = $mymessage
            ->field('m_id,title,content,create_time')
            ->where($whereStr)
            ->order('m_id desc')
            ->select();
        $count = $mymessage
            ->where($whereStr)
            ->count();
        $data = getPageList($list, $count, $page, $pagesize);
        $this->success($data);
    }

    //获取消息详情
    public function getMessageDetail()
    {
        $id = I('get.m_id/d');
        $model = M('Mymessage');
        $info = $model
            ->field('m_id,title,content,release_time,images_path')
            ->where(array('m_id' => $id))
            ->find();
        $info['content'] = str_replace('<img src="/uploads', '<img width="100%" src="http://school.xinpingtai.com/uploads', $info['content']);
        $this->success($info);
    }
}