<?php

namespace Api\Controller;

use Think\Controller;
use Think\Model;

class LoginController extends Controller
{

    public function index()
    {
        $username = I('post.username');
        $passWord = I('post.password');
        $type = I('post.type'); //3 老师 4 家长
        $system_model = I('post.system_model', 0);//1-android 0-ios 2-pc


        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        if (empty($username) || empty($username)) {
            $this->error('用户名或密码不能为空');
        } else {
            $map['username'] = $username;
            // $map['status'] = 0;
            $userInfo = M('user', 'think_')->where($map)->find();
            if ($userInfo['status'] != 0) {
                $this->error('该用户已被禁用！');
            }
            if (empty($userInfo)) {
                $this->error('该用户不存在！');
            }
            if ($userInfo['password'] != md5($passWord)) {
                $this->error('密码错误！');
            }
            // if ($userInfo['type'] == 4) {
            //     $typeContent = "家长";
            // } elseif ($userInfo['type'] == 3) {
            //     $typeContent = "老师";
            // } else {
            //     $typeContent = "";
            // }
            if ($type != intval($userInfo['type'])) {
                $this->error('登录角色选择错误！');
            }
            $isAdmin = 0;
            $Admin = D('User', 'Logic');
            $userInfo['system_model'] = $system_model;
            if ($Admin->setLogin($userInfo, $isAdmin)) {
                //判断ApiKey是否存在
                if (empty($userInfo['api_id'])) {
                    $where['user_id'] = $userInfo['user_id'];
                    $data['api_id'] = $this->createApiKey();
                    $data['security_key'] = $this->createApiKey();
                    M('user', 'think_')->where($where)->save($data);
                    $api_id = $data['api_id'];
                    $security_key = $data['security_key'];
                } else {
                    $api_id = $userInfo['api_id'];
                    $security_key = $userInfo['security_key'];
                }
                //返回老师和家长信息
                $where1['u_id'] = $userInfo['user_id'];
                if ($userInfo['type'] == 3) {//教师
                    $eduInfo = C('EDUCATION');
                    $teacherInfo = D('Teacher')->where($where1)->find();
                    $arrDate['t_id'] = $teacherInfo['t_id'];
                    $arrDate['name'] = $teacherInfo['name'];
                    $arrDate['phone'] = $teacherInfo['phone'];
                    $arrDate['s_id'] = $teacherInfo['s_id'];
                    $arrDate['s_name'] = $this->get_school_name($teacherInfo['s_id'])[$teacherInfo['s_id']];
                    $arrDate['a_id'] = $teacherInfo['a_id'];
                    $arrDate['a_name'] = $this->get_area_name($teacherInfo['a_id'])[$teacherInfo['a_id']];
                    $arrDate['d_id'] = $teacherInfo['d_id'];
                    $arrDate['d_name'] = $this->get_dept_name($teacherInfo['a_id'])[$teacherInfo['d_id']];
                    $Classinfo = M('Class')->where(array('t_id' => $teacherInfo['t_id']))->find();
                    if (empty($Classinfo)) {
                        $arrDate['charge'] = "0";
                    } else {
                        $arrDate['charge'] = "1";
                    }
                    $arrDate['education'] = $eduInfo[$teacherInfo['education']];
                    $arrDate['sex'] = $teacherInfo['sex'];

                    $arrDate['u_id'] = $teacherInfo['u_id'];
                    $arr['class'] = $this->getClass($userInfo['user_id']);
                    $arr['course'] = $this->getCourse($userInfo['user_id']);
                } elseif ($userInfo['type'] == 4) {
                    $RELATION = C('RELATION');
                    $parentInfo = D('StudentParent')->where($where1)->find();
                    $arrDate['sp_id'] = $parentInfo['sp_id'];
                    $arrDate['parent_name'] = $userInfo['name'];
                    $arrDate['parent_phone'] = $userInfo['username'];
                    $arrDate['relation'] = $RELATION[$parentInfo['relation']];

                    $arrDate['sex'] = $userInfo['sex'];
                    $arrDate['u_id'] = $parentInfo['u_id'];
                    $arrDate['address'] = $parentInfo['address'];
                    $arrDate['work_unit'] = $parentInfo['address'];
                    $arrDate['family_tel'] = $parentInfo['family_tel'];
                    $arrDate['email'] = $userInfo['email'];
                    $arr['stuData'] = $this->getStuden($parentInfo['u_id']);
                } elseif ($userInfo['type'] == 0) {//会员，1学校管理员 2 系统用户
                    $arrDate['name'] = $userInfo['name'];
                    $arrDate['username'] = $userInfo['username'];
                    $arrDate['user_id'] = $userInfo['user_id'];
                    $arrDate['type'] = $userInfo['type'];
                    $arrDate['sex'] = $userInfo['sex'];
                    $arrDate['phone'] = $userInfo['phone'];
                    $arrDate['email'] = $userInfo['email'];
                }
                $arrDate['api_id'] = $api_id;
                $arrDate['security_key'] = $security_key;
                $arr['login'] = $arrDate;
                $this->success($arr);
            } else {
                $this->error('服务器错误，登录失败！');
            }
        }

    }


