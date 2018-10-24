<?php
/**
 * 首页
 * 生成时间：2015-01-01
 * 作者：qianzhiqiang
 * 修改时间：
 * 修改备注：
 */

namespace Admin\Controller;

use Admin\Controller\BaseController;

class IndexController extends BaseController
{
    public function index()
    {
        if ($this->getType() == 3 || $this->getType() == 4 || $this->getType() == 1) {
            $authRule = $this->authRule();
            $where['s_id'] = $authRule['s_id'];
            $School = D('SchoolInformation')->where($where)->find();
            $this->School = $School;
        } else {
            $School = D('SchoolInformation')->order('s_id desc')->find();
            $this->School = $School;
        }
        $this->display();
    }


    public function home()
    {
        $this->display();
    }

    public function get_tree()
    {
        if ($_GET['type']) {
            $param = $_GET['type'];
        } else {
            $param = '';
        }
        if ($_GET['range']) {
            $range = $_GET['range'];
        } else {
            $range = '';
        }
        $arrRet = $this->getTreeList($param, $range);
//        echo $arrRet;
        $this->success($arrRet);
    }

    public function get_tree1()
    {
        $arrRet = $this->getTreeList();
        echo $arrRet;
        //$this->success($arrRet);
    }
}