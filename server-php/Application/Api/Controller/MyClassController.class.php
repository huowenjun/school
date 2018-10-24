<?php
/*
*我的班级
*by ruiping date 20161201
*/
namespace Api\Controller;
use Api\Controller\BaseController;
use Think\Model;
class MyClassController extends BaseController {
    /*
    *数据查询
    *@param
    */
    public function query(){
        if(IS_POST===false){
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/MyClass/query'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
    	//校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag){
            $this->error('接口访问不合法！');
        }
        $Teacher = D('Teacher');
        $teacherCourse = D('TeacherCourse');
        $Class = D('Class');
        $user_id = $this->getUserId();
        //查询老师ID
        $tId = "";
        $teacherInfo = $Teacher->where(array('u_id'=>$user_id))->find();
        if (empty($teacherInfo)) {
            $this->error('教师档案不存在！');
        }else{
            $tId = $teacherInfo['t_id'];
        }
        //获取相关联的所有c_id
        $tmpCid = $teacherCourse->union(" SELECT c_id FROM sch_class where t_id = '{$tId}' ")
                                ->where(" t_id = '{$tId}' ")->getField('c_id',true);
        //过滤不存在的班级
        $allClass =  $Class->getField("c_id",true);
        $newRet = array_intersect($allClass,$tmpCid);
        sort($newRet);
        //获取班级的详细信息

        foreach($newRet as $key=>$value){
            $info = $classInfo = M("class")->alias("a")
                                            ->field('a.c_id, a.s_id, a.a_id, a.g_id, 
                                                a.name, a.t_id, a.memo, b.name as g_name ')
                                            ->join(" __GRADE__ b ON  a.g_id = b.g_id ")
                                            ->where(" a.c_id = '{$value}' ")
                                            ->find();
            if(!empty($info)){
                //获取班主任姓名
                $info['t_name'] = $Teacher->where(" t_id = '{$info['t_id']}' ")->getField("name");
                //获取班级学生总数
                $info['stu_count'] = M("student")->where(" c_id = '{$value}' ")->count();
            }
            $classArr[] = $info;
        }
        $this->success($classArr);

    }
    public function test()
    {
        var_dump($this->getModule().date('Ymd',time()).$this->getSecurityKey());

    }
}