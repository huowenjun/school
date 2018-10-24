<?php

namespace Admin\Controller\DealOrder;

use Admin\Controller\BaseController;

class WithdrawDepositController extends BaseController
{
    public function index()
    {
        $this->display();
    }
    //查询
    public function query()
    {
        $sort = I('get.sort', 'id');
        $order = $sort . ' ' . I('get.order', 'desc');
        $page = I('get.page', 1);
        $pagesize = I('get.pagesize', $this->PAGE_SIZE);
        $cardholder = I('get.cardholder');
        // var_dump($cardholder);
        $isPay = I('get.is_pay');
        $UserRefund = D('UserRefund');
        if ($isPay == 99) {
            $map['is_pay'] = 0;
        }elseif ($isPay == 1) {
            $map['is_pay'] = 1;
        }elseif ($isPay == 2) {
            $map['is_pay'] = 2;
        }
        if (!empty($cardholder)) {
            $map['cardholder'] = array('like',"%$cardholder%");
        }
        $result = $UserRefund->queryListE('a.*,b.card_no,b.bankname,b.cardholder', $map, $order, $page, $pagesize, '');
        // var_dump($result);die;
        $this->success($result);
    }

    public function get(){
        $id = I('get.id');
        $UserRefund = D('UserRefund');
        $Card = M('BankCard');
        $map['id'] = $id;
        $info1 = $UserRefund->where(array('id'=>$id))->find();
        $where['id'] = $info1['bank_id'];
        $info2 = $Card->where($where)->find();
        // var_dump($info2);die;
        $arr = array();
        $arr['card_no'] = $info2['card_no'];
        $arr['bankname'] = $info2['bankname'];
        $arr['cardholder'] = $info2['cardholder'];
        $arr['money'] = $info1['money'];
        $arr['memo'] = $info1['memo'];
        $arr['reply'] = $info1['reply'];
        $arr['is_pay'] = $info1['is_pay'];
        $arr['create_time'] = $info1['create_time'];
        // var_dump($arr);die;
        if ($id > 0 && empty($info1)) {
            $this->error('不存在');
        }
        $this->success($arr);
    }

    //同意
    public function agree(){
        $UserRefund = D('UserRefund');
        $id = I('post.id');
        $info = $UserRefund->where(array('id'=>$id))->find();
        $reply = I('post.reply');//同意原因
        $data['reply'] = $reply;
        $data['is_pay'] = 1;
        if (!empty($info)) {
            $ret = $UserRefund->where(array('id'=>$id))->save($data);
            $this->success('操作成功!'); 
        }else{
            $this->error('非法操作!');
        }

    }

    //驳回
    public function reject(){
        $UserRefund = D('UserRefund');
        $id = I('post.id');
        $info = $UserRefund->where(array('id'=>$id))->find();
        $reply = I('post.reply');//驳回原因
        $data['reply'] = $reply;
        $data['is_pay'] = 2;
        $UserRefund->startTrans();
        if (!empty($info)) {
            $ret = $UserRefund->where(array('id'=>$id))->save($data);
            if ($ret) {
                $where['u_id'] = $info['user_id'];
                $accountInfo = D('userAccount')->where($where)->find();
                $data1['account'] = $info['money'] + $accountInfo['account'];
                $ret1 = D('userAccount')->where($where)->save($data1);
                if ($ret1) {
                    $data2['log_info'] = '提现驳回';
                    $data2['log_time'] = date('Y-m-d H:i:s');
                    $data2['money'] = $info['money'];
                    $data2['user_id'] = $info['user_id'];
                    $data2['type'] = 3;
                    $data2['order_id'] = $accountInfo['id'];
                    M('userBill')->add($data2); 
                    $UserRefund->commit();
                    $this->success('操作成功!'); 
                }else{
                    $UserRefund->rollback();
                    $this->error('操作失败!');
                }
            }else{
                $UserRefund->rollback();
                $this->error('操作失败!');
            } 
        }else{
            $this->error('操作失败!');
        }
    }
}
