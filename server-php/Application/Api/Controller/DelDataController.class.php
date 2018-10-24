<?php
namespace Api\Controller;
use Think\Controller;
class DelDataController extends Controller {
    //删除考勤
    public function delatt(){

        $sch_attendance = M("Attendance");

        $res = $sch_attendance->where(" s_id = 64 ")->delete();
        $info = $sch_attendance->where("  s_id = 64  ")->select();
        if($res){

            echo 'success';
        }else{
            if($info){
                echo 'fail';
            }else{
                echo 'success';
            }

        }
    }

}