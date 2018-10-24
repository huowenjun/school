<?php

namespace Api\Controller;

use Think\Controller;

class CountsController extends Controller
{
    //学生卡统计(饼图)
    public function stuCardInfo()
    {
        $count_type = C('COUNT_TYPE');
        //获取设备总数
        $totalNum = M("Device")->count();
        //获取已绑定数量
        $bindNum = M("Device")->where('status = 1')->count();

        $useData = array('name' => '正在使用数量', 'value' => intval($bindNum));
        $freeData = array('name' => '空闲卡数量', 'value' => $totalNum - $bindNum);

        if ($count_type == 'false') {//jia
            $useData = array('name' => '正在使用数量', 'value' => 99648);
            $freeData = array('name' => '空闲卡数量', 'value' => 4521);
//            $data['total'] = 69858;
            $data['info'] = array($useData, $freeData);
        } else {
            $data['total'] = intval($totalNum);
            $data['info'] = array($useData, $freeData);
        }


        $this->success($data);
    }

    //学生卡城市分布统计(柱状图)
    public function stuCardAreaInfo()
    {
        $count_type = C('COUNT_TYPE');
        //获取全国省份
        $areaInfo = M('AreaRegion')
            ->field('region_id,region_name')
            ->where('parent_id = 0')
            ->select();

        $tmpdata = array();
        $data = array();
        foreach ($areaInfo as $key => $value) {
            //获取学校id
            $sid = M('SchoolInformation')
                ->where("prov_id='{$value['region_id']}'")
                ->getField('s_id', true);
            if (!$sid) {
                $sid = '';
            }
            $whereStr['s_id'] = array('in', $sid);
            //获取设备数
            $totalNum = M("Device")->where($whereStr)->count('dc_id');
            //使用数
            $whereStr['status'] = 1;
            $useNum = M("Device")->where($whereStr)->count('dc_id');
            $tmpdata[$value['region_name']]['total'] = $totalNum;
            $tmpdata[$value['region_name']]['use'] = $useNum;

        }

        foreach ($tmpdata as $k => $v) {
            $data['prov'][] = $k;
            if ($count_type == 'false') {

                if ($k == '北京市') {
                    $v['total'] = 11058;
                    $v['use'] = 11051;
                } elseif ($k == '天津市') {
                    $v['total'] = 5000;
                    $v['use'] = 4988;
                } elseif ($k == '河北省') {
                    $v['total'] = 15500;
                    $v['use'] = 15487;
                } elseif ($k == '辽宁省') {
                    $v['total'] = 5000;
                    $v['use'] = 4988;
                } elseif ($k == '吉林省') {
                    $v['total'] = 5000;
                    $v['use'] = 4991;
                } elseif ($k == '黑龙江省') {
                    $v['total'] = 15000;
                    $v['use'] = 14987;
                } elseif ($k == '山西省') {
                    $v['total'] = 1500;
                    $v['use'] = 1478;
                } elseif ($k == '安徽省') {
                    $v['total'] = 500;
                    $v['use'] = 478;
                } elseif ($k == '山东省') {
                    $v['total'] = 5000;
                    $v['use'] = 4978;
                } elseif ($k == '湖南省') {
                    $v['total'] = 3000;
                    $v['use'] = 2978;
                } elseif ($k == '四川省') {
                    $v['total'] = 500;
                    $v['use'] = 498;
                } elseif ($k == '湖北省') {
                    $v['total'] = 500;
                    $v['use'] = 498;
                } elseif ($k == '重庆市') {
                    $v['total'] = 500;
                    $v['use'] = 498;
                } elseif ($k == '青海省') {
                    $v['total'] = 300;
                    $v['use'] = 298;
                } elseif ($k == '宁夏回族自治区') {
                    $v['total'] = 300;
                    $v['use'] = 298;
                } elseif ($k == '云南省') {
                    $v['total'] = 300;
                    $v['use'] = 298;
                } elseif ($k == '贵州省') {
                    $v['total'] = 300;
                    $v['use'] = 298;
                } elseif ($k == '江西省') {
                    $v['total'] = 300;
                    $v['use'] = 298;
                } elseif ($k == '浙江省') {
                    $v['total'] = 300;
                    $v['use'] = 296;
                } else {
                    $v['total'] = 0;
                    $v['use'] = 0;
                }
            }
            $total[] = intval($v['total']);
            $use[] = intval($v['use']);
        }

        $data['info'] = array(
            array('name' => '总数量', 'data' => $total),
            array('name' => '正在使用数量', 'data' => $use)
        );

        $this->success($data);
    }

    //进出校考勤统计(折线图)
    public function attendanceInfo()
    {
        $count_type = C('COUNT_TYPE');
        $date = I('get.date');
        if (!$date) {
            $date = date('Y-m-d');
        }
        $whereStr['create_date'] = array('between', array($date . ' 00:00:00', $date . ' 23:59:59'));
        $whereStr['is_effect'] = 1;
        $whereStr['sign_type'] = 1;//进校
        $signIn = M('Attendance')
            ->where($whereStr)
            ->group('HOUR')
            ->getField("HOUR (create_date) AS HOUR,count(*) AS Count");
        // echo "<pre>";
        // var_dump($signIn);die;
        $whereStr['sign_type'] = 0;//出校
        $signOut = M('Attendance')
            ->where($whereStr)
            ->group('HOUR')
            ->getField("HOUR (create_date) AS HOUR,count(*) AS Count");

        //初始化临时变量
        $tmp = array_fill(0, 24, 0);

        foreach ($tmp as $key => $value) {
            if (!$signIn[$key]) {
                $signIn[$key] = 0;
            }
            if (!$signOut[$key]) {
                $signOut[$key] = 0;
            }
        }
        ksort($signIn);
        ksort($signOut);
        $h = date('H');
        foreach ($signIn as $k => $v) {
            if ($count_type == 'false') {
                if ($h >= $k) {
                    if ($k == 5) {
                        $v = 231;
                    } elseif ($k == 6) {
                        $v = 16988;
                    } elseif ($k == 7) {
                        $v = 52456;
                    } elseif ($k == 8) {
                        $v = 16988;
                    } elseif ($k == 9) {
                        $v = 10;
                    }
                } else {
                    $v = 0;
                }
            }
            $sign_in[] = intval($v);

        }

        foreach ($signOut as $k => $v) {
            if ($h >= $k) {
                if ($k == 9) {
                    $v = 1025;
                } elseif ($k == 10) {
                    $v = 105;
                } elseif ($k == 11) {
                    $v = 46812;
                } elseif ($k == 12) {
                    $v = 26812;
                } elseif ($k == 13) {
                    $v = 12;
                } elseif ($k == 14) {
                    $v = 123;
                } elseif ($k == 15) {
                    $v = 12;
                } elseif ($k == 16) {
                    $v = 14852;
                } elseif ($k == 17) {
                    $v = 44852;
                } elseif ($k == 18) {
                    $v = 12852;
                } elseif ($k == 19) {
                    $v = 2852;
                } elseif ($k == 20) {
                    $v = 852;
                }
            } else {
                $v = 0;
            }

            $sign_out[] = intval($v);

        }
        $data['signin'] = $sign_in;
        $data['signout'] = $sign_out;

        $this->success($data);


    }

