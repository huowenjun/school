<?php
namespace Common\Model;
use Common\Model\BaseModel;
class LeaveModel extends BaseModel{

	protected $_validate =array(
		array('stu_id','require','请选择学生姓名'),
	    array('leave_type','require','请选择请假类型'),
	    array('leave_memo','require','请输入请假事由'),
	    array('start_time','require','请选择请假开始时间'),
	    array('end_time','require','请选择销假时间'),
	    array('t_id','require','请选择审批老师'),

	);

	public function queryListEX($fields,$where = null, $orderby = null, $page = 0, $pagesize = 0, $groupby = null,$data_auth = null) {
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
			
			$LEAVE_TYPE = C("LEAVE_TYPE");
			$LEAVE_STATUS = C("LEAVE_STATUS");
	      
			$Student = D("Student")->getField("stu_id,stu_no,stu_name");
			$Class = D("Class")->getField("c_id,name");
			$Teacher = D("Teacher")->getField("t_id,name");
			// var_dump($Class);
			
	        foreach ($ret['list'] as $k => $v) {
	      $ret['list'][$k]['c_id'] = $Class[$v['c_id']]?$Class[$v['c_id']]:'';
	      $ret['list'][$k]['t_id'] = $Teacher[$v['t_id']]?$Teacher[$v['t_id']]:'';
				$ret['list'][$k]['status'] = $LEAVE_STATUS[$v['status']]?$LEAVE_STATUS[$v['status']]:'';
				$ret['list'][$k]['leave_type'] = $LEAVE_TYPE[$v['leave_type']]?$LEAVE_TYPE[$v['leave_type']]:'';
				$ret['list'][$k]['stu_name'] = $Student[$v['stu_id']]['stu_name']?$Student[$v['stu_id']]['stu_name']:'';
				$ret['list'][$k]['stu_no'] = $Student[$v['stu_id']]['stu_no']?$Student[$v['stu_id']]['stu_no']:'';
	            
	        }
					
	        return $ret;
	    }
	
}