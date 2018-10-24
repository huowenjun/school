<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-在线咨询
 */
namespace Admin\Controller\Parent;

use Admin\Controller\BaseController;
        // echo 123;
class OnlineController extends BaseController
{
    /**
     *在线咨询
     */
    public function index()
    {
        $user_id = $this->getUserId();
        // var_dump($user_id);
        $ids = $this->authRule();
        $where="";
        if ($this->getType()==1) {
            $where["s_id"] = $ids['s_id'];
            // $where1["s_id"] = $ids['s_id'];
            $where1['p_id'] = 0;
            $where1['b.s_id'] = $where['s_id'];
        }elseif ($this->getType()==3) {
            // $where1['form_user'] = $user_id;
            // $where1['p_id'] = '0';
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
            $where1['b.s_id'] = $ids['s_id'];
            if (!empty($ids['a_id'])) {
               $where1["b.a_id"] = $ids['a_id'];
            }
            $where1['p_id'] = 0;
            $classList = $this->getTeacherClass($ids['t_id']);
            $where['g_id'] = array('IN',$classList['g_id']);//$ids['a_id'];
            $where['c_id'] = array('IN',$classList['c_id']);
        }elseif ($this->getType()==4) {
//            $where['s_id'] = $ids['s_id'];
//            $where['a_id'] = $ids['a_id'];
//            $where['stu_id'] = array('IN',$ids['stu_id']);
        }else{

        }
        // var_dump($where1);
        $p = I("get.p", 1);
        $pagesize = I('get.pagesize', $this->PAGE_SIZE);
        $sort = I('get.sort', 'id');
        $order = $sort . ' ' . I('get.order', 'desc');
        $teacher = I('get.teacher');//老师姓名
        $sdate = I('get.stime', date('Y-m-d', strtotime('-7 day'))); //开始时间
        $edate = I('get.etime', date('Y-m-d')); //结束时间
        $campus = I('get.campus');
        // var_dump($campus);
        $where['p_id'] = 0;
        $Qu = D('OnlineQu');
        if ($this->getType() == 3) {
            $where['form_user'] = $this->getUserId();
            if (!empty(I('get.campus'))) {
                $where['send_user'] = $campus;
            }
        } elseif ($this->getType() == 4) {
            $where['send_user'] = $this->getUserId();
        } else {
            if (!empty(I('get.campus'))) {
                $where['send_user'] = $campus;
            }
        }
        $sendName = $Qu->field('distinct a.send_user,b.name')->alias('a')->join('left join think_user b on a.send_user=b.user_id')->where($where1)->select();
        // echo M()->_Sql();
        //       echo "<pre>";
        // var_dump($sendName);die;
        $where['send_date'] = array('between', $sdate . "," . $edate);
        // echo 
        // var_dump($where);
        $result = $Qu->queryListEX('*', $where, $order, $p, $pagesize, '');

        $this->result = $result['list'];
        $this->p = $p;
        $this->sdate = $sdate;
        $this->edate = $edate;
        $this->campus = $campus;
        // echo "<pre>";
        // var_dump($sendName);
        $this->sendName = $sendName;
        $this->userType = $this->getType();
        //分页
        $Page = new \Think\Page($result['count'], $pagesize);// 实例化分页类 传入总记录数和每页显示的记录数
        $show = $Page->show();// 分页显示输出
        //dump($Page);
        //dump($show);
        $this->assign('page', $show);// 赋值分页输出

        $this->display();
    }


