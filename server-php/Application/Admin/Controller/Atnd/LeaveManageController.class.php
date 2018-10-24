<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-请假管理
 */

namespace Admin\Controller\Atnd;

use Admin\Controller\BaseController;

class LeaveManageController extends BaseController
{
    /**
     *请假管理
     */
    public function index()
    {

        $this->display();
    }

    public function query()
    {
        $ids = $this->authRule();
        $where = "";
        // 学校管理员
        if ($this->getType() == 1) {
            $where['s_id'] = $ids['s_id'];
            // 教师用户
        } elseif ($this->getType() == 3) {
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
            $where1['t_id'] = $ids['t_id'];
            $cidInfo = D('Class')->where($where1)->getField('c_id', true);

            $c_id = "";
            foreach ($cidInfo as $key => $value) {
                $c_id = $c_id . $value . ",";
            }
            $where['c_id'] = array('IN', rtrim($c_id, ','));
            // 家长用户
        } elseif ($this->getType() == 4) {
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
            $stuidlist = $this->getStudentClass($ids['stu_id']);
            $where['g_id'] = $stuidlist['g_id'];
            $where['c_id'] = $stuidlist['c_id'];
        } else {
            $arr = $_GET;
            $sthInfo = getSchoolId($arr);
            $where['s_id'] = array('IN', $sthInfo);
        }


        $s_id = I('get.s_id');//学校id
        $a_id = I('get.a_id');//校区id
        $g_id = I('get.g_id');//年级id
        $c_id = I('get.c_id');//班级id
        $stu_id = I("get.stu_id");
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
        if (!empty($stu_id)) {
            $where['stu_id'] = $stu_id;
        }
        $leave_type = I("get.leave_type"); //'请假类型 1 事假 2 病假 0 其它',

        $starttime = I("get.sdatetime");//开始时间
        $endtime = I("get.edatetime");//结束时间
        $page = I("get.page", 1);
        $pagesize = I("get.pagesize", $this->PAGE_SIZE);
        $sort = I('get.sort', 'id');
        $order = $sort . ' ' . I('get.order', 'desc');
        // $where['a.type'] = array('NEQ',2);
        // if (!empty(I('get.username')) || I('get.name')) {
        //     $where['username'] = array('like','%'.I('get.username').'%');
        //     $where['name'] = array('like','%'.I('get.name').'%');
        // }
        if ($leave_type != '') {
            $where['leave_type'] = $leave_type;
        }
        if ($starttime != '' && $endtime != '') {
            $where['start_time'] = array("BETWEEN", array($starttime, $endtime));
        } elseif ($starttime != '') {
            $where['start_time'] = array("EGT", $starttime);
        } elseif ($endtime != '') {
            $where['start_time'] = array("ELT", $endtime);
        }
        $Leave = D('Leave');
        $result = $Leave->queryListEX('*', $where, $order, $page, $pagesize, '');
        $this->success($result);
    }

    /**
     * 批准/驳回
     */
    public function enable()
    {
        $ids = I('post.id');
        // var_dump($ids);
        $type = I('post.status'); //'状态 1已批准 2 已驳回 0 已提交',
        // var_dump($type);
        $arrIds = explode(',', $ids);
        if (empty($arrIds)) {
            $this->error('参数错误');
        }
        $Leave = D('Leave');
        $okCount = 0;
        $typeContent = ($type == 1) ? "批准" : "驳回";
        foreach ($arrIds as $k => $v) {
            $where['id'] = $v;
            $ret = $Leave->where($where)->save(array('status' => $type));
            $id = $v;
            if ($type == 2) S($v, null);

            if ($ret !== false) {
                $okCount++;  //处理成功记录数
            }

            //app消息推送 By:Meng Fanmin 2017.06.28
            if ($_SERVER['SERVER_ADDR'] == C('SERVERADDR')) {
                $stu_id = M("Leave")->where("id = {$v}")->getField("stu_id");
                $tUserId = $this->getParUserId($stu_id);
                $pushArr = array(
                    'fsdx' => '0,1',
                    'u_id' => $tUserId,
                    'ticker' => '请假审批通知',
                    'title' => '请假审批通知',
                    'text' => $typeContent . '请假申请',
                    'after_open' => 'go_custom',
                    'activity' => 'leave',
                    'id' => $id,
                );
                sendAppMsg($pushArr);

            }
        }
        //写log
        $content = (($type == 1) ? "批准" : "驳回") . $okCount . "条用户信息:" . $ids;
        $state = $okCount > 0 ? 1 : 2;
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
        $this->success((($type == 1) ? "批准" : "驳回") . '成功' . $okCount . '条记录');
    }

