<?php
/**
 * 修改备注：平安校园-学生卡在线数量
 */
namespace Admin\Controller\StuCard;

use Admin\Controller\BaseController;

class StuCardLasttimeController extends BaseController
{
    public function index()
    {
        $this->display();
    }

    //查询数据
    public function query()
    {
        //权限
        $ids = $this->authRule();
        $map = "";
        if ($this->getType() == 1) {// 学校管理员
            $map['s_id'] = $ids['s_id'];
        } elseif ($this->getType() == 3) {// 老师
            $map['s_id'] = $ids['s_id'];
            $map['a_id'] = $ids['a_id'];
            $classList = $this->getTeacherClass($ids['t_id']);
            $map['g_id'] = array('IN', $classList['g_id']);//$ids['a_id'];
            $map['c_id'] = array('IN', $classList['c_id']);
            $map['user_id'] = $this->getUserId();
        } elseif ($this->getType() == 4) {// 家长
            $map['s_id'] = $ids['s_id'];
            $map['a_id'] = $ids['a_id'];
            $classList = $this->getStudentClass($ids['stu_id']);
            // var_dump($classList);
            $map['g_id'] = array('IN', $classList['g_id']);
            $map['c_id'] = array('IN', $classList['c_id']);
        } else {
            $arr = $_GET;
            $sthInfo = getSchoolId($arr);
            $map['s_id'] = array('IN', $sthInfo);
        }
        $stuLasttime = M('stuCardlasttime');
    }


    public function queryList()
    {
        $s_id = I('get.s_id');//学校ID
        $sort = I('get.sort', 'id');
        $orderby = $sort . ' ' . I('get.order', 'desc');
        $groupby = 'imei';
        $StucardLasttime = D('StucardLasttime');
        $time = I('get.view_type');
        $s_id = I('get.s_id');//学校ID
        $a_id = I('get.a_id');//校区ID
        $g_id = I('get.g_id');
        if (!empty($s_id)) {
            $where['sid'] = $s_id;
            $where1['s_id'] = $s_id;
        }
        if (!empty($a_id)) {
            $where['aid'] = $a_id;
            $where1['a_id'] = $a_id;
        }
        if (!empty($g_id)) {
            $where['gid'] = $g_id;
        }
        $where1['status'] = 1;
        //获取时间  年 0   月 1   日 2
        $timeList = $StucardLasttime->where($where)->getField('time', true);
//          var_dump($timeList);die;
        $res = array();
        if ($time == 2) {
            //按日计算
            foreach ($timeList as $key => $value) {
                $res[] = substr($value, 0, 10);
            }
        } elseif ($time == 1) {
            //按月计算
            foreach ($timeList as $key => $value) {
                $res[] = substr($value, 0, 7);
            }
        } else {
            foreach ($timeList as $key => $value) {
                $res[] = substr($value, 0, 4);
            }
        }

        $res = array_unique($res);
        $res = array_merge($res);
        // var_dump($res);die;
        //统计总的学生卡数
        $total = D('Device')->where($where1)->count('dc_id');
        // var_dump($total);die;
        $data = array();
        $rate = array();
        // $StucardLasttime = group('user_id,test_time')
        if ($time == 2) {
            foreach ($res as $key => $value) {
                $where['time'] = array("BETWEEN", array($value . ' 00:00:00', $value . ' 23:59:59'));
                $list = $StucardLasttime->distinct(true)->field('imei')->where($where)->getField('imei', true);
                $list = count($list);
                // var_dump($list);die;
                $data[] = $list;
                $rate[] = substr((($list / $total) * 100), 0, 4);
            }
            # code...
        } elseif ($time == 1) {
            # code...
            $list1 = '';
            $res1 = array();
            foreach ($res as $key => $value) {
                // var_dump($value);
                $stime = strtotime($value . '-01 00:00:00');
                $etime = strtotime($value . '-31 23:59:59');
                foreach ($timeList as $k => $v) {
                    $res1[] = substr($v, 0, 10);
                }
                $res1 = array_unique($res1);
                $res1 = array_merge($res1);

                foreach ($timeList as $key1 => $value1) {
                    $time1 = strtotime($value1 . ' 00:00:00');
                    $time2 = strtotime($value1 . ' 23:59:59');
                    if ($stime <= $time1 && $time2 <= $etime) {
                        # code...
                        // var_dump($stime);
                        $where['time'] = array("BETWEEN", array($value1 . ' 00:00:00', $value1 . ' 23:59:59'));
                        $list = $StucardLasttime->distinct(true)->field('imei')->where($where)->getField('imei', true);
                        $list = count($list);
                        // $a = substr($value1, 0,7);
                        // var_dump($a);
                        // if ($a ===$value) {

                        //     $list1 +=$list;
                        //     # code...
                        // }
                    }
                }
                $data[] = $list1;
                $rate[] = substr((($list / $total) * 100), 0, 4);

            }
        } else {
            $where['time'] = array("BETWEEN", array($value . '-01-01 00:00:00', $value . '-12-31 23:59:59'));
            $list = $StucardLasttime->distinct(true)->field('imei')->where($where)->getField('imei', true);
            $list = count($list);
            $data[] = $list;
            $rate[] = substr((($list / $total) * 100), 0, 4);
        }

        $data1['times'] = $res;
        $data1['indata'] = $data;
        $data1['inrate'] = $rate;
        $this->success($data1);

    }

