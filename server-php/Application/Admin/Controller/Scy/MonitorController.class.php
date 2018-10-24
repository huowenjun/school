<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-实时监控
 */
namespace Admin\Controller\Scy;

use Admin\Controller\BaseController;

class MonitorController extends BaseController
{
    /**
     *实时监控
     */
    public function index()
    {

        $this->display();
    }

    public function get()
    {
        $where['stu_id'] = I('get.stu_id');
        $res = D("Student")->where($where)->find();
        $this->success($res);
    }

    public function query2()
    {
        $stu_id = I('get.stu_id');
        $type = I('get.type');
        $Trail = D('Trail');
        $DEVICE_TYPE = C('DEVICE_TYPE');
        $where['longitude'] = array('neq', '0');
        // 通过学生stu_id 来获取该学生的 imei_id号
        $imei = D('Student')->where(array('stu_id'=>$stu_id))->getField('imei_id',false);
        $longitudestart = 73.33;
        $longitudeendtime = 135.05;
        $latitudestart = 3.51;//纬度
        $latitudeendtime = 53.33;//纬度

        $where['imei'] = $imei;
        $res = $Trail->where($where)->order('tr_id desc')->find();
       
        if ($res) {
            if (0 <= $res['signal1'] && $res['signal1'] <= 12) {
                $res['signal'] = 20;
            } elseif (13 <= $res['signal1'] && $res['signal1'] <= 18) {
                $res['signal'] = 40;
            } elseif (19 <= $res['signal1'] && $res['signal1'] <= 23) {
                $res['signal'] = 60;
            } elseif (24 <= $res['signal1'] && $res['signal1'] <= 28) {
                $res['signal'] = 80;
            } elseif (29 <= $res['signal1'] && $res['signal1'] <= 31) {
                $res['signal'] = 100;
            }

            $res1 = D('DeviceManage')->where(array("stu_id" => $stu_id))->getField('onoff', false);
            $res2 = D('Student')->where(array("stu_id" => $stu_id))->find();
            $res['sex'] = $res2['sex'];
            $res['imei_id'] = $res2['imei_id'];

            $res['onoff'] = $DEVICE_TYPE[$res1] ? $DEVICE_TYPE[$res1] : '';
            /////////////////////////////////
            //获取经纬度数据
            if ($res['type'] == 3) {//原始GPS坐标
                $point_info = array($res['longitude'], $res['latitude']);
                $point = get_gps_point($point_info, $res['type']);
                $type = "GPS定位";
            } elseif ($res['type'] == 1) {//基站信息处理
                $point_info = array(
                    'mcc' => $res['mcc'],
                    'mnc' => $res['mnc'],
                    'lac' => $res['lac'],
                    'cid' => $res['cid'],
                );
                $point = get_gps_point($point_info, $res['type']);
                $type = "基站定位";
                if ($point['type'] == 2) {
                    $this->error('获取基站位置信息失败');
                }
            } elseif ($res['type'] == 0) {//GPS坐标
                $point = array($res['longitude'], $res['latitude']);
                $type = "GPS定位";
            }
            $res['longitude'] = $point[0];
            $res['latitude'] = $point[1];

            //////////////////////////////////
            //过滤不合法定位
            if (($res['longitude'] > $longitudeendtime)
                || ($res['longitude'] < $longitudestart)
                || ($res['latitude'] > $latitudeendtime)
                || ($res['latitude'] < $latitudestart)) {
                $this->error('GPS信号弱!');
            }
            $operator = getMobileArea2($res2['stu_phone']);
            $res['type'] = $type;
            $res['power'] = intval($res['power']);
            $res['operator'] = $operator;
            $this->success($res);

        } else {
            $res = array();
            $this->success($res);
        }

    }


}