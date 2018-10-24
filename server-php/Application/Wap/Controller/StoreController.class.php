<?php
namespace Wap\Controller;
use Think\Controller;
class StoreController extends Controller {
    public function index(){

        $this->display();
    }
    public function query(){
        $userId = I('get.user_id');
        $keyword = I('get.keyword');
        $sort = I('get.sort', 'id');
        $order = $sort . ' ' . I('get.order', 'asc');
        if (!empty($keyword)){
            $where['describe'] = array('like',"%$keyword%");
        }
        $map['brand']  = array('NEQ','');
        $map['address']  = array('NEQ','');
        $map['describe']  = array('NEQ','');
        $where['_complex'] = $map;
        $result = D('GoodsDetail')->queryListEx('*', $where, $order, '');
        $where1['user_id'] = $userId;
        $collectList = M('Collect')->where($where1)->getField('product_id',true);
        foreach ($result['list'] as $k=>$v){
            // 0 收藏  1 已收藏
            if (in_array($v['id'], $collectList)) {
                $result['list'][$k]['status'] = '1';
            }else{
                $result['list'][$k]['status'] = '0';
            }
        }
        $this->success($result);
    }
    //收藏商品  1已收藏 0取消收藏
    public function collect(){
        $user_id = I('get.user_id');
        $id = I('get.id');
        $where['product_id'] = $id;
        $where['user_id'] = $user_id;
        $data['type']=2;
        $data['product_id'] = $id;
        $data['user_id'] = $user_id;
        $ret = D('Collect')->add($data);
        $this->success('收藏成功!');
    }
    public function unfavorite(){//取消收藏
        $user_id = I('get.user_id');
        $id = I('get.id');
        $where['product_id'] = $id;
        $where['user_id'] = $user_id;
        $ret = D('Collect')->where($where)->delete();
        $this->success('取消收藏!');
    }
}