<?php
/*
 * 系统—用户—用户列表
 * 生成时间：2015-01-01
 * 作者：qianzhiqiang
 * 修改时间：
 * 修改备注：
 */

namespace Admin\Controller\Sys;

use Admin\Controller\BaseController;

class UsersController extends BaseController
{
    public function index()
    {

        $this->display();
    }

    public function query()
    {
        $page = I("get.page", 1);
        $pagesize = I("get.pagesize", $this->PAGE_SIZE);
        $sort = I('get.sort', 'user_id');
        if ($sort == 'user_type') {
            $sort = 'type';
        }
        $order = $sort . ' ' . I('get.order', 'desc');
        //$where['a.type'] = array('in','1,3,4');
        $username = I('get.username');
        $name = I('get.name');
        if ($username != '' || $name != '') {
            $where['username'] = array("like", "%$username%");
            $where['name'] = array("like", "%$name%");
        }
        $AdminUser = D('AdminUser');
        $result = $AdminUser->queryListEx('a.*,c.title', $where, $order, $page, $pagesize);
        foreach ($result['list'] as &$value)
        {
            if($value['source']==0||$value['source']==1){
                if($value['type']==0){
                    $value['user_type']= '会员';
                }
            }else{
                if($value['type']==0){
                    $value['user_type']= '储户';
                }
            }
        }

        $this->success($result);

    }

    public function get()
    {
        $u_id = I('get.id/d', 0);
        $AdminUser = D('AdminUser');
        $userInfo = $AdminUser->getInfo($u_id);

        if ($u_id > 0 && empty($userInfo)) {
            $this->error('用户不存在');
        }
        if ($userInfo) $userInfo['password_old'] = $userInfo['password'];
        $where['uid'] = $u_id;
        $AuthGroup = M('auth_group_access', 'think_');
        $group_id = $AuthGroup->where($where)->getField('group_id');
        $userInfo['group_id'] = $group_id ? $group_id : 0;

        $this->success($userInfo);
    }

    //数据插入
    public function edit()
    {

        $AuthGroup = M('auth_group_access', 'think_');
        $AdminUser = D('AdminUser');
        $user_id = I('post.user_id/d', 0);
        $data['user_id'] = $user_id;
        $data['username'] = I('post.username');

        $password = I('post.password');
        $oldpassword = I('post.old_password');
        if ($password != $oldpassword) $data['password'] = $password;
        $data['sex'] = I('post.sex', 1);
        $data['phone'] = I('post.phone');
        $data['email'] = I('post.email');
        $data['status'] = I('post.status');
        $data['create_time'] = date('Y-m-d H:i:s');

        //dump($data);
        $where['uid'] = $user_id;
        $data1['group_id'] = I('post.group_id');

        if (empty($data1['group_id'])) {
            $this->error('请选择角色');
        }

        if (!$AdminUser->create($data)) {
            $this->error($AdminUser->getError());
        } else {
            if ($password != $oldpassword) {
                $data['password'] = md5($password);
            }

            if ($user_id > 0) {
                $ret = $AdminUser->save($data);
                $content = "编辑用户成功,ID:" . $user_id;
                $data1['uid'] = $user_id;
            } else {
                $ret = $AdminUser->add($data);
                $content = "新建用户,ID:" . $ret;
                $data1['uid'] = $ret;
            }
            if ($ret === false) {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
                $this->error($AdminUser->getError());
            } else {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                //权限组
                $groupCount = $AuthGroup->where($where)->select();
                if ($groupCount) {
                    $ret = $AuthGroup->where($where)->save($data1);
                } else {
                    $ret = $AuthGroup->add($data1);
                }
                $this->success($content, U('/Admin/Sys/Users/index'));
            }
        }
    }

    /**
     * 禁用/启用
     */
    public function enable()
    {
        $ids = I('post.user_id');
        $type = I('post.type');
        $arrIds = explode(',', $ids);
        if (empty($arrIds)) {
            $this->error('参数错误');
        }
        $AdminUser = D('AdminUser');
        $okCount = 0;
        foreach ($arrIds as $k => $v) {
            $where['user_id'] = $v;
            $ret = $AdminUser->where($where)->save(array('status' => $type));
            if ($type == 2) S($v, null);
            if ($ret !== false) {
                $okCount++;  //处理成功记录数
            }
        }
        //写log
        $content = (($type == 0) ? "启用" : "禁用") . $okCount . "条用户信息:" . $ids;
        $state = $okCount > 0 ? 1 : 2;
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, $state);
        $this->success((($type == 0) ? "启用" : "禁用") . '成功' . $okCount . '条记录');
    }

    //删除
    public function delete()
    {
        $uIds = I('post.id');

        $arrUids = explode(',', $uIds);
        if (empty($arrUids)) {
            $this->error('参数错误');
        }
        $AdminUser = D('AdminUser');
        $okCount = 0;
        foreach ($arrUids as $k => $v) {

            $where['user_id'] = $v;

            $ret = $AdminUser->where($where)->delete();
            //echo $AdminUser->_sql();
            if ($ret == 1) {
                F('admin_user_' . $v, NULL);
                $okCount++;  //处理成功记录数

            }

        }
        //写log
        $content = "删除" . $okCount . "条用户信息:" . $uIds;
        $state = $okCount > 0 ? 1 : 2;
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, $state);
        $this->success('删除成功' . $okCount . '条记录');
    }

    //查看角色
    public function view_group()
    {
        $id = I("get.user_id");
        if (empty($id)) {
            $this->error('参数错误');
        }
        $where1['uid'] = $id;
        $groupId = M('auth_group_access', 'think_')->where($where1)->find();


        $type = I('get.type', 0);
        if ($type == 1) {
            $where = "";
        } else {
            $where['id'] = $groupId['group_id'];
        }

        $Group = D('AdminGroup')->where($where)->getField('id,title');
        $this->success($Group);
    }

    //分配角色
    public function edit_group()
    {
        $user_id = I('get.id');
        //角色输出
        $groupInfo = D('AdminGroup')->getField('id,title');
        $this->groupInfo = $groupInfo;
        //查询用户当前角色
        $where['user_id'] = array('EQ', $user_id);
        $userInfo = D('AdminUser')->where($where)->find();
        dump($userInfo);
        $this->userType = $userInfo['type'];
        $this->display();
    }

}
