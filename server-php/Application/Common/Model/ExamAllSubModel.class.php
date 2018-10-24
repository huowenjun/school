<?php
namespace Common\Model;
use Common\Model\BaseModel;
class ExamAllSubModel extends BaseModel{

	protected $_validate =array(
	    array('crs_id','require','请选择科目'),
	    array('start_time','require','请选择考试开始时间'),
	    array('end_time','require','请选择考试结束时间'),
	);
	

	
}