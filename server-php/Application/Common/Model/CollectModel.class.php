<?php
namespace Common\Model;
use Common\Model\BaseModel;
class CollectModel extends BaseModel{

	protected $_validate =array(
	    array('crs_id','require','请选择科目'),
	    array('e_id','require','请选择考试'),
	    array('g_id','require','请选择年级'),
	    array('c_id','require','请选择班级'),
	   array('scores','require','请输入分数'),
	);
	
	public function queryListEx($fields,$where = null, $orderby = null, $page = 0, $pagesize = 0, $groupby = null) {
	
	    if ($page){
	        $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
	    }

	    if($page){
	        $ret['list'] = self::alias('a')->join('LEFT JOIN sch_goods_detail b ON a.product_id = b.id')->field($fields)->where($where)->order($orderby)->page($page,$pagesize)->group($groupby)->select();
	    }else{
	        $ret['list'] = self::alias('a')->join('LEFT JOIN sch_goods_detail b ON a.product_id = b.id')->field($fields)->where($where)->order($orderby)->group($groupby)->select();
	    }
	    if ($page == 0) {
	        $ret ["count"] = count ( $ret ["list"] );
	    } else {
	        $ret ["count"] = $this->getCount (); //获得记录总数
	    }
	
	    return $ret;
	}
}