<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-奖励管理
 */
namespace Admin\Controller\Parent;

use Admin\Controller\BaseController;

class RewardController extends BaseController
{
    /**
     *奖励管理
     */
    public function index()
    {
        $this->display();
    }

    public function query()
    {
        //权限
        $ids = $this->authRule();
        $student = I('get.stu_id_query');// 学生
        $user_id = I('get.user_id');// 发布人
        $rewardtype = I('get.reward_type');// 奖励类型
        $sId = I('get.s_id');
        $aId = I('get.a_id');
        $gId = I('get.g_id');
        $cId = I('get.c_id');
        $stuId = I('get.stu_id');
        $page = I('get.page');
        $pagesize = I('get.pagesize', $this->PAGE_SIZE);
        $sort = I('get.sort', 'rw_id');
        $order = $sort . ' ' . I('get.order', 'desc');
        $map = "";
        if ($this->getType() == 1) {// 学校管理员
            $map['s_id'] = $ids['s_id'];
        } elseif ($this->getType() == 3) {// 老师
            $map['s_id'] = $ids['s_id'];
            $map['a_id'] = $ids['a_id'];
            $classList = $this->getTeacherClass($ids['t_id']);
            $map['g_id'] = array('IN', $classList['g_id']);//$ids['a_id'];
            $map['c_id'] = array('IN', $classList['c_id']);
        } elseif ($this->getType() == 4) {// 家长
            $map['s_id'] = $ids['s_id'];
            $map['a_id'] = $ids['a_id'];
            $map['stu_id'] = array('IN', $ids['stu_id']);
        } else {
            $arr = $_GET;
            $sthInfo = getSchoolId($arr);
            $map['s_id'] = array('IN', $sthInfo);
        }
        $Reward = D('Reward');
        if ($student != '') {
            $where = "stu_name like '%" . $student . "%'";
            $stuInfo = D('Student')->where($where)->select();
            $stu_id = '';
            if (empty($stuInfo)) {
                $this->error("您输入的学生不存在哟！");
            } else {
                foreach ($stuInfo as $key => $value) {
                    $stu_id = $stu_id . $value['stu_id'] . ",";
                }
            }
            $map['stu_id'] = array('IN', rtrim($stu_id, ','));
        }
        if ($user_id != '') {
            $where1 = "name like '%" . $user_id . "%'";
            $issInfo = M('user', 'think_')->where($where1)->select();
            $user_id = '';
            if (empty($issInfo)) {
                $this->error("您输入的发布人不存在哟！");
            } else {
                foreach ($issInfo as $key => $value) {
                    $user_id = $user_id . $value['user_id'] . ",";
                }
            }
            $map['user_id'] = array('IN', rtrim($user_id, ','));
        }
        if ($rewardtype) $map['reward_type'] = $rewardtype;
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
        $result = $Reward->queryListEx('*', $map, $order, $page, $pagesize, '');
        $this->success($result);
    }

    public function get()
    {
        $rw_id = I('get.rw_id/d', 0);
        $Reward = D('Reward');
        $reward_info = $Reward->getInfo($rw_id);
        if ($rw_id > 0 && empty($reward_info)) {
            $this->error('不存在');
        }
        $this->success($reward_info);
    }

    //新增/编辑
    public function edit()
    {

        $ids = $this->authRule();
        $Reward = D('Reward');
        $Class = D('class');
        $id = I('post.rw_id/d', 0);
        $data['rw_id'] = $id;
        $data['a_id'] = I('post.a_id');
        $data['s_id'] = I('post.s_id');
        $data['g_id'] = I('post.g_id');
        $data['c_id'] = I('post.c_id');
        $data['stu_id'] = I('post.stu_id');
        $data['reward_type'] = I('post.reward_type');
        $data['reward_details'] = I('post.reward_details');
        $data['modify_time'] = date('Y-m-d H:i:s');
        $data['user_id'] = $this->getUserId();
        if ($this->getType() == 1) {// 学校管理员
            $data['s_id'] = $ids['s_id'];
        } elseif ($this->getType() == 3) {// 老师
            $data['s_id'] = $ids['s_id'];
            $data['a_id'] = $ids['a_id'];
        } elseif ($this->getType() == 4) {// 家长
            // $map['s_id'] = $ids['s_id']; 
            // $map['a_id'] = $ids['a_id'];
            // $map['stu_id'] = array('IN',$ids['stu_id']);
        } else {

        }
        if (!$Reward->create($data)) {
            $this->error($Reward->getError());
        } else {
            if ($id > 0) {
                $ret = $Reward->save($data);
            } else {
                $data['create_time'] = date('Y-m-d H:i:s');
                $ret = $Reward->add($data);
            }
            $content = ($id > 0 ? '编辑' : '新建') . '成功';
            if ($ret === false) {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
                $this->error($Reward->getError());
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
                $rname = C('REWARD_TYPE')[$data['reward_type']];

                $pushArr = array(
                    'fsdx' => '0,1',
                    'u_id' => $parentId,
                    'ticker' => '奖励消息',
                    'title' => $rname."-".$stu_name,
                    'text' => $rname."-".$stu_name,
                    'after_open' => 'go_custom',
                    'activity' => 'honor',
                    'id' => $ret,
                );
                sendAppMsg($pushArr);

                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                $this->success($content, U('/Admin/Parent/Reward/index'));
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
        $Reward = D('Reward');
        $okCount = 0;
        foreach ($arrUids as $k => $v) {
            $where['rw_id'] = $v;
            $ret = $Reward->where($where)->delete();
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
        $arrRet = array();
        $arrRet = D('Class')->getField('c_id as id,name as value');
        // var_dump($arrRet);
        $this->success($arrRet);
    }
}


