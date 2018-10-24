<?php

namespace Api\Controller;

use Think\Controller;

class HookController extends Controller
{
    public function doSendMsg()
    {
        $phone = I("get.phone");
        $msg = I("get.msg") . '#';

        if ($phone && $msg) {
            $res = sendSms($phone, $msg);
            if ($res) {
                $this->success('发送成功', '/Api/Hook/sendMsg', 5);
            } else {
                $this->error("发送失败");
            }
        } else {
            $this->error("参数错误");
        }
    }

    public function doSendIOTMsg()
    {
        $phone = I("get.phone");
        $msg = I("get.msg") . '#';

        if ($phone && $msg) {
            $res = sendIOTMsg($phone, $msg);
            if ($res) {
                $this->success('发送成功', '/Api/Hook/sendIOTMsg', 5);
            } else {
                $this->error("发送失败");
            }
        } else {
            $this->error("参数错误");
        }
    }

    //短信下发学生卡命令
    public function sendMsg()
    {
        $this->display();
    }

    //短信下发学生卡命令
    public function sendIOTMsg()
    {
        $this->display();
    }

    /**
     * APP报警警报推送
     * 标题: xxx警报提醒
     * 内容:学生姓名 警报类型
     * */
    public function warningMsgPush()
    {
        header("Content-type:text/html;charset=utf-8");
        //获取未处理的警报消息
        $unread = M("WarningMessage")->field("wm_id,imei,war_type,longitude,latitude,create_time,mcc,mnc,lac,cid")
            ->where('is_push = 0')
            ->limit(1000)
            ->order('wm_id desc')
            ->select();

        //警报类型
        $msgInfo = C('BAOJING_TYPE');
        $msgTime = C('APP_PUSH_TIME');
        $msgPushStep = C('APP_PUSH_TIME_STEP');
        $pushArr = array();//初始化队列数组
        $statusData = array();//初始化状态处理数组

        if ($unread) {
            foreach ($unread as $key => $value) {
                //获取学生信息
                $stu_info = get_stu_info($value['imei']);

                //检测时间区间 20:00 - 06:00不发送推送
                $nowHour = date('H');
                //符合发送时间要求
                if (($msgTime[0] <= $nowHour && $nowHour < $msgTime[1]) || ($value['war_type'] == 1) || ($value['war_type'] == 4)) {
                    //检测30分钟以内是否对监护人推送过该类型的消息
                    if (($value['war_type'] != 1) && ($value['war_type'] != 4)) {
                        $pushWhere['imei'] = $value['imei'];
                        $pushWhere['war_type'] = $value['war_type'];
                        $pushWhere['is_push'] = 1;
                        $pushWhere['create_time'] = array('gt', date("Y-m-d H:i:s", strtotime("-$msgPushStep minute")));

                        $is_push = M("WarningMessage")
                            ->cache(true)
                            ->field('wm_id')
                            ->where($pushWhere)
                            ->find();
                    } else {
                        $is_push = 0; //sos 围栏默认没有推送
                    }

                    //30分钟没有推送过 | 1SOS | 4电子围栏
                    if (!$is_push || ($value['war_type'] == 1)) {
                        //app消息推送 By:Meng Fanmin 2017.06.27
                        if ($value['imei']) {
                            //构建推送消息体
                            $tmp = array(
                                'fsdx' => '0,1',
                                'u_id' => $stu_info['parent_id'],//父母ID数组
                                'ticker' => $msgInfo[$value['war_type']] . '警报',
                                'title' => $msgInfo[$value['war_type']] . '警报',
                                'text' => '您好，您孩子的学生卡已发出' . $msgInfo[$value['war_type']] . '报警信息，请及时确认。',
                                'after_open' => 'go_custom',
                                'activity' => 'warning',
                                'id' => $value['wm_id'],
                            );
                            $pushArr[] = $tmp;
                        }

                        //推送成功
                        $statusData[1][] = $value['wm_id'];
                        if ($stu_info['parent_id']) {
                            if ($value['war_type'] == 1) {
                                $where['user_id'] = array('in', $stu_info['parent_id']);
                                //获取家长手机号
                                $p_phone = M('User', 'think_')->where($where)->getField('phone', true);
                                $p_phone = array_unique($p_phone);

                                foreach ($p_phone as $phone) {
                                    sendSms($phone, $tmp['text']);
                                }
                            }

                        }
                    } else {
                        //30分钟内已经推送
                        $statusData[2][] = $value['wm_id'];
                    }
                } else {
                    //不符合发送时间要求
                    $statusData[3][] = $value['wm_id'];
                }
            }

            foreach ($statusData as $k => $v) {
                $data['is_push'] = $k;
                $data['wm_id'] = array('in', $v);
                M("WarningMessage")->save($data);
            }

            //对消息队列进行发送
            foreach ($pushArr as $key => $value) {
                $res = $this->sendAppMsg($value);
            }
        }
    }

    /**
     * 获取监护人user_id
     * */
    public function getParUserId($stuArr)
    {
        if (!$stuArr) {
            return false;
        }
        $whereStr['a.stu_id'] = array('in', $stuArr);
        $p_user = M("StudentGroupAccess")
            ->cache('true')
            ->field('a.stu_id,b.u_id')
            ->alias("a")
            ->join("__STUDENT_PARENT__ b on a.sp_id = b.sp_id")
            ->where($whereStr)->select();

        $pUidArr = array();
        foreach ($p_user as $key => $value) {
            foreach ($stuArr as $k => $v) {
                if ($value['stu_id'] == $v) {
                    $pUidArr[$v][] = $value['u_id'];
                }
            }

        }
        return $pUidArr;
    }

