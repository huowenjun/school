<?php

namespace Wap\Controller;

use Think\Controller;

class IndexController extends Controller
{
    private $appid = "wx229e8482e32502ed";//开发者ID(AppID)
    private $appsecret = "53ce0566be98d9ec6539b98655ac580d";//开发者密码(AppSecret)
    private $redirect_uri = "http://school.xinpingtai.com/index.php/Wap/Index/goto_bindPhone";//回调域名


    private function wx_userInfo($code)
    {
        $access_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->appid . "&secret=" . $this->appsecret . "&code=" . $code . "&grant_type=authorization_code";
        $access_token_array = reqURL($access_token_url, '', 'get', $is_json = 0);
        $userInfo = $this->userInfo($access_token_array['access_token'], $access_token_array['openid']);
        return $userInfo;
    }

    private function userInfo($access_token, $openid)
    {
        $userinfo_url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
        $userinfo_array = reqURL($userinfo_url, '', 'get', $is_json = 0);
        return $userinfo_array;
    }

    //获取access_token
    public function getToken()
    {
        //将access_token存入缓存
        if (S('access_token')) {//缓存存在
            return S('access_token');
        } else {//缓存已过期
            $appid = C('WECHAT')['appid'];
            $scrret = C('WECHAT')['appsecret'];
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $scrret;
            $tokenArr = reqURL($url, '', 'get', $is_json = 0);
            if ($tokenArr['errcode']) {
                return $tokenArr['errmsg'];
            } else {
                //设置access_token缓存
                S('access_token', $tokenArr['access_token'], 7000);
                return $tokenArr['access_token'];
            }
        }
    }

    //模板消息的发送
    public function sendTemplateMsg($arr)
    {
        $access_token = $this->getToken();
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $access_token;
        if($arr['templateId']){
            $template_id = $arr['templateId'];
        }else{
            $template_id = C('WECHAT')['templateId'];
        }
        $postArr = array(
            'touser' => $arr['open_id'],//接收者的open_id
            'template_id' => "$template_id",//模板ID
            'data' => array(
                'first' => array('value' => $arr['first'], 'color' => '#173177'),
                'keyword1' => array('value' => $arr['keyword1'], 'color' => '#173177'),
                'keyword2' => array('value' => $arr['keyword2'], 'color' => '#173177'),
                'keyword3' => array('value' => $arr['keyword3'], 'color' => '#173177'),
                'remark' => array('value' => $arr['remark'], 'color' => '#173177'),
            ),
        );
        $postJson = json_encode($postArr);
        $res = reqURL($url, $postJson, 'post', $is_json = 0);
        if ($res['errmsg'] == 'ok') {
            return 1;
        } else {
            return 0;
        }
    }


    //已弃用
    public function getCode()
    {
        if (isset($_GET["code"])) {
            $code = $_GET["code"];
            $userInfo = $this->wx_userInfo($code);
            $openid = $userInfo['openid'];//获取用户openID
            if ($openid) {
                $data = M('user', 'think_')->where(['open_id' => $openid])->find();
                if (!$data) {
                    $userStr = 'openid=' . $openid;//获取微信用户的信息，拼接到url
                    //跳转绑定
                    $url = "location:http://school.xinpingtai.com/index.php/Wap/WeFinancial/register?$userStr";
                    header($url);
                    exit;
                } else {
                    $Admin = D('Adminuser', 'Logic');
                    //设置session
                    $Admin->setLogin($data);
                    //跳转至上级页面
                    header('location:http://school.xinpingtai.com/index.php/Wap/WeFinancial/homePage');
                    exit;
                }
            } else {
                $url = "location:http://school.xinpingtai.com/index.php/Wap/WeFinancial/register";
                header($url);
                exit;
            }
        } else {
            $url = "location: https://open.weixin.qq.com/connect/oauth2/authorize?appid="
                . $this->appid . "&redirect_uri="
                . urlencode($this->redirect_uri)
                . "&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";
            header($url);
            exit;
        }
    }

