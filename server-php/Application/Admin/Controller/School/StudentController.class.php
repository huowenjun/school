<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-学生信息
 */

namespace Admin\Controller\School;

use Admin\Controller\BaseController;

class StudentController extends BaseController
{
    /**
     *学生信息
     */

    public function index()
    {

        $this->display();
    }

    //查询数据
    public function query()
    {
        $ids = $this->authRule();
        $where = "";
        if ($this->getType() == 1) {
            $where['s_id'] = $ids['s_id'];
        } elseif ($this->getType() == 3) {
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
            $classList = $this->getTeacherClass($ids['t_id']);
            $where['g_id'] = array('IN', $classList['g_id']);//$ids['a_id'];
            $where['c_id'] = array('IN', $classList['c_id']);
        } elseif ($this->getType() == 4) {
            $where['stu_id'] = array('IN', $ids['stu_id']);
        } else {
            $arr = $_GET;
            $sthInfo = getSchoolId($arr);
            $where['s_id'] = array('IN', $sthInfo);
        }

        $s_id = I('get.s_id');
        $a_id = I('get.a_id');
        $g_id = I('get.g_id');
        $stu_no = I('get.stu_no');
        $stu_name = I('get.stu_name');
        // var_dump($stu_no);die;
        $imei_id = I('get.imei_id');
        $parent_name = I('get.parent_name');//家长姓名
        $phone = I('get.phone'); //联系方式
        $c_id = I('get.c_id');
        $authId = $this->authRule();
        // if($authId['s_id']>0){
        //   $where['s_id'] = $authId['s_id'];
        // }
        //  var_dump($where['s_id']);
        $pagesize = I("get.pagesize", $this->PAGE_SIZE);
        $page = I('get.page');
        $sort = I('get.sort', 'stu_id');
        $order = $sort . ' ' . I('get.order', 'desc');
        $Student = D('Student');
        // if(!empty($parent_name)){
        // 	$where['parent_name'] =array("like","%$parent_name%");;
        // }

        if (!empty($s_id)) {
            $where['s_id'] = $s_id;
        }
        if (!empty($a_id)) {
            $where['a_id'] = $a_id;
        }
        if (!empty($g_id)) {
            $where['g_id'] = $g_id;
        }

        if ($stu_no != '') {
            $where['stu_no'] = array("like", "%$stu_no%");
        }
        if (!empty($c_id)) {
            $where['c_id'] = $c_id;
        }
        if ($stu_name != '') {
            $where['stu_name'] = array("like", "%$stu_name%");
        }
        if ($imei_id != '') {
            $where['imei_id'] = array("like", "%$imei_id%");
        }
        //家长姓名查询
        if ($parent_name != '') {

            $where1['name'] = array("like", "%$parent_name%");
            $u_id = '';
            $userId = D('AdminUser')->where($where1)->getField('user_id', true);
            foreach ($userId as $key => $value) {
                $u_id = $u_id . $value . ",";
            }
            $where2['u_id'] = array('IN', rtrim($u_id, ','));
            $sp_id = D('StudentParent')->where($where2)->getField('sp_id', true);
            $sp_id1 = "";
            foreach ($sp_id as $key => $value) {
                $sp_id1 = $sp_id1 . $value . ",";
            }
            $where3['sp_id'] = array('IN', rtrim($sp_id1, ','));
            $stu_id = D('StudentGroupAccess')->where($where3)->getField('stu_id', true);
            $stu_id1 = '';
            foreach ($stu_id as $key => $value) {
                $stu_id1 = $stu_id1 . $value . ",";
            }
            $sId1 = rtrim($stu_id1, ',');
        }


        //联系方式查询
        if ($phone != '') {
            $where4['phone'] = array("like", "%$phone%");
            $u_id = '';
            $userId = D('AdminUser')->where($where4)->getField('user_id', true);
            // var_dump($userId);die;
            foreach ($userId as $key => $value) {
                $u_id = $u_id . $value . ",";
            }
            $where5['u_id'] = array('IN', rtrim($u_id, ','));
            $sp_id = D('StudentParent')->where($where5)->getField('sp_id', true);
            $sp_id2 = "";
            foreach ($sp_id as $key => $value) {
                $sp_id2 = $sp_id2 . $value . ",";
            }
            $where6['sp_id'] = array('IN', rtrim($sp_id2, ','));
            $stu_id = D('StudentGroupAccess')->where($where6)->getField('stu_id', true);
            $stu_id2 = '';
            foreach ($stu_id as $key => $value) {
                $stu_id2 = $stu_id2 . $value . ",";
            }
            $sId2 = rtrim($stu_id2, ',');

        }
        if ($phone != '' || $parent_name != '') {
            // $sId1 =implode(",",$sId1);
            // $sId2 =implode(",",$sId2);
            // var_dump($sId2);
            $a = $sId1 . "," . $sId2;
            // var_dump($a);
            $where['stu_id'] = array('IN', $a);
        }
        // var_dump($where);
        $result = $Student->queryListEX('*', $where, $order, $page, $pagesize, '');
        // var_dump($result);
        // echo $Student->_sql();
        // dump($result);die;
        $this->success($result);
    }

    public function get()
    {
        $stu_id = I('get.stu_id/d', 0);
        $Student = M('Student');
        $User = M('User', 'think_');
        $stu_info = $Student->where("stu_id = '{$stu_id}'")->find();

        if ($stu_id > 0 && empty($stu_info)) {
            $this->error('非法参数');
        }
        // 查询监护人表
        $list = M('StudentGroupAccess')
            ->alias('a')
            ->field('b.*')
            ->join('__STUDENT_PARENT__ b on a.sp_id = b.sp_id')
            ->where("a.stu_id = '$stu_id'")
            ->select();

        foreach ($list as $key => $value) {
            $user_info = $User
                ->field('email,name,phone,username')
                ->where(array('user_id' => $value['u_id']))
                ->find();
            $list[$key]['email'] = $user_info['email'];
            $list[$key]['parent_name'] = $user_info['name'];
            $list[$key]['parent_phone'] = $user_info['phone'];
            $list[$key]['username'] = $user_info['username'];
        }
        $data['data'] = $stu_info;
        $data['data1'] = $list;
        $this->success($data);
    }