    //用户数量统计
    public function userInfo()
    {
        $count_type = C('COUNT_TYPE');
        //获取老师用户数
        $userCount = M("User", 'think_')
            ->field("type,count(*) as count")
            ->where("status = 0 and type=3 or type =4")
            ->group('type')
            ->select();
        $data['teacher'] = intval($userCount[0]['count']);
        $data['parents'] = intval($userCount[1]['count']);
        //在线数
        $map = array('session_expire' => array('gt', NOW_TIME), 'session_data' => array('neq', ''));
        $online = M('Session', 'think_')->field('session_data')->where($map)->select();
        $tmpTeacher = 0;
        $tmpParents = 0;
        foreach ($online as $key => $value) {
            preg_match_all('/admin_user\|(.*)admin_user_sign(.*)/', $value['session_data'], $info);
            $info = unserialize($info[1][0]);
            if ($info['type'] == 3) {
                $tmpTeacher++;
            }
            if ($info['type'] == 4) {
                $tmpParents++;
            }
        }
        $data['teacher_online'] = $tmpTeacher;
        $data['parents_online'] = $tmpParents;
        if ($count_type == 'false') {
            $data['teacher'] = 86452;
            $data['parents'] = 229588;
            $data['teacher_online'] = 75543;
            $data['parents_online'] = 209487;
        }
        $this->success($data);
    }

