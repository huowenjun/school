<?php
namespace Common\Model;
use Common\Model\BaseModel;
class TeacherGroupModel extends BaseModel{

	protected $_validate =array(
		array('group_name','require','请输入组名称'),
	    array('t_id','require','请选择老师'),
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

       // dump(self::getLastSql());
    
        if ($page == 0) {
            $ret ["count"] = count ($ret ["list"]);
        } else {
            $ret ["count"] = $this->getCount (); //获得记录总数
        }
		$SEX_TYPE = C("SEX_TYPE");
      
		 $SchoolArea = D("SchoolArea")->getField("a_id,name");
		 $Dept = D('Dept')->getField("d_id,name");
		 // $Area = D('SchoolArea')->getField("a_id,name");
		 // echo "<pre>";;
		 // var_dump($AreaRegion);
		
				
        return $ret;
    }
	
}