    public function login2_1_3()
    {
        $username = I('post.username');
        $passWord = I('post.password');

        $system_model = I('post.system_model', 0);//1-android 0-ios 2-pc


        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        if (empty($username) || empty($username)) {
            $this->error('用户名或密码不能为空');
        }
        $whereStr['username'] = $username;
        $whereStr['phone'] = $username;
        $whereStr['_logic'] = 'or';
        $map['_complex'] = $whereStr;
        // $map['status'] = 0;
        $userInfo = M('user', 'think_')->where($map)->find();
        if (empty($userInfo)) {
            $this->error('该用户不存在！');
        }
        if ($userInfo['status'] != 0) {
            $this->error('该用户已被禁用！');
        }
        if ($userInfo['password'] != $passWord) {
            $this->error('密码错误！');
        }
        $isAdmin = 0;
        $Admin = D('User', 'Logic');
        $userInfo['system_model'] = $system_model;
        if ($Admin->setLogin($userInfo, $isAdmin)) {
            //判断ApiKey是否存在
            if (empty($userInfo['api_id'])) {
                $where['user_id'] = $userInfo['user_id'];
                $data['api_id'] = $this->createApiKey();
                $data['security_key'] = $this->createApiKey();
                M('user', 'think_')->where($where)->save($data);
                $api_id = $data['api_id'];
                $security_key = $data['security_key'];
            } else {
                $api_id = $userInfo['api_id'];
                $security_key = $userInfo['security_key'];
            }
            //返回老师和家长信息
            $where1['u_id'] = $userInfo['user_id'];
            $arrDate['type'] = $userInfo['type'];
            $arrDate['name'] = $userInfo['name'];
            $arrDate['username'] = $userInfo['username'];
            $arrDate['user_id'] = $userInfo['user_id'];
            $arrDate['sex'] = $userInfo['sex'];
            $arrDate['phone'] = $userInfo['phone'];
            $arrDate['email'] = $userInfo['email'];
            $arrDate['head_portrait'] = $userInfo['head_portrait'];

            //获取上级user_id  游客 老师 家长
            if (($userInfo['type'] == 0) || ($userInfo['type'] == 3) || ($userInfo['type'] == 4)) {
                if ($userInfo['ref_id'] == 0) {
                    $arrDate['ref_id'] = '10847';//信平台user_id
                } else {
                    $arrDate['ref_id'] = $userInfo['ref_id']; //显示上级的id
                }
            } else {
                $arrDate['ref_id'] = $userInfo['user_id']; //显示自己的id
            }

            if ($userInfo['type'] == 3) {//教师
                $eduInfo = C('EDUCATION');
                $teacherInfo = D('Teacher')->where($where1)->find();
                $arrDate['t_id'] = $teacherInfo['t_id'];
                $arrDate['name'] = $teacherInfo['name'];
                $arrDate['phone'] = $teacherInfo['phone'];
                $arrDate['s_id'] = $teacherInfo['s_id'];
                $arrDate['s_name'] = $this->get_school_name($teacherInfo['s_id'])[$teacherInfo['s_id']];
                $arrDate['a_id'] = $teacherInfo['a_id'];
                $arrDate['a_name'] = $this->get_area_name($teacherInfo['a_id'])[$teacherInfo['a_id']];
                $arrDate['d_id'] = $teacherInfo['d_id'];
                $arrDate['d_name'] = $this->get_dept_name($teacherInfo['a_id'])[$teacherInfo['d_id']];
                $Classinfo = M('Class')->where(array('t_id' => $teacherInfo['t_id']))->find();
                if (empty($Classinfo)) {
                    $arrDate['charge'] = "0";
                } else {
                    $arrDate['charge'] = "1";
                }
                $arrDate['education'] = $eduInfo[$teacherInfo['education']];
                $arrDate['sex'] = $teacherInfo['sex'];

                $arrDate['u_id'] = $teacherInfo['u_id'];
                $arr['class'] = $this->getClass($userInfo['user_id']);
                $arr['course'] = $this->getCourse($userInfo['user_id']);
            } elseif ($userInfo['type'] == 4) {
                $RELATION = C('RELATION');
                $parentInfo = D('StudentParent')->where($where1)->find();
                $arrDate['sp_id'] = $parentInfo['sp_id'];
                $arrDate['parent_name'] = $userInfo['name'];
                $arrDate['parent_phone'] = $userInfo['phone'];
                $arrDate['relation'] = $RELATION[$parentInfo['relation']];

                $arrDate['sex'] = $userInfo['sex'];
                $arrDate['u_id'] = $parentInfo['u_id'];
                $arrDate['address'] = $parentInfo['address'];
                $arrDate['work_unit'] = $parentInfo['address'];
                $arrDate['family_tel'] = $parentInfo['family_tel'];
                $arrDate['email'] = $userInfo['email'];
            } else {//代理商
                $data = D('region_user')->where(['u_id' => $userInfo['user_id']])->find();
                $where['region_id'] = array('in', array($data['prov_id'], $data['city_id'], $data['county_id']));
                $res = D('area_region')->where($where)->select();
                foreach ($res as $key => $value) {
                    $arrDate['area_list'][] = $value['region_name'];
                }
            }
            $arr['stuData'] = $this->getStuden($userInfo['user_id']);
            $arrDate['api_id'] = $api_id;
            $arrDate['security_key'] = $security_key;
            $arr['login'] = $arrDate;

            $this->success($arr);
        } else {
            $this->error('服务器错误，登录失败！');
        }
    }

