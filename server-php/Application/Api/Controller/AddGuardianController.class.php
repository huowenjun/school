<?php
/*
*   添加第二监护人
*by yumeng date 20170413
*/

namespace Api\Controller;

use Api\Controller\BaseController;
use Think\Model;

class AddGuardianController extends BaseController
{
    public function add()
    {

        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        // $token = md5('/Api/Homework/add'.date('Ymd').'ef1f8332abcf11e688eb00163e006cec');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            // $this->error('接口访问不合法');
        }
        $User = D('AdminUser');//用户
        $StudentParent = D('StudentParent');//家长
        $StuCardSet = D('StuCardSet');
        $arrUids = I('post.stu_id');

        $stu_id = explode(',', $arrUids);
        $phone = I('post.phone');//家长手机号
        $data['phone'] = $phone;
        $username = I('post.username');
        $data['username'] = $username;
        $data['name'] = I('post.name');//家长姓名
        $data['sex'] = I('post.sex');
        $data['email'] = I('post.email');
        $data['address'] = I('post.address');
        $data['group_id'] = 4;
        $data['type'] = 4;
        $data['password'] = "e10adc3949ba59abbe56e057f20f883e";
        $data['create_time'] = date('Y-m-d H:i:s');
        $User->startTrans();
        if (!$User->create($data)) {
            $this->error($User->getError());
        } else {
            //添加用户表
            $ret1 = $User->add($data);
            if ($ret1) {
                $data1['relation'] = I('post.relation');
                $data1['address'] = I('post.address');
                $data1['work_unit'] = I('post.work_unit');
                $data1['family_tel'] = I('post.family_tel');
                $data1['u_id'] = $ret1;
                //添加父母详情表
                $ret2 = $StudentParent->add($data1);
                if ($ret2) {

                    foreach ($stu_id as $key => $value) {
                        $where2['stu_id'] = $value;
                        //学生监护人id
                        $Parr = D('StudentGroupAccess')->where($where2)->getField('sp_id', true);
                        //监护人个数
                        $parentCount = count($Parr);
                        if ($parentCount < 10) {
                            $data2['stu_id'] = $value;
                            $data2['sp_id'] = $ret2;
                            //添加到监护人表
                            $ret3 = D('StudentGroupAccess')->add($data2);
                        } else {
                            $User->rollback();
                            $this->error("对不起,该学生监护人已超过10人,无法添加");
                        }
                    }
                    //将监护人电话存为白名单,并短信通知学生卡 start
                    $where1['stu_id'] = array('IN', $stu_id);
                    $studentInfo = D('Student')->where($where1)->select();

                    $whiteStr = "";
                    //修改白名单设置短信下发 By:Meng 6.19

                    foreach ($studentInfo as $key => $value) {
                        $dataCard = $StuCardSet->create($value);
                        $dataCard['imei'] = $value['imei_id'];
                        $dataCard['d_type'] = "whitelist";
                        $where3['d_type'] = $dataCard['d_type'];
                        //$where3['stu_id'] = array('IN', $stu_id);
                        $where3['stu_id'] = $value['stu_id'];
                        //大是大非:13225255656,发给:13225255656,:,:,:,:,:,:,:,:
                        $whiteInfo = $StuCardSet->where($where3)->getField('d_value');
                        if (!$whiteInfo) {
                            $whiteInfo = '';
                        }

                        $whiteArr = explode(',', $whiteInfo);

                        $newArr = array();
                        foreach ($whiteArr as $k => $v) {
                            if ($v != ':') {
                                $newArr[] = $v;
                            }
                        }

                        $whiteStr = implode(',', $newArr);

                        $whiteStr .= "," . $data['name'] . ":" . $data['phone'];
                        $whiteStr = trim($whiteStr, ',');
                        $dataCard['d_value'] = $whiteStr;
                        $res = $StuCardSet->where($where3)->save($dataCard);
                        //家长手机号 start
                        $arr = explode(',', $whiteStr);
                        //获取监护人电话数组

                        $par_phone = array();
                        foreach ($arr as $key => $value1) {
                            $tmp = explode(':', $value1);
                            $par_phone[] = $tmp[1];
                        }

                        $phoneArr = array_pad($par_phone, 10, '');

                        $phonestr = '';
                        for ($i = 1; $i <= 10; $i++) {
                            $phonestr .= $i . ',' . $phoneArr[$i - 1] . ',,';
                        }
                        $phone1 = rtrim($phonestr, ',');
                        $phoneStr = '*HQ,' . $value['imei_id'] . ',WLALL,' . date('His') . ',' . $phone1 . ',#';

                        $dataCard['stu_phone'] = $value['stu_phone'];
                        $dataCard['d_type'] = 'whitelist';
                        $dataCard['imei'] = $value['imei_id'];
                        $dataCard['status'] = 1;
                        $dataCard['create_time'] = date("Y-m-d H:i:s");
                        $dataCard['order'] = $phoneStr;

                        if ($res) {
                            M('StuCardCommand')->add($dataCard);
//                            $sendSmsInfo = $this->sendSms($value['stu_phone'], $phoneStr);
                        }
                    }
                    //将监护人电话存为白名单,并短信通知学生卡 end
                    $User->commit();
                    //添加视频通讯录 BY:Meng Fanmin 2017.06.15
                    video_add_user($ret1);
                    easemob_add_user($ret1);
                    $this->success('添加成功');

                } else {
                    $User->rollback();
                    $this->error('添加失败');
                }
            } else {
                $User->rollback();
                $this->error('添加失败');

            }
        }
    }


}