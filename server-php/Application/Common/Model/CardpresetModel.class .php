<?php
namespace Common\Model;
use Common\Model\BaseModel;
class CardpresetModel extends BaseModel{

    protected $_validate =array(

    );

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

        return $ret;
    }


}