<?php
namespace Common\Model;
use Common\Model\BaseModel;
class RemarkModel extends BaseModel{

	protected $_validate =array(
	    array('c_id','require','请选择班级'),
	    array('stu_id','require','请选择学生'),
	    array('r_type','require','请选择评语类型'),
	    array('content','require','请输入评论内容'),
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
		$stuInfo = D('student')->getField('stu_id,stu_name');
		$remarktypeInfo = C('REMARK_TYPE');
		foreach ($ret['list'] as &$value){
			$value['c_id'] = isset($classInfo[$value['c_id']])? $classInfo[$value['c_id']]:$value['c_id'];
			$value['stu_id'] = isset($stuInfo[$value['stu_id']])? $stuInfo[$value['stu_id']]:$value['stu_id'];
			$where1['user_id'] = $value['user_id'];
			$userInfo = M('user','think_')->where($where1)->getField('user_id,name');
			$value['user_id'] = isset($userInfo[$value['user_id']])? $userInfo[$value['user_id']]:$value['user_id'];
			$value['r_type'] = isset($remarktypeInfo[$value['r_type']])? $remarktypeInfo[$value['r_type']]:$value['r_type'];
		}
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

        $remarkTypeInfo = C('REMARK_TYPE');
        $classInfo = D('Class')->getField('c_id,name');
        $gradeInfo = D('grade')->getField('g_id,name');
        $stuInfo = M('Student')->getField('stu_id,stu_name');
        foreach ($ret['list'] as &$value){
            $value['r_type'] = isset($remarkTypeInfo[$value['r_type']])? $remarkTypeInfo[$value['r_type']]:$value['r_type'];
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