<?php

namespace Api\Controller;

use Api\Controller\BaseController;

class MobileFlowController extends BaseController
{
    protected function init()
    {

    }

    //获取流量充值产品列表
    public function getItemList()
    {
        $mobile = I('post.memo');
        $product = array('30M', '50M', '100M', '200M', '500M', '1G');
        if (!$mobile) {
            $this->success('请输入正确手机号');
        }

        //查询商品itemId
        import('Vendor.Elife.MobileFlow');
        $VideoCard = new \MobileFlow();
        //18511214006 15235058018
        $info = array();
        foreach ($product as $key => $value) {
            $item = $VideoCard->flowItemsList2($mobile, $value);
            $info[] = $item['info'];
        }
        $this->success($info);

    }

}

?>