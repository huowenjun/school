<?php
namespace Common\Model;
use Common\Model\BaseModel;
class RewardModel extends BaseModel{

	protected $_validate =array(
	    array('g_id','require','请选择年级'),
	    array('c_id','require','请选择班级'),
	    array('stu_id','require','请选择学生'),
	    array('reward_type','require','请选择奖励类型'),
	    array('reward_details','require','请输入奖励详情'),
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
	    
		$rewardTypeInfo = C('REWARD_TYPE');
		$gradeInfo = M('Grade')->getField('g_id,name');
		$classInfo = D('Class')->getField('c_id,name');
		$stuInfo = M('Student')->getField('stu_id,stu_name');
		foreach ($ret['list'] as &$value){
			$value['reward_type'] = isset($rewardTypeInfo[$value['reward_type']])? $rewardTypeInfo[$value['reward_type']]:$value['reward_type'];
			$value['stu_id'] = isset($stuInfo[$value['stu_id']])? $stuInfo[$value['stu_id']]:$value['stu_id'];
			$value['c_id'] = isset($classInfo[$value['c_id']])? $classInfo[$value['c_id']]:$value['c_id'];
			$value['g_id'] = isset($gradeInfo[$value['g_id']])? $gradeInfo[$value['g_id']]:$value['g_id'];
			$where1['user_id'] = $value['user_id'];
			$userInfo = M('user','think_')->where($where1)->getField('user_id,name');
			$value['user_id'] = isset($userInfo[$value['user_id']])? $userInfo[$value['user_id']]:$value['user_id'];
		}
	   //echo self::getLastSql();

	
	    return $ret;
	}


    public function queryListApp($fields,$where = null, $orderby = null, $page = 0, $pagesize = 0) {

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

        $rewardTypeInfo = C('REWARD_TYPE');
        $classInfo = D('Class')->getField('c_id,name');
        $gradeInfo = D('grade')->getField('g_id,name');
        $stuInfo = M('Student')->getField('stu_id,stu_name');
        foreach ($ret['list'] as &$value){
            $value['reward_type'] = isset($rewardTypeInfo[$value['reward_type']])? $rewardTypeInfo[$value['reward_type']]:$value['reward_type'];
            $value['c_name'] = isset($classInfo[$value['c_id']])? $classInfo[$value['c_id']]:$value['c_id'];
            $value['g_name'] = isset($gradeInfo[$value['g_id']])? $gradeInfo[$value['g_id']]:$value['g_id'];
            $where1['user_id'] = $value['user_id'];
            $userInfo = M('user','think_')->where($where1)->getField('user_id,name');
            $value['user_name'] = isset($userInfo[$value['user_id']])? $userInfo[$value['user_id']]:$value['user_id'];
            $value['stu_name'] = isset($stuInfo[$value['stu_id']])? $stuInfo[$value['stu_id']]:$value['stu_id'];
        }
        //echo self::getLastSql();


        return $ret;
    }
	
}