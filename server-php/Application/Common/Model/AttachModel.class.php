<?php
namespace Common\Model;
use Common\Model\BaseModel;

/**
 * Attach Model
 * @package Common\Model
 * 订单附加费用
 */
class AttachModel extends BaseModel{

	protected $_validate =array(
		array('a_mc','require','名称不能为空'),
		array('a_mc','','名称已经存在',self::EXISTS_VALIDATE,'unique',self::MODEL_BOTH),
		array('a_jjlx','require','计价类型不能为空'),
		array('a_dj','require','数值或单价不能为空'),
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

	}

	public function setData(&$record){

	}

	public function setListData(&$list){
		$jjlxList = C('JJLX');
		$jjjsList = C('JJJS');
		foreach ($list as &$value){
			$value['a_jjlx'] = isset($jjlxList[$value['a_jjlx']])?$jjlxList[$value['a_jjlx']]:$value['a_jjlx'];
			$value['a_jjjs'] = isset($jjjsList[$value['a_jjjs']])?$jjjsList[$value['a_jjjs']]:$value['a_jjjs'];
		}
		
	}

	

	
}