    /**
     * 下发APP公告
     * */
    public function sendAppMsg($pushArr)
    {
        import('Vendor.MsgPush.Push');
        //app消息推送 By:Meng Fanmin 2017.06.22
        $niticeArr = explode(',', $pushArr['fsdx']);//0教师 1家长
        //消息内容
        $pushArr['after_open'] = $pushArr['after_open'] ? $pushArr['after_open'] : 'go_app';
        $alert = array(
            'ticker' => $pushArr['ticker'],
            'title' => $pushArr['title'],
            'text' => $pushArr['text'],
            'after_open' => $pushArr['after_open'],
            'activity' => $pushArr['activity'],
            'id' => $pushArr['id'],
        );
        $whereStr['user_id'] = array('in', $pushArr['u_id']);
        $whereStr['system_model'] = 1;
        $androidToken = M('MsgpushToken')->field('device_token,push_name')->where($whereStr)->select();
        $whereStr['system_model'] = 0;
        $iosToken = M('MsgpushToken')->where($whereStr)->getField('device_token', true);

        if ($niticeArr[1]) {//通知家长
            //获取家长手机device_token
            if ($androidToken) {
                //Umeng
                foreach ($androidToken as $key => $value) {
                    if ($value['push_name'] == 'MIPush') {
                        $tokenArrMI[] = $value['device_token'];
                    } elseif ($value['push_name'] == 'HWPush') {
                        $tokenArrHW[] = $value['device_token'];
                    } elseif ($value['push_name'] == 'MZPush') {
                        $tokenArrMZ[] = $value['device_token'];
                    } else {
                        $tokenArrUM[] = $value['device_token'];
                    }
                }

                if ($tokenArrMI) {
                    $pushObj = new \Push('MIPush');
                    $tokenArr = $tokenArrMI;
                    $pushObj->sendAndroidPushP($tokenArr, $alert);
                }
                if ($tokenArrHW) {
                    $pushObj = new \Push('HWPush');
                    $tokenArr = $tokenArrHW;
                    $pushObj->sendAndroidPushP($tokenArr, $alert);
                }
                if ($tokenArrUM) {
                    $pushObj = new \Push('UPush');
                    $tokenArr = $tokenArrUM;
                    $pushObj->sendAndroidPushP($tokenArr, $alert);
                }
                if ($tokenArrMZ) {
                    $pushObj = new \Push('MZPush');
                    $tokenArr = $tokenArrMZ;
                    $pushObj->sendAndroidPushP($tokenArr, $alert);
                }
            }
            if ($iosToken) {
                $pushObj = new \Push('UPush');
                $tokenArr = $iosToken;
                $pushObj->sendIosPushP($tokenArr, $alert);
            }
        }
        if ($niticeArr[0]) {//通知教师
            //获取教师手机device_token
            if ($androidToken) {
                //Umeng
                foreach ($androidToken as $key => $value) {
                    if ($value['push_name'] == 'MIPush') {
                        $tokenArrMIT[] = $value['device_token'];
                    } else if ($value['push_name'] == 'HWPush') {
                        $tokenArrHWT[] = $value['device_token'];
                    } else if ($value['push_name'] == 'MZPush') {
                        $tokenArrMZT[] = $value['device_token'];
                    } else {
                        $tokenArrUMT[] = $value['device_token'];
                    }
                }
                if ($tokenArrMIT) {

                    $pushObj = new \Push('MIPush');
                    $tokenArr = $tokenArrMIT;
                    $pushObj->sendAndroidPushT($tokenArr, $alert);
                }
                if ($tokenArrHWT) {
                    $pushObj = new \Push('HWPush');
                    $tokenArr = $tokenArrHWT;
                    $pushObj->sendAndroidPushT($tokenArr, $alert);
                }
                if ($tokenArrUMT) {
                    $pushObj = new \Push('UPush');
                    $tokenArr = $tokenArrUMT;
                    $pushObj->sendAndroidPushT($tokenArr, $alert);
                }
                if ($tokenArrMZT) {
                    $pushObj = new \Push('MZPush');
                    $tokenArr = $tokenArrMZT;
                    $pushObj->sendAndroidPushT($tokenArr, $alert);
                }
            }
            if ($iosToken) {
                $pushObj = new \Push('UPush');
                $tokenArr = $iosToken;
                $pushObj->sendIosPushT($tokenArr, $alert);
            }

        }
    }

    public function unicode_encode($name, $iconv)
    {
        $name = iconv('UTF-8', $iconv, $name);
        $len = strlen($name);
        $str = '';
        for ($i = 0; $i < $len - 1; $i = $i + 2) {
            $c = $name[$i];
            $c2 = $name[$i + 1];
            $str[$i] = base_convert(ord($c), 10, 16) . base_convert(ord($c2), 10, 16);
            if (strlen($str[$i]) < 4) {
                $str[$i] = str_pad($str[$i], 4, 0, STR_PAD_LEFT);
            }

        }
        $str = implode('', $str);
        return $str;
    }

