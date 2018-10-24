<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-APP首页配置
 */

namespace Admin\Controller\Learning;

use Admin\Controller\BaseController;

class AppHomecfgController extends BaseController
{

    public function index()
    {
        $this->display();
    }

    public function query()
    {
        $where['pid'] = 0;
        $where['is_effedt']=1;
        $pidList = D('AppHomecfg')->where($where)->select();
        foreach ($pidList as $key => $value) {
            $where1['pid'] = $value['id'];
            $where1['is_effect'] = 1;
            $List = D('AppHomecfg')->where($where1)->select();
                if($List){
                    $pidList[$key]['array'] = $List;
                }
        }
        $this->success($pidList);
    }

    //编辑修改首页配置
    public function edit()
    {
        $id = intval(I('post.id'));

        $data['pid'] = intval(I('post.pid'));//父ID
        $data['title'] = I('post.title');
        $data['img'] = I('post.img');
        $data['url'] = I('post.url');
        $data['sort'] = I('post.sort');
        $data['is_effect'] = I('post.is_effect',1);
        $data['cell'] = I('post.cell');
        if (!$id) {//新增
            if(M('AppHomecfg')->create($data)){
                $homecfg_id = M('AppHomecfg')->add($data);
            }
        } else {//编辑
            $data['id'] = $id;
            if(M('AppHomecfg')->create($data)){
                $homecfg_id = M('AppHomecfg')->where("id = '{$id}'")->save($data);
            }
        }
        if ($homecfg_id > 0) {
            $this->success('设置成功,id:'.$homecfg_id);
        } else {
            $this->error('设置失败');
        }
    }

    //删除APP首页配置
    public function del(){
        $id = intval(I('get.id'));
        $pid = D('AppHomecfg')->where(array('pid'=>$id))->find();
        if (!empty($pid)){
            $this->error('删除失败!');
        }else{
            $where['id'] = $id;
            $ret = D('AppHomecfg')->where($where)->delete();
            if ($ret){
                $content = "删除成功!";
            }
        }
        $this->success($content);
    }
}