<?php
/*
 * 系统—角色—角色列表
 * 生成时间：2015-01-01
 * 作者：qianzhiqiang
 * 修改时间：
 * 修改备注：
 */
namespace Admin\Controller\Sys;
use Admin\Controller\BaseController;
class GroupController extends BaseController{
   public function index(){
      // dump($this->authRule());
      // dump($this->getGroupId());
   
       $this->display();
   }
   //查看角色
   public function view_rule(){
       $id = I('get.id');
       $Rule = D('Rule');
       $where['id'] = array('EQ',$id);
       $Group = D('AdminGroup')->where($where)->find();
       if (!empty($Group['rules'])) {
         $where1['id'] = array('in',$Group['rules']);
         $where1['pid'] = array('EQ',0);
         $result = $Rule->where($where1)->select();
         $arr = "";
         foreach ($result as $key => $value) {
            $arr = $arr."<div class=\"box\">";
              $arr = $arr."<div class=\"box-header with-border\">";
                $arr = $arr."<h3 class=\"box-title\">{$value['title']}</h3>";
              $arr = $arr."</div>";
              $arr = $arr."<div class=\"box-body\">";
              $where2['pid'] = $value['id'];
              $where2['id'] = array('in',$Group['rules']);
              $result1 = $Rule->where($where2)->select();
             
              foreach ($result1 as $key1 => $value1) {
                 $arr = $arr."<button type=\"button\" class=\"btn btn-info\" data-color=\"#39B3D7\" data-opacity=\"0.95\">";
                 $arr = $arr."{$value1['title']}";
                 $arr = $arr."</button>";
              }
              $arr = $arr."</div>";
            $arr = $arr."</div>";
         }
       }else{
        $arr = "该角色没有权限！";
       }
       
       $this->arr=$arr;
     $this->display();
   }
   public function query(){
       $page = I("get.page",1);
       $pagesize = I("get.pagesize",$this->PAGE_SIZE);
       $sort = I('get.sort','id');
       $order = $sort. ' ' . I('get.order','desc');
       $Group = D('AdminGroup');
       $result = $Group->queryListEx('a.*,count(c.user_id) as count',NULL,$order,$page,$pagesize,'');

       $this->success($result);
   }

   public function get(){
       $id = I('get.id/d',0);
       //输入角色列表
       $auth_group = D('AdminGroup');
       $group_info = $auth_group->getInfo($id);

       if($id > 0 && empty($group_info)){
           $this->error('角色不存在');
       }
       $this->success($group_info);
   }

   //数据插入
   public function edit(){
       $AdminGroup = D('AdminGroup');
       $id  = I('post.id/d',0);
       //dump($data);
       $data['title'] = trim(I('post.title'));
       $data['desc'] = trim(I('post.desc'));
       $data['id'] = $id;
       $data['status'] = I('post.status');
       $data['lock'] = 1;

       if(!$AdminGroup->create($data)){
           $this->error($AdminGroup->getError());
       }else{
           if($id > 0){
               $ret = $AdminGroup->save($data);
           }else{
               $ret = $AdminGroup->add($data);
           }
           $content = ($id>0? '编辑':'新建') . '角色成功,ID:'. ($id>0? $data['m_id']:$ret);
           if($ret === false){
               D('SystemLog')->writeLog($this->getModule(),$content,$this->getUserId(),$this->getType(),$this->getGroupId(),0,2);
               $this->error($AdminGroup->getError());
           }else{
               D('SystemLog')->writeLog($this->getModule(),$content,$this->getUserId(),$this->getType(),$this->getGroupId(),0,1);
               $this->success($content,U('/Admin/Sys/Group/index'));
           }
       }
   }
    
