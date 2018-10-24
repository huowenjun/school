<?php

namespace Admin\Controller\DealOrder;

use Admin\Controller\BaseController;

class DealOrderController extends BaseController
{
    public function index()
    {
        $this->display();
    }

    //查询接口
    public function query()
    {
        $page = I('get.page', 1);
        $pagesize = I('get.pagesize', 10);
        $user_name = I('get.user_name');
        $sort = I('get.sort','id').' '.I('get.order','desc');
        $deal_name = I('get.deal_name');
        $type = I('get.type');//订单类型 0零钱充值 1学生卡充值 2话费充值
        $order_status = I('get.order_status');//订单状态 0:未支付 1:已支付(过期) 2:已支付(无库存) 3:成功 4:话费充值中
            $is_paid = I('get.is_paid');//订单状态 0:未支付 1:已支付(过期) 2:已支付(无库存) 3:成功 4:话费充值中
        $create_time = I('get.create_time');//下单时间
        $pay_time = I('get.pay_time');//支付时间
        $notice_sn = I('get.notice_sn');//订单号
            $is_refund = I('get.is_refund');//退款
        $whereStr = '';

        if ($user_name!= NUll) {
            $whereStr['a.user_name'] = $user_name;
        }
        if ($deal_name!= NUll) {
            $whereStr['a.deal_name'] = $deal_name;
        }
        if ($type!= NUll) {
            $whereStr['a.type'] = $type;
        }
        if ($order_status!= NUll) {
            $whereStr['a.order_status'] = $order_status;
        }
        if ($is_paid != NUll) {
            $whereStr['b.is_paid'] = $is_paid;
        }
        if ($notice_sn!= NUll) {
            $whereStr['b.notice_sn'] = $notice_sn;
        }
        if ($is_refund!= NUll) {
            $whereStr['a.is_refund'] = $is_refund;
        }
        if ($create_time!= NUll) {
            $whereStr['a.create_time'] = array(array('egt', $create_time), array('elt', date('Y-m-d',strtotime($create_time)) . ' 23:59:59'));
        }
        if ($pay_time!= NUll) {
            $whereStr['a.pay_time'] = array(array('egt', $pay_time ), array('elt', date('Y-m-d',strtotime($pay_time)) . ' 23:59:59'));
        }

        $res['list'] = M('DealOrder')->alias('a')
            ->field('SQL_CALC_FOUND_ROWS  a.*,b.notice_sn,b.outer_notice_sn,b.is_paid')
            ->join(' __PAYMENT_NOTICE__ b on a.id = b.order_id ')
            ->where($whereStr)
            ->order($sort)
            ->page($page, $pagesize)
            ->select();

//        SELECT SQL_CALC_FOUND_ROWS  a.*,b.notice_sn,b.outer_notice_sn,b.is_paid FROM sch_deal_order a INNER JOIN  sch_payment_notice b on a.id = b.order_id   WHERE b.is_paid = '1' AND a.is_refund = '1' ORDER BY id desc LIMIT 0,10
        $paymentType = C('PAYMENT_TYPE');
        $orderStatus = C('ORDER_STATUS');
        $isSuccess = C('IS_EFFECT');
        $orderType = C('ORDER_TYPE');
        foreach ($res['list'] as $key => $value) {
            $res['list'][$key]['payment_id'] = $paymentType[$value['payment_id']];
            $res['list'][$key]['order_status'] = $orderStatus[$value['order_status']];
            $res['list'][$key]['is_success'] = $isSuccess[$value['is_success']];
            $res['list'][$key]['is_refund'] = $isSuccess[$value['is_refund']];
            $res['list'][$key]['is_paid'] = $value['is_paid'] ? '已支付':'未支付';
            $res['list'][$key]['type'] = $orderType[$value['type']];
        }
        $result = M('DealOrder')->query("select FOUND_ROWS() as count");
        $res['count'] = $result[0]['count'];
        $this->success($res);


    }

