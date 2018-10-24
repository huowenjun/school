<?php
namespace Common\Model;
use Common\Model\BaseModel;
class SchteacherCourseModel extends BaseModel{

	protected $_validate =array(
		array('crs_id','require','请选择课程'),
	);
	

	
}