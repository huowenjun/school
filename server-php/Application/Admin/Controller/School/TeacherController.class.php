<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-教师管理
 */
namespace Admin\Controller\School;

use Admin\Controller\BaseController;

class TeacherController extends BaseController
{
    /**
     *教师管理
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
        // 学校管理员权限
        if ($this->getType() == 1) {
            $where['s_id'] = $ids['s_id'];
        // 教师权限
        } elseif ($this->getType() == 3) {
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
        // 家长权限
        } elseif ($this->getType() == 4) {
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
            $where['stu_id'] = array('IN', $ids['stu_id']);
        } else {
            $arr = $_GET;
            $sthInfo = getSchoolId($arr);
            $where['s_id'] = array('IN', $sthInfo);
        }

        $name = I('get.name'); //姓名
        $user_name = I('get.user_name'); //登陆账号
        $s_id = I('get.s_id');
        $a_id = I('get.a_id');
        $d_id = I('get.d_id');
        $authId = $this->authRule();
        if ($authId['s_id'] > 0) {
            $where['s_id'] = $authId['s_id'];
        }
        if ($authId['a_id'] > 0) {
            $where['a_id'] = $authId['a_id'];
        }
        $pagesize = I("get.pagesize", $this->PAGE_SIZE);
        $page = I('get.page');
        $sort = I('get.sort', 't_id');
        $order = $sort . ' ' . I('get.order', 'desc');
        $Teacher = D('Teacher');
        if ($name != '') {
            $where['name'] = array("like", "%$name%");
        }
        if (!empty($s_id)) {
            $where['s_id'] = $s_id;
        }
        if (!empty($a_id)) {
            $where['a_id'] = $a_id;
        }
        if (!empty($d_id)) {
            $where['d_id'] = $d_id;
        }
        if ($user_name != '') {
            $where['user_name'] = array("like", "%$user_name%");
        }

        $result = $Teacher->queryListEX('*', $where, $order, $page, $pagesize, '');
        $this->success($result);
    }

    public function get()
    {

        $t_id = I('get.t_id/d', 0);
        $Teacher = D('Teacher');
        $area_info = $Teacher->getInfo($t_id);
        if ($t_id > 0 && empty($area_info)) {
            $this->error('不存在');
        }
        $this->success($area_info);

    }


    //新增/编辑
    public function edit()
    {

        $id = I('post.t_id');
        $username = I('post.user_name');
        $data1['t_id'] = $id;
        $data1['d_id'] = I("post.d_id");//部门d_id
        $data1['valid'] = I('post.valid');//状态
        $data1['education'] = I('post.education');// '学历 1初中 2中技 3高中 4中专 5大专 6本科 7硕士 8EMBA 9博士 0其它'
        $data1['email'] = I('post.email'); //邮箱
        $data1['name'] = I('post.name');// 名字
        $data1['phone'] = I('post.phone');// 手机号
        $data1['sex'] = I('post.sex');//性别

        if($username){
            $data1['user_name'] = $username; //登陆名
            $data2['username'] = $username;
        }
        $data2['name'] = I('post.name');
        $data2['status'] = I('post.valid'); // '有效状态 0 有效 1 无效',
        $AdminUser = D('AdminUser');
        $Teacher = D('Teacher');
        $teacherInfo = $Teacher->getInfo($id);
        $state = '';
        $state1 = '';
        if (!$Teacher->create()) {
            $this->error($Teacher->getError());
        } else {
            if ($id > 0) {
                //用户表信息编辑  开启事物
                $Teacher->startTrans();
                $ret = $Teacher->where(array('t_id' => $id))->save($data1);
                if (false === $ret) {
                    $state = '0';
                } else {
                    $state = '1';
                }
//                $where1['t_id'] = $id;
//                $teacherUid = $Teacher->where($where1)->getField('u_id',false);
//                $whereuid['username'] = $username;
//                $whereuid['user_id'] = array('NEQ',$teacherUid);
//                $uId = $AdminUser->where($whereuid)->find();
//                if (!empty($uId)){
//                    $this->error('该登录账号已存在!');
//                }else{
                    $res = $AdminUser->where(array('user_id' => $teacherInfo['u_id']))->save($data2);
//                }
                if (false === $res) {
                    $state1 = '0';
                } else {
                    $state1 = '1';
                }
                if ($state == 1 && $state1 == 1) {
                    $Teacher->commit();
                }else{
                    $Teacher->rollback();
                }
            } else {
                //用户表信息添加  开启事物
                $Teacher->startTrans();
                $data['username'] = $_POST['user_name'];
                $data['name'] = $_POST['name'];
                $data['password'] = "e10adc3949ba59abbe56e057f20f883e"; //默认123456
                $data['create_time'] = date('Y-m-d H:i:s');
                $data['a_id'] = $_POST['a_id'];
                $data['group_id'] = 3;
                $data['type'] = 3;
                $data['s_id'] = $_POST['s_id'];
                $where1['username'] = $_POST['user_name'];
                $userInfo = $AdminUser->where($where1)->find();
                if (empty($userInfo)) {
                    //添加用户表中数据
                    $retUser = $AdminUser->add($data);
                } else {
                    $this->error('用户名已存在，请重新输入！');
                }
                if (false === $retUser) {
                    $state = 0;;
                } else {
                    $state = 1;
                }
                //老师信息表添加
                $_POST['u_id'] = $retUser;
                // $_POST['user_id'] = $this->getUserId();
                $_POST['s_id'] = $_POST['s_id'];
                $_POST['create_time'] = date('Y-m-d H:i:s');
                $ret = $Teacher->add($_POST);
                if (false === $ret) {
                    $state1 = '0';
                } else {
                    $state1 = '1';
                }
                if ($state == 1 && $state1 == 1) {
                    $Teacher->commit();
                }else{
                    $Teacher->rollback();
                }
                //学校成功后更新用户表学校ID
                //添加视频通讯录
                video_add_user($retUser);
                easemob_add_user($retUser);
            }
            $content = ($id > 0 ? '编辑' : '新建') . '成功';
            if ($ret === false && $retUser === false) {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
                $this->error($Teacher->getError());
            } else {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                $this->success($content, U('/Admin/School/Teacher/index'));
            }
        }

    }

    //删除
    public function del()
    {
        $t_id = I('post.t_id');
        $arrUids = explode(',', $t_id);
        if (empty($arrUids)) {
            $this->error('参数错误');
        }
        $AdminUser = D('AdminUser');
        $Teacher = D('Teacher');
        $okCount = 0;
        foreach ($arrUids as $k => $v) {
            $where['t_id'] = $v;
            $result = $Teacher->getInfo($v);
            $where1['user_id'] = $result['u_id'];
            // return $where1;
            $data['valid'] = '1';
            $ret = $Teacher->where($where)->save($data);
//            $ret = $Teacher->where($where)->delete();
            if ($ret) {
                $data1['status'] = '1';
                $result = $AdminUser->where($where1)->save($data1);//'有效状态 0 有效 1 无效',
//                $result = $AdminUser->where($where1)->delete();//'有效状态 0 有效 1 无效',
            }
            if ($ret == 1) {
                $okCount++;  //处理成功记录数
            }
        }
        //写log
        $content = "处理" . $okCount . "条信息";
        $state = $okCount > 0 ? 1 : 2;
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, $state);
        $this->success('处理成功' . $okCount . '条记录');
    }

    public function getTreeList()
    {
        $t_id = I('get.t_id');// 教师ID
        $where['t_id'] = $t_id;
        $info = D("Teacher")->where($where)->find();


        //树状结构
        $School = D('SchoolInformation');
        $Area = D('SchoolArea');
        $Grade = D('Grade');
        $Class = D('Class');
        $Student = D('Student');
        // $where = "";
        // $regionList = D('AreaRegion')->getField('region_code,region_name');
        // $region = $School->field("distinct region_id as region_id")->where($where)->select();
        $schoolInfo = $School->where(array('s_id'=>$info['s_id']))->select();

        $arr = "";
        //学校
        foreach ($schoolInfo as $key => $value) {
            $arr[$key]['children'] = array();
            $arr[$key]['id'] = $value['s_id'];
            $arr[$key]['isStudent'] = 0;
            $arr[$key]['name'] = "{$value['name']}";
            $arr[$key]['pId'] = 0;
            $arr[$key]['type'] = 0;
            $arr[$key]['typeFlag'] = "shool";
            // $where2['s_id'] = array('EQ', $value['s_id']);
            $areaList = $Area->where(array('a_id'=>$info['a_id']))->select();
            //校区
            foreach ($areaList as $key1 => $value1) {
                $arr[$key]['children'][$key1]['children'] = array();
                $arr[$key]['children'][$key1]['id'] = $value1['a_id'];
                $arr[$key]['children'][$key1]['isStudent'] = 0;
                $arr[$key]['children'][$key1]['name'] = "{$value1['name']}";
                $arr[$key]['children'][$key1]['pId'] = $value['s_id'];
                $arr[$key]['children'][$key1]['type'] = 0;
                $arr[$key]['children'][$key1]['typeFlag'] = "area";
                //年级
                $where3['a_id'] = array('EQ', $value1['a_id']);
                $gradeList = $Grade->where($where3)->select();
                foreach ($gradeList as $key2 => $value2) {
                    $arr[$key]['children'][$key1]['children'][$key2]['children'] = array();
                    $arr[$key]['children'][$key1]['children'][$key2]['id'] = $value2['g_id'];
                    $arr[$key]['children'][$key1]['children'][$key2]['isStudent'] = 0;
                    $arr[$key]['children'][$key1]['children'][$key2]['name'] = "{$value2['name']}";
                    $arr[$key]['children'][$key1]['children'][$key2]['pId'] = $value1['a_id'];
                    $arr[$key]['children'][$key1]['children'][$key2]['type'] = 0;
                    $arr[$key]['children'][$key1]['children'][$key2]['typeFlag'] = "grade";
                    // 班级
                    $where4['g_id'] = array('EQ', $value2['g_id']);
                    $where4['a_id'] = array('EQ', $value1['a_id']);
                    $classList = $Class->where($where4)->select();
                    foreach ($classList as $key3 => $value3) {
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'] = array();
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['id'] = $value3['c_id'];
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['isStudent'] = 0;
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['name'] = "{$value3['name']}";
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['pId'] = $value2['g_id'];
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['type'] = 0;
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['typeFlag'] = "class";

                        //课程
                        $where5['g_id'] = array('EQ', $value3['g_id']);
                        $where5['s_id'] = array('EQ', $value1['s_id']);
                        $where5['a_id'] = array('EQ', $value2['a_id']);
                        $courseList = D('Course')->where($where5)->select();
                        foreach ($courseList as $key4 => $value4) {
                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['crs_id'] = $value4['crs_id'];
                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['isStudent'] = 0;
                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['name'] = $value4['name'];
                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['pId'] = $value3['c_id'];
                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['type'] = 0;
                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['typeFlag'] = 'course';
                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['showName'] = "{$value2['name']}" . " / " . "{$value3['name']}" . " / [" . "{$value4['name']}" . "]";
                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['istatue'] = $value2['g_id'] . "-" . $value3['c_id'] . "-" . $value4['crs_id'];
                        }
                    }
                }
            }
        }

        // }
        $data['code'] = 0;
        $data['data'] = $arr;

        $this->success($data);
    }


    //老师分组

    public function group_add()
    {
        $t_id = I('get.t_id');
        $data['id'] = I('get.id');
        $arrUids = explode(',', $t_id);
        if (empty($arrUids)) {
            $this->error('参数错误');
        }
        $TeacherGroupAccess = D('TeacherGroupAccess');
        $okCount = 0;
        foreach ($arrUids as $k => $v) {
            $data['t_id'] = $v;
            // 判断数据是否已存在
            $res = $TeacherGroupAccess->where(array('t_id' => $v, 'id' => $data['id']))->find();
            if (empty($res)) {
                $ret = $TeacherGroupAccess->add($data);
            } else {
                $this->error(' 分组中已存在 ');
            }
            if ($ret == 1) {
                $okCount++;  //处理成功记录数
            }
        }
        $content = ' 操作成功';
        if ($ret === false) {
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
            $this->error($TeacherGroupAccess->getError());
        } else {
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
            $this->success($content, U('/Admin/School/Teacher/index'));
        }
    }

    //教师授课
    public function lecture()
    {
        $t_id = I('post.t_id');//老师id
        $TeacherCourse = D('TeacherCourse');
        $Teacher = D('Teacher');
        $s_id = $Teacher->where(array("t_id" => $t_id))->getfield('s_id',false);//获取s_id
        //开启事物
        $TeacherCourse->startTrans();
        $state = '';
        // 教师授课 先把 该教师授过的课 删掉
        $res = $TeacherCourse->where(array('t_id' => $t_id))->delete();
        $GCCrsId = I('post.g_c_crs_id'); // 年级 班级 课程 拼接的字符串   组成数组里面的单元
        foreach ($GCCrsId as $key => $value) {
            $result = explode(',', $value);
            $data['t_id'] = $t_id;
            $data['g_id'] = $result['0'];
            $data['c_id'] = $result['1'];
            $data['crs_id'] = $result['2'];
            // 判断教师授课表中所受的课程是否已存在
            $crs_id = D('TeacherCourse')->where(array('crs_id' => $data['crs_id'], 'c_id' => $data['c_id']))->getField('crs_id',false);
            $teacherInfo = D('Course')->where(array('s_id' => $s_id, 'crs_id' => $data['crs_id']))->find();
            if (empty($teacherInfo)) {
                $Course = D('Course')->where(array('crs_id' => $crs_id))->getField('name', false);
                $this->error('选中的' . $Course . ',教师和课程不在同一学校');
            }
            if (empty($crs_id)) {
                $res = $TeacherCourse->add($data);
                $state = 1;
            } else {
                $state = 0;
                $Course = D('Course')->where(array('crs_id' => $crs_id))->getField('name', false);
                $this->error('选中的' . $Course . '已授课');
            }
        }

        if($state == true){
            $TeacherCourse->commit();
        }else{
            $TeacherCourse->rollback();
        }

        $content = "授课成功";
        if ($res === false) {
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
            $this->error($Course->getError());
        } else {
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
            $this->success($content, U('/Admin/School/Teacher/index'));
        }
    }

    //  获取 部门下拉列表
    public function get_list()
    {
        $a_id = I('get.a_id');
        // var_dump($a_id);
        if (!empty($a_id)) {
            $where['a_id|name'] = array('like', $a_id);
        }
        $where1['a_id'] = D('SchoolArea')->where($where)->getField('a_id', false);
        $arrRet = D('Dept')->where($where1)->getField('d_id as id , name as value');
        $this->success($arrRet);
    }

    //--------------------------------------分组查询-----------------------------------------------------------
    //查询数据
    public function query2()
    {
        $ids = $this->authRule();
        $where = "";
        if ($this->getType() == 1) {
            $where['s_id'] = $ids['s_id'];
        } elseif ($this->getType() == 3) {
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
        } elseif ($this->getType() == 4) {
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
            $where['stu_id'] = array('IN', $ids['stu_id']);
        } else {

        }
        $name = I('get.group_name');
        $s_id = I('get.s_id');
        $a_id = I('get.a_id');
        if (!empty($s_id)) {
            $where['s_id'] = $s_id;
        }
        if (!empty($a_id)) {
            $where['a_id'] = $a_id;
        }
        $page = I('get.page');
        $sort = I('get.sort', 'id');
        $order = $sort . ' ' . I('get.order', 'desc');
        $pagesize = I("get.pagesize", $this->PAGE_SIZE);
        $TeacherGroup = D('TeacherGroup');
        if (!empty($name)) {
            $where['group_name'] = array("like", "%$name%");;
        }
        $result = $TeacherGroup->queryListEX('*', $where, $order, $page, $pagesize, '');
        $this->success($result);
    }
    // 教师分组 get
    public function get2()
    {
        $stu_id = I('get.id/d', 0);
        $TeacherGroup = D('TeacherGroup');
        $area_info = $TeacherGroup->getInfo($stu_id);
        $res = D('TeacherGroupAccess')->where(array('id' => $area_info['id']))->getField('t_id', true);
        $t_id = '';
        foreach ($res as $key => $value) {
            $t_id = $t_id . $value . ",";
        }
        // var_dump($t_id);die;
        $where1['t_id'] = array('IN', rtrim($t_id, ','));
        // 获取在该分组中的教师
        $list = D('Teacher')->where($where1)->getField('t_id,name');
        if ($stu_id > 0 && empty($area_info)) {
            $this->error('不存在');
        }
        // var_dump($area_info);
        $data['data'] = $area_info;
        $data['teacher'] = $list;
        $this->success($data);
    }

    //新增/编辑
    public function edit2()
    {
        $id = I('post.id/d', 0);
        $data['id'] = $id;
        $data['group_name'] = I('post.group_name');
        $data['memo'] = I('post.memo');
        $data['s_id'] = I('post.s_id');
        $data['a_id'] = I('post.a_id');
        $teacher = I('post.teacher');
        $arrUids = explode(',', $teacher);
        $res = D('TeacherGroupAccess')->where(array("id" => $id))->delete();
        foreach ($arrUids as $k => $v) {
            $data1['t_id'] = $v;
            $data1['id'] = $id;
            $ret = D('TeacherGroupAccess')->add($data1);
        }
        $TeacherGroup = D('TeacherGroup');
        if (!$TeacherGroup->create($data)) {
            $this->error($TeacherGroup->getError());
        } else {
            if ($id > 0) {
                $ret = $TeacherGroup->save($data);
            } else {
                $ret = $TeacherGroup->add($data);
            }
            $content = ($id > 0 ? '编辑' : '新建') . '成功';
            if ($ret === false) {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
                $this->error($TeacherGroup->getError());
            } else {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                $this->success($content, U('/Admin/School/Teacher/index'));
            }
        }
    }


    //删除
    public function del2()
    {
        $id = I('post.id');
        $arrUids = explode(',', $id);
        if (empty($arrUids)) {
            $this->error('参数错误');
        }
        $TeacherGroup = D('TeacherGroup');
        $okCount = 0;
        foreach ($arrUids as $k => $v) {

            $where['id'] = $v;
            $ret = $TeacherGroup->where($where)->delete();
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


    /**
     *  导入数据
     */
    public function import()
    {


        $file_url = I('post.file_url');
        if (empty($file_url)) {
            $this->error("参数错误");
        }
        $s_id = I('get.s_id');//学校id
        $a_id = I('get.a_id');//校区id
        $g_id = I('get.g_id');//年级id
        $c_id = I('get.c_id');//班级id
        $file_url = $_SERVER['DOCUMENT_ROOT'] . $file_url;
        $arrField = array("教师姓名" => 'name',
            '部门' => 'd_id',
            '性别' => 'sex',
            '联系方式' => 'phone',
            '邮件' => 'email',
            '登录帐号' => 'user_name');
        // 读取.xls 模板的数据
        $arrList_tmp = import_excel($arrField, $file_url);
        // 没有name d_id 的数据就过滤掉
        $arrList = array();
        foreach ($arrList_tmp as $key => $value) {
            if ($value['name'] != '' || $value['d_id'] != '') {
                $arrList[] = $value;
            }
        }
        if ($arrList == false) {
            $this->error('表格内容不完整');
            # code...
        }
        unlink($file_url);
        if (count($arrList) > 500) {
            $content = '导入数据不能超过500条';
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
            $this->error('导入数据不能超过500条');
        }
        $Teacher = D('Teacher');
        //检查数据有效性

        foreach ($arrList as $key => $value) {
            $value['t_id'] = "0";
            // var_dump($value);
            if (!$Teacher->create($value)) {
                $key += 2;
                D('SystemLog')->writeLog($this->getModule(), '教师管理，导入失败,第' . $key . '行数据错误:' . $Teacher->getError(), $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                $this->error('第' . $key . '行数据错误:' . $Teacher->getError());
            }
        }

        // var_dump($arrList);die;
        //开启事务处理
        $Teacher->startTrans();
        $state = ''; // 判定状态
        foreach ($arrList as $key => $value) {
            $value['s_id'] = $s_id;//学校sid
            $value['a_id'] = $a_id;//学校aid
            $Dept = D('Dept')->where(array('name' => $value['d_id'], 'a_id' => $a_id))->find();
            if (empty($Dept)) {
                $this->error($value['name'] . '部门不存在');
            } else {
                $value['d_id'] = $Dept['d_id'];
            }
            if ($value['sex'] == "男") {
                $value['sex'] = "1";
            } else {
                $value['sex'] = "0";
            }
            $value['create_time'] = date('Y-m-d H:i:s');
            // 往教师表中插入数据
            $ret = $Teacher->add($value);

            $data['username'] = $value['user_name'];
            $data['name'] = $value['name'];
            $data['type'] = 3;
            $data['s_id'] = $s_id;
            $data['a_id'] = $a_id;
            $data['g_id'] = $g_id;
            $data['c_id'] = $c_id;
            $data['sex'] = $value['sex'];

            $data['password'] = "e10adc3949ba59abbe56e057f20f883e"; //默认123456
            $data['create_time'] = date('Y-m-d H:i:s');
            // 往user表中插入数据 并判断 模板中 有没有登陆账号重复的
            $res = $this->adduser($data,$key);
//            $res = D('AdminUser')->add($data);
            $data1['u_id'] = $res;
            $result = $Teacher->where(array('t_id' => $ret))->save($data1);//绑定user 表

            if ($res === false || $ret === false) {
                $state = 0;
//                $Teacher->rollback();
            } else {
                $state = 1;
                //添加视频通讯录 BY:Meng Fanmin 2017.06.15
                video_add_user($res);
                easemob_add_user($res);
//                $Teacher->commit();
            }
        }
        if($state === 1){
            $Teacher->commit();
        }else{
            $Teacher->rollback();
        }
        $content = '导入成功';
        if ($res === false) {
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
            $this->error($Teacher->getError());
        } else {
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
            $this->success($content, U('/Admin/School/Teacher/index'));
        }

    }


    // 教师相关联的用户表插入数据 create
    public function  adduser($data,$key){
        $key += 2;
        $userInfo = D('AdminUser');
        if (!$userInfo->create($data)) {
            $this->error('第' . $key . '行数据错误:' . $userInfo->getError());
        } else {
            $res = $userInfo->add($data);
            return $res;
        }
    }



    //获取授课数据
    public function get_teacher_course()
    {
        $t_id = I('get.t_id');
        $where['t_id'] = array('EQ', $t_id);
        $teacherInfo = D('TeacherCourse')->where($where)->select();
        $arr = array();
        if (!empty($teacherInfo)) {
            foreach ($teacherInfo as $key => $value) {
                $arr[$key]['chkDisabled'] = 'false';
                $arr[$key]['classesId'] = $value['c_id'];
                $arr[$key]['gradesId'] = $value['g_id'];
                $arr[$key]['id'] = $value['crs_id'];
                $arr[$key]['isStudent'] = 0;
                $arr[$key]['istatue'] = $value['g_id'] . '-' . $value['c_id'] . '-' . $value['crs_id'];
                $arr[$key]['name'] = "{$this->get_grade_name($value['g_id'])[$value['g_id']]}" . " / " . "{$this->get_class_name($value['c_id'])[$value['c_id']]}" . " / [" . "{$this->get_course_name($value['crs_id'])[$value['crs_id']]}" . "]";
                $arr[$key]['open'] = 'true';
                $arr[$key]['pId'] = "{$value['c_id']}";
                $arr[$key]['type'] = 0;
                $arr[$key]['typeFlag'] = 'def';
            }
        }


        $this->success($arr);
    }

    public function get_list1()
    {
        $arrRet = array();
        $type = I("get.type");
        $ids = $this->authRule();
        $where = "";
        if ($this->getType() == 1) {
            $where['s_id'] = $ids['s_id'];
        } elseif ($this->getType() == 3) {
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
        } elseif ($this->getType() == 4) {
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
            $where['stu_id'] = array('IN', $ids['stu_id']);
        } else {

        }
        $s_id = I('get.s_id');
        $a_id = I('get.a_id');
        if (!empty($s_id)) {
            $where['s_id'] = $s_id;//学校s_id
            # code...
        }
        if (!empty($a_id)) {
            $where['a_id'] = $a_id;//学校a_id
            # code...
        }
        // var_dump($where);die;
        switch ($type) {
            case 'dept': //部门
                $arrRet = D('Dept')->where($where)->getField('d_id as id , name as value');
                break;
            case 'teachergroup': //组名
                $arrRet = D('TeacherGroup')->where($where)->getField('id , group_name as value');
                break;
            default:
                $arrRet = D('Material')->getField('m_id as id,m_jc,m_mc ', '-');
                break;
        }
        $this->success($arrRet);
    }
}
