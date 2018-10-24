<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-考试管理
 */
namespace Admin\Controller\Parent;

use Admin\Controller\BaseController;

class ExaminationController extends BaseController
{
    /**
     *统考
     */
    public function detail()
    {
        // 考试详情-考试时间
        $e_id = I('get.e_id');
        $sub = D('ExamAllSub')->where(array('e_id' => $e_id))->select();
        // var_dump($sub);
        foreach ($sub as $val) {
            $ss = D('Course')->where(array('crs_id' => $val['crs_id']))->getField('name');
            $val['crs_id'] = $ss;
            $subInfo[] = $val;
        }
        // var_dump($subInfo);
        //考试详情-录入状态
        $Exam = D('ExamAll');
        $where1['a.e_id'] = $e_id;

        $groupby = "a.e_id,a.g_id,c.c_id,b.crs_id,c.name";
        $result = $Exam->queryListEx1('a.e_id,a.g_id,c.c_id,b.crs_id,c.name,count(d.er_id) cnt', $where1, $order, 0, '', $groupby);
        //dump($result['list']);
        $arr = "";
        foreach ($result['list'] as $key => $value) {
            $arr[$value['name']][$value['crs_id']]['cnt'] = $value['cnt'];
        }
        // dump($arr);
        $this->arr = $arr;
        $this->subInfo = $subInfo;
        $this->display();
    }

    public function index()
    {
        // 科目
        $sId = I('post.s_id');
        $aId = I('post.a_id');
        $gId = I('post.g_id');
        $e_id = I('post.e_id/d');
        if (!empty($sId)) {
            $where['s_id'] = $sId;
        }
        if (!empty($aId)) {
            $where['a_id'] = $aId;
        }
        if (!empty($gId)) {
            $where['g_id'] = $gId;
        }
        $where1['e_id'] = $e_id;
        $courseInfo = D('Course')->where($where)->getField('crs_id,name');
        $examallSub = D('ExamAllSub')->Field('crs_id,start_time,end_time')->where($where1)->select();
        $this->courseInfo = $courseInfo;
        $this->examallSub = $examallSub;
        $this->display();
    }

