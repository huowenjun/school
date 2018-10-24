<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-我的信息
 */

namespace Admin\Controller\Parent;

use Admin\Controller\BaseController;


class MyMessageController extends BaseController
{
    public function index()
    {
        $type = I('post.type', 1);//1收到的 2发出
        $m_tab = I('post.m_tab', 'tab1');
        $Mymessage = D('Mymessage');
        $user_id = $this->getUserId();//获取user_id
        $m_type = I('post.m_type');// 消息类型
        $MymessageSub = D('MymessageSub');

        $p = I("get.p", 1);
        $pagesize = 10;
        $sort = I('post.sort', 'm_id');
        $order = $sort . ' ' . I('post.order', 'desc');
        if ($type == 1) {//收到的
            $where['user_id'] = $user_id;
            $mSubInfo = $MymessageSub->where($where)->select();
            foreach ($mSubInfo as $key => $value) {
                $mId = $mId . $value['m_id'] . ',';
            }
            $where1['m_id'] = array('IN', rtrim($mId, ','));
        } elseif ($type == 2) {//已发送的
            $where1['user_id'] = $user_id;
            $where1['del_state'] = 0;
        }
        if ($m_type == 1) {//系统消息
            $where1['m_type'] = array('EQ', 1);
        } elseif ($m_type == 2) {//公告
            $where1['m_type'] = array('EQ', 2);
        } elseif ($m_type == 3) {//私信
            $where1['m_type'] = array('EQ', 3);
        }

        $mymesInfo = $Mymessage->queryListEx('*', $where1, $order, $p, $pagesize, '');
        foreach ($mymesInfo['list'] as $key => $value) {
            $where2['user_id'] = $user_id;
            $where2['m_id'] = $value['m_id'];
            $mSub = $MymessageSub->where($where2)->getField('status');
            $mymesInfo['list'][$key]['status'] = $mSub;
        }

        $this->sentmes = $mymesInfo['list'];
        $this->type = $type;//收 已 发
        $this->p = $p;
        $this->m_type = $m_type;
        $this->m_tab = $m_tab;//收 已
        $Page = new \Think\Page($mymesInfo['count'], $pagesize);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出

        $this->assign('page', $show);// 赋值分页输出
        $this->s_id = $this->authRule['s_id'];
        $this->a_id = $this->authRule['a_id'];
        $this->g_id = $this->authRule['g_id'];
        $this->c_id = $this->authRule['c_id'];
        $this->mType = C('MESSAGE_TYPE');
        $this->display();
    }

    public function detail()
    {//详情页
        $MymessageSub = D('MymessageSub');
        $Mymessage = D('Mymessage');
        $mId = I('get.m_id');
        $where1['m_id'] = $mId;
        $where1['user_id'] = $this->getUserId();
        $data1['status'] = 1;
        $MymessageSub->where($where1)->save($data1);
        $where['m_id'] = $mId;
        $user_id = $this->getUserId();//获取user_id
        $detail = $Mymessage->queryListEx('*', $where, $order, $page, $pagesize, '');
        foreach ($detail['list'] as $key => $value) {
            $where2['m_id'] = $value['m_id'];
            $where2['user_id'] = $user_id;
            $mSub = $MymessageSub->where($where2)->find();
            $value['status'] = $mSub['status'];
            $detailInfo = array(
                'm_id' => $value['m_id'],
                'user_id' => $value['user_id'],
                'm_type' => $value['m_type'],
                'title' => $value['title'],
                'content' => $value['content'],
                'create_time' => $value['create_time'],
                'status' => $mSub['status']
            );
        }
        $this->detail = $detailInfo;
        $this->display();
    }