    /**
     * 获取校园天气信息
     *
     * */
    public function getSchoolWeather()
    {

        //获取学校s_id及名称 17
        $cityName = M('SchoolInformation')->alias('a')
            ->field('b.region_id,b.region_name')
            ->join("__AREA_REGION__ b on a.city_id = b.region_id")
            ->group("a.city_id")
            ->select();

        foreach ($cityName as $key => $value) {
            //获取天气详情
            $weatherInfo = getNowWeather($value['region_name']);

            $weatherData['region_id'] = $value['region_id'];
            if ($weatherInfo['city']) {
                $weatherData['city'] = $value['region_name'];
            }
            if ($weatherInfo['wendu']) {
                $weatherData['temp'] = $weatherInfo['wendu'];
            }
            if ($weatherInfo['ganmao']) {
                $weatherData['memo'] = $weatherInfo['ganmao'];
            }
            $weatherData['cityid'] = $weatherInfo['cityid'];
            $weatherData['wind'] = $weatherInfo['forecast'][0]['fengxiang'] . $weatherInfo['forecast'][0]['fengli'];
            $weatherData['weather'] = $weatherInfo['forecast'][0]['type'] . ',' . $weatherData['temp'] . '度';

            $weatherData['unicoderevweather'] = utf8_unicode($weatherData['weather'], 1);
            $weatherData['unicodeweather'] = utf8_unicode($weatherData['weather'], 0);
            $weatherData['time'] = date('Y-m-d H:i:s');
            $weatherData['high_low'] = rtrim(substr($weatherInfo['forecast'][0]['high'], 6), "℃") . ',' . rtrim(substr($weatherInfo['forecast'][0]['low'], 6), "℃");

            //查询数据库是否有该城市信息
            $weatherId = M("SchoolWeather")->where("region_id = '{$value['region_id']}'")->getField('id');
            if (!$weatherId) {
                //新增
                M("SchoolWeather")->add($weatherData);
            } else {
                //修改
                M("SchoolWeather")->where("id = $weatherId")->save($weatherData);
            }

        }

    }

    /**
     * 家庭作业语音播报
     *
     * */
    public function getSchoolHomework($c_id = '')
    {
        $whereCid = '';
        if ($c_id) {
            $whereCid['c_id'] = $c_id;
        }
        //获取班级的id
        $cIdArr = M('Class')->where($whereCid)->getField('c_id', true);

        $beginTime = date('Y-m-d 00:00:00');
        $endTime = date('Y-m-d 23:59:59');
        $whereStr['create_time'] = array(array('gt', $beginTime), array('lt', $endTime));

        $workData['time'] = date('Y-m-d H:i:s');
        foreach ($cIdArr as $cid) {
            $workData['c_id'] = $cid;
            //查询当天班级下的所有作业
            $whereStr['c_id'] = $cid;
            $homeworkArr = M('Homework')->where($whereStr)->getField('name', true);
            if ($homeworkArr) {
                //当天有作业
                $workData['homework'] = implode(',', $homeworkArr);
            } else {
                //当天没有作业
                $workData['homework'] = '暂无作业';
            }
            $workData['unicoderevwork'] = utf8_unicode($workData['homework'], 1);
            $workData['unicodework'] = utf8_unicode($workData['homework'], 0);

            //检测作业语音表中是否有该班级作业信息
            $homework_exist = M('SchoolHomework')->where("c_id = '{$cid}'")->getField('id');
            if (!$homework_exist) {
                //新增
                M('SchoolHomework')->add($workData);
            } else {
                //编辑
                M('SchoolHomework')->where('c_id = ' . $cid)->save($workData);
            }

        }
    }

    /**
     * 补全空白时间段广告浏览/点击记录
     * */
    public function addBannerLog()
    {
        //获取所有广告id
        $bannerId = M("Banner")->field('id,region_id')->select();

        $now = date('Y-m-d H:00:00');

        foreach ($bannerId as $key => $value) {
            $whereStr['adid'] = $value['id'];
            $whereStr['type'] = 1;
            $whereStr['ntime'] = array(array('gt', $now), array('lt', date('Y-m-d H:59:59')));

            //检测是否有展示量
            $is_show = M("StaticsUion")->where($whereStr)->getField('media_id');

            //检测是否有点击量
            $whereStr['type'] = 2;
            $is_click = M("StaticsUion")->where($whereStr)->getField('media_id');

            //补全浏览记录
            $data['adid'] = $value['id'];
            $data['region_id'] = $value['region_id'];
            $data['ntime'] = date('Y-m-d H:i:s');
            if (!$is_show) {
                $data['type'] = 1;
                M("Statics_10")->add($data);
            }
            //补全点击记录
            if (!$is_click) {
                $data['type'] = 2;
                M("Statics_10")->add($data);
            }
        }
    }

    /**
     * 广告位地址展示
     *
     * */
    public function showAdPosition()
    {
        $AdPosition = M("Banner");
        $whereStr['is_effect'] = 1;
        $whereStr['ad_type'] = 2;
        $whereStr['begin_time'] = array('LT', date('Y-m-d H:i:s'));
        $whereStr['end_time'] = array('GT', date('Y-m-d H:i:s'));
        $data = $AdPosition->field("img,url,position")->where($whereStr)->select();

        $AdInfo = array(
            'top1' => '',
            'top2' => '',
            'right1' => '',
            'right2' => '',
            'bottom1' => '',
            'bottom2' => '',
        );
        foreach ($data as $key => $value) {
            switch ($value['position']) {
                case 1:
                    $AdInfo['top1'] = $value;
                    break;
                case 2:
                    $AdInfo['top2'] = $value;
                    break;
                case 3:
                    $AdInfo['right1'] = $value;
                    break;
                case 4:
                    $AdInfo['right2'] = $value;
                    break;
                case 5:
                    $AdInfo['bottom1'] = $value;
                    break;
                case 6:
                    $AdInfo['bottom2'] = $value;
                    break;
            }
        }
        //添加到计数器
        $this->bannerCounts('AdPositionBanner');
        $this->success($AdInfo);
    }

    //商城广告
    public function showAdShop()
    {
        $AdPosition = M("Banner");
        $whereStr['is_effect'] = 1;
        $whereStr['ad_type'] = 3;
        $whereStr['begin_time'] = array('LT', date('Y-m-d H:i:s'));
        $whereStr['end_time'] = array('GT', date('Y-m-d H:i:s'));
        $data = $AdPosition->field("img,url")->where($whereStr)->select();
        //添加到计数器
        $this->bannerCounts('AdShopBanner');
        $this->success($data);
    }

