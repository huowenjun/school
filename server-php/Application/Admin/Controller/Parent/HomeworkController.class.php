<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-作业管理
 */

namespace Admin\Controller\Parent;

use Admin\Controller\BaseController;

class HomeworkController extends BaseController
{

    /**
     *作业管理
     */
    public function index()
    {

        $this->display();
    }

    //查询数据
    public function query()
    {
        //权限
        $ids = $this->authRule();
        // var_dump($ids);
        // $map="";
        $map = "";
        if ($this->getType() == 1) {// 学校管理员
            $map['s_id'] = $ids['s_id'];
        } elseif ($this->getType() == 3) {// 老师
            $map['s_id'] = $ids['s_id'];
            $map['a_id'] = $ids['a_id'];
            $classList = $this->getTeacherClass($ids['t_id']);
            $map['g_id'] = array('IN', $classList['g_id']);//$ids['a_id'];
            $map['c_id'] = array('IN', $classList['c_id']);
            $map['user_id'] = $this->getUserId();
        } elseif ($this->getType() == 4) {// 家长
            $map['s_id'] = $ids['s_id'];
            $map['a_id'] = $ids['a_id'];
            $classList = $this->getStudentClass($ids['stu_id']);
            // var_dump($classList);
            $map['g_id'] = array('IN', $classList['g_id']);
            $map['c_id'] = array('IN', $classList['c_id']);
        } else {
            $arr = $_GET;
            $sthInfo = getSchoolId($arr);
            $map['s_id'] = array('IN', $sthInfo);
        }
        // var_dump($map);
        $teacher = I('get.user_id');// 老师
        $subject = I('get.crs_id');// 学科
        $sId = I('get.s_id');
        $aId = I('get.a_id');
        $gId = I('get.g_id');
        $cId = I('get.c_id');
        $stuId = I('get.stu_id');
        // var_dump($stuId);
        $page = I('get.page');
        $pagesize = I('get.pagesize', $this->PAGE_SIZE);
        $sort = I('get.sort', 'h_id');
        $order = $sort . ' ' . I('get.order', 'desc');
        $Homework = D('Homework');


        if ($teacher != '') {
            $where = "name like '%" . $teacher . "%'";
            $teaInfo = M('user', 'think_')->where($where)->select();
            $u_id = "";
            if (empty($teaInfo)) {
                $this->error('输入的发布人不存在！');
            } else {
                foreach ($teaInfo as $key => $value) {
                    $u_id = $u_id . $value['user_id'] . ",";
                }
            }
            $map['user_id'] = array('IN', rtrim($u_id, ','));
        }
        if ($subject != '') {
            $where = "name like '%" . $subject . "%'";
            $crsInfo = D('Course')->where($where)->select();
            $crs_id = "";
            if (empty($crsInfo)) {
                $this->error('输入的学科不存在！');
            } else {
                foreach ($crsInfo as $key => $value) {
                    $crs_id = $crs_id . $value['crs_id'] . ",";
                }
            }
            $map['crs_id'] = array('IN', rtrim($crs_id, ','));
        }
        // if ($subject) $map['crs_id']=$subject;

        if (!empty($sId)) {
            $map['s_id'] = $sId;
        }
        if (!empty($aId)) {
            $map['a_id'] = $aId;
        }
        if (!empty($gId)) {
            $map['g_id'] = $gId;
        }
        if (!empty($cId)) {
            $map['c_id'] = $cId;
        }
        if (!empty($stuId)) {
            // $map['stu_id'] = $stuId;
            $classList = $this->getStudentClass($stuId);
            // var_dump($classList);
            $map['g_id'] = array('IN', $classList['g_id']);
            $map['c_id'] = array('IN', $classList['c_id']);
            // var_dump($map['g_id']);
        }
        // $where = "1=1";
        // echo "<pre>";   // 
        // var_dump($map);
        $result = $Homework->queryListEx('*', $map, $order, $page, $pagesize, '');
        // var_dump($result);
        $this->success($result);
    }

    public function get()
    {
        $h_id = I('get.h_id/d', 0);
        $Homework = D('Homework');
        $homework_info = $Homework->getInfo($h_id);
        if ($h_id > 0 && empty($homework_info)) {
            $this->error('不存在');
        }
        $this->success($homework_info);
    }


    public function detailGet()
    {
        $h_id = I('get.h_id/d', 0);
        $Homework = D('Homework');
        $homework_info = $Homework->getInfo($h_id);
        $where1['h_id'] = $h_id;
        $homeworkFileInfo = D('HomeworkFile')->where($where1)->getField('file_path', true);
//        var_dump($homeworkFileInfo);
        //音频和图片
        $imgData = array();
        $audioData = array();
        foreach ($homeworkFileInfo as $k => $v) {
            $file = pathinfo($v, PATHINFO_EXTENSION);//详细信息
            if ($file != 'amr') {
                $imgData[] = C('ONLINE_NAME') . $v;//图片
            } else {
                $audioData[] = C('ONLINE_NAME') . $v;//音频
            }
        }
//        var_dump($imgData);
//        var_dump($audioData);
        $arr = array();
        $arr['name1'] = $homework_info['name'];
        $where['crs_id'] = $homework_info['crs_id'];
        $courseInfo = D('course')->where($where)->getField('crs_id,name');
        $arr['crs_id1'] = implode($courseInfo);
        $arr['work_content1'] = $homework_info['work_content'];
        $arr['finish_time1'] = $homework_info['finish_time'];
        $arr['create_time1'] = $homework_info['create_time'];
        $arr['img'] = implode($imgData);
        $str = implode($audioData);
        $audio = file_get_contents($str);
        $arr['audio'] = base64_encode($audio);
        if ($h_id > 0 && empty($homework_info)) {
            $this->error('不存在');
        }
        $this->success($arr);
    }