    //获取用户信息接口
    public function goto_bindPhone()
    {
        $ref = $_GET['ref'];//推荐人id
        $source = $_GET['source'] ? $_GET['source'] : 1;//推荐人来源
        if ($ref) {//通过微信二维码分享过来
            $url = "location: https://open.weixin.qq.com/connect/oauth2/authorize?appid="
                . $this->appid . "&redirect_uri="
                . urlencode('http://school.xinpingtai.com/index.php/Wap/WeFinancial/register?source='.$source.'&ref=' . $ref)
                . "&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";
            header($url);
            exit;
        } else {
            //通过公众号菜单过来
            if (isset($_GET["code"])) {
                $code = $_GET["code"];
                $userInfo = $this->wx_userInfo($code);
                $openid = $userInfo['openid'];//获取用户openID
                if ($openid) {
                    $wx_user_info = M('user', 'think_')
                        ->field('user_id,open_id,type,username,name,source,type')
                        ->where(['open_id' => $openid])
                        ->find();
                    $Admin = D('Adminuser', 'Logic');
                    //设置session
                    $Admin->setLogin($wx_user_info);
                    if ($wx_user_info) {//用户使用公众号登录过
                        $url = "location:http://school.xinpingtai.com/index.php/Wap/WeFinancial/homePage";
                        header($url);
                        exit;
                    } else {//微信用户没有使用公众号登录过
                        $userStr = 'openid=' . $openid . '&nickname=' . $userInfo['nickname'] . '&sex=' . $userInfo['sex'] . '&headimgurl=' . $userInfo['headimgurl'] . '&unionid=' . $userInfo['unionid'];//获取微信用户的信息，拼接到url
                        //跳转绑定
                        $url = "location:http://school.xinpingtai.com/index.php/Wap/WeFinancial/register?$userStr";
                        header($url);
                        exit;
                    }

                } else {
                    $url = "location:http://school.xinpingtai.com/index.php/Wap/WeFinancial/register";
                    header($url);
                    exit;
                }
                //跳转绑定
                $url = "location:http://school.xinpingtai.com/index.php/Wap/WeFinancial/register?code=$code";
                header($url);
                exit;
            } else {
                $url = "location: https://open.weixin.qq.com/connect/oauth2/authorize?appid="
                    . $this->appid . "&redirect_uri="
                    . urlencode($this->redirect_uri)
                    . "&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect";
                header($url);
                exit;
            }
        }


    }

    //用户注册绑定接口 微信详情
    public function bindPhone()
    {
        $username = I('post.phone');
        $verifyCode = I('post.verifyCode');
        $data['phone'] = $username;
        if (!$username) {
            $this->error('手机号不能为空');
        }
        $data['province'] = I('post.province');
        $data['city'] = I('post.city');
        $data['county'] = I('post.county');
        $data['create_time'] = date('Y-m-d H:i:s');
        $data['ip'] = $_SERVER['REMOTE_ADDR'];

        $codeInfo = D('Code')->field('code,create_time')->where(['phone' => $username])->order('id  desc')->find();
        $Tdifference = time() - strtotime($codeInfo['create_time']);
        if ($Tdifference > 180) {
            $this->error("验证码无效请重新获取");
        }
        if ($verifyCode != $codeInfo['code']) {
            $this->error("验证码错误");
        }

        if (I('post.code')) {
            $wx_user = $this->wx_userInfo(I('post.code'));
            $data['open_id'] = $wx_user['openid'];
            $data['name'] = $wx_user['nickname'];
            $data['sex'] = $wx_user['sex'];
            $data['head_portrait'] = $wx_user['headimgurl'];
            $data['unionid'] = $wx_user['unionid'];
        }
        if (I('post.openid')) {//接收传过来的openid
            $data['open_id'] = I('post.openid');
            $data['sex'] = I('post.sex');
            $data['head_portrait'] = I('post.headimgurl');
            $data['unionid'] = I('post.unionid');
        }
        if ($data['open_id']) {
            $wx_user_info = M('user', 'think_')
                ->field('user_id,open_id,type,username')
                ->where(['open_id' => $data['open_id']])
                ->find();
            //微信用户已经绑定一个手机号,再次绑定其他手机号
            if ($wx_user_info && ($wx_user_info['username'] != $username)) {
                $this->error('同一个微信用户只能绑定一个手机号');
            }
        }

        //通过手机号获取用户详情
        $userInfo = M('user', 'think_')
            ->field('user_id,open_id,type,username,source')
            ->where(['username' => $username])
            ->find();
        if(!$userInfo['name']){
            $data['name'] = I('post.nickname');
        }
        if(!$userInfo['source']){
            $data['source'] = I('post.source');
        }
        if(!$userInfo['type']){
            $data['type'] = I('post.type',0);
        }
        if(!$userInfo['group_id']){
            $data['group_id'] = $data['type'];
        }
        if ($userInfo) {
            //save openID
            $data['last_time'] = date('Y-m-d H:i:s');
            $data['modif_time'] = date('Y-m-d H:i:s');
            $bool = M('user', 'think_')
                ->where(['username' => $username])
                ->save($data);
            if (!$bool) {
                $this->error('网络错误');
            }
        } else {
            if (I('post.ref')) {
                $data['ref_id'] = base64_decode(I('post.ref'));
            }
            $data['username'] = $username;
            $bool = M('user', 'think_')->add($data);
            $userInfo['user_id'] = M('user', 'think_')->getLastInsID();
            if (!$bool) {
                $this->error('网络错误');
            }
        }
        $Admin = D('Adminuser', 'Logic');
        //设置session
        $Admin->setLogin($userInfo);
        $this->success('绑定成功');

    }
}