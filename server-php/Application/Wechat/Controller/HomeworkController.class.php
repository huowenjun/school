<?php
namespace Wechat\Controller;
use Wechat\Controller\BaseController;
class HomeworkController extends BaseController{
    public function index(){
        $ids = $this->authRule();
        $where = "";
        $userId = I('post.stu_id');
        $stime = I('post.stime');
        $etime = I('post.etime');
        if ($this->getType() == 4) {// 家长
            $classList = $this->getStudentClass($ids['stu_id']);
            $where['g_id'] = array('IN', $classList['g_id']);
            $where['c_id'] = array('IN', $classList['c_id']);
        } else {

        }
        if (!empty($stime) && !empty($etime)) {
            $where['create_time'] = array('BETWEEN',array($stime.' 00:00:00',$etime.' 23:59:59'));
        }elseif(!empty($stime)){
            $where['create_time'] = array('EGT',$stime.' 00:00:00');
        }elseif(!empty($etime)){
            $where['create_time'] = array('ELT',$etime.' 23:59:59');
        }
        if (!empty($userId)) {
            $where1['stu_id'] = $userId;
            $stuInfo = D('Student')->where($where1)->select();
            $c_id = "";
            foreach ($stuInfo as $key => $value) {
                $c_id = $value['c_id'];
            }
            $where['c_id'] = $c_id;
        }

        $homework = D('Homework')->queryListEx('*',$where);
        $this->homework=$homework['list'];
        $this->display();
    }
    public function getstuList(){
        $ids = $this->authRule();
        $where="";
        if ($this->getType()==4) {
             $where['stu_id'] = array('IN',$ids['stu_id']);
        }else{

        }
        $res = D('Student')->where($where)->field("stu_name as title,stu_id as value" )->select();
        $this->success($res);
    }
}