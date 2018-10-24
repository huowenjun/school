<?php

namespace Admin\Controller\Mall;

use Admin\Controller\BaseController;

class CommodityController extends BaseController
{
    public function index()
    {
        $this->display();
    }

    //商品列表
    public function query()
    {
        $cate_id = I('get.keyword');
        $page = I('get.page');
        $pagesize = I('get.pagesize', $this->PAGE_SIZE);
        $sort = I('get.sort', 'id');
        $order = $sort . ' ' . I('get.order', 'desc');
        if (!empty($cate_id)) {
            $where['cate_id'] = $cate_id;
        }
        $result = D('Goods')->queryListEx('*', $where, $order, $page, $pagesize, '');
        $this->success($result);
    }

    //商品修改
    public function get()
    {
        $id = I('get.id/d', 0);
        $GoodsCategory = D('Goods');
        $info = $GoodsCategory->getInfo($id);
        if ($id > 0 && empty($info)) {
            $this->error('商品不存在');
        }
        $this->success($info);
    }

    // 商品添加/修改
    public function edit()
    {
        $Goods = D('Goods');
        $id = I('post.id');
        $data['cate_id'] = I('post.cate_id');
        $data['url_pc_short'] = I('post.url_pc_short');//'https://s.click.taobao.com/HoHxyZw'
        $goodsHTML = $this->getGoodsHtml($data['url_pc_short']);

        if (!$goodsHTML['info']) {
            $this->error('商品短链接不合法');
        }
        //写入goods表
        if (!$Goods->create($data)) {
            $this->error($Goods->getError());
        } else {
            if ($id > 0) {
                $data['id'] = $id;
                $good_id = $Goods->save($data);
            } else {
                $good_id = $Goods->add($data);
            }
        }
        if (!$good_id) {
            $this->error('网络繁忙稍后再试');
        }
        //获取商品详情 并 写入detail表
        $prov = stripos($goodsHTML['info'], 'TShop.Setup');
        $a = substr($goodsHTML['info'], $prov);

        $kaitou = stripos($a, '(');
        $jiewei = stripos($a, ')');
        $s = $jiewei - $kaitou;
        $name = substr($a, $kaitou, $s);
        $goodsInfo = json_decode(ltrim($name, "("), true);

        $info['goods_id'] = $good_id;
        $info['cate_id'] = $data['cate_id'];
        $info['url_pc_short'] = $goodsHTML['url'];
        $info['address'] = $goodsInfo['itemDO']['prov'];
        $info['describe'] = $goodsInfo['itemDO']['title'];
        $info['price'] = intval($goodsInfo['detail']['defaultItemPrice']);
        $info['url_wap_short'] = $data['url_pc_short'];
//        $info['brand'] = $goodsInfo['itemDO']['brand'];
        $brand = $goodsInfo['itemDO']['brand'];
        $whereBrand['brand'] = $brand;
        $brandInfo = M('GoodsBrand')->where($whereBrand)->find();
        if (empty($brandInfo)){
            $data['brand'] = $brand;
            $ret = M('GoodsBrand')->add($data);
            if ($ret){
                $info['brand'] = $ret;
            }
        }else{
           $info['brand'] =  $brandInfo['id'];
        }
        $pattern = "/<strong>(.*?)<\/strong>/";
        preg_match($pattern, $goodsHTML['info'], $shop_name);
        $info['shop_name'] = $shop_name[1];
        $info['image'] = $goodsInfo['propertyPics']['default'][0];
        if (!$info['brand']) {
            $this->error('商品短链接不合法');
        }
        if ($id>0){
            $detail_id = M('GoodsDetail')->save($info);

        }else{
            $detail_id = M('GoodsDetail')->add($info);
        }
        if (!$detail_id) {
            $this->error('网络繁忙稍后再试');
        }
        
        $this->success('添加成功!');
    }

    //获取商品详情
    private function getGoodsHtml($url = 'https://s.click.taobao.com/HoHxyZw')
    {
        $html = file_get_contents($url);
        $responseinfo = $http_response_header;
        $aaaa = substr(strstr($responseinfo[13], "tu="), 3);
        $aaaa1 = urldecode($aaaa);
        $bbbb = substr(strstr($responseinfo[13], "Location"), 10);

        $header = [
            'referer:' . $bbbb,
        ];

        $oCurl = curl_init();
        curl_setopt($oCurl, CURLOPT_URL, $aaaa1);
        curl_setopt($oCurl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($oCurl, CURLOPT_HEADER, true);
        curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($oCurl, CURLOPT_POST, false);
        $sContent = curl_exec($oCurl);
        $headerSize = curl_getinfo($oCurl, CURLINFO_HEADER_SIZE);
        $header = substr($sContent, 0, $headerSize);
        curl_close($oCurl);

        $cccc = substr(strstr($header, "Location"), 10);

        $result = substr($cccc, 0, strpos($cccc, "Strict"));

        $html = file_get_contents($result);
        $data['url'] = $aaaa1;
        $data['info'] = iconv('GB2312', 'UTF-8', $html);

        return $data;
    }

    //商品删除
    public function del()
    {
        $id = I('post.id');
        $ids = explode(',', $id);
        if (empty($ids)) {
            $this->error('参数错误');
        }
        foreach ($ids as $key => $value) {
            $where1['id'] = $value;
            $where2['goods_id'] = $value;
            $ret1 = D('Goods')->where($where1)->delete();
            $ret2 = D('GoodsDetail')->where($where2)->delete();
        }

        //写log
        $content = "删除成功!";
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
        $this->success($content);
    }

    public function getList()
    {
        //商品分类下拉
        $categoryList = D('GoodsCategory')->getField('cate_id,name');
        $this->success($categoryList);
    }
}

?>