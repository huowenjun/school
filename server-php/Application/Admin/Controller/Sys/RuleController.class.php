<?php
/*
 * 系统—用户—规则列表
 * 生成时间：2015-01-01
 * 作者：qianzhiqiang
 * 修改时间：
 * 修改备注：
 */

namespace Admin\Controller\Sys;

use Admin\Controller\BaseController;

class RuleController extends BaseController
{
    public function index()
    {
        $this->display();
    }

    /**
     *角色输出
     */
    public function get_group()
    {
        $sort = I('get.sort', 'id');
        $orders = I('get.order', 'desc');
        $order = $sort . ' ' . $orders;
        $Group = D('AdminGroup');
        $result = $Group->queryListEx('a.*,count(c.user_id) as count', NULL, $order, '', '', '');
        $this->success($result);
    }

    /**
     *模块输出 权限
     */
    public function query()
    {
        $Rule = D('Rule');
        $id = I('get.id');
        // var_dump($id);
        $Group = D('AdminGroup');
        $where1['id'] = array('EQ', $id);
        $groupRule = $Group->where($where1)->find();

        $rules = $groupRule['rules'];
        $where['status'] = array('EQ', 1);
        $ruleList = $Rule->where($where)->field('*')->select();
        $arrruleId = explode(',', $rules);
        //dump($arrruleId);
        $arr = array();
        $arr1 = "";
        foreach ($ruleList as $key => $value) {
            $check = "";
            foreach ($arrruleId as $k => $v) {
                if ($v == $value['id']) {
                    $check = "true";
                }
            }

            $arrTemp = array();
            $arrTemp['id'] = $value['id'];
            $arrTemp['pId'] = $value['pid'];
            $arrTemp['name'] = $value['title'];
            $arrTemp['checked'] = $check;
            $arr[] = $arrTemp;

        }
        $arrs = $arr;

        $ouInput['data'] = json_encode($arrTemp);
        $ouInput['rules'] = $rules;

        $this->success($arrs);

    }


    //数据插入
    public function edit()
    {
        $id = I('post.id/d', 0);
        $data['id'] = $id;
        $data['rules'] = I('post.rules');
        $where['id'] = $id;
        $Group = D('AdminGroup');
        if (!$Group->create($data)) {
            $this->error($Group->getError());
        } else {
            $ret = $Group->where($where)->save($data);
            $content = "编辑角色权限成功";
            if ($ret === false) {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
                $this->error($Group->getError());
            } else {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                $this->success('保存成功', U('/Meet/Sys/Group/index'));
            }
        }
    }


}