    // 统考-查询
    public function index_query()
    {
        //权限
        $ids = $this->authRule();
        $where = "";
        if ($this->getType() == 1) {
            $where['s_id'] = $ids['s_id'];
        } elseif ($this->getType() == 3) {
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
            $classList = $this->getTeacherClass($ids['t_id']);
            $where['g_id'] = array('IN', $classList['g_id']);
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

        $stime = I('get.start_date');// 开始时间
        $etime = I('get.end_date');// 结束时间
        $examName = I('get.name');// 统考名称
        $ExamAll = D('ExamAll');
        $sId = I('get.s_id');
        $aId = I('get.a_id');
        $gId = I('get.g_id');
        $cId = I('get.c_id');
        $page = I('get.page');
        $pagesize = I('get.pagesize', $this->PAGE_SIZE);
        $sort = I('get.sort', 'e_id');
        $order = $sort . ' ' . I('get.order', 'desc');
        if (!empty($stime) && !empty($etime)) {
            $where['start_date'] = array("BETWEEN", array($stime, $etime));
        } elseif (!empty($stime)) {
            $where['start_date'] = array("EGT", $stime);
        } elseif (!empty($etime)) {
            $where['start_date'] = array("ELT", $etime);
        }

        if (!empty($sId)) {
            $where['s_id'] = $sId;
        }
        if (!empty($aId)) {
            $where['a_id'] = $aId;
        }
        if (!empty($gId)) {
            $where['g_id'] = $gId;
        }
        if (!empty($cId)) {
            $where['c_id'] = $cId;
        }
        if ($examName != '') {
            $where = "name like '%" . $examName . "%'";
        }
        // var_dump($where);
        $result = $ExamAll->queryListEx('*', $where, $order, $page, $pagesize, '');
        $this->success($result);

    }

    // 统考-添加
    public function examAll_add()
    {
        $gId = I('get.g_id');
        if (!empty($gId)) {
            $where['g_id'] = $gId;
        }
        $courseInfo = D('Course')->where($where)->getField('crs_id,name');
        $this->courseInfo = $courseInfo;
        $this->display();
    }

    public function examAll_add_handle()
    {
        $ExamAll = D('ExamAll');
        $data['s_id'] = I('post.s_id');
        $data['a_id'] = I('post.a_id');
        $data['g_id'] = I('post.g_id');
        $data['name'] = I('post.name');
        $data['exam_type'] = I('post.exam_type');
        $data['start_date'] = I('post.start_date');
        $data['end_date'] = I('post.end_date');
        $data['create_time'] = date('Y-m-d H:i:s');

        $ExamAllSub = D('ExamAllSub');
        $crs_id = I('post.crs_id');
        $start_time = I('post.start_time');
        $end_time = I('post.end_time');
        
//验证考试时间冲突start
        for ($i = 0; $i < count($start_time); $i++){
            $stime[$i] = strtotime($start_time[$i]);//开始时间转化时间戳
        }

        for ($i = 0; $i < count($end_time); $i++){
            $etime[$i] = strtotime($end_time[$i]);//结束时间转化时间戳
        }

        $len=count($stime);
        for($i=1;$i<$len;$i++) {
            for($k=0;$k<$len-$i;$k++) {
                if($stime[$k]>$stime[$k+1]) {
                    $tmp=$stime[$k+1];
                    $tmp1=$etime[$k+1];
                    $stime[$k+1]=$stime[$k];
                    $etime[$k+1]=$etime[$k];
                    $etime[$k]=$tmp1;
                    $stime[$k]=$tmp;
                }
            }
        }

        $stm[0] = $stime[0];
        $etm[0] = $etime[0];
        for($i=1;$i<$len;$i++){
            if ($etm[0]<$stime[$i] || $stm[0]>$etime[$i]){
                if ($stime[$i]<$etime[0] && $etime[$i]>$etime[0]){
                    $this->error('考试时间冲突!');
                }
                if ($etm[0]<$stime[$i]){
                    $etm[0] = $etm[0] + ($etime[$i]-$stime[$i]);
                }
                if ($stm[0]>$etime[$i]){
                    $stm[0] = $stm[0] - ($stime[$i]-$etime[$i]);
                }
            }else{
                $this->error('考试时间冲突!');
            }
        }

//验证考试时间冲突end

        $data['user_id'] = $this->getUserId();
        if (!$data['s_id'] || !$data['a_id']) {
            $schoolInfo = M("Grade")->field("s_id ,a_id")->where(" g_id = '{$data['g_id']}' ")->find();
            $data['s_id'] = $schoolInfo['s_id'];
            $data['a_id'] = $schoolInfo['a_id'];
        }

        if (!$ExamAll->create($data)) {
            $this->error($ExamAll->getError());
        } else {
            $ExamAll->startTrans();
            $ret = $ExamAll->add($data);
            $arr1 = array();
            foreach ($start_time as $i => $d) {
                if ($d == 0) continue;//if($d!=0){$arr[]=$d}
                $arr1[] = $d;
            }
            $arr2 = array();
            foreach ($end_time as $i => $d) {
                if ($d == 0) continue;//if($d!=0){$arr[]=$d}
                $arr2[] = $d;;
            }
            if (empty($arr1) || empty($arr2)) {
                $this->error('请选择 学科/时间');
            }
            $status = '';
            for ($i = 0; $i < count($arr1); $i++) {

                $data1['start_time'] = $arr1[$i];
                $data1['end_time'] = $arr2[$i];
                $data1['crs_id'] = $crs_id[$i];
                $data1['e_id'] = $ret;//添加考试呢条数据的id
                $result = $ExamAllSub->add($data1);
                if ($result) {
                    $ExamAllSub->commit();
                } else {
                    $ExamAllSub->rollback();
                }
            }

        }
        $content = '添加成功!';
        $state = $ret > 0 ? 1 : 2;
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, $state);
        $this->success($content, U('/Admin/Parent/Examination/index'));
    }

    // 统考-xiugai
    public function examAll_edit()
    {
        $s_id = I('get.s_id');
        $a_id = I('get.a_id');
        $g_id = I('get.g_id');
        $e_id = I('get.e_id');
        if (!empty($s_id)) {
            $where['s_id'] = $s_id;
        }
        if (!empty($a_id)) {
            $where['a_id'] = $a_id;
        }
        if (!empty($g_id)) {
            $where['g_id'] = $g_id;
        }
        $where1['e_id'] = $e_id;
        $examallSub = D('ExamAllSub')->where($where1)->getField('crs_id,start_time,end_time');
        $examall = D('ExamAll')->where($where1)->find();
        $courseInfo = D('Course')->where($where)->select();
        $arr = array();
        foreach ($courseInfo as $key => $value) {
            $arr[$key]['crs_id'] = $value['crs_id'];
            $arr[$key]['name'] = $value['name'];
            if ($value['crs_id'] == $examallSub[$value['crs_id']]['crs_id']) {
                $check = "checked";
                $sdate = $examallSub[$value['crs_id']]['start_time'];
                $edate = $examallSub[$value['crs_id']]['end_time'];
            } else {
                $check = "";
                $sdate = "";
                $edate = "";
            }
            $arr[$key]['check'] = $check;
            $arr[$key]['sdate'] = $sdate;
            $arr[$key]['edate'] = $edate;
        }
        $this->courseInfo = $arr;
        $this->examall = $examall;
        $this->examType = C('EXAM_TYPE');
        $this->display();
    }

