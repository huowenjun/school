<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：fuqian
 * 修改时间：
 * 修改备注：平安校园-GPS 考勤设置
 */
namespace Admin\Controller\Atnd;

use Admin\Controller\BaseController;

class GpsAtndSetController extends BaseController
{
    /**
     *
     */
    public function index()
    {
        $this->display();
    }


    public  function get(){
        $where['a_id'] = I('get.a_id');
        $Area = D('SchoolArea');
        $resInfo = $Area->where($where)->find();
        $this->success($resInfo);
    }




    // 查看
    public function query(){
        $a_id = I('post.a_id');
        // var_dump($a_id);
        // $where['a_id'] = I('post.a_id');;//学生stuid
        // var_dump($where);
        $ga_id = I('get.ga_id');//主键id
        if(!empty($ga_id)){
            $where['ga_id'] =$ga_id;
        }
        if(!empty($a_id)){
            $where['a_id'] =$a_id;
        }
        $page = I("post.page",1);
        $pagesize = I("post.pagesize",$this->PAGE_SIZE);
        $sort = I('post.sort','ga_id');
        $order = $sort. ' ' . I('post.order','desc');
        $GpsAttendanceSet = D('GpsAttendanceSet');
        $res = $GpsAttendanceSet->queryListEx('*',$where,$order,$page,$pagesize,'');
        // var_dump($res['list']);
        // $this->assign("list",$res['list']);
        $this->success($res['list']);
    }





    //新建考勤围栏
    public function edit(){
        $data['s_id'] = I('post.s_id');//学校id
        $data['a_id'] = I('post.a_id');//校区id
        $data['name'] = I('post.name');//名字
        $data['point'] = I('post.point');//经,纬度坐标
        // $data['radii'] = I('post.radii');//半径
        $data['create_time'] = date('Y-m-d H:i:s');
        $GpsAttendanceSet = D('GpsAttendanceSet');
        if(!$GpsAttendanceSet->create($data)){
            $this->error($GpsAttendanceSet->getError());
        }else{
            //添加缓存
            S('aid:'.$data['a_id'],$data['point']);
            $res = $GpsAttendanceSet->add($data);
        }
        $content =  '新建成功';
        if($res === false){
            D('SystemLog')->writeLog($this->getModule(),$content,$this->getUserId(),$this->getType(),$this->getGroupId(),0,2);
            $this->error($GpsAttendanceSet->getError());
        }else{
            D('SystemLog')->writeLog($this->getModule(),$content,$this->getUserId(),$this->getType(),$this->getGroupId(),0,1);
            $this->success($content,U('/Admin/Atnd/GpsAtndSet/index'));
        }
    }


    //删除
    public function del(){
        $ga_id = I('post.ga_id');
        // $arrUids = explode(',', $stu_id);
        if(empty($ga_id)){
            $this->error('参数错误');
        }
        $GpsAttendanceSet = D('GpsAttendanceSet');$okCount=0;

        $where['ga_id'] = $ga_id;
        $a_id = M('GpsAttendanceSet')->where($where)->getField('a_id');
        $ret=$GpsAttendanceSet->where($where)->delete();

        if($ret==1){
            //缓存围栏消息
            S('aid:'.$a_id,null);
            $okCount++;  //处理成功记录数
        }

        //写log
        $content = "删除".$okCount."条信息";
        $state = $okCount > 0?1:2;
        D('SystemLog')->writeLog($this->getModule(),$content,$this->getUserId(),$this->getType(),$this->getGroupId(),0,$state);
        $this->success('删除成功'.$okCount.'条记录');
    }



}