<?php
namespace Common\Model;
use Common\Model\BaseModel;
class WorkRuleModel extends BaseModel{

	protected $_validate =array(
		array('s_id','require','请选择学校'),
		array('a_id','require','请选择校区'),
		array('start_time','require','请选择进校时间'),
	    array('end_time','require','请选择出校时间'),
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
	     // echo self::getLastSql();
	    return $ret;
	}

	
}