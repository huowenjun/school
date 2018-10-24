<?php
namespace Common\Model;
use Common\Model\BaseModel;
class GradeModel extends BaseModel{

	protected $_validate =array(
		array('name','require','年级名称不能为空'),
		// array('a_id','require','校区不能为空'),
		// array('name','','年级名称已存在',1,'unique',1),
        array('name', 'checklength', '名称长度必须在2-16位之间！', 0, 'callback', 3, array(2, 16)),
        array('g_id,a_id,name','checkUnique','年级名称不能重复',self::MUST_VALIDATE,'callback',self::MODEL_BOTH), // 自定义函数验证规则 
	    // array('memo','require','备注不能为空'),
	);

        public function checklength($str, $min, $max) {
            preg_match_all("/./u", $str, $matches);
            $len = count($matches[0]);
            if ($len < $min || $len > $max) {
                return false;
            } else {
                return true;
            }
        }

        public function checkUnique($data){
        $areaInfo = self::getWhereInfoEx('g_id', array('a_id'=>$data['a_id'],'name'=>$data['name']));
        // var_dump($areaInfo);die;
        // var_dump($data['d_id']);
        if($areaInfo&&$areaInfo['g_id'] != $data['g_id'])
            return false;
        else
            return true;
        }

		public function queryListEX($fields,$where = null, $orderby = null, $page = 0, $pagesize = 0, $groupby = null,$data_auth = null) {
        if ($page){ 
            $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
        }
        if($page){
            $ret['list'] = self::field($fields)->where($where)->order($orderby)->page($page,$pagesize)->group($groupby)->select();
        }else{
            $ret['list'] = self::field($fields)->where($where)->order($orderby)->group($groupby)->select();
        }

       //dump(self::getLastSql());
    
        if ($page == 0) {
            $ret ["count"] = count ( $ret ["list"] );
        } else {
            $ret ["count"] = $this->getCount (); //获得记录总数
        }
		
		$USER_STATUS = C("USER_STATUS");
        $SchoolArea = D("SchoolArea")->where($where)->getField("a_id,name");

		
		
        foreach ($ret['list'] as $k => $v) {
          
            $ret['list'][$k]['valid'] = $USER_STATUS[$v['valid']]?$USER_STATUS[$v['valid']]:'';
            $ret['list'][$k]['a_id'] = $SchoolArea[$v['a_id']]?$SchoolArea[$v['a_id']]:'';
        }
				
        return $ret;
    }

}