<?php

namespace Wap\Controller;

use Think\Controller;

class SpFinancialController extends Controller
{
    public function homePage()
    {

        $this->display();
    }

    public function perCenter()
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

    /*
        * 根据code获取union_id
        */
    public function wx_login($code)
    {
        $config = C('WXSP');
        if ($code) {
            $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $config['appid'] . '&secret=' . $config['appsecret'] . '&js_code=' . $code . '&grant_type=authorization_code';
            $data = reqURL($url, '', 'get');
            if ($data['openid']) {
                return $data;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    public function session(){
        if(!I('post.session_key')){
            $this->error('请先登录');
        }
        $user_id = $_SESSION[I('post.session_key')];
        $this->success(['user_id' => $user_id]);
    }

    //登录
    public function login()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $userName = I('post.phone');//用户名
        $verifyCode = I('post.verifyCode');//验证码
        $code = I('post.code');//code
        if ($userName) {
            //验证码校验
            $codeInfo = D('Code')->field('code,create_time')->where(['phone' => $userName])->order('id  desc')->find();
            $Tdifference = time() - strtotime($codeInfo['create_time']);
            if ($Tdifference > 180) {
                $this->error("验证码无效请重新获取");
            }
            if ($verifyCode != $codeInfo['code']) {
                $this->error("验证码错误");
            }
            $info = M('user', 'think_')->where(['username' => $userName])->find();
            if(!$info){
                $this->error("手机号码有误");
            }
        } else if (!$code) {
            $this->error('非法请求！1001');
        } else {
            $data = $this->wx_login($code);
            if (!$data) {
                $this->error('非法请求！1002');
            } else {
                $info = M('user', 'think_')->where(['small_wxopenid' => $data['openid']])->find();
                if(!$info){
                    if ($data['unionid']) {
                        $this->error(['id' => $data['openid'], 'only' => $data['unionid'],'session_key'=>$data['session_key']]);//为防止模拟用户，修改字段为：id==小程序返回字段openid,only==小程序返回字段unionid，session_key==小程序返回字段session_key
                    } else {
                        $this->error(['id' => $data['openid'],'session_key'=>$data['session_key']]);
                    }
                }
            }
        }
        session($info['session_key'],$info['user_id']);
        $this->success(['user_id' => $info['user_id'],'session_key'=>$info['session_key']]);
    }

    //注册
    public function register()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $data['unionId'] = I('post.only');
        $small_wxopenid = I('post.id');
        if (!$data['unionId'] || !$small_wxopenid) {
            $this->error("参数错误");
        }
        $username = I('post.phone');
        $data['phone'] = I('post.phone');
        $verifyCode = I('post.verifyCode');
        $data['name'] = I('post.name');
        $data['sex'] = I('post.sex');
        $data['head_portrait'] = I('post.head_portrait');
        $data['province'] = I('post.province');
        $data['city'] = I('post.city');
        $data['county'] = I('post.county');
        $data['source'] = 2;
        $data['session_key']=I('post.session_key');
        $codeInfo = D('Code')->field('code,create_time')->where(['phone' => $username])->order('id  desc')->find();
        $Tdifference = time() - strtotime($codeInfo['create_time']);
        if ($Tdifference > 180) {
            $this->error("验证码无效请重新获取");
        }
        if ($verifyCode != $codeInfo['code']) {
            $this->error("验证码错误");
        }
        //1.根据手机号查询用户是否存在，若存在，则绑定openid，反之插入一条用户信息（username，openid，nickname，photo。。）
        $info = M('user', 'think_')->where(['username' => $username])->find();
        if ($info) {
            $bool = M('user', 'think_')->where(['username' => $username])->save(['small_wxopenid' => $small_wxopenid,'session_key'=>$data['session_key'], 'modif_time' => date('Y-m-d H:i:s')]);
            if(!$bool){
                $this->error("服务器异常");
            }
        } else {
            $data['username'] = $username;
            $data['small_wxopenid'] = $small_wxopenid;
            $bool = M('user', 'think_')->add($data);
            $info['user_id'] = M('user', 'think_')->getLastInsID();
            if(!$bool){
                $this->error("服务器异常");
            }
        }
        session($data['session_key'],$info['user_id']);
        $this->success(['user_id' => $info['user_id'],'session_key'=>$data['session_key']]);
    }

    /*
     * 获取用户信息,,,头像，手机号，用户类型
     * user_id
     * post
     *index.php/wap/SpFinancial/userInfo
     */
    public function userInfo()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $user_id = I('post.user_id');
        if (!$user_id) {
            $this->error('非法请求');
        }
        $data = M('user', 'think_')->field('head_portrait,phone,type')->where(['user_id'])->find();
        if (!$data) {
            $this->error('不存在该用户');
        } else {
            $type = C('USER_ROLE');
            $data['type'] = $type[$data['type']];
            $this->success($data);
        }
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
        $user_id = I('post.user_id');
        //2.没有user_id跳回登录页面
        if (!$user_id) {
            $this->error('登录后重试');
        }
        //3.根据user_id获取type，是不是特殊身份.
        //0会员 1学校管理员 2 系统用户 3 老师 4 家长  5省委 6市委 7县委 9第三方代理公司 10分销代理商 11区县代理商
        $userInfo = M('user','think_')->field('type,ref_id,source')->where(['user_id'=>$user_id])->find();
        if($userInfo['source']==0||$userInfo['source']==1){
            if ($userInfo['type'] == 9 || $userInfo['type'] == 10 || $userInfo['type'] == 11) {
                $ref_id = $user_id;
            } else {
                if ($userInfo['ref_id'] == 0) {
                    $ref_id = '10847';//信平台user_id
                } else {
                    $ref_id = $userInfo['ref_id']; //显示上级的id
                }
            }
        }elseif ($userInfo['source']==2){
            $ref_id = $user_id;
        }
//        http://school.xinpingtai.com/index.php/Wap/Register/index?ref=MTA5MzY=
        $content = "http://school.xinpingtai.com/index.php/Wap/Index/index?ref=" . base64_encode($ref_id);
        $fileName = "QRCode" . $ref_id . ".png";
        $data['img'] = getQrCode($content, $fileName);
        $data['url'] = $content;
        $data['wx'] = $this->weixinJS();
        $this->success($data);
    }

