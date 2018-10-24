<?php
//修改备注：平安校园-足迹回放
namespace Wechat\Controller;
use Wechat\Controller\BaseController;
class TrackPlaybackController extends BaseController
{
    public function index()
    {
        $this->display();
    }

  public function query(){
        $ids = $this->authRule();
        $where="";
        if ($this->getType()==4) {
           $where['stu_id'] = array('IN',$ids['stu_id']);
        }else{

        }
        $stu_id = I('get.stu_id');
        if (!empty($stu_id)) {
            $where['stu_id'] = I("get.stu_id");//学生stu_id
        # code...
        }
        // $where['stu_id'] = I("get.stu_id");//学生stu_id
        $where['type'] = I('get.type');
        $starttime = I("get.sdatetime");//开始时间
        $endtime = I("get.edatetime");//结束时间
        $pagesize = I("get.pagesize");
        $page = I('get.page');
        $sort = I('get.sort','create_time');
        $order = $sort. ' ' . I('get.order','ASC');
        if($starttime && $endtime){
            $where['create_time'] = array("BETWEEN",array($starttime,$endtime));
         }
//        $where['longitude']=array('NEQ',0);//经度
//        $where['latitude']=array('NEQ',90.0000);//纬度
        $where['longitude'] = array('GT',73.33);
        $where['longitude'] = array('LT',135.05);
        $where['latitude'] = array('GT',3.51);//纬度
        $where['latitude'] = array('LT',53.33);//纬度
        $Trail = D('Trail');
        $res = $Trail->queryListEx('*',$where,$order,$page,$pagesize,'');
        $this->success($res['list']);

     }

        // var data = [{
        //     title: "张三",
        //     value: "367",
        // },
        // {
        // title: "李四",
        // value: "002",
        // },

     public function get_list(){
        // $ids = $this->authRule();
        // $where="";
        // if ($this->getType()==1) {
        //   $where['s_id'] = $ids['s_id'];
        // }elseif ($this->getType()==3) {
        //   $where['s_id'] = $ids['s_id'];
        //   $where['a_id'] = $ids['a_id'];
        // }elseif ($this->getType()==4) {
        //   $where['s_id'] = $ids['s_id'];
        //   $where['a_id'] = $ids['a_id'];
        //   $where['stu_id'] = array('IN',$ids['stu_id']);
        // }else{

        // }

        $ids = $this->authRule();
        $where="";
        if ($this->getType()==4) {
        // $where['s_id'] = $ids['s_id'];
        // $where['a_id'] = $ids['a_id'];
             $where['stu_id'] = array('IN',$ids['stu_id']);
        }else{

        }


        // $res = D('Student')->where($where)->getField('stu_id as id , stu_name as value');
        // var_dump($res);
        $res = D('Student')->where($where)->field("stu_name as title,stu_id as value" )->select();
        //  echo "<pre>";
        // var_dump($res);die;
        $this->success($res);
     }

     //判断是否绑定设备
     public function  get1(){

      $where['stu_id'] = I('get.stu_id');
      $res = D('DeviceManage')->queryList('*',$where,$order,$page,$pagesize,'');
      $this->success($res);
     }
}