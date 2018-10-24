<?php
namespace Common\Model;
use Common\Model\BaseModel;
class StudentParentModel extends BaseModel{

	protected $_validate =array(
		// array('parent_name','require','请输入监护人姓名'),
	    array('parent_phone','require','请输入监护人电话'),
	    // array('parent_phone','','家长电话已存在',1,'unique',1),
	    // array('parent_name','require','请输入监护人姓名'),
	    // array('phone','require','请输入监护人电话'),
	    // array('phone','','监护人电话已存在',1,'unique',1),
	    array('relation','require','请选择学生与监护人关系'),
	    // array('work_unit','require','请输入监护人工作单位'),
	    array('address','require','请输入家庭地址'),
	    // array('family_tel','require','请输入家庭电话'),
	    // array('email','require','请输入邮箱'),
	    array('email','email','邮箱格式不正确',self::VALUE_VALIDATE),
		// parent_phone

        // array('main_phone', 'isTel', '主负责人电话格式不正确', 0, 'callback', 3),
        // array('main_phone','/(^1[3|4|5|7|8][0-9]{9}$)/','主负责人电话格式不正确',self::EXISTS_VALIDATE),
        // array('main_phone','','主负责人电话号已存在',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT),
        // array('parent_phone','/(^1[3|4|5|7|8][0-9]{9}$)/','副负责人电话格式不正确',self::EXISTS_VALIDATE),
        // array('sub_phone','','副负责人电话号已存在',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT),
        // array('family_tel','/^((0\d{2,3}-\d{7,8})|(1[3584]\d{9}))$/','家庭电话号格式不正确,格式(010-12345678)',self::EXISTS_VALIDATE),
        // array('tel','','内线电话号已存在',self::EXISTS_VALIDATE,'unique',self::MODEL_INSERT),


	    // array('email','','邮箱已经存在！',1,'unique',self::EXISTS_VALIDATE),
	    // array('user_name','require','请输入登录帐号'),
	    // array('user_name','','登录帐号已存在，请设置其它帐号',1,'unique',1),
	);
	

	
}