    public function queryList_bck()
    {
        $sId = I('get.s_id');//学校ID
        $aId = I('get.a_id');//校区ID
        $gId = I('get.g_id');
        $cId = I('get.c_id');
        $type = I('get.view_type');//1日 2七日 3三十日
        $Trail = M('Trail');

        $end_time = date("Y-m-d");
        $endtime = strtotime($end_time);

        if ($cId) {//判断c_id
            $map['b.c_id'] = array('in', $cId);
        } else {
            if ($gId) {//判断g_id
                $map['b.g_id'] = array('in', $gId);
            } else {
                if ($aId) {//判断a_id
                    $map['b.a_id'] = array('in', $aId);
                } else {
                    if ($sId) {//判断s_id
                        $map['b.s_id'] = array('in', $sId);
                    }
                }
            }
        }
        //获取设备总数
        $map['imei_id'] = array('gt', 0);
//        $Count = M('Student')->alias('b')->where($map)->count();

        if ($type == 1 || $type == 2 || $type == 3) {//日
            $map['a.create_time'] = array("BETWEEN", array($end_time . ' 00:00:00', $end_time . ' 23:59:59'));
            $times = "HOUR (a.create_time)";
        } elseif ($type == 2) {//7日
            $begintime = strtotime('-2 day');
            for ($start = $begintime; $start <= $endtime; $start += 24 * 3600) {
                $dateArr[] = date("Y-m-d", $start);
            }

            $map['a.create_time'] = array("BETWEEN", array(date('Y-m-d', strtotime('-7 day')) . ' 00:00:00', $end_time . ' 23:59:59'));
            $times = "DATE_FORMAT(a.create_time,'%Y-%m-%d')";
        } elseif ($type == 3) {//15日
            $begintime = strtotime('-2 day');
            for ($start = $begintime; $start <= $endtime; $start += 24 * 3600) {
                $dateArr[] = date("Y-m-d", $start);
            }

            $map['a.create_time'] = array("BETWEEN", array(date('Y-m-d', strtotime('-15 day')) . ' 00:00:00', $end_time . ' 23:59:59'));
            $times = "DATE_FORMAT(a.create_time,'%Y-%m-%d')";
        }
//Online:2017112217:s:11:a:12:g:13:c:14
        $list = $Trail->alias('a')
            ->join(" __STUDENT__ b on a.imei = b.imei_id ")
            ->where($map)
            ->group('times')
            ->getField("{$times} as times,count(DISTINCT a.imei) AS Count");

        //未获取数据
        if (!$list) {
            $list = array();
        }
        //补全空白时间段的结果集
        if ($type == 1) {
            for ($i = 0; $i < 24; $i++) {
                if (!$list[$i]) {
                    $list[$i] = 0;
                }
            }
            $count_list = $list;
        } else {
            foreach ($dateArr as $key => $value) {
                if (!$list[$value]) {
                    $count_list[$value] = 0;
                } else {
                    $count_list[$value] = $list[$value]['Count'];
                }
            }

        }

        $this->success($count_list);
    }

