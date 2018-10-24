<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-安全区域
 */

namespace Admin\Controller\Scy;

use Admin\Controller\BaseController;

class SafeAreaController extends BaseController
{
    /**
     *安全区域
     */
    public function index()
    {
        // $where['stu_id'] = I('post.stu_id',0);//学生stuid
        // $stu_id = I('post.stu_id',0);//学生stuid
        // // var_dump($where);
        // // $sr_id = I('get.sr_id');//主键id
        // // if(!empty($sr_id)){
        //  //  $where['sr_id'] =$sr_id;
        // // }
        // $page = I("post.page",1);
        // $pagesize = I("post.pagesize",$this->PAGE_SIZE);
        // $sort = I('post.sort','sr_id');
        // $order = $sort. ' ' . I('post.order','asc');
        // $SafetyRegion = D('SafetyRegion');
        // $res = $SafetyRegion->queryList('*',$where,$order,$page,$pagesize,'');
        // // var_dump($res['list']);
        //  $this->assign("list",$res['list']);
        //  $this->stu_id=$stu_id;
        $this->display();
    }


    // 查看
    public function query()
    {
        $stu_id = I('post.stu_id');
        // var_dump($stu_id);
        // $where['stu_id'] = I('post.stu_id');;//学生stuid
        // var_dump($where);
        $sr_id = I('get.sr_id');//主键id
        if (!empty($sr_id)) {
            $where['sr_id'] = $sr_id;
        }
        if (!empty($stu_id)) {
            $where['stu_id'] = $stu_id;
        }
        $page = I("post.page", 1);
        $pagesize = I("post.pagesize", $this->PAGE_SIZE);
        $sort = I('post.sort', 'sr_id');
        $order = $sort . ' ' . I('post.order', 'desc');
        $SafetyRegion = D('SafetyRegion');
        $res = $SafetyRegion->queryListEx('*', $where, $order, $page, $pagesize, '');
        // var_dump($res['list']);
        // $this->assign("list",$res['list']);
        $this->success($res['list']);
    }

    //新建
    public function edit()
    {
        $data['s_id'] = I('post.s_id');//学校id
        $data['a_id'] = I('post.a_id');//校区id
        $data['g_id'] = I('post.g_id');//年级id
        $data['c_id'] = I('post.c_id');//班级id
        $data['name'] = I('post.name');//名字
        $data['stu_id'] = I('post.stu_id');//学生id
        $data['point'] = I('post.point');//经,纬度坐标
        // $data['radii'] = I('post.radii');//半径
        $data['create_time'] = date('Y-m-d H:i:s');
        if($data['stu_id']){
            $imei = M('Student')->where("stu_id = '{$data['stu_id']}'")->getField('imei_id');
            if(!$imei){
                $this->error('该学生未绑定学生卡');
            }
            $data['imei'] = $imei;
        }
        $SafetyRegion = D('SafetyRegion');
        if (!$SafetyRegion->create($data)) {
            $this->error($SafetyRegion->getError());
        } else {
            $res = $SafetyRegion->add($data);
        }
        $content = '新建成功';
        if ($res === false) {
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
            $this->error($Dept->getError());
        } else {
            //更新缓存
            $point = M('SafetyRegion')->where("imei = '{$imei}'")->getField('point',true);
            S('SafeArea:' . $imei, $point);
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
            $this->success($content, U('/Admin/Scy/SafeArea/index'));
        }
    }


    //删除
    public function del()
    {
        $sr_id = I('post.sr_id');
        // $arrUids = explode(',', $stu_id);
        if (empty($sr_id)) {
            $this->error('参数错误');
        }
        $SafetyRegion = D('SafetyRegion');
        $okCount = 0;

        $where['sr_id'] = $sr_id;

        $imei = $SafetyRegion->where($where)->getField('imei');

        //删除缓存
        $res = S("SafeArea:$imei", null);
        if($res){
            $ret = $SafetyRegion->where($where)->delete();
        }else{
            $this->error('网络延迟');
        }
        $okCount++;  //处理成功记录数


        //写log
        $content = "删除" . $okCount . "条信息";
        $state = $okCount > 0 ? 1 : 2;
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, $state);
        $this->success('删除成功' . $okCount . '条记录');
    }

    public function get_list()
    {
        $ids = $this->authRule();
        $where = "";
        if ($this->getType() == 1) {
            $where['s_id'] = $ids['s_id'];
        } elseif ($this->getType() == 3) {
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
        } elseif ($this->getType() == 4) {
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
            $where['stu_id'] = array('IN', $ids['stu_id']);
        } else {

        }

        $res = D('Student')->where($where)->getField('stu_id as id , stu_name as value');
        $this->success($res);
    }

}