   //暂停
   public function pause(){
       $uIds = I('post.id');
   
       $arrUids = explode(',', $uIds);
       if(empty($arrUids)){
           $this->error('参数错误');
       }
       $AdminGroup = D('AdminGroup');$okCount=0;
       foreach ($arrUids as $k => $v) {
           $data['status'] = 2;  //2 禁用
           $where['id'] = $v;
            
           $ret=$AdminGroup->where($where)->save($data);
          // echo $AdminGroup->_sql();
           if($ret==1){

               $okCount++;  //处理成功记录数
           }
   
       }
       //写log
       $content = "暂停".$okCount."条角色信息";
       $state = 1;
       if($okCount==0){
           $state = 2;
       }
       D('SystemLog')->writeLog($this->getModule(),$content,$this->getUserId(),$this->getType(),$this->getGroupId(),0,$state);

       $this->success('暂停成功'.$okCount.'条记录');
   }
   
   //启用
   public function start(){
       $uIds = I('post.id');
   
       $arrUids = explode(',', $uIds);
       if(empty($arrUids)){
           $this->error('参数错误');
       }
       $AdminGroup = D('AdminGroup');$okCount=0;
       foreach ($arrUids as $k => $v) {
           $data['status'] = 1;  //2 禁用
           $where['id'] = $v;
            
           $ret=$AdminGroup->where($where)->save($data);
           // echo $arrUser->_sql();
           if($ret==1){

               $okCount++;  //处理成功记录数
           }
   
       }
       //写log
       $content = "启用".$okCount."条角色信息";
       $state = $okCount > 0?1:2;
       D('SystemLog')->writeLog($this->getModule(),$content,$this->getUserId(),$this->getType(),$this->getGroupId(),0,$state);
       $this->success('启用成功'.$okCount.'条记录');
   }
   
   //删除
   public function delete(){
       $uIds = I('post.id');
   
       $arrUids = explode(',', $uIds);
       if(empty($arrUids)){
           $this->error('参数错误');
       }
       $AdminGroup = D('AdminGroup');$okCount=0;
       foreach ($arrUids as $k => $v) {
            
           $where['id'] = $v;
           // $where['id'] = array('GT',5);
           $ret=$AdminGroup->where($where)->delete();
           // echo $arrUser->_sql();
           if($ret==1){

               $okCount++;  //处理成功记录数
           }
   
       }
       //写log
       $content = "删除".$okCount."条角色信息";
       $state = $okCount > 0?1:2;
       D('SystemLog')->writeLog($this->getModule(),$content,$this->getUserId(),$this->getType(),$this->getGroupId(),0,$state);
       $this->success('删除成功'.$okCount.'条记录');
   }

    /**
     * 功能权限
     */
    public function rule(){

        $id = I('get.id/d');
        $AdminGroup = D('AdminGroup');
        $where['id'] = $id;
        $groupInfo = $AdminGroup->getWhereInfoEx('*',$where);
        if($id > 0 && empty($groupInfo)){
            $this->error('角色ID参数错误');
        }

        // dump($groupInfo);
        $this->group=$groupInfo;

        $Rule = D('Rule');
        $ruleList = $Rule->getAllList();
        $arrIds = explode(',',$groupInfo['rules']);
        $ruleData = $Rule->getRuleList($ruleList,0,$arrIds);
        $this->ruleData = json_encode($ruleData);
        $this->rules = $groupInfo['rules'];
        $this->display();
    }

    public function rule_handle(){
        $data['id'] = I('post.id/d');
        $rules = I('post.rules');
        $data['rules'] = $rules;
        //dump($data);
        $AdminGroup = D('AdminGroup');
        if(!$AdminGroup->create($data)){
            $this->error($AdminGroup->getError());
        }else{
            $where['id'] = $data['id'];
            $ret = $AdminGroup->where($where)->save($data);
            //echo $AdminGroup->_sql();
            $content = "权限设置";
            if($AdminGroup->getError() == ''){
                //写log
                D('SystemLog')->writeLog($this->getModule(),$content,$this->getUserId(),$this->getType(),$this->getGroupId(),0,1);
                $this->success('保存成功',U('/Meet/Sys/Group/index'));
            }else{
                D('SystemLog')->writeLog($this->getModule(),$content,$this->getUserId(),$this->getType(),$this->getGroupId(),0,2);
                $this->error($AdminGroup->getError());
            }

        }
    }

    /**
    *查看角色权限
    */
    public function view(){

    }

    public function data_handle(){

    }
   
   
}
