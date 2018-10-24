<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-课程管理
 */
namespace Admin\Controller\School;

use Admin\Controller\BaseController;

class CourseController extends BaseController
{

    public function index()
    {

        $this->display();
    }

    public function query()
    {

        $ids = $this->authRule();
        $where = "";
        // 学校管理权限
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

        $authId = $this->authRule();
        if ($authId['s_id'] > 0) {
            $where['s_id'] = $authId['s_id'];
        }
        $pagesize = I("get.pagesize", $this->PAGE_SIZE);
        $page = I('get.page');
        $sort = I('get.sort', 'crs_id');
        $order = $sort . ' ' . I('get.order', 'desc');
        $Course = D('Course');
        if ($key != '') {
            $where['name'] = array("like", "%$key%");
        }
        $result = $Course->queryListEX('*', $where, $order, $page, $pagesize, '');
        // var_dump($result);
        $this->success($result);
    }

    public function get()
    {

        $crs_id = I('get.crs_id/d', 0);
        $Course = D('Course');
        $area_info = $Course->getInfo($crs_id);
        if ($crs_id > 0 && empty($area_info)) {
            $this->error('不存在');
        }
        $this->success($area_info);

    }


    //新增/编辑
    public function edit()
    {

        $id = I('post.crs_id/d', 0);
        $data['crs_id'] = $id;
        $data['s_id'] = I('post.s_id');//学校id
        $data['a_id'] = I('post.a_id');//校区id
        $data['name'] = I('post.name');
        $data['user_id'] = $this->getUserId();
        $data['g_id'] = I('post.g_id');//年级id
        $data['c_id'] = I('post.c_id');//班级id
        $data['memo'] = I('post.memo');
        $Course = D('Course');
        if (!$Course->create($data)) {
            $this->error($Course->getError());
        } else {
            if ($id > 0) {
                $data['modify_time'] = date('Y-m-d H:i:s');
                $ret = $Course->save($data);
            } else {
                $data['create_time'] = date('Y-m-d H:i:s');
                $ret = $Course->add($data);
            }
            $content = ($id > 0 ? '编辑' : '新建') . '成功';
            if ($ret === false) {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
                $this->error($Course->getError());
            } else {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                $this->success($content, U('/Admin/School/Course/index'));
            }
        }
    }

    //删除
    public function del()
    {
        $uIds = I('post.id');
        $arrUids = explode(',', $uIds);
        if (empty($arrUids)) {
            $this->error('参数错误');
        }
        $Course = D('Course');
        $okCount = 0;
        foreach ($arrUids as $k => $v) {
            $where['crs_id'] = $v;
            $ret = $Course->where($where)->delete();
            if ($ret == 1) {
                $okCount++;  //处理成功记录数
            }
        }
        //写log
        $content = "删除" . $okCount . "条信息";
        $state = $okCount > 0 ? 1 : 2;
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, $state);
        $this->success('删除成功' . $okCount . '条记录');
    }

}