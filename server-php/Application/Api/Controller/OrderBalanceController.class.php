<?php
/**
 * Created by PhpStorm.
 * User: Yumeng
 * Date: 2017/4/11
 * Time: 下午 4:45
 *
 */
namespace Api\Controller;

use Api\Controller\BaseController;
use Think\Model;

class OrderBalanceController extends BaseController
{
//    查询充值余额
    public function queryAccount()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
//            $this->error('接口访问不合法');
        }
        $user_id = $this->getUserId();
        $Account = D('UserAccount');
        $where['u_id'] = $user_id;
        $parent = $Account->field('u_id,account')->where($where)->find();
        if (empty($parent)) {
            $parent['u_id'] = $user_id;
            $parent['account'] = 0;
        }
        //查询学生卡余额
        $ids = $this->authRule();
        $whereCard['stu_id'] = array('IN', $ids['stu_id']);
        try {
            $StuCardBalances = M("StuCardBalances");
            $res = $StuCardBalances->field('stu_id,balances,freeze')->where($whereCard)->select();
        } //这里的\Exception不加斜杠的话回使用think的Exception类
        catch (\Exception $e) {
            $this->error('查询失败');
        }

        $result['parent'] = $parent;
        $result['student'] = $res;
        // $this->success($res);

        $this->success($result);
    }
    //获取orderID号
    public  function getCardOrderId(){
         $data['orderId'] = build_order_no();
         $data2['order_id'] = $data['orderId'];
         $data2['user_id'] = $this->getUserId();
         $data1['time'] = date('Y-m-d H:i:s');//修改时间
         $result = M('StuCardRechargeOrder')->add($data2);
         if ($result) {
            $this->success($data);    
         }else{
            $this->error('充值失败'); 
         }
    }

    //学生卡充值
    public function onCardRecharge()
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

        $order_id = I('post.order_id');//订单ID号
        $account = I('post.account');//充值金额
        $stu_id = I('post.stu_id');
        //验证token
        $md5 = md5($order_id.'shuhaixinxi_stu_card_recharge_order'.$stu_id.$account);
        if($md5 != $access_token){
            $this->error('充值失败');
        }

        $StuCardRechargeOrder = M('StuCardRechargeOrder')->where(array('order_id'=>$order_id,'user_id'=>$this->getUserId()))->find();
        //判断md5 加密后的数据 是否和access_token 相等 并且表里该订单号是否存在
        if (empty($StuCardRechargeOrder)||$StuCardRechargeOrder['status']==1) {
            $this->error('充值失败');
        }

        $UserAccount = M('UserAccount'); //账户余额表
        $StuCardBalances = M('StuCardBalances'); //学生卡余额表
        $StuCardBill = M('StuCardBill'); //学生消费记录表
        $user_id = $this->getUserId();//获取当前用户的ID号
        
        //
        $info = $UserAccount->where(array('u_id' => $user_id))->find();
        $stuid_info = $StuCardBalances->where(array('stu_id' => $stu_id))->find();

        $money = $info['account'] - $account;
        if ($money < 0) {
            $this->error('余额不足');
        }

        $UserAccount->startTrans();
        $data['account'] = $money;
        $data1['balances'] = $account + $stuid_info['balances'];

        $data1['stu_id'] = $stu_id;
        // $data1['balances'] = $account;
        $data1['modify_time'] = date('Y-m-d H:i:s');//修改时间

        $res = $UserAccount->where(array("id" => $info['id']))->save($data);
        if($res){
            //生成订单信息
            $trade_no = $this->addOrder($account);
            
            //存储学生卡余额
            if (empty($stuid_info)) {
                $res1 = $StuCardBalances->add($data1);
            } else {
                $res1 = $StuCardBalances->where(array('stu_id' => $stu_id))->save($data1);
            }
            if($res1){
                $data1['balances'] = $account;
                $data1['status'] = 1;
                $data1['create_time'] = date('Y-m-d H:i:s');//创建时间
                $data1['describe'] = "充值";//描述
                $res2 = $StuCardBill->add($data1);

            }
        }

        $rollback = false;
        if ($res && $res1  && $res2 && $trade_no) {
            $data2['status'] = 1;
            $data2['time'] = date('Y-m-d H:i:s');//修改时间
            $result = M('StuCardRechargeOrder')->where(array('id' => $StuCardRechargeOrder['id']))->save($data2);
            if ($result) {
                $UserAccount->commit();
                payment_paid($trade_no, 0);
                $this->success('充值成功');
            }else{
                $rollback = true;
            }
        } else {
            $rollback = true;
        }

        if ($rollback) {
            $UserAccount->rollback();
            $data2['status'] = 2;
            $data2['time'] = date('Y-m-d H:i:s');//修改时间
            $result = M('StuCardRechargeOrder')->where(array('id' => $StuCardRechargeOrder['id']))->save($data2);
            $this->error('充值失败');
        }

    }

    //获取学生卡余额信息
    public function getStudentCardBalance()
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
        $ids = $this->authRule();
        $where['stu_id'] = array('IN', $ids['stu_id']);
        try {
            $StuCardBalances = M("StuCardBalances");
            $res = $StuCardBalances->where($where)->select();
            if ($res) {
                //这里Exception前加\是php自带的Exception，不然报错  
                throw new \Exception();
            }
            // echo "aewsgfsdghdyjh";  
        } //这里的\Exception不加斜杠的话回使用think的Exception类
        catch (\Exception $e) {
            exit('查询失败');
        }
        $this->success($res);
    }

    //账户冻结，解冻
    public function onFreezeChange()
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

        $stu_id = I('post.stu_id');//学生 ID
        $type = I('post.type');//状态   1 冻结  0 解冻

        if ($stu_id === '' || $type === '' || ($type !== '0' && $type !== '1')) {
            $this->error('参数错误！');
        }

        $data['freeze'] = $type;
        $StuCardBalances = M("StuCardBalances");

        $res = $StuCardBalances->where(array("stu_id" => $stu_id))->save($data);
        if ($res === false) {
            $content = "操作失败";
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getUserType(), $this->getUserType(), 1, 2);
            $this->error("操作失败");
        } else {
            $content = "操作成功！";
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getUserType(), $this->getUserType(), 1, 1);
            $this->success('操作成功！');
        }

    }

    // 学生卡消费清单
    public function getStuCardBill()
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
        $stu_id = I('post.stu_id');//学生ID
        if (empty($stu_id)) {
            $this->error('参数错误！');
        }
        $where['stu_id'] = $stu_id;
        $pagesize = 20;
        $page = I('post.page');
        $sort = I('post.sort', 'create_time');
        $order = $sort . ' ' . I('post.order', 'desc');
        $StuCardBill = D("StuCardBill");//消费记录表
        $result = $StuCardBill->queryListEX('*', $where, $order, $page, $pagesize, '');
        if ($result === false) {
            $this->error('查询失败');
        }
        $billList = $result['list'];
        $billCount = $result['count'];
        $arr = array();
        $page_szie = ceil($billCount / $pagesize);
        $arr['total_page'] = $page_szie;
        $arr['total_count'] = $billCount;
        $arr['page'] = $page;
        $arr['content'] = $billList;

        $this->success($arr);
    }

    //零钱充值记录
    public function SmallchangeRecord()
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
        $paymentNotice = D('PaymentNotice');
        $pagesize = $this->PAGESIZE;
        $p = I('post.page', 1);
        $orderby = "id desc";
        $user_id = $this->getUserId();
        $where['user_id'] = $user_id;
        $where['is_paid'] = 1;
        $result = $paymentNotice->queryListEx('*', $where, $orderby, $p, $pagesize);
        $recordList = $result['list'];
        $recordCount = $result['count'];
        $arr = array();
        $page_szie = ceil($recordCount / $pagesize);
        $arr['total_page'] = $page_szie;
        $arr['total_count'] = $recordCount;
        $arr['page'] = $p;
        $arr['content'] = $recordList;
        $this->success($arr);
    }

    //将学生卡充值信息存入订单
    private function addOrder($account)
    {
        $user_id = $this->getUserId();
        //创建订单
        $data3['user_id'] = $user_id;
        $where['user_id'] = $user_id;
        $username = M('User','think_')->where($where)->getField("username");
        $data3['user_name'] = $username;
        $data3['deal_price'] = $account;//单价
        $data3['total_price'] = $account;//总价
        $data3['memo'] = '学生卡充值';//备注
        $data3['payment_id'] = 3;//支付方式 0支付宝 1微信 2.银联 3.学生卡充值
        $data3['type'] = 1; //学生卡余额充值
        $data3['num'] = 1; //个数
        $data3['deal_name'] = "学生卡充值";
        $data3['create_time'] = date("Y-m-d H:i:s");
        $order_id = M("DealOrder")->add($data3);

        if ($order_id) {
            //写入支付订单详情
            $data4['notice_sn'] = build_order_no();//内部订单号
            $data4['create_time'] = $data3['create_time'];
            $data4['order_id'] = $order_id;
            $data4['user_id'] = $user_id;
            $data4['payment_id'] = 3;
            $data4['memo'] = '学生卡充值';
            $data4['money'] = $account;
            $res = M("PaymentNotice")->add($data4);
        }
        if($res){
            return $data4['notice_sn'];
        }
    }

}

?>