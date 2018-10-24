<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-考勤记录
 */

namespace Admin\Controller\Atnd;

use Admin\Controller\BaseController;

class AtndRecordController extends BaseController
{
    /**
     *考勤记录
     */
    public function index()
    {
        $this->display();
    }

    public function query()
    {
        $ids = $this->authRule();
        $where = "";
        if ($this->getType() == 1) {
            $sId = $ids['s_id'];
        } elseif ($this->getType() == 3) {//教师
            $sId = $ids['s_id'];
            $aId = $ids['a_id'];
            $classList = $this->getTeacherClass($ids['t_id']);
            $gId = $classList['g_id'];//$ids['a_id'];
            $cId = $classList['c_id'];
        } elseif ($this->getType() == 4) {
            $stu_id = $ids['stu_id'];
        } else {
            $arr = $_GET;
            $sthInfo = getSchoolId($arr);
            $sId = $sthInfo;
        }
        $stuName = I('get.keyword');
        $sign_type = I('get.sign_type');

        if (I('get.s_id')) {
            $sId = I('get.s_id');//学校
        }
        if (I('get.a_id')) {
            $aId = I('get.a_id');//校区
        }
        if (I('get.g_id')) {
            $gId = I('get.g_id');//年级
        }
        if (I('get.c_id')) {
            $cId = I('get.c_id');//班级
        }
        if (I('get.stu_id')) {
            $stu_id = I('get.stu_id');//学生
        }
        if ($stuName != '') {//搜索学生名称
            $where1['stu_name'] = array('like', '%' . $stuName . '%');//"stu_name like '%".$stuId."%'"
            $stu_id = M('Student')->where($where1)->getField('stu_id', true);
            if (!$stu_id) {
                $this->error('输入的姓名不存在！');
            }
        }

        if ($stu_id) {//判断stu_id
            $map['stu_id'] = array('in', $stu_id);
        } else {
            if ($cId) {//判断c_id
                $map['c_id'] = array('in', $cId);
            } else {
                if ($gId) {//判断g_id
                    $map['g_id'] = array('in', $gId);
                } else {
                    if ($aId) {//判断a_id
                        $map['a_id'] = array('in', $aId);
                    } else {
                        if ($sId) {//判断s_id
                            $map['s_id'] = array('in', $sId);
                        }
                    }
                }
            }
        }

        $imei = M('Student')->where($map)->getField('imei_id', true);
        if ($imei) {
            $where['a.imei'] = array('in', $imei);
        } else {
            $result['count'] = 0;
            $result['list'] = array();
            $result['yichang'] = 0;
            $result['zhengc'] = 0;
            $this->success($result);
        }
        $sdatetime = I('get.sdatetime');
        $edatetime = I('get.edatetime');
        if (!$sdatetime) {
            $sdatetime = date('Y-m-d 00:00:00');
        } else {
            //2017-11-09 21:00
            $sdatetime .= ':00';
        }
        if (!$edatetime) {
            $edatetime = date('Y-m-d 23:59:59');
        } else {
            $edatetime .= ':59';
        }
        $page = I('get.page');
        $pagesize = I('get.pagesize', $this->PAGE_SIZE);
        $sort = I('get.sort', 'at_id');
        $order = $sort . ' ' . I('get.order', 'desc');
        $Attendance = D('Attendance');

        if ($sign_type == 0 || $sign_type == 1) {
            $where['sign_type'] = $sign_type;
        }
        if ($sdatetime || $edatetime) {
            if (!$sdatetime || !$edatetime) {
                $this->error('时间区间不全！');
            }
            if ($sdatetime && $edatetime) {
                $where['a.create_date'] = array("BETWEEN", array($sdatetime, $edatetime));
            }
        }

        $where['a.is_effect'] = '1';
        $result = $Attendance->queryListEX1('
            a.signin_type,a.signout_time,a.signin_time,a.create_date,
            a.sign_type,a.imei', $where, $order, $page, $pagesize, '');

        $this->success($result);
    }

    //获取进出校状态统计数据
    public function getStatusCount()
    {
        $ids = $this->authRule();
        $where = "";
        if ($this->getType() == 1) {
            $sId = $ids['s_id'];
        } elseif ($this->getType() == 3) {//教师
            $sId = $ids['s_id'];
            $aId = $ids['a_id'];
            $classList = $this->getTeacherClass($ids['t_id']);
            $gId = $classList['g_id'];//$ids['a_id'];
            $cId = $classList['c_id'];
        } elseif ($this->getType() == 4) {
            $stu_id = $ids['stu_id'];
        } else {
            $arr = $_GET;
            $sthInfo = getSchoolId($arr);
            $sId = $sthInfo;
        }
        $sign_type = I('get.sign_type');

        if (I('get.s_id')) {
            $sId = I('get.s_id');//学校
        }
        if (I('get.a_id')) {
            $aId = I('get.a_id');//校区
        }
        if (I('get.g_id')) {
            $gId = I('get.g_id');//年级
        }
        if (I('get.c_id')) {
            $cId = I('get.c_id');//班级
        }
        if (I('get.stu_id')) {
            $stu_id = I('get.stu_id');//学生
        }
        $stuName = I('get.keyword');
        if ($stuName != '') {//搜索学生名称
            $where1['stu_name'] = array('like', '%' . $stuName . '%');//"stu_name like '%".$stuId."%'"
            $stu_id = M('Student')->where($where1)->getField('stu_id', true);
            if (!$stu_id) {
                $this->error('输入的姓名不存在！');
            }
        }

        if ($stu_id) {
            $map['b.stu_id'] = array('in',$stu_id);
        } else {
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
        }
        $sdatetime = I('get.sdatetime');
        $edatetime = I('get.edatetime');
        if (!$sdatetime) {
            $sdatetime = date('Y-m-d 00:00:00');
        } else {
            //2017-11-09 21:00
            $sdatetime .= ':00';
        }
        if (!$edatetime) {
            $edatetime = date('Y-m-d 23:59:59');
        } else {
            $edatetime .= ':59';
        }
        if ($sign_type == 0 || $sign_type == 1) {
            $map['a.sign_type'] = $sign_type;
        }
        if ($sdatetime || $edatetime) {
            if (!$sdatetime || !$edatetime) {
                $this->error('时间区间不全！');
            }
            if ($sdatetime && $edatetime) {
                $map['a.create_date'] = array("BETWEEN", array($sdatetime, $edatetime));
            }
        }

        $map['a.is_effect'] = '1';


        $atteList = M('Attendance')
            ->alias('a')
            ->field('b.a_id,a.create_date,a.sign_type,a.imei')
            ->join(" __STUDENT__ b on a.imei = b.imei_id ")
            ->where($map)
            ->select();

        $sgtypeInfo = C('SIGNIB_TYPE');
        $nowTime = time();

        $normal = 0;
        $unusual = 0;

        foreach ($atteList as $key => $value) {
            //获取学生详情
            $stu_info = M('Student')
                ->cache(true)
                ->field('a_id,stu_no,stu_name')
                ->where("imei_id = '{$value['imei']}'")
                ->find();

            //获取考勤区间
            $workInfo = M('WorkRule')
                ->cache(true)
                ->field('start_time,end_time')
                ->where("a_id = '{$stu_info['a_id']}'")
                ->find();

            $beganTime = strtotime($workInfo['start_time']);
            $endTime = strtotime($workInfo['end_time']);

            if ($beganTime < $nowTime && $nowTime < $endTime) {
                $value['signin_type'] = 0;//异常
                $unusual++;
            } else {
                $value['signin_type'] = 1;//正常
                $normal++;
            }
            $ret['list'][$key]['signin_type'] = $sgtypeInfo[$value['signin_type']];
            $ret['list'][$key]['jc_time'] = $value['create_date'];
        }
        $data['yichang'] = $unusual;
        $data['zhengc'] = $normal;

        $this->success($data);
    }

    public function ss()
    {
        $Attendance = D('Attendance');
        $where['is_effect'] = '1';
        $res = $Attendance->where($where)->order('create_date asc')->select();
        foreach ($res as $key => $value) {
            $where['stu_id'] = $value['stu_id'];
            $signin = $value['signin_type'];
            if ($signin == '1') {
                $where['sign_type'] = '1';
                $res1 = $Attendance->where($where)->order('create_date asc')->select();
                if (count($res1) > 2) {
                    foreach ($res1 as $k => $v) {


                        if (strtotime($res1[$k + 1]['create_date']) - strtotime($res1[$k]['create_date']) < 180) {


                            $data['is_effect'] = '0';
                            $resinfo = $Attendance->where(array('at_id' => $v['at_id']))->save($data);
                            # code...
                        }
                        # code...
                    }


                }

                # code...
            } else {
                $where['sign_type'] = '0';

                $res1 = $Attendance->where($where)->order('create_date asc')->select();

                if (count($res1) > 2) {
                    foreach ($res1 as $k => $v) {
                        // $name = strtotime($res1[$k+1]['create_date']);
                        //  var_dump(strtotime($res1[$k]['create_date']));
                        //     var_dump(strtotime($res1[$k+1]['create_date']));
                        //     var_dump($where['stu_id']);die;
                        if (strtotime($res1[$k + 1]['create_date']) - strtotime($res1[$k]['create_date']) < 180) {

                            $data['is_effect'] = '0';
                            // var_dump($res1[$k+1]['create_date']);die;
                            // $time = $res1[$k+1]['create_date'];
                            $resinfo = $Attendance->where(array('at_id' => $res1[$k + 1]['at_id']))->save($data);
                            # code...
                        }
                        # code...
                    }


                    # code...
                }


            }


        }


    }
}