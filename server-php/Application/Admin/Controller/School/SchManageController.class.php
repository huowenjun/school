<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-学校管理
 */

namespace Admin\Controller\School;

use Admin\Controller\BaseController;
use Admin\Model\UserModel;

class SchManageController extends BaseController
{

    public function index()
    {
        $this->display();
    }

    //查询数据
    public function query()
    {

        $ids = $this->authRule();
        $where = "";
        if ($this->getType() == 1) {
            $where['s_id'] = $ids['s_id'];
        } elseif ($this->getType() == 3) {
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
        } elseif ($this->getType() == 4) {
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
            $where['stu_id'] = array('IN', $ids['stu_id']);
        } else {
            $arr = $_GET;
            $sthInfo = getSchoolId($arr);
            $where['s_id'] = array('IN', $sthInfo);
        }
        $key = I('get.keyword');
        $authId = $this->authRule();
        if ($authId['a_id'] > 0) {
            $where['a_id'] = $authId['a_id'];
        }
        $pagesize = I("get.pagesize", $this->PAGE_SIZE);
        $page = I('get.page', 1);
        $sort = I('get.sort', 's_id');
        $order = $sort . ' ' . I('get.order', 'desc');
        $School = D('SchoolInformation');
        if ($key != '') {
            $where['name'] = array("like", "%$key%");;
        }
        $result = $School->queryListEX('*', $where, $order, $page, $pagesize, '');
        $this->success($result);
    }

    public function get()
    {

        $s_id = I('get.s_id/d', 0);
        $School = D('SchoolInformation');
        $areaInfo = $School->getInfo($s_id);
        if ($s_id > 0 && empty($areaInfo)) {
            $this->error('学校不存在');
        }
        $this->success($areaInfo);
    }


    //新增/编辑
    public function edit()
    {
        $id = I('post.s_id');
        $u_id = I('post.u_id');
        $userInfo = D('AdminUser');
        if (I('post.user_name')) {
            $data1['username'] = I('post.user_name');
        }
        $School = D('SchoolInformation');
        if ($id > 0) {
            $resInfo = $School->where(array("s_id" => $id))->find();
            if ($data1['username'] && ($resInfo['user_name'] != $data1['username'])) {
                $this->error('登录名不能修改');
            }
            $map['region_id'] = $_POST['county_id'];
            $regionInfo = get_top_region($map['region_id']);

            $_POST['region_id'] = M("AreaRegion")->where("region_id = '{$map['region_id']}'")->getField('region_code');
            $_POST['prov_id'] = $regionInfo['prov_id'];
            $_POST['city_id'] = $regionInfo['city_id'];
            $_POST['county_id'] = $regionInfo['county_id'];
            if (!$_POST['prov_id'] || !$_POST['city_id'] || !$_POST['county_id']) {
                $this->error('地区级别请选择到县区');
            }

            $ret = $School->save($_POST);
            // $this->
        } else {
            $School->startTrans();
            $where['username'] = I('post.user_name');
            $data = [
                'username'=>I('post.user_name'),
                'name'=>I('post.name'),
                'password'=>md5(123456)
            ];
            $userM = new UserModel();
            $userInfo = $userM->lookup($where);
            if (empty($userInfo)) {
                $retUser = D('AdminUser')->add($data);
            } else {
                $this->error('用户名已存在，请重新输入！');
            }
            if (!$retUser) {
                D('AdminUser')->rollback();
                $this->error('网络异常');
            }
            //学校信息表添加
            $_POST['u_id'] = $retUser;
            $_POST['user_id'] = $this->getUserId();
            $_POST['create_time'] = date('Y-m-d H:i:s');
            $map['county_id'] = $_POST['county_id'];
            $regionInfo = get_top_region($map['county_id']);

            if (!$regionInfo['prov_id'] || !$regionInfo['city_id'] || !$regionInfo['county_id']) {
                $this->error('地区不存在！');
                D('AdminUser')->rollback();
            } else {
                $_POST['region_id'] = M("AreaRegion")->where("region_id = '{$map['county_id']}'")->getField('region_code');
                $_POST['prov_id'] = $regionInfo['prov_id'];
                $_POST['city_id'] = $regionInfo['city_id'];
                $_POST['county_id'] = $regionInfo['county_id'];
            }
            $ret = $School->add($_POST);
            $where['user_id'] = $retUser;
            $data2['s_id'] = $ret;
            D('AdminUser')->where($where)->save($data2);
            if (false === $ret) {
                $School->rollback();
            } else {
                $data1['region_code'] = rtrim($_POST['region_id'], ',');
                $data1['s_id'] = $ret;
                D('RegionCode')->add($data1);
                $School->commit();
            }

            //学校成功后更新用户表学校ID
        }
        $content = ($id > 0 ? '编辑' : '新建') . '成功';
        if ($ret === false && $retUser === false) {
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
            $this->error($School->getError());
        } else {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                $this->success($content, U('/Admin/School/SchManage/index'));
            }





    }


    //删除
    public function del()
    {
        $s_id = I('post.s_id');
        $School = D('SchoolInformation');
        $AdminUser = D('AdminUser');
        $arrUids = explode(',', $s_id);
        if (empty($arrUids)) {
            $this->error('参数错误');
        }

        $okCount = 0;
        foreach ($arrUids as $k => $v) {
            $where['s_id'] = $v;
            $SchAreainfo = D('SchoolArea')->where($where)->find();
            if (!empty($SchAreainfo)) {
                $this->error('该学校存在校区');
            }
            $ret = $School->where($where)->delete();
            if ($ret) {
                $AdminUser->where($where)->delete();
            }
            if ($ret == 1) {
                $okCount++;  //处理成功记录数
            }
        }
        //写log
        $content = "删除学校" . $okCount . "条信息";
        $state = $okCount > 0 ? 1 : 2;
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, $state);
        $this->success('删除学校成功' . $okCount . '条记录');
    }
}