    /*
     * 邀请人数据接口
     * post
     * user_id
     * index.php/wap/SpFinancial/userAccount
     */
    public function userAccount()
    {
        $user_id = I('post.user_id');
        if (!$user_id) {
            $this->error('登录后重试');
        }
        $AdminUser = D('AdminUser');
        //个人信息
        $result['userInfo'] = $AdminUser->field('user_id,username,type,head_portrait,phone,name')->where(['user_id' => $user_id])->find();
        $type = C('USER_ROLE');
        $result['userInfo']['type'] = $type[$result['userInfo']['type']];
        $type = array(10,11,0);
        foreach ($type as $key=>$value){
            $result['num'][$key]['value'] = $AdminUser->where(['ref_id' => $result['userInfo']['user_id'], 'type' => $value])->count();
            if($value==10){
                $result['num'][$key]['name'] = '分销代理商(人数)';
            }elseif ($value==11){
                $result['num'][$key]['name'] = '区县代理商(人数)';
            }elseif ($value==0){
                $result['num'][$key]['name'] = '会员（人数）';
            }
        }
        $address = $AdminUser->field('count(*) as num,region_name')->join("sch_area_region on think_user.province = sch_area_region.region_code")->where(['ref_id'=>$result['userInfo']['user_id']])->group('province')->select();
        $data = [];
        foreach ($address as $key=>$value){
            $data['num'][] = $value['num'];
            $data['province'][] = $value['region_name'];
        }
        $result['barGraph'] = $data;
        $this->success($result);
    }
}