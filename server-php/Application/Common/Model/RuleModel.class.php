<?php
namespace Common\Model;
use Common\Model\BaseModel;
class RuleModel extends BaseModel{

	protected $_validate =array(
	    array('name','require','模块链接不能为空'),
        array('name','','模块链接已经存在',1,'unique',1),
	);
	
	protected $trueTableName  = 'think_auth_rule';

	public function queryListEx($fields,$pid=0) {
		$ret['list'] = self::field($fields)->order('pid asc,sort asc')->select();
		if($pid){ //过滤
			$parentList = array();
			$parentList[] = $pid;
			foreach($ret['list'] as $key=>$value){
				if(in_array($value['pid'],$parentList)){
					$parentList[] = $value['id'];
				}else{
					unset($ret['list'][$key]);
				}
			}

		}
		$ret ["count"] = $this->getCount (); //获得记录总数
		return $ret;
	}

	public function getParentList($id){
		$where = array();
		$where['id'] = array('NEQ',$id);
		$list = $this->where($where)->order('pid,sort')->select();
		$this->setListData($list);
		$retList = array();
		foreach($list as $value){
			$retList[] = array('id'=>$value['id'],'title'=>$value['title']);
		}
		//trace($retList);
		return $retList;

	}

	public function setListData(&$list){
		//parent list
		$parentList = array();
		foreach ($list as $key => $value){
			$parentList[$value['id']] = array('title'=>$value['title'],'hierarchy'=>$value['id']);
		}
		foreach($list as $key => $value){
			if($value['pid'] > 0){
				$list[$key]['title'] = $parentList[$value['pid']]['title'].'>>' . $value['title'];
				$list[$key]['hierarchy'] = $parentList[$value['pid']]['hierarchy'].'>>' .$value['pid'] . '>>' . $value['sort'];
				$parentList[$value['id']]['title'] = $list[$key]['title'];
				$parentList[$value['id']]['hierarchy'] = $list[$key]['hierarchy'];
			}else{
				$list[$key]['hierarchy'] = $value['sort'];
				$parentList[$value['id']]['hierarchy'] = $list[$key]['hierarchy'];
			}
		}
		//sort
		$list = $this->arraySort($list,'hierarchy');
		//dump($list);

	}

	private function arraySort($array, $key, $order=SORT_ASC){
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

	public function hasChild($id){
		$result = $this->field('count(*) as cnt')->where(array('pid'=>$id))->find();

		return $result['cnt'] > 0 ? true:false;
	}

	/**
	 * 检查pid是否自己的孩子
	 * @param $id
	 * @param $pid
	 */
	public function checkHierarchy($id,$pid){
		if($pid == 0) return false;
		$list = $this->field('id,pid')->order('pid asc,sort asc')->select();
		$parentList = array();
		$parentList[] = $id;
		foreach($list as $value){
			if(in_array($value['pid'],$parentList)){
				if($value['id'] == $pid) return true;
				$parentList[] = $value['id'];
			}
		}
		return false;
	}



	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	public function getAllList(){
		return $this->field('*')->order('pid,sort')->select();
	}
	private function getChildren(&$list,$pid){
		$arrRet = array();
		foreach($list as $value){
			if($value['pid'] == $pid){
				$arrRet[] = $value;
			}
		}
		return $arrRet;
	}

	private function isParent(&$list,$id){
		$ret = false;
		foreach($list as $value){
			if($value['pid'] == $id){
				$ret = true;
				break;
			}
		}
		return $ret;
	}


	public function getRuleList(&$list,$pid=0,$arrSelect=array()){
		$arrRet = array();
		$listChildren = $this->getChildren($list,$pid);
		foreach($listChildren as $value){
			$arrTemp = array();
			$arrTemp['id'] = $value['id'];
			$arrTemp['text'] = $value['title'];
			if(in_array($value['id'],$arrSelect)){
				$arrTemp['checked'] = true;
			}else{
				$arrTemp['checked'] = false;
			}
			if($this->isParent($list,$value['id'])){
				$arrTemp['expanded'] = true;
				$arrTemp['children'] = $this->getRuleList($list,$value['id'],$arrSelect);
			}
			$arrRet[] = $arrTemp;
		}
		return $arrRet;
	}



	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	public function getMenuList($auth,$uid,$type='Admin',$super=false){
		//模块列表
		$ruleList = self::field('*')->where('ismenu = 1')->order('pid asc,sort asc')->select();
		$parentList = array();
		foreach ($ruleList as $key => $value){
			$parentList[$value['id']] = $value['id'];
		}
		if($super){
			$ids = array();
		}else {
			//读取用户所属用户组
			$groups = $auth->getGroups($uid);
			$ids = array();//保存用户所属用户组设置的所有权限规则id
			foreach ($groups as $g) {
				$ids = array_merge($ids, explode(',', trim($g['rules'], ',')));
			}
			$ids = array_unique($ids);
		}
		$retMenuList = array();
		foreach($ruleList as $value){
			$key = str_replace("/","_",$value['name']);
			$arrTemp = $value;
			$arrTemp['url'] = U($value['name']);
			//继承
			if($value['pid'] > 0){
				$arrTemp['hierarchy'] = $parentList[$value['pid']].'>>' .$value['pid'] . '>>' . $value['sort'];
				$parentList[$value['id']] = $arrTemp['hierarchy'];
			}else{
				$arrTemp['hierarchy'] = $value['id'];
			}
			$arrTemp['selected'] = in_array($value['id'],$ids)||$super? 1:0;
			$retMenuList[$key] = $arrTemp;

		}

		//一级菜单url替换成第一个孩子的url
		foreach($retMenuList as $key => $value){
			if(strpos($value['name'],'/') === false){
				$search = $value['hierarchy'] . '>>';
				foreach($retMenuList as $v) {
					if ($v['ismenu'] ==1 && $v['selected'] == 1 && strpos($v['hierarchy'], $search) !== false ) {
						$retMenuList[$key]['url'] = $v['url'];
						break;
					}
				}
			}
		}
		return $retMenuList;

	}


	
}