    public function examAll_edit_handle()
    {
        $ExamAll = D('ExamAll');
        $id = I('post.e_id/d');
        $data['e_id'] = $id;
        $data['s_id'] = I('post.s_id');
        $data['a_id'] = I('post.a_id');
        $data['g_id'] = I('post.g_id');
        $data['name'] = I('post.name');
        $data['exam_type'] = I('post.exam_type');
        $data['start_date'] = I('post.start_date');
        $data['end_date'] = I('post.end_date');
        $ExamAllSub = D('ExamAllSub');
        $crs_id = I('post.crs_id');
        $start_time = I('post.start_time');
        $end_time = I('post.end_time');

        $arr1 = array();
        foreach ($start_time as $k => $v) {
            if ($v == 0) continue;//if($d!=0){$arr[]=$d}
            $arr1[] = $v;
        }
        $arr2 = array();
        foreach ($end_time as $key => $value) {
            if ($value == 0) continue;//if($d!=0){$arr[]=$d}
            $arr2[] = $value;
        }
        if (empty($arr1) || empty($arr2)) {
            $this->error('请选择 学科/时间');
        }


        $es_id = I('post.es_id');
        if (!$ExamAll->create($data)) {
            $this->error($ExamAll->getError());
        } else {
            $ret = $ExamAll->save($data);
            if (!$ret) {
                $ExamAllSub->where(array('e_id' => $id))->delete();
                for ($i = 0; $i < count($arr1); $i++) {
                    $data1['start_time'] = $arr1[$i];
                    $data1['end_time'] = $arr2[$i];
                    $data1['crs_id'] = $crs_id[$i];
                    $data1['e_id'] = $id;//添加考试呢条数据的id
                    $result = $ExamAllSub->add($data1);
                    // if($result){
                    // $ExamAllSub->commit();
                    // }else{
                    // $ExamAllSub->rollback();
                    // }
                }
                // for ($i=0; $i < count($crs_id); $i++) { 
                //     $data1['crs_id'] = $crs_id[$i];
                //     $data1['start_time'] = $start_time[$i];
                //     $data1['end_time'] = $end_time[$i];
                //     $ExamAllSub->where(array('e_id' =>$id))->save($data1);
                // } 
            }
        }
        $content = '编辑成功!';
        $state = $ret > 0 ? 1 : 2;
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, $state);
        $this->success($content, U('/Admin/Parent/Examination/index'));
    }


    //统考-删除
    public function index_del()
    {
        $uIds = I('post.id');
        $ExamAll = D('ExamAll');
        $ExamAllSub = D('ExamAllSub');
        $arrUids = explode(',', $uIds);
        if (empty($arrUids)) {
            $this->error('参数错误');
        }
        $okCount = 0;
        foreach ($arrUids as $k => $v) {
            $where['e_id'] = $v;
            $result = D('ExamAllResults')->where($where)->select();
            if (!empty($result)) {
                $this->error('本次考试成绩已录入,无法删除');
            } else {
                $ret = $ExamAll->where($where)->delete();
                $ExamAllSub->where($where)->delete();
            }
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

    /**
     *单考
     */
    // 单考-查询
    public function exam_query()
    {
        //权限
        $ids = $this->authRule();
        $where = "";
        if ($this->getType() == 1) {
            $where['s_id'] = $ids['s_id'];
        } elseif ($this->getType() == 3) {
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
            $classList = $this->getTeacherClass($ids['t_id']);
            $where['g_id'] = array('IN', $classList['g_id']);
            $where['c_id'] = array('IN', $classList['c_id']);
        } elseif ($this->getType() == 4) {
            $where['s_id'] = $sId;
            $where['a_id'] = $aId;
            $where['stu_id'] = array('IN', $ids['stu_id']);
        } else {

        }
        $region_id = I('get.region_id');
        if (!empty($region_id)) {
            # code...
            $sthInfo = D('SchoolInformation')->where(array("region_id" => $region_id))->getField('s_id', true);
            foreach ($sthInfo as $key => $value) {
                $s_id = $s_id . $value . ",";
            }
            $where['s_id'] = array('IN', rtrim($s_id, ','));
        }
        $crsname = I('get.crs_id');// 科目名称
        $stime = I('get.start_time');// 开始时间
        $etime = I('get.end_time');// 结束时间
        $examName = I('get.name');// 单考名称
        $sId = I('get.s_id', $ids['s_id']);
        $aId = I('get.a_id', $ids['a_id']);
        $gId = I('get.g_id', $ids['g_id']);
        $cId = I('get.c_id', $ids['c_id']);
        $Exam = D('Exam');
        $page = I('get.page');
        $pagesize = I('get.$pagesize', 10);
        $sort = I('get.sort', 'ex_id');
        $order = $sort . ' ' . I('get.order', 'desc');
        if ($crsname != '') {
            $where1 = "name like '%" . $crsname . "%'";
            $crsInfo = D('Course')->where($where1)->select();
            $crs_id = "";
            if (empty($crsInfo)) {
                $this->error('输入的学科不存在！');
            } else {
                foreach ($crsInfo as $key => $value) {
                    $crs_id = $crs_id . $value['crs_id'] . ",";
                }
            }
            $where['crs_id'] = array('IN', rtrim($crs_id, ','));
        }
        if ($examName != '') {
            $where = "name like '%" . $examName . "%'";
        }
        if (!empty($stime)) {
            $where['start_time'] = array("EGT", $stime . ' 00:00:00');
        }
        if (!empty($etime)) {
            $where['end_time'] = array("ELT", $etime . ' 23:59:59');
        }
        if (!empty($sId)) {
            $where['s_id'] = $sId;
        }
        if (!empty($aId)) {
            $where['a_id'] = $aId;
        }
        if (!empty($gId)) {
            $where['g_id'] = $gId;
        }
        if (!empty($cId)) {
            $where['c_id'] = $cId;
        }
        $result = $Exam->queryListEx('*', $where, $order, $page, $pagesize, '');
        // var_dump($result);die;
        $this->success($result);
    }

    public function exam_get()
    {
        $ex_id = I('get.ex_id/d', 0);
        $where['ex_id'] = array('EQ', $ex_id);
        $Exam = D('Exam');
        $examInfo = $Exam->where($where)->find();
        $arr = array();
        $arr['ex_id'] = $examInfo['ex_id'];
        $arr['c_id'] = $examInfo['c_id'];
        $arr['input_state'] = $examInfo['input_state'];
        $arr['exam_site2'] = $examInfo['exam_site'];
        $arr['memo2'] = $examInfo['memo'];
        $arr['g_id2'] = $examInfo['g_id'];
        $arr['name2'] = $examInfo['name'];
        $arr['crs_id2'] = $examInfo['crs_id'];
        $arr['a_id2'] = $examInfo['a_id'];
        $arr['s_id2'] = $examInfo['s_id'];
        $arr['exam_type2'] = $examInfo['exam_type'];
        $arr['start_time2'] = $examInfo['start_time'];
        $arr['end_time2'] = $examInfo['end_time'];
        // $exam_info = $Exam->getInfo($ex_id);
        if ($ex_id > 0 && empty($examInfo)) {
            $this->error('搜索不存在');
        }
        $this->success($arr);
    }

    //单考-新增/编辑
    public function exam_edit()
    {
        $ids = $this->authRule();
        $Exam = D('Exam');
        $id = I('post.ex_id/d', 0);
        $data['ex_id'] = $id;
        $data['s_id'] = I('post.s_id');
        $data['a_id'] = I('post.a_id');
        $data['g_id'] = I('post.g_id');
        $data['c_id'] = I('post.c_id');
        $data['crs_id'] = I('post.crs_id');
        $data['name'] = I('post.name');
        $data['exam_type'] = I('post.exam_type');
        $data['exam_site'] = I('post.exam_site');
        $data['memo'] = I('post.memo');
        $data['start_time'] = I('post.start_time');
        $data['end_time'] = I('post.end_time');
        $data['user_id'] = $this->getUserId();
        if ($this->getType() == 1) {// 学校管理员
            $data['s_id'] = $ids['s_id'];
        } elseif ($this->getType() == 3) {// 老师
            $data['s_id'] = $ids['s_id'];
            $data['a_id'] = $ids['a_id'];
        } elseif ($this->getType() == 4) {// 家长
            $this->error('对不起,您没有权限');
        } else {

        }
        if (!$Exam->create($data)) {
            $this->error($Exam->getError());
        } else {
            if ($id > 0) {
                $ret = $Exam->save($data);
            } else {
                $data['create_time'] = date('Y-m-d H:i:s');
                $ret = $Exam->add($data);
            }
            $content = ($id > 0 ? '编辑' : '新建') . '成功';
            if ($ret === false) {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                $this->error($Exam->getError());
            } else {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                $this->success($content, U('/Admin/Parent/Examaction/exam'));
            }
        }
    }

    //单考-删除
    public function exam_del()
    {
        $uIds = I('post.id');
        $arrUids = explode(',', $uIds);
        if (empty($arrUids)) {
            $this->error('参数错误');
        }
        $Exam = D('Exam');
        $okCount = 0;
        foreach ($arrUids as $k => $v) {
            $where['ex_id'] = $v;
            $result = D('ExamResults')->where($where)->select();
            if (!empty($result)) {
                $this->error('本次考试成绩已录入,无法删除');
            } else {
                $ret = $Exam->where($where)->delete();
            }
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