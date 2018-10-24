<?php

namespace Common\Model;

use Common\Model\BaseModel;

class AttendanceModel extends BaseModel
{

    protected $_validate = array();

    public function queryListEx($fields, $where = null, $orderby = null, $page = 0, $pagesize = 0)
    {//实时签到
        if ($page) {
            $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
        }
        if ($page) {
            $ret['list'] = self::field($fields)->where($where)->order($orderby)->page($page, $pagesize)->select();
        } else {
            $ret['list'] = self::field($fields)->where($where)->order($orderby)->select();
        }
        if ($page == 0) {
            $ret ["count"] = count($ret ["list"]);
        } else {
            $ret ["count"] = $this->getCount(); //获得记录总数
        }
//	    $arrMaid = array();
//        foreach($list as $key => $value){
//            if($value['stu_id'] > 0)
//                $arrMaid[] = $value['stu_id'];
//        }
//        $where['stu_id'] = array('in',implode(',', $arrMaid));
        $studentInfo = D('student')->getField('imei_id,stu_name,stu_no');
        $classInfo = D('class')->getField('c_id,name');
        $sgtypeInfo = C('SCHOOL_TYPE');
        foreach ($ret['list'] as &$value) {
            $value['stu_name'] = isset($studentInfo[$value['imei']]['stu_name']) ? $studentInfo[$value['imei']]['stu_name'] : $value['stu_name'];
            $value['stu_no'] = isset($studentInfo[$value['imei']]['stu_no']) ? $studentInfo[$value['imei']]['stu_no'] : $value['stu_no'];
            $value['c_id'] = isset($classInfo[$value['c_id']]) ? $classInfo[$value['c_id']] : $value['c_id'];
            $value['sign_type'] = isset($sgtypeInfo[$value['sign_type']]) ? $sgtypeInfo[$value['sign_type']] : $value['sign_type'];
            $value['jc_time'] = ($value['sign_type'] == "进校") ? $value['signin_time'] : $value['signout_time'];
        }
        //echo self::getLastSql();


        return $ret;
    }

    public function queryListEx1($fields, $where = null, $orderby = null, $page = 0, $pagesize = 0)
    {//考勤记录
        if ($page) {
            $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
        }
        if ($page) {
            $ret['list'] = self::alias('a')->field($fields)->where($where)->order($orderby)->page($page, $pagesize)->select();
        } else {
            $ret['list'] = self::alias('a')->field($fields)->where($where)->order($orderby)->select();
        }

        if ($page == 0) {
            $ret ["count"] = count($ret ["list"]);
        } else {
            $ret ["count"] = $this->getCount(); //获得记录总数
        }

        // echo self::getLastSql();
        $stypeInfo = C('SCHOOL_TYPE');
        $sgtypeInfo = C('SIGNIB_TYPE');
        $nowTime = time();

        $normal = 0;
        $unusual = 0;

        foreach ($ret['list'] as $key => $value) {
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

            $ret['list'][$key]['shijianduan'] = $workInfo['start_time'] . '-' . $workInfo['end_time'];//时间段
            $beganTime = strtotime($workInfo['start_time']);
            $endTime = strtotime($workInfo['end_time']);

            if ($beganTime < $nowTime && $nowTime < $endTime) {
                $value['signin_type'] = 0;//异常
                $unusual++;
            } else {
                $value['signin_type'] = 1;//正常
                $normal++;
            }

            $ret['list'][$key]['stu_name'] = $stu_info['stu_name'];
            $ret['list'][$key]['stu_no'] = $stu_info['stu_no'];
//            $ret['list'][$key]['c_id'] = $stu_info['c_id'];
            $ret['list'][$key]['sign_type'] = $stypeInfo[$value['sign_type']];
            $ret['list'][$key]['signin_type'] = $sgtypeInfo[$value['signin_type']];
            $ret['list'][$key]['jc_time'] = $value['create_date'];
        }
        $ret['yichang'] = $unusual;
        $ret['zhengc'] = $normal;
        return $ret;
    }

    public function queryListEx1_0_1($fields, $where = null, $orderby = null, $page = 0, $pagesize = 0)
    {//考勤记录
        if ($page) {
            $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
        }
        if ($page) {
            $ret['list'] = self::alias('a')->field($fields)->where($where)->order($orderby)->page($page, $pagesize)->select();
        } else {
            $ret['list'] = self::alias('a')->field($fields)->where($where)->order($orderby)->select();
        }

        if ($page == 0) {
            $ret ["count"] = count($ret ["list"]);
        } else {
            $ret ["count"] = $this->getCount(); //获得记录总数
        }
        $stypeInfo = C('SCHOOL_TYPE');//进校 出校
        $sgtypeInfo = C('SIGNIB_TYPE');
        $nowTime = time();

        $normal = 0;
        $unusual = 0;

        foreach ($ret['list'] as $key => $value) {

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


            $ret['list'][$key]['shijianduan'] = $workInfo['start_time'] . '-' . $workInfo['end_time'];//时间段
            $beganTime = strtotime($workInfo['start_time']);
            $endTime = strtotime($workInfo['end_time']);

            if ($beganTime < $nowTime && $nowTime < $endTime) {
                $value['signin_type'] = 0;//异常
                $unusual++;
            } else {
                $value['signin_type'] = 1;//正常
                $normal++;
            }

            $ret['list'][$key]['shijianduan'] = $workInfo['start_time'] . '-' . $workInfo['end_time'];//时间段
            //签到时间
            $nowTime = explode(' ', $value['s_time'])[1];

            $ret['list'][$key]['stu_name'] = $stu_info['stu_name'];
            $ret['list'][$key]['school_type'] = $stypeInfo[$value['sign_type']];//进校出校
            //判断签到正常异常
            $ret['list'][$key]['signin_type'] = $sgtypeInfo[$value['signin_type']];
        }
        return $ret;
    }


}