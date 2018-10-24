<?php
namespace Api\Controller;

use Api\Controller\BaseController;

class UserRefundController extends BaseController
{
    /**
     * 用户申请提现功能
     * @param string $money 提现金额
     * @param string $memo 提现备注
     * @param int $bank_id 银行卡id
     *
     * */
    public function addRefund()
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
        $memo = I("post.memo");
        $bankId = I("post.bank_id");
        $whereStr = array();
        if ($money > 0) {
            //判断用户余额
            $whereStr['u_id'] = $user_id;
            $account = M("UserAccount")->where($whereStr)->getField("account");
            if ($money > $account) {
                $this->error('余额不足');
            } else {
                //添加提现表
                $data['money'] = $money;
                $data['user_id'] = $user_id;
                $data['create_time'] = date("Y-m-d H:i:s");
                $data['is_pay'] = 0;
                $data['memo'] = $memo;
                $data['bank_id'] = $bankId;
                if (!M("UserRefund")->create($data)) {
                    $this->error(M("UserRefund")->getError());
                } else {
                    M("UserRefund")->startTrans();
                    $refund_id = M("UserRefund")->add($data);

                    //进行账户减操作
                    $accountData['account'] = $account-$money;
                    $res2 = M("UserAccount")->where($whereStr)->save($accountData);
                    if ($refund_id && $res2) {
                        $refundInfo = M("UserRefund")->where("id = '{$refund_id}'")->find();
                        $log_id = save_log(3, $refundInfo);
                    }
                    if($refund_id && $res2 && $log_id){
                        M("UserRefund")->commit();
                        $this->success('申请提现成功');
                    }else{
                        M("UserRefund")->rollback();
                        $this->error('申请提现失败');
                    }

                }

            }
        } else {
            $this->error('参数错误');
        }

    }

    /**
     * 提现列表查询
     * @param int $is_pay 0 表示未审核;1 表示 允许操作成功；2 表示 未允许操作成功;3 表示 提现确认成功
     * @param string $begin_time 查询日期
     * @param string $end_time 查询日期
     * */
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
        $user_id = $this->getUserId();
        $Refund = D("UserRefund");


        $is_pay = I("post.is_pay");
        $begin_time = I("post.begin_time");
        $end_time = I("post.end_time");
        $pagesize = I("get.pagesize", $this->PAGE_SIZE);
        $page = I('get.page');

        $order = " id desc ";

        $whereStr['user_id'] = $user_id;
        if ($is_pay !== "") {
            $whereStr['is_pay'] = $is_pay;
        }
        if ($begin_time) {
            $whereStr['create_time'] = array("EGT", $begin_time);
        }
        if ($end_time) {
            $whereStr['create_time'] = array("ELT", $end_time);
        }
        $res = $Refund->queryListEX("*", $whereStr, $order, $page, $pagesize);
        $this->success($res);
    }
}

?>