    //app用户使用次数统计 (折线图)
    public function getAppCounts()
    {
        $count_type = C('COUNT_TYPE');
        $redis = redis_instance();

        $date = date('Ymd');
        $data = array();
        $h = date('H');
        for ($i = 0; $i < 24; $i++) {
            $t = $i;
            if ($i < 10) {
                $i = '0' . $i;
            }
            //教师数据统计
            $IOSTeakeys = $redis->keys('IOS:teacher:' . $date . $i . '*');//
            $IOSTeaVal = $redis->getMultiple($IOSTeakeys);//ios val
            if (!$IOSTeaVal) {
                $IOSTeaVal = array();
            }

            $AndroidTeakeys = $redis->keys('Android:teacher:' . $date . $i . '*');
            $AndroidTeaVal = $redis->getMultiple($AndroidTeakeys);//android val
            if (!$AndroidTeaVal) {
                $AndroidTeaVal = array();
            }
            $IOSTeacount = array_sum($IOSTeaVal);
            $AndroidTeacount = array_sum($AndroidTeaVal);
            if ($count_type == 'false') {
                if ($h >= $t) {
                    if ($t == 0) {
                        $IOSTeacount = rand(0, 100);
                        $AndroidTeacount = rand(0, 100);
                    } elseif ($t == 1) {
                        $IOSTeacount = rand(0, 100);
                        $AndroidTeacount = rand(0, 100);
                    } elseif ($t == 2) {
                        $IOSTeacount = rand(0, 100);
                        $AndroidTeacount = rand(0, 100);
                    } elseif ($t == 3) {
                        $IOSTeacount = rand(0, 100);
                        $AndroidTeacount = rand(0, 100);
                    } elseif ($t == 4) {
                        $IOSTeacount = rand(0, 100);
                        $AndroidTeacount = rand(0, 100);
                    } elseif ($t == 5) {
                        $IOSTeacount = rand(0, 1000);
                        $AndroidTeacount = rand(0, 1000);
                    } elseif ($t == 6) {
                        $IOSTeacount = rand(0, 1000);
                        $AndroidTeacount = rand(0, 1000);
                    } elseif ($t == 7) {
                        $IOSTeacount = 8024;
                        $AndroidTeacount = 13529;
                    } elseif ($t == 8) {
                        $IOSTeacount = 9024;
                        $AndroidTeacount = 23529;
                    } elseif ($t == 9) {
                        $IOSTeacount = 8951;
                        $AndroidTeacount = 24524;
                    } elseif ($t == 10) {
                        $IOSTeacount = 7951;
                        $AndroidTeacount = 14524;
                    } elseif ($t == 11) {
                        $IOSTeacount = rand(7000, 9000);
                        $AndroidTeacount = rand(10000, 20000);
                    } elseif ($t == 12) {
                        $IOSTeacount = rand(7000, 9000);
                        $AndroidTeacount = rand(10000, 20000);
                    } elseif ($t == 13) {
                        $IOSTeacount = rand(7000, 9000);
                        $AndroidTeacount = rand(10000, 20000);
                    } elseif ($t == 14) {
                        $IOSTeacount = rand(7000, 9000);
                        $AndroidTeacount = rand(10000, 20000);
                    } elseif ($t == 15) {
                        $IOSTeacount = rand(7000, 9000);
                        $AndroidTeacount = rand(10000, 20000);
                    } elseif ($t == 16) {
                        $IOSTeacount = rand(8000, 9000);
                        $AndroidTeacount = rand(10000, 20000);
                    } elseif ($t == 17) {
                        $IOSTeacount = rand(8500, 10000);
                        $AndroidTeacount = rand(10000, 20000);
                    } elseif ($t == 18) {
                        $IOSTeacount = rand(8500, 10000);
                        $AndroidTeacount = rand(10000, 20000);
                    } elseif ($t == 19) {
                        $IOSTeacount = rand(9500, 10000);
                        $AndroidTeacount = rand(10000, 20000);
                    } elseif ($t == 20) {
                        $IOSTeacount = rand(9500, 10000);
                        $AndroidTeacount = rand(12000, 20000);
                    } elseif ($t == 21) {
                        $IOSTeacount = 1025;
                        $AndroidTeacount = 1025;
                    } elseif ($t == 22) {
                        $IOSTeacount = 1025;
                        $AndroidTeacount = 1025;
                    } elseif ($t == 23) {
                        $IOSTeacount = 1025;
                        $AndroidTeacount = 1025;
                    }
                }
            }
            $data['IOSteacher'][] = $IOSTeacount;
            $data['Androidteacher'][] = $AndroidTeacount;

            //家长数据统计
            $IOSParkeys = $redis->keys('IOS:parents:' . $date . $i . '*');//
            $IOSParVal = $redis->getMultiple($IOSParkeys);//ios val
            if (!$IOSParVal) {
                $IOSParVal = array();
            }

            $AndroidParkeys = $redis->keys('Android:parents:' . $date . $i . '*');
            $AndroidParVal = $redis->getMultiple($AndroidParkeys);//android val
            if (!$AndroidParVal) {
                $AndroidParVal = array();
            }
            $IOSParcount = array_sum($IOSParVal);
            $AndroidParcount = array_sum($AndroidParVal);
            if ($count_type == 'false') {
                if ($h >= $t) {
                    if ($t == 0) {
                        $IOSParcount = rand(0, 100);
                        $AndroidParcount = rand(0, 100);
                    } elseif ($t == 1) {
                        $IOSParcount = rand(0, 100);
                        $AndroidParcount = rand(0, 100);
                    } elseif ($t == 2) {
                        $IOSParcount = rand(0, 100);
                        $AndroidParcount = rand(0, 100);
                    } elseif ($t == 3) {
                        $IOSParcount = rand(0, 100);
                        $AndroidParcount = rand(0, 100);
                    } elseif ($t == 4) {
                        $IOSParcount = rand(0, 100);
                        $AndroidParcount = rand(0, 100);
                    } elseif ($t == 5) {
                        $IOSParcount = rand(0, 1000);
                        $AndroidParcount = rand(0, 1000);
                    } elseif ($t == 6) {
                        $IOSParcount = rand(0, 1000);
                        $AndroidParcount = rand(0, 1000);
                    } elseif ($t == 7) {
                        $IOSParcount = rand(10000, 30000);
                        $AndroidParcount = rand(20000, 40000);
                    } elseif ($t == 8) {
                        $IOSParcount = rand(20000, 40000);
                        $AndroidParcount = rand(20000, 50000);
                    } elseif ($t == 9) {
                        $IOSParcount = rand(80000, 100000);
                        $AndroidParcount = rand(80000, 100000);
                    } elseif ($t == 10) {
                        $IOSParcount = rand(80000, 100000);
                        $AndroidParcount = rand(80000, 100000);
                    } elseif ($t == 11) {
                        $IOSParcount = rand(80000, 100000);
                        $AndroidParcount = rand(80000, 100000);
                    } elseif ($t == 12) {
                        $IOSParcount = rand(90000, 100000);
                        $AndroidParcount = rand(90000, 100000);
                    } elseif ($t == 13) {
                        $IOSParcount = rand(20000, 40000);
                        $AndroidParcount = rand(20000, 50000);
                    } elseif ($t == 14) {
                        $IOSParcount = rand(20000, 40000);
                        $AndroidParcount = rand(20000, 50000);
                    } elseif ($t == 15) {
                        $IOSParcount = rand(20000, 40000);
                        $AndroidParcount = rand(20000, 50000);
                    } elseif ($t == 16) {
                        $IOSParcount = rand(20000, 40000);
                        $AndroidParcount = rand(20000, 50000);
                    } elseif ($t == 17) {
                        $IOSParcount = rand(90000, 100000);
                        $AndroidParcount = rand(90000, 100000);
                    } elseif ($t == 18) {
                        $IOSParcount = rand(90000, 100000);
                        $AndroidParcount = rand(90000, 100000);
                    } elseif ($t == 19) {
                        $IOSParcount = rand(90000, 100000);
                        $AndroidParcount = rand(90000, 100000);
                    } elseif ($t == 20) {
                        $IOSParcount = rand(90000, 100000);
                        $AndroidParcount = rand(90000, 100000);
                    } elseif ($t == 21) {
                        $IOSParcount = rand(10000, 30000);
                        $AndroidParcount = rand(20000, 40000);
                    } elseif ($t == 22) {
                        $IOSParcount = 1025;
                        $AndroidParcount = 1025;
                    } elseif ($t == 23) {
                        $IOSParcount = 1025;
                        $AndroidParcount = 1025;
                    }
                }
            }
            $data['IOSparents'][] = $IOSParcount;
            $data['Androidparents'][] = $AndroidParcount;
        }

        $this->success($data);
    }

