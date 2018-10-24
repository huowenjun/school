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

class AdminUserController extends BaseController
{
    public function index()
    {

        $this->display();
    }

    public function query()
    {
//begin_time
        $pagesize = I("get.pagesize", $this->PAGE_SIZE);
        $page = I('get.page');
        $sort = I('get.sort', 'user_id');
        if ($sort == 'user_type') {
            $sort = 'type';
        }
        $order = $sort . ' ' . I('get.order', 'desc');
        $AdminUser = D('AdminUser');
        $username = I('get.username');
        $name = I('get.name');

        $begin_time = I('get.begin_time');
        $end_time = I('get.end_time');

        if ($username != '' || $name != '') {
            $where['username'] = array("like", "%$username%");
            $where['name'] = array("like", "%$name%");
        }
        $where['a.type'] = array('not in', '0,1,3,4');
        if (I('get.group_type') != null) {
            $where['a.type'] = I('get.group_type');
        }
        //比较时间
        if (($begin_time != null) && ($end_time != null)) {
            $where['create_time'] = array('between', array($begin_time, $end_time));
        }

        $result = $AdminUser->queryListEx('a.*,c.title', $where, $order, $page, $pagesize);
        # 10分销代理商 11区县代理商  0会员 ，select count(1) as num from think_user where ref_id=10847 and type=0

            foreach ($result['list'] as $key => $value) {
                if($value['source']==0||$value['source']==1){
                    //分销代理商(人数)
                    $distribution = $AdminUser->where(['ref_id' => $value['user_id'], 'type' => 10])->count();
                    //区县代理商(人数)
                    $county = $AdminUser->where(['ref_id' => $value['user_id'], 'type' => 11])->count();
                    //会员 （人数）
                    $member = $AdminUser->where(['ref_id' => $value['user_id'], 'type' => 0])->count();
                    $result['list'][$key]['info'] = '第三方代理商人数：<strong style="margin-right:10px;">' . $distribution . '</strong>区县代理商人数：<strong style="margin-right:10px;">' . $county . '</strong>会员人数：<strong>' . $member . '</strong>';
                } elseif ($value['source']==2){
                    //储户(人数)
                $depositor = $AdminUser->where(['ref_id' => $value['user_id'], 'type' => 0])->count();
                //会员(人数)
                $vip = $AdminUser->where(['ref_id' => $value['user_id'], 'type' => 12])->count();
                $result['list'][$key]['info'] = '储户人数：<strong style="margin-right:10px;">' .  $depositor . '</strong>会员：<strong style="margin-right:10px;">' . $vip ;
                }
            }
        $this->success($result);

    }

    public function get()
    {
        $u_id = I('get.user_id/d', 0);
        $AdminUser = D('AdminUser');
        $userInfo = $AdminUser->getInfo($u_id);

        if ($u_id > 0 && empty($userInfo)) {
            $this->error('用户不存在');
        }
        if ($userInfo) $userInfo['password_old'] = $userInfo['password'];
        $where['a.uid'] = $u_id;
        $AuthGroup = M('auth_group_access', 'think_');
        $group_id = $AuthGroup->alias("a")
            ->join("sch_region_user b on a.uid = b.u_id")
            ->where($where)
            ->field('a.group_id,b.prov_id,b.city_id,b.county_id')->find();
        $userInfo['group_id'] = $group_id['group_id'];
        $userInfo['prov_id'] = $group_id['prov_id'];
        $userInfo['city_id'] = $group_id['city_id'];
        $userInfo['county_id'] = $group_id['county_id'];

        $this->success($userInfo);
    }

