<?php

namespace Wap\Controller;

use Think\Controller;

class AppHookController extends Controller
{
    //APP生活缴费列表页
    public function livingPayment()
    {
        $this->display();
    }

    //话费
    public function telephonePayment()
    {
        $this->display();
    }

    //流量费
    public function flowPayment()
    {
        $this->display();
    }

    //水费
    public function waterPayment()
    {
        $this->display();
    }

    //电费
    public function electricPayment()
    {
        $this->display();
    }

    //煤气费
    public function gasPayment()
    {
        $this->display();
    }

    //缴费机构列表页
    public function operatorList()
    {
        $this->display();
    }

    //水费、电费、燃气费 支付信息页
    public function propertyPaymentSubmenu()
    {
        $this->display();
    }

    //淘票
    public function buyTickets()
    {
        $this->display();
    }

    //订单页面
    public function order()
    {
        $this->display();
    }
}