    //新增/编辑
    public function edit()
    {
        $Student = D('Student');
        $Device = D('Device');
        $id = I('post.stu_id', 0);
        $type = I('post.type');//把已存在用户名绑定上
        $data['user_id'] = $this->getUserId();
        $data['stu_id'] = $id;
        $data['s_id'] = I('post.s_id');//学校ID
        $data['a_id'] = I('post.a_id');//校区ID
        if (empty($data['s_id']) || empty($data['a_id'])) {
            $ids = $this->authRule();
            $data['s_id'] = $ids['s_id'];
            $data['a_id'] = $ids['a_id'];
        }
        $data['g_id'] = I('post.g_id');//年级ID
        $data['c_id'] = I('post.c_id');//班级ID
        $data['card_id'] = I('post.card_id');   //身份证号
        $data['birth_date'] = I('post.birth_date');   //出生日期
        $data['rx_date'] = I('post.rx_date');   //入学日期
        $data['create_time'] = date('Y-m-d H:i:s');  //创建时间
        $data['icc_id'] = I('post.icc_id');  //icc_id
        $data['nfc_id'] = I('post.nfc_id');  //nfc_id
        $data['parent_name'] = I('post.parent_name');  //家长姓名
        $data['username'] = I('post.username');
        $data['parent_phone'] = I('post.parent_phone');
        $data['relation'] = I('post.relation');
        $data['address'] = I('post.address');
        // 时间间隔设置为三年
        if ((strtotime($data['create_time']) - strtotime($data['birth_date'])) < (31536000 * 3)) {
            $this->error("出生日期应远小于当前日期");
        }
        if ((strtotime($data['rx_date']) - strtotime($data['birth_date'])) < (31536000 * 3)) {
            $this->error("出生日期应远小于入学日期");
        }

        $data['sex'] = I('post.sex');   //性别
        $data['stu_name'] = I('post.stu_name');   //学生姓名
        $data['stu_no'] = I('post.stu_no');   //学号
        $data['imei_id'] = I('post.imei_id');    //  IMEI
        $data['stu_phone'] = I('post.stu_phone');//  学生手机号
        if (!empty($data['imei_id'])) {
            $data['status'] = 1;
            # code...
        }
        //设备表数据
        $data2['imei'] = I('post.imei_id');    //  IMEI
        $data2['phone'] = I('post.stu_phone');//  学生手机号
        $data2['a_id'] = I('post.a_id');
        $data2['s_id'] = I('post.s_id');
        $data2['g_id'] = I('post.g_id');

        if (empty($data2['s_id']) || empty($data2['a_id'])) {
            $ids = $this->authRule();
            $data2['s_id'] = $ids['s_id'];
            $data2['a_id'] = $ids['a_id'];
            # code...
        }
        $where['stu_id'] = $id;

        $data1['s_id'] = I('post.s_id');//学校ID
        $data1['a_id'] = I('post.a_id');//校区ID
        $data1['g_id'] = I('post.g_id');//
        $data1['c_id'] = I('post.c_id');//
        $data1['address'] = I('post.address');  //家庭住址
        $data1['email'] = I('post.email');  //邮件
        $data1['family_tel'] = I('post.family_tel');    //家庭电话
        $data1['parent_name'] = I('post.parent_name');  //家长姓名
        $data1['parent_phone'] = I('post.parent_phone');    //家长电话
        $data1['username'] = I('post.username');    //家长登录账号
        $data1['relation'] = I('post.relation');    //与学生关系
        $data1['work_unit'] = I('post.work_unit');  //工作单位
        $data1['sp_id'] = I('post.sp_id', 0);
        $data1['u_id'] = I('post.u_id', 0);
        $data1['imei_id'] = I('post.imei_id');    //  IMEI
        $data1['stu_phone'] = I('post.stu_phone');

        if (!$Student->create($data)) {
            $this->error($Student->getError());
        } else {
            $Student->startTrans();
            $state = "";
            $state1 = "";
            $state2 = "";
            if ($id > 0) {
                $ret = $Student->where($where)->save($data);
                if ($ret === false) {
                    $state = false;
                } else {
                    $state = 1;
                }
                $where1['imei'] = $data2['imei'];
                // 编辑中如果输入的IMEI号 已存在 且已绑定  提示报错!
                $DeviceInfo = $Device->where($where1)->find();
                if ($DeviceInfo['status'] == 1 && $DeviceInfo['stu_id'] !== $id) {
                    $this->error('该设备已绑定');
                }
                if (empty($DeviceInfo)) {


                    $data3['status'] = '0';
                    $data3['stu_id'] = '0';
                    $data3['phone'] = '0';
                    // 把原先该学生id号绑定的设备解绑
                    $ret4 = $Device->where($where)->save($data3);
                    $data2['status'] = "1";
                    $data2['stu_id'] = $id;
                    $res1 = $Device->add($data2);
                    if ($res1 === false) {
                        $state1 = false;
                    } else {
                        $state1 = 1;
                    }
                } else {
                    // if (!empty($DeviceInfo) && $DeviceInfo['status'] === 0) {
                    // 把原先该学生id号绑定的设备解绑
                    $data3['status'] = '0';
                    $data3['stu_id'] = '0';
                    $data3['phone'] = '0';
                    $ret4 = $Device->where($where)->save($data3);

                    $data2['status'] = "1";
                    $data2['stu_id'] = $id;
                    // $data2['phone'] = $data['stu_phone'];
                    $res = $Device->where($where1)->save($data2);
                    if ($res === false) {
                        $state1 = false;
                    } else {
                        $state1 = 1;
                    }
                }
                // 家长 用户编辑
                $rets = $this->getStudentParent($data1, $id, '', $type);

                if ($state1 === 1 && $rets === true && $state === 1) {
                    $Student->commit();
                } else {
                    $Student->rollback();

                }
            } else {
                $res = $Student->where(array('stu_no' => $data['stu_no'], 's_id' => $data['s_id']))->find();
                if (empty($res)) {
                    $where1['imei'] = $data2['imei'];
                    // 判断IMEI号 是否已存在 存在 但是没有绑定 那就把该设备与改学生绑定!   存在但是已绑定学生 那么 我们提示报错
                    $DeviceInfo = $Device->where($where1)->find();
                    if ($DeviceInfo['status'] == 1) {
                        $this->error('该设备已绑定');
                    }
                    if (empty($DeviceInfo)) {
                        $data['status'] = "1";
                        $ret = $Student->add($data);
                        $data2['status'] = "1";
                        $data2['stu_id'] = $ret;
                        $res1 = $Device->add($data2);
                        if ($res1 === false) {
                            $state1 = false;
                        } else {
                            $state1 = 1;
                        }
                    } else {
                        $data['status'] = '1';
                        $ret = $Student->add($data);
                        $data2['status'] = "1";
                        $data2['stu_id'] = $ret;
                        $res1 = $Device->where(array('imei' => $data['imei_id']))->save($data2);
                        if ($res1 === false) {
                            $state1 = false;
                        } else {
                            $state1 = 1;
                        }
                    }
                } else {
                    $this->error('该学号已存在');
                }
                // 家长 添加
                $rets = $this->getStudentParent($data1, $id, $ret, $type);
                if ($rets === true && $state1 === 1) {
                    $Student->commit();
                } else {
                    $Student->rollback();
                }
            }

            $content = ($id > 0 ? '编辑' : '新建') . '成功';
            if ($ret === false) {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
            } else {
                $url = "http://yingyan.baidu.com/api/v3/entity/add";
                $post_data = array(
                    "ak" => "pObgXUnySvcX39VAqroiOt6xqUrNoOzK",
                    "service_id" => "146714",
                    "entity_name" => I('post.imei_id'),
                    "entity_desc" => I('post.stu_name'),
                );
                $this->Dispose($url, $post_data);
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                $this->success($content, U('/Admin/School/Student/index'));
            }
        }

    }

