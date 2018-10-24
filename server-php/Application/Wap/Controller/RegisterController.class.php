<?php
/*
*   注册
*by yumeng date 20170413
*/

namespace Wap\Controller;

use Think\Controller;

class RegisterController extends Controller
{
    //注册静态页
    public function index()
    {
        $this->display();
    }

    //注册
    public function register()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $phone = I('post.phone');//账号
        $pwd = I('post.pwd');//密码
        $send_code = I('post.code');//验证码
        $ref_id = I('post.ref');//推荐人id

        $User = D('AdminUser');//用户表
        $Code = D('Code');//保存验证码表
        if (empty($phone) || empty($send_code) || empty($pwd)) {
            $this->error('参数错误!');
        }
        $where1['phone'] = $phone;
        $where1['username'] = $phone;
        $where1['_logic'] = 'or';
        $user_id = $User->where($where1)->getField('user_id');
        if ($user_id) {
            $this->error("该手机号已注册");
        }
        //获取 验证码 判断是否相等
        $where['phone'] = $phone;//手机号
        $time = time();//获取当前时间
        $codeInfo = $Code->field('code,create_time')->where($where)->order('id  desc')->find();
        $Tdifference = $time - strtotime($codeInfo['create_time']);//获取时间差  验证码 有效时间为3分钟
        if ($Tdifference > 180) {
            $this->error("验证码无效请重新获取");
        }
        if ($send_code != $codeInfo['code']) {
            $this->error("验证码错误");
        }
        $data['username'] = $phone;//账号
        $data['phone'] = $phone;//手机号
        $data['type'] = 0;//类型
        $data['password'] = md5($pwd);//密码
        $data['code'] = $send_code;//验证码
        $data['create_time'] = date('Y-m-d H:i:s');
        if ($ref_id) {//推荐人处理
            $ref_id = base64_decode($ref_id);
            $check_ref = $User->where("user_id = '{$ref_id}'")->find();
            if ($check_ref) {
                $data['ref_id'] = $check_ref['user_id'];
                if ($check_ref['type'] == 9) {//第三方代理公司邀请的用户
                    $data['company_id'] = $check_ref['user_id'];
                } else {//代理商邀请的用户
                    $data['company_id'] = $check_ref['company_id'];
                }

            }
        }
        $result = $User->add($data);
        if ($result) {
            //给上级发布通知消息
            if ($check_ref) {
                $title = '用户推荐注册成功';
                $content = '您邀请的用户' . $phone . '注册成功';
                $msg_data['m_type'] = 7;
                $msg_data['title'] = $title;
                $msg_data['content'] = $content;
                $msg_data['create_time'] = date('Y-m-d H:i:s');
                $msg_data['to_user'] = $ref_id;
                $msg_data['user_id'] = $result;
                $msg_data['is_effect'] = 1;

                M('Mymessage')->add($msg_data);
                $pushArr = array(
                    'fsdx' => '0,1',
                    'u_id' => $ref_id,
                    'ticker' => $title,
                    'title' => $title,
                    'text' => $content,
                    'after_open' => 'go_custom',
                    'activity' => 'register',
                    'id' => 666,
                );
                sendAppMsg($pushArr);
                if ($check_ref['open_id']) {
                    $arr = [
                        'open_id' => $check_ref['open_id'],
                        'first' => '您邀请的用户' . $phone . '注册成功',
                        'keyword1' => $phone,
                        'keyword2' => $phone,
                        'remark' => '恭喜你多了一位小伙伴',
                    ];
                    $index = new \Wap\Controller\IndexController();
                    $res = $index->sendTemplateMsg($arr);//手机微信模板推送
                }


            }
            easemob_add_user($result);//友盟用户添加
            $this->success('注册成功!');
        } else {
            $this->error('注册失败!');
        }
    }
    //下发短信
    public function getCode()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $User = D('AdminUser');//用户表
        $Code = D('Code');//保存验证码表
        $phone = I('post.phone');//手机号
        $send_code = random(6, 1);
        $where1['phone'] = $phone;
        $where1['username'] = $phone;
        $where1['_logic'] = 'or';
        $res = $User->where($where1)->find();
        if (!empty($res)) {
            $this->error("该账号已存在!");
        }
        if (empty($phone)) {
            $this->error('手机号码不能为空!');
        }
        $yz = preg_match('/^1[3578]\d{9}$/', $phone) ? true : false;
        if ($yz === false) {
            $this->error("手机号格式错误");
        }
        //防用户恶意请求
        if (empty($send_code)) {
            $this->error('验证码生成失败,请重新请求!');
        }
        $where['phone'] = $phone;
        $res1 = $Code->where($where)->find();
        $time = time();//当前时间
        $Tdifference = $time - strtotime($res1['create_time']);//获取时间 过后账号可再次发送验证码  86400 等于24小时
        if ($Tdifference < 86400 && $res1 != '' && $res1['count'] >= 5) {
            $this->error("短信下发量超过每天限制额度");
        }
        //
        if ($Tdifference > 86400 && $res1 != '') {
            $data1['count'] = 0;
            $res1 = $Code->where($where)->save($data1);
        }
        $content = "您的验证码是：" . $send_code . "。请不要把验证码泄露给其他人。";
        $sendSmsInfo = sendSms($phone, $content);
        if ($sendSmsInfo === true) {
            if ($res1) {
                $data['phone'] = $phone;//账号的 ID
                $data['count'] = $res1['count'] + 1;
                $data['code'] = $send_code;//验证码
                $data['create_time'] = date('Y-m-d H:i:s');
                $Code->where($where)->save($data);
            } else {
                $data['phone'] = $phone;//账号的 ID
                $data['code'] = $send_code;//验证码
                $data['create_time'] = date('Y-m-d H:i:s');
                $data['count'] = 1;//验证码
                $Code->add($data);
            }
            $this->success("发送成功");
        } else {
            $this->error("发送失败");
        }
    }

    //获取我的邀请人
    public function getReferrer()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $user_id = I('post.user_id');
        if (!$user_id) {
            $this->error('请登录后重试');
        }
        $page = I('post.page', 1);
        $pagesize = 20;
        $list = M('User', 'think_')
            ->field('user_id,username,name,head_portrait,type,create_time')
            ->where("ref_id = '{$user_id}'")
            ->order('user_id desc')
            ->page($page, $pagesize)
            ->select();
        $count = M('User', 'think_')
            ->where("ref_id = '{$user_id}'")
            ->count();

        foreach ($list as $key => $value) {
            $list[$key]['status'] = '已注册';
        }

        $data = getPageList($list, $count, $page, $pagesize);
        $this->success($data);
    }


}