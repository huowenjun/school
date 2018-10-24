<?php
namespace Common\Model;
use Common\Model\BaseModel;
class DeviceManageModel extends BaseModel{

	protected $_validate =array(
	   
	    // array('imei','require','imei号不能为空'),
	    // array('nfc_id','require','NFCID号不能为空'),
	    array('imei','validStorage','imei号不能为空',1,'callback',3),  //使用回调函数validStorage
	    array('nfc_id','validStorage','NFCID号不能为空',1,'callback',3),  //使用回调函数validStorage
      	array('stu_no','validStorage','学号不能为空',1,'callback',3),  //使用回调函数validStorage
      	array('stu_name','validStorage','学生姓名不能为空',1,'callback',3),  //使用回调函数validStorage
      	array('stu_phone', 'validStorage', '请输入学生卡手机号码', 1, 'callback', 1),
        array('stu_phone', 'isTel1', '学生卡手机号码格式不正确', 1, 'callback', 3),
		// array('stu_no','','stu_no号不能为空',1,'require',3),
		// array('name','','stu_no号不能为空',1,'require',3),
		// array('icc_id','','ICCID已存在',2,'unique',3),
		// array('imei','','已存在',1,'unique',1),
        // array('name', 'checklength', '名称长度必须在2-16位之间！', 0, 'callback', 3, array(2, 16)),
        // array('c_id,g_id,name','checkUnique','班级名称不能重复',self::MUST_VALIDATE,'callback',self::MODEL_BOTH), // 自定义函数验证规则 
	    // array('memo','require','备注不能为空'),
	);

		//使用回调函数validStorage
		protected function validStorage($value){
		if(empty($value)){
			return false;
		}else{
			return true;
			}
		}

		  function isTel1($tel, $type = 'sj')
    {
        $regxArr = array(
            'sj' => '/^1[3578]\d{9}$/',
            // 'tel' => '/^(010|02\d{1}|0[3-9]\d{2})-\d{7,9}(-\d+)?$/',
            // '400' => '/^400(-\d{3,4}){2}$/',
        );
        if ($type && isset($regxArr[$type])) {
            return preg_match($regxArr[$type], $tel) ? true : false;
        }
        foreach ($regxArr as $regx) {
            if (preg_match($regx, $tel)) {
                return true;
            }
        }
        return false;
    }



	protected $trueTableName  = 'sch_device';

	public function queryListEX($fields,$where = null, $orderby = null, $page = 0, $pagesize = 0, $groupby = null,$data_auth = null) {
	        if ($page){ 
	            $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
	        }
	        if($page){
	            $ret['list'] = self::field($fields)->where($where)->order($orderby)->page($page,$pagesize)->group($groupby)->select();
	        }else{
	            $ret['list'] = self::field($fields)->where($where)->order($orderby)->group($groupby)->select();
	        }

	      // var_dump(self::getLastSql());
	    
	        if ($page == 0) {
	            $ret ["count"] = count ( $ret ["list"] );
	        } else {
	            $ret ["count"] = $this->getCount (); //获得记录总数
	        }
			
			$DEVICE = C("DEVICE");
	      
	        foreach ($ret['list'] as $k => $v) {
	          
	            $ret['list'][$k]['status'] = $DEVICE[$v['status']]?$DEVICE[$v['status']]:'';
	        }
					
	        return $ret;
	    }
	
}