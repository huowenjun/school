<?php
namespace Common\Model;
use Common\Model\BaseModel;
class ExamAllModel extends BaseModel{

	protected $_validate =array(
	    array('name','require','请输入考试名称'),
	    array('g_id','require','请选择年级'),
	    array('exam_type','require','请选择考试类型'),
	    array('crs_id','require','请选择学科'),
	    array('start_date','require','请选择考试开始日期'),
	    array('end_date','require','请选择考试结束日期'),
	    array('start_time','require','请选择考试开始时间'),
	    array('end_time','require','请选择考试结束时间'),
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
	   //echo self::getLastSql();
	    $gradeInfo = D('Grade')->getfield('g_id,name');
	    $courseInfo = D('Course')->getfield('crs_id,name');
	    // echo "<pre>";
	    // var_dump( $courseInfo );
		$examtypeInfo = C('EXAM_TYPE');
		foreach ($ret['list'] as &$value){
			$value['exam_type'] = isset($examtypeInfo[$value['exam_type']])?$examtypeInfo[$value['exam_type']]:$value['exam_type'];
			$value['g_id_title'] = isset($gradeInfo[$value['g_id']])?$gradeInfo[$value['g_id']]:$value['g_id'];
			$value['crs_id'] = isset($courseInfo[$value['crs_id']])?$courseInfo[$value['crs_id']]:$value['crs_id'];
			$where1['user_id'] = $value['user_id'];
			$userInfo = M('user','think_')->where($where1)->getField('user_id,name');
			$value['user_id'] = isset($userInfo[$value['user_id']])? $userInfo[$value['user_id']]:$value['user_id'];
		}
	    return $ret;
	}



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
	public function queryListEx1($fields,$where = null, $orderby = null, $page = 0, $pagesize = 0, $groupby = null) {
	
	    if ($page){
	        $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
	    }

	    if($page){
	        $ret['list'] = self::alias('a')->join('LEFT JOIN sch_exam_all_sub b ON a.e_id = b.e_id LEFT JOIN sch_class c ON a.g_id=c.g_id LEFT JOIN sch_exam_all_results d on c.c_id=d.c_id and b.crs_id=d.crs_id')->field($fields)->where($where)->order($orderby)->page($page,$pagesize)->group($groupby)->select();
	    }else{
	        $ret['list'] = self::alias('a')->join('LEFT JOIN sch_exam_all_sub b ON a.e_id = b.e_id LEFT JOIN sch_class c ON a.g_id=c.g_id LEFT JOIN sch_exam_all_results d on c.c_id=d.c_id and b.crs_id=d.crs_id')->field($fields)->where($where)->order($orderby)->group($groupby)->select();
	    }
	    if ($page == 0) {
	        $ret ["count"] = count ( $ret ["list"] );
	    } else {
	        $ret ["count"] = $this->getCount (); //获得记录总数
	    }

		// $lstStatus = C('USER_STATUS');
		// $userType = C('USER_TYPE');
		// $sexType = C('SEX_TYPE');
		foreach ($ret['list'] as &$value){
			$where1['crs_id'] = $value['crs_id'];
			$courseInfo = D('Course')->where($where1)->getfield('crs_id,name');
			$value['crs_id'] = isset($courseInfo[$value['crs_id']])?$courseInfo[$value['crs_id']]:$value['crs_id'];	
			$val = "已录入";
			if ($value['cnt']==0) {
				$val = "未录入";
			}
			$value['cnt'] = $val;
		}

		// echo self::getLastSql();

	
	    return $ret;
	}





	public function queryListR($fields,$where = null, $orderby = null, $page = 0, $pagesize = 0, $groupby = null) {
	
	    if ($page){
	        $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
	    }

	    if($page){
	        $ret['list'] = self::alias('a')->join('LEFT JOIN sch_exam_all_sub b on a.e_id=b.e_id left join sch_student c on a.g_id=c.g_id  left join sch_exam_all_results d on c.stu_id=d.stu_id and b.crs_id=d.crs_id left join sch_course e on b.crs_id=e.crs_id')->field($fields)->where($where)->order($orderby)->page($page,$pagesize)->group($groupby)->select();
	    }else{
	        $ret['list'] = self::alias('a')->join('LEFT JOIN sch_exam_all_sub b on a.e_id=b.e_id left join sch_student c on a.g_id=c.g_id  left join sch_exam_all_results d on c.stu_id=d.stu_id and b.crs_id=d.crs_id left join sch_course e on b.crs_id=e.crs_id')->field($fields)->where($where)->order($orderby)->group($groupby)->select();
	    }
	    if ($page == 0) {
	        $ret ["count"] = count ( $ret ["list"] );
	    } else {
	        $ret ["count"] = $this->getCount (); //获得记录总数
	    }

		 //echo self::getLastSql();
	    foreach ($ret['list'] as &$value){
			$where1['c_id'] = $value['c_id'];
			$classInfo = D('Class')->where($where1)->getField('c_id,name');
			$value['c_id'] = isset($classInfo[$value['c_id']])? $classInfo[$value['c_id']]:$value['crs_id'];
		}

	
	    return $ret;
	}



	
}