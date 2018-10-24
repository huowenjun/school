<?php
/*
*考试成绩查询
*by ruiping date 20161117
*/
namespace Api\Controller;
use Api\Controller\BaseController;
use Think\Model;
class ExamController extends BaseController {
    /*
    *数据查询
    *@param
    */
    public function query(){
        if(IS_POST===false){
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/Exam/query'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
    	//校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag){
            $this->error('接口访问不合法！');
        }
        $type = I('post.type'); //类型 1 单考 2统考
        if (empty($type)) {
            $this->error('选择考试类型！');
        }
        $e_id = I('post.e_id');
        $stu_id = I('post.stu_id');
        if (empty($e_id)) {
            $this->error('请选择考试！');
        }
        $examResults = D('ExamResults');
        $examSubResults = D('ExamAllResults');
        $Exam = D('ExamAll');
        if ($type==1) {
            if ($this->getUserType()==4) {//家长
                if (!empty($stu_id)) {
                    $where['stu_id'] = $stu_id;
                }else{
                    $studentAccess = D('StudentGroupAccess')->where(array('sp_id'=>$this->getUserId()))->select();
                    $stuId = array();
                    foreach ($studentAccess as $key => $value) {
                        $stuId[] = $value['stu_id'];
                    }
                    $where['stu_id'] = array('IN',implode(',',$stuId));
                }
            }elseif ($this->getUserType()==3) {
                $where['ex_id'] = $e_id;
            }else{
                $this->error('用户类型不正确！');
            }
            
            $examResultsList = $examResults->Field('stu_id,crs_id,scores')->where($where)->select();
            foreach ($examResultsList as $key => $value) {
                $examResultsList[$key]['stu_name'] = $this->get_stu_name($value['stu_id'])[$value['stu_id']]['stu_name'];
                $examResultsList[$key]['stu_no'] = $this->get_stu_name($value['stu_id'])[$value['stu_id']]['stu_no'];
                $examResultsList[$key]['crs_name'] = $this->get_course_name($value['crs_id'])[$value['crs_id']];
            }
        }elseif ($type==2) {
            if ($this->getUserType()==4) {//家长
                if (!empty($stu_id)) {
                    $where['c.stu_id'] = $stu_id;
                }else{
                    $studentAccess = D('StudentGroupAccess')->where(array('sp_id'=>$this->getUserId()))->select();
                    $stuId = array();
                    foreach ($studentAccess as $key => $value) {
                        $stuId[] = $value['stu_id'];
                    }
                    $where['c.stu_id'] = array('IN',implode(',',$stuId));
                }
            }elseif ($this->getUserType()==3) {
                $where['a.e_id'] = $e_id;
                $where['c.g_id'] = I('post.g_id');
                $where['c.c_id'] = I('post.c_id');
            }else{
                $this->error('用户类型不正确！');
            }
            $groupby = "c.stu_name,e.name desc";
            $result = $Exam->queryListR('c.c_id,c.stu_id,c.stu_no,c.stu_name,e.name,IFNULL(d.scores,0) as scores',$where,$order,0,'',$groupby);
            
            $examResultsList1 = array();
            $examResultsList_km=array();
            foreach ($result['list'] as $k => $v) {
               $ts=$v['c_id'].$v['stu_name'];
               $examResultsList1[$ts][$v['name']]=$v['scores'];
               $examResultsList1[$ts]['sum']=$v['scores']+$examResultsList1[$ts]['sum'];
               $examResultsList1[$ts]['name']=$v['stu_name'];
               $examResultsList1[$ts]['stu_id']=$v['stu_id'];
               $examResultsList1[$ts]['c_id']=$v['c_id'];
               if (!empty($v['name'])) {
                   $examResultsList_km[$v['name']]=$v['name'];
               }
            }

            $courseName = array();

            foreach ($examResultsList_km as $key => $value) {
                $courseName[][$key] = $value;
            }

            //$examResultsList1['km']['km'] = $examResultsList_km;
            if (count($courseName)!=0) {
                 rsort($examResultsList1);
            }else{
                $examResultsList1 = array();
            }
            
            $examResultsList['result'] = $examResultsList1;
            $examResultsList['course'] = $courseName;
        }else{
            $this->error('请选择考试类型！');
        }
        $this->success($examResultsList);
    }


    public function getScoresForParent(){

        if(IS_POST===false){
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        $flag = $this->checkToken($token);
        if (!$flag){
            $this->error('接口访问不合法');
        }
        $stu_id = I('post.stu_id/d','');
        $dates = I('post.dates');

        if (empty($stu_id)||empty($dates)) {
            $this->error('参数不正确！');
        }

        // var_dump($stu_id);die();
        //统考成绩
        $ExamAll = D('ExamAllResults');
        $allScores = $ExamAll->queryScoreByStuId($stu_id,$dates);
        //单考成绩
        $Exam = D('ExamResults');
        $singleScores = $Exam->queryScoreByStuId($stu_id,$dates);
        //总成绩
        $scores = array_merge($singleScores,$allScores);
        //排序
        usort($scores, function($a, $b) {
            $atime = strtotime($a['start_time']);
            $btime = strtotime($b['start_time']);
            
            if ($atime-$btime == 0)
                return 0;
            return $atime-$btime>0 ? -1 : 1;
        });

        $tempScores = array();
        $EXAM_TYPE = C('EXAM_TYPE');
        foreach ($scores as $key => $value) {
            # code...
            if ($value['exam_com_type']=='1') {
                $tempScores[$key]['exam_com_type'] = '统考';    
            }elseif ($value['exam_com_type']=='0') {
                $tempScores[$key]['exam_com_type'] = '单考';
            }
            $tempScores[$key]['exam_type'] = $EXAM_TYPE[$value['exam_type']];
            $tempScores[$key]['exam_name'] = $value['exam_name'];
            $tempScores[$key]['start_time'] = $value['start_time'];
            $tempScores[$key]['end_time'] = $value['end_time'];

            $courseNames = explode(',',$value['course_name']);
            $courseScores = explode(',',$value['scores']);

            $mScores = array();
            foreach ($courseNames as $j => $value) {
                $mScores[$j]['course_name'] = $value;
                $mScores[$j]['course_score'] = $courseScores[$j];
                //默认总分为100分
                $mScores[$j]['course_score_total'] = '100';
            }
            $tempScores[$key]['scores'] = $mScores;
        }

        // var_dump($scores);die();
        $this->success($tempScores);
        //$this->success(array_merge($allScores,$singleScores));
    }


}