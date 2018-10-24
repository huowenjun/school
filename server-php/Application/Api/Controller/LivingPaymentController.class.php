<?php

namespace Api\Controller;

use Api\Controller\BaseController;

class LivingPaymentController extends BaseController
{
    /*
     * 话费充值 获取充值详情
     * */
    public function getChargeInfo()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法！');
        }

        $mobile = I('post.mobile');
        if(!$mobile){
            $this->error('手机号码不能为空');
        }
        //加载话费充值类库
        import('Vendor.Elife.TelephoneCharge');
        $itemArr = array(10, 20, 30, 50, 100, 200);
        $TelephoneCharge = new \TelephoneCharge;
        $data = array();
        foreach ($itemArr as $key=>$value) {
            $info = $TelephoneCharge->getItemInfo($mobile,$value);
            if($info['status'] == 0){
                $this->error($info['info']);
            }
            $data[] = array('inPrice'=>$info['info']->inPrice,'advicePrice'=>$info['info']->advicePrice);
        }
        $this->success($data);
    }

}

?>