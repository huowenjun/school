<?php
require_once 'AopSdk.php';

$aop = new AopClient;
$aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
$aop->appId = "2017032706425485";
$aop->rsaPrivateKey = 'MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQC8c05wA5eZPK224dGBWNMuDjw46YQSLdPnNwBluBNGgsAouQ3f6L0MJ5RIVPH5+7IkuwJ0lVF093LbML9LAJNyOmVZ0Rs5wtT4VgcTFWJgcfm+svSyCtfSNlN/dH3vM3PWW6Y1SaY2ceihf3HTb2YL4jNRQaWudgoOumriikGwDoa4N1E8goH6DYtt0ff+6keoSgRXDNjK8mCFqrQRyYN190CbsA+duog/oX/gboK0ZtnQoa6qHTUNZ4jpG4QtPdh+h/k3X1Y7hGTpKNPU4y0bXdo7GLVL/mjyLJDIBU6GqydrOKjIZ3s6ags5OE9abNYym6XYMR/JFNzZ7BRScatFAgMBAAECggEAaL7Ci1pDyiXC/JLZy0Ze4wuAh7Wr9hrI3IxiySced6O3QStSvfD0GyxorCei8+rloqrbe4d/Zj8f9RtMSFkCm4w/x0OGGX3kuD/A4OeS7b6MLWX0wn1qZmpR0NckJG955Fy+roHIRBzeS921m+sgUlyhX3nYqHbtsjAFtvNX/Y2xKPf5WNa4glFk7aij/iPX6pXrHdxdo/rpD6Zt11EpnAk8aGKDunDResJ98OcSA0myPFC51TumePCdDQJyvuwnrKgwQMrI6OZO8xeiiOxJRE0wMCE3RfJjMoCzmB8cwq5EWTiZjDILuLdSBG6Exj28zNYcAQHcdCHE+SfXwWmBwQKBgQDiAmqsmlLxFufeuAHlZR9ocAI+EwhB/ZwiJqzKjCbg5QJqcaVpdJET7kTMfrpL3CdhezdlbDS530SEwrKjKDt+FOHJ5ezF6PTOTxK4KrIE2ZPr7dL9xMKf2Alegj9vrR5yIkoKL9j6vaUu4ePy3mu0/7JgLoZymkHebBBl8xEvkQKBgQDVdQCnPf2abHaP92CzAsKZkmCZPYyLYvfMH7tJH1F1PF8WCP2ZBOp8euHdXNkDHGQduYhn8HkSf2e2ay/ShpZuSA61kclNadluEe0x8iaEEgn+EBfH2kMF68pH0iW5DlW8oer7E1kybL0G0ZEHyZ9S+r0wKf6T3nXTgQ8qxq0OdQKBgQDKcR26M5WdrEXPgoT4REcI1mO71HJuIcvL71aRK07b3WX3kIp41lfpQWDQx6b5sl53+9WX/H+SCoImZPt8F9qKSgwhO9mFQPCfJ8b9vgitPXM5PlLiym8GnI1v4T0PPENsOniVfVxe5KZkQyRadI6Hlw3hB2uYlcHwiF175Gh9cQKBgDBmUzuYpsQ5C7khEmAEpDNGKXkVp6SDUESMfV7bJxE6GyVX7IihwLlw833J67r02Q6UXwWSVSGIme+W5kUKF1nyJMOuxsIy2gZHMk085tbTcEiXRY0fREs3Z6pZUAxh37bhz/IWNQdl+IZvRj9JzEJ4cCVXoE3PB1Bp1xKP8fVxAoGBANOa07QOJ5CmngAKpDlzfsyKfW3CIoeJf/0pSV1LzLgI36lNaiLHEW7HkQAVCkc5E3fzi3LOSeaaQ6vvAe+MWQgkaSpa447HDVHWtXvmgtkSb3ntVP/kMnOtx60UPGfpO2F2xVelAoMv4C5BpFHj0FzeGgBEdhZqUHp23dc3C+tb' ;
$aop->format = "json";
$aop->charset = "UTF-8";
$aop->signType = "RSA2";
$aop->alipayrsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvHNOcAOXmTyttuHRgVjTLg48OOmEEi3T5zcAZbgTRoLAKLkN3+i9DCeUSFTx+fuyJLsCdJVRdPdy2zC/SwCTcjplWdEbOcLU+FYHExViYHH5vrL0sgrX0jZTf3R97zNz1lumNUmmNnHooX9x029mC+IzUUGlrnYKDrpq4opBsA6GuDdRPIKB+g2LbdH3/upHqEoEVwzYyvJghaq0EcmDdfdAm7APnbqIP6F/4G6CtGbZ0KGuqh01DWeI6RuELT3Yfof5N19WO4Rk6SjT1OMtG13aOxi1S/5o8iyQyAVOhqsnazioyGd7OmoLOThPWmzWMpul2DEfyRTc2ewUUnGrRQIDAQAB';
//实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
$request = new AlipayTradeAppPayRequest();
//SDK已经封装掉了公共参数，这里只需要传入业务参数
$bizcontent = "{\"body\":\"我是测试数据\","
    . "\"subject\": \"App支付测试\","
    . "\"out_trade_no\": \"20170125test01\","
    . "\"timeout_express\": \"30m\","
    . "\"total_amount\": \"0.01\","
    . "\"product_code\":\"QUICK_MSECURITY_PAY\""
    . "}";
$request->setNotifyUrl("http://school.xinpingtai.com/notify.php");
$request->setBizContent($bizcontent);
//这里和普通的接口调用不同，使用的是sdkExecute
$response = $aop->sdkExecute($request);
//htmlspecialchars是为了输出到页面时防止被浏览器将关键参数html转义，实际打印到日志以及http传输不会有这个问题
echo htmlspecialchars($response);//就是orderString 可以直接给客户端请求，无需再做处理。
?>