    //广告请求计数器
    public function bannerCounts($model)
    {
        $userInfo = session('admin_user');
        $redis = redis_instance();

        $time = date('YmdH');
        $systemModel = C('SYSTEM_MODEL')[$userInfo['system_model']];
        $userType = C('USER_TYPE_EN')[$userInfo['type']];
        $userId = $userInfo['user_id'];

        $redis->incr($systemModel . ':' . $userType . ':' . $model . ':' . $time . ':' . $userId);
    }

    //短信下发学生卡设置命令
    public function sendMessage()
    {
        //查询状态为0的命令
        $StuCardCommand = M('StuCardCommand');
        $where['status'] = 0;
        $commandInfo = $StuCardCommand->where($where)->limit(10)->select();
        foreach ($commandInfo as $key => $value) {
            $phone = $value['stu_phone'];
            $sendSmsInfo = sendSms($phone, $value['order']);
            if ($sendSmsInfo == false) {
                $data1['status'] = 2;//发送失败
                $where1['stu_phone'] = $value['stu_phone'];
                $res = $StuCardCommand->where($where1)->save($data1);
            } else {
                $data1['status'] = 1;//发送成功
                $where1['stu_phone'] = $value['stu_phone'];
                $res = $StuCardCommand->where($where1)->save($data1);
            }
        }

    }

    /**
     *用户推送devicetoken添加
     * */
    public function addPushToken()
    {
        $status = I('post.status');//登录1 其他是2
        $data['user_name'] = I('post.user_name');
        $data['system_model'] = I('post.system_model');
        $data['user_id'] = I('post.user_id');
        $data['device_token'] = I('post.device_token');
        $data['mobile_model'] = I('post.mobile_model');//手机型号
        $data['push_name'] = I('post.push_name');//推送类型
        $data['create_time'] = date('Y-m-d H:i:s');//
        $data['user_type'] = I('post.user_type');// 3教师 4家长

        if ($status == 1) {
            M("MsgpushToken")
                ->where("user_id = '{$data['user_id']}'")
                ->delete();
            M("MsgpushToken")
                ->where("device_token = '{$data['device_token']}'")
                ->delete();

            $res = M("MsgpushToken")->add($data);
        } else {
            //注销 和 退出删除账号token
            $res = M("MsgpushToken")->where("user_id = '{$data['user_id']}'")->delete();
            $res = 1;
        }
        if ($res) {
            $this->success('success');
        } else {
            $this->error('error');
        }
    }

    /**
     * 聊天APP推送
     * */
    public function pushChatMsg()
    {
        //查询ispush=0
        $msgList = M("PushchatList")
            ->alias('a')
            ->join(" __CHATINFO__ b on a.chatid = b.chatid ")
            ->field('a.id,a.frm,a.pid,a.tid,a.type,b.ispush')
            ->where("a.ispush = 0")
            ->limit(100)
            ->select();

        foreach ($msgList as $key => $value) {
            //消息未读
            if ($value['ispush'] == 0) {
                //获取user_id //0家长给老师发 1 老师给家长发
                if ($value['frm'] == 0) {
                    $user_id = $value['tid'];
                    $fromName = M("user", 'think_')->where("user_id = '{$value['pid']}'")->getField('name');
                } else {
                    $user_id = $value['pid'];
                    $fromName = M("user", 'think_')->where("user_id = '{$value['tid']}'")->getField('name');
                }
                if ($value['type'] == 0) {
                    $msgtype = '[消息]';
                } elseif ($value['type'] == 1) {
                    $msgtype = '[文件]';
                } elseif ($value['type'] == 2) {
                    $msgtype = '[语音]';
                } elseif ($value['type'] == 3) {
                    $msgtype = '[视频]';
                }
                $msg = $fromName . '给您发来 ' . $msgtype;
                $pushArr = array(
                    'fsdx' => '1,1',
                    'u_id' => $user_id,
                    'ticker' => $msg,
                    'title' => $msg,
                    'text' => $msg,
                    'after_open' => 'go_custom',
                    'activity' => 'chat',
                    'id' => $user_id,
                );
                $this->sendAppMsg($pushArr);
                $data['ispush'] = 1;
            } else {
                //已读消息
                $data['ispush'] = 2;
            }
            //修改消息状态
            M("PushchatList")->where("id = '{$value['id']}'")->save($data);
        }


    }

