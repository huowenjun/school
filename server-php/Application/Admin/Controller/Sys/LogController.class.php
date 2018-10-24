<?php
/*
 * 系统—操作日志
 * 生成时间：2015-01-01
 * 作者：qianzhiqiang
 * 修改时间：
 * 修改备注：
 */
namespace Admin\Controller\Sys;
use Admin\Controller\BaseController;
class LogController extends BaseController{
    public function index(){
        $this->sdate=date('Y-m-d',strtotime('-7 day'));
        $this->edate=date('Y-m-d');
        $this->display();
    }
    public function query(){

        $page = I("get.page",1);
        $pagesize = I("get.pagesize",$this->PAGE_SIZE);
        $sort = I('get.sort','sl_id');
        $order = $sort. ' ' . I('get.order','desc');
        $sdate = I('get.sdatetime',date('Y-m-d',strtotime('-7 day')));
        $edate = I('get.edatetime',date('Y-m-d'));
        $sl_content = I('get.content');
        $userid=I('get.user_id/d');

        $group_id = I('get.group_id');
        $sl_ip = I('get.sl_ip');
        $username = I('get.username');
        $name = I('get.name');
    
        if (!empty($group_id)) {
            $where['group_id'] = $group_id;
        }
        if ($sl_ip!='') {
            $where['sl_ip'] = $sl_ip;
        }

        if ($username!= '' || $name!= '') {
            if ($username!='') {
                $where1['username'] = array('like','%'.$username.'%');
            }
            if ($name!= '') {
                $where1['name'] = array('like','%'.$name.'%');
            }
            if ($username!='' && $name!='') {
                $where1['_logic'] = 'OR';
            }
            
            $userInfo = D('AdminUser')->where($where1)->select();
            //echo D('AdminUser')->_Sql();
            $user_id = "";
            foreach ($userInfo as $key => $value) {
                $user_id = $user_id.$value['user_id'].",";
            }
            $where['user_id'] = array('IN',rtrim($user_id,','));
        }

        $Syslog=D('SystemLog');
        
        //查询操作用户列表
        $arrUser = M('user','think_')->getField('user_id,username as username');
        $this->arruser=$arrUser;


        
        if (!empty($sl_content)) {
            $where['sl_content'] = array('like','%'.$sl_content.'%');
        }

        if ($userid > 0) $where['user_id'] = $userid;
        if($sdate && $edate) $where['sl_operate_time'] = array('BETWEEN', array($sdate,$edate));
        elseif ($sdate) $where['sl_operate_time'] = array('EGT', $sdate);
        elseif ($edate) $where['sl_operate_time'] = array('ELT', $sdate);

        //var_dump($user_id);die;
        $result = $Syslog->queryListEX('*',$where,$order,$page,$pagesize,'');
        
        
        $this->success($result);
    }
}