    /*
     * 最新版登录
     * 多一个字段为标识 identification
     * index.php/Api/Login/login2_2_2
     */
    public function login2_2_2()
    {
        $username = I('post.phone');
        $verfiyCode = I('post.verifyCode');
        $password = I('post.password');
        $openId = I('post.openId');
        $roof_name = I('post.platform_name');
        $system_model = I('post.system_model', 0);//1-android 0-ios 2-pc
        if ($username) {
            if ($verfiyCode) {//验证码登录
                $codeInfo = D('Code')->field('code,create_time')->where(['phone' => $username])->order('id  desc')->find();
                $Tdifference = time() - strtotime($codeInfo['create_time']);
                if ($Tdifference > 180) {
                    $this->error("验证码无效请重新获取");
                }
                if ($verfiyCode != $codeInfo['code']) {
                    $this->error("验证码错误");
                }
                //拿手机号码去用户表查找，有数据，登录成功，没数据，添加数据
                $userInfo = $this->userInfo($username);
                if (!$userInfo) {
                    $data['username'] = $username;
                    $data['name'] = filter_Emoji(I('post.name'));
                    $data['create_time'] = date('Y-m-d H:i:s');
                    $data['source'] = I('post.source');
                    $data['ref_id'] = base64_decode(I('post.ref'));
                    $bool = M('user', 'think_')->add($data);
                    $userInfo = $this->userInfo($username);
                    if (!$bool) {
                        $this->error('请检查网络');
                    }
                }
            } else if ($password) {//密码登录
                $userInfo = $this->userInfo($username);
                if ($userInfo) {
                    if ($userInfo['password'] != $password) {
                        $this->error('用户名或密码错误');
                    }
                } else {
                    $this->error('用户名或密码错误');
                }
            } else {
                $this->error('参数错误');
            }
        } else {
            //第三方openId 登录（） 参数：（openId,平台名称） 若 openId 不存在，则返回 info:1001;若存在，返回成功信息
            if ($roof_name == 'wx') {
                $userInfo = M('user', 'think_')
                    ->field('name,username,password,user_id,sex,phone,qq_openid,wx_openid,head_portrait')
                    ->where(['wx_openid' => $openId])
                    ->find();
                if (!$userInfo) {
                    $this->error("1001");
                }
            } elseif ($roof_name == 'qq') {
                $userInfo = M('user', 'think_')
                    ->field('name,username,password,user_id,sex,phone,qq_openid,wx_openid,head_portrait')
                    ->where(['qq_openid' => $openId])
                    ->find();
                if (!$userInfo) {
                    $this->error("1001");
                }
            } else {
                $this->error('参数错误');
            }
        }
        //设备信息
        $data['devices'] = $this->filter_data($this->getStuden($userInfo['user_id']));
        //用户信息
        $userInfo['system_model'] = $system_model;
        $tokenArr = array(
            $userInfo['username'],
            $userInfo['user_id'],
            strtotime('+1 month')
        );
        $userInfo['token'] = encrypt_ase(json_encode($tokenArr));
        $userInfo['time'] = strtotime('+1 month');
        $data['login'] = $userInfo;
        easemob_add_user($userInfo['user_id']);
        //设置session
        $Admin = D('User', 'Logic');
        $Admin->setLogin($userInfo);
        $this->success($data);
    }