    //新增/编辑
    public function edit()
    {
        //权限
        $ids = $this->authRule();
        $Homework = D('Homework');
        $id = I('post.h_id/d', 0);
        $data['h_id'] = $id;
        $data['user_id'] = $this->getUserId();
        $authId = $this->authRule();
        $data['s_id'] = $authId['s_id'];
        $data['name'] = I('post.name');
        $data['s_id'] = I('post.s_id');
        $data['a_id'] = I('post.a_id');
        $data['g_id'] = I('post.g_id');
        $data['c_id'] = I('post.c_id');
        $data['crs_id'] = I('post.crs_id');
        $data['work_content'] = I('post.work_content');
        $data['finish_time'] = I('post.finish_time');
        if ($this->getType() == 1) {// 学校管理员
            $data['s_id'] = $ids['s_id'];
        } elseif ($this->getType() == 3) {// 老师
            $data['s_id'] = $ids['s_id'];
            $data['a_id'] = $ids['a_id'];
        } elseif ($this->getType() == 4) {// 家长
            $this->error('对不起,您没有权限');
        } else {

        }

        if (!$Homework->create($data)) {
            $this->error($Homework->getError());
        } else {
            if ($id > 0) {
                $ret = $Homework->save($data);//修改
            } else {
                $data['create_time'] = date('Y-m-d H:i:s');
                $ret = $Homework->add($data);//添加
                $id = $ret;
            }
            $info = ($id > 0 ? '编辑' : '新建') . '成功';
            //班级ID=》学生id=》所有学生设备的imie号，=》循环task：imie=》（作业内容）
            //作业内容
            $content = utf8_unicode($data['work_content']);
            $content = iconv("utf-8", "gb2312", $content);
            //imie
            $studentM = D('student');
            $imieList = $studentM->field('g_id,c_id,a_id,s_id,imei_id')->where(['g_id' => $data['g_id'], 'c_id' => $data['c_id'], 'a_id' => $data['a_id'], 's_id' => $data['s_id'], 'devicetype' => 2])->select();
            foreach ($imieList as $value) {
                $num = ($id % 3) + 1;
                $Content = "HOMEWORK," . $content . ",$num";
                $content_len_16 = get_len_16($Content);
                $command = '[3G*' . $value['imei_id'] . '*' . $content_len_16 . '*' . $Content . ']';
                taskRedis($value['imei_id'], $command);
            }
            if ($ret === false) {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
                $this->error($Course->getError());
            } else {
                //app消息推送 By:Meng Fanmin 2017.06.26
                //线上服务器下发APP公告推送 By:Meng Fanmin 2017.06.23
                if ($_SERVER['SERVER_ADDR'] == C('SERVERADDR')) {
                    $parentId = getParentId(I('post.c_id'));
                    $classInfo = getClassInfo(I('post.c_id'));
                    $pushArr = array(
                        'fsdx' => '0,1',
                        'u_id' => $parentId,
                        'ticker' => '作业消息',
                        'title' => $classInfo['g_name'] . $classInfo['c_name'] . I('post.name'),
                        'text' => $classInfo['g_name'] . $classInfo['c_name'] . I('post.name'),
                        'after_open' => 'go_custom',
                        'activity' => 'homework',
                        'id' => $id,
                    );
                    sendAppMsg($pushArr);
                }
                //将作业内容更新到作业语音表 By:Meng Fanmin 2017.07.04
                $Hook = new \Api\Controller\HookController();
                $Hook->getSchoolHomework($data['c_id']);

                D('SystemLog')->writeLog($this->getModule(), $info, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                $this->success($info, U('/Admin/Parent/Homework/index'));
            }
        }
    }

    //删除
    public function del()
    {
        $uIds = I('post.id');
        $arrUids = explode(',', $uIds);
        if (empty($arrUids)) {
            $this->error('参数错误');
        }
        $Homework = D('Homework');
        $okCount = 0;
        foreach ($arrUids as $k => $v) {
            $where['h_id'] = $v;
            $ret = $Homework->where($where)->delete();
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

    public function getList()
    {
        $g_id = I('get.g_id');
        $user_id = $this->getUserId();
        $whereUser['user_id'] = $user_id;
        $type = D('AdminUser')->where($whereUser)->find();
        if ($type['type'] == 3) {
            $whereUid['u_id'] = $user_id;
            $tId = D('Teacher')->where($whereUid)->find();
            $whereTid['t_id'] = $tId['t_id'];
//            $whereTid['g_id']=$g_id;
            $crsId = D('TeacherCourse')->where($whereTid)->select();
            $result = array();
            foreach ($crsId as $key => $value) {
                $courseInfo = D('course')->getField('crs_id as id,name as value');
                $result[$value['crs_id']] = $courseInfo[$value['crs_id']];
//                var_dump($result);die;
            }
        } else {
            if (!empty($g_id)) {
                $where['g_id|name'] = array('like', '%' . $g_id . '%');
            }
            $where1['g_id'] = D('Grade')->where($where)->getField('g_id', false);
            $result = D('Course')->where($where1)->getField('crs_id as id , name as value');
//            var_dump($result);die;
        }

        $this->success($result);
    }
}