<?php
namespace Common\Model;
use Common\Model\BaseModel;
class ExamResultsModel extends BaseModel{

	protected $_validate =array(
	    array('scores','require','请输入分数'),
	);
	
	public function queryListEx($fields,$where = null, $orderby = null, $page = 0, $pagesize = 0) {
	
	    if ($page){
	        $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
	    }
	    if($page){
	        $ret['list'] = self::alias('a')->field($fields)->where($where)->order($orderby)->page($page,$pagesize)->select();
	    }else{
	        $ret['list'] = self::alias('a')->field($fields)->where($where)->order($orderby)->select();
	    }
	    if ($page == 0) {
	        $ret ["count"] = count ( $ret ["list"] );
	    } else {
	        $ret ["count"] = $this->getCount (); //获得记录总数
	    }
	    
		$classInfo = D('Class')->getField('c_id,name');
		$examInfo = D('exam')->getField('ex_id,name');
		$courseInfo = D('course')->getField('crs_id,name');
		$stuInfo = D('student')->getField('stu_id,stu_name');
		foreach ($ret['list'] as &$value){
			$value['c_id'] = isset($classInfo[$value['c_id']])? $classInfo[$value['c_id']]:$value['c_id'];
			$value['ex_id'] = isset($examInfo[$value['ex_id']])? $examInfo[$value['ex_id']]:$value['ex_id'];
			$value['crs_id'] = isset($courseInfo[$value['crs_id']])? $courseInfo[$value['crs_id']]:$value['crs_id'];
			$value['stu_id'] = isset($stuInfo[$value['stu_id']])? $stuInfo[$value['stu_id']]:$value['stu_id'];
		}
	   //echo self::getLastSql();

	
	    return $ret;
	}
	

	public function queryListEx1($fields,$where = null, $orderby = null, $page = 0, $pagesize = 0) {
	
	    if ($page){
	        $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
	    }//sch_student
	    if($page){
	        $ret['list'] = self::alias('a')->join('LEFT JOIN sch_student b ON a.stu_id=b.stu_id')->field($fields)->where($where)->order($orderby)->page($page,$pagesize)->select();
	    }else{
	        $ret['list'] = self::alias('a')->join('LEFT JOIN sch_student b ON a.stu_id=b.stu_id')->field($fields)->where($where)->order($orderby)->select();
	    }
	    if ($page == 0) {
	        $ret ["count"] = count ( $ret ["list"] );
	    } else {
	        $ret ["count"] = $this->getCount (); //获得记录总数
	    }
	   //echo self::getLastSql();

	
	    return $ret;
	}

	//根据学生id，时间段查询统考成绩
    function queryScoreByStuId($stu_id,$dates){

		$where['exam_single_rslt.stu_id'] = $stu_id;
		$where['exam_single.start_time'] = array('like','%'.$dates.'%');

    	$ret = self::alias('exam_single_rslt')
    	->field('0 as exam_com_type,exam_single.exam_type as exam_type,exam_single.name as exam_name,'
				.'exam_single.start_time,exam_single.end_time,'
				.'GROUP_CONCAT(crs.name) as course_name,GROUP_CONCAT(exam_single_rslt.scores) as scores')
    	->join('sch_course crs on exam_single_rslt.crs_id = crs.crs_id')
    	->join('sch_exam exam_single on exam_single_rslt.ex_id = exam_single.ex_id')
    	->where($where)->group('exam_single_rslt.exs_id')->select();

    	return $ret;
    }

}