    /*
     * 第三方账号绑定手机号
     *  若login_2-2-2方法，返回1001，则客户端调用此接口
     *   传参：openId，平台名称，手机号，验证码，昵称，性别，头像路径
     */
    public function bindPhone()
    {
        $openId = I('post.openId');
        $platform_name = I('post.platform_name');
        $username = I('post.phone');
        $verifyCode = I('post.verifyCode');
        $name = filter_Emoji(I('post.name'));
        $sex = I('post.sex');
        $head_portrait = I('post.head_portrait');
        $system_model = I('post.system_model', 0);//1-android 0-ios 2-pc

        $codeInfo = D('Code')->field('code,create_time')->where(['phone' => $username])->order('id  desc')->find();
        $Tdifference = time() - strtotime($codeInfo['create_time']);
        if ($Tdifference > 180) {
            $this->error("验证码无效请重新获取");
        }
        if ($verifyCode != $codeInfo['code']) {
            $this->error("验证码错误");
        }
        //1.根据手机号查询用户是否存在，若存在，则绑定openid，反之插入一条用户信息（username，openid，nickname，photo。。）
        //用open_id去wx_openid查找用户
        if (!$username) {
            $this->error('非法请求');
        }
        $info = $this->userInfo($username);
        if ($info) {
            if ($platform_name == 'wx') {
                $bool = M('user', 'think_')->where(['username' => $username])->save(['wx_openid' => $openId, 'modif_time' => date('Y-m-d H:i:s')]);
            } elseif ($platform_name == 'qq') {
                $bool = M('user', 'think_')->where(['username' => $username])->save(['qq_openid' => $openId, 'modif_time' => date('Y-m-d H:i:s')]);
            } else {
                $this->error('非法请求');
            }

        }
        else {
            if ($platform_name == 'wx') {
                $data['wx_openid'] = $openId;
            } elseif ($platform_name == 'qq') {
                $data['qq_openid'] = $openId;
            } else {
                $this->error('非法请求');
            }
            $data['username'] = $username;
            $data['name'] = $name;
            $data['sex'] = $sex;
            $data['phone']=$username;
            $data['create_time']=date('Y-m-d H:i:s');
            $data['head_portrait'] = $head_portrait;
            $bool = M('user', 'think_')->add($data);
        }
        if (!$bool) {
            $this->error('服务器错误');
        }
        $info = $this->userInfo($username);
        unset($info['password']);
        $userinfo['devices'] = $this->filter_data($this->getStuden($info['user_id']));
        $Admin = D('User', 'Logic');
        $info['system_model'] = $system_model;
        $tokenArr = array(
            $info['username'],
            $info['user_id'],
            strtotime('+1 month')
        );
        $info['token'] = encrypt_ase(json_encode($tokenArr));
        $userinfo['login'] = $info;
        easemob_add_user($info['user_id']);
        $Admin->setLogin($info);
        $this->success($userinfo);

    }