    //过滤3分钟内无效的考勤记录
    public function filterSign()
    {
        $begin_today = date('Y-m-d') . ' 00:00:00';
        $end_today = date('Y-m-d') . ' 23:59:59';
        $whereStr = " and create_date > '{$begin_today}' and create_date < '{$end_today}' ";
        //查询is_effect为1的考勤记录
        $stu_info = M('Attendance')->field('stu_id,a_id')->where('is_effect = 1' . $whereStr)->limit(500)->group('stu_id')->select();

        foreach ($stu_info as $key => $value) {
            //进校考勤记录
            $signin_info = M('Attendance')
                ->field('at_id,stu_id,create_date,sign_type,rfidseq,signin_time')
                ->where("is_effect = 1 and stu_id = '{$value['stu_id']}' and sign_type = 1" . $whereStr)
                ->limit(500)
                ->select();

            //出校考勤记录
            $signout_info = M('Attendance')
                ->field('at_id,stu_id,create_date,sign_type,rfidseq,signout_time')
                ->where("is_effect = 1 and stu_id = '{$value['stu_id']}' and sign_type = 0" . $whereStr)
                ->limit(500)
                ->select();

            //考勤时间段
            $signin_range = M('WorkRule')->field('start_time,end_time')->where("a_id = '{$value['a_id']}'")->find();
            $beginTime = strtotime($signin_range['start_time']);
            $endTime = strtotime($signin_range['end_time']);
            //进校判断
            $signin_intime = 0;
            $sign_temp_time = 0;
            foreach ($signin_info as $key => $value) {
                $sign_temp_time = $this->getTimeStamp($value['create_date'], $value['rfidseq']);
                if ($signin_intime == 0) {
                    //判断$signin_intime 是否有初始值
                    $signin_intime = $sign_temp_time;
                } else {
                    //有初始值
                    if ($sign_temp_time - $signin_intime == 0) {
                        //当前记录在3分钟内,置为无效
                        $data['is_effect'] = 0;
                        M('Attendance')->where("at_id = '{$value['at_id']}'")->save($data);

                    } else {
                        //
                        $signin_intime = $sign_temp_time;
                    }
                }
                //判断考勤正常还是异常
                $signinTime = strtotime($value['signin_time']);
                if ($signinTime > $beginTime) {
                    //异常
                    $signData['signin_type'] = 0;
                    M('Attendance')->where("at_id = '{$value['at_id']}'")->save($signData);
                }
            }

            //出校判断
            $signin_intime_out = 0;
            $sign_temp_time_out = 0;
            foreach ($signout_info as $key1 => $value1) {
                $sign_temp_time_out = $this->getTimeStamp($value1['create_date'], $value1['rfidseq']);
                if ($signin_intime_out == 0) {
                    //$signin_intime_out 是否有初始值
                    $signin_intime_out = $sign_temp_time_out;
                } else {
                    //有初始值
                    if ($sign_temp_time_out - $signin_intime_out == 0) {
                        //当前记录在3分钟内,置为无效
                        $data['is_effect'] = 0;
                        M('Attendance')->where("at_id = '{$value1['at_id']}'")->save($data);

                    } else {
                        //
                        $signin_intime_out = $sign_temp_time_out;
                    }
                }
                //判断考勤正常还是异常
                $signoutTime = strtotime($value1['signout_time']);
                if ($signoutTime < $endTime) {
                    //异常
                    $signData['signin_type'] = 0;
                    M('Attendance')->where("at_id = '{$value1['at_id']}'")->save($signData);
                }
            }
        }
        //推送APP消息
        $msgList = M('Attendance')
            ->alias('a')
            ->join('__STUDENT__ b on a.stu_id = b.stu_id')
            ->field('a.at_id,a.sign_type,a.stu_id,b.stu_name')
            ->where('a.is_push = 0 and a.is_effect = 1 and  a.stu_id > 0')->select();

        $pushArr = array();//初始化队列数组
        $stuIdArr = array();//学生id数组
        foreach ($msgList as $key => $value) {
            if ($value['sign_type'] == 1) {
                $content = '签到提醒';
            } else {
                $content = '签退提醒';
            }
            //app消息推送 By:Meng Fanmin 2017.06.27
            if ($value['stu_id']) {
                $tmp = array(
                    'fsdx' => '0,1',
                    'u_id' => $value['stu_id'],
                    'ticker' => $value['stu_name'] . $content,
                    'title' => $value['stu_name'] . $content,
                    'text' => $value['stu_name'] . $content,
                    'after_open' => 'go_custom',
                    'activity' => 'attendance',
                    'id' => $value['at_id'],
                );
                $stuIdArr[] = $value['stu_id'];
                $pushArr[] = $tmp;
            }
            //推送成功
            $data2['is_push'] = 1;
            M('Attendance')->where("at_id = '{$value['at_id']}'")->save($data2);
        }
        //对消息队列进行发送
        //获取监护人user_id
        $pUserId = $this->getParUserId($stuIdArr);
        foreach ($pushArr as $key => $value) {
            $value['u_id'] = $pUserId[$value['u_id']];
            $this->sendAppMsg($value);
        }

    }

    //修复stu_id信息不合法的考勤 并推送消息
    public function filterTrail()
    {
        //推送APP消息
        $msgList = M('Attendance')
            ->alias('a')
            ->join('__STUDENT__ b on a.stu_id = b.stu_id')
            ->field('a.at_id,a.sign_type,a.stu_id,b.stu_name')
            ->where('a.is_push = 0 and a.is_effect = 1 and  a.stu_id > 0')->select();

        $pushArr = array();//初始化队列数组
        $stuIdArr = array();//学生id数组
        foreach ($msgList as $key => $value) {
            if ($value['sign_type'] == 1) {
                $content = '签到提醒';
            } else {
                $content = '签退提醒';
            }
            //app消息推送 By:Meng Fanmin 2017.06.27
            if ($value['stu_id']) {
                $tmp = array(
                    'fsdx' => '0,1',
                    'u_id' => $value['stu_id'],
                    'ticker' => $value['stu_name'] . $content,
                    'title' => $value['stu_name'] . $content,
                    'text' => $value['stu_name'] . $content,
                    'after_open' => 'go_custom',
                    'activity' => 'attendance',
                    'id' => $value['at_id'],
                );
                $stuIdArr[] = $value['stu_id'];
                $pushArr[] = $tmp;
            }
            //推送成功
            $data2['is_push'] = 1;
            M('Attendance')->where("at_id = '{$value['at_id']}'")->save($data2);
        }
        //对消息队列进行发送
        //获取监护人user_id
        $pUserId = $this->getParUserId($stuIdArr);
        foreach ($pushArr as $key => $value) {
            $value['u_id'] = $pUserId[$value['u_id']];
            $this->sendAppMsg($value);
        }

    }

