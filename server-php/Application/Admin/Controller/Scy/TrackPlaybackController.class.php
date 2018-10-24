<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-足迹回放
 */

namespace Admin\Controller\Scy;

use Admin\Controller\BaseController;

class TrackPlaybackController extends BaseController
{
    /**
     *足迹回放
     */
    public function index()
    {

        $this->display();
    }


    public function query()
    {
        $stu_id = I("get.stu_id");
        $state = I("get.state", 0);//绑路 状态 0 未绑路 1  绑路

        $imei = M('Student')->where("stu_id = '{$stu_id}'")->getField('imei_id');
        if (!$imei) {
            $this->error('未绑定imei号');
        } else {
            $where['imei'] = $imei;
        }
//        $where['type'] = array('in','0,3');
//        $where['type'] = 3;
        $starttime = I("get.sdatetime");//开始时间
        $endtime = I("get.edatetime");//结束时间
        $pagesize = I("get.pagesize", $this->PAGE_SIZE);
        $page = I('get.page');
        $sort = I('get.sort', 'create_time');
        $order = $sort . ' ' . I('get.order', 'ASC');
        if ($starttime && $endtime) {
            $where['create_time'] = array("BETWEEN", array($starttime, $endtime));
        }
        if ((strtotime($endtime) - strtotime($starttime)) > 86400) {
            $this->error("起始和结束时间必须在24小时之内");
        }
        $where['longitude'] = array('neq', '0');

        $before = date('Y-m-d', strtotime('-15 day'));


        if ($starttime < $before && $endtime < $before) {
            $trail_id = get_trail_id($imei);
            $Trail = M('Trail_' . $trail_id);
        } else {

            $Trail = M('Trail');
        }

        $res1 = D('Student')->where(array("stu_id" => $stu_id))->find();
        $res = $this->queryListEx($Trail, '*', $where, $order, $page, $pagesize, '');
        if (!$res['list']) {
            $this->error('此时间间隔内该学生没有足迹回放数据！');
        }
        if ($state == 1) {
            $data = TraceReplay($res['list'], $state, $res1, $starttime, $endtime);
            $type = gettype($data);
            if ($type == 'array') {
                $this->success($data);
            } else {
                $this->error($data);
            }
        } else {
            $this->success($res['list']);
        }
    }

    /**
     * 程序封装处理
     *
     * */
    private function queryListEx($obj, $fields, $where = null, $orderby = null, $page = 0, $pagesize = 0, $groupby = null, $data_auth = null)
    {
        if ($page) {
            $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
        }
        if ($page) {
            $ret['list'] = $obj->field($fields)->where($where)->order($orderby)->page($page, $pagesize)->group($groupby)->select();
        } else {
            $ret['list'] = $obj->field($fields)->where($where)->order($orderby)->group($groupby)->select();
        }
        if ($page == 0) {
            $ret ["count"] = count($ret ["list"]);
        } else {
            $ret ["count"] = $this->getCount(); //获得记录总数
        }

        $TYPE = C("TYPE");
        foreach ($ret['list'] as $k => $v) {
            //获取经纬度数据
            if ($v['type'] == 3) {//原始GPS坐标
                $point_info = array($v['longitude'], $v['latitude']);
                $point = get_gps_point($point_info, $v['type']);
                $type = "GPS定位";
            } elseif ($v['type'] == 1) {//基站信息处理
                $point_info = array(
                    'mcc' => $v['mcc'],
                    'mnc' => $v['mnc'],
                    'lac' => $v['lac'],
                    'cid' => $v['cid'],
                );
                $point = get_gps_point($point_info, $v['type']);
                $type = "基站定位";
                if ($point['type'] == 2) {
                    $this->error('获取基站位置信息失败');
                }
            } elseif ($v['type'] == 0) {//GPS坐标
                $point = array($v['longitude'], $v['latitude']);
                $type = "GPS定位";
            }
            $ret['list'][$k]['longitude'] = $point[0];
            $ret['list'][$k]['latitude'] = $point[1];

            $ret['list'][$k]['sex'] = M("Student")
                ->cache(true)
                ->where("imei_id = '{$where['imei']}'")
                ->getField('sex');
            $ret['list'][$k]['type'] = $TYPE[$v['type']] ? $TYPE[$v['type']] : '';
            if (0 <= $ret['list'][$k]['signal1'] && $ret['list'][$k]['signal1'] <= 12) {
                $ret['list'][$k]['signal'] = 20;
            } elseif (13 <= $ret['list'][$k]['signal1'] && $ret['list'][$k]['signal1'] <= 18) {
                $ret['list'][$k]['signal'] = 40;
            } elseif (19 <= $ret['list'][$k]['signal1'] && $ret['list'][$k]['signal1'] <= 23) {
                $ret['list'][$k]['signal'] = 60;
            } elseif (24 <= $ret['list'][$k]['signal1'] && $ret['list'][$k]['signal1'] <= 28) {
                $ret['list'][$k]['signal'] = 80;
            } elseif (29 <= $ret['list'][$k]['signal1'] && $ret['list'][$k]['signal1'] <= 31) {
                $ret['list'][$k]['signal'] = 100;
            }

        }

        return $ret;
    }

    protected function getCount()
    {
        $result = self::query("select FOUND_ROWS() as count");
        return $result[0]["count"];
    }


    //判断是否绑定设备
    public function get()
    {

        $where['stu_id'] = I('get.stu_id');
        $res = D('DeviceManage')->queryList('*', $where, $order, $page, $pagesize, '');
        $this->success($res);
    }

    public function get_list()
    {
        $ids = $this->authRule();
        $where = "";
        $where['s_id'] = $ids['s_id'];
        $where['a_id'] = $ids['a_id'];
        $where['stu_id'] = array('IN', $ids['stu_id']);
        $res = D('Student')->where($where)->getField('stu_id as id , stu_name as value');
        $this->success($res);
    }
}