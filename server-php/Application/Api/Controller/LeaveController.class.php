<?php
/*
*请假记录查询
*by ruiping date 20161201
*/

namespace Api\Controller;

use Api\Controller\BaseController;
use Think\Model;

class LeaveController extends BaseController
{
    /*
    *数据查询
    *@param
    */
    public function query()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
//        $token = md5('/Api/Leave/query'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法！');
        }
        $type = $this->getUserType();
        $pagesize = $this->PAGESIZE;
        $p = I('post.page', 1);
        $dates = I('post.dates');


        $where1['u_id'] = $this->getUserId();
        if (empty($dates)) {
            $this->error('请选择日期！');
        }

        if ($type == 4) {//家长
            $stu_id = I('post.stu_id');//家长查询时

            if (empty($stu_id)) {
                $this->error('未选择学生！');
            } else {
                $where['stu_id'] = $stu_id;
            }
            $where['start_time'] = array('like', '%' . $dates . '%');
        } elseif ($type == 3) {//老师
            $g_id = I('post.g_id');
            $c_id = I('post.c_id');
            if (empty($g_id)) {
                $this->error('请选择年级！');
            }
            if (empty($c_id)) {
                $this->error('请选择班级！');
            }
//            $where['start_time'] = array('BETWEEN',array($dates.' 00:00:00',$dates.' 23:59:59'));
            $where['start_time'] = array('like', '%' . $dates . '%');
            $where['g_id'] = $g_id;
            $where['c_id'] = $c_id;
            $teacherInfo = D('Teacher')->where($where1)->find();
            $where['t_id'] = $teacherInfo['t_id'];
        } else {
            $this->error('用户类型不正确！');
        }
        $Leave = D('Leave');
        $orderby = 'start_time desc';
        $leaveList = $Leave->queryList('id,g_id,c_id,stu_id,leave_type,leave_memo,start_time,end_time,status,t_id,reply', $where, $orderby, $p, $pagesize);
        $leaveCount = $leaveList['count'];
        $arr = array();
        $page_szie = ceil($leaveCount / $pagesize);
        $LEAVE_TYPE = C('LEAVE_TYPE');
        $LEAVE_STATUS = C('LEAVE_STATUS_NAME');
        foreach ($leaveList['list'] as $key => $value) {
            $leaveList['list'][$key]['stu_name'] = $this->get_stu_name($value['stu_id'])[$value['stu_id']]['stu_name'];
            $leaveList['list'][$key]['t_name'] = $this->get_teacher_name($value['t_id'], 1)[$value['t_id']];
            $leaveList['list'][$key]['g_name'] = $this->get_grade_name($value['g_id'])[$value['g_id']];
            $leaveList['list'][$key]['c_name'] = $this->get_class_name($value['c_id'])[$value['c_id']];
            $leaveList['list'][$key]['start_time'] = date_format(date_create($value['start_time']), 'Y-m-d H:i');
            $leaveList['list'][$key]['end_time'] = date_format(date_create($value['end_time']), 'Y-m-d H:i');
            $leaveList['list'][$key]['leave_name'] = $LEAVE_TYPE[$value['leave_type']];
            $leaveList['list'][$key]['status_name'] = $LEAVE_STATUS[$value['status']];
        }

        $arr['total_page'] = $page_szie;
        $arr['total_count'] = $leaveCount;
        $arr['page'] = $p;
        $arr['content'] = $leaveList['list'];
        $this->success($arr);
    }

    //获取数据详情
    public function getDetail()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法');
        }
        $id = I('post.id');
        $LEAVE_TYPE = C('LEAVE_TYPE');
        $LEAVE_STATUS = C('LEAVE_STATUS_NAME');
        if ($id <= 0) {
            $this->error('参数错误');
        }
        $info = M('Leave')
            ->field('id,g_id,c_id,stu_id,leave_type,leave_memo,start_time,end_time,status,reply,t_id,create_time')
            ->where("id = '{$id}'")
            ->find();
        if (empty($info)) {
            $this->error('参数错误');
        }
        $class_info = getClassInfo($info['c_id']);
        $stu_name = M('Student')->where("stu_id = '{$info['stu_id']}'")->getField('stu_name');
        $info['stu_name'] = $stu_name ? $stu_name : '已删除';
        $info['t_name'] = M('Teacher')->where("t_id = '{$info['t_id']}'")->getField('name');
        $info['leave_name'] = $LEAVE_TYPE[$info['leave_type']];
        $info['status_name'] = $LEAVE_STATUS[$info['status']];
        $info['g_name'] = $class_info['g_name'];
        $info['c_name'] = $class_info['c_name'];

        $this->success($info);
    }

    //学生请假详情
    public function view()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/Leave/view'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法！');
        }
        $id = I('post.id');
        if (empty($id)) {
            $this->error('参数错误！');
        }
        $Leave = D('Leave');
        $where['id'] = array('EQ', $id);
        $LEAVE_TYPE = C('LEAVE_TYPE');

        $LEAVE_STATUS = C('LEAVE_STATUS');
        $leaveList = $Leave->Field('g_id,c_id,stu_id,leave_type,leave_memo,reply,start_time,end_time,status,t_id')->where($where)->find();
        $leaveList['stu_name'] = $this->get_stu_name($leaveList['stu_id'])[$leaveList['stu_id']]['stu_name'];
        $leaveList['t_name'] = $this->get_teacher_name($leaveList['t_id'])[$leaveList['t_id']];
        $leaveList['g_name'] = $this->get_grade_name($leaveList['g_id'])[$leaveList['g_id']];
        $leaveList['c_name'] = $this->get_class_name($leaveList['c_id'])[$leaveList['c_id']];
        $leaveList['leave_name'] = $LEAVE_TYPE[$leaveList['leave_type']];
        $leaveList['status_name'] = $LEAVE_STATUS[$leaveList['status']];

        $this->success($leaveList);
    }

    //请假批改
    public function edit()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/Leave/edit'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法！');
        }
        $status = I('post.status');
        $reply = I('post.reply');
        $id = I('post.id');
        if (empty($status) || empty($id)) {
            $this->error('参数错误！');
        }
        $where['id'] = $id;
        $data['id'] = $id;
        $data['status'] = $status;
        $data['reply'] = $reply;
        $Leave = D('Leave');
        $ret = $Leave->where($where)->save($data);
        $typeContent = ($status == 1) ? "批准" : "驳回";
        if ($ret == 0) {
            $content = "请假批改失败！";
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getUserType(), $this->getUserType(), 1, 2);
            $this->error($Mess->getError());
        } else {
            $content = "请假批改成功！";
            //app消息推送 By:Meng Fanmin 2017.06.28
            $stu_id = M("Leave")->where("id = {$id}")->getField("stu_id");
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
            $this->sendAppMsg($pushArr, 1);
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getUserType(), $this->getUserType(), 1, 1);
            $this->success('操作成功！');
        }
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

    //新建假条
    public function add_leave()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/Leave/add_leave'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法！');
        }
        $id = I('post.id/d', 0);
        $data['id'] = $id;
        $data['stu_id'] = I('post.stu_id');
        $data['t_id'] = I('post.t_id');
        $res = D('Student')->where(array('stu_id' => $data['stu_id']))->find();
        $data['c_id'] = $res['c_id'];
        $data['g_id'] = $res['g_id'];
        $data['s_id'] = $res['s_id'];
        $data['a_id'] = $res['a_id'];
        $data['leave_memo'] = I('post.leave_memo');
        $data['leave_type'] = I('post.leave_type');
        $data['start_time'] = I('post.start_time');//开始时间
        $data['end_time'] = I('post.end_time');//结束时间
        if (strtotime($data['start_time']) > strtotime($data['end_time'])) {
            $this->error('开始时间不能大于结束时间');
        }
        $data['create_time'] = date('Y-m-d H:i:s');
        $Leave = D('Leave');

        if (!$Leave->create($data)) {
            $this->error($Leave->getError());
        } else {
            if ($id > 0) {
                $ret = $Leave->save($data);
            } else {
                $ret = $Leave->add($data);
                $id = $ret;
            }
            $content = ($id > 0 ? '编辑请假' : '请假提交') . '成功';
            if ($ret === false) {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getUserType(), $this->getUserType(), 1, 2);
                $this->error($Leave->getError());
            } else {
                //app消息推送 By:Meng Fanmin 2017.06.28
                $tUserId = M("Teacher")->where("t_id = {$data['t_id']}")->getField('u_id');
                $pushArr = array(
                    'fsdx' => '1,0',
                    'u_id' => $tUserId,
                    'ticker' => '请假审批',
                    'title' => '请假审批',
                    'text' => $res['stu_name'] . '请假审批申请',
                    'after_open' => 'go_custom',
                    'activity' => 'leave',
                    'id' => $id,
                );
                $this->sendAppMsg($pushArr, 1);

                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getUserType(), $this->getUserType(), 1, 1);
                $this->success($content);
            }
        }

    }

    //删除
    public function del()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/Leave/del'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法！');
        }
        $id = I('post.id');
        if (empty($id)) {
            $this->error('参数错误！');
        }
        $Leave = D('Leave');

        $where['id'] = $id;
        $ret = $Leave->where($where)->delete();
        if (!$ret) {
            $content = "删除失败";
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getUserType(), $this->getUserType(), 1, 2);
            $this->error($content);
        } else {
            $content = "删除成功";
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getUserType(), $this->getUserType(), 1, 1);
            $this->success($content);
        }
    }

}