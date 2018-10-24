<?php
namespace Common\Model;
use Common\Model\BaseModel;
class SafetyRegionModel extends BaseModel{

	protected $_validate =array(

         array('name','require','请输入围栏名称'),
         array('name', 'checklength', '名称长度必须在2-8位之间！', 0, 'callback', 3, array(2, 8)),
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
    public function queryListEx($fields,$where = null, $orderby = null, $page = 0, $pagesize = 0, $groupby = null,$data_auth = null) {
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
        // var_dump($where);die;
        $Student = D('Student')->where($where)->getField('stu_id,imei_id');
        $TYPE = C("TYPE");
        foreach ($ret['list'] as $k => $v) {
          
            // $ret['list'][$k]['valid'] = $USER_STATUS[$v['valid']]?$USER_STATUS[$v['valid']]:'';
            // $ret['list'][$k]['type'] = $TYPE[$v['type']]?$TYPE[$v['type']]:'';
            $ret['list'][$k]['imei_id'] = $Student[$v['stu_id']]?$Student[$v['stu_id']]:'';
            $ret['list'][$k]['point'] = $v['point'];
        }
                
        return $ret;
    }

	
}