    //硬件考勤推送处理
    public function attendanceMsg()
    {
        $today = date('Y-m-d');
        //获取未推送的考勤列表
        $whereStr['is_push'] = 0;
        $whereStr['create_date'] = array('between', array($today . ' 00:00:00', $today . ' 23:59:59'));
        $list = M('Attendance')
            ->field('at_id,a_id,stu_id,sign_type,create_date,imei')
            ->where($whereStr)
            ->limit(1000)
            ->select();
        $redis = redis_instance();
        $dayStr = date('Y-m-d');
        foreach ($list as $key => $value) {
            $stu_info = get_stu_info($value['imei']);
            if ($value['sign_type'] == 1) {
                $content = '签到提醒';
                //查询一天的缓存数据
                $redis->sAdd('Signin:' . $dayStr . ':s:' . $stu_info['s_id'] . ':a:' . $stu_info['a_id'] . ':g:' . $stu_info['g_id'] . ':c:' . $stu_info['c_id'], $value['imei']);
                $redis->expire('Signin:' . $dayStr . ':s:' . $stu_info['s_id'] . ':a:' . $stu_info['a_id'] . ':g:' . $stu_info['g_id'] . ':c:' . $stu_info['c_id'], 2592000);//30天
            } else {
                //移除当前集合中的值
                $redis->sRem('Signin:' . $dayStr . ':s:' . $stu_info['s_id'] . ':a:' . $stu_info['a_id'] . ':g:' . $stu_info['g_id'] . ':c:' . $stu_info['c_id'], $value['imei']);
                $content = '签退提醒';
            }
            //app消息推送 By:Meng Fanmin 2017.06.27
            if ($value['imei']) {
                $pushArr[] = array(
                    'fsdx' => '0,1',
                    'u_id' => $stu_info['parent_id'],
                    'ticker' => $stu_info['stu_name'] . $content,
                    'title' => $stu_info['stu_name'] . $content,
                    'text' => $stu_info['stu_name'] . $content,
                    'after_open' => 'go_custom',
                    'activity' => 'attendance',
                    'id' => $value['at_id'],
                );
            }
            //推送成功
            $data2['is_push'] = 1;
            M('Attendance')->where("at_id = '{$value['at_id']}'")->save($data2);
        }

        //对消息队列进行发送
        foreach ($pushArr as $key => $value) {
            $this->sendAppMsg($value);
        }

    }

    public function getTimeStamp($Year, $Hour)
    {
        $timeStr = date('Y-m-d', strtotime($Year)) . ' ' .
            substr($Hour, 0, 2) . ':' .
            substr($Hour, 2, 2) . ':' .
            substr($Hour, 4, 2);
        return strtotime($timeStr);
    }

    //保存Redis请求数据到MySQL
    public function saveRedisData()
    {
        //删除7天前的key
        $this->delKeys();
        //家长端和老师端APP使用情况
        $UseData = reqURL('http://school.xinpingtai.com/a.php/Api/Counts/getAppCountsT');
        //功能统计getAppModuleCounts
        $ModuleData = reqURL('http://school.xinpingtai.com/a.php/Api/Counts/getAppModuleCountsT');
        $UseDataStr = json_encode($UseData['data']);
        $ModuleDataStr = json_encode($ModuleData['data']);

        $use_data['contents'] = $UseDataStr;
        $use_data['type'] = 1;
        $use_data['date'] = date('Y-m-d H:i:s');

        $model_data['contents'] = $ModuleDataStr;
        $model_data['type'] = 2;
        $model_data['date'] = date('Y-m-d H:i:s');
        $data = array($use_data, $model_data);

        M('AppLog')->addAll($data);
    }

    //删除7天前的Redis键
    private function delKeys()
    {
        $delDate = date('Ymd', strtotime('-7 days'));
        $redis = redis_instance();
        $keys = $redis->keys('*' . $delDate . '*');
        $redis->del($keys);
    }

    /**
     * lbs定位信息处理
     *
     * */
    public function lbs2gpsAdd()
    {
        //学生信息
        $data['imei'] = I('get.imei');
        //基站定位数据
        $lbsdata['mcc'] = I('get.mcc');
        $lbsdata['mnc'] = intval(I('get.mnc'));
        $lbsdata['lac'] = I('get.lac');
        $lbsdata['cid'] = I('get.cid');
        if (!$lbsdata['lac'] || !$lbsdata['cid']) {
            $this->error('param error!');
        }
        $data['mcc'] = I('get.mcc');
        $data['mnc'] = I('get.mnc');
        $data['lac'] = I('get.lac');
        $data['cid'] = I('get.cid');
        $data['num'] = I('get.num');
        $data['ta'] = I('get.ta');
        $data['rxlev'] = I('get.rxlev');
        $data['power'] = I('get.power');
        $data['signal1'] = I('get.signal1');
        $data['gpsnum'] = I('get.gpsnum');
        //公共信息
        $data['create_time'] = date('Y-m-d H:i:s');
        $data['type'] = 0;//0gps 1lbs

        //$data['mnc'];//00联通定位
        //在位置库表lbs2gps中查询经纬度信息
        $gpsData = M('Lbs2gps')->field('longti,lati,addr')->where($lbsdata)->find();

        //未取到经纬度数据
        if (!$gpsData) {
            //判断是否已经查询过数据接口
            $place_status = M('Trail')
                ->where("mcc = '{$data['mcc']}' and lac = '{$data['lac']}' and cid = '{$data['cid']}' and type = 2")
                ->getField('type');
            //之前未获取定位信息
            if ($place_status) {
                $place['stats'] = 0;
                $place['reason'] = '未获取定位信息';
            } else {
                //调用lbs基站接口
                $place = getLongtiLati($data);
            }

            //未获取位置信息
            if ($place['stats'] == 0 || $place['longti'] == '') {
                $data['type'] = 2;//未获取位置
            } else {
                //获取位置信息
                //将位置信息存入lbs2gps
                $gpsData['mnc'] = $data['mnc'];
                $gpsData['mcc'] = 460;
                $gpsData['lac'] = $lbsdata['lac'];
                $gpsData['cid'] = $lbsdata['cid'];
                $gpsData['longti'] = $place['longti'];
                $gpsData['lati'] = $place['lati'];
                $gpsData['addr'] = $place['addr'];
                M('Lbs2gps')->add($gpsData);

                $data['longitude'] = $gpsData['longti'];
                $data['latitude'] = $gpsData['lati'];
            }

        } else {
            //在位置库中存在经纬度
            $data['longitude'] = $gpsData['longti'];
            $data['latitude'] = $gpsData['lati'];
        }

        //进出校判断
        //        if ($data['latitude']) {
        //            $this->AttendanceArith($data, '1');
        //        }
        $trail_id = M('Trail')->add($data);
        if (!$trail_id) {
            $this->error('fail');
        }
        $this->success('ok');
    }

