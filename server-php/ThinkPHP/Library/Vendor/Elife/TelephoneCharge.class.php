<?php

/**
 *斑马开放平台 PHP调用示例
 *适用于PHP5.1.2及以上版本
 */
include_once(dirname(__FILE__) . '/autoload.php');

class TelephoneCharge extends ElifeBase
{

    /*
     * 查询单个话费直充商品
     * 非必选步骤，查询单个话费充值商品：
        1.返回指定面值，手机号所在区域下优先级最高商品，优先级："市>省>全国，固定面值>任意充"
        2.在同样充值金额下，满足客户选择不同商品需求，可选择与 "查询话费直充商品列表"接口分场景使用
        3.可放弃此步骤，直接根据手机号码、充值金额直接生成订单(知道商品编号的前提下)。
     * */
    public function getItemInfo($mobile, $money)
    {
        if (!$mobile || !$money) {
            return false;
        }
        $client = new OpenClient;
        $req = new BmRechargeMobileGetItemInfoRequest;
        $req->setMobileNo($mobile);
        $req->setRechargeAmount($money);
        $infoObj = $client->execute($req, $this->accessToken);
        if ($infoObj->itemId > 0) {//存在相关产品
            $data['status'] = 1;
            $data['info'] = $infoObj;
        } else {
            $data['status'] = 0;//未存在先关产品
            $data['info'] = $infoObj[0]->message;
        }
        return $data;
    }

    /*
     * payBill话费充值订单
     * 调用此接口，充值话费订单，并且返回订单详情
     * */
    public function payBill($mobile, $money, $outer_id = '')
    {
        if (!$mobile || !$money) {
            return false;
        }
        $client = new OpenClient;
        $req = new BmRechargeMobilePayBillRequest;
        $req->setMobileNo($mobile);
        $req->setRechargeAmount($money);
        $req->setOuterTid($outer_id);
        $req->setCallback('http://school.xinpingtai.com/Api/OrderNotify/telephoneChargeNotify');//回调地址
        $infoObj = $client->execute($req, $this->accessToken);

        if (is_object($infoObj)) {
            $data['status'] = 1;
            $data['info'] = $infoObj;
        } else {
            $data['status'] = 0;
            $data['info'] = $infoObj[0]->message;
        }

        return $data;
    }


}


?>