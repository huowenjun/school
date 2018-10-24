<?php
/*
*公共下拉选项列表
*by ruiping date 20161115
*/
namespace Api\Controller;
use Api\Controller\BaseController;
use Think\Model;
class CommentController extends BaseController {
    public function get_class(){
        $Class = $this->getClass();
        if ($Class) {
            $this->success($Class);
        }else{
            $this->error('参数无效！');
        }
        
    }

    public function get_course(){
        $Class = $this->getCourse();
        if ($Class) {
            $this->success($Class);
        }else{
            $this->error('参数无效！');
        }
        
    }

    //请求考试名称
    public function get_exam_name(){
        $years = I('post.years');
        $g_id = I('post.g_id');
        $c_id = I('post.c_id');
        if (empty($g_id)) {
            $this->error('请选择年级');
        }
        $whereg = "g_id=".$g_id;
        if (!empty($years)) {
            $where1 = " and DATE_FORMAT(create_time,'%Y-%m') = '{$years}'";
        }
        
        
        if (!empty($c_id)) {
            $wherec = " and c_id=".$c_id;
        }
        
        $where = $whereg.$where1.$wherec;
        //dump($where);die;
        $userExam = D('Exam')->field("ex_id as e_id,concat('单考-[',DATE_FORMAT(create_time,'%m-%d'),']-',name) as name,concat('1') as type")
        ->union("select e_id,concat('统考-[',DATE_FORMAT(create_time,'%m-%d'),']-',name) as name,concat('2') as type from sch_exam_all where g_id=".$g_id.$where1)
        ->where($where)
        ->select();

        $this->success($userExam);
    }

    //老师列表
    public function get_teacher_list(){
        $g_id = I('post.g_id');
        $c_id = I('post.c_id');
        if (empty($g_id) || empty($c_id)) {
            $this->error('参数错误');
        }
        $where1['g_id'] = $g_id;
        $where1['c_id'] = $c_id;
        //班主任
        $adviser = D('Class')->Field('t_id')->where($where1)->find();
        //执教老师
        $courseClass = D('TeacherCourse')->Field('distinct t_id')->where($where1)->select();
        $tId = array();$crsName = array();
        $tId[] = $adviser['t_id'];
        foreach ($courseClass as $key => $value) {
            $tId[] = $value['t_id'];
        }
        $where['t_id'] = array('IN',implode(',',$tId));
        $teacherInfo = D('Teacher')->where($where)->Field('t_id,u_id,name,charge')->select();

        $arr = array();
        foreach ($teacherInfo as $key => $value) {
            $arr[$key]['t_id'] = $value['t_id'];
            $arr[$key]['u_id'] = $value['u_id'];
            $arr[$key]['name'] = $value['name'];
            $arr[$key]['charge'] = $value['charge'];
            //是否是班主任
            $is_head = M("Class")->where("t_id = '{$value['t_id']}' and c_id = '{$c_id}'")->find();
            if($is_head){
                $arr[$key]['charge'] = 1;
            }else{
                $arr[$key]['charge'] = 0;
            }
            //代理科目

            $res = M("TeacherCourse")->field("b.name")->alias('a')->join("__COURSE__ b ON a.crs_id = b.crs_id")->where("a.t_id = '{$value['t_id']}' and a.c_id = '{$c_id}' ")->select();
            $crs_arr = array();
            foreach($res as $v){
                $crs_arr[] = $v['name'];
            }

            $arr[$key]['crs_name'] = implode(',',$crs_arr);
        }
        $this->success($arr);
    }
    
}