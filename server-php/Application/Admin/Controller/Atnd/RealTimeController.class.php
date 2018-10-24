<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-实时签到
 */

namespace Admin\Controller\Atnd;

use Admin\Controller\BaseController;

class RealTimeController extends BaseController
{
    /**
     *实时签到
     */
    public function index()
    {
        $this->display();
    }

    public function query()
    {
        //权限
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
        $page = I('get.page');
        $pagesize = I('get.pagesize', $this->PAGE_SIZE);
        $sort = I('get.sort', 'at_id');
        $order = $sort . ' ' . I('get.order', 'desc');
        $Attendance = D('Attendance');

        if ($sdatetime || $edatetime) {
            if (!$sdatetime || !$edatetime) {
                $this->error('时间区间不全！');
            }
            if ($sdatetime && $edatetime) {
                $where['a.create_date'] = array("BETWEEN", array($sdatetime . ':00', $edatetime . ':59'));

            }
        }
        $where['is_effect'] = '1';
//        dump($where);die;
        $result = $Attendance->queryListEX1('*', $where, $order, $page, $pagesize, '');
        $this->success($result);
    }

}