<?php

namespace Admin\Controller\Backlog;

use Admin\Controller\BaseController;
use Wap\Controller\IndexController;

class BacklogController extends BaseController
{
    public function index()
    {
        $this->display();
    }

    //查询接口
    public function query()
    {
        $user_type = $this->getType();
        if ($user_type != 2) {
            $this->error('暂无权限');
        }

        $page = I('get.page', 1);
        $pagesize = I('get.pagesize', 20);
        $username = I('get.username');
        $status = I('get.status');
        $name = I('get.name');

        if ($username) {
            $where['b.username'] = $username;
        }
        if ($name) {
            $where['a.name'] = $name;
        }

        if ($status != NULL) {
            $where['a.status'] = $status;
        }
        $list = M('Backlog')
            ->alias('a')
            ->field('a.*,b.username,b.name,apply_images')
            ->join('think_user b on a.user_id = b.user_id')
            ->where($where)
            ->page($page, $pagesize)
            ->select();
        $count = M('Backlog')
            ->alias('a')
            ->join('think_user b on a.user_id = b.user_id')
            ->where($where)
            ->count();

        $agent_status = C('REFUND_STATUS');
        foreach ($list as $key => $value) {
            $list[$key]['status'] = $agent_status[$value['status']];
            if($value['apply_images']){//判断是否有图片。
                $value['apply_images'] = explode(',',$value['apply_images']);
                foreach ($value['apply_images'] as $k=>$v)
                {
                    $value['apply_images'][$k]="<img src='$v' class='smallimg' width='auto' height='100'/>";
                }
                $value['apply_images'] = implode('<br>',$value['apply_images']);
                $list[$key]['apply_images'] = $value['apply_images'];
            }
        }
        $data = getPageList($list, $count, $page, $pagesize, 1);
        $this->success($data);
    }

    //获取用户详情
    public function getDetail()
    {
        $user_type = $this->getType();

        if (!$this->getUserId()) {
            $this->error('请登录后重试');
        }
        if ($user_type != 2) {
            $this->error('暂无权限');
        }

        $id = I('get.id');
        if (!$id) {
            $this->error('参数错误');
        }

        $user_info = M('Backlog')
            ->alias('a')
            ->field('a.*,b.username')
            ->join('think_user b on a.user_id = b.user_id')
            ->where("a.id = '{$id}'")
            ->find();

        if (!$user_info) {
            $this->error('未获取用户信息');
        }
        $user_info['company_name'] = M('User', 'think_')
            ->where("user_id = '{$user_info['company_id']}'")
            ->getField('name');

        $this->success($user_info);
    }

    //执行用户审核
    public function reply()
    {
        $user_type = $this->getType();
        if (!$this->getUserId()) {
            $this->error('请登录后重试');
        }
        if ($user_type != 2) {
            $this->error('暂无权限');
        }
        $id = I('post.id');
        $reply = I('post.reply');
        $status = I('post.status');
        if (!$id) {
            $this->error('参数错误');
        }
        $backlog_info = M('Backlog')->where("id = '{$id}'")->find();
        if (!$backlog_info) {
            $this->error('未获取该条信息');
        }
        $data['id'] = $id;
        $data['status'] = $status;
        $data['reply'] = $reply;
        $data['modif_time'] = date('Y-m-d H:i:s');
        M('User', 'think_')->startTrans();
        if ($status == 1) {//通过审核
            if($backlog_info['type']==1){
                $user_data['type'] = 12;
            }else{
                $user_data['type'] = 10;
                $user_data['name'] = $backlog_info['name'];
            }
            $user_data['user_id'] = $backlog_info['user_id'];
            $user_data['modif_time'] = date('Y-m-d H:i:s');
            $type_res = M('User', 'think_')->save($user_data);
            if (!$type_res) {
                M('User', 'think_')->rollback();
                $this->error('用户角色更新失败');
            }
        } else {
            $type_res = 1;
        }
        $backlog_res = M('Backlog')->where("id = '{$id}'")->save($data);
        if (!$backlog_res) {
            M('User', 'think_')->rollback();
            $this->error('用户角色更新失败');
        }
        if ($type_res && $backlog_res) {
            //发送短讯通知
            if($backlog_info['type']==0){
                if ($status == 1) {
                    $content = '恭喜您,您的代理商审核已经通过,业务二维码已下发，请您重新登录信平台APP查收。';
                    $phone = M('User', 'think_')->where("user_id = '{$backlog_info['user_id']}'")->getField('phone');
                    sendSms($phone, $content);
                }
            }else{
                if ($status == 1) {
                    $content = '尊敬的用户：您已成功申请升级为会员，二维码已为您下发，您将通过分享二维码发展您的客户资源！';
                    $data['open_id'] = M('User', 'think_')->where("user_id = '{$backlog_info['user_id']}'")->getField('open_id');
                    $data['first'] = $content;
                    $data['keyword1'] = '会员';
                    $data['keyword2'] = 2000;
                    $data['keyword3'] = date('Y-m-d H:i:s');
                    $data['templateId'] = 'whQu1ypMgJAuZeiIJmrt4xqp7VEI1tuOu6Jj4kyPcls';
                    $Index = new IndexController();
                    $bool = $Index->sendTemplateMsg($data);
                    if(!$bool){
                        $this->error('模板消息下发失败');
                    }
                }
            }
            M('User', 'think_')->commit();
            $this->success('操作成功');
        }

    }
}
