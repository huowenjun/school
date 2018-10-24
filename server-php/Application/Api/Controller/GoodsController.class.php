<?php

namespace Api\Controller;

use Think\Controller;

class GoodsController extends Controller
{
    //获取商品列表接口
    public function getGoodsList()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }

        $cate_id = I('post.cate_id');//g_1智能手表 g_2视力矫正器 g_3最强大脑 g_4明亮眼睛 g_5增高减肥 g_6重病
        if (!$cate_id) {
            $this->error('参数错误');
        }
        $where['cate_id'] = $cate_id;
        $where['is_effect'] = 1;
        $list = M('GoodsDetail')
            ->field('id,brand,address,describe,price,shop_name,image')
            ->where($where)
            ->order('sort asc')
            ->select();
        $this->success($list);
    }

    //获取商品详情
    public function getGoodsDetail()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }

        $id = I('post.id/d');
        $model = M('GoodsDetail');

        $info = $model->where(array('id' => $id))->find();
        $info['desc'] = str_replace('<img src="/uploads','<img width="100%" src="http://school.xinpingtai.com/uploads',$info['desc']);

        if (empty($info)) {
            $this->error('网络延迟,请稍后再试.');
        }
        $this->success($info);
    }
}

?>