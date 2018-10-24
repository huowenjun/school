<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-短信管'理
 */
namespace Admin\Controller\Parent;

use Admin\Controller\BaseController;

class MessageController extends BaseController
{
    /**
     *发送短信
     */
    public function index()
    {


        $this->display();
    }

    /**
     *收到短信
     */
    public function receive_mes()
    {
        $page = I('post.page', 1);
        $pagesize = I('post.$pagesize', 10);
        $sort = I('post.sort', 'sm_id');
        $order = $sort . ' ' . I('post.order', 'desc');
        $user_id = $this->getUserId();
        $username = I('get.username');
        $stime = I('get.stime');
        $etime = I('get.etime');
        $Message = D('Message');
        $MessageSub = D('MessageSub');
        if (!empty($username)) {
            $where1 = "name like '%" . $username . "%'";
            $usernameInfo = $Message->where($where1)->select();
            $user_id = "";
            if (empty($usernameInfo)) {
                $this->error('输入的账户不存在！');
            } else {
                foreach ($usernameInfo as $key => $value) {
                    $user_id = $user_id . $value['user_id'] . ",";
                }
            }
            $where['user_id'] = array('IN', rtrim($user_id, ','));
        }

        if ($stime && $etime) {
            $where['create_time'] = array("BETWEEN", array($stime . ' 00:00:00', $etime . ' 23:59:59'));
        } elseif ($stime) {
            $where['create_time'] = array("EGT", $stime . ' 00:00:00');
        } elseif ($etime) {
            $where['create_time'] = array("ELT", $etime . ' 23:59:59');
        }

        $where2['u_id'] = $user_id;
        $mSubInfo = $MessageSub->where($where2)->select();
        $smId = '';
        foreach ($mSubInfo as $key => $value) {
            $smId = $smId . $value['sm_id'] . ',';
        }
        $where['sm_id'] = array('IN', rtrim($smId, ','));
        $mesInfo = $Message->queryList('*', $where, $order, $page, $pagesize, '');
        foreach ($mesInfo['list'] as $key => &$value) {
            $where1['user_id'] = $value['user_id'];
            $mesaccountInfo = D('MessageAccount')->where($where1)->find();
            $value['user_id'] = $mesaccountInfo['username'];
            $messubInfo = $MessageSub->where($where1)->find();
            $value['username'] = $messubInfo['phone'];
        }
        $this->success($mesInfo);
        // var_dump($mesInfo['list']);
    }

    /**
     *已发短信
     */
    public function sent_mes()
    {
        $page = I('post.page', 1);
        $pagesize = I('post.$pagesize', 10);
        $sort = I('post.sort', 'sm_id');
        $order = $sort . ' ' . I('post.order', 'desc');
        $username = I('get.username');
        $stime = I('get.stime');
        $etime = I('get.etime');
        $where['user_id'] = $this->getUserId();
        $Message = D('Message');

        if (!empty($username)) {
            $where2 = "name like '%" . $username . "%'";
            $usernameInfo = $Message->where($where2)->select();
            $user_id = "";
            if (empty($usernameInfo)) {
                $this->error('输入的账户不存在！');
            } else {
                foreach ($usernameInfo as $key => $value) {
                    $user_id = $user_id . $value['user_id'] . ",";
                }
            }
            $where['user_id'] = array('IN', rtrim($user_id, ','));
        }
        if ($stime && $etime) {
            $where['create_time'] = array("BETWEEN", array($stime . ' 00:00:00', $etime . ' 23:59:59'));
        } elseif ($stime) {
            $where['create_time'] = array("EGT", $stime . ' 00:00:00');
        } elseif ($etime) {
            $where['create_time'] = array("ELT", $etime . ' 23:59:59');
        }

        $result = $Message->queryList('*', $where, $order, $page, $pagesize, '');
        foreach ($result['list'] as $key => &$value) {
            $where2['user_id'] = $value['user_id'];
            $where1['sm_id'] = $value['sm_id'];
            $Count = D('MessageSub')->where($where1)->count();
            $mesaccountInfo = D('MessageAccount')->where($where)->find();
            $value['user_id'] = $mesaccountInfo['username']; //账号
            $messubInfo = D('MessageSub')->where($where2)->find();
            $value['username'] = $messubInfo['phone'];
            $value['sumCount'] = $Count;
        }
        //dump($result['list']);
        $this->success($result);
    }

