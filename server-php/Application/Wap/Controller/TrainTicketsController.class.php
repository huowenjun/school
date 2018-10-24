<?php
/*
*   注册
*by yumeng date 20170413
*/

namespace Wap\Controller;

use Think\Controller;
use think\session\driver\Redis;
use Vendor\Elife;

class TrainTicketsController extends Controller
{
    public static function base()
    {
        import('Vendor.Elife.TrainTickes');
        $trainTickes = new \TrainTickes();
        return $trainTickes;
    }
    /*
     * 站点预加载
     * redis set存储形式 trainAddress中文地名 或者 trainAddress拼音地名
     * 数据库 存储形式 name中文地名 全拼拼音地名
     */
    private  function preloadingStation()
    {
        set_time_limit(0);
        $redis = new \Redis();
        $redis->connect('127.0.0.1', '6379');
        $redis->auth('School1502');
        $TrainTickesList = self::base()->queryAllTrainTickesMessage();
        $list = $TrainTickesList->stations->station;
        for ($i = 0; $i < count($list); $i++) {
            $redis->set("trainAddress:" . $list[$i]->quanpin . "", "" . $list[$i]->name . "");
            $redis->set("trainAddress:" . $list[$i]->name . "", "" . $list[$i]->name . "");
        }
        $PlaneStationM = M('PlaneStation');
        for ($i = 0; $i < count($list); $i++) {
//            $bool = $PlaneStationM->where(['name'=>$list[$i]->quanpin])->find();
//            if(!$bool){
                $PlaneStationM ->add(['name'=>$list[$i]->name,'quanpin'=>$list[$i]->quanpin,'type'=>6]);
//            }
        }
    }

    public function index()
    {
        $this->display();
    }

    /*
     * 站点联想
     */
    public function associateTrainTickets()
    {
        $address = trim(I('post.addr'));
        $type = I('post.type');
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        if ($address == "") {
            $this->success($address);
        }
        if ($type == 6) {
            //查看redis集合是否有站点词汇
            $redis = redis_instance();
            $data = $redis->keys("trainAddress:" . $address . "*");
            $len = count($data);
            if(!$len){
                $trainList = M('PlaneStation')
                    ->field('name,quanpin as code')
                    ->where(" type = 6 and quanpin like '{$address}%' or name like '{$address}%'")
                    ->select();//查到的数据存到redis
                foreach ($trainList as $key=>$value) {
                    $redis->set("trainAddress:" . $value['name'] . "", "" . $value['name'] . "");
                    $redis->set("trainAddress:" . $value['code'] . "", "" . $value['code'] . "");
                }
            }else{
                for ($i = 0; $i < $len; $i++) {
                    $trainList[$i]['name'] = $redis->get("$data[$i]");
                    $trainList[$i]['code'] = $redis->get("$data[$i]");
                }
            }
            $this->success($trainList);
        } else {
            //飞机
            $addr_info = M('PlaneStation')
                ->field('name,code')
                ->where("type = 7 and quanpin like '{$address}%' or name like '{$address}%'")
                ->select();
            $this->success($addr_info);
        }
    }

    /*
     * 开始搜索
     * 参数：$from：始发地 $date：发车时间 $to：目的地
     */
    public function startSearch()
    {
        $from = I('post.from', '0', 'string');
        $date = I('post.date', '0', 'string');
        $to = I('post.to', '0', 'string');
        $dateNow = date('Y-m-d');
        if (!$from) {//不可为空
            $this->error('请输入始发地');
        }
        if (!$date) {//不可为空
            $this->error('请选择发车时间');
        }
        if (strtotime($date) < strtotime($dateNow)) {//必须大于当前时间
            $this->error('时间已过，请选择发车时间');
        }
        if (!$to) {//不可为空
            $this->error('请输入目的地');
        }

        $TrainTickesList = $this->base()->queryTrainTickesToStation($from, $date, $to);
        foreach ($TrainTickesList->trainlines->trainline as $key => $value) {

            $len = count($value->trainSeats->trainSeat);
            for ($k = 0; $k <= $len; $k++) {
                for ($j = $len - 1; $j > $k; $j--) {
                    if ($value->trainSeats->trainSeat[$j]->seatPrice < $value->trainSeats->trainSeat[$j - 1]->seatPrice) {
                        $temp = $value->trainSeats->trainSeat[$j];
                        $value->trainSeats->trainSeat[$j] = $value->trainSeats->trainSeat[$j - 1];
                        $value->trainSeats->trainSeat[$j - 1] = $temp;
                    }
                }
            }
        }
        $this->success($TrainTickesList->trainlines);
    }

