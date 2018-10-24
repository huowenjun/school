<?php

//修改备注：平安校园-实时监控
namespace Wechat\Controller;
use Wechat\Controller\BaseController;
class MonitorController extends BaseController
{
    public function index()
    {
        $this->display();
    }

    public function query(){

        $ids = $this->authRule();
        $where="";
        if ($this->getType()==4) {
            // $where['s_id'] = $ids['s_id'];
            // $where['a_id'] = $ids['a_id'];
            $where['stu_id'] = array('IN',$ids['stu_id']);
        }else{

        }
        $stu_id = I('get.stu_id');
        if (!empty($stu_id)) {
         $where['stu_id'] = I("get.stu_id");//学生stu_id
            # code...
        }
        $type = I('get.type');
        $Trail = D('Trail');
        $DEVICE_TYPE = C('DEVICE_TYPE'); 
        $TYPE = C('TYPE'); 
//        $where['longitude']=array('NEQ',0);//经度
//        $where['latitude']=array('NEQ',90.0000);//纬度
        $where['longitude'] = array('GT',73.33);
        $where['longitude'] = array('LT',135.05);
        $where['latitude'] = array('GT',3.51);//纬度
        $where['latitude'] = array('LT',53.33);//纬度
        $where['type']=$type;
        $res = $Trail->where($where)->order('create_time desc')->limit(1)->find();
        if($res){
            if (0<=$res['signal1']&&$res['signal1']<=12  ) {
            $res['signal'] = 20;
            }elseif (13<=$res['signal1']&&$res['signal1']<=18) {
            $res['signal'] = 40;
            }elseif (19<=$res['signal1']&&$res['signal1']<=23) {
            $res['signal'] = 60;
            }elseif (24<=$res['signal1']&&$res['signal1']<=28) {
            $res['signal'] = 80;
            }elseif (29<=$res['signal1']&&$res['signal1']<=31) {
            $res['signal'] = 100;
            }

            $res1 = D('DeviceManage')->where(array("stu_id"=>$stu_id))->getField('onoff',false);
            $res2 = D('Student')->where(array("stu_id"=>$stu_id))->find();
            $res['sex'] = $res2['sex'];
            $res['imei_id'] = $res2['imei_id'];
            $res['type'] = $TYPE[$res['type']]?$TYPE[$res['type']]:'';
            $res['onoff'] = $DEVICE_TYPE[$res1]?$DEVICE_TYPE[$res1]:'';
            $this->success($res);

        }else{
            $res = array();
            $this->success($res);
        }

    }

    public function get_list(){
        $ids = $this->authRule();
        $where="";
       if ($this->getType()==4) {
          $where['stu_id'] = array('IN',$ids['stu_id']);
        }else{
        }
        $res = D('Student')->where($where)->field("stu_name as title,stu_id as value" )->select();
        $this->success($res);
     }

     //判断是否绑定设备
     public function  get1(){

      $where['stu_id'] = I('get.stu_id');
      $res = D('DeviceManage')->queryList('*',$where,$order,$page,$pagesize,'');
      $this->success($res);
     }

}