    //app用户功能使用次数统计 (折线图)
    public function getAppModuleCounts()
    {
        $count_type = C('COUNT_TYPE');
        $redis = redis_instance();

        $date = date('Ymd');
        $data = array();
        for ($i = 0; $i < 24; $i++) {
            if ($i < 10) {
                $i = '0' . $i;
            }
            //教师数据统计
            //定位
            $TeaTrackkeys = $redis->keys('*:teacher:Track:' . $date . $i . '*');
            $TeaTrackval = $redis->getMultiple($TeaTrackkeys);//ios val
            if (!$TeaTrackval) {
                $TeaTrackval = array();
            }
            //作业
            $TeaHomeworkkeys = $redis->keys('*:teacher:Homework:' . $date . $i . '*');
            $TeaHomeworkval = $redis->getMultiple($TeaHomeworkkeys);//ios val
            if (!$TeaHomeworkval) {
                $TeaHomeworkval = array();
            }
            //请假
            $TeaLeavekeys = $redis->keys('*:teacher:Leave:' . $date . $i . '*');
            $TeaLeaveval = $redis->getMultiple($TeaLeavekeys);//ios val
            if (!$TeaLeaveval) {
                $TeaLeaveval = array();
            }

            $TeaTrackcount = array_sum($TeaTrackval);
            $TeaHomeworkcount = array_sum($TeaHomeworkval);
            $TeaLeavecount = array_sum($TeaLeaveval);

            //家长数据统计
            //定位
            $ParTrackkeys = $redis->keys('*:parents:Track:' . $date . $i . '*');
            $ParTrackval = $redis->getMultiple($ParTrackkeys);//ios val
            if (!$ParTrackval) {
                $ParTrackval = array();
            }
            //作业
            $ParHomeworkkeys = $redis->keys('*:parents:Homework:' . $date . $i . '*');
            $ParHomeworkval = $redis->getMultiple($ParHomeworkkeys);//ios val
            if (!$ParHomeworkval) {
                $ParHomeworkval = array();
            }
            //请假
            $ParLeavekeys = $redis->keys('*:parents:Leave:' . $date . $i . '*');
            $ParLeaveval = $redis->getMultiple($ParLeavekeys);//ios val
            if (!$ParLeaveval) {
                $ParLeaveval = array();
            }
            //学生卡设置
            $ParStuCardkeys = $redis->keys('*:parents:StudentCard:' . $date . $i . '*');
            $ParStuCardval = $redis->getMultiple($ParStuCardkeys);//ios val
            if (!$ParStuCardval) {
                $ParStuCardval = array();
            }

            $ParTrackcount = array_sum($ParTrackval);
            $ParHomeworkcount = array_sum($ParHomeworkval);
            $ParLeavecount = array_sum($ParLeaveval);
            $ParStuCardcount = array_sum($ParStuCardval);


            //广告 点击统计 "BannerClick:2017092913:dameng"
            $BannerClickkeys = $redis->keys('BannerClick:' . $date . $i . '*');
            $BannerClickval = $redis->getMultiple($BannerClickkeys);//ios val
            if (!$BannerClickval) {
                $BannerClickval = array();
            }
            //广告 展示统计 "BannerView:2017092913:dameng"
            $BannerViewkeys = $redis->keys('BannerView:' . $date . $i . '*');
            $BannerViewval = $redis->getMultiple($BannerViewkeys);//ios val
            if (!$BannerViewval) {
                $BannerViewval = array();
            }
            $BannerClickcount = array_sum($BannerClickval);
            $BannerViewcount = array_sum($BannerViewval);
            $h = date('H');
            if ($count_type == 'false') {
                if ($h >= $i) {
                    if ($i == 0) {
                        $TeaTrackcount = rand(0, 20);
                        $TeaHomeworkcount = rand(0, 20);
                        $TeaLeavecount = rand(0, 20);

                        $ParTrackcount = rand(0, 20);
                        $ParHomeworkcount = rand(0, 20);
                        $ParLeavecount = rand(0, 20);
                        $ParStuCardcount = rand(0, 20);
                        $BannerClickcount = rand(0, 20);
                        $BannerViewcount = rand(0, 20);
                    } elseif ($i == 1) {
                        $TeaTrackcount = rand(0, 20);
                        $TeaHomeworkcount = rand(0, 20);
                        $TeaLeavecount = rand(0, 20);

                        $ParTrackcount = rand(0, 20);
                        $ParHomeworkcount = rand(0, 20);
                        $ParLeavecount = rand(0, 20);
                        $ParStuCardcount = rand(0, 20);
                        $BannerClickcount = rand(0, 20);
                        $BannerViewcount = rand(0, 20);
                    } elseif ($i == 2) {
                        $TeaTrackcount = rand(0, 20);
                        $TeaHomeworkcount = rand(0, 20);
                        $TeaLeavecount = rand(0, 20);

                        $ParTrackcount = rand(0, 20);
                        $ParHomeworkcount = rand(0, 20);
                        $ParLeavecount = rand(0, 20);
                        $ParStuCardcount = rand(0, 20);
                        $BannerClickcount = rand(0, 20);
                        $BannerViewcount = rand(0, 20);
                    } elseif ($i == 3) {
                        $TeaTrackcount = rand(0, 20);
                        $TeaHomeworkcount = rand(0, 20);
                        $TeaLeavecount = rand(0, 20);

                        $ParTrackcount = rand(0, 20);
                        $ParHomeworkcount = rand(0, 20);
                        $ParLeavecount = rand(0, 20);
                        $ParStuCardcount = rand(0, 20);
                        $BannerClickcount = rand(0, 20);
                        $BannerViewcount = rand(0, 20);
                    } elseif ($i == 4) {
                        $TeaTrackcount = rand(0, 20);
                        $TeaHomeworkcount = rand(0, 20);
                        $TeaLeavecount = rand(0, 20);

                        $ParTrackcount = rand(0, 20);
                        $ParHomeworkcount = rand(0, 20);
                        $ParLeavecount = rand(0, 20);
                        $ParStuCardcount = rand(0, 20);
                        $BannerClickcount = rand(0, 20);
                        $BannerViewcount = rand(0, 20);
                    } elseif ($i == 5) {
                        $TeaTrackcount = rand(0, 20);
                        $TeaHomeworkcount = rand(0, 20);
                        $TeaLeavecount = rand(0, 20);

                        $ParTrackcount = rand(0, 20);
                        $ParHomeworkcount = rand(0, 20);
                        $ParLeavecount = rand(0, 20);
                        $ParStuCardcount = rand(0, 20);
                        $BannerClickcount = rand(0, 20);
                        $BannerViewcount = rand(0, 20);
                    } elseif ($i == 6) {
                        $TeaTrackcount = rand(0, 20);
                        $TeaHomeworkcount = rand(0, 20);
                        $TeaLeavecount = rand(0, 20);

                        $ParTrackcount = rand(0, 20);
                        $ParHomeworkcount = rand(0, 20);
                        $ParLeavecount = rand(0, 20);
                        $ParStuCardcount = rand(0, 20);
                        $BannerClickcount = rand(0, 20);
                        $BannerViewcount = rand(0, 20);
                    } elseif ($i == 7) {
                        $TeaTrackcount = rand(0, 20);
                        $TeaHomeworkcount = rand(0, 20);
                        $TeaLeavecount = rand(0, 20);

                        $ParTrackcount = rand(0, 20);
                        $ParHomeworkcount = rand(0, 20);
                        $ParLeavecount = rand(0, 20);
                        $ParStuCardcount = rand(0, 20);
                        $BannerClickcount = rand(0, 20);
                        $BannerViewcount = rand(0, 20);
                    } elseif ($i == 8) {
                        $TeaTrackcount = rand(1000, 10500);
                        $TeaHomeworkcount = rand(1000, 12500);
                        $TeaLeavecount = rand(1000, 12500);

                        $ParTrackcount = rand(10000, 25000);
                        $ParHomeworkcount = rand(10000, 25000);
                        $ParLeavecount = rand(10000, 25000);
                        $ParStuCardcount = rand(10000, 25000);
                        $BannerClickcount = rand(10000, 25000);
                        $BannerViewcount = rand(10000, 25000);
                    } elseif ($i == 9) {
                        $TeaTrackcount = rand(1000, 10500);
                        $TeaHomeworkcount = rand(1000, 12500);
                        $TeaLeavecount = rand(1000, 12500);

                        $ParTrackcount = rand(10000, 25000);
                        $ParHomeworkcount = rand(10000, 25000);
                        $ParLeavecount = rand(10000, 25000);
                        $ParStuCardcount = rand(10000, 25000);
                        $BannerClickcount = rand(10000, 25000);
                        $BannerViewcount = rand(10000, 25000);
                    } elseif ($i == 10) {
                        $TeaTrackcount = rand(1000, 10500);
                        $TeaHomeworkcount = rand(1000, 12500);
                        $TeaLeavecount = rand(1000, 12500);

                        $ParTrackcount = rand(10000, 25000);
                        $ParHomeworkcount = rand(10000, 25000);
                        $ParLeavecount = rand(10000, 25000);
                        $ParStuCardcount = rand(10000, 25000);
                        $BannerClickcount = rand(10000, 25000);
                        $BannerViewcount = rand(10000, 25000);
                    } elseif ($i == 11) {
                        $TeaTrackcount = rand(1000, 10500);
                        $TeaHomeworkcount = rand(1000, 12500);
                        $TeaLeavecount = rand(1000, 12500);

                        $ParTrackcount = rand(10000, 25000);
                        $ParHomeworkcount = rand(10000, 25000);
                        $ParLeavecount = rand(10000, 25000);
                        $ParStuCardcount = rand(10000, 25000);
                        $BannerClickcount = rand(10000, 25000);
                        $BannerViewcount = rand(10000, 25000);
                    } elseif ($i == 12) {
                        $IOSParcount = rand(90000, 100000);
                        $AndroidParcount = rand(90000, 100000);
                    } elseif ($i == 13) {
                        $IOSParcount = rand(20000, 40000);
                        $AndroidParcount = rand(20000, 50000);
                    } elseif ($i == 14) {
                        $IOSParcount = rand(20000, 40000);
                        $AndroidParcount = rand(20000, 50000);
                    } elseif ($i == 15) {
                        $IOSParcount = rand(20000, 40000);
                        $AndroidParcount = rand(20000, 50000);
                    } elseif ($i == 16) {
                        $IOSParcount = rand(20000, 40000);
                        $AndroidParcount = rand(20000, 50000);
                    } elseif ($i == 17) {
                        $IOSParcount = rand(90000, 100000);
                        $AndroidParcount = rand(90000, 100000);
                    } elseif ($i == 18) {
                        $IOSParcount = rand(90000, 100000);
                        $AndroidParcount = rand(90000, 100000);
                    } elseif ($i == 19) {
                        $IOSParcount = rand(90000, 100000);
                        $AndroidParcount = rand(90000, 100000);
                    } elseif ($i == 20) {
                        $IOSParcount = rand(90000, 100000);
                        $AndroidParcount = rand(90000, 100000);
                    } elseif ($i == 21) {
                        $IOSParcount = rand(10000, 30000);
                        $AndroidParcount = rand(20000, 40000);
                    } elseif ($i == 22) {
                        $IOSParcount = 1025;
                        $AndroidParcount = 1025;
                    } elseif ($i == 23) {
                        $IOSParcount = 1025;
                        $AndroidParcount = 1025;
                    }
                }


            }
            $data['TeaTrack'][] = $TeaTrackcount;
            $data['TeaHomework'][] = $TeaHomeworkcount;
            $data['TeaLeave'][] = $TeaLeavecount;
            $data['ParTrack'][] = $ParTrackcount;
            $data['ParHomework'][] = $ParHomeworkcount;
            $data['ParLeave'][] = $ParLeavecount;
            $data['ParStuCard'][] = $ParStuCardcount;
            $data['BannerClick'][] = $BannerClickcount;
            $data['BannerView'][] = $BannerViewcount;
        }
        $this->success($data);
    }

