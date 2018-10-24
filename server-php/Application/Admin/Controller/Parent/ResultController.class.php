<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-成绩管理
 */

namespace Admin\Controller\Parent;

use Admin\Controller\BaseController;

class ResultController extends BaseController
{
    /**
     *统考
     */
    public function index()
    {
        $this->display();
    }

    //统考-查询数据
    public function query_index()
    {
        //权限
        $ids = $this->authRule();
        $where1 = "";
        if ($this->getType() == 1) {// 学校管理员
            $where1['d.s_id'] = $ids['s_id'];
        } elseif ($this->getType() == 3) {// 老师
            $where1['d.s_id'] = $ids['s_id'];
            $where1['d.a_id'] = $ids['a_id'];
            $classList = $this->getTeacherClass($ids['t_id']);
            $where1['d.g_id'] = array('IN', $classList['g_id']);//$ids['a_id'];
            $where1['d.c_id'] = array('IN', $classList['c_id']);
        } elseif ($this->getType() == 4) {// 家长
            $where1['d.s_id'] = $ids['s_id'];
            $where1['d.a_id'] = $ids['a_id'];
            $classList = $this->getStudentClass($ids['stu_id']);
            $where1['d.g_id'] = array('IN', $classList['g_id']);
            $where1['d.c_id'] = array('IN', $classList['c_id']);
        } else {

        }
        //搜索条件
        $student = I('get.stu_name_id');
        if ($student != '') {
            $where = "stu_name like '%" . $student . "%'";
            $studentInfo = D('Student')->where($where)->select();
            $stu_id = "";
            foreach ($studentInfo as $key => $value) {
                $stu_id = $stu_id . $value['stu_id'] . ",";
            }
            $where1['d.stu_id'] = array('IN', rtrim($stu_id, ','));
        }
        $Exam = D('ExamAll');
        if (I('get.e_id')) {
            $where1['d.e_id'] = I('get.e_id');
        }

        $groupby = "c.stu_name,e.name desc";
        $result = $Exam->queryListR('c.c_id,c.stu_id,c.stu_name,e.name,IFNULL(d.scores,0) as scores', $where1, $order, 0, '', $groupby);
        //dump($result['list']);
        $arrs = "";
        $arrs_km = "";
        foreach ($result['list'] as $k => $v) {
            $ts = $v['c_id'] . $v['stu_name'];
            $arrs[$ts]['sum'] = $v['scores'] + $arrs[$ts]['sum'];
            $arrs[$ts][$v['name']] = $v['scores'];
            $arrs[$ts]['name'] = $v['stu_name'];
            $arrs[$ts]['stu_id'] = $v['stu_id'];
            $arrs[$ts]['c_id'] = $v['c_id'];
            $arrs_km[$v['name']] = $v['name'];
        }
        rsort($arrs);
        $arr['result'] = $arrs;
        $arr['course'] = $arrs_km;
        // dump($arr);
        $this->success($arr);
    }

    //统考-新增
    public function examAll_add()
    {
        $Student = D('Student');
        $g_id = I('post.g_id');
        $c_id = I('post.c_id');
        $crs_id = I('post.crs_id');
        $e_id = I('post.e_id');
        $where['g_id'] = $g_id;
        $where['c_id'] = $c_id;
        $where1['g_id'] = $g_id;
        $where1['c_id'] = $c_id;
        $where1['crs_id'] = $crs_id;
        $where1['e_id'] = $e_id;
        $type = 0;
        $results = D('ExamAllResults')->where($where1)->select();
        if (!empty($results)) {
            $type = 1;
        }
        if ($g_id || $c_id) {
            $stu = $Student->where($where)->select();
        } else {
            $stu = array();
        }

        $this->crs_id = $crs_id;
        $this->g_id = $g_id;
        $this->e_id = $e_id;
        $this->c_id = $c_id;
        $this->stu = $stu;
        $this->type = $type;
        $this->display();
    }