    //短息发布处理
    public function add()
    {
        $to_user = I('post.u_id');
        $toUser = explode(',', $to_user);
        $content = I('post.content');
        $user_id = $this->getUserId();
        $Message = D('Message');
        $where1['s_id'] = $this->authRule['s_id'];
        $data['s_id'] = $this->authRule['s_id'];
        $data['content'] = $content;
        $data['user_id'] = $user_id;
        $data['create_time'] = date('Y-m-d H:i:s');
        $accountInfo = D('MessageAccount')->where($where1)->find();
        if (empty($accountInfo)) {
            $this->error('短信服务没有开通');
        } else {
            if ($accountInfo['m_count'] - $accountInfo['use_count'] <= 0) {
                $this->error('短信余额不足');
            }
        }
        if (!$Message->create($data)) {
            $this->error($Message->getError());
        } else {
            $ret = $Message->add($data);
            $content = "发送成功,ID:" . $ret;
            if ($ret === false) {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 1, 2);
                $this->error($Message->getError());
            } else {
                $okCount = 0;
                foreach ($toUser as $key => $value) {
                    $uId = $value;
                    if (!$uId) continue;
                    $where['u_id'] = $uId;
                    $userPhone = D('Teacher')->field('phone')
                        ->union('select parent_phone as phone from sch_student_parent where u_id=' . $uId)
                        ->where($where)
                        ->select();
                    $date1['u_id'] = $uId;
                    $date1['phone'] = $userPhone[0]['phone'];
                    $date1['sm_id'] = $ret;
                    $res = D('MessageSub')->add($date1);
                    $okCount++;
                    //发送短信
                    $this->sendSms($date1['phone'], $data['content']);
                }

                D('MessageAccount')->where($where1)->setInc('use_count', $okCount);
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 1, 1);
                $this->success('发送成功');
            }
        }
    }
    // private function sendSms($phone,$msg){
    // 	vendor('ChuanglanSmsHelper.ChuanglanSmsApi');
    // 	$clapi  = new \ChuanglanSmsApi();
    // 	$result = $clapi->sendSMS($phone, $msg,'true');
    // 	$result = $clapi->execResult($result);
    // 	//var_dump($result);
    // 	if($result[1]==0){
    // 		\Think\Log::write('短信发送成功','WARN');
    // 		return true;
    // 	}else{
    // 		\Think\Log::write('短信发送失败,电话号码:'.$phone,'WARN');
    // 		return false;
    // 	}
    // }
    public function get_user_id()
    {
        // dump($_GET);die;
        $type = I('get.type'); //1 老师  2 家长
        $s_id = I('get.s_id');
        $a_id = I('get.a_id', 41);
        $g_id = I('get.g_id', 7);
        $c_id = I('get.c_id', 4);
        $where = array();
        if (!empty($s_id)) {
            $where['s_id'] = array('IN', rtrim($s_id, ','));
        }
        //老师ID
        $teacherList = "";
        $teacherInfo = D('Teacher')->where($where)->select();
        foreach ($teacherInfo as $key => $value) {
            $teacherList = $teacherList . $value['u_id'] . ',';
        }
        // var_dump($teacherList);
        //家长ID
        $parentList = "";
        $student = D('Student')->where($where)->select();
        $sutId = array();
        foreach ($student as $key => $value) {
            $sutId[] = $value['stu_id'];
        }
        $where2['stu_id'] = array('in', implode(',', $sutId));
        $parentId = D('StudentGroupAccess')->where($where2)->select();
        $spId = array();
        foreach ($parentId as $key => $value) {
            $spId[] = $value['sp_id'];
        }
        $where3['sp_id'] = array('in', implode(',', $spId));
        $studentParent = D('StudentParent')->where($where3)->select();
        foreach ($studentParent as $key => $value) {
            $parentList = $parentList . $value['u_id'] . ',';
        }
        // var_dump($teacherList);
        if ($type == 1) {
            $userList[] = $teacherList;
        } elseif ($type == 2) {
            $userList[] = $parentList;
        } else {
            $userList = array($teacherList . $parentList);
        }
        $this->success(implode(',', $userList));
    }
}