    /**
     *问题发布/回复/编辑
     * @param type 1 发布 2 回复 3 编辑
     * @param $_POST
     */
    public function edit()
    {
        $data['id'] = I('get.id', 0);
        $data['p_id'] = I('post.p_id', 0);
        // var_dump($data);
        $data['send_user'] = $this->getUserId();
        $data['title'] = I('post.title');
        $data['content'] = I('post.editorValue');

        $data['form_user'] = I('post.form_user');
        if (empty(I('post.form_user'))) {
            $this->error('请选择接收人！');
        }
        if (empty(I('post.title'))) {
            $this->error('请输入标题！');
        }
        if (empty(I('post.editorValue'))) {
            $this->error('请输入内容！');
        }
        if ($data['send_user'] == $data['form_user']) {
            $OnlineQuInfo = D('OnlineQu')->getInfo($data['p_id']);
            $data['form_user'] = $OnlineQuInfo['form_user'];
        }
        $data['send_date'] = date('Y-m-d');
        $data['create_time'] = date('Y-m-d H:i:s');
        $data['p_id'] = I('post.p_id', 0);
        $data['a_id'] = I('post.a_id', 0);
        $data['s_id'] = I('post.s_id', 0);
        $data['c_id'] = I('post.c_id', 0);
        $data['g_id'] = I('post.g_id', 0);

        $type = I('post.type', 1);
        // var_dump($type);
        $Qu = D('OnlineQu', 'Logic');
        $ret = $Qu->addOnline($data, $type);
        if ($ret === false) {
            $this->error($Qu->getError());
        } else {
            //var_dump($ret);exit;
            $this->success($ret['msg'], '/a.php/Admin/Parent/Online/index');
        }

    }

    /**
     *删除
     * @param $id
     */
    public function del()
    {
        $ids = I('post.id');
        $arrIds = explode(',', $ids);
        if (empty($arrIds)) {
            $this->error('参数错误');
        }
        $Qu = D('OnlineQu');
        //dump($arrIds);
        $okCount = 0;
        foreach ($arrIds as $k => $v) {
            $where['ma_id'] = $v;
            $ret = $Qu->where($where)->delete();
            if ($ret !== false) {
                $okCount++;  //处理成功记录数
            }
        }
        //写log
        $content = "删除问题" . $okCount . "条:" . $ids;
        $state = $okCount > 0 ? 1 : 2;
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $state);
        $this->success('删除成功' . $okCount . '条记录');
    }

    public function publish()
    {
        //老师列表
        if ($this->authRule()['s_id'] > 0) {
            $where['s_id'] = $this->authRule()['s_id'];
        }
        if ($this->authRule()['a_id'] > 0) {
            $where['a_id'] = $this->authRule()['a_id'];
        }

        if (!empty($this->authRule()['stu_id'])) {
            //获取所有孩子的班级列表
            $classArr = D('Student')->where("stu_id in ({$this->authRule()['stu_id']})")->group('c_id')->getField('c_id', true);
            //获取所有班级的代课老师列表
            if (!empty($classArr)) {
                $classStr = implode(',', $classArr);
                $teacherArr = M("TeacherCourse")->where("c_id IN ($classStr)")->group('t_id')->getField('t_id', true);
                $teacherArr2 = M("Class")->where("c_id IN ($classStr)")->group('t_id')->getField('t_id', true);
                if (!$teacherArr) {
                    $teacherArr = array();
                }
                if (!$teacherArr2) {
                    $teacherArr2 = array();
                }
                $newTeacherArr = array_unique(array_merge($teacherArr, $teacherArr2));

                if (!empty($newTeacherArr)) {
                    $where['t_id'] = array('in', $newTeacherArr);
                }
            }
            $Teacher = D('Teacher');
            $result = $Teacher->where($where)->getField('u_id,name');
        }else{
            $result = array();
        }


        $this->result = $result;
        $this->s_id = $this->authRule()['s_id'];
        $this->a_id = $this->authRule()['a_id'];
        $this->c_id = $this->authRule()['c_id'];
        $this->g_id = $this->authRule()['g_id'];
        $this->display();
    }

    //信息详情
    public function details()
    {
        $id = I("get.id");
        $Qu = D('OnlineQu');
        $where['id'] = $id;
        // var_dump($where);die;
        $where1['p_id'] = $id;
        $result = $Qu->where($where)->find();
        // var_dump($result);
        $result1 = $Qu->where($where1)->select();
        $this->result = $result;
        $this->result1 = $result1;
        $this->display();
    }
}