    public function examAll_add_handle()
    {
        $ExamAllResults = D('ExamAllResults');
        $stu_id = I('post.stu_id');
        $g_id = I('post.g_id');
        $c_id = I('post.c_id');
        $e_id = I('post.e_id');
        $crs_id = I('post.crs_id');
        $scores = I('post.scores');
        $examInfo = D('ExamAll')->where(array('e_id' => $e_id))->find();
        $data['s_id'] = $examInfo['s_id'];
        $data['a_id'] = $examInfo['a_id'];
        $data['crs_id'] = $crs_id;
        $data['g_id'] = $g_id;
        $data['c_id'] = $c_id;
        $data['e_id'] = $e_id;
        $data['create_time'] = date('Y-m-d H:i:s');
        for ($i = 0; $i < count($stu_id); $i++) {
            $data['scores'] = $scores[$i];
            $data['stu_id'] = $stu_id[$i];
            $ret = $ExamAllResults->add($data);
        }
        $content = '添加成功!';
        $state = $ret > 0 ? 1 : 2;
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, $state);
        $this->success($content, U('/Admin/Parent/Result/index'));
    }

    //统考-编辑
    public function examAll_edit()
    {
        $stu_id = I('get.stu_id');
        $e_id = I('get.e_id');
        $examAllResults = D('ExamAllResults');
        $student = D('Student');
        $where['stu_id'] = array('EQ', $stu_id);
        $where['e_id'] = $e_id;
        $results = $examAllResults->where($where)->select();
        foreach ($results as $key => $value) {
            $where1['stu_id'] = $stu_id;
            $studentInfo = $student->where($where1)->getField('stu_id,stu_name,stu_no');
            $where2['crs_id'] = $value['crs_id'];
            $courseInfo = D('Course')->where($where2)->getField('crs_id,name');
            $results[$key]['stu_name'] = $studentInfo[$value['stu_id']]['stu_name'];
            $results[$key]['stu_no'] = $studentInfo[$value['stu_id']]['stu_no'];
            $results[$key]['crs_name'] = $courseInfo[$value['crs_id']];
        }
        $this->results = $results;
        $this->display();
    }

    public function examAll_edit_handle()
    {
        $ExamAllResults = D('ExamAllResults');
        $stu_id = I('post.stu_id');
        $crs_id = I('post.scores');
        $where['stu_id'] = $stu_id;
        foreach ($crs_id as $key => $value) {
            $crs_id = I('post.crs_id');
            $scores = I('post.scores');
            $data['scores'] = $scores[$key];
            $data['crs_id'] = $crs_id[$key];
            $where['crs_id'] = $crs_id[$key];
            //dump($where);die;
            $ret = $ExamAllResults->where($where)->save($data);
        }

        if ($ret === false) {
            $this->error('编辑失败');
        } else {
            $content = "编辑学生成绩";
            $state = $ret > 0 ? 1 : 2;
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, $state);
            $this->success('编辑学生成绩成功');
        }
    }

    //统考-删除
    public function del_index()
    {
        $uIds = I('post.e_id');
        $arrUids = explode(',', $uIds);
        if (empty($arrUids)) {
            $this->error('参数错误');
        }
        $ExamAllResult = D('ExamAllResults');
        $okCount = 0;
        foreach ($arrUids as $k => $v) {
            $where['e_id'] = $v;
            $ret = $ExamAllResult->where($where)->delete();
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

    //统考-成绩导入
    public function import1()
    {
        $s_id = I('get.s_id');
        $a_id = I('get.a_id');
        $g_id = I('get.g_id');
        $c_id = I('get.c_id');
        $e_id = I('get.e_id');// 考试
        // var_dump($e_id);die;

        $crs_id = I('get.crs_id');// 学科
        $file_url = I('post.file_url');
        if (empty($file_url)) {
            $this->error('参数错误');
            # code...
        }
        // $file_url = I('E:\work\成绩录入模板.xls');
        $file_url = $_SERVER['DOCUMENT_ROOT'] . $file_url;
        // var_dump($file_url);
        $arrField = array('学生学号' => 'stu_no', '学生姓名' => 'stu_name', '成绩' => 'scores');
        // var_dump($arrField);
        $arrList = import_excel($arrField, $file_url);
        // echo "<pre>";
        // var_dump($arrList);
        unlink($file_url);
        if (count($arrList) > 500) {
            D('SystemLog')->writeLog($this->getModule(), '导入数据不能超过500条', $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
            $this->error('导入数据不能超过500条');
        }
        $ExamAllResults = D('ExamAllResults');// 学生表
        //检查数据有效性
        foreach ($arrList as $key => $value) {
            $value['stu_no'] = 0;
            if (!$ExamAllResults->create($value)) {
                $key += 2;
                D('SystemLog')->writeLog($this->getModule(), '成绩导入失败,第' . $key . '行数据错误:' . $ExamAllResults->getError(), $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                $this->error('第' . $key . '行数据错误:' . $ExamAllResults->getError());
            }
        }
        //检查学生是否重复
        $stuList = array();
        foreach ($arrList as $value) {
            $stuList[$value['stu_no']] = 1;
        }
        if (count($stuList) < count($arrList)) {
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
        }
        //插入数据
        // var_dump($arrList);
        $data['a_id'] = $a_id;   // 校区ID
        $data['s_id'] = $s_id;   // 学校ID
        $data['g_id'] = $g_id;   // 年级ID
        $data['c_id'] = $c_id;   // 班级ID
        $data['e_id'] = $e_id;   // 班级ID
        $data['crs_id'] = $crs_id;   // 班级ID

        foreach ($arrList as $value) {
            $where['stu_no'] = $value['stu_no'];
            $where['stu_name'] = $value['stu_name'];
            $stuInfo = D('Student')->where($where)->getField('stu_id');//获取stu_id
            if (empty($stuInfo)) {
                $this->error("输入的学生或学号不存在");
            }
            // var_dump($stuInfo);die;
            $data['stu_id'] = $stuInfo; // stu_id
            // var_dump($data['stu_id']);die;
            $data['scores'] = $value['scores']; // 成绩
            $data['create_time'] = date('Y-m-d H:i:s');
            $res1 = $ExamAllResults->add($data);
            $content = '导入成功';
            if ($res1 === false) {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
                $this->error($Teacher->getError());
            } else {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                $this->success($content, U('/Admin/Parent/Result/index'));
            }
        }
    }

    /**
     *单考
     */
    public function exam_result()
    {

        $this->display();
    }

    //单考-查询数据
    public function query_exam()
    {
        //权限
        $ids = $this->authRule();

        $ex_id = I('get.ex_id');// 单考名称
        $student = I('get.stu_name_id');// 学生姓名
        $course = I('get.crs_id');// 学科
        $sId = I('get.s_id', $ids['s_id']);//学校
        $aId = I('get.a_id', $ids['a_id']);//校区
        $gId = I('get.g_id', $ids['g_id']);//年级
        $cId = I('get.c_id', $ids['c_id']);//班级
        $authId = $this->authRule();
        if ($authId['s_id'] > 0) {
            $where['s_id'] = $authId['s_id'];
        }
        $page = I('get.page');
        $pagesize = I('get.pagesize', $this->PAGE_SIZE);
        $sort = I('get.sort', 'exs_id');
        $order = $sort . ' ' . I('get.order', 'desc');
        $ExamResults = D('ExamResults');

        $map = "";
        if ($this->getType() == 1) {
            $map['s_id'] = $ids['s_id'];
        } elseif ($this->getType() == 3) {
            $map['s_id'] = $ids['s_id'];
            $map['a_id'] = $ids['a_id'];
            $classList = $this->getTeacherClass($ids['t_id']);
            $map['g_id'] = array('IN', $classList['g_id']);
            $map['c_id'] = array('IN', $classList['c_id']);
        } elseif ($this->getType() == 4) {
            $map['s_id'] = $ids['s_id'];
            $map['a_id'] = $ids['a_id'];
            $map['stu_id'] = array('IN', $ids['stu_id']);
        } else {
            $arr = $_GET;
            $sthInfo = getSchoolId($arr);
            $map['s_id'] = array('IN', $sthInfo);
        }

        if ($course != '') {
            $where = "name like '%" . $course . "%'";
            $exsInfo = D('Course')->where($where)->select();
            $crs_id = "";
            if (empty($exsInfo)) {
                $this->error('输入学科不存在！');
            } else {
                foreach ($exsInfo as $key => $value) {
                    $crs_id = $crs_id . $value['crs_id'] . ",";
                }
            }
            $map['crs_id'] = array('IN', rtrim($crs_id, ','));
        }
        if ($student != '') {
            $where1 = "stu_name like '%" . $student . "%'";
            $studentInfo = D('Student')->where($where1)->select();
            $stu_id = "";
            if (empty($studentInfo)) {
                $this->error('未查找到您输入的学生姓名！');
            } else {
                foreach ($studentInfo as $key => $value) {
                    $stu_id = $stu_id . $value['stu_id'] . ",";
                }
            }
            $map['stu_id'] = array('IN', rtrim($stu_id, ','));
        }
        if ($ex_id != '') {
            $map['ex_id'] = $ex_id;
        }
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

        $result = $ExamResults->queryListEx('*', $map, $order, $page, $pagesize, '');
        $this->success($result);
    }

    //单考-新增/编辑
    public function exam_add()
    {
        $Student = D('Student');
        $g_id = I('post.g_id');
        $c_id = I('post.c_id');
        $ex_id = I('post.ex_id');
        $where['g_id'] = $g_id;
        $where['c_id'] = $c_id;
        $where['ex_id'] = $ex_id;
        $type = 0;
        $results = D('ExamResults')->where($where)->select();
        if (!empty($results)) {
            $type = 1;
        }
        $stu = "";
        if (!empty($_POST)) {
            $stu = $Student->where($where)->select();
        }
        //dump($stu);
        $this->stu = $stu;
        $this->ex_id = $ex_id;
        $this->type = $type;
        $this->g_id = $g_id;
        $this->c_id = $c_id;
        $this->a_id = I('post.a_id');
        $this->s_id = I('post.s_id');

        $this->display();
    }

    public function exam_add_handle()
    {
        $ids = $this->authRule();
        $ExamResults = D('ExamResults');
        $stu_id = I('post.stu_id');
        $ex_id = I('post.ex_id');
        $crs_id = I('post.crs_id');
        $s_id = I('post.s_id');
        $a_id = I('post.a_id');
        $g_id = I('post.g_id');
        $c_id = I('post.c_id');
        $scores = I('post.scores');
        $examInfo = D('Exam')->where(array('ex_id' => $ex_id))->find();
        $data['crs_id'] = $examInfo['crs_id'];
        $data['s_id'] = $s_id;
        $data['a_id'] = $a_id;
        $data['g_id'] = $g_id;
        $data['c_id'] = $c_id;
        $data['ex_id'] = $ex_id;
        if ($this->getType() == 1) {// 学校管理员
            $data['s_id'] = $ids['s_id'];
        } elseif ($this->getType() == 3) {// 老师
            $data['s_id'] = $ids['s_id'];
            $data['a_id'] = $ids['a_id'];
        } elseif ($this->getType() == 4) {// 家长
            $this->error('对不起,您没有权限');
        } else {

        }
        for ($i = 0; $i < count($stu_id); $i++) {
            $data['scores'] = $scores[$i];
            $data['stu_id'] = $stu_id[$i];
            $data['create_time'] = date('Y-m-d H:i:s');
            $ret = $ExamResults->add($data);
        }
        $content = '添加成功!';
        $state = $ret > 0 ? 1 : 2;
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, $state);
        $this->success($content, U('/Admin/Parent/Result/index'));
    }

    public function exam_edit()
    {
        $exs_id = I('get.exs_id');
        $examResults = D('ExamResults');
        $where['exs_id'] = $exs_id;
        $result = $examResults->where($where)->find();
        $where1['stu_id'] = $result['stu_id'];
        $studentInfo = D('Student')->where($where1)->getField('stu_id,stu_no,stu_name');
        $results = array(
            'stu_name' => $studentInfo[$result['stu_id']]['stu_name'],
            'stu_no' => $studentInfo[$result['stu_id']]['stu_no'],
            'scores' => $result['scores']
        );
        $this->result = $results;
        $this->exs_id = $exs_id;
        $this->display();
    }

    public function exam_edit_handle()
    {
        $exs_id = I('post.exs_id');
        $scores = I('post.scores');
        $data['scores'] = $scores;
        $data['exs_id'] = $exs_id;
        $where['exs_id'] = $exs_id;
        $ExamResults = D('ExamResults');
        if (!$ExamResults->create($data)) {
            $this->error($ExamResults->getError());
        } else {
            $ret = $ExamResults->where($where)->save($data);
        }
        $content = "编辑学生成绩";
        $state = $ret > 0 ? 1 : 2;
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, $state);
        $this->success('编辑学生成绩成功', U('/Admin/Parent/Results/exam_result'));
    }

    public function exam_del()
    {
        $uIds = I('post.exs_id');
        $arrUids = explode(',', $uIds);
        if (empty($arrUids)) {
            $this->error('参数错误');
        }
        $ExamResult = D('ExamResults');
        $okCount = 0;
        foreach ($arrUids as $k => $v) {
            $where['exs_id'] = $v;
            $ret = $ExamResult->where($where)->delete();
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

    //单考-成绩导入
    public function import2()
    {
        $s_id = I('get.s_id');
        $a_id = I('get.a_id');
        $g_id = I('get.g_id');
        $c_id = I('get.c_id');
        $ex_id = I('get.ex_id');// 考试
        $crs_id = I('get.crs_id');// 学科
        $file_url = I('post.file_url');
        if (empty($file_url)) {
            $this->error('参数错误');
            # code...
        }
        $file_url = $_SERVER['DOCUMENT_ROOT'] . $file_url;
        // var_dump($file_url);
        $arrField = array('学生学号' => 'stu_no', '学生姓名' => 'stu_name', '成绩' => 'scores');
        // var_dump($arrField);
        $arrList = import_excel($arrField, $file_url);
        if ($arrList == false) {
            $this->error('表格不能为空');
            # code...
        }
        // echo "<pre>";
        // var_dump($arrList);
        unlink($file_url);
        if (count($arrList) > 500) {
            D('SystemLog')->writeLog($this->getModule(), '导入数据不能超过500条', $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
            $this->error('导入数据不能超过500条');
        }
        $ExamResults = D('ExamResults');// 学生表
        //检查数据有效性
        foreach ($arrList as $key => $value) {
            $value['stu_no'] = 0;
            if (!$ExamResults->create($value)) {
                $key += 2;
                D('SystemLog')->writeLog($this->getModule(), '成绩导入失败,第' . $key . '行数据错误:' . $ExamResults->getError(), $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                $this->error('第' . $key . '行数据错误:' . $ExamResults->getError());
            }
        }
        //检查学生是否重复
        $stuList = array();
        foreach ($arrList as $value) {
            $stuList[$value['stu_no']] = 1;
        }
        if (count($stuList) < count($arrList)) {
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
        }

        //插入数据
        // var_dump($arrList);
        $data['a_id'] = $a_id;   // 校区ID
        $data['s_id'] = $s_id;   // 学校ID
        $data['g_id'] = $g_id;   // 年级ID
        $data['c_id'] = $c_id;   // 班级ID
        $data['ex_id'] = $ex_id;   // 考试ID
        $data['crs_id'] = $crs_id;   // 学科ID
        foreach ($arrList as $value) {
            $where['stu_no'] = $value['stu_no'];
            $where['stu_name'] = $value['stu_name'];
            $where['g_id'] = $value['g_id'];
            $stuInfo = D('Student')->where($where)->find();
            if (empty($stuInfo)) {
                $this->error("输入的学生或学号不存在");
            }
            $data['stu_id'] = $stuInfo['stu_id']; // stu_id
            $data['scores'] = $value['scores']; // 成绩
            $data['create_time'] = date('Y-m-d H:i:s');
            $res1 = $ExamResults->add($data);
            $content = '导入成功';
            if ($res1 === false) {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
                $this->error($Teacher->getError());
            } else {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                $this->success($content, U('/Admin/Parent/Result/index'));
            }
        }
    }

    //统考名称
    public function get_examAll()
    {
        $s_id = I('get.s_id');
        $a_id = I('get.a_id');
        $g_id = I('get.g_id');
        $stu_id = I('get.stu_id');
        if (!empty($s_id)) {
            $where['s_id'] = $s_id;
        }
        if (!empty($a_id)) {
            $where['a_id'] = $a_id;
        }
        if (!empty($g_id)) {
            $where['g_id'] = $g_id;
        }
        if (!empty($stu_id)) {
            $classList = $this->getStudentClass($stu_id);
            $where['g_id'] = array('IN', $classList['g_id']);
            $where['c_id'] = array('IN', $classList['c_id']);
        }
        // var_dump($where);die;
        $result = D('ExamAll')->where($where)->getField('e_id,start_date,name', '-');
        $this->success($result);
    }

    //统考科目
    public function course()
    {
        $e_id = I('get.e_id');
        if (!empty($e_id)) {
            $where['e_id'] = $e_id;
        }
        $courseList = D('Course')->getField('crs_id,name');
        $result = D('ExamAllSub')->where($where)->select();
        $arr = array();
        foreach ($result as $key => $value) {
            $arr[$value['crs_id']] = $courseList[$value['crs_id']];
        }

        // var_dump($result);
        $this->success($arr);
    }


    //单考科目
    public function course2()
    {

        $ex_id = I('get.ex_id');
        if (empty($ex_id)) {
            $this->error("参数错误");
        }
        $where['ex_id'] = $ex_id;
        $courseList = D('Course')->getField('crs_id,name');
        $result = D('Exam')->where($where)->select();
        foreach ($result as $key => $value) {
            $arr[$value['crs_id']] = $courseList[$value['crs_id']];
        }

        // var_dump($result);
        $this->success($arr);

    }


    //单考名称
    public function get_exam()
    {
        $s_id = I('get.s_id');
        $a_id = I('get.a_id');
        $g_id = I('get.g_id');
        $c_id = I('get.c_id');
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
        $result = D('Exam')->where($where)->getField('ex_id,start_time,name', '-');
        $this->success($result);
    }
}