    //新增/编辑 By:Meng ceshi
    public function edit2()
    {
        $Student = D('Student');
        $Device = D('Device');
        $id = I('post.stu_id', 0);
        $type = I('post.type');//把已存在用户名绑定上
        $data['user_id'] = $this->getUserId();
        $data['stu_id'] = $id;
        $data['s_id'] = I('post.s_id');//学校ID
        $data['a_id'] = I('post.a_id');//校区ID

        $data['g_id'] = I('post.g_id');//年级ID
        $data['c_id'] = I('post.c_id');//班级ID
        $data['card_id'] = I('post.card_id');   //身份证号
        $data['birth_date'] = I('post.birth_date');   //出生日期
        $data['rx_date'] = I('post.rx_date');   //入学日期
        $data['create_time'] = date('Y-m-d H:i:s');  //创建时间
        $data['icc_id'] = I('post.icc_id');  //icc_id
        $data['nfc_id'] = I('post.nfc_id');  //nfc_id
        $data['parent_name'] = I('post.parent_name');  //家长姓名
        $data['username'] = I('post.username');
        $data['parent_phone'] = I('post.parent_phone');
        $data['relation'] = I('post.relation');
        $data['address'] = I('post.address');
        // 时间间隔设置为三年
        if ((strtotime($data['rx_date']) - strtotime($data['birth_date'])) < (31536000 * 3)) {
            $this->error("出生日期应远小于入学日期");
        }

        $data['sex'] = I('post.sex');   //性别
        $data['stu_name'] = I('post.stu_name');   //学生姓名
        $data['stu_no'] = I('post.stu_no');   //学号
        $data['imei_id'] = I('post.imei_id');    //  IMEI
        $data['stu_phone'] = I('post.stu_phone');//  学生手机号
        if (!empty($data['imei_id'])) {
            $data['status'] = 1;
            # code...
        }
        //设备表数据
        $data2['imei'] = I('post.imei_id');    //  IMEI
        $data2['phone'] = I('post.stu_phone');//  学生手机号
        $data2['a_id'] = I('post.a_id');
        $data2['s_id'] = I('post.s_id');
        $data2['g_id'] = I('post.g_id');

        $data1['s_id'] = I('post.s_id');//学校ID
        $data1['a_id'] = I('post.a_id');//校区ID
        $data1['g_id'] = I('post.g_id');//
        $data1['c_id'] = I('post.c_id');//
        $data1['address'] = I('post.address');  //家庭住址
        $data1['email'] = I('post.email');  //邮件
        $data1['family_tel'] = I('post.family_tel');    //家庭电话
        $data1['parent_name'] = I('post.parent_name');  //家长姓名
        $data1['parent_phone'] = I('post.parent_phone');    //家长电话
        $data1['username'] = I('post.username');    //家长登录账号
        $data1['relation'] = I('post.relation');    //与学生关系
        $data1['work_unit'] = I('post.work_unit');  //工作单位
        $data1['sp_id'] = I('post.sp_id', 0);
        $data1['u_id'] = I('post.u_id', 0);
        $data1['imei_id'] = I('post.imei_id');    //  IMEI
        $data1['stu_phone'] = I('post.stu_phone');

        if (!$Student->create($data)) {
            $this->error($Student->getError());
        } else {
            $Student->startTrans();
            $state = "";
            $state1 = "";
            $state2 = "";
            if ($id > 0) {
                $where['stu_id'] = $id;
                $stu_id = $Student->where($where)->save($data);
                if ($stu_id === false) {
                    $state = false;
                } else {
                    $state = 1;
                }
                $where1['imei'] = $data2['imei'];
                // 编辑中如果输入的IMEI号 已存在 且已绑定  提示报错!
                $DeviceInfo = $Device->where($where1)->find();
                if ($DeviceInfo['status'] == 1 && $DeviceInfo['stu_id'] !== $id) {
                    $this->error('该设备已绑定');
                }
                if (empty($DeviceInfo)) {
                    $data3['status'] = '0';
                    $data3['stu_id'] = '0';
                    $data3['phone'] = '0';
                    // 把原先该学生id号绑定的设备解绑
                    $ret4 = $Device->where($where)->save($data3);
                    $data2['status'] = "1";
                    $data2['stu_id'] = $id;
                    $device_id = $Device->add($data2);
                    if ($device_id === false) {
                        $state1 = false;
                    } else {
                        $state1 = 1;
                    }
                } else {
                    // if (!empty($DeviceInfo) && $DeviceInfo['status'] === 0) {
                    // 把原先该学生id号绑定的设备解绑
                    $data3['status'] = '0';
                    $data3['stu_id'] = '0';
                    $data3['phone'] = '0';
                    $ret4 = $Device->where($where)->save($data3);

                    $data2['status'] = "1";
                    $data2['stu_id'] = $id;
                    // $data2['phone'] = $data['stu_phone'];
                    $device_id = $Device->where($where1)->save($data2);
                    if ($device_id === false) {
                        $state1 = false;
                    } else {
                        $state1 = 1;
                    }
                }
                // 家长 用户编辑
                $rets = $this->getStudentParent($data1, $id, '', $type);

                if ($state1 === 1 && $rets === true && $state === 1) {
                    $Student->commit();
                } else {
                    $Student->rollback();
                }
            } else {
                $res = $Student->where(array('stu_no' => $data['stu_no'], 's_id' => $data['s_id']))->find();
                if (empty($res)) {
                    $where1['imei'] = $data2['imei'];
                    // 判断IMEI号 是否已存在 存在 但是没有绑定 那就把该设备与改学生绑定!   存在但是已绑定学生 那么 我们提示报错
                    $DeviceInfo = $Device->where($where1)->find();
                    if ($DeviceInfo['status'] == 1) {
                        $this->error('该设备已绑定');
                    }
                    if (empty($DeviceInfo)) {
                        $data['status'] = "1";
                        $ret = $Student->add($data);
                        $data2['status'] = "1";
                        $data2['stu_id'] = $ret;
                        $res1 = $Device->add($data2);
                        if ($res1 === false) {
                            $state1 = false;
                        } else {
                            $state1 = 1;
                        }
                    } else {
                        $data['status'] = '1';
                        $ret = $Student->add($data);
                        $data2['status'] = "1";
                        $data2['stu_id'] = $ret;
                        $res1 = $Device->where(array('imei' => $data['imei_id']))->save($data2);
                        if ($res1 === false) {
                            $state1 = false;
                        } else {
                            $state1 = 1;
                        }
                    }
                } else {
                    $this->error('该学号已存在');
                }
                // 家长 添加
                $rets = $this->getStudentParent($data1, $id, $ret, $type);
                if ($rets === true && $state1 === 1) {
                    $Student->commit();
                } else {
                    $Student->rollback();
                }
            }

            $content = ($id > 0 ? '编辑' : '新建') . '成功';
            if ($ret === false) {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
            } else {
                $url = "http://yingyan.baidu.com/api/v3/entity/add";
                $post_data = array(
                    "ak" => "pObgXUnySvcX39VAqroiOt6xqUrNoOzK",
                    "service_id" => "146714",
                    "entity_name" => I('post.imei_id'),
                    "entity_desc" => I('post.stu_name'),
                );
                $this->Dispose($url, $post_data);
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                $this->success($content, U('/Admin/School/Student/index'));
            }
        }

    }

