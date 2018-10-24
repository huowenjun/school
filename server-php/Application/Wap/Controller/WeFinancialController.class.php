<?php

namespace Wap\Controller;

use Think\Controller;

class WeFinancialController extends Controller
{
    public function homePage()
    {

        $this->display();
    }

    public function perCenter()
    {

        $this->display();
    }

    public function memberShip()
    {

        $this->display();
    }

    public function inviteCode()
    {

        $this->display();
    }

    public function capitalDetails()
    {

        $this->display();
    }

    public function bind()
    {
        $this->display();
    }

    public function login()
    {
        $this->display();
    }

    public function register()
    {
        $this->display();
    }


    //登录
    public function login_handle()
    {
        $userName = I('get.username');
        $verifyCode = I('post.verifyCode');//验证码
        if (empty($userName) || empty($verifyCode)) {
            $this->error('用户名或密码未填写！');
        }
        $codeInfo = D('Code')->field('code,create_time')->where(['phone' => $userName])->order('id  desc')->find();
        $Tdifference = time() - strtotime($codeInfo['create_time']);
        if ($Tdifference > 180) {
            $this->error("验证码无效请重新获取");
        }
        if ($verifyCode != $codeInfo['code']) {
            $this->error("验证码错误");
        }
        // 查询用户
        $userInfo = M('user', 'think_')->where(['username' => $userName])->find();
        if (!$userInfo) {
            $this->error('登录失败');
        }
        session('admin_user', [
            'user_id' => $userInfo['user_id'] ? $userInfo['user_id'] : '',
            'username' => $userName,
            'name' => $userInfo['name'],
            'source' => $userInfo['source'],
            'type' => $userInfo['type']
        ]);
        $this->success('登录成功');

    }

    //微信接口
    public function weixinJS()
    {
        $appid = "wx229e8482e32502ed";//开发者ID(AppID)
        $appsecret = "53ce0566be98d9ec6539b98655ac580d";//开发者密码(AppSecret)
        vendor('Sdk.jssdk');
        $jssdk = new \JSSDK($appid, $appsecret);
        $signPackage = $jssdk->GetSignPackage();

        return $signPackage;
    }

    //获取邀请二维码
    public function getRefferCode()
    {
        //1.session去user_id
        $user_id = I('get.user_id');
        //2.没有user_id跳回登录页面
        if (!$user_id) {
            $this->error('参数错误');
        }
        $user_info = M('user', 'think_')->field('user_id,source,type,head_portrait,username,name')->where("user_id = '{$user_id}'")->find();
        //3.根据user_id获取type，是不是特殊身份.
        //0会员 1学校管理员 2 系统用户 3 老师 4 家长  5省委 6市委 7县委 9第三方代理公司 10分销代理商 11区县代理商
        //source 0自助注册  1老规则 2新规则
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
            }
        } elseif ($user_info['source'] == 2) {
            $ref_id = $user_info['user_id'];
            $source = 2;//新规则标识
        }
        if (!$user_info['head_portrait']) {
            $user_info['head_portrait'] = 'http://school.xinpingtai.com/Public/logo.png';
        }
        if (!$user_info['name']) {
            $user_info['name'] = $user_info['username'];
        }

