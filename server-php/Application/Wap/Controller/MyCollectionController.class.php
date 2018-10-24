<?php
namespace Wap\Controller;
use Think\Controller;
class MyCollectionController extends Controller {
    /**
     *我的收藏
     */
    public function index(){

        $this->display();
    }
    public function query(){
        $userId = I('get.user_id');
        $page = I('get.page');
        $pagesize = I('get.pagesize');
        $Collect = D('Collect');
        $where['user_id'] = $userId;
        $result = $Collect->queryListEx('*', $where,'',$page,$pagesize, '');
        $this->success($result);
    }
}