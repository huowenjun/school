<?php

/**
 *斑马开放平台 PHP调用示例
 *适用于PHP5.1.2及以上版本
 */
include_once(dirname(__FILE__) . '/autoload.php');

class WaterElectric extends ElifeBase
{
    /*
     * 查询水电煤类标准商品列表
     * 查询上级对接的标准商品列表（信息只包含商品编号与名称），以缩小标准商品选择范围，提高下单成功率。
     * 注意：下单时该标准商品是否能正常使用仍限制于上级对于货源的策略控制及货源本身的实时状态。
     * */
    public function waterCoalItem($prov, $city)
    {
        if (!$prov || !$city) {
            return false;
        }
        $client = new OpenClient;
        $req = new BmDirectRechargeWaterCoalItemListRequest;
        $req->setItemName($prov);
        $req->setCity($city);
        $infoObj = $client->execute($req, $this->accessToken);
        $data = $infoObj->items->item;
        foreach ($data as $key => $val) {
            $str = $val->itemName;
            if (strpos($str, '电费')) {
                $val->type = 'electric';
            } elseif (strpos($str, '燃气')) {
                $val->type = 'gas';
            } elseif (strpos($str, '水费')) {
                $val->type = 'water';
            }
        }
        return $data;
    }

    /*
     * 查询指定单一水电煤商品的所有标准属性
     *
     * */
    private function waterCoalItemProps($itemId)
    {
        if (!$itemId) {
            return false;
        }
        $client = new OpenClient;
        $req = new BmDirectRechargeWaterCoalItemPropsListRequest;
        $req->setItemId($itemId);
        $infoObj = $client->execute($req, $this->accessToken);
        if ($infoObj->itemId) {
            return $infoObj;
        }

        return false;
    }

    /*
     * 查询水电煤欠费账单
     * 兼容qianmi.elife.directRecharge.lifeRecharge.getAccountInfo接口查询功能
     * $itemId 缴费单位item_id
       $account 户号
     * */
    public function getAccountInfo($itemId, $account)
    {
        if (!$itemId || !$account) {
            $data['status'] = 0;//未存在先关产品
            $data['info'] = '参数错误';
            return $data;
        }
        $ItemProps = $this->waterCoalItemProps($itemId);
        $ItemInfo = $ItemProps->itemProps->itemProp;//属性详情
        if (!$ItemProps) {
            $data['status'] = 0;//未存在先关产品
            $data['info'] = '未获取水电煤商品标准属性';
            return $data;
        }
        $client = new OpenClient;
        $req = new BmDirectRechargeWaterCoalGetAccountInfoRequest;
        $req->setItemId($ItemProps->itemId);//标准商品编号
        $req->setAccount($account);//客户编号
        $req->setProjectId($ItemProps->cid);//费用类别 水费c2670，电费c2680，气费c2681，(属性查询接口中返回的参数cid)
        foreach ($ItemInfo as $key => $value) {
            if ($value->type == 'SPECIAL') {
                $req->setModeId($value->vid);//缴费方式V编号 (属性查询接口中返回的参数itemProps-"type": "SPECIAL"下的vid)
            } elseif ($value->type == 'PRVCIN') {
                $req->setProvince($value->vname);//省名称(后面不带"省"，属性查询接口中返回的参数itemProps-"type": "PRVCIN"下的vname)
            } elseif ($value->type == 'CITYIN') {
                $req->setCityId($value->vid);//城市V编号(属性查询接口中返回的参数itemProps-"type": "CITYIN"下的vid)
                $req->setCity($value->vname);//市名称(后面不带"市"，属性查询接口中返回的参数itemProps-"type": "CITYIN"下的vname)
            } elseif ($value->type == 'BRAND') {
                $req->setUnitId($value->vid);//缴费单位V编号(属性查询接口中返回的参数itemProps-"type": "BRAND"下的vid)
                $req->setUnitName($value->vname);//缴费单位名称(属性查询接口中返回的参数itemProps-"type": "BRAND"下的vname)
            }
        }

        $req->setModeType(2);//缴费方式：1是条形码 2是户号
        $res = $client->execute($req, $this->accessToken);
        return $res;
    }

    /*
     * 水电煤充值订单接口，返回订单详情
     * $itemId 商品编号
     * $account 户号
     * $money   金额
     * $outer_id 内部订单
     *
     * */
    public function payBill($itemId, $account, $money, $outer_id = '')
    {
        $client = new OpenClient;
        $req = new BmDirectRechargeLifeRechargePayBillRequest;
        $req->setItemId($itemId);
        $req->setItemNum($money);
        $req->setRechargeAccount($account);
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