<?php
/*
*位置查询/轨迹/警报
*by ruiping date 20161201
*/

namespace Api\Controller;

use Api\Controller\BaseController;
use Think\Model;

class TrackController extends BaseController
{
    /*
    *实时位置
    *@param
    */
    public function realtimeLocation()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法！');
        }
        $stu_id = I('post.stu_id');
        $stu_info = M('Student')
            ->field('imei_id,stu_phone')
            ->where("stu_id = '{$stu_id}'")
            ->find();

        if (empty($stu_id)) {
            $this->error('参数错误！');
        }
        if (!$stu_info['imei_id']) {
            $this->error('学生暂未绑定设备！');
        }

        $where['imei'] = array('EQ', $stu_info['imei_id']);
        $where['type'] = array('neq', '2');//过滤非法定位

        $longitudestart = 73.33;
        $longitudeendtime = 135.05;
        $latitudestart = 3.51;//纬度
        $latitudeendtime = 53.33;//纬度

        $where['longitude'] = array('neq', '0');
        $where['latitude'] = array('neq', '0');

        $Trail = D('Trail');
        $trailInfo = $Trail->where($where)->order('tr_id desc')->find();

        if (empty($trailInfo)) {
            $this->error('没有定位信息！');
        } else {
            //获取经纬度数据
            if ($trailInfo['type'] == 3) {//原始GPS坐标
                $point_info = array($trailInfo['longitude'], $trailInfo['latitude']);
                $point = get_gps_point($point_info, $trailInfo['type']);
                $trail['gps_type'] = "GPS定位";
            } elseif ($trailInfo['type'] == 1) {//基站信息处理
                $point_info = array(
                    'mcc' => $trailInfo['mcc'],
                    'mnc' => $trailInfo['mnc'],
                    'lac' => $trailInfo['lac'],
                    'cid' => $trailInfo['cid'],
                );
                $point = get_gps_point($point_info, $trailInfo['type']);
                $trail['gps_type'] = "基站定位";
                if ($point['type'] == 2) {
                    $this->error('获取基站位置信息失败');
                }
            } elseif ($trailInfo['type'] == 0) {//GPS坐标
                $point = array($trailInfo['longitude'], $trailInfo['latitude']);
                $trail['gps_type'] = "GPS定位";
            }

            $trailInfo['longitude'] = $point[0];
            $trailInfo['latitude'] = $point[1];

            //过滤不合法定位
            if (($trailInfo['longitude'] > $longitudeendtime)
                || ($trailInfo['longitude'] < $longitudestart)
                || ($trailInfo['latitude'] > $latitudeendtime)
                || ($trailInfo['latitude'] < $latitudestart)) {
                $this->error('GPS信号弱！');
            }

            $trail['longitude'] = $trailInfo['longitude'];
            $trail['latitude'] = $trailInfo['latitude'];

            $trail['time'] = $trailInfo['create_time'];
            $trail['imei'] = $stu_info['imei_id'];
            $trail['power'] = $trailInfo['power'];
            $signal = (int)$trailInfo['signal1'];
            if ($signal <= 12) {
                $signal = 20;
            } else if (13 <= $signal && $signal <= 18) {
                $signal = 40;
            } else if (19 <= $signal && $signal <= 23) {
                $signal = 60;
            } else if (24 <= $signal && $signal <= 28) {
                $signal = 80;
            } else if (29 <= $signal && $signal <= 31) {
                $signal = 100;
            } else {
                $signal = 100;
            }
            $operator = getMobileArea2($stu_info['stu_phone']);

            $trail['operator'] = $operator;

            $trail['signal1'] = $signal;
        }
        //下发实时定位命令
        $redis = redis_instance('39.107.98.114');
        $redis->sAdd('task:' . $stu_info['imei_id'], '[3G*' . $stu_info['imei_id'] . '*0002*CR]');
        $this->success($trail);
    }


    //学生围栏
    public function studentFence()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/Track/studentFence'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法！');
        }
        $stu_id = I('post.stu_id');
        if (empty($stu_id)) {
            $this->error('参数错误！');
        }
        $where['stu_id'] = array('in', $stu_id);
        $Safety = D('SafetyRegion');
        $safetyList = $Safety->where($where)->Field('sr_id,stu_id,name,point,create_time')->select();

        $this->success($safetyList);
    }

    //添加学生围栏
    public function addStudentFence()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/Track/studentFence'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法！');
        }
        $data['stu_id'] = I('post.stu_id');

        if (empty($data['stu_id'])) {
            $this->error('参数错误！');
        }

        $where['stu_id'] = $data['stu_id'];

        $Safety = D('SafetyRegion');
        $safetyList = $Safety->where($where)->select();
        $imei = M('Student')->where("stu_id = '{$data['stu_id']}'")->getField('imei_id');

        if (count($safetyList) >= 5) {
            $this->error('围栏信息最多添加5个');
        }
        //插入
        $data['s_id'] = I('post.s_id');
        $data['a_id'] = I('post.a_id');
        $data['g_id'] = I('post.g_id');
        $data['c_id'] = I('post.c_id');
        $data['point'] = I('post.point');
        $data['name'] = I('post.name');
        $data['create_time'] = date('Y-m-d H:i:s');
        $data['imei'] = $imei;

        if (empty($data['point']) || empty($data['name'])) {
            $this->error('参数错误！');
        }

        if (!$Safety->add($data)) {
            $this->error('添加失败');
        } else {
            //更新缓存
            $point = $Safety->where($where)->getField('point', true);
            S("SafeArea:$imei", $point);
            $this->success('添加成功');
        }
    }

    //删除学生围栏
    public function deleteStudentFence()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/Track/studentFence'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法！');
        }
        $sr_id = I('post.sr_id');
        if (empty($sr_id)) {
            $this->error('参数错误！');
        }

        $Safety = D('SafetyRegion');
        $where['sr_id'] = $sr_id;
        $info = $Safety->where("sr_id = '{$sr_id}'")->find();
        if(!$info['imei']){
            $info['imei'] = M('Student')->where("stu_id = '{$info['stu_id']}'")->getField('imei_id');
        }
        if(S("SafeArea:{$info['imei']}")){
            $res = S("SafeArea:{$info['imei']}", null);
        }else{
            $res = true;
        }

        if (!$res) {
            $this->error("稍后再试");
        } else {
            $ret = $Safety->where($where)->delete();
            if ($ret) {
                $this->success("删除成功");
            } else {
                $this->error("删除失败");
            }
        }
    }

    //报警记录
    public function alarmRecord()
    {

        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/Track/alarmRecord'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法！');
        }
        $dates = I('post.dates');
        $user_type = $this->getUserType();
        if ($user_type == 4) {
            # 家长
            $stu_id = I('post.stu_id');
            if (empty($stu_id)) {
                $this->error('参数错误！');
            }
            $where['stu_id'] = array('EQ', $stu_id);
            $where['create_time'] = array('like', '%' . $dates . '%');
        } else if ($user_type == 3) {
            # 老师
            $c_id = I('post.c_id');
            $where['c_id'] = $c_id;
            $where['create_time'] = array('between', array($dates . ' 00:00:00', $dates . ' 23:59:59'));
        }
        $where['is_push'] = '1';
        $Warning = D('WarningMessage');
        $warningList = $Warning->where($where)
            ->Field('wm_id,stu_id,longitude,latitude,war_status,war_type,create_time')
            ->order('war_status,create_time desc')->select();
        if (empty($warningList)) {
            $this->error('没有报警记录！');
        }
        $device_where['c_id'] = $c_id;
        //获取IMEI号
        $deviceInfo = D('DeviceManage')->where($device_where)->getField('stu_id,imei');

        $BAOJING_TYPE = C('BAOJING_TYPE');
        foreach ($warningList as $key => $value) {
            $studentInfo = $this->get_stu_name($value['stu_id'])[$value['stu_id']];
            $warningList[$key]['stu_name'] = $studentInfo["stu_name"];
            $warningList[$key]['war_type'] = $BAOJING_TYPE[$value['war_type']];
            $warningList[$key]['imei'] = $deviceInfo[$value['stu_id']];
        }

        $this->success($warningList);

    }

    //报警处理
    public function alarm_edit()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/Track/alarm_edit'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法！');
        }
        $wm_id = I('post.wm_id');
        if (empty($wm_id)) {
            $this->error('参数错误！');
        }
        $data['war_status'] = 1;
        $data['wm_id'] = $wm_id;
        $where['wm_id'] = array('EQ', $wm_id);
        $ret = D('WarningMessage')->where($where)->save($data);
        if ($ret === false) {
            $this->error('修改失败！');
        } else {
            if ($ret > 0) {
                $this->success('更新成功！');
            } else {
                $this->error('修改失败！');
            }
        }

    }

    //获取报警详情
    public function getDetail()
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
        $id = I('post.id');

        if (empty($id)) {
            $this->error('参数错误');
        }
        $info = M('WarningMessage')->field('wm_id,imei,war_type,longitude,latitude,war_status,create_time')->where(array('wm_id' => $id))->find();
        $info['war_type'] = C('BAOJING_TYPE')[$info['war_type']];
        $sex = M('Student')->field('sex')->where(array('imei_id' => $info['imei']))->find();
        $info['stu_name'] = M('Student')->where(array('imei_id' => $info['imei']))->getField('stu_name');
        $info['stu_sex'] = C('SEX_TYPE')[$sex['sex']];
        if (empty($info)) {
            $this->error('参数错误');
        }
        $this->success($info);
    }

    /*
     *警报查询新增分页功能,保证未传入page的接口可正常调用
     *
     *
     * */
    public function alarmRecord_v1_0_1()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法！');
        }
        $page = I('post.page', 1);
        $pagesize = $this->PAGESIZE;

        $dates = I('post.dates');
        if (empty($dates)) {
            $this->error('请选择日期！');
        }
        $user_type = $this->getUserType();
        if ($user_type == 3) {
            $where['create_time'] = array('between', array($dates . ' 00:00:00', $dates . ' 23:59:59'));
            # 老师
            $c_id = I('post.c_id');
            if ($c_id) {
                $imei = M('Student')
                    ->cache(true)
                    ->field('stu_name,imei_id,sex')
                    ->where("c_id = '{$c_id}' and imei_id > 0")
                    ->getField('imei_id', true);
                if ($imei) {
                    $where['imei'] = array('in', $imei);
                } else {
                    $this->error('该班级学生未绑定学生卡！');
                }
            }
        } else {
            $where['create_time'] = array('like', $dates . '%');
            # 家长
            $stu_id = I('post.stu_id');
            if (empty($stu_id)) {
                $this->error('参数错误！');
            }
            if ($stu_id) {
                $imei = M('Student')
                    ->cache(true)
                    ->where("stu_id = '{$stu_id}'")
                    ->getField('imei_id');
                if ($imei) {
                    $where['imei'] = array('EQ', $imei);
                } else {
                    $this->error('该学生未绑定学生卡！');
                }

            }
        }
        $where['is_push'] = 1;


        $Warning = D('WarningMessage');
        $warningList = $Warning->where($where)
            ->Field('wm_id,longitude,latitude,war_status,war_type,create_time,imei,mcc,mnc,lac,cid,type')
            ->order('war_status,create_time desc')->page($page, $pagesize)->select();


        if (empty($warningList)) {
            $this->error('没有报警记录！');
        }
        $count = M("WarningMessage")->where($where)->count('wm_id');
        $BAOJING_TYPE = C('BAOJING_TYPE');
        foreach ($warningList as $key => $value) {
            $stu_info = get_stu_info($value['imei']);

            $warningList[$key]['stu_id'] = $stu_info["stu_id"];
            $warningList[$key]['stu_name'] = $stu_info["stu_name"];
            $warningList[$key]['stu_sex'] = $stu_info["sex"];
            $warningList[$key]['war_type'] = $BAOJING_TYPE[$value['war_type']];
            //获取经纬度数据
            if ($value['type'] == 3) {
                $point_info = array($value['longitude'], $value['latitude']);
                $point = get_gps_point($point_info, $value['type']);
            } elseif ($value['type'] == 1) {//基站信息处理
                $point_info = array(
                    'mcc' => $value['mcc'],
                    'mnc' => $value['mnc'],
                    'lac' => $value['lac'],
                    'cid' => $value['cid'],
                );
                $point = get_gps_point($point_info, $value['type']);
            } else {
                $point = array($value['longitude'], $value['latitude']);
            }


            $warningList[$key]['longitude'] = $point[0];
            $warningList[$key]['latitude'] = $point[1];

        }

        $page_szie = ceil($count / $pagesize);
        $arr['total_page'] = $page_szie;
        $arr['total_count'] = $count;
        $arr['page'] = $page;
        $arr['content'] = $warningList;
        $this->success($arr);
    }


    //轨迹 绑路功能 0 不绑路  1 绑路
    public function historyTrack()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $stu_id = I("post.stu_id");
        $state = I('post.state', 0); // 绑路 状态 0 未绑路 1  绑路

        $imei = M('Student')->where("stu_id = '{$stu_id}'")->getField('imei_id');
        if (!$imei) {
            $this->error('未绑定imei号');
        } else {
            $where['imei'] = $imei;
        }
