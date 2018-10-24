<?php
use xmpush\Builder;
use xmpush\HttpBase;
use xmpush\Sender;
use xmpush\Constants;
use xmpush\Stats;
use xmpush\Tracer;
use xmpush\Feedback;
use xmpush\DevTools;
use xmpush\Subscription;
use xmpush\TargetedMessage;

include_once(dirname(__FILE__) . '/autoload.php');

class MIpush {
    protected $sender           = NULL;

    function __construct( $package,$secret) {
        // 常量设置必须在new Sender()方法之前调用
        Constants::setPackage($package);
        Constants::setSecret($secret);
        $this->sender = new Sender();
    }


    //Android单播
    function sendAndroidUnicast($device_token,$alert) {
        $message1 = new Builder();
        $message1->title($alert['title']);  // 通知栏的title
        $message1->description($alert['title']); // 通知栏的descption
        $message1->passThrough(0);  // 这是一条通知栏消息，如果需要透传，把这个参数设置成1,同时去掉title和descption两个参数
        $activityStr = json_encode(array('activity'=>$alert['activity'],'id'=>$alert['id']));
        $message1->payload($activityStr); // 携带的数据，点击后将会通过客户端的receiver中的onReceiveMessage方法传入。
        $message1->extra(Builder::notifyForeground, 1); // 应用在前台是否展示通知，如果不希望应用在前台时候弹出通知，则设置这个参数为0
//        $message1->extra(Builder::notifyEffect, 2);
        $message1->notifyId(2); // 通知类型。最多支持0-4 5个取值范围，同样的类型的通知会互相覆盖，不同类型可以在通知栏并存
        $message1->build();
        $targetMessage = new TargetedMessage();
        $targetMessage->setTarget('regid', TargetedMessage::TARGET_TYPE_REGID); // 设置发送目标。可通过regID,alias和topic三种方式发送
        $targetMessage->setMessage($message1);
        $aliasList = array($device_token);
        $result = $this->sender->sendToIds($message1, $aliasList)->getRaw();
        if($result['code'] > 0){
            return($result);
        }
    }



}
