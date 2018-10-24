<?php
namespace Common\Model;
use Common\Model\BaseModel;
class HomeworkModel extends BaseModel{

	protected $_validate =array(
	    array('name','require','请输入作业名称'),
	    array('g_id','require','选择年级'),
	    array('c_id','require','请选择班级'),
	    array('crs_id','require','请选择课程'),
	    array('work_content','require','请输入作业内容'),
	    array('finish_time','require','请选择作业完成时间'),
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
		$gradeInfo = D('grade')->getField('g_id,name');
		$courseInfo = D('course')->getField('crs_id,name');
		foreach ($ret['list'] as &$value){
			$value['c_id'] = isset($classInfo[$value['c_id']])? $classInfo[$value['c_id']]:$value['c_id'];
			$value['g_id'] = isset($gradeInfo[$value['g_id']])? $gradeInfo[$value['g_id']]:$value['g_id'];
			$value['crs_id'] = isset($courseInfo[$value['crs_id']])? $courseInfo[$value['crs_id']]:$value['crs_id'];
			$where1['user_id'] = $value['user_id'];
			$userInfo = M('user','think_')->where($where1)->getField('user_id,name');
			$value['user_id'] = isset($userInfo[$value['user_id']])? $userInfo[$value['user_id']]:$value['user_id'];
			$where2['h_id'] = $value['h_id'];
			$homeworkFileInfo = D('HomeworkFile')->where($where2)->getField('file_path',true);
			$value['file_path'] = isset($homeworkFileInfo)? $homeworkFileInfo:'';
		}

	    return $ret;
	}


	public function queryListForApp($fields,$where = null, $orderby = null, $page = 0, $pagesize = 0) {
	
	    if ($page){
	        $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
	    }
	    if($page){
	        $ret['list'] = self::alias('a')->field($fields)->where($where)->order($orderby)->page($page,$pagesize)->select();
	    }else{
	        $ret['list'] = self::alias('a')->field($fields)->where($where)->order($orderby)->select();
	    }
//		echo self::getLastSql();die;
	    if ($page == 0) {
	        $ret ["count"] = count ( $ret ["list"] );
	    } else {
	        $ret ["count"] = $this->getCount (); //获得记录总数
	    }

		$classInfo = D('Class')->getField('c_id,name');
		$gradeInfo = D('grade')->getField('g_id,name');
		$courseInfo = D('course')->getField('crs_id,name');
		foreach ($ret['list'] as &$value){
			$value['c_name'] = isset($classInfo[$value['c_id']])? $classInfo[$value['c_id']]:$value['c_id'];
			$value['g_name'] = isset($gradeInfo[$value['g_id']])? $gradeInfo[$value['g_id']]:$value['g_id'];
			$value['crs_name'] = isset($courseInfo[$value['crs_id']])? $courseInfo[$value['crs_id']]:$value['crs_id'];
			$where1['user_id'] = $value['user_id'];
			$userInfo = M('user','think_')->where($where1)->getField('user_id,name');
			$value['user_name'] = isset($userInfo[$value['user_id']])? $userInfo[$value['user_id']]:$value['user_id'];
			$where2['h_id'] = $value['h_id'];
			$homeworkFileInfo = D('HomeworkFile')->where($where2)->getField('file_path',true);
			$value['file_path'] = isset($homeworkFileInfo)? $homeworkFileInfo:array();
		}

	    return $ret;
	}


	
}