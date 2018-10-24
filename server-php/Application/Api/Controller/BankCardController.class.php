<?php
/**
 * Created by PhpStorm.
 * User: Yumeng
 * Date: 2017/4/14
 * Time: 下午 3:43
 *
 */
namespace Api\Controller;

use Api\Controller\BaseController;
use Think\Model;

class BankCardController extends BaseController
{
//    银行卡列表
    public function query()
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
        $BankCard = D('BankCard');
        $user_id = $this->getUserId();
        $where['u_id'] = $user_id;
        $where['del_status'] = 0;
        $result = $BankCard->where($where)->select();
        $this->success($result);
    }

    //添加银行卡
    public  function  add(){
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
           $this->error('接口访问不合法');
        }
        $BankCard = M('BankCard');
        $user_id = $this->getUserId();
        $data['u_id'] = $user_id;
        $data['cardholder'] = I('post.cardholder');//持卡人姓名
        $cardNo = I('post.card_no');//卡号
        $data['card_no'] = $cardNo;
        $data['card_type'] = I('post.card_type');//类型 借记卡/信用卡
        $data['bankname'] = I('post.bankname');//银行名称
        $data['create_time'] = date('Y-m-d H:i:s');
        $where['card_no'] = $cardNo;
        $where['u_id'] = $user_id;
        $cardInfo = $BankCard->where($where)->find();

        if (!empty($cardInfo)){
            if ($cardInfo['del_status'] == 1){
                $data1['del_status'] = 0;
                $data1['create_time'] = date('Y-m-d H:i:s');
                $BankCard->where($where)->save($data1);
            }else{
                $this->error('银行卡已添加!');
            }
        }else{
            $res = $BankCard->add($data);
        }
        if ($res === false) {
            $content = "操作失败";
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getUserType(), $this->getUserType(), 1, 2);
            $this->error("操作失败");
        } else {
            $content = "操作成功！";
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getUserType(), $this->getUserType(), 1, 1);
            $card= $res;
            $this->success($card);
        }
    }

    //删除银行卡
    public  function del(){
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
           $this->error('接口访问不合法');
        }
        $BankCard = M('BankCard');
        $id = I('post.id');
        if (empty($id)) {
            $this->error('参数不正确！');
        }
        //检查权限
        $where['id'] = $id;
        $auth = $BankCard->where($where)->find();
        if (!empty($auth) && $auth['u_id']!=$this->getUserId()) {
            $this->error('没有权限删除');
        }
        //检查是否有提现
        $refundInfo = M('userRefund')->where(array('bank_id'=>$id))->find();
        if (empty($refundInfo)){
            $ret = $BankCard->where($where)->delete();
        }else{
            $data['del_status'] = 1;
            $ret = $BankCard->where($where)->save($data);
        }
        if ($ret === false) {
            $content = "操作失败";
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getUserType(), $this->getUserType(), 1, 2);
            $this->error("操作失败");
        } else {
            $content = "操作成功！";
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getUserType(), $this->getUserType(), 1, 1);
            $this->success('操作成功！');
        }
    }

}
?>