<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/7 0007
 * Time: 13:11
 */
/**
 * app版本控制
 * 生成时间：2016-09-26
 */
namespace Admin\Controller\AppVersions;

use Admin\Controller\BaseController;
use Think\Controller;

class VersionsController extends BaseController
{
    public function index(){
        $this->display();
    }
    /*
     * 查找修改的单条数据
     * 请求方式：get
     * 参数：v_id
     * url:index.php/admin/AppVersions/Versions/query
     */
    public function get(){
        $v_id = I('get.v_id');
        $page = I('get.page', 1);
        $pagesize = I('get.pagesize', 20);
        if($v_id){
            $data = D('app_version')->where(['v_id'=>$v_id])->find();
            $data['v_time']=date("Y-m-d H:i",$data['v_time']);
            if($data['v_upgrade']==1){
                $data['v_upgrade']="是";
                $data['v_yon']=1;
            }else{
                $data['v_upgrade']="否";
                $data['v_yon']=0;
            }
        }else{
            $list = D('app_version')->page($page, $pagesize)->order('v_id desc')->select();
            foreach ($list as &$value){
                $value['v_time']=date("Y-m-d H:i:s",$value['v_time']);
                if($value['v_upgrade']==1){
                    $value['v_upgrade']="是";
                }else{
                    $value['v_upgrade']="否";
                }
            }
            $count = D('app_version')->count();
            $data = getPageList($list, $count, $page, $pagesize, 1);
        }
        $this->success($data);
    }
    /*
     * pc端添加或者修改版本信息
     * 请求方式：post
     */
    public function edit(){
    $v_id = I('post.v_id',0);
    $data['v_code']     = I('post.v_code');
    if(!$data['v_code']){
        $this->error('版本号不能为空');
    }
    $data['v_name']     = I('post.v_name');
    if(!$data['v_name']){
        $this->error('版本名称不能为空');
    }
    $data['v_upgrade']  = I('post.v_upgrade');
    $data['v_path']     = I('post.v_path');
    $data['v_memo']     = I('post.v_memo');
    if(!$data['v_memo']){
        $this->error('备注不能为空');
    }
    $data['v_time']     = strtotime(date('Y-m-d H:i:s'));
    if($v_id){//修改
        $bool = D('app_version')->where(['v_id'=>$v_id])->save($data);
        if($bool){
            $this->success('修改成功');
        }else{
            $this->error('修改失败');
        }
    }
    //添加
    $bool = D('app_version')->add($data);
    if($bool){
        $this->success('添加成功');
    }else{
        $this->error('添加失败');
    }

    }
    /*
     * pc端删除版本信息
     */
    public function del(){
        $v_id = I('post.v_id',0);
        if(!$v_id){
            $this->error("缺少请求参数");
        }
        $bool = D('app_version')->where(['v_id'=>['in',"$v_id"]])->delete();
        if($bool){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }

    }


}