    //获取设备信息
    public function devices()
    {
        $user_id = I('post.user_id');
        $devices=array();
        if ($user_id) {
            $devices = $this->filter_data($this->getStuden($user_id));
            if ($devices) {
                $this->success($devices);
            } else {
                $this->success($devices);
            }
        } else {
            $this->error('非法请求');
        }

    }

    //下发短信
    public function getSMSCode()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $Code = D('Code');//保存验证码表
        $phone = I('post.phone');//手机号
        $send_code = random(6, 1);
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
        if ($Tdifference < 60) {
            $this->error("您刚刚发送过一条验证码，如果一分钟内没有收到，您可以再次发送");
        }
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

    //处理数据
    protected function filter_data($devices)
    {
        $arr = array();
        foreach ($devices as $key => $value) {
            $arr[$key]['stu_id'] = $devices[$key]['stu_id'];
            $arr[$key]['stu_name'] = $devices[$key]['stu_name'];
            $arr[$key]['imei_id'] = $devices[$key]['imei_id'];
            $arr[$key]['watch_phone'] = $devices[$key]['card_phone'];
            $arr[$key]['birth_date'] = $devices[$key]['birth_date'];
            $arr[$key]['sex'] = $devices[$key]['sex'];
            $arr[$key]['sos'] = $devices[$key]['sos'];
            $arr[$key]['phb'] = $devices[$key]['phb'].','.$devices[$key]['phb2'];
            $arr[$key]['relation'] = $devices[$key]['relation'];
            $arr[$key]['photo'] = $devices[$key]['photo'];

        }
        return $arr;
    }

    //获取用户信息
    protected function userInfo($username = "")
    {
        $info = M('user', 'think_')->field('name,username,password,user_id,sex,phone,qq_openid,wx_openid,head_portrait,api_id,security_key')->where(['username' => $username])->find();
        return $info;
    }

    public function edit_password()
    {
        $user_id = I('post.user_id');
        $ypassword = I('post.ypassword');
        $password = I('post.password');
        if (empty($user_id)) {
            $this->error('参数错误！');
        }
//        if (empty($ypassword)) {
//            $this->error('旧密码不能为空');
//        }
        if (empty($password)) {
            $this->error('新密码不能为空');
        }

        $where['user_id'] = $user_id;
        $data['password'] = md5($password);
        $data['user_id'] = $user_id;

        //查询旧密码是否正确
        //$userInfo = M('AdminUser')->where($where)->find();
        $userInfo = M('user', 'think_')->where($where)->find();
        if ($userInfo['password']) {//设置过密码
            if (md5($ypassword) != $userInfo['password']) {
                $this->error('旧密码不正确');
            }
            if (0 == strcmp($userInfo['password'], md5($password))) {
                $this->error('新密码不得与旧密码一样');
            }
        }
        //$ret = D('AdminUser')->where($where)->save($data);
        $ret=M('user', 'think_')->where($where)->save($data);
        if ($ret == 0) {
            $this->error('密码修改失败');
        } else {
            $this->success('密码修改成功，请重新登陆');
        }

    }

    // 年级班级返回
    public function getClass($userId = 0)
    {
        $where['u_id'] = $userId;
        $newRet2 = array();
        $tId = D('Teacher')->where($where)->find();
        if (!empty($tId)) {
            $where1['t_id'] = array('EQ', $tId['t_id']);
            //获取授课列表
            $arrClass = D('TeacherCourse')->where($where1)->getField("c_id", true);
            //获取担任班主任列表
            $classInfo = D('Class')->where($where1)->getField("c_id", true);
            if ($arrClass === NULL) {
                $arrClass = array();
            }
            if ($classInfo === NULL) {
                $classInfo = array();
            }
            //班级数组
            $arrRet = array_merge($arrClass, $classInfo);
            $tmpRet = array_unique($arrRet);
            //过滤不存在的班级
            $allClass = M('Class')->getField("c_id", true);
            $newRet = array_intersect($allClass, $tmpRet);
            sort($newRet);

            //判断是否为班主任
            foreach ($newRet as $key => $value) {
                $newRet2[$key]['c_id'] = $value;
                if (in_array($value, $classInfo)) {
                    $newRet2[$key]['is_head'] = 1;
                } else {
                    $newRet2[$key]['is_head'] = 0;
                }
                $Info = getClassInfo($value);
                //获取班级名称
                $newRet2[$key]['c_name'] = $Info['c_name'];
                //获取年级gid
                $newRet2[$key]['g_id'] = $Info['g_id'];
                //获取g_name
                $newRet2[$key]['g_name'] = $Info['g_name'];
            }
        }
        return $newRet2;
    }