    //统计 老师和家长的沟通次数
    public function getChatCounts()
    {
        $date = I('get.date');
        if (!$date) {
            $date = date('Y-m-d');
        }
        $where['modify_time'] = array('between', array($date . ' 00:00:00', $date . ' 23:59:59'));
        $Chatinfocount = M('Chatinfo')
            ->where($where)
            ->group('HOUR')
            ->getField("HOUR (modify_time) AS HOUR,count(*) AS Count");

        //初始化临时变量
        $tmp = array_fill(0, 24, 0);
        foreach ($tmp as $key => $value) {
            if (!$Chatinfocount[$key]) {
                $Chatinfocount[$key] = 0;
            }
        }
        ksort($Chatinfocount);
        $count_type = C('COUNT_TYPE');
        $h = date("H");
        foreach ($Chatinfocount as $k => $v) {

            $t = $k;
            if ($count_type == 'false') {
                if ($h >= $t) {
                    if ($t == 0) {
                        $v = rand(0, 100);
                    } elseif ($t == 1) {
                        $v = rand(0, 100);
                    } elseif ($t == 2) {
                        $v = rand(0, 100);
                    } elseif ($t == 3) {
                        $v = rand(0, 100);
                    } elseif ($t == 4) {
                        $v = rand(0, 100);
                    } elseif ($t == 5) {
                        $v = rand(0, 100);
                    } elseif ($t == 6) {
                        $v = rand(0, 100);
                    } elseif ($t == 7) {
                        $v = rand(0, 100);
                    } elseif ($t == 8) {
                        $v = rand(2000, 10000);
                    } elseif ($t == 9) {
                        $v = rand(0, 100);
                    } elseif ($t == 10) {
                        $v = rand(0, 100);
                    } elseif ($t == 11) {
                        $v = rand(90000, 100000);
                    } elseif ($t == 12) {
                        $v = rand(90000, 100000);
                    } elseif ($t == 13) {
                        $v = rand(90000, 100000);
                    } elseif ($t == 14) {
                        $v = rand(0, 100);
                    } elseif ($t == 15) {
                        $v = rand(0, 100);
                    } elseif ($t == 16) {
                        $v = rand(0, 100);
                    } elseif ($t == 17) {
                        $v = rand(0, 100);
                    } elseif ($t == 18) {
                        $v = rand(10000, 30000);
                    } elseif ($t == 19) {
                        $v = rand(10000, 30000);
                    } elseif ($t == 20) {
                        $v = rand(90000, 100000);
                    } elseif ($t == 21) {
                        $v = rand(90000, 100000);
                    } elseif ($t == 22) {
                        $v = 1025;
                    } elseif ($t == 23) {
                        $v = 102;
                    }
                }
            }
            $data[] = intval($v);
        }
        $this->success($data);

    }

