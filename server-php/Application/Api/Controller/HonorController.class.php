<?php
/*
*荣誉
*
*/
namespace Api\Controller;
use Api\Controller\BaseController;
use Think\Model;
class HonorController extends BaseController {
    /*
    *数据查询
    *@param
    */
    public function query(){
        if(IS_POST===false){
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag){
            $this->error('接口访问不合法！');
        }
        $pagesize = $this->PAGESIZE;
        $p = I('post.page',1);
        $orderby = "rw_id desc";
        $type = I('post.reward_type'); //1 班级奖励, 2 年级奖励, 3 校级奖励, 4 市级奖励, 5 省级奖励, 6 国家奖励
        if (!empty($type)) {
            $where['reward_type'] = $type;
        }
        $Reward = D('Reward');
        if ($this->getUserType()==4) {//家长
            $stu_id = I('post.stu_id');
            if (!empty($stu_id)) {
                $where['stu_id'] = $stu_id;
            }else{
                //查询该家长的所有孩子
                $studentParent = D('StudentParent')->field('sp_id')->where(array('u_id'=>$this->getUserId()))->find();
                $stuId = D('StudentGroupAccess')->where(array('sp_id'=>$studentParent['sp_id']))->getField('stu_id',true);
                if ($stuId){
                    $where['stu_id'] = array('IN',$stuId);
                }else{
                    $this->error('该账号下没有孩子!');
                }
            }
        }elseif ($this->getUserType()==3) { //老师
            $c_id = I('post.c_id');
            if (!empty($c_id)) {
                $where['c_id'] = $c_id;
            }else{
                //获取教师t_id
                $t_id = M("Teacher")->where(array('u_id'=>$this->getUserId()))->getField("t_id");
                $classInfo  = $this->getTeacherClass($t_id);
                $cIds = $classInfo['c_id'];
                if($cIds){
                    $where['c_id'] = array("IN",$cIds);
                }else{
                    $this->error('该老师没有授课的班级!');
                }
            }
        }else{
            $this->error('用户类型不正确！');
        }

        $rewardList = $Reward->queryListApp('rw_id,g_id,c_id,stu_id,user_id,reward_type,reward_details,create_time,modify_time',$where,$orderby,$p,$pagesize);
        $List = $rewardList['list'];
        $Count = $rewardList['count'];
        $arr = array();
        $page_size = ceil($Count/$pagesize);
        $arr['total_page'] = $page_size;
        $arr['total_count'] = $Count;
        $arr['page'] = $p;
        $arr['content'] = $List;
        $this->success($arr);
    }

    //获取荣誉详情
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
        $info = M('Reward')->field('rw_id,g_id,c_id,stu_id,user_id,reward_type,reward_details,create_time,modify_time')->where(array('rw_id'=>$id))->find();
        $class_info = getClassInfo($info['c_id']);
        $info['c_name'] = $class_info['c_name'];
        $info['g_name'] = $class_info['g_name'];
        $info['reward_type'] = C('REWARD_TYPE')[$info['reward_type']];
        $info['stu_name'] = M('Student')->where(array('stu_id'=>$info['stu_id']))->getField('stu_name');
        $info['username'] = M('user','think_')->where(array('user_id'=>$info['user_id']))->getField('username');
        if(empty($info)){
            $this->error('参数错误');
        }
        $this->success($info);
    }

    public function edit(){
        if(IS_POST===false){
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag){
            $this->error('接口访问不合法！');
        }
        $userType = $this->getUserType();
        if ($userType != 3){
            $this->error('对不起,您没有操作权限!');
        }else{
            $Reward = D('Reward');
            $rw_id = I('post.rw_id');
            $stu_id = I('post.stu_id');
            $data['stu_id'] = $stu_id;
            $data['s_id'] = I('post.s_id');
            $data['a_id'] = I('post.a_id');
            $data['g_id'] = I('post.g_id');
            $data['c_id'] = I('post.c_id');
            $data['reward_type'] = I('post.reward_type');//奖励类型
            $data['reward_details'] = I('post.reward_details');//奖励详情
            $data['user_id'] = $this->getUserId();//发布人
            $data['modify_time'] = date('Y-m-d H:i:s');//编辑时间
            if(!$Reward->create($data)){
                $this->error($Reward->getError());
            }else{
                if ($rw_id>0){
                    //编辑评语
                    $where['rw_id'] = $rw_id;
                    $data['rw_id'] = $rw_id;
                    $ret = $Reward->where($where)->save($data);
                }else{
                    //添加评语
                    $data['create_time'] = date('Y-m-d H:i:s');//创建时间
                    $ret = $Reward->add($data);
                }
                if ($ret){
                    $content = ($rw_id > 0 ? '编辑' : '新建') . '成功';

                    //app消息推送 By:ZhangYumeng 2018.01.11
                    //获取学生详情
                    $student = M('Student')
                        ->field('stu_id,s_id,a_id,g_id,c_id,stu_no,stu_name,imei_id,sex,stu_phone')
                        ->where(array('stu_id'=>$data['stu_id']))
                        ->find();
                    if ($student) {
                        //获取家长信息
                        $parentId = M('StudentGroupAccess')
                            ->alias('a')
                            ->join('__STUDENT_PARENT__ b on a.sp_id = b.sp_id')
                            ->where("a.stu_id = '{$student['stu_id']}'")
                            ->getField('u_id', true);
                    }
                    $stu_name = $student['stu_name'];
                    $rname = C('REWARD_TYPE')[$data['reward_type']];

                    $pushArr = array(
                        'fsdx' => '0,1',
                        'u_id' => $parentId,
                        'ticker' => '奖励消息',
                        'title' => $rname."-".$stu_name,
                        'text' => $rname."-".$stu_name,
                        'after_open' => 'go_custom',
                        'activity' => 'honor',
                        'id' => $ret,
                    );
                    $this->sendAppMsg($pushArr);



                    $this->success($content);
                }
            }
        }
    }

    //删除评语
    public function del(){
        if(IS_POST===false){
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag){
            $this->error('接口访问不合法！');
        }
        $Reward = D('Reward');
        $user_id = $this->getUserId();
        $id = I('post.rw_id');
        $where['rw_id'] = $id;
        $auth = $Reward->where($where)->find();
        if(!empty($auth) && $auth['user_id']!=$user_id){
            $this->error('您没有操作权限!');
        }
        $ret = $Reward->where($where)->delete();
        if ($ret){
            $content = '删除成功!';
            $this->success($content);
        }
    }
}