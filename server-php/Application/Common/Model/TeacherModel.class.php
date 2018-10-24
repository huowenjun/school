<?php
namespace Common\Model;
use Common\Model\BaseModel;
class TeacherModel extends BaseModel{

	protected $_validate =array(
		// array('name','require','请输入教师姓名'),
        array('name','validStorage','请输入教师姓名',1,'callback',3),  //使用回调函数validStorage   
	    // array('phone','require','请输入教师电话'),
	    // array('phone','','教师电话已存在',1,'unique',3),
	    array('a_id','require','请选择校区'),

	    array('d_id','require','请选择部门'),
	    // array('education','require','请选择学历'),
	    // array('email','require','请输入邮箱'),
	    array('email','email','邮箱格式不正确',2),
	    // array('email','','邮箱已经存在！',2,'unique',1),
	    // array('phone','require','联系方式不能为空'),
	    // array('user_name','require','请输入登录帐号'),
        array('phone','validStorage','请输入教师电话',1,'callback',3),  //使用回调函数validStorage
        array('phone', 'isTel1', '电话号码格式不正确', 1, 'callback', 3),
        array('user_name','require','请输入登录帐号'),  //使用回调函数validStorage
        array('user_name','','登录账号已存在',1,'unique',3),

	);
	

     //使用回调函数validStorage
        protected function validStorage($value){
            // var_dump($value);die;
        if(empty($value)){
            return false;
        }else{
            return true;
            }
        }

         function isTel1($tel, $type = 'sj')
    {
        $regxArr = array(
            'sj' => '/^1[3578]\d{9}$/',
            // 'tel' => '/^(010|02\d{1}|0[3-9]\d{2})-\d{7,9}(-\d+)?$/',
            // '400' => '/^400(-\d{3,4}){2}$/',
        );
        if ($type && isset($regxArr[$type])) {
            return preg_match($regxArr[$type], $tel) ? true : false;
        }
        foreach ($regxArr as $regx) {
            if (preg_match($regx, $tel)) {
                return true;
            }
        }
        return false;
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
            $ret ["count"] = count ($ret ["list"]);
        } else {
            $ret ["count"] = $this->getCount (); //获得记录总数
        }
		$SEX_TYPE = C("SEX_TYPE");
        $lstStatus = C('USER_STATUS');//状态
        $Class = D('Class'); 
        $SchoolArea = D("SchoolArea")->getField("a_id,name");
        $Dept = D('Dept')->getField("d_id,name");
		
        foreach ($ret['list'] as $k => $v) {
            $ret['list'][$k]['sex'] = $SEX_TYPE[$v['sex']]?$SEX_TYPE[$v['sex']]:'';
            $ret['list'][$k]['d_id'] = $Dept[$v['d_id']]?$Dept[$v['d_id']]:'';
            $ret['list'][$k]['a_id'] = $SchoolArea[$v['a_id']]?$SchoolArea[$v['a_id']]:'';
            $Classinfo = $Class->where(array('t_id'=>$v['t_id']))->find();
            if (empty($Classinfo)) {
                 $ret['list'][$k]['charge'] = "否";
            }else{
                 $ret['list'][$k]['charge'] = "是";
            }
            $ret['list'][$k]['valid'] = $lstStatus[$v['valid']]?$lstStatus[$v['valid']]:'';

        }
				
        return $ret;
    }
	
	
}