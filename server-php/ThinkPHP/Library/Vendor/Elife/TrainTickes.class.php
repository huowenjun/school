<?php
/**
 *斑马开放平台 PHP调用示例
 *适用于PHP5.1.2及以上版本
 * 火车票调用接口
 */

include_once(dirname(__FILE__) . '/autoload.php');
class TrainTickes extends ElifeBase
{
    public $appKey = 10001771;
    public $appSecret ='cVXMBG0JMLWqvnBmZpoL7Ph8nmOqABpv';
    private function client(){
        $client = new OpenClient;
        $client->appKey = $this->appKey;
        $client->appSecret = $this->appSecret;
        return $client;
    }
    /*查询火车票标准商品列表
     *辅助接口
     *@return json数据
     */
    public function queryTrainTickesList(){
        $req = new TrainItemsListRequest;
        $res = $this->client()->execute($req, $this->accessToken);
        return $res;
    }
    /*
     * 查询火车票保险标准商品列表
     * 辅助接口
     * @return json数据
     */
    public function queryTrainTickesSafe(){
        $req = new TrainInsursListRequest;
        $res = $this->client()->execute($req,$this->accessToken);
        return$res;
    }
    /*
     * 查询火车票商品货源详情
     * 1.辅助接口，查询火车票标准商品货源详情，包含供货商，价格等；
     * 2.如返回为空，请联系上级确认保险商品对接状态以避免下单失败。
     * itemId   火车票标准商品编号 5500201
     * return 0: 商品编号错误
     *       -1：获取商品信息失败
     *         json返回数据
     */
    public function queryTrainTickesGoods($itemId){
        if(!$itemId){
            return 0;
        }
        $req = new TrainItemDetailRequest;
        $req->setItemId("$itemId");
        $res = $this->client()->execute($req, $this->accessToken);
        if(is_object($res)){
            return json_encode($res,JSON_UNESCAPED_UNICODE);
        }else{
            return -1;
        }
    }
    /*
     * 查询所有火车站点信息
     * 查询所有火车站点信息，信息固定，可缓存客户端用于站点的展示与筛选匹配。
     * @return json所有站点信息
     */
    public function queryAllTrainTickesMessage(){
        $req = new TrainStationsListRequest;
        $res = $this->client()->execute($req, $this->accessToken);
        return $res;
    }
    /**
     * 查询火车票站到站信息(含票价)
     * 查询指定“起点站”到“终点站”在指定日期所有火车票站次信息列表，包含座位与票价信息等；不支持指定无座。
     * 参数：$from：始发地 $date：发车时间 $to：目的地
     * return 0:不可为空
     */
    public function queryTrainTickesToStation($from,$date,$to){
        if(empty($from)){
            return 0;
        }
        if(empty($date)){
            return 0;
        }
        if(empty($to)){
            return 0;
        }
        $req = new TrainLinesListRequest;
        $req->setFrom("$from");
        $req->setTo("$to");
        $req->setDate("$date");
        $res = $this->client()->execute($req, $this->accessToken);
        return $res;
    }
    /**
     * 预订火车票
     * 预订：
    1.保险商品目前只支持千米官方非人工充值的货源，请确认货源对接正确以避免下单失败。
    2.操作结果不代表预订结果，最终预订结果由货源方决定。 购票须知：
    1.火车票相关业务处理时间为8:00-22:00
    2.发车前4小时以内退款不做处理，请自行去火车站退票
    3.不受理改签业务，需用户自行在发车前去车站办理
    4.火车票新版上线，支持购买卧铺票、硬座，不支持指定无座
    5.禁止代理商向用户收取票面价格以外的手续费
    6.提交订单时如提示身份证待核验，乘车人需带二代身份证到火车站通过身份认证才能网上购票。
     * 参数：from：始发地
     *       to : 目的地
     *      date:出发日期
     *      trainNumber:车次
     *      itemIdInsur：保险商品编号(最终投保份数为乘客总数)。可为空
     *      contactName：联系人姓名
     *      contactTel：联系人电话
     *      passagers：乘客信息,以英文逗号分隔,依次为：
    乘客姓名,
     *                          乘客手机号码,
     *                          乘客证件号码,
     *                          选择座位类型名称,
     *                          多个乘客时以英文分号分隔，
     *                          同一笔订单最多支持五个乘客
     *      itemIdTrain：火车票商品编号
     *      startTime：发车时刻
     */
    public function bespeakTrainTickes($from,$to,$date,$trainNumber,$itemIdInsur,$contactName,$contactTel,$passagers,$itemIdTrain,$startTime){
        $arr = explode(';',$passagers);
        $len = count($arr);
        if($len>6){
            return 0;
        }
        $req = new TrainOrderCreateRequest;
        $req->setFrom("$from");
        $req->setTo("$to");
        $req->setDate("$date");
        $req->setTrainNumber("$trainNumber");
        $req->setStartTime("$startTime");
        $req->setContactName("$contactName");
        $req->setContactTel("$contactTel");
        $req->setPassagers("$passagers");
        $req->setItemIdTrain("$itemIdTrain");
        $req->setItemIdInsur("$itemIdInsur");
        $infoObj = $this->client()->execute($req, $this->accessToken);
        $res = is_object($infoObj); //true存在商品列表  false不存在商品列表
        if ($res) {
            $data['status'] = 1;
            $data['info'] = $infoObj;
        } else {
            $data['status'] = 0;//未存在先关产品
            $data['info'] = $infoObj[0]->message;
        }

        return $data;
    }
    /**
     * 支付火车票订单
     * 支付预定过的火车票订单：
    1.支付需在预定成功后半小时内完成，否则自动取消预定。
    2.支付状态前提：预定完成（state-2），未支付（billstate-0）且发车时间未过期。
     * $tradeNo:订单编号
     */
    public function payTrainTickes($tradeNo){
        $trainInfo = $this->orderInfo($tradeNo);
        $state = $trainInfo->state;
        $billstate = $trainInfo->billState;
        $carStartTime = strtotime($trainInfo->startTime);
        $nowdate = strtotime(date('Y-m-d H:i:s'));
        if($state==2){
            if($billstate==0){
                if($carStartTime>$nowdate){
                    $req = new TrainOrderPayRequest;
                    $req->setTradeNo("$tradeNo");
                    $res = $this->client()->execute($req, $this->accessToken);
                    return 1;//支付成功
                }
                return -1;//"订单过期,请重新下单"
            }
            return 0;//"已支付"
        }
        return -2;//"请您先预定"

    }
    /**
     * 退订火车票订单
     * 交易完成，取消订单，支持选择子单退订：
    1.退订订单状态前提：已完成（state-1），已支付（ billstate 1）；
    2.人工充值的火车票，退订时，不自动退保险，需供货商人工操作退款；
    3.非人工充值的火车票，退订时，自动退保险。
     *$orderNos:订单子单编号，多个时以‘,’分隔
     */
    public function backTrainTickesOrder($orderNos){
        $trainInfo = $this->orderInfo($orderNos);
        $state = $trainInfo->state;
        $billstate = $trainInfo->billState;
        if($state==1){
            if($billstate==1){
                $req = new TrainOrderRefundRequest;
                $req->setOrderNos("$orderNos");
                $res = $this->client()->execute($req, $this->accessToken);
                return $res;
            }else{
                return '您还未支付';
            }
        }
            return '您还未买票';

    }
    /**
     * 查询火车票订单列表
     * 查询火车票订单列表，分页返回
     * tradeNo:订单主单号
     * startTime:订单开始时间
     * endTime：订单结束时间
     * sort：按订单生成时间排序标志，默认降序 格式: asc-升序，desc-降序
     * supUserId：供货商编号
     * pageNo：当前页码 从0开始
     * pageSize：每页显示条数，最大值100
     * orderStatus：订单状态： 0-预定中 1-待支付 2-已取消 3-出票中 4-已出票 5-出票失败
     */
    public function queryTrainTickesOrderList($tradeNo="",$startTime,$endTime,$sort='desc',$pageNo=0,$pageSize=10,$orderStatus=4){
        $req = new TrainOrdersListRequest;
        $req->setStartTime("$startTime");
        $req->setEndTime("$endTime");
        $req->setSort("$sort");
        $req->setTradeNo("$tradeNo");
        $req->setPageNo("$pageNo");
        $req->setPageSize("$pageSize");
        $req->setOrderStatus("$orderStatus");
        $res = $this->client()->execute($req, $this->accessToken);
        return json_encode($res,JSON_UNESCAPED_UNICODE);
    }
    /**
     * 查看整笔订单详情
     * tradeNo:订单编号
     */
    public function orderInfo($tradeNo){
        $req = new TrainOrderDetailRequest;
        $req->setTradeNo("$tradeNo");
        $res = $this->client()->execute($req, $this->accessToken);
        return $res;
    }
}
