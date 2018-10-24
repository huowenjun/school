<?php

namespace Api\Controller;

use Think\Controller;

class TestController extends Controller
{
    public function test()
    {
        ini_set('max_execution_time', '0');
        //获取is_select=0的记录
        $trailList = M('Trail')
            ->field('tr_id,longitude,latitude,type,imei')
            ->where("imei = '863075030574840' and  type = 3")
            ->limit(1000)
            ->select();

        $timeStr = date('YmdH');
        foreach ($trailList as $key => $value) {
            //判断定位类型 类型 0:GPS 1:LBS 2:未获取位置 3:wgs84
            if ($value['type'] != 2) {//合法的数据
                if ($value['type'] == 3) {//wgs84数据
                    //wgs84转gps
                    $point = $this->origin2gps($value['longitude'], $value['latitude']);

                    //获取GPS经纬度
                    $value['longitude'] = $point[0];
                    $value['latitude'] = $point[1];
                }

                //调用围栏考勤算法
                $res = $this->AttendanceArith($value);

            }

        }
    }

    /**
     *考勤信息算法 By:Meng
     * @param $gpsInfo array gps坐标信息
     * */
    private function AttendanceArith($gpsInfo = array())
    {
        if (!$gpsInfo['imei'] || !$gpsInfo['longitude'] || !$gpsInfo['latitude']) {
            return false;
        }
        $gpsInfo['a_id'] = M('Student')->cache(true)->where("imei_id = '{$gpsInfo['imei']}'")->getField('a_id');
        $is_insert = 0;
        if ($gpsInfo['a_id']) {
            //获取家长围栏信息
            if (!S('SafeArea:' . $gpsInfo['imei'])) {
                $SafeTmp = M('SafetyRegion')->where("imei = '{$gpsInfo['imei']}'")->getField('point', true);
                S('SafeArea:' . $gpsInfo['imei'], $SafeTmp);
            }
            //获取学校围栏信息
            if (!S('aid:' . $gpsInfo['a_id'])) {
                $mapTmp = M('GpsAttendanceSet')->where("a_id = '{$gpsInfo['a_id']}'")->getField('point');
                S('aid:' . $gpsInfo['a_id'], $mapTmp);
            }
            $mapStr = S('aid:' . $gpsInfo['a_id']);//考勤围栏
            $SafeArr = S('SafeArea:' . $gpsInfo['imei']);//安全围栏

            //$mapStr = '116.291226,39.841079,116.292344,39.841165,116.292443,39.840625,116.291302,39.840476|116.293108,39.840653,116.293691,39.840715,116.293817,39.840431,116.293265,39.840358|116.293161,39.840822,116.2941,39.840937,116.294253,39.840068,116.292739,39.840171';
            if (!$mapStr && !$SafeArr) {
                return false;
            }
            $mapArr = explode('|', $mapStr);
            if (!$mapArr[2] && $SafeArr) {
                return false;
            }


            //已知GPS坐标点 s_id
            $point = $gpsInfo['longitude'] . ',' . $gpsInfo['latitude'];
            //安全围栏
            if ($SafeArr) {//设置了安全围栏
                $safeRes = array();
                foreach ($SafeArr as $ky => $ve) {
                    $tmp = is_inside($point, $SafeArr);
                    $safeRes[] = $tmp;
                    if ($tmp) {
                        break;
                    }
                }
                $res = in_array(1, $safeRes);
                if (!$res) {//没在安全区域
                    $wm_data['tr_id'] = $gpsInfo['tr_id'];
                    $wm_data['war_type'] = 4;
                    $wm_data['imei'] = $gpsInfo['imei'];
                    $wm_data['longitude'] = $gpsInfo['longitude'];
                    $wm_data['latitude'] = $gpsInfo['latitude'];
                    $wm_data['type'] = $gpsInfo['type'];

                    M('WarningMessage')->add($wm_data);
                }
            }

            //考勤围栏
            if ($mapStr) {
                //学生信息
                $date = date('Y-m-d');
                //是否签到
                $whereStr['create_time'] = array('between', array($date . " 00:00:00", $date . " 23:59:59"));
                $whereStr['imei'] = $gpsInfo['imei'];
                //签到详情 1进 0出
                $SignInInfo = M('GpsAttendance1')->where($whereStr)->getField('sign_type', true);
                //上一次签到状态
                $prev = end($SignInInfo);
                //加入lbs和gps算法
                if ($gpsInfo['type'] == 0) {//lbs算法部分
                    //是否在围栏里
                    $isInsideC = is_inside($point, $mapArr[2]);
                    //当天无签到行为
                    if (!$SignInInfo) {
                        //在A围栏里 add
                        if ($isInsideC == 1) {
                            $data['sign_type'] = 1;
                        }
                    } else {//当天有签到行为
                        //上次签到为签到 并且 在圈外 签退add
                        if ($prev == 1 && ($isInsideC == 2)) {
                            $data['sign_type'] = 0;
                        } elseif ($prev == 0) {
                            $isInsideA = $isInsideC;
                            //上次签到为签退 并且 在圈内 签到add
                            if ($isInsideA == 1) {
                                $data['sign_type'] = 1;
                            }
                        }
                    }
                } else {//gps算法部分
                    //是否在围栏里
                    $isInsideA = is_inside($point, $mapArr[1]);

                    //当天无签到行为
                    if (!$SignInInfo) {
                        //在A围栏里 add
                        if ($isInsideA == 1) {
                            $is_insert = 1;
                            $data['sign_type'] = 1;
                        }
                    } else {//当天有签到行为
                        //上次签到为签到 并且 在圈外 签退add
                        if ($prev == 1) {
                            $isInsideB = $isInsideA;
                            if ($isInsideB == 2) {
                                $is_insert = 1;
                                $data['sign_type'] = 0;
                            }
                        } elseif ($prev == 0 && ($isInsideA == 1)) {
                            //上次签到为签退 并且 在圈内 签到add
                            $is_insert = 1;
                            $data['sign_type'] = 1;
                        }
                    }
                }

                //数据写入部分
                if ($is_insert) {
                    $data['imei'] = $gpsInfo['imei'];
                    $data['type'] = $gpsInfo['type'];//0:lbs判定的考勤 3:GPS判定的考勤
                    $data['create_time'] = date('Y-m-d H:i:s');
                    $data['longitude'] = $gpsInfo['longitude'];
                    $data['latitude'] = $gpsInfo['latitude'];
                    $data['tr_id'] = $gpsInfo['tr_id'];

                    M('GpsAttendance1')->add($data);
                }
            }

        } else {
            return false;
        }

    }

    //原始坐标转gps算法
    private function origin2gps($lng, $lat)
    {
        if (!$lng || !$lat) {
            return false;
        }
        $lngD = substr($lng, 0, 3);
        $latD = substr($lat, 0, 2);
        $lngF = substr($lng, 3, 10) * 1000000 / 60 / 1000000;
        $latF = substr($lat, 2, 10) * 1000000 / 60 / 1000000;
        $arr = array(
            $lngD + $lngF,
            $latD + $latF,
        );
        return ($arr);
    }

    public function test1()
    {
        echo S('test');
    }
}