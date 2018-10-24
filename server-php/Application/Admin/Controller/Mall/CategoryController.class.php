<?php
namespace Admin\Controller\Mall;
use Admin\Controller\BaseController;
class CategoryController extends BaseController {
    /**
     *分类管理
     */
    public function index(){
        $this->display();
    }
    //子分类列表
    public function catesubList(){
        $cate_id = I('get.cate_id');//一级分类ID
        $page = I('get.page');
        $pagesize = I('get.pagesize', $this->PAGE_SIZE);
        $sort = I('get.sort', 'cate_id');
        $order = $sort . ' ' . I('get.order', 'desc');
        $GoodsCategory = D('GoodsCategory');
        if (!empty($cate_id)){
            $where['parent_id'] = $cate_id;
        }else{
            $where['parent_id'] = array('GT',0);
        }
        $result = $GoodsCategory->queryListEx('*', $where, $order, $page, $pagesize, '');
        $this->success($result);
    }

    //一级分类列表
    public function cateList(){
        $keyword = I('get.keyword');
        if (!empty($keyword)){
            $where1 = "name like'%".$keyword ."%'";
            $cateInfo = D('GoodsCategory')->where($where1)->select();
            $cate_id = '';
            if (empty($cateInfo)){
                $this->error('您输入的分类不存在!');
            }else{
                foreach($cateInfo as $value){
                    $cate_id = $cate_id.$value['cate_id'];
                }
            }
            $where['cate_id'] = array('IN',rtrim($cate_id,','));
        }
        $where['parent_id'] = 0;
        $list = D('GoodsCategory')->where($where)->select();
        foreach ($list as $key => $value){
            $result = array();
            $result['id'] = $value['cate_id'];
            $result['name'] = $value['name'];
            $arr[] = $result;
        }
        $this->success($arr);
    }

    //一级分类添加/修改
    public function cateEdit(){
        $cate_id = I('post.cate_id');
        $name = I('post.name');//分类名称
        $data['cate_id'] = $cate_id;
        $data['name'] = $name;
        $data['parent_id'] = 0;
        $GoodsCategory = D('GoodsCategory');
        if (!$GoodsCategory->create($data)){
            $this->error($GoodsCategory->getError());
        }else{
            if($cate_id > 0){
                $ret = $GoodsCategory->save($data);
            }else{
                $ret = $GoodsCategory->add($data);
            }
            $content = ($cate_id > 0 ? '编辑' : '添加') . '成功';
            if ($ret === false){
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
                $this->error($GoodsCategory->getError());
            }else{
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                $this->success($content, U('/Admin/Parent/Category/index'));
            }
        }
    }
    //一级列表删除
    public function cateDel(){
        $id =I('post.cate_id');
        $GoodsCategory = D('GoodsCategory');
        //判断该分类是否有子分类
        $wherePid['parent_id'] = $id;
        $subInfo = $GoodsCategory->where($wherePid)->find();
        //判断该分类是否有商品
        $whereSp['cate_id'] = $id;
        $spInfo = D('GoodsDetail')->where($whereSp)->find();
        if (!empty($subInfo)){
            $this->error('该分类有子分类,无法删除!');
        }elseif(!empty($spInfo)){
            $this->error('该分类下有商品,无法删除!');
        }else{
            $where['cate_id']=$id;
            $GoodsCategory->where($where)->delete();
        }
        $content = '删除一条记录';
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0,1);
        $this->success($content);
    }
    //子分类编辑
    public function get(){
        $id = I('get.cate_id/d', 0);
        $GoodsCategory = D('GoodsCategory');
        $info = $GoodsCategory->getInfo($id);
        if ($id > 0 && empty($info)) {
            $this->error('分类不存在');
        }
        $this->success($info);
    }

    //子分类修改
    public function catesubEdit(){
        $cate_id = I('post.cate_id');
        $id = I('post.id');//父ID
        $name = I('post.name');//分类名称
        $data['parent_id'] = $id;
        $data['cate_id'] = $cate_id;
        $data['name'] = $name;
        $GoodsCategory = D('GoodsCategory');
        if (!$GoodsCategory->create($data)){
            $this->error($GoodsCategory->getError());
        }else{
            $ret = $GoodsCategory->save($data);
        }
        $content = '编辑成功';
        if ($ret){
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
            $this->success($content, U('/Admin/Parent/Category/index'));
        }
    }

    //子分类添加
    public function catesubAdd(){
        $id = I('post.id');//父ID
        $name = I('post.name');//分类名称
        $data['parent_id'] = $id;
        $data['cate_id'] = $ret;
        $data['name'] = $name;
        $GoodsCategory = D('GoodsCategory');
        if (!$GoodsCategory->create($data)){
            $this->error($GoodsCategory->getError());
        }else{
            $ret = $GoodsCategory->add($data);
        }
        $content = '添加成功';
        if ($ret){
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
            $this->success($content, U('/Admin/Parent/Category/index'));
        }
    }

    //子分类删除
    public function catesubDel(){
        $id = I('post.cate_id');
        $ids = explode(',',$id);
        if (empty($ids)){
            $this->error('参数错误');
        }
        foreach($ids as $key=>$value){
            $where['cate_id'] = $value;
            $spInfo = D('GoodsDetail')->where($where)->find();//判断该分类是否有商品
            if(empty($spInfo)){
                $ret = D('GoodsCategory')->where($where)->delete();
            }else{
                $subInfo = D('GoodsCategory')->where($where)->getField('name',false);//分类名称
                $this->error($subInfo."分类下有商品,无法删除!");
            }
        }

        //写log
        $content = "删除成功!";
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
        $this->success($content);
    }
}
?>