    //生成ApiId
    protected function createApiKey()
    {
        $Model = new Model();
        $ret = $Model->query("Select UUID() as uuid");
        $key = $ret[0]['uuid'];
        $qian = array("-");
        $hou = array("");
        return str_replace($qian, $hou, $key);
    }

    public function logout()
    {
        $Admin = D('Adminuser', 'Logic');
        $Admin->logOut();
        $this->success('退出系统成功');
    }

    public function login()
    {

        $this->display();
    }


    //获取孩子班级
    public function getStuden($user_id)
    {
        $studentAccessList = D('StudentGroupAccess')->where(array('user_id' => $user_id))->select();
        $gId = array();
        $cId = array();
        $stuId = array();
        $arr = array();
        foreach ($studentAccessList as $key => $value) {
            $stuId[] = $value['stu_id'];
        }
        $where['stu_id'] = array('IN', implode(',', $stuId));
        $student = D('Student')->where($where)->select();
        foreach ($student as $key => $value) {
            $arr[$key]['s_id'] = $value['s_id'];
            $arr[$key]['a_id'] = $value['a_id'];
            $arr[$key]['g_id'] = $value['g_id'];
            $arr[$key]['c_id'] = $value['c_id'];
            $arr[$key]['stu_id'] = $value['stu_id'];
            $arr[$key]['stu_name'] = $value['stu_name'];
            $arr[$key]['stu_no'] = $value['stu_no'];
            $arr[$key]['imei_id'] = $value['imei_id'];
            $arr[$key]['card_phone'] = $value['stu_phone'];
            $arr[$key]['birth_date'] = $value['birth_date'];
            $arr[$key]['rx_date'] = $value['rx_date'];
            $arr[$key]['sex'] = $value['sex'];
            $arr[$key]['devicetype'] = $value['devicetype'];
            $arr[$key]['s_name'] = $this->get_school_name($value['s_id'])[$value['s_id']];
            $arr[$key]['a_name'] = $this->get_area_name($value['a_id'])[$value['a_id']];
            $arr[$key]['g_name'] = $this->get_grade_name($value['g_id'])[$value['g_id']];
            $arr[$key]['c_name'] = $this->get_class_name($value['c_id'])[$value['c_id']];
            $where['stu_id'] = $value['stu_id'];
            $where['d_type'] = "sos";
            $deviceInfo = D('StuCardSet')->where($where)->find();
            $arr[$key]['sos'] = $deviceInfo['d_value'];
            $where1['stu_id'] = $value['stu_id'];
            $where1['d_type'] = "phb";
            $deviceInfo1 = D('StuCardSet')->where($where1)->find();
            $arr[$key]['phb'] = $deviceInfo1['d_value'];
            //
            $where1['stu_id'] = $value['stu_id'];
            $where1['d_type'] = "phb2";
            $deviceInfo1 = D('StuCardSet')->where($where1)->find();
            $arr[$key]['phb2'] = $deviceInfo1['d_value'];
            //
            $where2['stu_id'] = $value['stu_id'];
            $where2['d_type'] = "monitor";
            $deviceInfo2 = D('StuCardSet')->where($where2)->find();
            $arr[$key]['monitor'] = $deviceInfo2['d_value'];
            $relation = D('student_group_access')->where(['stu_id' => $value['stu_id'], 'user_id' => $user_id])->find();
            $arr[$key]['relation'] = $relation['relation'];
            $arr[$key]['photo'] = $value['photo'];
        }
        return $arr;
    }

