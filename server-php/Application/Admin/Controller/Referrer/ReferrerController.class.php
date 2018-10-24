<?php

namespace Admin\Controller\Referrer;

use Admin\Controller\BaseController;

class ReferrerController extends BaseController
{
    public function index()
    {
        $this->display();
    }

    //查询接口
    public function query()
    {
        $user_id = $this->getUserId();
        if (!$user_id) {
            $this->error('请登录后重试');
        }
        $username = I('get.username');
        $type = I('get.user_type');
        $map['a.ref_id'] = $user_id;
        $map['a.company_id'] = $user_id;
        $map['_logic'] = 'or';
        $where['_complex'] = $map;

        if ($username) {
            $where['a.username'] = $username;
        }
        $where['a.type'] = array('neq', 9);
        if ($type != NULL) {
            $where['a.type'] = $type;
        }

        $page = I('get.page', 1);
        $pagesize = I('get.pagesize', 20);
        $list = M('User', 'think_')
            ->alias('a')
            ->field('a.user_id,a.name,a.username,a.create_time,a.company_id,a.type,b.status as agent_status,b.memo,b.reply')
            ->join('left join sch_backlog b on a.user_id = b.user_id')
            ->where($where)
            ->group('a.user_id')
            ->page($page, $pagesize)
            ->select();
        $count = M('User', 'think_')
            ->alias('a')
            ->join('left join sch_backlog b on a.user_id = b.user_id')
            ->where($where)
            ->count();

        $agent_status = C('REFUND_STATUS');
        $user_role = C('USER_ROLE');
        foreach ($list as $key => $value) {
            $list[$key]['status'] = '已注册';
            $list[$key]['user_type'] = $user_role[$value['type']];
            if ($value['agent_status'] !== NULL) {
                $list[$key]['agent_status'] = $agent_status[$value['agent_status']];
            } else {
                $list[$key]['agent_status'] = '未申请';
            }
            //分销代理商(人数)
            $distribution = M('User', 'think_')->where(['ref_id' => $value['user_id'], 'type' => 10])->count();
            //区县代理商(人数)
            $county = M('User', 'think_')->where(['ref_id' => $value['user_id'], 'type' => 11])->count();
            //会员 （人数）
            $member = M('User', 'think_')->where(['ref_id' => $value['user_id'], 'type' => 0])->count();
            $list[$key]['info'] = '第三方代理商人数：<strong style="margin-right:10px;">' . $distribution . '</strong>区县代理商人数：<strong style="margin-right:10px;">' . $county . '</strong>会员人数：<strong>' . $member . '</strong>';
        }
        $data = getPageList($list, $count, $page, $pagesize, 1);
        $this->success($data);
    }

    //获取用户详情
    public function getUser()
    {
        $ref_id = $this->getUserId();
        if (!$ref_id) {
            $this->error('请登录后重试');
        }
        $user_id = I('get.user_id');
        if (!$user_id) {
            $this->error('参数错误');
        }

        $user_info = M('User', 'think_')
            ->field('user_id,name,username,company_id')
            ->where("user_id = '{$user_id}' and ref_id = '{$ref_id}'")
            ->find();

        if (!$user_info) {
            $this->error('未获取用户信息');
        }

        $this->success($user_info);
    }

    //提交代理商审核
    public function apply()
    {
        $user_id = I('post.user_id');
        $company_id = I('post.company_id');
        $memo = I('post.memo');
        $name = trim(I('post.name'));
        if (!$user_id) {
            $this->error('参数错误');
        }
        if (!$name) {
            $this->error('姓名不能为空');
        }
        $user_type = M('User', 'think_')->where("user_id = '{$user_id}'")->getField('type');
        if ($user_type == 10) {
            $this->error('该用户已经是第三方代理商角色,无需重复申请');
        }
        //检测代理商今天是否已经提交申请
        $backlog_info = M('Backlog')
            ->where("user_id = '{$user_id}'")
            ->find();
        if (!empty($backlog_info)) {
            if ($backlog_info['status'] == 0) {
                $this->error('您的申请已经提交,请耐心等待');
            } elseif ($backlog_info['status'] == 1) {
                $this->error('您的申请已经被审核通过');
            }
        }


        $data['user_id'] = $user_id;
        $data['company_id'] = $company_id;
        $data['memo'] = $memo;
        $data['name'] = $name;
        M('User', 'think_')->save($data);
        $data['create_time'] = date('Y-m-d H:i:s');
        $res = M('Backlog')->add($data);
        if (!$res) {
            $this->error('申请操作失败,请稍后再试');
        }
        $this->success('申请成功');
    }

    //获取我的邀请人
    public function getReferrer()
    {
        $user_id = I('get.user_id');
        if (!$user_id) {
            $this->error('参数错误');
        }
        $page = I('get.page', 1);
        $pagesize = I('get.pagesize', 10);
        $begin_time = I('get.begin_time');
        $end_time = I('get.end_time');

        $where['ref_id'] = $user_id;
        //比较时间
        if (($begin_time != null) && ($end_time != null)) {
            $where['a.create_time'] = array('between', array($begin_time, $end_time));
        }

        $list = M('User', 'think_')
            ->alias('a')
            ->field('a.user_id,a.name,a.username,a.create_time,a.company_id,a.type,b.status as agent_status,b.memo,b.reply,a.source')
            ->join('left join sch_backlog b on a.user_id = b.user_id')
            ->where($where)
            ->group('a.user_id')
            ->page($page, $pagesize)
            ->select();
        $count = M('User', 'think_')
            ->alias('a')
            ->join('left join sch_backlog b on a.user_id = b.user_id')
            ->where($where)
            ->count();
        $user_role = C('USER_ROLE');
        $agent_status = C('REFUND_STATUS');
        foreach ($list as $key => $value) {
            $list[$key]['user_type'] = $user_role[$value['type']];
            if ($value['agent_status'] !== NULL) {
                $list[$key]['agent_status'] = $agent_status[$value['agent_status']];
            } else {
                $list[$key]['agent_status'] = '未申请';
            }
            if($value['source']==0||$value['source']==1){
                if($value['type']==0){
                    $list[$key]['user_type']= '会员';
                }
            }else{
                if($value['type']==0){
                    $list[$key]['user_type']= '储户';
                }
            }
            if($value['source']==0||$value['source']==1){
                //分销代理商(人数)
                $distribution = M('User', 'think_')->where(['ref_id' => $value['user_id'], 'type' => 10])->count();
                //区县代理商(人数)
                $county = M('User', 'think_')->where(['ref_id' => $value['user_id'], 'type' => 11])->count();
                //会员 （人数）
                $member = M('User', 'think_')->where(['ref_id' => $value['user_id'], 'type' => 0])->count();
                $list[$key]['info'] = '第三方代理商人数：<strong style="margin-right:10px;">' . $distribution . '</strong>区县代理商人数：<strong style="margin-right:10px;">' . $county . '</strong>会员人数：<strong>' . $member . '</strong>';
            }elseif ($value['source']==2){
                //储户(人数)
                $depositor = M('User', 'think_')->where(['ref_id' => $value['user_id'], 'type' => 0])->count();
                //会员(人数)
                $vip = M('User', 'think_')->where(['ref_id' => $value['user_id'], 'type' => 12])->count();
                $list[$key]['info'] = '储户人数：<strong style="margin-right:10px;">' .  $depositor . '</strong>会员：<strong style="margin-right:10px;">' . $vip ;
            }

        }
        $data = getPageList($list, $count, $page, $pagesize, 1);
        $this->success($data);
    }

    //用户列表页
    public function showUserList()
    {
        $this->display();
    }
}
