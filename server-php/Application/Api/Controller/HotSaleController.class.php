<?php
/**
 * 首页
 * 生成时间：2017-11-13
 * 作者：zhangyumeng
 * 修改时间：
 * 修改备注：newApp-推荐商品
 */

namespace Api\Controller;

use Think\Controller;

class HotSaleController extends Controller
{
    public function query(){
    /* if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法');
        }*/
        $where['image'] = array('NEQ','');
        $result = D('GoodsDetail')->where($where)->field('id,image,url_pc_short')->order('id desc')->limit(3)->select();
        $this->success($result);
    }
}
?>