<?php
namespace Common\Model;
use Common\Model\BaseModel;
class MessageModel extends BaseModel{

	protected $_validate =array(
	    array('s_id','require','请选择校区!'),
	    array('content','require','请输入消息内容!'),
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
	    
		$userInfo = M('user','think_')->getField('user_id,name');
		$mtypeInfo = C('MESSAGE_TYPE');
		foreach ($ret['list'] as &$value){
			$value['m_type'] = isset($mtypeInfo[$value['m_type']])? $mtypeInfo[$value['m_type']]:$value['m_type'];
			$value['user_id'] = isset($userInfo[$value['user_id']])? $userInfo[$value['user_id']]:$value['user_id'];
		}
	   //echo self::getLastSql();

	
	    return $ret;
	}
	
}