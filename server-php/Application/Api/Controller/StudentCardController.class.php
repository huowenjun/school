<?php
/*
* 学生卡设置
*/

namespace Api\Controller;

use Api\Controller\BaseController;
use Think\Model;

class StudentCardController extends BaseController
{

    //设置sos，白名单和监听号码
    public function set_cardphone()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/MyClass/query'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法！');
        }

        $where['stu_id'] = I('post.stu_id');
        $where['d_type'] = I('post.typeFlag');//类型
        $data['s_id'] = I('post.s_id');
        $data['a_id'] = I('post.a_id');
        $data['g_id'] = I('post.g_id');
        $data['c_id'] = I('post.c_id');
        $data['imei'] = I('post.imei');
        $data['stu_id'] = I('post.stu_id');
        $StuCardSet = D('StuCardSet');
        $data['d_type'] = I('post.typeFlag');
        $deviceInfo = D('student')->where(array('imei_id' => $data['imei']))->find();
        if(!$data['imei']){
            $this->error('非法请求');
        }
        if (empty($deviceInfo['stu_phone'])) {
            $this->error('学生卡未绑定电话号码！');
        }
        $StuCardCommand = M('StuCardCommand');
        $dataCard['stu_phone'] = $deviceInfo['stu_phone'];
        $dataCard['d_type'] = $data['d_type'];
        $dataCard['imei'] = $data['imei'];
        $dataCard['status'] = 0;
        $dataCard['create_time'] = date("Y-m-d H:i:s");
        $whereStr = array(
            'imei_id' => $data['imei'],
            'stu_id' => $deviceInfo['stu_id'],
        );
        $stuList = D('Student')->field('stu_id,s_id,a_id,g_id,c_id,stu_no,stu_name,imei_id,sex,stu_phone,devicetype')->where($whereStr)->find();//devicetype设备类型1 is card, 2 is wetch
        $devicetype = $stuList['devicetype'];
        if ($devicetype == 1) {//学生卡
            switch ($data['d_type']) {
                case 'sos': // sos号码
                    $data['d_value'] = I('post.sos');
                    $where['stu_id'] = $data['stu_id'];
                    $where['d_type'] = $data['d_type'];

                    $contents = '*HQ,' . $data['imei'] . ',S8,' . date('His') . ',' . $data['d_value'] . '#';
                    $dataCard['order'] = $contents;
                    taskRedis($data['imei'], $dataCard['order']);
                    break;
                case 'whitelist': // 白名单
                    $data['d_value'] = I('post.whitelist');
                    //xuyujie:15652933152,张豪:18816274777,孔1:17600200507,孔二愣子:13652515859
                    //获取白名单详情
                    $whiteInfo = explode(',', $data['d_value']);
                    foreach ($whiteInfo as $key => $value) {
                        $tmp = explode(':', $value);
                        $phone[] = $tmp[1];
                    }
                    $phoneArr = array_pad($phone, 10, '');
                    $phonestr = '';
                    for ($i = 1; $i <= 10; $i++) {
                        $phonestr .= $i . ',' . $phoneArr[$i - 1] . ',,';
                    }
                    $phone1 = rtrim($phonestr, ',');
                    //$str = 1,18511214006,,2,18511214003
                    $contents = '*HQ,' . $data['imei'] . ',WLALL,' . date('His') . ',' . $phone1 . ',#';
                    $dataCard['order'] = $contents;
                    taskRedis($data['imei'], $dataCard['order']);
                    break;
                case 'monitor': // 监听号码
                    $data['d_value'] = I('post.monitor');
                    $monitorInfo = explode(',', $data['d_value']);
                    $contents = '*HQ,' . $data['imei'] . ',MONITOR,' . date('His') . ',' . $monitorInfo[1] . '#';
//                $sendSmsInfo = $this->sendSms($deviceInfo['phone'], $contents);
                    $dataCard['order'] = $contents;
//                if ($sendSmsInfo == false) {
//                    $dataCard['status'] = 2;
//                    $mse = "监听号码设置失败";
//                }
                    taskRedis($data['imei'], $dataCard['order']);
                    break;
                case 'debug':
                    $sendSmsInfo = false;
                    $contents = '*HQ,' . $data['imei'] . ',S8,' . date('His') . ',' . $data['d_value'] . '#';
                    taskRedis($data['imei'], $dataCard['order']);
                    break;
            }
        }elseif ($devicetype == 2) {//手表
            switch ($data['d_type']) {
                case 'whitelist': // 白名单
                    //111:18511214006,:,:,:,:,:,:,:,:,:
                    $data['d_value'] = I('post.whitelist');
                    $whiteInfo = explode(',', $data['d_value']);
                    foreach ($whiteInfo as $key => $value) {
                        $phone_info = explode(':', $value);
                        if ($key < 3) {//SOS设置
                            $i = $key + 1;
                            $sos_info = '[3G*' . $data['imei'] . '*0010*SOS' . $i . ',' . $phone_info[1] . ']';
                            taskRedis($data['imei'], $sos_info);
                        }
                        //电话本设置 [3G*8800000015*len*PHB,号码,名字,号码,名字,号码,名字,号码,名字,号码,名字,号码,名字]
                        $phone_book_arr[] = $phone_info[1] . ',' . utf8_unicode($phone_info[0]);
                    }
                    $phone_book_str = 'PHB,' . implode(',', $phone_book_arr);
                    $phone_book_len_16 = get_len_16($phone_book_str);

                    //存入StuCardCommand表中
                    //[3G*8800000015*len*PHB,号码,名字,号码,名字,号码,名字,号码,名字,号码,名字,号码,名字]
                    $dataCard['order'] = '[3G*' . $data['imei'] . '*' . $phone_book_len_16 . '*' . $phone_book_str . ']';//电话本
                    taskRedis($data['imei'], $dataCard['order']);
                    break;

                case 'monitor': // 监听号码 中心号码
                    $data['d_value'] = I('post.monitor');
                    $monitorInfo = explode(',', $data['d_value']);
                    //存入StuCardCommand表中 [3g*8800000015*0012*CENTER,00000000000]
                    $dataCard['order'] = '[3G*' . $data['imei'] . '*0012*CENTER,' . $monitorInfo[1] . ']';
                    taskRedis($data['imei'], $dataCard['order']);
                    break;
            }
        }
        $res = $StuCardCommand->add($dataCard);

        if ($res == false) {
            $arr['phone'] = $deviceInfo['phone'];
            $arr['content'] = $contents;
            $this->error($arr);
        } else {
                $resinfo = $StuCardSet->where($where)->find();
                if (!empty($resinfo)) {
                    $ret = $StuCardSet->where($where)->save($data);
                } else {
                    $ret = $StuCardSet->add($data);
                }
                $content = '设置成功';
                if ($ret === false) {
                    D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getUserType(), $this->getUserType(), 1, 2);
                    $this->error($StuCardSet->getError());
                } else {
                    D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getUserType(), $this->getUserType(), 1, 1);
                    $this->success($content);
                }
            }


    }

    //根据stu_id获取学生卡信息
    public function get_cardphone()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //$token = md5('/Api/MyClass/query'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法！');
        }

        $data['stu_id'] = I('post.stu_id');
        $where['stu_id'] = $data['stu_id'];
        $where['d_type'] = array('in', array('sos', 'phb','phb2', 'monitor'));
        $deviceInfo = D('StuCardSet')->field('d_type,d_value')->where($where)->select();
        foreach ($deviceInfo as $key => $value)
        {
            if($deviceInfo[$key]['d_type']==='phb2')
            {
                $phb2=$deviceInfo[$key]['d_value'];
                $phb_num=$key;
            }
            if($deviceInfo[$key]['d_type']==='phb')
            {
                $i=$key;
            }

        }
        if($phb_num!=0){
            $deviceInfo[$i]['d_value']=$deviceInfo[$i]['d_value'].','.$phb2;
        }

        $this->success($deviceInfo);

    }

}