    //课程
    protected function getCourse($userId)
    {
        $where['u_id'] = $userId;
        $tId = D('Teacher')->where($where)->find();

        if (!empty($tId)) {
            $where1['a.t_id'] = array('EQ', $tId['t_id']);
            $arrRet = D('TeacherCourse')->distinct(true)->alias("a")
                ->field('a.crs_id as id, a.g_id,g.name as g_name, b.name ')
                ->join(" __COURSE__ b ON a.crs_id=b.crs_id ")
                ->join(" __GRADE__ g ON a.g_id=g.g_id ")
                ->where($where1)
                ->select();
        } else {
            $arrRet = false;
        }
        return $arrRet;
    }

    //查询学校名称
    protected function get_school_name($s_id)
    {
        $where['s_id'] = $s_id;
        $schoolName = D('SchoolInformation')->where($where)->getField('s_id,name');
        return $schoolName;
    }

    //查询校区名称
    protected function get_area_name($a_id)
    {
        $where['a_id'] = $a_id;
        $areaName = D('SchoolArea')->where($where)->getField('a_id,name');
        return $areaName;
    }

    //查询部门名称
    protected function get_dept_name($a_id)
    {
        $where['a_id'] = $a_id;
        $deptName = D('Dept')->where($where)->getField('d_id,name');
        return $deptName;
    }

    protected function get_grade_name($g_id)
    {
        $where['g_id'] = array('EQ', $g_id);
        $courseInfo = D('Grade')->where($where)->getField('g_id,name');
        return $courseInfo;
    }

    protected function get_class_name($c_id)
    {
        $where['c_id'] = array('EQ', $c_id);
        $courseInfo = D('Class')->where($where)->getField('c_id,name');
        return $courseInfo;
    }

    //第三方解除綁定
    public function bind_unbind()
    {
        $openId = I('post.openId');
        $platform_name = I('post.platform_name');
        $userId = I('post.user_id');
        $status = I('post.bind_status');
        if(empty($openId))
        {
            $this->error('openid不能为空');
        }
        if(empty($platform_name))
        {
            $this->error('platform_name不能为空');
        }
        if(empty($userId))
        {
            $this->error('user_id不能为空');
        }
        if($status===NULL)
        {
            $this->error('bind_status不能为空');
        }

        $where['user_id'] = $userId;
        $ret = M('user', 'think_')->where($where)->find();//判断是否有该wx openid
        if (!$ret) {
            $this->error("没有该数据");
        }
        if ($status == 1) {//第三方绑定
            if ($platform_name == 'wx') {
                $wxInfo['wx_openid']=$openId;
                $ret = M('user', 'think_')->where($wxInfo)->find();
                if($ret){
                    $this->error('该微信号已经绑定');
                }
                $saveInfo['wx_openid'] = $openId;
            } elseif ($platform_name == 'qq') {
                $qqInfo['qq_openid']=$openId;
                $ret = M('user', 'think_')->where($qqInfo)->find();
                if($ret){
                    $this->error('该qq号已经绑定');
                }
                $saveInfo['qq_openid'] = $openId;
            }
            $msg = '绑定';
        } else {//解绑
            if ($platform_name == 'wx') {//
                $where['user_id'] = $userId;
                $where['wx_openid'] = $openId;
                $ret = M('user', 'think_')->where($where)->find();//判断是否有该wx openid
                if (!$ret) {
                    $this->error("该设备没有绑定微信");
                }
                $saveInfo['wx_openid'] = '';
            } elseif ($platform_name == 'qq') {
                $where['user_id'] = $userId;
                $where['qq_openid'] = $openId;
                $ret = M('user', 'think_')->where($where)->find();//判断是否有该qq openid
                if (!$ret) {
                    $this->error("该设备没有绑定QQ");
                }
                $saveInfo['qq_openid'] = '';
            }
            $msg = '解绑';
        }

        $saveInfo['modif_time'] = date("Y-m-d H:i:s");
        $ret = M('user', 'think_')->where("user_id = '{$userId}'")->save($saveInfo);//判断是否有该qq openid
        if (!$ret) {
            $this->error($msg . '失败,请稍后重试');
        }
        $this->success($msg . '成功');
    }


}