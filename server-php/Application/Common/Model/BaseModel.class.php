<?php
namespace Common\Model;
use \Think\Model;
class BaseModel extends Model{
    
    
    /**
     * 查询列表
     * @param string $fields 字段
     * @param array $where where条件数组: array('field1'=>'value1','field2'=>'value2')
     * @param array $orderby orderby数组: array('field1'=>'ASC','field2'=>'DESC')
     * @param int $page 页码
     * @param int $pagesize 每页数量
     * @param array $groupby
     * @param array $data_auth 数据权限
     *  * @return uret['count']  总数    $ret['list']  查询结果列表
     */
    public function queryList($fields,$where = null, $orderby = null, $page = 0, $pagesize = 0, $groupby = null,$data_auth = null) {
        if ($page){ 
            $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
        }
        if($page){
            $ret['list'] = self::field($fields)->where($where)->order($orderby)->page($page,$pagesize)->group($groupby)->select();
        }else{
            $ret['list'] = self::field($fields)->where($where)->order($orderby)->group($groupby)->select();
        }

       // dump(self::getLastSql());

     

        
        if ($page == 0) {
            $ret ["count"] = count ( $ret ["list"] );
        } else {
            $ret ["count"] = $this->getCount (); //获得记录总数
        }
        return $ret;
    }
    
    /**
     * 当查询中使用了 SQL_CALC_FOUND_ROWS 时,调用本方法可得到记录总数
     *
     */
    protected function getCount() {
        $result = self::query ( "select FOUND_ROWS() as count" );
        return $result[0]["count"];
    }
    
    /**
     * 获取信息
     * @param int $classId ID
     * @return array 信息
     */
    public function getInfo($id){
        $map = array();
        $key = self::getPk();
        $map[$key] = $id;
        return $this->getWhereInfo($map);
    }

    public function getInfoEx($field,$where,$order=null) {
        if($order)
            return self::field($field)->where($where)->order($order)->find();
        else
            return self::field($field)->where($where)->find();
    }
    
    /**
     * 获取信息
     * @param array $where 条件可以是字符串也可以是数组
     * @param array $order 排序  
     * @return array 信息
     */
    public function getWhereInfo($where,$order=null) {
        if($order)
            return self::where($where)->order($order)->find();
        else 
            return self::where($where)->find();
    }
    
    public function getWhereInfoEx($field,$where,$order=null) {
        if($order)
            return self::field($field)->where($where)->order($order)->find();
        else
            return self::field($field)->where($where)->find();
    }
    		
    public function setData(&$record){
    
    }
    
    public function setListData(&$list){
        foreach ($list as $key => $value)
            $this->setData($list[$key]);
    }
    
    
    public function array_sort($array, $key, $order=SORT_ASC){
        $new_array = array();
        $sortable_array = array();
    
        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $key) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }
    
            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }
             
            foreach ($sortable_array as $k => $v) {
                $new_array[] = $array[$k];
            }
        }
        return $new_array;
    }
    
    /**
     * 将列表中field字段根据table表批量获取ID,Name
     * @param unknown $list
     */
    public function getNameList(&$list,$field,$table,$id,$name){
        $arrName = array();
        foreach ($list as $value){
            $arrName[] = $value[$field];
        }
        $arrName = array_unique($arrName);//去重
        //代理商列表
        $where[$id] = array('in',implode(',',$arrName));
        $arrName = M($table)->where($where)->getField("$id,$name");
        
        return $arrName;
    }
    
    public function getInfoFromCache($id){
        $name = $this->getModelName().'_'.$id;
        $value = S($name);
        if(!$value){//cache 不存在到数据库中取
            $value = $this->getInfo($id);
            if($value){
                S($name,$value);
            }
        }
        return $value;
    }

    public function getInfoFromCacheEx($id,$where){
        $name = $this->getModelName().'_'.$id;
        $value = S($name);
        if(!$value){//cache 不存在到数据库中取
            $value = $this->getWhereInfoEx('*',$where);
            if($value){
                S($name,$value);
            }
        }
        return $value;
    }

    /*
	 *下拉类表给js传数据
	 */
    public function getSelectList($value, $text,$selected,$orderby = null,$arr_where=null) {
        $retList = array();
        $key = $this->getPk();
        if ($orderby == null)
            $orders = $key . " asc" ;
        else
            $orders = $orderby;
        $list = $this->queryList ( "$value,$text",$arr_where , $orders );
        if ($list ['count'] > 0) {
            foreach ( $list ['list'] as $v ) {
                $arrTemp = array();
                $arrTemp['value'] = $v[$value];
                $arrTemp['text'] = $v[$text];
                $arrTemp['selected'] = $v[$value] == $selected ? 1:0;
                $retList [] = $arrTemp;
            }
        }
        return $retList;
    }


    /**
     * 检查下级表是否存在业务
     * @param $module
     * @param $id
     * @param $field
     * @return bool
     */
    public function hasBusiness($module,$id,$field){

        $v = D($module)->where(array($field=>$id))->getField($id);
        return $v? true:false;
    }

    protected function getColor($rgb){
        if(!$rgb) return '';
        if($rgb[0] != '#') { //rgb()
            $len = strlen($rgb);
            $rgb = substr($rgb,4,$len-5);
            $arrRGB = explode(',',$rgb);
            $rgb = "#";
            $rgb .= str_pad(dechex($arrRGB[0]), 2, "0", STR_PAD_LEFT);
            $rgb .= str_pad(dechex($arrRGB[1]), 2, "0", STR_PAD_LEFT);
            $rgb .= str_pad(dechex($arrRGB[2]), 2, "0", STR_PAD_LEFT);
        }
        return $rgb;

    }

	
}