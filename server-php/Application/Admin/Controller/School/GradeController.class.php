<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-年级管理
 */
namespace Admin\Controller\School;

use Admin\Controller\BaseController;

class GradeController extends BaseController
{

    public function index()
    {

        $this->display();
    }

    public function query()
    {
        $ids = $this->authRule();
        $where = "";
        // 学校管理员权限
        if ($this->getType() == 1) {
            $where['s_id'] = $ids['s_id'];
            //老师权限
        } elseif ($this->getType() == 3) {
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
            // 家长权限
        } elseif ($this->getType() == 4) {
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
            $where['stu_id'] = array('IN', $ids['stu_id']);
        } else {
            $arr = $_GET;
            $sthInfo = getSchoolId($arr);
            $where['s_id'] = array('IN', $sthInfo);
        }

        $s_id = I('get.s_id');
        $a_id = I('get.a_id');
        $g_id = I('get.g_id');
        if (!empty($s_id)) {
            $where['s_id'] = $s_id;
        }
        if (!empty($a_id)) {
            $where['a_id'] = $a_id;
        }
        if (!empty($g_id)) {
            $where['g_id'] = $g_id;
        }
        $key = I('get.keyword');

        $pagesize = I("get.pagesize", $this->PAGE_SIZE);
        $page = I('get.page');
        $sort = I('get.sort', 'g_id');
        $order = $sort . ' ' . I('get.order', 'desc');
        $Grade = D('Grade');
        if ($key != '') {
            $where['name'] = array("like", "%$key%");
        }
        if (!empty($s_id)) {
            $where['s_id'] = $s_id;
        }
        $result = $Grade->queryListEX('*', $where, $order, $page, $pagesize, '');
        $this->success($result);
    }

    // 编辑
    public function get()
    {

        $g_id = I('get.g_id/d', 0);
        $Grade = D('Grade');
        $area_info = $Grade->getInfo($g_id);
        if ($g_id > 0 && empty($area_info)) {
            $this->error('不存在');
        }
        $this->success($area_info);
    }


    //新增/编辑
    public function edit()
    {


        $id = I('post.g_id/d', 0);
        $data['g_id'] = $id;
        $data['s_id'] = I('post.s_id');//学校id
        $data['a_id'] = I('post.a_id');//校区id
        $data['name'] = I('post.name');
        $data['memo'] = I('post.memo');
        $data['user_id'] = $this->getUserId();
        $Grade = D('Grade');
        if (!$Grade->create($data)) {
            $this->error($Grade->getError());
        } else {
            if ($id > 0) {
                $data['modify_time'] = date('Y-m-d H:i:s');
                $ret = $Grade->save($data);
            } else {
                $data['create_time'] = date('Y-m-d H:i:s');
                $ret = $Grade->add($data);
            }
            $content = ($id > 0 ? '编辑' : '新建') . '成功';
            if ($ret === false) {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
                $this->error($Grade->getError());
            } else {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                $this->success($content, U('/Admin/School/Grade/index'));
            }
        }
    }

    //删除
    public function del()
    {
        $uIds = I('post.id');
        $Class = D('Class');

        $arrUids = explode(',', $uIds);
        if (empty($arrUids)) {
            $this->error('参数错误');
        }
        $Grade = D('Grade');
        $okCount = 0;
        foreach ($arrUids as $k => $v) {

            $where['g_id'] = $v;
            $result = $Class->where($where)->find();
            if (empty($result)) {
                $ret = $Grade->where($where)->delete();
            } else {
                $this->error('有下一级，不能删除');
            }
            if ($ret == 1) {
                $okCount++;  //处理成功记录数
            }
        }
        $content = "删除" . $okCount . "条信息";
        $state = $okCount > 0 ? 1 : 2;
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, $state);
        $this->success('删除成功' . $okCount . '条记录');
    }


}