//        http://school.xinpingtai.com/index.php/Wap/Register/index?ref=MTA5MzY=
        //获取邀请二维码内容
        $content = "http://school.xinpingtai.com/index.php/Wap/Index/goto_bindPhone?source={$source}&ref=" . base64_encode($ref_id);
        $fileName = "QRCode" . $ref_id . ".png";
        $data['img'] = getQrCode($content, $fileName);
        $data['url'] = $content;
        $data['head_portrait'] = $user_info['head_portrait'];
        $data['name'] = $user_info['name'];
        $data['wx'] = $this->weixinJS();
        $this->success($data);
    }

    //返回给前端对应user_id的数据
    public function retInfo()
    {
        //1.session去找user_id
        $sessionInfo = $_SESSION['admin_user'];
        //2.没有user_id跳回登录页面
        if (!$sessionInfo['user_id']) {
            $this->error('登录后重试');
        }
        //提取出来user_id
        $user_id = $sessionInfo['user_id'];
        //初始化user表
        $user = M('user', 'think_');
        $where['ref_id'] = $user_id;
        //从user表里面查找该user_id
        $user_info = $user->where($where)->select();
        if (empty($user_info)) {
            $this->error('没有该数据');
        } else {
            $this->success($user_info);
        }

    }


    /*
     * 邀请人数据接口
     * post
     * user_id
     * index.php/wap/WeFinancial/userAccount
     */
    public function userAccount()
    {
//        1.session去user_id
        $sessionInfo = $_SESSION['admin_user'];
        //2.没有user_id跳回登录页面
        if (!$sessionInfo['user_id']) {
            $this->error('登录后重试');
        }
        $user_id = $sessionInfo['user_id'];
        $AdminUser = D('AdminUser');
        //个人信息
        $result['userInfo'] = $AdminUser->field('user_id,username,type,head_portrait,phone,name,province,city,source')->where(['user_id' => $user_id])->find();
        if($result['userInfo']['source']==1||$result['userInfo']['source']==0){
            $result['userInfo']['classify'] = 1;//标识，区分展示页面的形式
        }else{
            $result['userInfo']['classify'] = $result['userInfo']['type'];//标识，区分展示页面的形式
        }
        $regionList = D('area_region')->where(['region_code'=>['in',[$result['userInfo']['province'],$result['userInfo']['city']]]])->select();
        $result['userInfo']['province'] = $regionList[0]['region_name'];
        $result['userInfo']['city'] = $regionList[1]['region_name'];
        if (!$result['userInfo']['head_portrait']) {
            $result['userInfo']['head_portrait'] = 'http://school.xinpingtai.com/Public/logo.png';
        }
        if($result['userInfo']['source']==1||$result['userInfo']['source']==0){
            $type = C('USER_ROLE');
            if($result['userInfo']['type']==0){
                $result['userInfo']['type'] = '会员';
            }else{
                $result['userInfo']['type'] = $type[$result['userInfo']['type']];
            }

            $type = array(10, 11, 0);
            foreach ($type as $key => $value) {
                $result['num'][$key]['value'] = $AdminUser->where(['ref_id' => $result['userInfo']['user_id'], 'type' => $value,'_string'=>'source=0 or source=1'])->count();
                if ($value == 10) {
                    $result['num'][$key]['name'] = '分销代理商(人数)';
                } elseif ($value == 11) {
                    $result['num'][$key]['name'] = '区县代理商(人数)';
                } elseif ($value == 0) {
                    $result['num'][$key]['name'] = '会员（人数）';
                }
            }
            $result['barGraph'] = $this->addressNum($user_id,$result['userInfo']['source'],implode(',',$type));
        }elseif ($result['userInfo']['source']==2){
            if($result['userInfo']['type']!=0){
                $type = array(12,0);
                foreach ($type as $key => $value) {
                    $result['num'][$key]['value'] = $AdminUser->where(['ref_id' => $result['userInfo']['user_id'], 'type' => $value,'source'=>$result['userInfo']['source']])->count();
                 if ($value == 12) {
                        $result['num'][$key]['name'] = '会员(人)';
                        $result['num'][$key]['type'] = 12;
                    }elseif ($value == 0) {
                        $result['num'][$key]['name'] = '储户(人)';
                        $result['num'][$key]['type'] = 0;
                    }
                }
                $result['barGraph'] = $this->addressNum($user_id,$result['userInfo']['source'],implode(',',$type));
            }
            $type = C('USER_ROLE');
            $result['userInfo']['type'] = $type[$result['userInfo']['type']];
        }

        $this->success($result);
    }

    /*
     * 返回地狱所对应的数据
     */
    protected function addressNum($user_id,$source,$num){
        $AdminUser = D('AdminUser');
        $address = $AdminUser->field('count(*) as num,region_name')->join("sch_area_region on think_user.province = sch_area_region.region_code")->where(['ref_id' => $user_id,'_string'=>'source=0 or source=1','type'=>['in',"$num"]])->group('province')->select();
        $data = [];
        foreach ($address as $key => $value) {
            $data['num'][] = $value['num'];
            $data['province'][] = $value['region_name'];
        }
        return $data;
    }

    /*
     * 微信公众号用户换头像
     * user_id 用户id
     * head_portrait 用户头像路径
     * post
     * index.php/Wap/WeFinancial/editUserHeadPortrait
     */
    public function editUserHeadPortrait()
    {
        $user_id = I('post.user_id');
        if (!$user_id) {
            $this->error('非法请求');
        }
        $data['head_portrait'] = I('post.head_portrait');
        if (!$data['head_portrait']) {
            $this->error('请添加头像');
        }
        $bool = M('user', 'think_')->where(['user_id' => $user_id])->save($data);
        if (!$bool) {
            $this->error('网络异常,请稍后重试');
        }
        $this->success(['head_portrait' => $data['head_portrait']]);
    }

    /*
     * 显示用户邀请的储户或者会员的详细信息
     * 参数：
     * type 类型
     * get 请求
     * time  (0=>最近一周,1=>最近半个月,2=>最近一个月,3=>最近三个月,4=>最近半年，5=>最近一年)
     * index.php/wap/WeFinancial/showUserInvitationType
     */
    public function showUserInvitationType()
    {
        $sessionInfo = $_SESSION['admin_user'];
        //2.没有user_id跳回登录页面
        if (!$sessionInfo['user_id']) {
            $this->error('登录后重试');
        }
        $ref_id = $sessionInfo['user_id'];
        $type = I('get.type',12);
        $create_time = I('get.time',0);
        $AdminUser = D('AdminUser');
        if($create_time==0){//最近一周
           $date = date("Y-m-d H:i:s",strtotime("-1 week"));
        }elseif ($create_time==1){//最近半个月
            $date = date("Y-m-d H:i:s",strtotime("-2 week"));
        }elseif ($create_time==2){//最近一个月
            $date = date("Y-m-d H:i:s",strtotime("-1 month"));
        }elseif ($create_time==3){//最近三个月
            $date = date("Y-m-d H:i:s",strtotime("-3 month"));
        }elseif ($create_time==4){//最近半年
            $date = date("Y-m-d H:i:s",strtotime("-6 month"));
        }elseif ($create_time==5){//最近一年
            $date = date("Y-m-d H:i:s",strtotime("-1 year"));
        }
        $userInvitationNumber = $AdminUser->field('head_portrait,name,create_time,type,phone,source')->where(['ref_id'=>$ref_id,'type'=>$type,'create_time'=>['gt',$date]])->select();
        $type = C('USER_ROLE');
        foreach ($userInvitationNumber as &$value)
        {

            if($value['source']==0||$value['source']==1){
                if($value['type']==0){
                    $value['type']= '会员';
                }
            }else if ($value['source']==2){
                if($value['type']==0){
                    $value['type']= '储户';
                }
            }else{
                $value['type'] = $type[$value['type']];
            }
            $value['phone'] = substr($value['phone'],0,3).'****'.substr($value['phone'],-4);
            $value['create_time'] = substr($value['create_time'],0,10);
            if(!$value['name']){
                $value['name'] = '匿名';
            }

        }
        $this->success($userInvitationNumber);
    }

    /*
     * 图片上传审核
     * post请求
     * apply_images 审核的图片路径
     */
    public function fileUploadAuditing()
    {
        $sessionInfo = $_SESSION['admin_user'];
        //2.没有user_id跳回登录页面
        if (!$sessionInfo['user_id']) {
            $this->error('登录后重试');
        }
        $data['user_id'] = $sessionInfo['user_id'];
        $backLogD = D('backlog')->where(['user_id'=>$data['user_id']])->find();
        if($_POST){
            if(!I('post.apply_images')){
            $this->error('请先上传图片');
            }
            $data['apply_images'] = I('post.apply_images');
            $data['create_time'] = date('Y-m-d H:i:s');
            $data['type'] = 1;
            if($backLogD['apply_images']){//有图片，修改
                $data['reply'] = '';
                $data['status'] = 0;
                $bool = D('backlog')->where(['user_id'=>$data['user_id']])->save($data);
            }else{
                $bool = D('backlog')->add($data);
            }
            if($bool){
               $this->success(['reply'=>'你已成功提交申请,请耐心等待','apply_images'=>$data['apply_images']]);
            }else{
                $this->error('添加失败');
            }
        }else{//get请求
            if($backLogD){
                if($backLogD['status']==0){
                    $res['status'] = 0;
                    $res['reply'] = '你提交的申请正在审核中，请耐心等待';
                }elseif ($backLogD['status']==2){
                    $res['status'] = 2;
                    $res['reply'] = $backLogD['reply'];//审核失败原因：您提交的图片不清晰！ 您提交的图片中银行存款金额不足2000元！您提交的图片中身份核实不统一；
                }
                $res['apply_images'] = $backLogD['apply_images'];
            }
            $this->success($res);
        }







    }

}