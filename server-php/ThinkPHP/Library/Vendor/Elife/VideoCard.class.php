<?php

/**
 *斑马开放平台 PHP调用示例
 *适用于PHP5.1.2及以上版本
 */
include_once(dirname(__FILE__) . '/autoload.php');

class VideoCard extends ElifeBase
{
    /*
     * 查询视频卡直充商品列表
     * 非必选步骤，查询视频卡直充商品列表：
        1.查询上级对接的标准视频卡商品列表
        2.可放弃此步骤，直接根据充值卡号直接生成订单(知道商品编号的前提下)。
     * */
    public function videoCardList()
    {
        $client = new OpenClient;
        $req = new BmVideoCardItemsListRequest;
        $res = $client->execute($req, $this->accessToken);
        return $res;
    }

    /*
     * 充值视频卡订单
     *调用此接口，充值视频卡订单，并且返回订单详情
     * */
    public function payBill($mobile, $item)
    {
        if (!$mobile || !$item) {
            return false;
        }
        $client = new OpenClient;
        $req = new BmVideoCardPayBillRequest;
        $req->setAccount($mobile);
        $req->setItemId($item);
        $res = $client->execute($req, $this->accessToken);
    }


}


?>