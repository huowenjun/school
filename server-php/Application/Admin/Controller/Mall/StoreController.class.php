<?php
namespace Admin\Controller\Mall;
use Admin\Controller\BaseController;
class StoreController extends BaseController {
    public function index(){
        //商品分类
        $category = D('GoodsCategory');
        $categoryList = $category->where(array('parent_id'=>0))->select();
        $this->categoryList=$categoryList;
        //子分类
        $id = I('get.cate_id');//一级菜单id
        $categorySubList = $category->where(array('parent_id'=>$id))->select();
        $this->categorySub=$categorySubList;
        //金额区间
//        $priceList = array(1=>'0-99',3=>'100-199',4=>'200-299',5=>'300-399',6=>'400-499',7=>'500-699',8=>'700-999',9=>'1000-1499',10=>'1500-1999',11=>'2000以上');
//        $this->priceList=$priceList;
        //品牌分类
        $brandList = D('GoodsBrand')->select();
//        $brandArr = array();
//        foreach ($brandList as $key=>$value){
//                $brandArr[$key] = $value['name'];
//        }
        $this->brandList=$brandList;
        $this->display();
    }
    public function query(){
        $keyword = I('get.keyword');
        $category =I('get.category'); //产品分类
        $subcategories = I('get.subcategories'); //子分类
//        $price = I('get.price'); //价格
        $sprice = I('get.sprice');
        $eprice = I('get.eprice');
        $brand = I('get.brand'); //品牌分类
        $page = I('get.page');
        $pagesize = I('get.pagesize');
        $orderType = I('get.orderType');//排序
        switch ($orderType){ //1 价格低→高   2 价格高→低     3 销量低→高   4 销量高→低
            case 1 :
                $order = 'price';
                break;
            case 2 :
                $order = 'price desc';
                break;
            case 3 :
                $order = 'sales';
                break;
            default :
                $order ='sales desc';
                break;
        }
        if (!empty($keyword)){
            $where['describe'] = array('like',"%$keyword%");
//            $keywordRet = D('GoodsDetail')->where($where)->select();
//            if(empty($keywordRet)){
//                $this->error('未查询到相关内容');
//            }
        }
        if (!empty($category)){
            $where['cate_id'] = $category;
        }
        if (!empty($subcategories)){
            $where['cate_id'] = $subcategories;
        }
//        if ($price != '2000以上' || !empty($price)){
//            $prices = explode('-',$price);
//            $where['price'] = array('between',array($prices[0],$prices[1]));
//        }elseif ($price = '2000以上'){
//            $where['price'] = array('EGT',$price);
//        }
        if (!empty($sprice) || !empty($eprice)){
            $where['price'] = array('between',array($sprice,$eprice));
        }
        if (!empty($brand)){
            $where['brand'] = array('IN',$brand);
        }
        $map['brand']  = array('NEQ','');
        $map['address']  = array('NEQ','');
        $map['describe']  = array('NEQ','');
        $where['_complex'] = $map;
        $res=D('GoodsDetail')->where($where)->count();
        $where1['user_id'] = $this->getUserId();
        $collectList = M('Collect')->where($where1)->getField('product_id',true);
        $result = D('GoodsDetail')->queryListEx('*', $where, $order,$page,$pagesize, '');

        foreach ($result['list'] as $k=>$v){
            // 0 收藏  1 已收藏
            if (in_array($v['id'], $collectList)) {
                $result['list'][$k]['status'] = '1';
            }else{
                $result['list'][$k]['status'] = '0';
            }

        }
        $result['count'] = $res;
        $this->success($result);
    }

    //热卖排行
    public function hotProducts(){
        $result = D('GoodsDetail')->order('sales desc')->limit('0,2')->select();
        $this->success($result);
    }

    //收藏商品  1已收藏 0取消收藏
    public function collect(){
        $user_id = $this->getUserId();
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
        $user_id = $this->getUserId();
            $id = I('get.id',3);
            $where['product_id'] = $id;
            $where['user_id'] = $user_id;
            $ret = D('Collect')->where($where)->delete();
            $this->success('已取消!');
    }
}