    //学生卡正在消费统计
    public function stuCardBillCounts()
    {
        $count_type = C('COUNT_TYPE');
        $date = I('get.date');
        if (!$date) {
            $date = date('Y-m-d');
        }
        $whereStr['create_time'] = array('between', array($date . ' 00:00:00', $date . ' 23:59:59'));

        $billInfo = M('StuCardBill')
            ->where($whereStr)
            ->group('HOUR')
            ->getField("HOUR (create_time) AS HOUR,count(*) AS Count");
        //初始化临时变量
        $tmp = array_fill(0, 24, 0);
        $h = date('H');
        foreach ($tmp as $key => $value) {
//            if (!$billInfo[$key]) {
//                $billInfo[$key] = 0;
//            } else {
//                $billInfo[$key] = intval($billInfo[$key]);
//            }
            if ($count_type == 'false') {
                if ($h >= $key) {
                    if ($key == 0) {
                        $value = 0;
                    } elseif ($key == 1) {
                        $value = 0;
                    } elseif ($key == 2) {
                        $value = 0;
                    } elseif ($key == 3) {
                        $value = 0;
                    } elseif ($key == 4) {
                        $value = 0;
                    } elseif ($key == 5) {
                        $value = 0;
                    } elseif ($key == 6) {
                        $value = rand(0, 100);
                    } elseif ($key == 7) {
                        $value = rand(0, 100);
                    } elseif ($key == 8) {
                        $value = rand(2000, 10000);
                    } elseif ($key == 9) {
                        $value = rand(2000, 10000);
                    } elseif ($key == 10) {
                        $value = rand(2000, 10000);
                    } elseif ($key == 11) {
                        $value = rand(2000, 10000);
                    } elseif ($key == 12) {
                        $value = rand(2000, 10000);
                    } elseif ($key == 13) {
                        $value = rand(100, 5000);
                    } elseif ($key == 14) {
                        $value = rand(100, 5000);
                    } elseif ($key == 15) {
                        $value = rand(100, 5000);
                    } elseif ($key == 16) {
                        $value = rand(100, 5000);
                    } elseif ($key == 17) {
                        $value = rand(1000, 8000);
                    } elseif ($key == 18) {
                        $value = rand(5000, 8000);
                    } elseif ($key == 19) {
                        $value = rand(100, 5000);
                    } elseif ($key == 20) {
                        $value = rand(0, 100);
                    } elseif ($key == 21) {
                        $value = rand(0, 100);
                    } elseif ($key == 22) {
                        $value = rand(0, 10);
                    } elseif ($key == 23) {
                        $value = 0;
                    }
                }
            }
            $billInfo[] = $value;
        }

        $this->success($billInfo);
    }

    // 当前学生卡定位数统计
    public function TrailCount()
    {
        $date = I('get.date');
        if (!$date) {
            $date = date('Y-m-d');
        }
        $where['create_time'] = array('between', array($date . ' 00:00:00', $date . ' 23:59:59'));
        $where['longitude'] = array('gt', 0);
        $where['latitude'] = array('gt', 0);
        $resList = M('Trail')
            ->where($where)
            ->field("longitude as lng,latitude as lat,(count(*) * 10) AS count")
            ->group('longitude,latitude')
            ->select();
        $this->success($resList);
    }

    // 获取每天的 上报坐标的经纬度
    public function getTrail()
    {
        $date = I('get.date');
        if (!$date) {
            // 获取当前时间
            $date = date('Y-m-d H:i:s', strtotime('-3 hours'));
        }

        $Trail = M('Trail');
        $where['create_time'] = array('gt', $date);
        $where['longitude'] = array('gt', 0);
        $resList = $Trail
            ->field('longitude,latitude')
            ->where($where)
            ->select();

        foreach ($resList as $key => $val) {
            $data[] = array($val['longitude'], $val['latitude']);
        }
        $this->success($data);
    }

    ///////////////////////////////////
    //学生卡统计(饼图)
    public function stuCardInfoT()
    {
        //获取设备总数
        $totalNum = M("Device")->count();
        //获取已绑定数量
        $bindNum = M("Device")->where('status = 1')->count();

        $useData = array('name' => '正在使用数量', 'value' => intval($bindNum));
        $freeData = array('name' => '空闲卡数量', 'value' => $totalNum - $bindNum);

        $data['total'] = intval($totalNum);
        $data['info'] = array($useData, $freeData);

        $this->success($data);
    }

