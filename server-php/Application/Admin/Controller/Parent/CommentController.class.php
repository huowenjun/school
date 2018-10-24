<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-评语管理
 */
namespace Admin\Controller\Parent;

use Admin\Controller\BaseController;

class CommentController extends BaseController
{
    /**
     *评语管理
     */
    public function index()
    {

        $this->display();
    }
    public function query()
    {
        $ids = $this->authRule();

        $map = "";
        if ($this->getType() == 1) {//学校管理
            $map['s_id'] = $ids['s_id'];
        } elseif ($this->getType() == 3) {//教师
            $map['s_id'] = $ids['s_id'];
            $map['a_id'] = $ids['a_id'];
            $classList = $this->getTeacherClass($ids['t_id']);
            $map['g_id'] = array('IN', $classList['g_id']);
            $map['c_id'] = array('IN', $classList['c_id']);
        } elseif ($this->getType() == 4) {//家长
            $map['s_id'] = $ids['s_id'];
            $map['a_id'] = $ids['a_id'];
            $map['stu_id'] = array('IN', $ids['stu_id']);
        } else {//系统管理 省市县管理
            $arr = $_GET;
            $sthInfo = getSchoolId($arr);
            $map['s_id'] = array('IN', $sthInfo);
        }

        $issUser = I('get.user_id');// 老师
        $remarktype = I('get.r_type');// 评语类型
        $sId = I('get.s_id');
        $aId = I('get.a_id');
        $gId = I('get.g_id');
        $cId = I('get.c_id');
        $stuId = I('get.stu_id');
        $page = I('get.page');
        $pagesize = I('get.pagesize', $this->PAGE_SIZE);
        $sort = I('get.sort', 'r_id');
        $order = $sort . ' ' . I('get.order', 'desc');


        $Remark = D('Remark');
        if ($issUser != '') {
            $where = "name like '%" . $issUser . "%'";
            $userInfo = D('AdminUser')->where($where)->select();
            $user_id = "";
            if (empty($userInfo)) {
                $this->error('输入的教师姓名不存在！');
            } else {
                foreach ($userInfo as $key => $value) {
                    $user_id = $user_id . $value['user_id'] . ",";
                }
            }
            $map['user_id'] = array('IN', rtrim($user_id, ','));
        }
        if ($remarktype) $map['r_type'] = $remarktype;
        if (!empty($sId)) {
            $map['s_id'] = $sId;
        }
        if (!empty($aId)) {
            $map['a_id'] = $aId;
        }
        if (!empty($gId)) {
            $map['g_id'] = $gId;
        }
        if (!empty($cId)) {
            $map['c_id'] = $cId;
        }
        if (!empty($stuId)) {
            $map['stu_id'] = $stuId;
        }
        $result = $Remark->queryListEx('*', $map, $order, $page, $pagesize, '');
        $this->success($result);
    }
    public function get()
    {
        $r_id = I('get.r_id/d', 0);
        $Remark = D('Remark');
        $Remark_info = $Remark->getInfo($r_id);
        if ($r_id > 0 && empty($Remark_info)) {
            $this->error('不存在');
        }
        $this->success($Remark_info);
    }
    public function edit()
    {
        $Remark = D('Remark');
        $id = I('post.r_id/d', 0);
        $data['r_id'] = $id;
        $data['s_id'] = I('post.s_id');
        $data['a_id'] = I('post.a_id');
        if (empty($data['s_id']) || empty($data['a_id'])) {
            $ids = $this->authRule();
            $data['s_id'] = $ids['s_id'];
            $data['a_id'] = $ids['a_id'];
        }
        $stu_id = I('post.stu_id');
        $data['g_id'] = I('post.g_id');
        $data['c_id'] = I('post.c_id');
        $data['stu_id'] = $stu_id;
        $data['r_type'] = I('post.r_type');
        $data['content'] = I('post.content');
        $data['user_id'] = $this->getUserId();
        $data['modify_time'] = date('Y-m-d H:i:s');
        if (!$Remark->create($data)) {
            $this->error($Remark->getError());
        } else {
            if ($id > 0) {
                $ret = $Remark->save($data);
            } else {
                $data['create_time'] = date('Y-m-d H:i:s');
                $ret = $Remark->add($data);
            }
            $content = ($id > 0 ? '编辑' : '新建') . '成功';
            if ($ret === false) {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
                $this->error($Remark->getError());
            } else {

                //app消息推送 By:ZhangYumeng 2018.01.11
                //获取学生详情
                $student = M('Student')
                    ->field('stu_id,s_id,a_id,g_id,c_id,stu_no,stu_name,imei_id,sex,stu_phone')
                    ->where(array('stu_id'=>$data['stu_id']))
                    ->find();
                if ($student) {
                    //获取家长信息
                    $parentId = M('StudentGroupAccess')
                        ->alias('a')
                        ->join('__STUDENT_PARENT__ b on a.sp_id = b.sp_id')
                        ->where("a.stu_id = '{$student['stu_id']}'")
                        ->getField('u_id', true);
                }
                $stu_name = $student['stu_name'];
                $rname = C('REMARK_TYPE')[$data['r_type']];

                $pushArr = array(
                    'fsdx' => '0,1',
                    'u_id' => $parentId,
                    'ticker' => '评语消息',
                    'title' => $rname ."-". $stu_name,
                    'text' =>$rname ."-". $stu_name,
                    'after_open' => 'go_custom',
                    'activity' => 'remark',
                    'id' => $ret,
                );
                sendAppMsg($pushArr);

                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                $this->success($content, U('/Admin/Parent/Comment/index'));
            }
        }
    }
    public function del()
    {
        $uIds = I('post.id');
        $arrUids = explode(',', $uIds);
        if (empty($arrUids)) {
            $this->error('参数错误');
        }
        $Remark = D('Remark');
        $okCount = 0;
        foreach ($arrUids as $k => $v) {
            $where['r_id'] = $v;
            $ret = $Remark->where($where)->delete();
            if ($ret == 1) {
                $okCount++;
            }
        }
        $content = "删除" . $okCount . "条信息";
        $state = $okCount > 0 ? 1 : 2;
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, $state);
        $this->success('删除成功' . $okCount . '条记录');
    }
}