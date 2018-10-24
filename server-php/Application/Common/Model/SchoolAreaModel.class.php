<?php
namespace Common\Model;
use Common\Model\BaseModel;
class SchoolAreaModel extends BaseModel{

	protected $_validate =array(
		array('name','require','校区名称不能为空'),
		// array('name','','校区名称已存在',1,'unique',1),
        array('name', 'checklength', '名称长度必须在2-16位之间！', 0, 'callback', 3, array(2, 16)),
        // array(‘mobile’,’11′,’手机号码长度必须11位 ‘,self::MUST_VALIDATE,’length’,self::MODEL_INSERT)
        array('a_id,s_id,name','checkUnique','校区名称不能重复',self::MUST_VALIDATE,'callback',self::MODEL_BOTH), // 自定义函数验证规则 

    );

    public  function checklength($str, $min, $max) {
        preg_match_all("/./u", $str, $matches);
        $len = count($matches[0]);
        if ($len < $min || $len > $max) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 校区名称不能重复
     * @param unknown $data
     */
    public function checkUnique($data){
        $areaInfo = self::getWhereInfoEx('a_id', array('s_id'=>$data['s_id'],'name'=>$data['name']));
    
        if($areaInfo&&$areaInfo['a_id'] != $data['a_id']){
            return false;
        }else{
            return true;
        }
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
      
		
		
        foreach ($ret['list'] as $k => $v) {
          
            $ret['list'][$k]['valid'] = $USER_STATUS[$v['valid']]?$USER_STATUS[$v['valid']]:'';
            
        }
				
        return $ret;
    }

	
}