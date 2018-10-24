<?php
namespace Common\Model;
use Common\Model\BaseModel;
class TrailModel extends BaseModel{

	protected $_validate =array(
		
	);
	
    public function queryListEx($fields,$where = null, $orderby = null, $page = 0, $pagesize = 0, $groupby = null,$data_auth = null) {
        if ($page){ 
            $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
        }
        if($page){
            $ret['list'] = self::field($fields)->where($where)->order($orderby)->page($page,$pagesize)->group($groupby)->select();
        }else{
            $ret['list'] = self::field($fields)->where($where)->order($orderby)->group($groupby)->select();
        }
        if ($page == 0) {
            $ret ["count"] = count ( $ret ["list"] );
        } else {
            $ret ["count"] = $this->getCount (); //获得记录总数
        }

        $TYPE = C("TYPE");
        foreach ($ret['list'] as $k => $v) {
            if ($v['type'] == 3){
                $Info = origin2gps($v['longitude'],$v['latitude']);
                $ret['list'][$k]['longitude'] =$Info[0];
                $ret['list'][$k]['latitude'] =$Info[1];
            }
            $ret['list'][$k]['sex'] = M("Student")
                ->cache(true)
                ->where("imei_id = '{$where['imei']}'")
                ->getField('sex');
            $ret['list'][$k]['type'] = $TYPE[$v['type']]?$TYPE[$v['type']]:'';
            if (0<=$ret['list'][$k]['signal1']&&$ret['list'][$k]['signal1']<=12  ) {
            $ret['list'][$k]['signal'] = 20;
            }elseif (13<=$ret['list'][$k]['signal1']&&$ret['list'][$k]['signal1']<=18) {
            $ret['list'][$k]['signal'] = 40;
            }elseif (19<=$ret['list'][$k]['signal1']&&$ret['list'][$k]['signal1']<=23) {
            $ret['list'][$k]['signal'] = 60;
            }elseif (24<=$ret['list'][$k]['signal1']&&$ret['list'][$k]['signal1']<=28) {
            $ret['list'][$k]['signal'] = 80;
            }elseif (29<=$ret['list'][$k]['signal1']&&$ret['list'][$k]['signal1']<=31) {
            $ret['list'][$k]['signal'] = 100;
            }

        }
                
        return $ret;
    }

	
}