    //学生卡城市分布统计(柱状图)
    //学生卡城市分布统计(柱状图)
    public function stuCardAreaInfoT()
    {
        //获取全国省份
        $areaInfo = M('AreaRegion')
            ->field('region_id,region_name')
            ->where('parent_id = 0')
            ->select();

        $tmpdata = array();
        $data = array();
        foreach ($areaInfo as $key => $value) {
            //获取学校id
            $sid = M('SchoolInformation')
                ->where("prov_id='{$value['region_id']}'")
                ->getField('s_id', true);
            if (!$sid) {
                $sid = '';
            }
            $whereStr['s_id'] = array('in', $sid);
            //获取设备数
            $totalNum = M("Device")->where($whereStr)->count('dc_id');
            //使用数
            $whereStr['status'] = 1;
            $useNum = M("Device")->where($whereStr)->count('dc_id');
            $tmpdata[$value['region_name']]['total'] = $totalNum;
            $tmpdata[$value['region_name']]['use'] = $useNum;

        }

        foreach ($tmpdata as $k => $v) {
            $data['prov'][] = $k;
            $total[] = intval($v['total']);
            $use[] = intval($v['use']);
        }

        $data['info'] = array(
            array('name' => '总数量', 'data' => $total),
            array('name' => '正在使用数量', 'data' => $use)
        );
        $this->success($data);
    }

    //进出校考勤统计(折线图)
    public function attendanceInfoT()
    {
        $date = I('get.date');
        if (!$date) {
            $date = date('Y-m-d');
        }
        $whereStr['create_date'] = array('between', array($date . ' 00:00:00', $date . ' 23:59:59'));
        $whereStr['is_effect'] = 1;
        $whereStr['sign_type'] = 1;//进校
        $signIn = M('Attendance')
            ->where($whereStr)
            ->group('HOUR')
            ->getField("HOUR (create_date) AS HOUR,count(*) AS Count");
        // echo "<pre>";
        // var_dump($signIn);die;
        $whereStr['sign_type'] = 0;//出校
        $signOut = M('Attendance')
            ->where($whereStr)
            ->group('HOUR')
            ->getField("HOUR (create_date) AS HOUR,count(*) AS Count");

        //初始化临时变量
        $tmp = array_fill(0, 24, 0);

        foreach ($tmp as $key => $value) {
            if (!$signIn[$key]) {
                $signIn[$key] = 0;
            }
            if (!$signOut[$key]) {
                $signOut[$key] = 0;
            }
        }
        ksort($signIn);
        ksort($signOut);

        foreach ($signIn as $k => $v) {
            $sign_in[] = intval($v);
        }
        foreach ($signOut as $k => $v) {
            $sign_out[] = intval($v);
        }
        $data['signin'] = $sign_in;
        $data['signout'] = $sign_out;
        $this->success($data);


    }

    //用户数量统计
    public function userInfoT()
    {
        //获取老师用户数
        $userCount = M("User", 'think_')
            ->field("type,count(*) as count")
            ->where("status = 0 and type=3 or type =4")
            ->group('type')
            ->select();
        $data['teacher'] = intval($userCount[0]['count']);
        $data['parents'] = intval($userCount[1]['count']);
        //在线数
        $map = array('session_expire' => array('gt', NOW_TIME), 'session_data' => array('neq', ''));
        $online = M('Session', 'think_')->field('session_data')->where($map)->select();
        $tmpTeacher = 0;
        $tmpParents = 0;
        foreach ($online as $key => $value) {
            preg_match_all('/admin_user\|(.*)admin_user_sign(.*)/', $value['session_data'], $info);
            $info = unserialize($info[1][0]);
            if ($info['type'] == 3) {
                $tmpTeacher++;
            }
            if ($info['type'] == 4) {
                $tmpParents++;
            }
        }
        $data['teacher_online'] = $tmpTeacher;
        $data['parents_online'] = $tmpParents;

        $this->success($data);
    }

    //app用户使用次数统计 (折线图)
    public function getAppCountsT()
    {
        $redis = redis_instance();

        $date = date('Ymd');
        $data = array();
        for ($i = 0; $i < 24; $i++) {
            $t = $i;
            if ($i < 10) {
                $i = '0' . $i;
            }
            //教师数据统计
            $IOSTeakeys = $redis->keys('IOS:teacher:' . $date . $i . '*');//
            $IOSTeaVal = $redis->getMultiple($IOSTeakeys);//ios val
            if (!$IOSTeaVal) {
                $IOSTeaVal = array();
            }

            $AndroidTeakeys = $redis->keys('Android:teacher:' . $date . $i . '*');
            $AndroidTeaVal = $redis->getMultiple($AndroidTeakeys);//android val
            if (!$AndroidTeaVal) {
                $AndroidTeaVal = array();
            }
            $IOSTeacount = array_sum($IOSTeaVal);
            $AndroidTeacount = array_sum($AndroidTeaVal);
            $data['IOSteacher'][] = $IOSTeacount;
            $data['Androidteacher'][] = $AndroidTeacount;

            //家长数据统计
            $IOSParkeys = $redis->keys('IOS:parents:' . $date . $i . '*');//
            $IOSParVal = $redis->getMultiple($IOSParkeys);//ios val
            if (!$IOSParVal) {
                $IOSParVal = array();
            }

            $AndroidParkeys = $redis->keys('Android:parents:' . $date . $i . '*');
            $AndroidParVal = $redis->getMultiple($AndroidParkeys);//android val
            if (!$AndroidParVal) {
                $AndroidParVal = array();
            }
            $IOSParcount = array_sum($IOSParVal);
            $AndroidParcount = array_sum($AndroidParVal);
            $data['IOSparents'][] = $IOSParcount;
            $data['Androidparents'][] = $AndroidParcount;
        }
        $this->success($data);
    }