    //redis统计在线学生卡
    public function queryList2()
    {
        $sId = I('get.s_id');//学校ID
        $aId = I('get.a_id');//校区ID
        $gId = I('get.g_id');
        $cId = I('get.c_id');
        $type = I('get.view_type');//1日 2七日 3三十日


        if ($cId) {//判断c_id
            $schoolInfo = M('Class')->field("s_id,a_id,g_id")->where("g_id = '{$gId}'")->find();
            $map['b.c_id'] = array('in', $cId);
            $schoolkey = ':s:' . $schoolInfo['s_id'] . ':a:' . $schoolInfo['a_id'] . ':g:' . $schoolInfo['g_id'] . ':c:' . $cId;
        } else {
            if ($gId) {//判断g_id
                $schoolInfo = M('Class')->field("s_id,a_id")->where("g_id = '{$gId}'")->find();
                $map['b.g_id'] = array('in', $gId);
                $schoolkey = ':s:' . $schoolInfo['s_id'] . ':a:' . $schoolInfo['a_id'] . ':g:' . $gId . ':*';
            } else {
                if ($aId) {//判断a_id
                    $map['b.a_id'] = array('in', $aId);
                    $schoolkey = ':s:' . $sId . ':a:' . $aId . ':*';
                } else {
                    if ($sId) {//判断s_id
                        $map['b.s_id'] = array('in', $sId);
                        $schoolkey = ':s:' . $sId . ':*';
                    }
                }
            }
        }
        //获取设备总数
        $map['imei_id'] = array('gt', 0);
        $Total = M('Student')->alias('b')->where($map)->count();

        $redis = redis_instance();
        //Online:2017112219:s:77:a:160:g:106:c:76
        if ($type == 1) {//日
            $dateStr = date('Ymd');
            //获取keys
            for ($i = 0; $i < 24; $i++) {
                if ($i < 10) {
                    $i = '0' . $i;
                }
                //拼接键名
                $key = 'Online:' . $dateStr . $i . $schoolkey;

                //获取数据库key
                $keys = $redis->keys($key);
                if (!$keys) {//此时间段没有数据
                    $count = 0;
                    $members = array();
                } else {
                    $countArr = array();
                    $members = $redis->sUnion($keys);
                    foreach ($members as $k => $imei) {
                        $stu_info = get_stu_info($imei);
                        $members[$k] = $stu_info['stu_name'] . '--' . $imei;

                    }
                    foreach ($keys as $k => $v) {
                        //获取集合总数
                        $countArr[] = $redis->sCard($v);
                    }
                    $count = array_sum($countArr);

                }
                $data['count'][] = $count;//在线数
                $data['ratio'][] = round($count / $Total * 100, 2);//在线率
                $data['online'][] = $members;//在线列表

            }
        } else {
            if ($type == 2) {//7日
                $end_time = date("Y-m-d");
                $endtime = strtotime($end_time) + 24 * 3600;
                $begintime = strtotime('-7 day');
                for ($start = $begintime; $start <= $endtime; $start += 24 * 3600) {
                    $dateArr[] = date("Ymd", $start);
                }

            } elseif ($type == 3) {//15日
                $end_time = date("Y-m-d");
                $endtime = strtotime($end_time) + 24 * 3600;
                $begintime = strtotime('-15 day');
                for ($start = $begintime; $start <= $endtime; $start += 24 * 3600) {
                    $dateArr[] = date("Ymd", $start);
                }
            }

            foreach ($dateArr as $k7 => $v7) {
                //拼接键名
                $key = 'Online:' . $v7 . '*' . $schoolkey;

                //获取数据库key
                $keys = $redis->keys($key);
                if (!$keys) {//此时间段没有数据
                    $count = 0;
                    $members = array();
                } else {
                    //获取成员并集
                    $members = $redis->sUnion($keys);
                    foreach ($members as $k => $imei) {
                        $stu_info = get_stu_info($imei);
                        $members[$k] = $stu_info['stu_name'] . '--' . $imei;

                    }

                    $count = count($members);
                }
                $data['count'][$v7] = $count;//在线数
                $data['ratio'][$v7] = round($count / $Total * 100, 2);//在线率
                $data['online'][$v7] = $members;//在线列表
            }
        }
        $data['total'] = $Total;
        $this->success($data);
    }


    //统计进出校
    public function queryList3()
    {
        $redis = redis_instance();
        $sId = I('get.s_id');//学校ID
        $aId = I('get.a_id');//校区ID
        $gId = I('get.g_id');
        $cId = I('get.c_id');
        $dateStr = I('get.date', date('Y-m-d'));//日期

        if ($cId) {//判断c_id
            $map['c_id'] = array('in', $cId);
            $schoolkey = ':s:' . $sId . ':a:' . $aId . ':g:' . $gId . ':c:' . $cId;
        } else {
            if ($gId) {//判断g_id
                $map['g_id'] = array('in', $gId);
                $schoolkey = ':s:' . $sId . ':a:' . $aId . ':g:' . $gId . ':*';
            } else {
                if ($aId) {//判断a_id
                    $map['a_id'] = array('in', $aId);
                    $schoolkey = ':s:' . $sId . ':a:' . $aId . ':*';
                } else {
                    if ($sId) {//判断s_id
                        $map['s_id'] = array('in', $sId);
                        $schoolkey = ':s:' . $sId . ':*';
                    }
                }
            }
        }
        $map['imei_id'] = array('gt', 0);
        $Total = M('Student')->where($map)->count();
        //拼接键名  Signin:20171122:s:77:a:160:g:106:c:76

        $key = 'Signin:' . $dateStr . $schoolkey;

        //获取数据库key
        $keys = $redis->keys($key);

        $member = $redis->sUnion($keys);
        if(!$member){
            $member = array();
        }
        $inschool_count = count($member);
        $InSchool = array('name' => '在校人数', 'value' => $inschool_count);

        //获取请假人数
        $map['create_time'] = array('between', array($dateStr . " 00:00:00", $dateStr . " 23:59:59"));
        $leave_count = M('Leave')->where($map)->group('stu_id')->count();
        $leave_count = intval($leave_count);
        $leaveSchool = array('name' => '请假人数', 'value' => $leave_count);
        //获取未在校人数
        $outschool_count = $Total - $inschool_count;

        $outSchool = array('name' => '离校人数', 'value' => $outschool_count);
        $data = array($InSchool, $leaveSchool, $outSchool);
        $this->success($data);

    }
}

?>
