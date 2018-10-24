<?php
/*
*我的学生查询
*by ruiping date 20161201
*/
namespace Api\Controller;
use Api\Controller\BaseController;
use Think\Model;
class MyStudentController extends BaseController {
    /*
    *数据查询
    *@param
    */
    public function query(){
        if(IS_POST===false){
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/MyStudent/query'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
    	//校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag){
            $this->error('接口访问不合法！');
        }
        $c_id = I('post.c_id');
        $g_id = I('post.g_id');
        if (empty($c_id) || empty($g_id)) {
            $this->error('参数错误！');
        }
        $where['c_id'] = array('EQ',$c_id);
        $where['g_id'] = array('EQ',$g_id);
        $studentInfo = D('Student')->Field('stu_id,stu_name,stu_no,sex')->where($where)->select();

        $this->success($studentInfo);
    }

    //学生详情
    public function view(){
        if(IS_POST===false){
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/MyStudent/view'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag){
            $this->error('接口访问不合法！');
        }
        $stu_id = I('post.stu_id');
        if (empty($stu_id)) {
            $this->error('参数错误！');
        }
        $where['stu_id'] = array('EQ',$stu_id);
        $studentInfo = D('Student')->Field('stu_id,stu_name,sex,g_id,c_id,birth_date')->where($where)->select();
        if (empty($studentInfo)) {
            $this->error('学生档案不存在！');
        }else{
            foreach ($studentInfo as $key => $value) {
                $studentInfo[$key]['stu_name'] = $value['stu_name'];
                $studentInfo[$key]['sex'] = $value['sex'];
                $studentInfo[$key]['g_name'] = $this->get_grade_name($value['g_id'])[$value['g_id']];
                $studentInfo[$key]['c_name'] = $this->get_class_name($value['c_id'])[$value['c_id']];
                $age = 0;
                if (!empty($value['birth_date'])) {
                    $age = get_birthday($value['birth_date']);
                }
                $studentInfo[$key]['age'] = $age;
                $studentInfo[$key]['parent_phone'] = $this->get_parent_phone($value['stu_id']);
            }
            
        }
        
        $this->success($studentInfo);
    }
}