<?php

namespace Common\Model;

use Common\Model\BaseModel;

class TeacherCourseModel extends BaseModel
{
    protected $_validate = array(
        array('t_id', 'require', '老师不能为空'),
        array('g_id', 'require', '年级不能为空'),
        array('c_id', 'require', '班级不能为空'),
        array('crs_id', 'require', '课程不能为空'),
    );


    /* 查询列表
     * @param string $fields 字段
     * @param array $where where条件数组: array('field1'=>'value1','field2'=>'value2')
     * @param array $orderby orderby数组: array('field1'=>'ASC','field2'=>'DESC')
     * @param int $page 页码
     * @param int $pagesize 每页数量
     * @param array $groupby
     * @param array $data_auth 数据权限
     *  * @return uret['count']  总数    $ret['list']  查询结果列表
     */
    public function queryListEx($fields, $where = null, $orderby = null, $page = 0, $pagesize = 0)
    {

        if ($page) {
            $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
        }
        if ($page) {
            $ret['list'] = self::alias('a')->field($fields)->where($where)->order($orderby)->page($page, $pagesize)->select();
        } else {
            $ret['list'] = self::alias('a')->field($fields)->where($where)->order($orderby)->select();
        }
        if ($page == 0) {
            $ret ["count"] = count($ret ["list"]);
        } else {
            $ret ["count"] = $this->getCount(); //获得记录总数
        }

        $classInfo = D('Class')->getField('c_id,name');
        $gradeInfo = D('grade')->getField('g_id,name');
        $courseInfo = D('course')->getField('crs_id,name');
        foreach ($ret['list'] as &$value) {
            $value['c_id'] = isset($classInfo[$value['c_id']]) ? $classInfo[$value['c_id']] : $value['c_id'];
            $value['g_id'] = isset($gradeInfo[$value['g_id']]) ? $gradeInfo[$value['g_id']] : $value['g_id'];
            $value['crs_id'] = isset($courseInfo[$value['crs_id']]) ? $courseInfo[$value['crs_id']] : $value['crs_id'];
            $where1['user_id'] = $value['user_id'];
            $userInfo = M('user', 'think_')->where($where1)->getField('user_id,name');
            $value['user_name'] = isset($userInfo[$value['user_id']]) ? $userInfo[$value['user_id']] : $value['user_id'];
            $where2['h_id'] = $value['h_id'];
            $homeworkFileInfo = D('HomeworkFile')->where($where2)->getField('file_path', true);
            $value['file_path'] = isset($homeworkFileInfo) ? $homeworkFileInfo : '';
        }
        //echo self::getLastSql();

        return $ret;
    }

    //根据年纪班级id获取老师信息
    public function getTeacherByGidCid($g_ids, $c_ids)
    {

        $where['g_id'] = array('in', $g_ids);
        $where['c_id'] = array('in', $c_ids);

        $where['valid'] = '0';
        $data = array();
        if ($c_ids) {
            $tArr = M("Class")->union(" select t_id,c_id,g_id from sch_teacher_course where c_id in ({$c_ids})")->where($where)->getField("t_id,c_id,g_id");

            foreach ($tArr as $key => $value) {
                $ret[$key] = M("Teacher")
                    ->alias("tch")->field('tch.t_id,tch.u_id,tch.name,tch.phone,tch.s_id,sch_info.name s_name,tch.a_id,sch_area.name a_name,dep.name d_name,tch.education,tch.sex,tch.email,tch.valid')
                    ->join('LEFT JOIN sch_dept dep on dep.d_id = tch.d_id')
                    ->join('LEFT JOIN sch_school_information sch_info on tch.s_id = sch_info.s_id')
                    ->join('LEFT JOIN sch_school_area sch_area on tch.a_id = sch_area.a_id')
                    ->where(" tch.t_id = {$value['t_id']} AND tch.valid = 0 ")->find();
                //判断是否为该班班主任
                $is_head = M("Class")->where(" t_id = {$value['t_id']} AND c_id = {$value['c_id']} ")->find();
                if ($is_head) {
                    $ret[$key]['charge'] = 1;
                } else {
                    $ret[$key]['charge'] = 0;
                }
                $ret[$key]['g_id'] = $value['g_id'];
                $ret[$key]['c_id'] = $value['c_id'];
            }

            foreach ($ret as $key => $value) {
                if (!empty($value['name'])) {
                    $data[] = $value;
                }
            }
            // var_dump($data);die;
            // if(!empty($ret['teacher'][])){
            // }
            sort($data);
        }

        return $data;
    }

}