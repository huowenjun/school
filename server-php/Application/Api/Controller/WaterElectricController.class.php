<?php

namespace Api\Controller;

use Api\Controller\BaseController;

class WaterElectricController extends BaseController
{
    protected function init()
    {

    }

    //获取流量充值产品列表
    public function getItemList()
    {
        $prov = I('post.prov');
        $city = I('post.city');
        if (!$prov || !$city) {
            $this->error('参数错误');
        }
        import('Vendor.Elife.WaterElectric');
        $VideoCard = new \WaterElectric();
        $data = $VideoCard->waterCoalItem($prov, $city);

        $this->success($data);
    }

    //查询水电煤欠费账单(检测相关户号是否可以充值)
    public function getAccountInfo()
    {
        $itemId = I('post.item_id');
        $account = I('post.memo');
        if (!$itemId || !$account) {
            $this->error('参数错误');
        }
        import('Vendor.Elife.WaterElectric');
        $VideoCard = new \WaterElectric();
        $dataObj = $VideoCard->getAccountInfo($itemId, $account);
        if (!$dataObj->status) {
            $this->error($dataObj->message);
        }
        $this->success($dataObj);

    }

}

?>