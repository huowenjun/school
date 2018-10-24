<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-部门管理
 */
namespace Admin\Controller\School;

use Admin\Controller\BaseController;

class DeptController extends BaseController
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
            // 教师管理员权限
        } elseif ($this->getType() == 3) {
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
            // 家长管理员权限
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
        $d_id = I('get.dept_id');
        $pagesize = I("get.pagesize", $this->PAGE_SIZE);
        $page = I('get.page');
        $sort = I('get.sort', 'd_id');
        $order = $sort . ' ' . I('get.order', 'desc');
        $Dept = D('Dept');
        if (!empty($s_id)) {
            $where['s_id'] = $s_id;
        }
        if (!empty($a_id)) {
            $where['a_id'] = $a_id;
        }
        if (!empty($g_id)) {
            $where['g_id'] = $g_id;
        }
        if ($d_id != '') {
            $where['name'] = array("like", "%$d_id%");
        }
        if ($a_id > 0) {
            $where['a_id'] = $a_id;
        }
        $result = $Dept->queryListEX('*', $where, $order, $page, $pagesize, '');
        $this->success($result);
    }

    public function get()
    {

        $d_id = I('get.d_id/d', 0);

        $Dept = D('Dept');
        $area_info = $Dept->getInfo($d_id);
        if ($d_id > 0 && empty($area_info)) {
            $this->error('不存在');
        }
        $this->success($area_info);

    }


    //新增/编辑
    public function edit()
    {
        $id = I('post.d_id/d', 0);
        $data['d_id'] = $id;
        $data['name'] = I('post.name');//名字
        $data['s_id'] = I('post.s_id');//学校id
        $data['a_id'] = I('post.a_id');//校区id
        $data['memo'] = I('post.memo');//备注
        $data['user_id'] = $this->getUserId();
        $Dept = D('Dept');
        if (!$Dept->create($data)) {
            $this->error($Dept->getError());
        } else {
            if ($id > 0) {
                $data['modify_time'] = date('Y-m-d H:i:s');
                $ret = $Dept->save($data);
            } else {
                $data['create_time'] = date('Y-m-d H:i:s');
                $ret = $Dept->add($data);
            }
            //  dump(self::getLastSql());
            $content = ($id > 0 ? '编辑' : '新建') . '成功';
            if ($ret === false) {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
                $this->error($Dept->getError());
            } else {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                $this->success($content, U('/Admin/School/Dept/index'));
            }
        }
    }

    //删除
    public function del()
    {
        $uIds = I('post.d_id');
        $arrUids = explode(',', $uIds);
        if (empty($arrUids)) {
            $this->error('参数错误');
        }
        $Dept = D('Dept');
        $okCount = 0;
        foreach ($arrUids as $k => $v) {
            $where['d_id'] = $v;
            $ret = $Dept->where($where)->delete();
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
