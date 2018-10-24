<?php
/**
 * SDK调用demo，详细参数传递，请参照使用说明文档
 * 说明：pushId由IMEI+appId构成，如：868773027203481100999，其中868773027203481 为IMEI号，100999为appId
 * User: even
 * Date: 2016/8/25
 * Time: 20:30
 */

include_once(dirname(__FILE__) . '/mzPushSDK/autoload.php');

class MZPush
{
    protected $mzPush = NULL;

    function __construct($appid, $secret)
    {
        $this->mzPush = new MzPush2($appid, $secret);
    }


    //Android单播
    function sendAndroidUnicast($device_token, $alert)
    {
        //通知栏消息对象
        $varnishedMessage = new VarnishedMessage();
        $activityStr = array('activity'=>$alert['activity'],'id'=>$alert['id']);
        $varnishedMessage->setTitle($alert['title'])
            ->setContent($alert['title'])
            ->setClickType(0)
            ->setParameters($activityStr)
            ->setNoticeExpandContent('');
        /**
         * 通知栏消息，根据pushid
         * @param $pushIds pushid集合，
         * @param $varnishedMessage 通知栏消息对象实例
         */
        $result = $this->mzPush->varnishedPush($device_token, $varnishedMessage);
        $result = json_decode($result, true);
        if ($result['code'] != 200) {
            return $result;
        }
    }


}
