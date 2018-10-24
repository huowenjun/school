<?php

namespace Api\Controller;

use Api\Controller\BaseController;

class TelChargeController extends BaseController
{
    /**
     * 获取充值话费状态信息
     * @param string $money 话费金额
     * @param string $mobile 手机号
     *
     * @return json   "cardid": "191404", 卡类ID
     * "cardname": "江苏电信话费100元直充",  卡类名称
     * "inprice": 98.4,  购买价格
     * "game_area": "江苏苏州电信"  手机号码归属地
     *
     * */
    public function getTelCharge()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法');
        }

        $user_id = $this->getUserId();
        $money = I("post.money");
        $mobile = I("post.mobile");
        $whereStr = array();
        if ($money > 0) {
            //判断用户余额
            $whereStr['u_id'] = $user_id;
            $account = M("UserAccount")->where($whereStr)->getField("account");
            if ($money > $account) {
                $this->error('余额不足');
            } else {
                //订单创建成功 查询话费订单详情
                $TopUpInfo = $this->getTelTopUpInfo($mobile, $money);
                if ($TopUpInfo['result']['inprice'] > $account) {
                    $this->error('余额不足');
                }
                //添加订单表
                $notice_sn = $this->addOrder($mobile, $money, $TopUpInfo['result']);
                $TopUpInfo['result']['notice_sn'] = $notice_sn;
                $this->success($TopUpInfo['result']);

            }
        } else {
            $this->error('参数错误');
        }

    }

    /**
     * 用户确认充值 执行充值
     *
     * */
    public function telTopUp()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        $access_token = I('post.access_token');//凭证
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法');
        }

        $notice_sn = I("post.notice_sn");

        //获取订单详情
        $orderInfo = M("PaymentNotice")
            ->alias("a")
            ->field("b.*,a.notice_sn")
            ->join("__DEAL_ORDER__ b on a.order_id = b.id")
            ->where("a.notice_sn = '{$notice_sn}' and a.is_paid != 1")
            ->find();


        if (empty($orderInfo)) {
            $this->error('订单不存在');
        }
        $money = intval($orderInfo['deal_price']);
        $mobile = explode(':', $orderInfo['memo'])['2'];

        //验证token
        $md5 = md5($orderInfo['notice_sn'] . 'shuhaixinxi_phone_recharge_order' . $this->getUserId() . $orderInfo['inprice']);

        if ($md5 != $access_token) {
            $this->error('充值失败');
        }
        $whereStr['u_id'] = $this->getUserId();
        $account = M("UserAccount")->where($whereStr)->getField("account");
        if ($orderInfo['total_price'] > $account) {
            $this->error('余额不足');
        }


        //检测手机号码是否能充值
        $isMobile = $this->checkMobile($mobile, $money);
        if ($isMobile['error_code'] > 0) {
            $this->error($isMobile['reason']);
        }

        //根据手机号和面值查询商品
        $isGoods = $this->getGoods($mobile, $money);
        if ($isGoods['error_code'] > 0) {
            $this->error($isGoods['reason']);
        }


        //执行手机充值
        $TopUpRes = $this->doTelTopUp($mobile, $money, $notice_sn);

        if ($TopUpRes['error_code'] > 0) {//订单提交错误
            if ($TopUpRes['error_code'] == 10014) {
                //聚合充值订单查询接口
                $TopUpStatus = $this->getTopUpOrder($notice_sn);
                if ($TopUpStatus['error_code'] == 0) {
                    //表示查询到话费充值订单
                    //进行订单回调处理
                    $res = payment_paid($notice_sn, $TopUpStatus['result']['sporder_id'], 4);
                    if ($res) {
                        //更新零钱余额表 进行账户减操作
                        $accountData['account'] = $account - $orderInfo['total_price'];
                        $res = M("UserAccount")->where($whereStr)->save($accountData);
                    }
                    $this->success('充值成功' . $res);

                }
            } else {
                $this->error($TopUpRes['reason']);
            }
        } else {
            //充值订单提交成功
            //聚合充值订单查询接口
            $TopUpStatus = $this->getTopUpOrder($notice_sn);

            if ($TopUpStatus['error_code'] == 0) {
                //表示查询到话费充值订单
                //进行订单回调处理
                $res = payment_paid($notice_sn, $TopUpStatus['result']['sporder_id'], 4);
                if ($res) {
                    //更新零钱余额表 进行账户减操作
                    $accountData['account'] = $account - $orderInfo['total_price'];
                    M("UserAccount")->where($whereStr)->save($accountData);
                }

                $this->success('充值成功');
            } else {
                $this->error($TopUpStatus['reason']);
            }
        }


    }

    public function test($notice_sn = '2017051857535210', $arr = 'J17051809403446543217066')
    {
        $res = payment_paid($notice_sn, $arr, 4);
        var_dump($res);
    }

    //将学生卡充值信息存入订单
    private function addOrder($mobile, $money, $info)
    {
        $user_id = $this->getUserId();
        //创建订单
        $data3['user_id'] = $user_id;
        $where['user_id'] = $user_id;
        $username = M('User', 'think_')->where($where)->getField("username");
        $data3['user_name'] = $username;
        $data3['deal_price'] = $money;//单价
        $data3['inprice'] = $info['inprice'];
        if ($money > $info['inprice']) {
            $info['inprice'] = $money;
        }
        $data3['total_price'] = $info['inprice'];//总价
        $data3['memo'] = '话费充值:' . $info['cardname'] . ':' . $mobile;//备注
        $data3['payment_id'] = 3;//支付方式 0支付宝 1微信 2.银联 3.账户余额
        $data3['type'] = 2; //话费充值
        $data3['num'] = 1; //个数
        $data3['deal_name'] = "话费充值";
        $data3['create_time'] = date("Y-m-d H:i:s");
        $order_id = M("DealOrder")->add($data3);

        if ($order_id) {
            //写入支付订单详情
            $data4['notice_sn'] = build_order_no();//内部订单号
            $data4['create_time'] = $data3['create_time'];
            $data4['order_id'] = $order_id;
            $data4['user_id'] = $user_id;
            $data4['payment_id'] = 3;
            $data4['memo'] = $info['game_area'];
            $data4['money'] = $info['inprice'];
            $res = M("PaymentNotice")->add($data4);
        }
        if ($res) {
            return $data4['notice_sn'];
        }
    }

    //获取话费充值订单详情
    public function getTelTopUpInfo($mobile = '', $money = 0)
    {
        //检测手机号码是否能充值
        $isMobile = $this->checkMobile($mobile, $money);
        if ($isMobile['error_code'] > 0) {
            $this->error($isMobile['reason']);
        }

        //根据手机号和面值查询商品
        $isGoods = $this->getGoods($mobile, $money);
        if ($isGoods['error_code'] > 0) {
            $this->error($isGoods['reason']);
        }
        return $isGoods;
    }

    //检测手机号码是否能充值
    public function checkMobile($mobile, $money)
    {
        $url = "http://op.juhe.cn/ofpay/mobile/telcheck";
        $data = "cardnum=" . $money . "&phoneno=" . $mobile . "&key=" . C('TELTOPUP');

        $result = reqURL($url, $data, "POST");
        return $result;
    }

    //根据手机号和面值查询商品
    public function getGoods($mobile, $money)
    {
        $url = "http://op.juhe.cn/ofpay/mobile/telquery";
        $data = "cardnum=" . $money . "&phoneno=" . $mobile . "&key=" . C('TELTOPUP');

        $result = reqURL($url, $data, "POST");
        return $result;
    }

    //执行手机充值
    //如果充值过程中，遇到http网络状态异常或错误码返回系统异常10014，请务必通过订单查询接口检测订单或联系客服，不要直接做失败处理，避免造成不必要的损失！！！
    public function doTelTopUp($mobile, $money, $notice_sn)
    {
        $url = "http://op.juhe.cn/ofpay/mobile/onlineorder";
        $sign = md5("JH155340052736440a3bbf99ddb78045ac" . C('TELTOPUP') . $mobile . $money . $notice_sn);
        $data = "key=" . C('TELTOPUP') . "&phoneno=" . $mobile . "&cardnum=" . $money . "&orderid=" . $notice_sn . "&sign=" . $sign;

        $result = reqURL($url, $data, "POST");
        return $result;
    }

    //聚合充值订单查询接口
    public function getTopUpOrder($notice_sn = '')
    {
        $url = "http://op.juhe.cn/ofpay/mobile/ordersta";
        $data = "key=" . C('TELTOPUP') . "&orderid=" . $notice_sn;

        $result = reqURL($url, $data, "POST");
        return $result;
    }


}

?>