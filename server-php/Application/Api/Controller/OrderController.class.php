<?php
/**
 * Created by PhpStorm.
 * User: mengfanmin
 * Date: 2017/4/6 0006
 * Time: 下午 4:18
 *
 */

namespace Api\Controller;

use Api\Controller\BaseController;

class OrderController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法');
        }
    }

    protected function init()
    {

    }

    //    创建订单接口
    public function getOrder()
    {
        $deal_price = I("post.deal_price");
        $num = I("post.num", 1);

        if ($deal_price <= 0) {
            $this->error('订单金额不能小于0');
        }
        $total_price = $deal_price * $num;
        $user_id = I('post.user_id', session('admin_user')['user_id']);
        if ($user_id === 'null' || !$user_id) {
            $user_id = session('admin_user')['user_id'];
        }
        if (!$user_id) {
            $this->error('请登录后再试');
        }
        $user_name = M('User', 'think_')->where("user_id = '{$user_id}'")->getField('username');

        if (!$user_name) {
            $this->error('用户信息不合法');
        }
        $type = I("post.type", 0);
        $deal_name = I("post.deal_name", '充值');

        //创建充值订单
        $data['user_id'] = $user_id;
        $data['user_name'] = $user_name;
        $data['deal_price'] = $deal_price;//单价
        $data['inprice'] = I("post.inprice");//进货价
        $data['item_id'] = I("post.item_id");//第三方接口产品id
        $data['total_price'] = $total_price;//总价
        $data['memo'] = htmlspecialchars_decode(I("post.memo"), ENT_QUOTES);//备注
        $data['payment_id'] = I("post.payment_id", 0);//支付方式 0支付宝 1微信 2.银联
        $data['deal_name'] = $deal_name;
        $data['type'] = $type;
        $data['create_time'] = date("Y-m-d H:i:s");
        //话费充值 判断手机号
        if ($type == 2) {
            $is_mobile = is_mobile($data['memo']);
            if (!$is_mobile) {
                $this->error('请输入正确手机号');
            }
            //调用话费充值接口
            import('Vendor.Elife.TelephoneCharge');
            $TelephoneCharge = new \TelephoneCharge();
            $telephoneChargeInfo = $TelephoneCharge->getItemInfo($data['memo'], $total_price);
            if ($telephoneChargeInfo['status'] == 0) {
                $this->error($telephoneChargeInfo['info']);
            }
        }

        if (!M("DealOrder")->create($data)) {
            $this->error(M("DealOrder")->getError());
        } else {
            M("DealOrder")->startTrans();
            $order_id = M("DealOrder")->add($data);
            //订单创建成功
            if ($order_id) {
                //写入支付订单详情
                $data2['notice_sn'] = build_order_no();//内部订单号
                $data2['create_time'] = $data['create_time'];
                $data2['order_id'] = $order_id;
                $data2['user_id'] = $user_id;
                $data2['payment_id'] = $data['payment_id'];
                $data2['memo'] = $data['memo'];
                $data2['money'] = $data['total_price'];
                //$data['payment_id'] = 3;
                if (!M("PaymentNotice")->create($data2)) {
                    $this->error(M("PaymentNotice")->getError());
                } else {
                    $res = M("PaymentNotice")->add($data2);
                    if (!$res) {
                        M("DealOrder")->rollback();
                        $this->error('订单创建失败');
                    } else {
                        M("DealOrder")->commit();
                        //服务端SDK生成APP支付订单信息 并返回
                        $orderInfo = array(
                            'body' => $data['deal_name'],
                            'subject' => $data['deal_name'],
                            'out_trade_no' => $data2['notice_sn'],
                            'total_amount' => $data2['money'],
                        );
                        if ($data['payment_id'] == 0) {
                            //支付宝
                            $res = $this->getAliServerOrder($orderInfo);
                            $this->success($res);
                        } elseif ($data['payment_id'] == 1) {
                            //APP微信
                            $res = $this->getWxPayOrder($orderInfo);
                            $this->success($res);
                        } elseif ($data['payment_id'] == 2) {
                            //银联
                            $res = $this->getWxPayOrder($total_price * 100);
                            $this->success($res);
                        } elseif ($data['payment_id'] == 3) {
                            //APP微信
                            $orderInfo['openid'] = M('user','think_')->where('user_id = '.$user_id)->getField('open_id');
                            if(!$orderInfo['openid']){
                               // $this->error('微信授权失败');
                            }
                            $res = $this->getWxJSPayOrder($orderInfo);
                            $this->success($res);
                        }
                    }

                }

            } else {
                M("DealOrder")->rollback();
                $this->error('订单创建失败');
            }
        }

    }

    //微信PHP服务端统一下单
    private function getWxPayOrder($orderInfo = '')
    {
        $res = Vendor('WxpayAPI.lib.WxPayApi');
        //统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($orderInfo['body']);
        $input->SetAttach($orderInfo['subject']);
        $input->SetOut_trade_no($orderInfo['out_trade_no']);
        $input->SetTotal_fee($orderInfo['total_amount'] * 100);
        $input->SetTime_start(date("YmdHis"));
//        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetNotify_url("http://school.xinpingtai.com/Api/OrderNotify/wxnotify");
        $input->SetTrade_type("APP");
        $result = \WxPayApi::unifiedOrder($input);
        $timeStamp = time();

        //二次签名appid，partnerid，prepayid，noncestr，timestamp，package
        $resultObj = new \WxPayUnifiedOrder();
        $resultObj->SetAppid($result['appid']);
        $resultObj->SetPartner_id($result['mch_id']);
        $resultObj->SetPrepay_id($result['prepay_id']);
        $resultObj->SetNonce_str2($result['nonce_str']);
        $resultObj->SetTimeStamp($timeStamp);
        $resultObj->SetPackage("Sign=WXPay");
        $signStr = $resultObj->SetSign();
        //sign，appId，partnerId，prepayId，nonceStr，timeStamp，package
        $data = array(
            'appid' => $result['appid'],
            'partnerid' => $result['mch_id'],
            'prepayid' => $result['prepay_id'],
            'noncestr' => $result['nonce_str'],
            'timestamp' => $timeStamp,
            'package' => 'Sign=WXPay',
            'sign' => $signStr,
        );
        return $data;
    }

    //微信公众号支付
    private function getWxJSPayOrder($orderInfo = '')
    {
        $res = Vendor('WxpayAPI.libJS.WxPayJsApiPay');
        $tools = new \JsApiPay();
        //②、统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($orderInfo['body']);
        $input->SetAttach($orderInfo['subject']);
        $input->SetOut_trade_no($orderInfo['out_trade_no']);
        $input->SetTotal_fee($orderInfo['total_amount'] * 100);
        $input->SetTime_start(date("YmdHis"));
//        $input->SetTime_expire(date("YmdHis", time() + 3600));
//        $input->SetGoods_tag("test");
        $input->SetNotify_url("http://school.xinpingtai.com/Api/OrderNotify/wxjsnotify.html");
        $input->SetTrade_type("JSAPI");
        //$input->SetOpenid($orderInfo['openid']);
        $input->SetOpenid($orderInfo['openid']);
        $order = \WxPayApi::unifiedOrder($input);
        $jsApiParameters = $tools->GetJsApiParameters($order);
        return $jsApiParameters;
    }

    //支付宝PHP服务端SDK生成APP支付订单信息
    private function getAliServerOrder($orderInfo = '')
    {
        //加载sdk类库
        import('Vendor.AliAppSDK.AopSdk');
        $aop = new \AopClient();
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = "2017032706425485";
        $aop->rsaPrivateKey = C('RSA_PRIVATE_KEY');
        $aop->format = "json";
        $aop->charset = "UTF-8";
        $aop->signType = "RSA2";
        //家长端应用公钥
        $aop->alipayrsaPublicKey = C('ALIPAY_RSA_PUBLIC_KEY2');
        //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
        $request = new \AlipayTradeAppPayRequest();
        //SDK已经封装掉了公共参数，这里只需要传入业务参数
        $bizcontent = "{\"body\":\"{$orderInfo['body']}\","
            . "\"subject\": \"{$orderInfo['subject']}\","
            . "\"out_trade_no\": \"{$orderInfo['out_trade_no']}\","
            . "\"timeout_express\": \"30m\","
            . "\"total_amount\": \"{$orderInfo['total_amount']}\","
            . "\"product_code\":\"QUICK_MSECURITY_PAY\""
            . "}";

        $request->setNotifyUrl("http://school.xinpingtai.com/Api/OrderNotify/alinotify");
        $request->setBizContent($bizcontent);
        //这里和普通的接口调用不同，使用的是sdkExecute
        $response = $aop->sdkExecute($request);
        return ($response);
    }

    // 消费获取tn示例类 银联
    public function getUPPayOrder($total_price)
    {
        import('Vendor.uppay.AcpService');
        $params = array(

            //以下信息非特殊情况不需要改动
            'version' => \com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->version,                 //版本号
            'encoding' => 'utf-8',                  //编码方式
            'txnType' => '02',                      //交易类型
            'txnSubType' => '01',                  //交易子类
            'bizType' => '000201',                  //业务类型
            'frontUrl' => \com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->frontUrl,  //前台通知地址
            'backUrl' => \com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->backUrl,      //后台通知地址
            'signMethod' => \com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->signMethod,                  //签名方法
            'channelType' => '08',                  //渠道类型，07-PC，08-手机
            'accessType' => '0',                  //接入类型
            'currencyCode' => '156',              //交易币种，境内商户固定156

            //TODO 以下信息需要填写
//            'merId' => $_POST["merId"],        //商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
            'merId' => '777290058110048',        //商户代码，请改自己的测试商户号，此处默认取demo演示页面传递的参数
            'orderId' => build_order_no(),    //商户订单号，8-32位数字字母，不能含“-”或“_”，此处默认取demo演示页面传递的参数，可以自行定制规则
            'txnTime' => date("YmdHis"),    //订单发送时间，格式为YYYYMMDDhhmmss，取北京时间，此处默认取demo演示页面传递的参数
            'txnAmt' => $total_price,    //交易金额，单位分，此处默认取demo演示页面传递的参数

            // 请求方保留域，
            // 透传字段，查询、通知、对账文件中均会原样出现，如有需要请启用并修改自己希望透传的数据。
            // 出现部分特殊字符时可能影响解析，请按下面建议的方式填写：
            // 1. 如果能确定内容不会出现&={}[]"'等符号时，可以直接填写数据，建议的方法如下。
            //    'reqReserved' =>'透传信息1|透传信息2|透传信息3',
            // 2. 内容可能出现&={}[]"'符号时：
            // 1) 如果需要对账文件里能显示，可将字符替换成全角＆＝｛｝【】“‘字符（自己写代码，此处不演示）；
            // 2) 如果对账文件没有显示要求，可做一下base64（如下）。
            //    注意控制数据长度，实际传输的数据长度不能超过1024位。
            //    查询、通知等接口解析时使用base64_decode解base64后再对数据做后续解析。
            //    'reqReserved' => base64_encode('任意格式的信息都可以'),

            //TODO 其他特殊用法请查看 pages/api_05_app/special_use_preauth.php
        );

        \com\unionpay\acp\sdk\AcpService::sign($params); // 签名
        $url = \com\unionpay\acp\sdk\SDKConfig::getSDKConfig()->appTransUrl;

        $result_arr = \com\unionpay\acp\sdk\AcpService::post($params, $url);
        if (count($result_arr) <= 0) { //没收到200应答的情况
//            printResult($url, $params, "");
            $this->error("获取订单失败");
        }

//        printResult($url, $params, $result_arr); //页面打印请求应答数据

        if (!\com\unionpay\acp\sdk\AcpService::validate($result_arr)) {
            $this->error("应答报文验签失败");
        }

        if ($result_arr["respCode"] == "00") {
            //成功
            //TODO
            $this->success($result_arr["tn"]);

        } else {
            //其他应答码做以失败处理
            //TODO
            $this->error("获取订单失败");
        }

    }

    //全部订单接口
    public function getOrderList()
    {
        $user_id = $this->getUserId();
        $whereStr['user_id'] = $user_id;
        $orderby = "id desc";
        $pagesize = I("post.pagesize", $this->PAGESIZE);
        $page = I('post.page');
        $payment = C('PAYMENT_TYPE');

        $result = queryList(M("DealOrder"), '*', $whereStr, $orderby, $page, $pagesize);

        foreach ($result['content'] as $key => $value) {
            $result['content'][$key]['payment_id'] = $payment[$value['payment_id']];
            $result['content'][$key]['order_status'] = C('ORDER_STATUS')[$value['order_status']];
            $result['content'][$key]['is_refund'] = C('IS_EFFECT')[$value['is_refund']];
            $result['content'][$key]['is_success'] = C('IS_EFFECT')[$value['is_success']];
            $result['content'][$key]['type'] = C('ORDER_TYPE')[$value['type']];
        }

        $this->success($result);
    }

    //获取交易账单
    public function getBillList()
    {
        $user_id = $this->getUserId();
        $whereStr['user_id'] = $user_id;
        $orderby = "id desc";
        $pagesize = I("post.pagesize", $this->PAGESIZE);
        $page = I('post.page');

        $result = queryList(M("UserBill"), '*', $whereStr, $orderby, $page, $pagesize);

        foreach ($result['content'] as $key => $value) {
            if ($value['type'] == 0) {
                //获取充值支付方式
                $payment_id = M("DealOrder")->where("id = '{$value['order_id']}'")->getField("payment_id");
                $result['content'][$key]['payment_id'] = C('PAYMENT_TYPE')[$payment_id];
            } else {
                $result['content'][$key]['payment_id'] = '';
            }

            $result['content'][$key]['type'] = C('ORDER_TYPE')[$value['type']];

        }

        $this->success($result);
    }

    //获取账单详情
    public function getBillDetail()
    {
        $id = I("post.id");
        $info = M("UserBill")->where("id = '{$id}'")->find();
        if (!$info) {
            $this->success("账单不存在");
        }

        $order_id = $info["order_id"];
        $whereStr['refund.user_id'] = $this->getUserId();
        $whereStr['refund.id'] = $order_id;

        if ($info['type'] == 3) {
            //提现订单
            $result = M("UserRefund")
                ->alias('refund')
                ->field("refund.*,card.card_no,card.card_type,card.bankname")
                ->join('left join sch_bank_card card on card.id = refund.bank_id')
                ->where($whereStr)->find();
            //'未审核','通过','驳回','成功'
            // $result['is_pay'] = C('REFUND_STATUS')[$result['is_pay']];
        } else {
            //充值/购买订单
            $result = M("DealOrder")
                ->alias('refund')
                ->field("refund.*,notice.notice_sn")
                ->join("__PAYMENT_NOTICE__ notice on refund.id = notice.order_id")
                ->where($whereStr)->find();
            $payment = C('PAYMENT_TYPE');
            $result['payment_id'] = $payment[$result['payment_id']];
            if ($result['payment_id'] == null) {
                $result['payment_id'] = '零钱';
            }
            $result['order_status'] = C('ORDER_STATUS')[$result['order_status']];
            $result['is_refund'] = C('IS_EFFECT')[$result['is_refund']];
            $result['is_success'] = C('IS_EFFECT')[$result['is_success']];
            $result['type'] = C('ORDER_TYPE')[$result['type']];
        }


        $this->success($result);
    }

    //E生活-查询单个话费直充商品
    public function getElifeTelephoneInfo()
    {
        $mobile = I('post.memo');
        $moneyArr = array(1);

        $data = array();
        //调用话费充值接口
        import('Vendor.Elife.TelephoneCharge');
        $TelephoneCharge = new \TelephoneCharge();
        foreach ($moneyArr as $key => $value) {
            $info = $TelephoneCharge->getItemInfo($mobile, $value);
            $data[] = $info['info'];
        }
        $this->success($data);
    }


}

?>