    /**
     * 火车票详情
     * 参数：$trainNumber:车次
     */
    public function OneTrainTickets()
    {
        $from = I('post.from', '0', 'string');
        $date = I('post.date', '0', 'string');
        $to = I('post.to', '0', 'string');
        $dateNow = date('Y-m-d');
        $trainNumber = I('post.trainNumber', '0', 'string');
        if (!$from) {//不可为空
            $this->error('请输入始发地');
        }
        if (!$date) {//不可为空
            $this->error('请选择发车时间');
        }
        if (strtotime($date) < strtotime($dateNow)) {//必须大于当前时间
            $this->error('时间已过，请选择发车时间');
        }
        if (!$to) {//不可为空
            $this->error('请输入目的地');
        }
        if (!$trainNumber) {
            $this->error('非法请求');
        }
        $TrainTickesList = $this->base()->queryTrainTickesToStation($from, $date, $to);
        $len = count($TrainTickesList->trainlines->trainline);
        for ($i = 0; $i < $len; $i++) {
            if ($TrainTickesList->trainlines->trainline[$i]->trainNumber == $trainNumber) {
                $this->success($TrainTickesList->trainlines->trainline[$i]);
            }
        }
    }

    /*
     * 订单填写
     * * 参数：from：始发地
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
    public function OrderWrite()
    {
        $seatCode = I('post.seatCode');//航班舱位编码
        $flightCompanyCode = I('post.flightCompanyCode');//航空公司二字码

        $from = I('post.from', '0', 'string');
        $date = I('post.date', '0', 'string');
        $to = I('post.to', '0', 'string');
        $dateNow = date('Y-m-d');
        $trainNumber = I('post.trainNumber', '0', 'string');
        $contactName = I('post.contactName', '0', 'string');
        $contactTel = I('post.contactTel', '0', 'string');
        $passagers = I('post.passagers', '0', 'string');
        $TrainTickesList = $this->base()->queryTrainTickesList();
        $itemIdTrain = $TrainTickesList->items->item[0]->itemId;
        $startTime = I('post.startTime', '0', 'string');
        $TrainTickesSafe = $this->base()->queryTrainTickesSafe();
        $itemIdInsur = $TrainTickesSafe->items->item[0]->itemId;
        if (!$from) {//不可为空
            $this->error('请输入始发地');
        }
        if (!$date) {//不可为空
            $this->error('请选择发车时间');
        }
        if (strtotime($date) < strtotime($dateNow)) {//必须大于当前时间
            $this->error('时间已过，请选择发车时间');
        }
        if (!$to) {//不可为空
            $this->error('请输入目的地');
        }
        if (!$trainNumber) {
            $this->error('非法请求');
        }
        if (!$contactName) {
            $this->error('请输入联系人姓名');
        }
        if (!$contactTel) {
            $this->error('请输入联系人电话');
        }
        if (!$passagers) {
            $this->error('请选择购票人员');
        }
        $pEx = explode(';', $passagers);
        $len = count($pEx);
        if ($len > 5) {
            $this->error('最多选择5个人');
        }
        if (!$itemIdTrain) {
            $this->error('火车票商品编号错误');
        }
        if (!$startTime) {
            $this->error('发车时刻错误');
        }
        if ($seatCode && $flightCompanyCode) {//飞机订单
            $tickesInfo = json_encode($_POST);
            $this->success($tickesInfo);
        } else {
            //火车票订单
            $tickesInfo = $this->base()->bespeakTrainTickes($from, $to, $date, $trainNumber, $itemIdInsur, $contactName, $contactTel, $passagers, $itemIdTrain, $startTime);
            if ($tickesInfo['status'] == 0) {//接口调用失败
                $this->error($tickesInfo['info']);
            } else {
                $this->success($tickesInfo['info']);
            }
        }

    }

    /*
     * 支付后查看我的火车票
     */
    public function Info()
    {
        $userId = I('get.user_id', 0, 'int');
        $listData = D('dealOrder')->field('sch_deal_order.id,sch_deal_order.user_id,sch_deal_order.memo,sch_deal_order.total_price,sch_deal_order.is_refund,sch_payment_notice.notice_sn')->where(['sch_deal_order.user_id' => $userId, 'order_status' => 3, 'type' => 6])->join('sch_payment_notice on sch_deal_order.id=sch_payment_notice.order_id')->select();
        if (empty($listData)) {
            $this->success('您还没有票，快去淘票');
        }
        $this->success($listData);
    }


    /**
     * 火车票退款接口，返回为1时，到公司账户
     * $orderNos  订单号
     * 返回为1，退款成功
     */
    public function RefundTrainTickets()
    {
        $orderNos = I('post.orderNos', '0', 'string');
        $data = $this->base()->backTrainTickesOrder($orderNos);
        $this->success($data);
    }


}