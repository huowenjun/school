<?php
/*
*通讯录
*by ruiping date 20161201
*/

namespace Api\Controller;

use Api\Controller\BaseController;
use Think\Model;

class ContactsController extends BaseController
{
    /*
    *数据查询
    *@param
    */
    public function query()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
//            $this->error('接口访问不合法！');
        }
        $type = $this->getUserType();
        $user_id = $this->getUserId();

        //类型为老师的时候显示学生家长联系方式
        if ($type == 3) {
            //查询班级学生
            $Teacher = D('Teacher');
            $tId = $Teacher->where(array('u_id' => $user_id))->getField("t_id");
            if (empty($tId)) {
                $this->error('教师档案不存在！');
            }

            $classInfo = $this->getTeacherClass($tId);
            if (empty($classInfo)) {
                $this->error('该教师没有负责的班级！');
            }
            $gId = $classInfo['g_id'];
            $cId = $classInfo['c_id'];

            $where1['g_id'] = array('IN', $gId);
            $where1['c_id'] = array('IN', $cId);

            $student = D('Student')->where($where1)->Field('g_id,c_id,stu_id,sex,birth_date,stu_name')->select();
            foreach ($student as $key => $value) {
                $age = 0;
                if (!empty($value['birth_date'])) {
                    $age = get_birthday($value['birth_date']);
                }
                $student[$key]['age'] = $age;
                $student[$key]['parent'] = $this->get_parent_phone($value['stu_id']);
                $student[$key]['g_name'] = $this->get_grade_name($value['g_id'])[$value['g_id']];
                $student[$key]['c_name'] = $this->get_class_name($value['c_id'])[$value['c_id']];
            }

            $s_id = I('post.s_id');
            $a_id = I('post.a_id');
            if (empty($s_id) || empty($a_id)) {
                $this->error('参数不正确！');
            }
            $where['s_id'] = $s_id;
            $where['a_id'] = $a_id;
            //教师通讯录
            $teacherPhone = $Teacher->where($where)->Field('t_id,s_id,a_id,name,sex,phone')->select();
            foreach ($teacherPhone as $key => $value) {
                $teacherPhone[$key]['school_name'] = $this->get_school_name($value['s_id'])[$value['s_id']];
                $teacherPhone[$key]['area_name'] = $this->get_area_name($value['a_id'])[$value['a_id']];
                $teacherPhone[$key]['crsClass'] = $this->get_teacher_course($value['t_id']);
            }

            $arr['teacher'] = $teacherPhone;
            $arr['student'] = $student;
        } elseif ($type == 4) {
            //家长查看通讯录调用另一接口
            //getContactsForParent

        } else {
            $this->error('用户类型不正确！');
        }

        $this->success($arr);
    }

    //教师端获取通讯录
    public function getContactsForTeacher()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
//            $this->error('接口访问不合法！');
        }
        $type = $this->getUserType();
        $user_id = $this->getUserId();

        //类型为老师的时候显示学生家长联系方式
        if ($type == 3) {
            //查询班级学生
            $Teacher = D('Teacher');
            $tId = $Teacher->where(array('u_id' => $user_id))->getField("t_id");
            if (empty($tId)) {
                $this->error('教师档案不存在！');
            }

            $classInfo = $this->getTeacherClass($tId);
            if (empty($classInfo)) {
                $this->error('该教师没有负责的班级！');
            }
            $gId = $classInfo['g_id'];
            $cId = $classInfo['c_id'];
            $where['a_id'] = 0;

            $where1['g_id'] = array('IN', $gId);
            $where1['c_id'] = array('IN', $cId);
            $student = D('Student')->where($where1)->Field('g_id,c_id,stu_id,sex,birth_date,stu_name')->select();

            foreach ($student as $key => $value) {
                $age = 0;
                if (!empty($value['birth_date'])) {
                    $age = get_birthday($value['birth_date']);
                }
                $student[$key]['age'] = $age;
                $student[$key]['parent'] = $this->get_parent_phone($value['stu_id']);
                $student[$key]['g_name'] = $this->get_grade_name($value['g_id'])[$value['g_id']];
                $student[$key]['c_name'] = $this->get_class_name($value['c_id'])[$value['c_id']];
            }

            $s_id = I('post.s_id');
            $a_id = I('post.a_id');
            if (empty($s_id) || empty($a_id)) {
                $this->error('参数不正确！');
            }
            $where['s_id'] = $s_id;
            $where['a_id'] = $a_id;
            $where['valid'] = '0';
            //教师通讯录
            $teacherPhone = $Teacher->where($where)->Field('t_id,s_id,a_id,name,sex,phone,u_id,charge,email,d_id,education')->select();
            $EDUCATION = C('EDUCATION');
            foreach ($teacherPhone as $key => $value) {
                $teacherPhone[$key]['s_name'] = $this->get_school_name($value['s_id'])[$value['s_id']];
                $teacherPhone[$key]['a_name'] = $this->get_area_name($value['a_id'])[$value['a_id']];
//                $teacherPhone[$key]['crsClass'] = $this->get_teacher_course($value['t_id']);
                $teacherPhone[$key]['d_name'] = $this->get_dept_name($value['a_id'])[$value['d_id']];
                $teacherPhone[$key]['g_id'] = $this->getTeacherClass($value['t_id'])['g_id'];
                $teacherPhone[$key]['c_id'] = $this->getTeacherClass($value['t_id'])['c_id'];
                $teacherPhone[$key]['education'] = $EDUCATION[$value['education']];
            }

            $arr['teacher'] = $teacherPhone;
            $arr['student'] = $student;
        } elseif ($type == 4) {
            //家长查看通讯录调用另一接口
            //getContactsForParent

        } else {
            $this->error('用户类型不正确！');
        }

        $this->success($arr);
    }


    //家长端获取通讯录
    function getContactsForParent()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
//            $this->error('接口访问不合法！');
        }
        $user_id = $this->getUserId();

        $student = D('Student');
        //获取相关的班级信息
        $class = $student->getStuClassByParentUserId($user_id);

        $g_ids = array();
        $c_ids = array();
        $a_ids = array();
        $s_ids = array();
        foreach ($class as $key => $value) {
            $g_ids[$key] = $value['g_id'];
            $c_ids[$key] = $value['c_id'];
            $s_ids[$key] = $value['s_id'];
            $a_ids[$key] = $value['a_id'];
        }
        if (empty($s_ids)) {
            $s_ids = '';
        }
        if (empty($a_ids)) {
            $a_ids = '';
        }
        if (empty($g_ids)) {
            $g_ids = '';
        }
        if (empty($c_ids)) {
            $c_ids = '';
        }
        $ret['school'] = D('SchoolInformation')->getSchoolInfoBySidAids($s_ids, $a_ids);

        $tcourse = D('TeacherCourse');
        $teacher = $tcourse->getTeacherByGidCid($g_ids, $c_ids);

        $EDUCATION = C('EDUCATION');
        foreach ($teacher as $key => $value) {
            $teacher[$key]['education'] = $EDUCATION[$value['education']];
        }
        $ret['teacher'] = $teacher;
        $this->success($ret);

    }


}