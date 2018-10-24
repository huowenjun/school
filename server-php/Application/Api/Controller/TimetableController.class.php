<?php
/*
*课程表查询
*by ruiping date 20161201
*/
namespace Api\Controller;
use Api\Controller\BaseController;
use Think\Model;
class TimetableController extends BaseController {
    /*
    *数据查询
    *@param
    */
    public function query(){
        if(IS_POST===false){
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/Timetable/query'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
    	//校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag){
            $this->error('接口访问不合法！');
        }
        $c_id = I('post.c_id');
        if (empty($c_id)) {
            $this->error('参数错误');
        }
        $where['c_id'] = array('EQ',$c_id);
        $timetableInfo = D('Timetable')->where($where)->find();
        $Timetable = unserialize($timetableInfo['timetable']);
        $courseInfo = D('Course')->where($where)->getField('crs_id,name');
        $arr['timeTable'] = $Timetable;
        $arr['course'] = $courseInfo;
        $this->success($arr);
    }
}