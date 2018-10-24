<?php

namespace Api\Controller;

use Api\Controller\BaseController;
use Think\Controller;


class UserInfoController extends Controller
{
    /**
     *  修改用户注册后的信息
     *  用户更改信息：
     * 头像，name，sex，email，
     *
     * 非家长老师角色登录后返回信息：
     * user_id,username,name,type,sex,phone,email,
     * 若为代理商则返回代理商代理区域信息
     * select user_id,name,sex,email,type,head_portrait from think_user where username='15210217123'
     * 接值参数：
     *      user_id
     *      name
     *      sex
     *      email
     *      head_portrait
     */
    public function updateUserInfo()
    {
        if (IS_POST === false) {//验证post数据
            $this->error('the request method is not post');
        }
        //验证token
        $tokenArr = array(
            $username=$_SESSION['admin_user']['username'],
            $user_id=$_SESSION['admin_user']['user_id'],
            $_SESSION['admin_user']['time']
        );
        $token = encrypt_ase(json_encode($tokenArr));
        if($token!=I('post.token')){
            $this->error('非法请求');
        }
        $data['user_id'] = I('post.user_id', 0, 'int');//用户id
        if (!$data['user_id']) {
            $this->error('参数错误');
        }
        $name = I('post.name', '0', 'string');
        $sex = I('post.sex', '', 'string');
        $email = I('post.email', '0', 'string');
        if ($name) {
            $data['name'] = filter_Emoji($name);//用户名
        }
        if ($sex != NULL) {
            $data['sex'] = $sex; //性别
        }
        if ($email) {
            $data['email'] = $email;//邮箱
        }
        $UserD = M('user', 'think_');//用户表
        foreach ($_FILES as $key => $value) {//重组文件数组
            $photo[] = $value;
        }
        $photo[0]['name'] = $photo[0]['name'] ? $photo[0]['name'] : 0;
        if ($photo[0]['name']) {
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 2097150;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath = './Public/'; // 设置附件上传根目录
            $upload->savePath = '/userPhoto/'; // 设置附件上传（子）目录
            $upload->stream = true;
            // 上传单个文件
            $info = $upload->uploadOne($photo[0]);
            if (!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            } else {// 上传成功 获取上传文件信息
                $data['head_portrait'] = '/Public' . $info['savepath'] . $info['savename'];
            }
        }
        $data['modif_time'] = date('Y-m-d H:i:s');
        //修改数据，入库
        $bool = $UserD->where(['user_id' => $data['user_id']])->save($data);
        if ($bool) {
            $arr = $UserD->field('user_id,name,sex,email,type,head_portrait')->where(['user_id' => $data['user_id']])->find();
            $this->success($arr);
        } else {
            $this->error('修改失败');
        }
    }

//'RELATION' => array(1 => '爸爸', 2 => '妈妈', 3 => '爷爷', 4 => '奶奶', 5 => '外公', 6 => '外婆',7=>'哥哥',8=>'姐姐',9=>'叔叔',10=>'舅舅',11=>'阿姨',12=>'婶婶', 0 => '其它'),
    /*
     * post 请求
     *
     */
    public function updateStudentInfo()
    {
        if (IS_POST === false) {//验证post数据
            $this->error('the request method is not post');
        }
        if($_FILES){
            $upload_info = upload_file();
            if ($upload_info['status'] == 0) {
                $this->error($upload_info['info']);
            }
            $data['photo'] = $upload_info['info'];//头像路径
        }
        $stu_id = I('post.stu_id');//学生id
        $user_id = I('post.user_id');//登陆者id
        $relation = I('post.relation');//修改关系
        if(I('post.stu_name')){
            $data['stu_name'] = I('post.stu_name');//学生姓名
        }
        if(I('post.sex')!=""){
            $data['sex'] = I('post.sex');//性别 0 女 1 男
        }
        if(I('post.stu_phone')){
            $data['stu_phone'] = I('post.stu_phone');//手机号码
        }
        $data['modif_time'] = date('Y-m-d H:i:s');//更新时间
        if($stu_id){
            $bool1 = D('student')->where(['stu_id'=>$stu_id])->save($data);
        }
        if($relation!="")
        {
           $bool2 = D('student_group_access')->where(['stu_id'=>$stu_id,'user_id'=>$user_id])->save(['relation'=>$relation]);
        }
        if(!$data['photo']){
            $data['photo']="";
        }
        if($bool1 || $bool2){
            $this->success(['photo'=>$data['photo']]);
        }else{
            $this->error('网络异常，稍后重试');
        }

    }

    //返回信息
    public function  getRefferCode()
    {
        //1.session去user_id
        $user_id = I('post.user_id');
        //2.没有user_id跳回登录页面
        if (!$user_id) {
            $this->error('参数错误');
        }
        $user_info = M('user', 'think_')->field('user_id,source,type,head_portrait,username,name')->where("user_id = '{$user_id}'")->find();

        //3.根据user_id获取type，是不是特殊身份.
        //0会员 1学校管理员 2 系统用户 3 老师 4 家长  5省委 6市委 7县委 9第三方代理公司 10分销代理商 11区县代理商
        //source 0自助注册  1老规则 2新规则
        $data['type']=C('USER_ROLE')[$user_info['type']];
        if ($user_info['source'] == 1 || $user_info['source'] == 0) {
            $source = 1;//第三方代理标识
            if ($user_info['type'] == 9 || $user_info['type'] == 10 || $user_info['type'] == 11) {
                $ref_id = $user_info['user_id'];
            } else {
                $userInfo = M('user', 'think_')->field('ref_id')->where(['user_id' => $user_info['user_id']])->find();
                if ($userInfo['ref_id'] == 0) {
                    $ref_id = '10847';//信平台user_id
                } else {
                    $ref_id = $userInfo['ref_id']; //显示上级的id
                }
                $data['type']= '会员';
            }
        } elseif ($user_info['source'] == 2) {
            $ref_id = $user_info['user_id'];
            $source = 2;//新规则标识
            if($user_info['type']==0){
                $data['type']= '储户';
            }
        }
        $data['ref_id']=$ref_id;
        $data['source']=$user_info['source'];
        $this->success($data);
    }


}