    /**
     *定时任务 围栏定位考勤判断
     * */
    public function trailAttendance()
    {
        ini_set('max_execution_time', '0');

        $redis = redis_instance();

        //获取is_select=0的记录
        $trailList = M('Trail')
            ->field('tr_id,longitude,latitude,type,imei,create_time,is_sos,is_lo_bat,power')
            ->where("is_select = 0 ")
            ->limit(1000)
            ->select();


        $trId = '';

        foreach ($trailList as $k => $v) {
            $trId[] = $v['tr_id'];
        }
        //优先修改定位状态
        $where['tr_id'] = array('in', $trId);
        $data['is_select'] = 1;
        M('Trail')->where($where)->save($data);
        $timeStr = date('YmdH');
        foreach ($trailList as $key => $value) {
            //判断定位类型 类型 0:GPS 1:LBS 2:未获取位置 3:wgs84
            if ($value['type'] != 2) {//合法的数据
                if ($value['type'] == 3) {//wgs84数据
                    //wgs84转gps
                    $point = $this->origin2gps($value['longitude'], $value['latitude']);
                    //获取GPS经纬度
                    $value['longitude'] = $point[0];
                    $value['latitude'] = $point[1];
                }
                //调用围栏考勤算法
                $res = $this->AttendanceArith($value);
            }
            $stu_info = get_stu_info($value['imei']);
            //设置redis缓存
            $redis->sAdd('Online:' . $timeStr . ':s:' . $stu_info['s_id'] . ':a:' . $stu_info['a_id'] . ':g:' . $stu_info['g_id'] . ':c:' . $stu_info['c_id'], $value['imei']);
            $redis->expire('Online:' . $timeStr . ':s:' . $stu_info['s_id'] . ':a:' . $stu_info['a_id'] . ':g:' . $stu_info['g_id'] . ':c:' . $stu_info['c_id'], 2592000);//30天
            $lo_bat_err = 0;
            if($value['power'] < 20 && $value['power'] > 10){
                $now = date('Y-m-d');
                $lo_bat = M('WarningMessage')->where("create_time like '{$now}%' and war_type = 2")->getField('wm_id');
                if(!$lo_bat){
                    $lo_bat_err = 1;
                }
            }
            //警报
            if ($value['is_sos'] || $value['is_lo_bat'] || $lo_bat_err) {
                $wm_data['longitude'] = $value['longitude'];
                $wm_data['latitude'] = $value['latitude'];
                $wm_data['create_time'] = $value['create_time'];
                $wm_data['tr_id'] = $value['tr_id'];
                if ($value['is_sos']) {
                    $wm_data['war_type'] = 1;
                }
                if ($value['is_lo_bat']) {
                    $wm_data['war_type'] = 2;
                }
                if($lo_bat_err){
                    $wm_data['war_type'] = 2;
                }
                $wm_data['imei'] = $value['imei'];

                M('WarningMessage')->add($wm_data);
            }
        }
    }

    public function trailAttendance1()
    {
        $this->trailAttendance();
    }

    public function trailAttendance2()
    {
        $this->trailAttendance();
    }

    public function trailAttendance3()
    {
        $this->trailAttendance();
    }

    public function trailAttendance4()
    {
        $this->trailAttendance();
    }

    public function trailAttendance5()
    {
        $this->trailAttendance();
    }