    //详情
    //查询接口
    public function get()
    {
        $id = I('get.id');
        $page = I('get.page', 1);
        $pagesize = I('get.pagesize', 10);

        $whereStr = '';
        $whereStr['a.id'] = $id;

        $res = M('DealOrder')->alias('a')
            ->field('SQL_CALC_FOUND_ROWS  a.*,b.notice_sn,b.outer_notice_sn,b.is_paid')
            ->join(' __PAYMENT_NOTICE__ b on a.id = b.order_id ')
            ->where($whereStr)
            ->order('a.id desc')
            ->page($page, $pagesize)
            ->find();
        $paymentType = C('PAYMENT_TYPE');
        $orderStatus = C('ORDER_STATUS');
        $isSuccess = C('IS_EFFECT');
        $orderType = C('ORDER_TYPE');

        $res['payment_id'] = $paymentType[$res['payment_id']];
        $res['order_status'] = $orderStatus[$res['order_status']];
        $res['is_success'] = $isSuccess[$res['is_success']];
        $res['is_refund'] = $isSuccess[$res['is_refund']];
        $res['type'] = $orderType[$res['type']];
        $res['is_paid'] = $res['is_paid'] ? '已支付':'未支付';

        $this->success($res);
    }

    //获取交易账单
    public function getBillList()
    {
        $user_id = I('get.user_id');
        if(!$user_id){
            $this->error('参数错误');
        }
        $whereStr['user_id'] = $user_id;
        $orderby = "id desc";
        $pagesize = I("get.pagesize", 10);
        $page = I('get.page',1);

        $result = queryList(M("UserBill"), '*', $whereStr, $orderby, $page, $pagesize);
        foreach ($result['content'] as $key => $value) {
            if ($value['type'] == 0) {
                //获取充值支付方式
                $payment_id = M("DealOrder")->where("id = '{$value['order_id']}'")->getField("payment_id");
                $result['content'][$key]['payment_id'] = C('PAYMENT_TYPE')[$payment_id];
            } else {
                $result['content'][$key]['payment_id'] = '零钱';
            }

            $result['content'][$key]['type'] = C('ORDER_TYPE')[$value['type']];
        }
        $ret['list'] =  $result['content'];
        $ret['count'] =  $result['total_count'];

        $this->success($ret);
    }
    //获取账单详情
    public function getBillDetail()
    {
        $id = I("post.id",753);
        $info = M("UserBill")->where("id = '{$id}'")->find();
        if (!$info) {
            $this->success("账单不存在");
        }

        $order_id = $info["order_id"];
        $whereStr['refund.user_id'] = $info['user_id'];
        $whereStr['refund.id'] = $order_id;

        if ($info['type'] == 3) {
            //提现订单
            $result = M("UserRefund")
                ->alias('refund')
                ->field("refund.*,card.card_no,card.card_type,card.bankname")
                ->join('left join sch_bank_card card on card.id = refund.bank_id')
                ->where($whereStr)->find();
            //'未审核','通过','驳回','成功'
             $result['is_pay'] = C('REFUND_STATUS')[$result['is_pay']];
        } else {
            //充值/购买订单
            $result = M("DealOrder")
                ->alias('refund')
                ->field("refund.*,notice.notice_sn")
                ->join("__PAYMENT_NOTICE__ notice on refund.id = notice.order_id")
                ->where($whereStr)->find();
            $payment = C('PAYMENT_TYPE');
            $result['payment_id'] = $payment[$result['payment_id']];
            if ($result['payment_id'] == null) {
                $result['payment_id'] = '零钱';
            }
            $result['order_status'] = C('ORDER_STATUS')[$result['order_status']];
            $result['is_refund'] = C('IS_EFFECT')[$result['is_refund']];
            $result['is_success'] = C('IS_EFFECT')[$result['is_success']];
            $result['type'] = C('ORDER_TYPE')[$result['type']];
        }


        $this->success($result);
    }
}