    /**
     *发送新消息
     *
     */
    public function mymes_add()
    {
        $Mymessage = M('Mymessage');
        $data['s_id'] = I('post.s_id');
        $data['a_id'] = I('post.a_id');
        $data['g_id'] = I('post.g_id');
        $data['c_id'] = I('post.c_id');
        $m_type = I('post.m_type', '1');//消息类型 1 系统消息2 公告3私信
        $data['m_type'] = I('post.m_type');
        $toUser = I('post.u_id');
        $data['title'] = I('post.title');
        if ($m_type == 1) {
            //系统消息
            $data['title'] = "系统消息";
            $toUser = '';
        } elseif ($m_type == 2) {
            $data['jjcd'] = I('post.jjcd', 1);//紧急程度
        } elseif ($m_type == 3) {

        }

        $data['fsdx'] = I('post.fsdx', 2); //发送对象 1 组织 2 个人

        $data['content'] = I('post.content', '测试数据');

        $data['to_user'] = $toUser;
        $data['user_id'] = $this->getUserId();
        $data['create_time'] = date('Y-m-d H:i:s');
        $ret = $Mymessage->add($data);
        $id = $ret;
        if (!$Mymessage->create($data)) {
            $this->error($Mymessage->getError());
        } else {
            $toUser = explode(',', $toUser);
            $data1['m_id'] = $ret;
            if ($m_type != 1) {
                foreach ($toUser as $key => $value) {
                    $data1['user_id'] = $value;
                    $date1['date_time'] = date('Y-m-d H:i:s');
                    D('MymessageSub')->add($data1);
                }
            }

        }
        //线上服务器下发APP公告推送 By:Meng Fanmin 2017.06.23
        if ($_SERVER['SERVER_ADDR'] == C('SERVERADDR')) {
            $pushArr = array(
                'fsdx' => $data['fsdx'],
                'u_id' => trim(I('post.u_id'), ','),
                'ticker' => '班级公告',
                'title' => I('post.title'),
                'text' => I('post.title'),
                'after_open' => 'go_custom',
                'activity' => 'notice',
                'id' => $id,
            );
            sendAppMsg($pushArr);
        }

        $content = '发送成功!';
        $state = $ret > 0 ? 1 : 2;
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, $state);
        $this->success($content, U('/Admin/Parent/Mymessage/index'));

    }


    /**
     *已发送的信息
     */
    public function sentMes_del()
    {
        $Mymessage = D('Mymessage');
        $mIds = I('post.m_id');
        $user_id = $this->getUserId();
        $arrMids = explode(',', $mIds);
        $where['user_id'] != $user_id;
        if (empty($mIds) && $where) {
            $this->error('参数错误');
        } else {
            foreach ($arrMids as $k => $v) {
                $data['del_state'] = 1;//已删除
                $where['m_id'] = $v;
                $ret = $Mymessage->where($where)->save($data);
            }
        }
        $content = '操作成功!';
        $state = $ret > 0 ? 1 : 2;
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, $state);
        $this->success('删除成功', U('/Admin/Parent/Mymessage/index'));

    }

    /**
     *收到的信息
     */
    public function readed()
    { //设置成已读
        $MymessageSub = D('MymessageSub');
        $msIds = I('post.m_id');
        $arrMsids = explode(',', $msIds);
        $user_id = $this->getUserId();
        $where['user_id'] = $user_id;
        $okCount = 0;
        if (empty($arrMsids)) {
            $this->error('参数错误');
        } else {
            foreach ($arrMsids as $k => $v) {
                $data['status'] = 1;//已读
                $data['data_time'] = date('Y-m-d H:i:s');//已读
                $where['m_id'] = $v;
                $ret = $MymessageSub->where($where)->save($data);
                if ($ret == 1) {
                    $okCount++;
                }
            }
        }
        $content = '操作成功' . $okCount . '条记录!';
        $state = $ret > 0 ? 1 : 2;
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, $state);
        $this->success($content);
    }

    public function receiveMes_del()
    {//删除
        $MymessageSub = D('MymessageSub');
        $msIds = I('post.m_id');
        $user_id = $this->getUserId();
        $arrMsids = explode(',', $msIds);
        $where['user_id'] != $user_id;
        if (empty($msIds) && $where) {
            $this->error('参数错误');
        } else {
            foreach ($arrMsids as $k => $v) {
                $where['m_id'] = $v;
                $where['user_id'] = $user_id;
                $ret = $MymessageSub->where($where)->delete();
            }
        }
        $content = '删除成功!';
        $state = $ret > 0 ? 1 : 2;
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, $state);
        $this->success($content, U('/Admin/Parent/Mymessage/index'));
    }

    //老师或家长ID
    public function get_user_id()
    {
        // dump($_GET);die;
        $ids = $this->authRule();// 权限
        $type = I('get.fsdx'); //1,0 老师 1,1 老师和家长  0,1 家长
        $s_id = I('get.s_id', $ids['s_id']);
        $a_id = I('get.a_id', $ids['a_id']);
        $g_id = I('get.g_id');
        $c_id = I('get.c_id');
        $stuId = I('get.stu_id');
        $where = array();
        if ($this->getType() == 1) {// 学校管理员
            $where1['s_id'] = $ids['s_id'];
            $where1['g_id'] = array('IN', $g_id);
            $where1['c_id'] = array('IN', $c_id);
        } elseif ($this->getType() == 3) {// 老师
            $where1['s_id'] = $ids['s_id'];
            $where1['a_id'] = $ids['a_id'];
            $classList = $this->getTeacherClass($ids['t_id']);
            $where1['g_id'] = array('IN', $classList['g_id']);
            $where1['c_id'] = array('IN', $classList['c_id']);
        } elseif ($this->getType() == 4) {// 家长
            $where1['s_id'] = $ids['s_id'];
            $where1['a_id'] = $ids['a_id'];
            $stuclassList = $this->getStudentClass($stuId);
            $where1['g_id'] = array('IN', $stuclassList['g_id']);
            $where1['c_id'] = array('IN', $stuclassList['c_id']);
        } else {
            if (empty($s_id) || empty($a_id) || empty($g_id) || empty($c_id)) {
                $this->error('参数错误');
            } else {
                $where1['s_id'] = array('IN', rtrim($s_id, ','));
                $where1['a_id'] = array('IN', rtrim($a_id, ','));
                $where1['g_id'] = array('IN', rtrim($g_id, ','));
                $where1['c_id'] = array('IN', rtrim($c_id, ','));
            }
        }
        $where1['_logic'] = "OR";
        $where['_complex'] = $where1;
        // var_dump($where);
        //老师ID
        $Teacher = D('Teacher');
        $teacherList = "";
        $where1['s_id'] = array('IN', rtrim($s_id, ','));
        $where1['a_id'] = array('IN', rtrim($a_id, ','));
        $TeacherCourseInfo = D('TeacherCourse')->where(array('g_id' => $where1['g_id'], 'c_id' => $where1['c_id']))->getField('t_id', true);
        $ClassTeacher = D('Class')->where(array('g_id' => $where1['g_id'], 'c_id' => $where1['c_id']))->getField('t_id', true);
        // var_dump(D('TeacherCourse')->getLastSql());die;
        $t_id1 = '';
        $t_id2 = '';
        foreach ($TeacherCourseInfo as $k => $v) {
            $t_id1 = $t_id1 . $v . ",";
        }
        foreach ($ClassTeacher as $k => $v) {
            $t_id2 = $t_id2 . $v . ",";
        }
        $t_id = $t_id1 . $t_id2;
        // var_dump($t_id);die;
        $where4['t_id'] = array('IN', $t_id);
        $teacherInfo = $Teacher->where($where4)->select();
        foreach ($teacherInfo as $key => $value) {
            $teacherList = $teacherList . $value['u_id'] . ',';
        }
        // var_dump($teacherList);die;
        //家长ID
        $parentList = "";
        $student = D('Student')->where($where)->select();
        $sutId = array();
        foreach ($student as $key => $value) {
            $sutId[] = $value['stu_id'];
        }
        $where2['stu_id'] = array('in', implode(',', $sutId));
        // var_dump($where2);
        $parentId = D('StudentGroupAccess')->where($where2)->getField('sp_id', true);


        $where3['sp_id'] = array('in', $parentId);
        // var_dump($where3);
        $studentParent = D('StudentParent')->where($where3)->select();
        foreach ($studentParent as $key => $value) {
            $parentList = $parentList . $value['u_id'] . ',';
        }
        // var_dump($parentList);
        if ($type == '1,0') {
            $userList[] = $teacherList;
        } elseif ($type == '0,1') {
            $userList[] = $parentList;
        } elseif ($type == '1,1') {
            $userList = array($teacherList . $parentList);
        }
        $this->success(implode(',', $userList));
    }

    //个人
    public function get_user_id1()
    {
        $ids = $this->authRule();
        $where = "";
        $type = I('get.type'); //1 老师 2 家长
        // $s_id = I('get.s_id');
        $a_id = I('get.a_id');
        $g_id = I('get.g_id');
        $c_id = I('get.c_id');
        $stuId = I('get.stu_id');
        $Teacher = D('Teacher');
        if ($this->getType() == 1) {// 学校管理员
            $where['s_id'] = $ids['s_id'];
        } elseif ($this->getType() == 3) {// 老师
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
            $classList = $this->getTeacherClass($ids['t_id']);
            $where['g_id'] = array('IN', $classList['g_id']);
            $where['c_id'] = array('IN', $classList['c_id']);
        } elseif ($this->getType() == 4) {// 家长
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
            $stuclassList = $this->getStudentClass($stuId);
            $where['g_id'] = array('IN', $stuclassList['g_id']);
            $where['c_id'] = array('IN', $stuclassList['c_id']);
        } else {
            // 			if (empty($a_id) || empty($g_id) || empty($c_id)) {
            // 	$this->error('参数错误');
            // }
            if (!empty($a_id)) {
                $where['a_id'] = $a_id;
            }
            if (!empty($g_id)) {
                $where['g_id'] = $g_id;
            }
            if (!empty($c_id)) {
                $where['c_id'] = $c_id;
            }
            if (!empty($stuId)) {
                $where['stu_id'] = $stuId;
            }
        }

        $dataInfo = array();

        if ($type == 1) {
            // $where['stu_id'] = $stuId;
            $studentInfo = D('Student')->where($where)->field('g_id,c_id')->find();
            $TeacherCourseInfo = D('TeacherCourse')->where(array('g_id' => $studentInfo['g_id'], 'c_id' => $studentInfo['c_id']))->getField('t_id', true);
            $ClassTeacher = D('Class')->where(array('g_id' => $where1['g_id'], 'c_id' => $where1['c_id']))->getField('t_id', true);

            $t_id1 = '';
            $t_id2 = '';
            foreach ($TeacherCourseInfo as $k => $v) {
                $t_id1 = $t_id1 . $v . ",";
            }
            foreach ($ClassTeacher as $k => $v) {
                $t_id2 = $t_id2 . $v . ",";
            }
            $t_id = $t_id1 . $t_id2;
            $where2['t_id'] = array('IN', $t_id);
            $dataInfo = $Teacher->where($where2)->getField('u_id,name');
            // foreach ($teacherInfo as $key => $value) {
            // 	$userList[] = $value['u_id'];
            // }
            // $dataInfo = implode(',', $userList);
        } elseif ($type == 2) {
            $student = D('Student')->where($where)->select();
            // var_dump($student);
            // echo D()->_sql();
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
            $spUid = D('StudentParent')->where($where3)->select();
            $uId = array();
            foreach ($spUid as $key => $value) {
                $uId[] = $value['u_id'];
            }
            $where4['user_id'] = array('in', implode(',', $uId));
            $dataInfo = D('AdminUser')->where($where4)->getField('user_id,name');

        } else {
            $this->error('异常错误!');
        }
        // var_dump($dataInfo);
        $this->success($dataInfo);
    }

}


