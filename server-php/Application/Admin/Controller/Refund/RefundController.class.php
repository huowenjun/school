<?php
/*
*   退款API
*/

namespace Admin\Controller\Refund;

use Admin\Controller\BaseController;

class RefundController extends BaseController
{   
    /*
     * 系统退款接口
     * url:  /index.php/Admin/Refund/Refund/refund
     * post 请求
     * 参数：notice_sn 内部订单号
     */
    public function refund()
    {
        //接收订单号
        $notice_sn = I('post.notice_sn', '0', 'string');
        if (!$notice_sn) {
            $this->error('没有此订单');
        }
        //根据订单号找唯一单条数据
        $orderList = D('payment_notice')->field('order_id,outer_notice_sn,money,is_paid,payment_id')->where(['notice_sn' => $notice_sn])->find();//outer_notice_sn支付宝或者微信内部订单;payment_id支付类型 0支付宝 1微信
        $orderInfo = D('deal_order')->where(['id' => $orderList['order_id']])->find();
        //判断是否已经退款
        if ($orderInfo['is_refund'] == 1) {//已退款 0:未 1:已
            $this->error('已退款');
        }
        if (!$orderList) {//可能订单号有误，数据不存在
            $this->error('没有此订单');
        }
        if ($orderList['is_paid'] == 0) {//未付款
            $this->error('请先付款');
        }
        //修改订单信息，把未退款改为已退款，把0状态改为1状态，在这里使用事务
        D('deal_order')->startTrans();
        $bool = D('deal_order')->where(['id' => $orderList['order_id']])->save(['is_refund' => 1]);
        //根据订单信息查找是支付宝还是微信
        if ($bool) {
            $data['out_trade_no']=$notice_sn;//商户订单号
            $data['money']=$orderList['money'];//退款金额
            $data['type']=$orderInfo['type'];//退款金额
            $data['num']=$orderInfo['num'];//退款金额
            $data['deal_name']=$orderInfo['deal_name'];//退款金额
            //判断
            if ($orderList['payment_id'] == 0) {//支付宝
                $data['trade_no']=$orderList['outer_notice_sn'];//支付宝交易号
                //调用者支付宝
                $bool = $this->alipayRefund($data);
                if ($bool) {
                    D('deal_order')->commit();
                    $this->success('退款成功');
                } else {
                    D('deal_order')->rollback();
                    $this->error('退款失败');
                }
            } elseif ($orderList['payment_id'] == 1) {//微信
                //调用微信
                $bool = $this->wechatRefund($data);
                if ($bool) {
                    D('deal_order')->commit();
                    $this->success('退款成功');
                } else {
                    D('deal_order')->rollback();
                    $this->error('退款失败');
                }
            }
        } else {
            D('deal_order')->rollback();
            $this->error('退款失败');
        }


    }

    /*
     * alipay refund API
     */
    protected function alipayRefund($data = "")
    {
        //加载sdk类库
        import('Vendor.AliAppSDK.AopSdk');
        //根据用户ID查找订单
        $aop = new \AopClient ();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = '2017032706425485';
        $aop->rsaPrivateKey = C('RSA_PRIVATE_KEY');
        $aop->alipayrsaPublicKey = C('ALIPAY_RSA_PUBLIC_KEY');
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->format = "json";
        $aop->charset = "UTF-8";
        $request = new \AlipayTradeRefundRequest ();
        $request->setBizContent("{" .
            "\"out_trade_no\":\"{$data['out_trade_no']}\"," .//商户订单号
            "\"trade_no\":\"{$data['trade_no']}\"," .//支付宝交易号
            "\"refund_amount\":{$data['money']}," .//退款金额
            "\"refund_reason\":\"正常退款\"," .
            "\"out_request_no\":\"{$data['out_trade_no']}\"," .
            "\"operator_id\":\"{$this->getUserId()}\"," .
            "\"store_id\":\"{$data['type']}\"," .
            "\"terminal_id\":\"{{$data['type']}}\"," .
            "      \"goods_detail\":[{" .
            "        \"goods_id\":\"{{$data['type']}}\"," .
            "\"alipay_goods_id\":\"{$data['out_trade_no']}\"," .
            "\"goods_name\":\"{$data['deal_name']}\"," .
            "\"quantity\":{$data['num']}," .
            "\"price\":{$data['money']}," .
            "\"goods_category\":\"{$data['type']}\"," .
            "\"body\":\"{$data['deal_name']}\"," .
            "\"show_url\":\"http://school.xinpingtai.com\"" .
            "        }]" .
            "  }");
        $result = $aop->execute($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 微信退款接口
     */
    protected function wechatRefund($data = "")
    {
        Vendor('WxpayAPI.lib.WxPayApi');
        //商品memo，$data['memo']
        $input = new \WxPayRefund();
        //* 申请退款，WxPayRefund中out_trade_no、transaction_id至少填一个
        //且out_refund_no、total_fee、refund_fee、op_user_id为必填参数
        $input->SetOut_trade_no($data['out_trade_no']);//商户订单号
        $input->SetOut_refund_no(\WxPayConfig::MCHID . date("YmdHis"));//商户退款单号
        $input->SetTotal_fee($data['money'] * 100);//订单金额 ,精确到分
        $input->SetRefund_fee($data['money'] * 100);//退款金额,精确到分
        $input->SetOp_user_id(\WxPayConfig::MCHID);//商户号
        $refund = new \WxPayApi();
        $data = $refund::refund($input);
        if ($data['result_code'] == "SUCCESS") {
            return true;
        } else {
            return false;
        }

    }


}