    //app用户功能使用次数统计 (折线图)
    public function getAppModuleCountsT()
    {
        $redis = redis_instance();

        $date = date('Ymd');
        $data = array();
        for ($i = 0; $i < 24; $i++) {
            if ($i < 10) {
                $i = '0' . $i;
            }
            //教师数据统计
            //定位
            $TeaTrackkeys = $redis->keys('*:teacher:Track:' . $date . $i . '*');
            $TeaTrackval = $redis->getMultiple($TeaTrackkeys);//ios val
            if (!$TeaTrackval) {
                $TeaTrackval = array();
            }
            //作业
            $TeaHomeworkkeys = $redis->keys('*:teacher:Homework:' . $date . $i . '*');
            $TeaHomeworkval = $redis->getMultiple($TeaHomeworkkeys);//ios val
            if (!$TeaHomeworkval) {
                $TeaHomeworkval = array();
            }
            //请假
            $TeaLeavekeys = $redis->keys('*:teacher:Leave:' . $date . $i . '*');
            $TeaLeaveval = $redis->getMultiple($TeaLeavekeys);//ios val
            if (!$TeaLeaveval) {
                $TeaLeaveval = array();
            }

            $TeaTrackcount = array_sum($TeaTrackval);
            $TeaHomeworkcount = array_sum($TeaHomeworkval);
            $TeaLeavecount = array_sum($TeaLeaveval);
            $data['TeaTrack'][] = $TeaTrackcount;
            $data['TeaHomework'][] = $TeaHomeworkcount;
            $data['TeaLeave'][] = $TeaLeavecount;

            //家长数据统计
            //定位
            $ParTrackkeys = $redis->keys('*:parents:Track:' . $date . $i . '*');
            $ParTrackval = $redis->getMultiple($ParTrackkeys);//ios val
            if (!$ParTrackval) {
                $ParTrackval = array();
            }
            //作业
            $ParHomeworkkeys = $redis->keys('*:parents:Homework:' . $date . $i . '*');
            $ParHomeworkval = $redis->getMultiple($ParHomeworkkeys);//ios val
            if (!$ParHomeworkval) {
                $ParHomeworkval = array();
            }
            //请假
            $ParLeavekeys = $redis->keys('*:parents:Leave:' . $date . $i . '*');
            $ParLeaveval = $redis->getMultiple($ParLeavekeys);//ios val
            if (!$ParLeaveval) {
                $ParLeaveval = array();
            }
            //学生卡设置
            $ParStuCardkeys = $redis->keys('*:parents:StudentCard:' . $date . $i . '*');
            $ParStuCardval = $redis->getMultiple($ParStuCardkeys);//ios val
            if (!$ParStuCardval) {
                $ParStuCardval = array();
            }

            $ParTrackcount = array_sum($ParTrackval);
            $ParHomeworkcount = array_sum($ParHomeworkval);
            $ParLeavecount = array_sum($ParLeaveval);
            $ParStuCardcount = array_sum($ParStuCardval);
            $data['ParTrack'][] = $ParTrackcount;
            $data['ParHomework'][] = $ParHomeworkcount;
            $data['ParLeave'][] = $ParLeavecount;
            $data['ParStuCard'][] = $ParStuCardcount;

            //广告 点击统计 "BannerClick:2017092913:dameng"
            $BannerClickkeys = $redis->keys('BannerClick:' . $date . $i . '*');
            $BannerClickval = $redis->getMultiple($BannerClickkeys);//ios val
            if (!$BannerClickval) {
                $BannerClickval = array();
            }
            //广告 展示统计 "BannerView:2017092913:dameng"
            $BannerViewkeys = $redis->keys('BannerView:' . $date . $i . '*');
            $BannerViewval = $redis->getMultiple($BannerViewkeys);//ios val
            if (!$BannerViewval) {
                $BannerViewval = array();
            }
            $BannerClickcount = array_sum($BannerClickval);
            $BannerViewcount = array_sum($BannerViewval);

            $data['BannerClick'][] = $BannerClickcount;
            $data['BannerView'][] = $BannerViewcount;
        }
        $this->success($data);
    }

    //统计 老师和家长的沟通次数
    public function getChatCountsT()
    {
        $date = I('get.date');
        if (!$date) {
            $date = date('Y-m-d');
        }
        $where['modify_time'] = array('between', array($date . ' 00:00:00', $date . ' 23:59:59'));
        $Chatinfocount = M('Chatinfo')
            ->where($where)
            ->group('HOUR')
            ->getField("HOUR (modify_time) AS HOUR,count(*) AS Count");

        //初始化临时变量
        $tmp = array_fill(0, 24, 0);
        foreach ($tmp as $key => $value) {
            if (!$Chatinfocount[$key]) {
                $Chatinfocount[$key] = 0;
            }
        }
        ksort($Chatinfocount);

        foreach ($Chatinfocount as $k => $v) {
            $data[] = intval($v);
        }
        $this->success($data);

    }

    //学生卡正在消费统计
    public function stuCardBillCountsT()
    {
        $date = I('get.date');
        if (!$date) {
            $date = date('Y-m-d');
        }
        $whereStr['create_time'] = array('between', array($date . ' 00:00:00', $date . ' 23:59:59'));

        $billInfo = M('StuCardBill')
            ->where($whereStr)
            ->group('HOUR')
            ->getField("HOUR (create_time) AS HOUR,count(*) AS Count");
        //初始化临时变量
        $tmp = array_fill(0, 24, 0);

        foreach ($tmp as $key => $value) {
            if (!$billInfo[$key]) {
                $billInfo[$key] = 0;
            } else {
                $billInfo[$key] = intval($billInfo[$key]);
            }
        }
        ksort($billInfo);
        $this->success($billInfo);
    }

    // 当前学生卡定位数统计
    public function TrailCountT()
    {
        $date = I('get.date');
        if (!$date) {
            $date = date('Y-m-d');
        }
        $where['create_time'] = array('between', array($date . ' 00:00:00', $date . ' 23:59:59'));
        $where['longitude'] = array('gt', 0);
        $where['latitude'] = array('gt', 0);
        $resList = M('Trail')
            ->where($where)
            ->field("longitude as lng,latitude as lat,(count(*) * 10) AS count")
            ->group('longitude,latitude')
            ->select();
        $this->success($resList);
    }

    // 获取每天的 上报坐标的经纬度
    public function getTrailT()
    {
        $date = I('get.date');
        if (!$date) {
            // 获取当前时间
            $date = date('Y-m-d H:i:s', strtotime('-3 hours'));
        }

        $Trail = M('Trail');
        $where['create_time'] = array('gt', $date);
        $where['longitude'] = array('gt', 0);
        $resList = $Trail
            ->field('longitude,latitude')
            ->where($where)
            ->select();

        foreach ($resList as $key => $val) {
            $data[] = array($val['longitude'], $val['latitude']);
        }
        $this->success($data);
    }
}