    //判断用户是否存在
    public function checkUser()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $username = I('post.username');//账号
        $User = D('AdminUser');//用户表
        if (empty($username)) {
            $this->error('用户名不能为空');
        }
        $user_id = $User->where(array("username" => $username))->getField('user_id');
        if ($user_id > 0) {
            $this->error($user_id);
        } else {
            $this->success('该用户不存在');
        }
    }

    //数据插入
    public function edit()
    {
        $AuthGroup = M('auth_group_access', 'think_');
        $AdminUser = D('AdminUser');
        $user_id = I('post.user_id');
        if ($user_id > 0) {
            $data['user_id'] = $user_id;
            $userInfo = $AdminUser->getInfo($user_id);
            $oldpassword = $userInfo['password'];
        }
        if (I('post.username')) {
            $data['username'] = I('post.username');
        }
        $password = I('post.password');
        if ($password) {
            $data['password'] = $password;
            if ($password != $oldpassword) $data['password'] = md5($password);
        }

        $data['sex'] = I('post.sex', 1);
        $data['phone'] = I('post.phone');
        $data['name'] = I('post.name');
        $data['email'] = I('post.email');
        $data['status'] = I('post.status');
        $data['create_time'] = date('Y-m-d H:i:s');
        $data['group_id'] = I('post.type');
        $data['type'] = I('post.type');
        if($data['type']>11){
            $data['source'] = 2;
        }
        $data['company_name'] = I('post.company_name');
        $data['identify_number'] = I('post.identify_number');
        $data['identify_business_licence'] = I('post.identify_business_licence');
        $data['address'] = I('post.address');
        $where['uid'] = $user_id;
        $data1['group_id'] = I('post.type');
        if (empty($password)) {
            $this->error('请输入密码');
        }
        if (empty($data['name'])) {
            $this->error('请输入姓名');
        }
        if (empty($data1['group_id'])) {
            $this->error('请选择角色');
        }
        if (($data['type'] == 5) || ($data['type'] == 6) || ($data['type'] == 7) || ($data['type'] == 8)) {
            if (!I("post.prov_id") || !I("post.city_id") || !I("post.county_id")) {
                $this->error("请重新选择地区");
            }
        }

        if (!$AdminUser->create($data)) {
            $this->error($AdminUser->getError());
        } else {
            if ($user_id > 0) {
                if ($data['username'] && ($data['username'] != $userInfo['username'])) {
                    $this->error('登录名不能修改');
                }
                $ret = $AdminUser->save($data);
                $content = "编辑用户成功,ID:" . $user_id;
                $data1['uid'] = $user_id;
            } else {
                $ret = $AdminUser->add($data);
                if ($data['type'] == 9) {//第三方代理公司
                    $data['user_id'] = $ret;
                    $data['company_id'] = $ret;
                    $AdminUser->save($data);
                }

                $content = "新建用户,ID:" . $ret;
                $data1['uid'] = $ret;

            }
            //地区管理员
            //if (($data['type'] == 5) || ($data['type'] == 6) || ($data['type'] == 7) || ($data['type'] == 8)) {
            $region['u_id'] = $data1['uid'];
            $region['prov_id'] = I("post.prov_id");
            $region['city_id'] = I("post.city_id");
            $region['county_id'] = I("post.county_id");
            $region['region_code'] = $list = get_region_code(I("post.county_id"));

            //获取地区表详情
            $regionInfo = M("RegionUser")->where("u_id = '{$user_id}'")->getField('u_id');
            if ($regionInfo) {
                M("RegionUser")->where("u_id = '{$user_id}'")->save($region);
            } else {
                M("RegionUser")->add($region);
            }

            //}
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
                $this->success($content, U('/Admin/Sys/AdminUsers/index'));
            }
        }
    }

    //输出角色
    public function get_group()
    {
        $where['id'] = array('not in', '1,3,4');
        $where['status'] = 1;
        $userType = D('AdminGroup')->where($where)->getField('id,title');
        $this->success($userType);
    }

    /**
     * 禁用/启用
     */
    public function enable()
    {
        $ids = I('post.id');
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
        $content = (($type == 1) ? "启用" : "禁用") . $okCount . "条用户信息:" . $ids;
        $state = $okCount > 0 ? 1 : 2;
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, $state);
        $this->success((($type == 1) ? "启用" : "禁用") . '成功' . $okCount . '条记录');
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
            $where2['u_id'] = $v;

            $ret = $AdminUser->where($where)->delete();
            M("RegionUser")->where($where2)->delete();
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


}