    /**
     * 获取监护人user_id
     * */
    private function getParUserId($stu_id)
    {
        $p_user = M("StudentGroupAccess")->field('b.u_id')
            ->alias("a")
            ->join("__STUDENT_PARENT__ b on a.sp_id = b.sp_id")
            ->where('a.stu_id = ' . $stu_id)->select();
        $pUidArr = array();
        foreach ($p_user as $key => $value) {
            $pUidArr[] = $value['u_id'];
        }
        return $pUidArr;
    }

    public function get()
    {
        $id = I('get.id/d', 0);
        $Leave = D('Leave');
        $area_info = $Leave->getInfo($id);
        if ($id > 0 && empty($area_info)) {
            $this->error('不存在');
        }
        $this->success($area_info);
    }


    //新增/编辑
    public function edit()
    {

        $id = I('post.id/d', 0);
        $data['id'] = $id;
        $ids = $this->authRule();
        $data['s_id'] = $ids['s_id'];
        $data['a_id'] = $ids['a_id'];
        $data['stu_id'] = I('post.stu_id');

        $data['t_id'] = I('post.t_id');
        $res = D('Student')->where(array('stu_id' => $data['stu_id']))->find();
        $data['c_id'] = $res['c_id'];
        $data['g_id'] = $res['g_id'];
        $data['leave_memo'] = I('post.leave_memo');
        $data['leave_type'] = I('post.leave_type');
        $data['start_time'] = I('post.start_time');//开始时间
        $data['end_time'] = I('post.end_time');//结束时间

        // if (strtotime($data['start_time']) > strtotime($data['end_time'])) {

        //      $this->error('开始时间不能大于结束时间');
        // }
        $data['create_time'] = date('Y-m-d H:i:s');
        $Leave = D('Leave');
        if (!$Leave->create($data)) {
            $this->error($Leave->getError());
        } else {
            if (strtotime($data['start_time']) > strtotime($data['end_time'])) {

                $this->error('开始时间不能大于结束时间');
            }
            if ($id > 0) {
                $ret = $Leave->save($data);
            } else {
                $ret = $Leave->add($data);
                $id = $ret;
            }
            //  dump(self::getLastSql());
            $content = ($id > 0 ? '编辑' : '新建') . '成功';
            if ($ret === false) {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
                $this->error($Leave->getError());
            } else {
                //app消息推送 By:Meng Fanmin 2017.06.28
                $tUserId = M("Teacher")->where("t_id = {$data['t_id']}")->getField('u_id');
                $stuName = M("Student")->where("stu_id = {$data['stu_id']}")->getField('stu_name');
                if ($_SERVER['SERVER_ADDR'] == C('SERVERADDR')) {
                    $pushArr = array(
                        'fsdx' => '1,0',
                        'u_id' => $tUserId,
                        'ticker' => '请假审批',
                        'title' => '请假审批',
                        'text' => $stuName . '请假审批申请',
                        'after_open' => 'go_custom',
                        'activity' => 'leave',
                        'id' => $id,
                    );
                    sendAppMsg($pushArr);

                }
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                $this->success($content, U('/Admin/Atnd/LeaveManage/index'));
            }
        }
    }


    //删除
    public function del()
    {
        $type = $this->getType();
        $id = I('post.id');
        $arrUids = explode(',', $id);
        if (empty($arrUids)) {
            $this->error('参数错误');
        }
        $Leave = D('Leave');
        $okCount = 0;
        foreach ($arrUids as $k => $v) {
            $where['id'] = $v;
            if ($type == 4) {
                $LeaveInfo = $Leave->where($where)->find();
                if ($LeaveInfo['status'] == 1) {
                    $this->error('该信息已处理,不能删除');
                }
            }
            $ret = $Leave->where($where)->delete();
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

    public function get_list()
    {
        $type = I("get.type");
        $stu_id = I("get.stu_id");
        // var_dump($stu_id);
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

        }
        if (!empty($stu_id)) {

            $where1['stu_id'] = $stu_id;
        }
        $Studentinfo = D('Student')->where($where1)->find();
        $res1 = D('Class')->where(array("c_id" => $Studentinfo['c_id']))->find();
        switch ($type) {
            case 'student': //学生
                $res = D('Student')->where($where)->getField('stu_id as id , stu_name as value');
                break;
            case 'teacher': //老师
                $res = D('Teacher')->where(array('t_id' => $res1['t_id']))->getField('t_id as id , name as value');
                break;
        }
        $this->success($res);
    }

}