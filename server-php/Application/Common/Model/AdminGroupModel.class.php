<?php
namespace Common\Model;
use Common\Model\BaseModel;
class AdminGroupModel extends BaseModel{

	protected $_validate =array(
	    array('title','require','角色名不能为空'),
        array('title','','角色名已经存在',1,'unique',1),
	);
	
	protected $trueTableName  = 'think_auth_group';

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
	        $ret['list'] = self::alias('a')->join('LEFT JOIN think_auth_group_access b ON a.id = b.group_id LEFT JOIN think_user c ON b.uid = c.user_id')->field($fields)->where($where)->order($orderby)->page($page,$pagesize)->group('a.id')->select();
	    }else{
	        $ret['list'] = self::alias('a')->join('LEFT JOIN think_auth_group_access b ON a.id = b.group_id LEFT JOIN think_user c ON b.uid = c.user_id')->field($fields)->where($where)->order($orderby)->group('a.id')->select();
	    }
	    if ($page == 0) {
	        $ret ["count"] = count ( $ret ["list"] );
	    } else {
	        $ret ["count"] = $this->getCount (); //获得记录总数
	    }
	    
		$lstStatus = array(1=>'<span class="label label-success">有效</span>',0=>'<span class="label label-danger">无效</span>');
		foreach ($ret['list'] as &$value){
			if(isset($value['status']))
				$value['status'] = isset($lstStatus[$value['status']])? $lstStatus[$value['status']]:$value['status'];
		}
	   //echo self::getLastSql();

	
	    return $ret;
	}
	
	

	
}