    /**
     *考勤信息算法 By:Meng
     * @param $gpsInfo array gps坐标信息
     * */
    private function AttendanceArith($gpsInfo = array())
    {
        if (!$gpsInfo['imei'] || !$gpsInfo['longitude'] || !$gpsInfo['latitude']) {
            return false;
        }
        $gpsInfo['a_id'] = M('Student')->cache(true)->where("imei_id = '{$gpsInfo['imei']}'")->getField('a_id');

        //获取家长围栏信息
        if (!S('SafeArea:' . $gpsInfo['imei'])) {
            $SafeTmp = M('SafetyRegion')->where("imei = '{$gpsInfo['imei']}'")->getField('point', true);
            S('SafeArea:' . $gpsInfo['imei'], $SafeTmp);
        }
        $SafeArr = S('SafeArea:' . $gpsInfo['imei']);//安全围栏


        //获取学校围栏信息
        if($gpsInfo['a_id'] > 0){
            if (!S('aid:' . $gpsInfo['a_id'])) {
                $mapTmp = M('GpsAttendanceSet')->where("a_id = '{$gpsInfo['a_id']}'")->getField('point');

                S('aid:' . $gpsInfo['a_id'], $mapTmp);
            }
            $mapStr = S('aid:' . $gpsInfo['a_id']);//考勤围栏
        }else{
            $mapStr = false;
        }



        //$mapStr = '116.291226,39.841079,116.292344,39.841165,116.292443,39.840625,116.291302,39.840476|116.293108,39.840653,116.293691,39.840715,116.293817,39.840431,116.293265,39.840358|116.293161,39.840822,116.2941,39.840937,116.294253,39.840068,116.292739,39.840171';
        if (!$mapStr && !$SafeArr) {
            return false;
        }

        $mapArr = explode('|', $mapStr);
        if (!$mapArr[2] && !$SafeArr) {
            return false;
        }


        //已知GPS坐标点 s_id
        $point = $gpsInfo['longitude'] . ',' . $gpsInfo['latitude'];

        //安全围栏
        if ($SafeArr) {//设置了安全围栏
            $safeRes = array();
            foreach ($SafeArr as $ky => $ve) {
                $tmp = is_inside($point, $ve);
                $safeRes[] = $tmp;
                if ($tmp) {
                    break;
                }
            }
            $res = in_array(1, $safeRes);
            if (!$res) {//没在安全区域
                $wm_data['tr_id'] = $gpsInfo['tr_id'];
                $wm_data['war_type'] = 4;
                $wm_data['imei'] = $gpsInfo['imei'];
                $wm_data['longitude'] = $gpsInfo['longitude'];
                $wm_data['latitude'] = $gpsInfo['latitude'];
                $wm_data['type'] = $gpsInfo['type'];
                $wm_data['create_time'] = date('Y-m-d H:i:s');

                M('WarningMessage')->add($wm_data);
            }
        }
        //考勤围栏
        if ($mapStr) {
            //学生信息
            $date = date('Y-m-d');
            //是否签到
            $whereStr['create_time'] = array('between', array($date . " 00:00:00", $date . " 23:59:59"));
            $whereStr['imei'] = $gpsInfo['imei'];
            //签到详情 1进 0出
            //$SignInInfo = M('GpsAttendance')->where($whereStr)->getField('sign_type', true);
            $SignInInfo = M('GpsAttendance')
                ->field('sign_type,create_time')
                ->order('gt_id desc')
                ->where($whereStr)
                ->find();


            //加入lbs和gps算法
            if ($gpsInfo['type'] == 0) {//lbs算法部分
                //是否在围栏里
                $isInsideA = is_inside($point, $mapArr[0]);
                //当天无签到行为
                if (!$SignInInfo) {
                    //在A围栏里 add
                    if ($isInsideA == 1) {
                        $data['sign_type'] = 1;
                    }
                } else {//当天有签到行为
                    //上一次签到状态
                    $prev = $SignInInfo['sign_type'];
                    $nowStr = time();
                    $provStr = strtotime($SignInInfo['create_time']); //上次签到时间戳
                    $d_value = $nowStr - $provStr;
                    //上次签到为签到 并且 在圈外 签退add
                    if ($d_value > 300) {
                        if ($prev == 1) {
                            $isInsideB = is_inside($point, $mapArr[1]);
                            if ($isInsideB == 2) {
                                $data['sign_type'] = 0;
                            }
                        } elseif ($prev == 0 && ($isInsideA == 1)) {
                            //上次签到为签退 并且 在圈内 签到add
                            $data['sign_type'] = 1;
                        }
                    }

                }
            } else {//gps算法部分
                //是否在围栏里
                $isInsideA = is_inside($point, $mapArr[0]);
                //当天无签到行为
                if (!$SignInInfo) {
                    //在A围栏里 add
                    if ($isInsideA == 1) {
                        $data['sign_type'] = 1;
                    }
                } else {//当天有签到行为
                    //上一次签到状态
                    $prev = $SignInInfo['sign_type'];
                    $nowStr = time();
                    $provStr = strtotime($SignInInfo['create_time']); //上次签到时间戳
                    $d_value = $nowStr - $provStr;
                    //上次签到为签到 并且 在圈外 签退add
                    if ($d_value > 300) {
                        if ($prev == 1) {
                            $isInsideB = is_inside($point, $mapArr[1]);
                            if ($isInsideB == 2) {
                                $data['sign_type'] = 0;
                            }
                        } elseif ($prev == 0 && ($isInsideA == 1)) {
                            //上次签到为签退 并且 在圈内 签到add
                            $data['sign_type'] = 1;
                        }
                    }
                }
            }
            //数据写入部分
            if (isset($data['sign_type'])) {

                $data['imei'] = $gpsInfo['imei'];
                $data['type'] = $gpsInfo['type'];//0:lbs判定的考勤 3:GPS判定的考勤
                $data['create_time'] = $gpsInfo['create_time'];
                $data['longitude'] = $gpsInfo['longitude'];
                $data['latitude'] = $gpsInfo['latitude'];
                $data['tr_id'] = $gpsInfo['tr_id'];

                M('GpsAttendance')->add($data);
            }
        }


    }

    //原始坐标转gps算法
    private function origin2gps($lng, $lat)
    {
        if (!$lng || !$lat) {
            return false;
        }
        $lngD = substr($lng, 0, 3);
        $latD = substr($lat, 0, 2);
        $lngF = substr($lng, 3, 10) * 1000000 / 60 / 1000000;
        $latF = substr($lat, 2, 10) * 1000000 / 60 / 1000000;
        $arr = array(
            $lngD + $lngF,
            $latD + $latF,
        );
        return ($arr);
    }

    // 创建trail分表数据
    public function createTrailData()
    {
        $Trail = M('Trail');
        $begintime = date('Y-m-d', strtotime('-15 day'));
        $where['create_time'] = array('LT', $begintime);
        $trailList = $Trail->where($where)->limit(1000)->select();
        if ($trailList) {
            foreach ($trailList as $key => $value) {
                $id = get_trail_id($value['imei']);
                $data = $Trail->create($value);
                $ret = M('Trail_' . $id)->add($data);
                if ($ret) {
                    $where['tr_id'] = $value['tr_id'];
                    $Trail->where($where)->delete();
                }
            }
        }
    }


}