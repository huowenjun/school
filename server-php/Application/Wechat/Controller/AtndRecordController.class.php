<?php
namespace Wechat\Controller;
use Think\Controller;
class AtndRecordController extends BaseController {
    public function index(){
        $ids = $this->authRule();
        $where = "";
//    	$cId = I('post.c_id');
    	$stuId = I('post.stu_id');
    	$sdate = I('post.sdate');
        if ($this->getType() == 4) {// 家长
            $where['stu_id'] = array('IN', $ids['stu_id']);
        } else {

        }
//    	if (!empty($cId)) {
//    		$where1 = "name like '%".$cId."%'";
//            $cInfo = D('Class')->where($where1)->select();
//            $c_id = "";
//            foreach ($cInfo as $key => $value) {
//                $c_id = $c_id.$value['c_id'].",";
//            }
//            $where['c_id'] = array('IN',rtrim($c_id,','));
//        }
        if (!empty($stuId)) {
        	$where1 = "stu_name like '%".$stuId."%'";
            $cInfo = D('Student')->where($where1)->select();   
            $stu_id = "";
            foreach ($cInfo as $key => $value) {
                $stu_id = $stu_id.$value['stu_id'].",";
            }
            $where['stu_id'] = array('IN',rtrim($stu_id,','));
        }
        if(!empty($sdate)){
            $map['signin_time'] = array('like', '%'.$sdate.'%');
            $map['signout_time'] = array('like', '%'.$sdate.'%');
            $map['_logic'] = 'or';
            $where['_complex'] = $map;
        }
        $Attendance = D('Attendance');
        $attendanceList = $Attendance->queryListEx1('*',$where);
        $this->attendance=$attendanceList['list'];
        // $this->cId=$cId;
        // $this->stuId=$stuId;
        // $this->sdate=$sdate;
        $this->display();
    }
    public function getclassList(){
    	$ids = $this->authRule();
        $where="";
 		if ($this->getType()==4) {// 家长
            $classList = $this->getStudentClass($ids['stu_id']);
            $where['g_id'] = array('IN',$classList['g_id']);
            $where['c_id'] = array('IN',$classList['c_id']);
        }else{

        }
        $res = D('Class')->where($where)->field('name as title,c_id as value')->select();
        $this->success($res);
    }

}