//        $where['type'] = array('in','0,3');
//        $where['type'] = 3;
        $starttime = I("post.sdate");//开始时间
        $endtime = I("post.edate");//结束时间deleteStudentFence

        if ($starttime && $endtime) {
            $where['create_time'] = array("BETWEEN", array($starttime, $endtime));
        }
        if ((strtotime($endtime) - strtotime($starttime)) > 86400) {
            $this->error("起始和结束时间必须在24小时之内");
        }
        $where['longitude'] = array('neq', '0');
        $Trail = M('Trail');
        $trailInfo = $Trail
            ->where($where)
            ->order('create_time asc')
            ->Field('*')
            ->select();
        if (!$trailInfo) {
            $this->error('此时间间隔内该学生没有足迹回放数据！');
        }
        if ($state == 1) {
            $res1 = D('Student')->where(array("stu_id" => $stu_id))->find();
            $res = TraceReplay($trailInfo, $state, $res1, $starttime, $endtime);
            $type = gettype($res);
            if ($type == 'array') {
                $this->success($res);
            } else {
                $this->error($res);
            }
        } else {
            foreach ($trailInfo as $key => $value) {
                //获取经纬度数据
                if ($value['type'] == 3) {//原始GPS坐标
                    $point_info = array($value['longitude'], $value['latitude']);
                    $point = get_gps_point($point_info, $value['type']);
                    $type = "GPS定位";
                } elseif ($value['type'] == 1) {//基站信息处理
                    $point_info = array(
                        'mcc' => $value['mcc'],
                        'mnc' => $value['mnc'],
                        'lac' => $value['lac'],
                        'cid' => $value['cid'],
                    );
                    $point = get_gps_point($point_info, $value['type']);
                    $type = "基站定位";
                    if ($point['type'] == 2) {
                        $this->error('获取基站位置信息失败');
                    }
                } elseif ($value['type'] == 0) {//GPS坐标
                    $point = array($value['longitude'], $value['latitude']);
                    $type = "GPS定位";
                }

                $trailInfo1[$key]['gps_type'] = $type;
                $trailInfo1[$key]['imei'] = $imei;
                $trailInfo1[$key]['latitude'] = $point[1];
                $trailInfo1[$key]['longitude'] = $point[0];
                $trailInfo1[$key]['create_time'] = $value['create_time'];
            }

            $this->success($trailInfo1);
        }
    }


}