<?php

namespace Admin\Controller\Mall;

use Admin\Controller\BaseController;

class GoodsController extends BaseController
{
    public function index()
    {
        $this->display();
    }

    //商品添加
    /*
     * 分类id:   cate_id  g_1智能手表 g_2视力矫正器 g_3最强大脑 g_4明亮眼睛 g_5增高减肥 g_6重病
    产品名称:   describe
    产品图片:   image
    品牌  :   brand
    厂家地址:   address
    价格  :   price
    名称(厂家/店铺)  :    shop_name
    排序  :   sort
     *
     * */
    public function edit()
    {
        $id = I('post.id');
        $cate_id = I('post.cate_id');
        $describe = I('post.describe');
        $image = I('post.image');
        $brand = I('post.brand');
        $address = I('post.address');
        $price = I('post.price');
        $shop_name = I('post.shop_name');
        $sort = I('post.sort');
        $desc = I('post.desc');
        $desc = htmlspecialchars_decode($desc);
        $is_effect = I('post.is_effect');

        $data['cate_id'] = $cate_id;
        $data['describe'] = $describe;
        if ($image) {
            $data['image'] = $image;
        }
        $data['brand'] = $brand;
        $data['address'] = $address;
        $data['price'] = $price;
        $data['shop_name'] = $shop_name;
        $data['sort'] = $sort;
        $data['desc'] = $desc;
        $data['type'] = 2;
        $data['create_time'] = date('Y-m-d H:i:s');

        $data['is_effect'] = $is_effect;

        $goods_detail = M('GoodsDetail');
        if ($id) {
            $res = $goods_detail->where("id = '{$id}'")->save($data);
        } else {
            $res = $goods_detail->add($data);

        }
        if ($res > 0) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }

    }

    //首页请求查询
    public function query()
    {
        $page = I('get.page');
        $pagesize = I('get.pagesize', 20);

        $model = M('GoodsDetail');
        $where['type'] = 2;
        if(I('get.keyword')){
            $where['cate_id'] = I('get.keyword');
        }


        $list = $model->where($where)->page($page, $pagesize)->order('sort asc')->select();
        $count = $model->where($where)->count();
        $IS_EFFECT = C('IS_EFFECT');
        //g_1智能手表 g_2视力矫正器 g_3最强大脑 g_4明亮眼睛 g_5增高减肥 g_6重病
        $image = '';
        $product = array('g_1' => '平安宝宝', 'g_2' => '最美妈妈', 'g_3' => '最强大脑', 'g_4' => '明亮眼睛', 'g_5' => '增高减肥', 'g_6' => '疑难重病');
        foreach ($list as $key => $value) {
            $list[$key]['is_effect'] = $IS_EFFECT[$value['is_effect']];
            $tmp_image = explode('|', $value['image']);
            $image = $tmp_image[0];
            $list[$key]['image'] = "<img width='100' src='" . $image . "'>";
            $list[$key]['cate_id'] = $product[$value['cate_id']];
        }
        $result['list'] = $list;
        $result['count'] = $count;
        $this->success($result);
    }

    public function get()
    {
        $id = I('get.id/d');
        $model = M('GoodsDetail');

        $info = $model->where(array('id' => $id))->find();

        if (empty($info)) {
            $this->error('网络延迟,请稍后再试.');
        }
        $this->success($info);
    }

    //删除
    public function delete()
    {
        $ids = I('post.id');
        if (empty($ids) || empty($ids)) {
            $this->error('参数错误!');
        }
        $model = M('GoodsDetail');

        $arrIds = explode(',', $ids);
        foreach ($arrIds as $key => $value) {
            $where['id'] = $value;
            $ret = $model->where($where)->delete();
        }

        $this->success('删除成功!');

    }

}