<?php
namespace Common\Model;
use Common\Model\BaseModel;
class ExamAllResultsModel extends BaseModel{

	protected $_validate =array(
	    array('crs_id','require','请选择科目'),
	    array('e_id','require','请选择考试'),
	    array('g_id','require','请选择年级'),
	    array('c_id','require','请选择班级'),
	   array('scores','require','请输入分数'),
	);
	
	public function queryListEx($fields,$where = null, $orderby = null, $page = 0, $pagesize = 0, $groupby = null) {
	
	    if ($page){
	        $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
	    }

	    if($page){
	        $ret['list'] = self::alias('a')->join('LEFT JOIN sch_exam_all_sub b ON a.e_id=b.e_id and a.crs_id=b.crs_id LEFT JOIN sch_student c ON a.c_id=c.c_id ')->field($fields)->where($where)->order($orderby)->page($page,$pagesize)->group($groupby)->select();
	    }else{
	        $ret['list'] = self::alias('a')->join('LEFT JOIN sch_exam_all_sub b ON a.e_id=b.e_id and a.crs_id=b.crs_id LEFT JOIN sch_student c ON a.c_id=c.c_id ')->field($fields)->where($where)->order($orderby)->group($groupby)->select();
	    }
	    if ($page == 0) {
	        $ret ["count"] = count ( $ret ["list"] );
	    } else {
	        $ret ["count"] = $this->getCount (); //获得记录总数
	    }
	
	    return $ret;
	}

	/**
     * 查询列表
     * @param string $fields 字段
     * @param array $where where条件数组: array('field1'=>'value1','field2'=>'value2')
     * @param array $orderby orderby数组: array('field1'=>'ASC','field2'=>'DESC')
     * @param int $page 页码
     * @param int $pagesize 每页数量
     * @param array $groupby
     * @param array $data_auth 数据权限
     *  * @return uret['count']  总数    $ret['list']  查询结果列表
     */
    public function queryListR($fields,$where = null, $orderby = null, $page = 0, $pagesize = 0, $groupby = null,$data_auth = null) {
        if ($page){ 
            $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
        }
        if($page){
            $ret['list'] = self::field($fields)->where($where)->order($orderby)->page($page,$pagesize)->group($groupby)->select();
        }else{
            $ret['list'] = self::field($fields)->where($where)->order($orderby)->group($groupby)->select();
        }

       // dump(self::getLastSql());
        
        if ($page == 0) {
            $ret ["count"] = count ( $ret ["list"] );
        } else {
            $ret ["count"] = $this->getCount (); //获得记录总数
        }

        $coureInfo = D('Course')->getField('crs_id,name');
		foreach ($ret['list'] as &$value){
			$where['stu_id'] = $value['stu_id'];
			$studentInfo = D('Student')->where($where)->getField('stu_id,stu_name');
			$value['crs_id'] = isset($coureInfo[$value['crs_id']])? $coureInfo[$value['crs_id']]:$value['crs_id'];
			$value['stu_id'] = isset($studentInfo[$value['stu_id']])? $studentInfo[$value['stu_id']]:$value['stu_id'];
				
		}
        return $ret;
    }

    //根据学生id，时间段查询统考成绩
    function queryScoreByStuId($stu_id,$dates){

    	//var_dump('queryScoreByStuId');die();
    	$where['exam_rslt.stu_id'] = $stu_id;
		$where['exam_all.start_date'] = array('like','%'.$dates.'%');

    	$ret = self::alias('exam_rslt')
    	->field('1 as exam_com_type,exam_all.exam_type as exam_type,exam_all.name as exam_name,'
				.'exam_all.start_date as start_time,exam_all.end_date as end_time,'
				.'GROUP_CONCAT(crs.name) as course_name,GROUP_CONCAT(exam_rslt.scores) as scores')
    	->join('sch_course crs on exam_rslt.crs_id = crs.crs_id ')
    	->join('sch_exam_all exam_all on exam_rslt.e_id = exam_all.e_id')
    	->where($where)->group('exam_rslt.e_id')->select();

    	return $ret;
    }

	
}