    public function Dispose($url, $post_data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    //删除
    public function del()
    {
        $stu_id = I('post.id');//学生 ID
        // var_dump($stu_id);die;
        $arrUids = explode(',', $stu_id);
        if (empty($arrUids)) {
            $this->error('参数错误');
        }
        $StudentParent = D('StudentParent');//家长表
        $Student = D('Student');
        $okCount = 0;//学生表
        foreach ($arrUids as $k => $v) {
            $where['stu_id'] = $v;
            $sthInfo = D('StudentGroupAccess')->where($where)->getField("sp_id", true);
            $ret = $Student->where($where)->delete();
            //查询外链主键表  得到监护人ID
            $sp_id = "";
            foreach ($sthInfo as $key => $value) {
                $sp_id = $sp_id . $value . ",";
            }
            $where1['sp_id'] = array('IN', rtrim($sp_id, ','));
            if ($ret) {
                $resInfo = D('StudentGroupAccess')->where($where1)->getField("stu_id", true);
                $studentParenInfo = $StudentParent->where($where1)->getField('u_id', true);
                $sp_id1 = "";
                foreach ($resInfo as $key => $value) {
                    $sp_id1 = $sp_id1 . $value . ",";
                }

                $u_id = "";
                foreach ($studentParenInfo as $key => $value) {
                    $u_id = $u_id . $value . ",";
                }
                // var_dump($sp_id);die;
                $where3['stu_id'] = array('IN', rtrim($sp_id1, ','));
                $student1 = $Student->where($where3)->select();//判断是否还有学生！ 没有了 则删除用户表中的数据的用表
                if (empty($student1)) {
                    $where2['user_id'] = array('IN', rtrim($u_id, ','));
                    $StudentParent->where($where1)->delete();
                    $StudentGroupAccess = D('StudentGroupAccess')->where($where)->delete();
                    $res = D('AdminUser')->where($where2)->delete();
                    $where3['send_user'] = array('IN', rtrim($u_id, ','));
                    $OnlineQu = D('OnlineQu')->where($where3)->delete();//删除 学生关联 在线提问信息 家长不存在才删除在线提问信息记录
                    $Mymessage = D('Mymessage')->where($where2)->delete();   //删除 我的消息表中的数据
                }
                $Reward = D('Reward')->where($where)->delete();//删除 学生奖励信息奖励 php
                $ExamAllResult = M('ExamAllResults')->where($where)->delete(); //删除学生的统考成绩
                $ExamResults = D('ExamResults')->where($where)->delete();//删除学生的单考考成绩
                $Remark = D('Remark')->where($where)->delete(); //删除学生 评语信息
                $Attendance = D('Attendance')->where($where)->delete(); //删除学生 考勤信息
                $StuCardSet = D('StuCardSet')->where($where)->delete(); //删除学生 学生卡信息
                $Leave = M('Leave')->where($where)->delete(); //删除学生 学生请假信息

                //把绑定的设备解绑
                $data['status'] = '0';
                $data['stu_id'] = '0';
                $data['phone'] = '0';
                $res1 = D('Device')->where($where)->save($data);
            }
            if ($ret == 1) {
                $okCount++;  //处理成功记录数
            }
        }
        //写log
        $content = "删除" . $okCount . "条信息";
        $state = $okCount > 0 ? 1 : 2;
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, $state);
        $this->success('删除成功' . $okCount . '条记录');
    }

//处理家长信息
    protected function getStudentParent($data, $stu_id, $id, $type)
    {
        $userInfo = D('AdminUser'); // 用户表
        $StudentParent = D('StudentParent');//家长表
        $StuCardSet = D('StuCardSet');//学生卡设置表
        $Teacher = D('Teacher');//教师表
        $data1['create_time'] = date('Y-m-d H:i:s');
        $name = "";
        // 添加学生  登陆名在数据库中已存在的话 ,提示xx已存在 是否导入到该账号信息中
        if (empty($stu_id)) {
            for ($i = 0; $i < count($data['parent_name']); $i++) {
                $data4['username'] = $data['username'][$i];
                $where11['username'] = $data4['username'];
                $result = $userInfo->where($where11)->find();
                if (!empty($result)) {
                    $name = $name . $data4['username'] . ",";
                }
            }
            if (!empty($name) && empty($type)) {
                $name = trim($name, ',');
                $this->success(array('type' => '1', 'name' => $name));
            }
        }

        for ($i = 0; $i < count($data['parent_name']); $i++) {
            $data1['address'] = $data['address'][$i];
            $data1['email'] = $data['email'][$i];
            $data1['family_tel'] = $data['family_tel'][$i];
            $data1['parent_name'] = $data['parent_name'][$i];
            $data1['parent_phone'] = $data['parent_phone'][$i];
            $data1['username'] = $data['username'][$i];
            $data1['relation'] = $data['relation'][$i];
            $data1['work_unit'] = $data['work_unit'][$i];
            $where['u_id'] = $data['u_id'][$i];
            $data1['sp_id'] = $data['sp_id'][$i];

            $data1['s_id'] = $data['s_id'];
            $data1['a_id'] = $data['a_id'];
            if (!$StudentParent->create($data1)) {
                $this->error($StudentParent->getError());
            } else {
                if ($stu_id > 0) {
                    $data1['u_id'] = $data['u_id'][$i];

                    $ret = $StudentParent->where($where)->save($data1);
                    $info = $userInfo->where(array('user_id' => $data1['u_id']))->find();
                    $info1 = $userInfo->where(array('username' => $data1['username']))->find();
                    if ($data1['username'] != $info['username'] && !empty($info1)) {

                        $name = "";
                        for ($i = 0; $i < count($data['parent_name']); $i++) {

                            $data4['address'] = $data['address'][$i];
                            $data4['email'] = $data['email'][$i];
                            $data4['family_tel'] = $data['family_tel'][$i];
                            $data4['parent_name'] = $data['parent_name'][$i];
                            $data4['parent_phone'] = $data['parent_phone'][$i];
                            $data4['username'] = $data['username'][$i];
                            $data4['relation'] = $data['relation'][$i];
                            $data4['work_unit'] = $data['work_unit'][$i];
                            $where['u_id'] = $data['u_id'][$i];
                            $data4['sp_id'] = $data['sp_id'][$i];

                            // var_dump($where['u_id']);die;
                            $data4['s_id'] = $data['s_id'];
                            $data4['a_id'] = $data['a_id'];


                            $where11['username'] = $data4['username'];
                            $result = $userInfo->where($where11)->find();
                            if (!empty($result) && !empty($info1)) {
                                // echo "123123";die;

                                // $where5['u_id'] = $result['user_id'];
                                // $ret = $StudentParent->where($where5)->getField('sp_id',false);
                                $name = $name . $data4['username'] . ",";

                            }

                        }
                        // var_dump($name);die;
                        $name = rtrim($name, ',');
                        if (!empty($name) && empty($type)) {
                            // echo 123;die;
                            $this->success(array('type' => '1', 'name' => $name));
                        }


                    }

                    if (!empty($type)) {
                        // echo 123;die;
                        if (!empty($info1)) {
                            // echo 123;die;
                            $where5['u_id'] = $info1['user_id'];
                            $sp_id = $StudentParent->where($where5)->getField('sp_id', false);
                            $data3['sp_id'] = $sp_id;
                            $rets1 = D('StudentGroupAccess')->where(array('sp_id' => $data1['sp_id'], 'stu_id' => $stu_id))->save($data3);
                            # code...
                        } else {

                            $data2['phone'] = $data1['parent_phone'];
                            $data2['username'] = $data1['username'];
                            $data2['name'] = $data1['parent_name'];
                            $rets1 = $userInfo->where(array('user_id' => $data['u_id'][$i]))->save($data2);


                        }

                    } else {

                        if ($data1['username'] != $info['username'] && !empty($info1)) {
                            # code...
                            // echo 123;die;
                            $this->success(array('type' => '1', 'name' => $data1['username']));

                        }

                        $data2['phone'] = $data1['parent_phone'];
                        $data2['username'] = $data1['username'];
                        $data2['name'] = $data1['parent_name'];
                        $rets1 = $userInfo->where(array('user_id' => $data['u_id'][$i]))->save($data2);
                        //添加视频通讯录 BY:Meng Fanmin 2017.06.15
                        video_add_user($data['u_id'][$i]);
                        easemob_add_user($data['u_id'][$i]);


                    }

                    if ($rets1 === false) {
                        $state2 = 0;
                    } else {
                        $state2 = 1;
                    }
                } else {
                    //家长信息添加
                    if (!empty($type)) {
                        $where12['username'] = $data1['username'];
                        $result = $userInfo->where($where12)->find();
                        if (!empty($result)) {

                            $where5['u_id'] = $result['user_id'];
                            $sp_id = $StudentParent->where($where5)->getField('sp_id', false);
                            if (empty($sp_id)) {
                                $sp_id = $Teacher->where($where5)->getField('t_id', false);
                                if ($sp_id) {
                                    $this->error("{$data1['username']}" . '为老师账号 不能绑定');
                                    # code...
                                }
                            }
                            $data3['sp_id'] = $sp_id;
                            $data3['stu_id'] = $id;
                            $ss = D('StudentGroupAccess')->add($data3);

                        } else {

                            $ret = $StudentParent->add($data1);
                            // echo M()->_sql();die;
                            if ($ret) {
                                //用户表插入 获取 userID
                                $ret1 = $this->getUser($data1);
                            }
                            $where1['sp_id'] = $ret;
                            $data2['u_id'] = $ret1;
                            // var_dump($data2);die;
                            $StudentParent->where($where1)->save($data2);

                            $data3['stu_id'] = $id;
                            $data3['sp_id'] = $ret;
                            // var_dump($data3);die;
                            $ss = D('StudentGroupAccess')->add($data3);
                        }
                    } else {
                        // echo 1232;die;
                        $where11['username'] = $data1['username'];
                        $result = $userInfo->where($where11)->find();
                        if (empty($result)) {
                            // echo "123123";die;
                            $ret = $StudentParent->add($data1);
                            // echo M()->_sql();die;
                            if ($ret) {
                                //用户表插入 获取 userID
                                $ret1 = $this->getUser($data1);
                            }
                            $where1['sp_id'] = $ret;
                            $data2['u_id'] = $ret1;
                            //添加视频通讯录 BY:Meng Fanmin 2017.06.15
                            video_add_user($ret1);
                            easemob_add_user($ret1);
                            // var_dump($data2);die;
                            $StudentParent->where($where1)->save($data2);
                        } else {
                            // $where5['u_id'] = $result['user_id'];
                            // $ret = $StudentParent->where($where5)->getField('sp_id',false);

                            $this->success(array('type' => '1', 'name' => array($data1['username'])));
                        }
                        $data3['stu_id'] = $id;
                        $data3['sp_id'] = $ret;
                        // var_dump($data3);die;
                        $ss = D('StudentGroupAccess')->add($data3);

                    }

                    if ($ss === false) {
                        $state2 = 0;
                    } else {
                        $state2 = 1;
                    }
                }

            }

        }


        //将监护人电话存为白名单,并短信通知学生卡
        if (!$stu_id) {
            // echo "12312";die;
            $phoneStr = "*HQ," . $data['imei_id'] . ",WLALL," . date('His') . ",1," . $data['parent_phone'][0] . ",,2," . $data['parent_phone'][1] . ",,3,,,4,,,5,,,6,,,7,,,8,,,9,,,10,#";
            $dataCard = $StuCardSet->create($data);
            $dataCard['imei'] = $data['imei_id'];
            $dataCard['stu_id'] = $id;
            $dataCard['d_type'] = "whitelist";
            $dataCard['d_value'] = $data['parent_name'][0] . ":" . $data['parent_phone'][0] . "," . $data['parent_name'][1] . ":" . $data['parent_phone'][1] . ",:,:,:,:,:,:,:,:";
            $res = $StuCardSet->add($dataCard);

            $commandData['stu_phone'] = $data['stu_phone'];
            $commandData['order'] = $phoneStr;
            $commandData['d_type'] = $dataCard['d_type'];
            $commandData['imei'] = $dataCard['imei'];
            $commandData['create_time'] = date("Y-m-d H:i:s");
            D('StuCardCommand')->add($commandData);


            if ($res) {
                if ($state2 === 1) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            if ($state2 === 1) {
                return true;
            } else {
                return false;
            }

        }
    }

    // return $ret;

    //处理用户信息···
    protected function getUser($data)
    {
        // echo "12313";die;

        $userInfo = D('AdminUser');
        $data1['s_id'] = $data['s_id'];//学校ID
        $data1['a_id'] = $data['a_id'];//校区ID
        $data1['name'] = $data['parent_name'];
        $data1['email'] = $data['email'];
        $data1['group_id'] = 4;
        $data1['type'] = 4;
        $data1['sex'] = 1;
        $data1['username'] = $data['username'];//用户名
        $data1['phone'] = $data['parent_phone'];//手机号
        $data1['password'] = "e10adc3949ba59abbe56e057f20f883e";
        $data1['create_time'] = date('Y-m-d H:i:s');
        $res = $userInfo->add($data1);
        //添加视频通讯录 BY:Meng Fanmin 2017.06.15
        video_add_user($res);
        easemob_add_user($res);
        return $res;


    }

    public function inguiry_unit()
    {
        $s_id = I('get.s_id');//学校ID
        $imei = I('get.imei');//imei号
        $where['stu_id'] = 0;
        $where['status'] = 0;
        if (!empty($imei)) {
            $where['imei'] = array("like", "%$imei%");
        }
        if (!empty($s_id)) {
            $where['s_id'] = $s_id;
        }
        $pagesize = I("get.pagesize", $this->PAGE_SIZE);
        // var_dump($pagesize);
        $page = I('get.page');
        $sort = I('get.sort', 'dc_id');
        $order = $sort . ' ' . I('get.order', 'desc');
        $Device = D('DeviceManage');
        $result = $Device->queryListEX('*', $where, $order, $page, $pagesize, '');
        // var_dump($result);

        $this->success($result);
    }


    /**
     * 学生信息导入
     */
    public function import()
    {
        $s_id = I('get.s_id');
        $a_id = I('get.a_id');
        $g_id = I('get.g_id');
        $c_id = I('get.c_id');
        $type = I('post.type');//一个 有用户名 存在的 状态值
        $file_url = I('param.file_url');
        $Student = D('Student');//学生表
        $StudentParent = D('StudentParent');//家长表
        $RELATION = C('RELATION');//
        $userInfo = D('AdminUser');//用户表
        $StuCardSet = D('StuCardSet');//学生卡表
        if (empty($file_url)) {
            $this->error('参数错误');
            # code...
        }
        $file_url = $_SERVER['DOCUMENT_ROOT'] . $file_url;
        $arrField = array('学号' => 'stu_no',
            '学生姓名' => 'stu_name',
            '出生日期' => 'birth_date',
            '入学时间' => 'rx_date',
            '身份证号' => 'card_id',
            '校区名称' => 'a_id',
            '性别' => "sex",
            "监护人姓名" => "parent_name",
            "关系" => "relation",
            "邮箱" => "email",
            "联系电话" => "parent_phone",
            "家庭电话" => "family_tel",
            "工作单位" => "work_unit",
            "常住地址" => "address",
            "ICCID" => "icc_id",
            "登录帐号" => "username");

        $arrList_tmp = import_excel($arrField, $file_url);
//        echo "<pre>";
//        var_dump($arrList_tmp);die;
        if ($arrList_tmp == false) {
            $this->error('表格不能为空');
            # code...
        }
        $arrList = array();
        foreach ($arrList_tmp as $key => $value) {
            if ($value['stu_no'] != '' || $value['stu_name'] != '') {
                $arrList[] = $value;
            }
        }

        // unlink($file_url);//删除文件

        if (count($arrList) > 500) {
            D('SystemLog')->writeLog($this->getModule(), '导入数据不能超过500条', $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
            $this->error('导入数据不能超过500条');
        }

        //检查数据有效性
        foreach ($arrList as $key => $value) {
            $value['stu_id'] = "0";
            if (!$Student->create($value)) {
                $key += 2;
                D('SystemLog')->writeLog($this->getModule(), '学生管理，导入失败,第' . $key . '行数据错误:' . $Student->getError(), $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                $this->error('第' . $key . '行数据错误:' . $Student->getError());
            }
        }


        $Student->startTrans();
        foreach ($arrList as $value) {


            //把用户名存在的 过滤 出来
            foreach ($arrList as $k => $v) {

                $name = "";
                $data4['username'] = $v['username'];//用户登录账号
                $where11['username'] = $data4['username'];
                $result = $userInfo->where($where11)->find();
                if (!empty($result)) {
                    $name = $name . $data4['username'] . ",";
                }
            }
            if (!empty($name) && empty($type)) {
                $this->success(array('type' => '1', 'name' => $name));
            }
            //检查数据的有效性

            // $value['stu_id'] = "0"; // 注释
            // if(!$Student->create($value)){
            //     $key += 2;
            //     D('SystemLog')->writeLog($this->getModule(), '学生管理，导入失败,第' . $key . '行数据错误:' . $Student->getError(), $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
            //     $this->error('第' . $key . '行数据错误:' . $Student->getError());
            // }


            $data['card_id'] = $value['card_id'];   //身份证号
            $data['birth_date'] = $value['birth_date'];   //出生日期
            $data['rx_date'] = $value['rx_date'];   //入学日期
            if ($value['sex'] == "男") {
                $data['sex'] = "1";
            } else {
                $data['sex'] = "0";
            }
            $value['relation'] = array_search($value['relation'], $RELATION); // 关系
            if ($value['relation'] === false) {
                $value['relation'] = "0";
                # code...
            }
            $data['a_id'] = $a_id;   //校区ID
            $data['s_id'] = $s_id;   //学校ID
            $data['g_id'] = $g_id;   //年级ID
            $data['c_id'] = $c_id;   //班级ID
            $data['stu_no'] = $value['stu_no'];   //学号
            $data['stu_name'] = $value['stu_name'];   //学生名称
            $data['icc_id'] = $value['icc_id'];   //ICCID
            $data['create_time'] = date('Y-m-d H:i:s');
            // $DeviceInfo = $Device->where(array('imei' => $value['imei'], 'status' => '1'))->find();
            // if ($DeviceInfo) {
            //     $this->error(""."{$value['imei']}" . "　imei号已绑定");
            //     # code...
            // }
            // 家长信息
            $data1['a_id'] = $a_id;   //校区ID
            $data1['s_id'] = $s_id;   //学校ID
            $data1['g_id'] = $g_id;   //年级ID
            // $data1['c_id'] = $c_id;
            $data1['parent_name'] = $value['parent_name'];
            // var_dump($value['parent_name']);die;
            $data1['username'] = $value['username'];
            $data1['parent_phone'] = $value['parent_phone'];
            $data1['family_tel'] = $value['family_tel'];
            $data1['work_unit'] = $value['work_unit'];
            $data1['address'] = $value['address'];
            $data1['relation'] = $value['relation'];
            $data1['email'] = $value['email'];

            $res1 = $Student->add($data);//学生stu_id

            if (!empty($type)) {

                // echo 123;die;
                $where12['username'] = $value['username'];
                // var_dump($where12);
                // $where12['phone'] = $data1['parent_phone'];
                $result = $userInfo->where($where12)->find();
                // echo M()->_sql();die;
                if (!empty($result)) {
                    // echo 123;die;
                    $where5['u_id'] = $result['user_id'];
                    $sp_id = $StudentParent->where($where5)->getField('sp_id', false);
                    $data3['sp_id'] = $sp_id;
                    $data3['stu_id'] = $res1;
                    // $rets1 = D('StudentGroupAccess')->where(array('sp_id'=>$data1['sp_id'],'stu_id'=>$stu_id))->save($data3);
                    $ss = D('StudentGroupAccess')->add($data3);
                    // var_dump($ss);die;

                } else {
                    // echo 222;die;
                    $ret = $StudentParent->add($data1);
                    // var_dump($ret);die;
                    // echo M()->_sql();die;
                    if ($ret) {
                        //用户表插入 获取 userID
                        $ret1 = $this->getUser($data1);

                    }
                    $where1['sp_id'] = $ret;
                    $data2['u_id'] = $ret1;
                    // var_dump($data2);die;
                    $StudentParent->where($where1)->save($data2);

                    $data3['stu_id'] = $res1;
                    $data3['sp_id'] = $ret;
                    // var_dump($data3);die;
                    $ss = D('StudentGroupAccess')->add($data3);
                }
            } else {
                // echo 1232;die;
                $where11['username'] = $value['username'];
                $result = $userInfo->where($where11)->find();
                if (empty($result)) {
                    $ret = $StudentParent->add($data1);
                    if ($ret) {
                        //用户表插入 获取 userID
                        // var_dump($data1);die;
                        $ret1 = $this->getUser($data1);
                    }
                    $where13['sp_id'] = $ret;
                    $data2['u_id'] = $ret1;
                    // var_dump($data2);die;
                    $StudentParent->where($where13)->save($data2);
                } else {
                    // echo "<pre>";
                    $ret = $StudentParent->where(array("u_id" => $result['user_id']))->getField('sp_id', false);
                    // $this->success(array('type' => '1123123', 'name' => array($data1['username'])));
                }
                $data5['stu_id'] = $res1;
                $data5['sp_id'] = $ret;
                // var_dump($data3);die;
                $ss = D('StudentGroupAccess')->add($data5);


            }

        }


        if ($ss === false || $res1 === false) {
            D('StudentGroupAccess')->rollback();
        } else {
            D('StudentGroupAccess')->commit();
        }
/// --------------------------------------------------------------------------------------------------------------


        if ($ss === false || $res1 === false) {
            $content = '导入失败';

            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
            $this->error($Student->getError());
        } else {
            $content = '导入成功';
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
            $this->success($content, U('/Admin/School/Student/index'));
        }


    }


    //学生 升学机制
    public function Upgrade()
    {

        $stu_id = I('post.stu_id'); //学生 stu_id
        $s_id = I('post.s_id'); //学校 s_id
        $a_id = I('post.a_id'); //校区 a_id
        $g_id = I('post.g_id'); //要升向的年级
        $c_id = I('post.c_id'); //要升向的班级
        if (!empty($s_id)) {
            $data['s_id'] = $s_id;
            # code...
        }
        $data['a_id'] = $a_id;
        $data['g_id'] = $g_id;
        $data['c_id'] = $c_id;
        $Student = D('Student'); //学生表
        $stuInfo = explode(',', $stu_id);
        // 开启事物   有一条数据不正常 该次升学 失败
//                $Student->startTrans();
        $state = '';
        foreach ($stuInfo as $key => $value) {

            $where['stu_id'] = $value;
            // $getinfo = $Student->where($where)->getField('g_id',false);
            /*
            // 获取第一条数据 然后判断传过来的所有数据是否是在同一个年级下的  <--  一次只能传一个年级下的所有班级 不能同时传多个年级-->
            $where1['g_id'] = $stuInfo[0];
            $StuInfo = $Student->where($where1)->find();
            */

            // 判断 要升级的 是否 不同的年级
            $getInfo = $Student->where($where)->find();
            if ($getInfo['g_id'] == $g_id) {
                $this->error('请选择不同年级下的班级升级');

            }
            $res = $Student->where($where)->save($data);
            # code...
        }


//                $Student->commit();
//                $Student->rollback();

        $content = "升级成功";
        if ($res === false) {
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
            $this->error($Student->getError());
        } else {
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
            $this->success($content, U('/Admin/School/Student/index'));
        }


    }
}
