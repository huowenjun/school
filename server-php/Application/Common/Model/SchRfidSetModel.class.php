<?php
namespace Common\Model;
use Common\Model\BaseModel;
class SchRfidSetModel extends BaseModel{

	protected $_validate =array(
	   
//	    array('name','require','请输入班级名称'),
//	    array('t_id','require','请选择班主任'),
//
//		// array('name','','班级名称已存在',1,'unique',1),
//        array('name', 'checklength', '名称长度必须在2-16位之间！', 0, 'callback', 3, array(2, 16)),
//        array('c_id,g_id,name','checkUnique','班级名称不能重复',self::MUST_VALIDATE,'callback',self::MODEL_BOTH), // 自定义函数验证规则
	    // array('memo','require','备注不能为空'),
	);

   protected $trueTableName = 'sch_cardpreset';
	
}