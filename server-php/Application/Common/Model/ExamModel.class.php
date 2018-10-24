<?php
namespace Common\Model;
use Common\Model\BaseModel;
class ExamModel extends BaseModel{

	protected $_validate =array(
	    array('name','require','请输入考试名称'),
	    array('crs_id','require','请选择科目'),
	    array('g_id','require','请选择年级'),
	    array('c_id','require','请选择班级'),
	    array('exam_type','require','请选择考试类型'),
	    array('start_time','require','请选择考试开始时间'),
	    array('end_time','require','请选择考试结束时间'),
	    array('exam_site','require','请输入考试地点'),
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
		$courseInfo = D('course')->getField('crs_id,name');
		$examInfo = C('EXAM_TYPE');
		$inputInfo = C('INPUT_STATE');
		foreach ($ret['list'] as &$value){
			$value['c_id'] = isset($classInfo[$value['c_id']])? $classInfo[$value['c_id']]:$value['c_id'];
			$value['crs_id'] = isset($courseInfo[$value['crs_id']])? $courseInfo[$value['crs_id']]:$value['crs_id'];
			$where1['user_id'] = $value['user_id'];
			$userInfo = M('user','think_')->where($where1)->getField('user_id,name');
			$value['user_id'] = isset($userInfo[$value['user_id']])? $userInfo[$value['user_id']]:$value['user_id'];
			$value['exam_type'] = isset($examInfo[$value['exam_type']])?$examInfo[$value['exam_type']]:$value['exam_type'];
			$resultInfo = D('ExamResults')->where(array('ex_id'=> $value['ex_id']))->select();
			if(empty($resultInfo)){
				$value['input_state'] = 0;//未录入
			}else{
				$value['input_state'] = 1;//已录入
			}
			$value['input_state'] = isset($inputInfo[$value['input_state']])?$inputInfo[$value['input_state']]:$value['input_state'];

		}
	   //echo self::getLastSql();

	
	    return $ret;
	}
	
}