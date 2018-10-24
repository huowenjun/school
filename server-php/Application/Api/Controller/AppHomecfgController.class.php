<?php
/*
*我的班级
*by Mengfanmin date 20170323
*/

namespace Api\Controller;

use Api\Controller\BaseController;
use Think\Controller;

class AppHomecfgController extends BaseController
{
    /*
     * 重写父类方法
     * */
    protected function init()
    {

    }

    /*
     *数据查询 二合一项目
     *
     * */
    public function query()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法');
        }
        //获取主模块
        $parent = M('AppHomecfg')
            ->field('id,title,img,url,cell,pid')
            ->where("pid = 0 and is_effect = 1")
            ->select();
        if ($parent) {
            foreach ($parent as $key => $value) {
                //获取子集
                $child = M('AppHomecfg')
                    ->field('id,title,img,url,pid')
                    ->where("pid = '{$value['id']}' and is_effect = 1")
                    ->select();
                $parent[$key]['child'] = $child;
            }
        }
        $this->success($parent);
    }

    public function appVersion(){
        $data = D('app_version')->field('v_code,v_yesorno,v_path,v_memo')->order('v_id desc')->limit('1')->select()[0];
        $this->success($data);
    }

}