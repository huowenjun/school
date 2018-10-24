<?php

namespace Common\Model;
use Common\Model\BaseModel;
class SystemLogModel extends BaseModel{
    


    /**
     * 
     * @param string $moduleName 模块名称
     * @param string $content  操作内容
     * @param int $uId 操作者
     * @param number $result =1成功，0-失败
     * @param userType 用户类型 1 系统用户 0 学校用户
     * @param groupId 角色id
     * @param recordType 记录类型 0 平台 1 APP
     */
	public function writeLog($moduleName,$content,$uId,$userType,$groupId,$recordType,$result=1){
	    $data['sl_module_name'] = $moduleName;
	    $data['sl_content'] = $content;
	    $data['user_type'] = $userType;
	    $data['group_id'] = $groupId;
	    $data['user_id'] = $uId;
	    $data['sl_result'] = $result;
	    $data['record_type'] = $recordType;
	    $data['sl_operate_time'] = date('Y-m-d H:i:s');
		$data['sl_ip'] = get_client_ip();
	    self::add($data);
	} 
	
	
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
	public function queryListEX($fields,$where = null, $orderby = null, $page = 0, $pagesize = 0, $groupby = null,$data_auth = null) {
	    if ($page){
	        $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
	    }
	    if($page){
	        $ret['list'] = self::field($fields)->where($where)->order($orderby)->page($page,$pagesize)->group($groupby)->select();
	    }else{
	        $ret['list'] = self::field($fields)->where($where)->order($orderby)->group($groupby)->select();
	    }

		//var_dump($this->getLastSql());
	
	    if ($page == 0) {
	        $ret ["count"] = count ( $ret ["list"] );
	    } else {
	        $ret ["count"] = $this->getCount (); //获得记录总数
	    }
	
	   

	    $agentList = self::getUserList($ret['list']);
        $urlList = self::getUrlName($ret['list']);
        $recoroType = C('RECORD_TYPE');
        $groupList = D('AdminGroup')->getField('id,title');
	    foreach($ret['list'] as $key => $value){
	        $ret['list'][$key]['username'] =  isset($agentList[$value['user_id']]['username'])?  $agentList[$value['user_id']]['username']:'无';
	        $ret['list'][$key]['name'] =  isset($agentList[$value['user_id']]['name'])?  $agentList[$value['user_id']]['name']:'无';
			$arrModule = explode('/',$value['sl_module_name']);
			$value['sl_module_name'] = $arrModule[0] . '/' . $arrModule[1] . '/' . $arrModule['2'] . '/' . $arrModule['3'] . '/index';
            $ret['list'][$key]['urlname'] =  isset($urlList[$value['sl_module_name']])?  $urlList[$value['sl_module_name']]:'无';
            $ret['list'][$key]['record_type'] =  isset($recoroType[$value['record_type']])?  $recoroType[$value['record_type']]:'无';
            $ret['list'][$key]['group_name'] =  isset($groupList[$value['group_id']])?  $groupList[$value['group_id']]:'未设置';
	    }
	    //End
	    // dump($urlList);
	     
	    return $ret;
	}
	
	
	
	/**
	 * 取操作用户列表
	 * @param unknown $list
	 */
	public function getUserList(&$list){
	    $arrUser = array();
	    foreach ($list as $value){
	        $arrUser[] = $value['user_id'];
	    }
	    $arrUser = array_unique($arrUser);//去重
	    //用户列表
	    $where['user_id'] = array('in',implode(',', $arrUser));
	    $arrUser = M('user','think_')->where($where)->getField('user_id,username,name');
	
	    return $arrUser;
	}

    /*
    取URL模块名称
    */
	public function getUrlName(){
        $arrUrl = M('auth_rule','think_')->getField('name,title as urlname');
        return $arrUrl;
    }
}