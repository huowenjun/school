<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-报警信息
 */
namespace Admin\Controller\Scy;

use Admin\Controller\BaseController;

class WarnInfoController extends BaseController
{
    /**
     *报警信息
     */
    public function index()
    {

        $this->display();
    }


    public function query()
    {

        $stu_id = I('get.stu_id');
        $war_status = I('get.war_status');

        $page = I("post.page", 1);
        $pagesize = I("post.pagesize", $this->PAGE_SIZE);
        $sort = I('post.sort', 'wm_id');
        $order = $sort . ' ' . I('post.order', 'desc');
        $where['war_status'] = $war_status;  //报警状态 0 未处理 1 已处理',
        $where['is_push'] = 1;
        $imei = M('Student')->where(array('stu_id' => $stu_id))->getField('imei_id');
        if($imei){
            $where['imei'] = $imei;
        }else{
            $this->error('该学生未绑定学生卡');
        }
        $dates = date('Y-m-d');
        $where['create_time'] = array('between', array($dates . ' 00:00:00', $dates . ' 23:59:59'));
        $WarningMessage = D('WarningMessage');
        $res = $WarningMessage->queryListEx('*', $where, $order, $page, $pagesize, '');
        if ($res) {
            $this->success($res['list']);
        } else {
            $this->error('该学生不存在');
        }
    }

//
    public function edit()
    {
        $where['wm_id'] = I('get.wm_id/d', 0);
        $pagesize = I("post.pagesize", $this->PAGE_SIZE);
        $page = I('get.page');
        $sort = I('post.sort', 'wm_id');
        $order = $sort . ' ' . I('post.order', 'desc');
        // if($wm_id > 0 && empty($area_info)){
        // $this->error('不存在');
        // }
        $WarningMessage = D('WarningMessage');
        $result = $WarningMessage->queryListEx('*', $where, $order, $page, $pagesize, '');
        $this->success($result['list']);
    }

    //处理报警信息
    public function Deal_with()
    {

        $where['wm_id'] = I('get.wm_id/d', 0);
        $data['war_status'] = '1';
        $WarningMessage = D('WarningMessage');
        $res = $WarningMessage->where($where)->save($data);
        $this->success(U('/Admin/Scy/WarnInfo/index'));

    }

    //   public function query1(){
    //     $stu_id = I('get.stu_id');
    //     $war_status = 1;
    //   $WarningMessage = D('WarningMessage');
    //   $res = $WarningMessage->where(array("stu_id"=>$stu_id,"war_status"=>$war_status))->select();
    //   // echo $Trail->_sql();
    //   if($res){
    // $this->success($res);
    //   }else{
    //     $this->error('该学生不存在');
    //   }
    //  }

    public function get_list()
    {
        $ids = $this->authRule();
        $where = "";
        if ($this->getType() == 1) {
            $where['s_id'] = $ids['s_id'];
        } elseif ($this->getType() == 3) {
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
        } elseif ($this->getType() == 4) {
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
            $where['stu_id'] = array('IN', $ids['stu_id']);
        } else {

        }

        $res = D('Student')->where($where)->getField('stu_id as id , stu_name as value');
        $this->success($res);
    }
}