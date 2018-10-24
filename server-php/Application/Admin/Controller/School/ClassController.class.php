<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-班级管理
 */
namespace Admin\Controller\School;

use Admin\Controller\BaseController;

class ClassController extends BaseController
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
            $classList = $this->getTeacherClass($ids['t_id']);
            $where['g_id'] = array('IN', $classList['g_id']);//$ids['a_id'];
            $where['c_id'] = array('IN', $classList['c_id']);
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

        $s_id = I('get.s_id');//学校ID
        $a_id = I('get.a_id');//校区ID
        $g_id = I('get.g_id');//年级ID
        $c_id = I('get.c_id');//班级ID
        $pagesize = I("get.pagesize", $this->PAGE_SIZE);
        $page = I('get.page');
        $sort = I('get.sort', 'c_id');
        $order = $sort . ' ' . I('get.order', 'desc');
        $Class = D('Class');
        if (!empty($s_id)) {
            $where['s_id'] = $s_id;
        }
        if (!empty($a_id)) {
            $where['a_id'] = $a_id;
        }
        if (!empty($g_id)) {
            $where['g_id'] = $g_id;
        }
        if (!empty($c_id)) {
            $where['c_id'] = $c_id;
        }
        if ($key != '') {
            $where['name'] = array("like", "%$key%");
        }
        $result = $Class->queryListEX('*', $where, $order, $page, $pagesize, '');
        $this->success($result);
    }

    public function get()
    {

        $c_id = I('get.c_id/d', 0);
        $Class = D('Class');
        $area_info = $Class->getInfo($c_id);
        if ($c_id > 0 && empty($area_info)) {
            $this->error('不存在');
        }
        $this->success($area_info);
    }


    //新增/编辑
    public function edit()
    {
        $id = I('post.c_id/d', 0);
        $g_id = I('post.g_id/d', 0);
        $data['c_id'] = $id;

        $data['s_id'] = I('post.s_id');
        $data['a_id'] = I('post.a_id');
        $data['name'] = I('post.name');
        $data['g_id'] = $g_id;
        $data['t_id'] = I('post.t_id'); //班主任
        $data['memo'] = I('post.memo');//描述
        $data['create_time'] = date('Y-m-d H:i:s');
        $Class = D('Class');
        if (!$Class->create($data)) {
            $this->error($Class->getError());
        } else {
            if ($id > 0) {
                $ret = $Class->save($data);
            } else {
                $ret = $Class->add($data);
            }
            $content = ($id > 0 ? '编辑' : '新建') . '成功';
            if ($ret === false) {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
                $this->error($Class->getError());
            } else {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                $this->success($content, U('/Admin/School/Class/index'));
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
        $Class = D('Class');
        $okCount = 0;
        foreach ($arrUids as $k => $v) {

            $where['c_id'] = $v;
            $studentInfo = D('Student')->where($where)->find();
            if (!empty($studentInfo)) {
                $this->error('班级中存在学生不能删除');
            }
            $ret = $Class->where($where)->delete();

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

    //班主任列表
    public function get_list()
    {
        $a_id = I('get.a_id');
        if (!empty($a_id)) {
            $where['a_id|name'] = array('like', $a_id);
        }
        $where1['a_id'] = D('SchoolArea')->where($where)->getField('a_id', false);
        $arrRet = D('Teacher')->where($where1)->getField('t_id as id , name as value');
        $this->success($arrRet);
    }
}