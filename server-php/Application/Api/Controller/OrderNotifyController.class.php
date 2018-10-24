<?php

namespace Api\Controller;

use Think\Controller;

class OrderNotifyController extends Controller
{
    //阿里云APP支付异步回调
    public function alinotify()
    {
        //加载sdk类库
        import('Vendor.AliAppSDK.AopSdk');
        $aop = new \AopClient();
        $aop->alipayrsaPublicKey = C('ALIPAY_RSA_PUBLIC_KEY');
        $flag = $aop->rsaCheckV1($_POST, NULL, "RSA2");
        if ($flag) {
            //支付成功 验证成功进行订单处理
            //支付平台参数
            $trade_no = $_POST['out_trade_no'];
            $out_trade_no = $_POST['trade_no'];
            if ($_POST['trade_status'] == 'TRADE_SUCCESS' || $_POST['trade_status'] == 'TRADE_FINISHED') {
                $type = M("PaymentNotice")
                    ->alias("a")
                    ->join("__DEAL_ORDER__ b on a.order_id = b.id")
                    ->where("a.notice_sn = '{$trade_no}'")
                    ->getField('type');
                if (($type == 2) || ($type == 4)) {//订单类型 0账户零钱充值 1学生卡余额充值 2话费充值 3提现 4流量充值
                    payment_paid($trade_no, $out_trade_no, 4);//充值中
                } else {
                    payment_paid($trade_no, $out_trade_no);
                }

                echo 'success';
            } else {
                echo 'failure';
            }

        } else {
            //支付成功 验证失败
            echo 'failure';
        }

    }

    //微信支付异步回调
    public function wxnotify()
    {
        Vendor('WxpayAPI.lib.WxPayNotify');
        $notify = new \WxPayNotify();
        //已签名验证
        $result = $notify->Handle(false);

        //订单支付成功进行状态处理
        if (($result['result_code'] === 'SUCCESS') && ($result['return_code'] === 'SUCCESS')) {
            $type = M("PaymentNotice")
                ->alias("a")
                ->join("__DEAL_ORDER__ b on a.order_id = b.id")
                ->where("a.notice_sn = '{$result['out_trade_no']}'")
                ->getField('type');
            if (($type == 2) || ($type == 4)) {//订单类型 0账户零钱充值 1学生卡余额充值 2话费充值 3提现 4流量充值
                payment_paid($result['out_trade_no'], $result['transaction_id'], 4);//话费充值中
            } else {
                payment_paid($result['out_trade_no'], $result['transaction_id']);
            }

        }
    }

    //微信公众号支付异步回调
    public function wxjsnotify()
    {
        Vendor('WxpayAPI.libJS.PayNotifyCallBack');
        $notify = new \PayNotifyCallBack();
        $result = $notify->Handle(false);
        file_put_contents('wxpay.txt', date('H:i:s') . json_encode($result) . PHP_EOL, FILE_APPEND);
        //订单支付成功进行状态处理
        if (($result['result_code'] === 'SUCCESS') && ($result['return_code'] === 'SUCCESS')) {
            $type = M("PaymentNotice")
                ->alias("a")
                ->join("__DEAL_ORDER__ b on a.order_id = b.id")
                ->where("a.notice_sn = '{$result['out_trade_no']}'")
                ->getField('type');
            if (($type == 2) || ($type == 4)) {//订单类型 0账户零钱充值 1学生卡余额充值 2话费充值 3提现 4流量充值
                payment_paid($result['out_trade_no'], $result['transaction_id'], 4);//话费充值中
            } else {
                payment_paid($result['out_trade_no'], $result['transaction_id']);
            }
            echo 'success';
        } else {
            echo 'fail';
        }


    }

    //银联异步回调
    public function uppaynotify()
    {
        import('Vendor.uppay.AcpService');
        $logger = \com\unionpay\acp\sdk\LogUtil::getLogger();
        $logger->LogInfo("receive back notify: " . \com\unionpay\acp\sdk\createLinkString($_POST, false, true));
        if (isset ($_POST ['signature'])) {

            $flag = \com\unionpay\acp\sdk\AcpService::validate($_POST);

            if ($flag) {
                //验证成功
                $trade_no = $_POST ['orderId'];
                $out_trade_no = $_POST['queryId'];
                payment_paid($trade_no, $out_trade_no);
            } else {
                //验证失败

            }

        } else {
            echo '签名为空';
        }
    }

    //聚合-话费充值回调
    public function teltopupnotify()
    {
        $appkey = C('TELTOPUP'); //您申请的数据的APIKey

        $sporder_id = addslashes($_POST['sporder_id']); //聚合订单号
        $orderid = addslashes($_POST['orderid']); //商户的单号
        $sta = addslashes($_POST['sta']); //充值状态
        $sign = addslashes($_POST['sign']); //校验值

        $local_sign = md5($appkey . $sporder_id . $orderid); //本地sign校验值

        if ($local_sign == $sign) {
            if ($sta == '1') {
                //充值成功,根据自身业务逻辑进行后续处理
                //进行订单回调处理
                payment_paid($orderid, $sporder_id, 0);
                echo 'success';
            } elseif ($sta == '9') {
                //充值失败,根据自身业务逻辑进行后续处理
                $res = payment_paid($orderid, $sporder_id, 5);
                //进行余额回滚
                if ($res) {
                    roll_back_order($orderid);
                }

                echo 'fail';

            }
        }
    }

    //E生活-话费充值回调
    public function telephoneChargeNotify()
    {
        $status = $_POST['recharge_state'];//1（充值成功） 、9（充值失败）
        $trade_no = $_POST['outer_tid'];//平台订单号
        $out_trade_no = $_POST['tid'];
        if ($status == '1') {
            //充值成功,根据自身业务逻辑进行后续处理
            //进行订单回调处理
            payment_paid($trade_no, $out_trade_no, 0);
            echo 'success';
        } elseif ($status == '9') {
            //充值失败,根据自身业务逻辑进行后续处理
            $res = payment_paid($trade_no, $out_trade_no, 5);

            echo 'fail';

        }
    }
}

