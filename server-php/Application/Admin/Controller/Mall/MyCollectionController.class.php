<?php
namespace Admin\Controller\Mall;
use Admin\Controller\BaseController;
class MyCollectionController extends BaseController {
    public function index(){
        $this->display();
    }
    //我的收藏
    public function query(){
        $keyword = I('get.keyword');
        $userId = $this->getUserId();
        $page = I('get.page');
        $pagesize = I('get.pagesize',$this->PAGE_SIZE);
        $Collect = D('Collect');
        if (!empty($keyword)){
            $where['describe'] = array('like',"%$keyword%");
        }
        $where['user_id'] = $userId;
        $result = $Collect->queryListEx('*', $where,'',$page,$pagesize, '');
        $res = $Collect->where($where)->count();
        $result['count'] = $res;
        $this->success($result);

    }

    //收藏删除
    public function del(){
        $ids = I('get.collect_id');
        $arrIds = explode(',', $ids);
        if (empty($arrIds)) {
            $this->error('参数错误');
        }
        $Collect = D('Collect');
        $okCount = 0;
        foreach ($arrIds as $k => $v) {
            $where['collect_id'] = $v;
            $ret = $Collect->where($where)->delete();
            if ($ret == 1) {
                $okCount++;  //处理成功记录数
            }
        }
        //写log
        $content = "删除" . $okCount . "条信息";
        $state = $okCount > 0 ? 1 : 2;
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, $state);
        $this->success('删除成功' . $okCount . '条记录');
    }

}