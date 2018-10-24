<?php
/*
*考勤记录查询
*by ruiping date 20161201
*/
namespace Api\Controller;
use Api\Controller\BaseController;
use Think\Model;
class AttendanceController extends BaseController {
    /*
    *数据查询
    *@param
    */
    public function query()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/Attendance/query'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法！');
        }
        $type = $this->getUserType();
        $dates = I('post.dates');

        if (empty($dates)) {
            $this->error('请选择日期！');
        }

        if ($type == 4) {//家长
            $where['b.signin_time'] = array('like', '%' . $dates . '%');

            if (!empty(I('post.stu_id'))) {
                $where['a.stu_id'] = array('EQ', I('post.stu_id'));
            } else {
                $whereP['u_id'] = $this->getUserId();
                $teacherInfo = D('StudentParent')->where($whereP)->find();
                $whereStu['sp_id'] = $teacherInfo['sp_id'];

                $stuInfo = D('StudentGroupAccess')->where($whereStu)->select();
                $stuId = array();
                foreach ($stuInfo as $key => $value) {
                    $stuId[] = $value['stu_id'];
                }
                $map['stu_id'] = array('in', implode(',', $stuId));
                $imeiArr = M('Student')->where($map)->getField('imei_id', true);
                if($imeiArr){
                    $where['a.imei'] = array('in', $imeiArr);
                }
            }

        } elseif ($type == 3) {//老师
            $where['b.signin_time'] = array('EGT', $dates . ' 00:00:00');
            $g_id = I('post.g_id');
            $c_id = I('post.c_id');
            if (empty($g_id)) {
                $this->error('请选择年级！');
            }
            if (empty($c_id)) {
                $this->error('请选择班级！');
            }else{
                $imeiArr = M('Student')->where("c_id = '{$c_id}'")->getField('imei_id', true);
                if($imeiArr){
                    $where['a.imei'] = array('in', $imeiArr);
                }
            }
//            $where['a.g_id'] = $g_id;
//            $where['a.c_id'] = $c_id;
        } else {
            $this->error('用户类型不正确！');
        }

        $Student = D('Student');
        $Leave = D('Leave');
        // var_dump($where);die;
        $attendanceList = $Student->queryListA('a.stu_id,a.stu_name,b.signin_time,b.signout_time', $where);
        foreach ($attendanceList['list'] as $key => $value) {
            $where1['stu_id'] = $value['stu_id'];
            $where1['end_time'] = array('EGT', $dates . ' 00:00:00');
            $leaveInfo = $Leave->where($where1)->select();
            if (empty($leaveInfo)) {
                $attendanceList['list'][$key]['leave'] = '未请假';
            } else {
                $hs = 0;
                $ds = 0;
                foreach ($leaveInfo as $k => $v) {
                    $one = strtotime($v['start_time']);//开始时间 时间戳
                    $tow = strtotime($v['end_time']);//结束时间 时间戳
                    $cle = $tow - $one; //得出时间戳差值
                    /*Rming()函数，即舍去法取整*/
                    $d = floor($cle / 3600 / 24);
                    $h = floor(($cle % (3600 * 24)) / 3600);  //%取余
                    $m = floor(($cle % (3600 * 24)) % 3600 / 60);
                    $s = floor(($cle % (3600 * 24)) % 60);
                    $hs = $hs + $h;
                    $ds = $ds + $d;
                }
                $attendanceList['list'][$key]['leave'] = '请假' . $ds . '天' . $hs . '小时';
            }
        }

        $this->success($attendanceList['list']);
    }

    //获取考勤详情
    public function getDetail(){
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

        if(empty($id)){
            $this->error('参数错误');
        }
        $info = M('Attendance')->field('imei,signin_type,sign_type,create_date')->where(array('at_id'=>$id))->find();
        $info['sign_type'] = C('SCHOOL_TYPE')[$info['sign_type']];//考勤类型  1进校 0出校
        $info['signin_type'] = C('SIGNIB_TYPE')[$info['signin_type']];//考勤状态  1正常 0异常
        if(empty($info)){
            $this->error('参数错误');
        }
        $this->success($info);
    }


    //学生考勤查询
    public function view(){
        $this->error('功能开发中！');//详情页面没有规划
        if(IS_POST===false){
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/Attendance/view'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag){
            $this->error('接口访问不合法！');
        }
        $stu_id = I('post.stu_id');
        if (empty($stu_id)) {
            $this->error('参数错误！');
        }
    }

    /*新增query_v1_0_1请假查询接口
     *
     *输入参数：1.家长端 日期yyyy-MM，stu_id,page,token
     * 2.老师端 日期yyyy-MM-dd,g_id,c_id,page,token
     *返回数据：学生名称，签到类型，签到状态，签到时间，考勤时间段
     *
     * */
    public function query_v1_0_1(){
        if(IS_POST===false){
            $this->error('the request method is not post');
        }
        $u_id = $this->getUserId();
        $token = I('post.token');
        $dates = I('post.dates');
        $sign_type = I('post.sign_type');//'进校 1 出校 0',

        if(($sign_type === '0') || ($sign_type === '1')){
            $where['a.sign_type'] = $sign_type;
        }


        $page = I('post.page',1);
        $pagesize = $this->PAGESIZE;
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag){
            $this->error('接口访问不合法！');
        }
        $type = $this->getUserType();
        $where1['u_id'] = $this->getUserId();
        $order = " a.stu_id desc,a.at_id desc ";
        if (empty($dates)) {
            $this->error('请选择日期！');
        }

        if ($type==4) {//家长
            $stu_id = I('post.stu_id');//家长查询时

            if (empty($stu_id)) {
                $this->error('未选择学生！');
            }else{
                $imei = M('Student')->where("stu_id = '{$stu_id}'")->getField('imei_id');
                if(!$imei){
                    $this->error('未绑定imei！');
                }else{
                    $where['a.imei'] = $imei;
                }
            }
        }elseif ($type==3) {//老师
            $c_id = I('post.c_id');
            if (empty($c_id)) {
                $this->error('请选择班级！');
            }
            $imei = M("Student")->where("c_id = '{$c_id}' and imei_id > 0")->getField('imei_id',true);
            if($imei){
                $where['a.imei'] = array('in',$imei);
            }
        }else{
            $this->error('用户类型不正确！');
        }

        $where['a.create_date'] = array('like','%'.$dates.'%');
        ///////////////////////////
        // var_dump($where);die; //
        ///////////////////////////
         $where['a.is_effect'] = '1';

        $result = D("Attendance")->queryListEx1_0_1('a.signin_type,a.signout_time,a.signin_time,a.create_date as s_time,
            a.sign_type,a.imei',
            $where,$order,$page,$pagesize,'');

        $page_szie = ceil($result['count']/$pagesize);
        $arr['total_page'] = $page_szie;
        $arr['total_count'] = $result['count'];
        $arr['page'] = $page;
        $arr['content'] = $result['list'];
        $this->success($arr);
    }
}