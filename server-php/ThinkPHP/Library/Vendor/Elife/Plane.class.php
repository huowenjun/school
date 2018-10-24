<?php

/**
 *斑马开放平台 PHP调用示例
 *适用于PHP5.1.2及以上版本
 */
include_once(dirname(__FILE__) . '/autoload.php');

class Plane extends ElifeBase
{
    /*
     * 查询飞机票标准商品列表： 5500301飞机票
        1.辅助接口，确认当前用户被允许使用该标准商品，获取标准商品编号用于货源确认或机票预订；
        2.该商品相对稳定，如已经确认并获取过，可作缓存；
        3.如返回为空，请联系上级确认飞机票商品对接状态以避免下单失败。
     * */
    private function airItemsList()
    {
        $client = new OpenClient;
        $req = new AirItemsListRequest;
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
     * 查询飞机票商品货源详情(如果已确认当前已对接有效货源，可忽略此步)：
        1.辅助接口，查询飞机票标准商品货源详情，包含供货商，价格等；
        2.如返回为空，请联系上级确认保险商品对接状态以避免下单失败。
     * */
    public function airItemDetail()
    {
        $client = new OpenClient;
        $itemId = $this->airItemsList()['info']->itemId;
        $req = new AirItemDetailRequest;
        $req->setItemId($itemId);
        $infoObj = $client->execute($req, $this->accessToken);
        $res = is_object($infoObj); //true存在商品列表  false不存在商品列表
        if ($res) {
            $data['status'] = 1;
            $data['info'] = $infoObj->item;
        } else {
            $data['status'] = 0;//未存在先关产品
            $data['info'] = $infoObj[0]->message;
        }
        return $data;
    }

    /*
     * 查询所有飞机站点信息，信息固定，可缓存客户端用于站点的展示与筛选匹配。
     * */
    public function airStationsList()
    {
        $client = new OpenClient;
        $req = new AirStationsListRequest;
        $infoObj = $client->execute($req, $this->accessToken);
        $res = is_object($infoObj); //true存在商品列表  false不存在商品列表
        if ($res) {
            $data['status'] = 1;
            $data['info'] = $infoObj->stations->station;
        } else {
            $data['status'] = 0;//未存在先关产品
            $data['info'] = $infoObj[0]->message;
        }
        return $data;
    }

    /*
     *查询航线列表,重要提示：
        1、以下票面价不包含机场建设费、燃油附加税。
        2、特殊舱位价格变化快，以下特殊舱位价格仅作参考，以订座成功后价格为准。
     * @param $from/$to 起点/终点三字码 从$this->airStationsList中获取
     * */
    public function airLinesList($from = '', $to = '', $date = '')
    {
        $client = new OpenClient;
        $req = new AirLinesListRequest;
        $req->setFrom($from);//起飞站点(机场)三字码
        $req->setTo($to);//目的地站点(机场)三字码
        $req->setDate($date);
        $itemId = $this->airItemsList()['info']->itemId;
        $req->setItemId($itemId);//选择的飞机票标准商品编号
        $infoObj = $client->execute($req, $this->accessToken);
        $res = is_object($infoObj); //true存在商品列表  false不存在商品列表
        if ($res) {
            $data['status'] = 1;
            $data['info'] = $infoObj->airlines->airline;
        } else {
            $data['status'] = 0;//未存在先关产品
            $data['info'] = $infoObj[0]->message;
        }
        return $data;
    }

    /*
     * 购票须知
        1、机票相关业务处理时间为8:00-22:00
        2、机票业务不做改签处理
        3、机票支持在线申请退废票和自愿退票，非自愿退票请根据平台规则联系客服处理
        4、出票当日提交"退废票"，具体以航司规定为准
        5、请仔细核对用户身份证号以及姓名，以免造成不必要的损失
        6、行程单(报销凭证)机场提供打印服务
        7、目前不支持飞机票保险货源
     *
     * */
    public function payBill($ticket_info)
    {
        $client = new OpenClient;
        $req = new AirOrderPayBillRequest;
        $itemId = $this->airItemsList()['info']->itemId;
        $req->setItemId($itemId);
        $req->setContactName($ticket_info['contactName']);//订票联系人
        $req->setContactTel($ticket_info['contactTel']);//联系电话
        $req->setDate($ticket_info['date']);//出发日期
        $req->setFrom($ticket_info['from']);//起点三字码
        $req->setTo($ticket_info['to']);//终点三字码
        $req->setCompanyCode($ticket_info['flightCompanyCode']);//航空公司编码
        $req->setFlightNo($ticket_info['trainNumber']);//航班号
        $req->setSeatCode($ticket_info['seatCode']);//舱位编码
        //乘客信息,以英文逗号分隔,依次为：乘客姓名,乘客手机号码,乘客证件号码,多个乘客时以英文分号分隔，同一笔订单最多支持五个乘客
        $req->setPassagers(trim($ticket_info['passagers'],','));
        $infoObj = $client->execute($req, $this->accessToken);
        $res = is_object($infoObj); //true存在商品列表  false不存在商品列表
        if ($res) {
            $data['status'] = 1;
            $data['info'] = $infoObj;
        } else {
            $data['status'] = 0;//未存在先关产品
            $data['info'] = $infoObj;
        }
        return $data;

    }


}


?>