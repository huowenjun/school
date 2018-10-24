<?php
namespace Common\Model;

use Common\Model\BaseModel;

class UserRefundModel extends BaseModel
{
    public function queryListEX($fields, $where = null, $orderby = null, $page = 0, $pagesize = 0, $groupby = null, $data_auth = null)
    {
        if ($page) {
            $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
        }
        if ($page) {
            $ret['list'] = self::field($fields)->where($where)->order($orderby)->page($page, $pagesize)->group($groupby)->select();
        } else {
            $ret['list'] = self::field($fields)->where($where)->order($orderby)->group($groupby)->select();
        }

        if ($page == 0) {
            $ret ["count"] = count($ret ["list"]);
        } else {
            $ret ["count"] = $this->getCount(); //获得记录总数
        }

        return $ret;
    }

    public function queryListE($fields, $where = null, $orderby = null, $page = 0, $pagesize = 0, $groupby = null, $data_auth = null)//提现管理
    {
        if ($page) {
            $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
        }
        if ($page) {
            $ret['list'] = self::alias('a')->join('LEFT JOIN sch_bank_card b ON a.bank_id=b.id')->field($fields)->where($where)->order($orderby)->page($page,$pagesize)->group($groupby)->select();
        } else {
            $ret['list'] = self::alias('a')->join('LEFT JOIN sch_bank_card b ON a.bank_id=b.id')->field($fields)->where($where)->order($orderby)->page($page,$pagesize)->group($groupby)->select();
        }

        if ($page == 0) {
            $ret ["count"] = count($ret ["list"]);
        } else {
            $ret ["count"] = $this->getCount(); //获得记录总数
        }
        $AUDIT = C('AUDIT');
        foreach ($ret['list'] as $key=>$value){
            $where['id'] = $value['bank_id'];
            $bankInfo = D('BankCard')->where($where)->find();
            $ret['list'][$key]['information'] = '开户名:'.$bankInfo['cardholder'].','.$bankInfo['bankname'].',卡号:'.$bankInfo['card_no'];
            $ret['list'][$key]['is_pay'] = $ret['list'][$key]['is_pay'] = $AUDIT[$value['is_pay']]?$AUDIT[$value['is_pay']]:'';
        }

        return $ret;
    }
}