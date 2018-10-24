<?php

/**
 *斑马开放平台 PHP调用示例
 *适用于PHP5.1.2及以上版本
 */
include_once(dirname(__FILE__) . '/autoload.php');

class MobileFlow extends ElifeBase
{
    /*
     * 根据充值流量查询流量商品列表
     * */
    public function flowItemsList2($mobile, $unit)
    {
        $client = new OpenClient;
        $req = new BmMobileFlowItemsList2Request;
        $req->setMobileNo($mobile);
        $req->setFlow($unit);
        $infoObj = $client->execute($req, $this->accessToken);
        $res = is_object($infoObj); //true存在商品列表  false不存在商品列表
        if ($res) {
            $data['status'] = 1;
            $data['info'] = $infoObj->items->item[0];
        } else {
            $data['status'] = 0;//未存在先关产品
            $data['info'] = $infoObj[0]->message;
        }
        return $data;
    }

    /*
     *调用此接口，充值手机流量订单，并且返回订单详情
     * */
    public function payBill($mobile, $itemId, $trade_no = '')
    {
        if (!$mobile || !$itemId) {
            return false;
        }
        $client = new OpenClient;
        $req = new BmMobileFlowPayBillRequest;
        $req->setItemId($itemId);
        $req->setMobileNo($mobile);
        $req->setOuterTid($trade_no);
        $req->setCallback('http://school.xinpingtai.com/Api/OrderNotify/telephoneChargeNotify');
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