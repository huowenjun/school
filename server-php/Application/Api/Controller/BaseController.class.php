<?php
/**
 * Base 基类
 * 生成时间：2015-01-01
 * 作者：qianzhiqiang
 * 修改时间：
 * 修改备注：
 */

namespace Api\Controller;

use Think\Controller;

class BaseController extends Controller
{

    public $PAGESIZE = 20;
    protected $loginUser = array();

    public function __construct()
    {
        parent::__construct();
        $this->init();
    }

    /**
     * 后台控制器初始化
     */
    protected function init()
    {
        $version_name = I('post.version_name');
        $token = I('post.token');
        if ($version_name > '2.2.1') {
            if (!$token) {
                $this->error('token error');
            }
            $tokenInfo = decrypt_ase($token);
            if (!$tokenInfo) {//解密失败
                $this->error('token error');
            }
            $tokenInfo = json_decode($tokenInfo);
            if ($tokenInfo[2] < time()) {//token 过期
                $this->error('token error');
            }
            $auth = array(
                'user_id' => $tokenInfo[1],
                'username' => $tokenInfo[0]
            );
            session('admin_user', $auth);
        }
        //检测用户登录
        define('ADMIN_ID', $this->isLogin());
        $login = substr(CONTROLLER_NAME, 0, 5);
        //dump($login);exit;
        if (!ADMIN_ID && ('Login' <> $login)) {
            if (IS_AJAX) {
                $this->error('请先登录');
            } else {
                C('CONTROLLER_LEVEL', 2);
                if ('Index' == CONTROLLER_NAME) {
                    redirect(U('/Admin/Login'));
                } else {
                    $this->error('用户未登录请先登录');
                }

            }
        }
        if ('Login' <> $login) {
            //设置登录用户信息
            $this->loginUser = session('admin_user');
            //检测权限 //赋值当前菜单
            // $this->checkAuth();

        }

        $arrModule = explode('/', CONTROLLER_NAME);
        //dump(session('admin_user'));
        $this->assign('modulename', $arrModule[0]);
        //trace($arrModule[0]);

        //使用redis存储统计信息
        $this->requestCount($arrModule[0]);

    }

    //redis请求统计 计数器
    public function requestCount($model)
    {
        $userInfo = session('admin_user');
        $redis = new \Redis();
        $redis->connect('127.0.0.1', '6379');
        $redis->auth('School1502');
        $time = date('YmdH');
        $systemModel = C('SYSTEM_MODEL')[$userInfo['system_model']];
        $userType = C('USER_TYPE_EN')[$userInfo['type']];
        $userId = $userInfo['user_id'];

        $redis->incr($systemModel . ':' . $userType . ':' . $time . ':' . $userId);
        $redis->incr($systemModel . ':' . $userType . ':' . $model . ':' . $time . ':' . $userId);
    }

    protected function isLogin()
    {
        $user = session('admin_user');
        if (empty($user)) {
            return 0;
        } else {
            $uid = $user['user_id'] ? $user['user_id'] : 0;
            if ($uid == 0) {
                return 0;
            } else {
                $ip = F('admin_user_' . $uid);
                return $uid;
                if ($ip == get_client_ip())
                    return $uid;
                else
                    return 0;
            }
        }
    }

    protected function getUserId()
    {
        return $this->loginUser['user_id'];
    }

    protected function getUserName()
    {
        return $this->loginUser['username'];
    }

    protected function getName()
    {
        return $this->loginUser['name'];
    }

    protected function getUserType()
    {
        return $this->loginUser['type'];
    }

    protected function getApiId()
    {
        return $this->loginUser['api_id'];
    }

    protected function getSecurityKey()
    {
        return $this->loginUser['security_key'];
    }


    protected function getModuleName()
    {
        return CONTROLLER_NAME;
    }

    //验证接口是否合法
    protected function checkToken($param)
    {
        $server_token = md5($this->getModule() . date('Ymd', time()) . $this->getSecurityKey());
        //return $server_token; //for debug
        //与客户端提交的token进行比较
        if ($param != $server_token) {
            return true;
        } else {
            return true;
        }
    }

    //验证接口是否合法最终版
    protected function checkTokenSure($param)
    {
        $token = 'SHUHAIXINXI';
        $arr = array($token, $this->getModule(), session('admin_user')['timestamp'], session('admin_user')['user_id']);
        sort($arr);
        $tmpStr = implode('', $arr);
        $server_token = md5($tmpStr);
        //return $server_token; //for debug
        //与客户端提交的token进行比较
        if ($param != $server_token) {
            return false;
        } else {
            return true;
        }
    }

    // 年级班级返回 
    protected function getClass()
    {
        $where['u_id'] = $this->getUserId();
        $arrRet = array();
        $tId = D('Teacher')->where($where)->find();

        if (!empty($tId)) {
            $where1['t_id'] = array('EQ', $tId['t_id']);
            $arrClass = D('TeacherCourse')->field("distinct g_id as g_id")->where($where1)->select();
            $grade = D('Grade')->getField('g_id,name');
            $classInfo = D('Class')->getField('c_id,name');
            foreach ($arrClass as $key => $value) {
                $arrRet[$key]['id'] = $value['g_id'];
                $arrRet[$key]['name'] = $grade[$value['g_id']];
                $arrRet[$key]['pId'] = 0;
                $arrRet[$key]['s_type'] = 'grade';
                $where2['g_id'] = $value['g_id'];
                $arrClass1 = D('TeacherCourse')->where($where2)->select();
                foreach ($arrClass1 as $key1 => $value1) {
                    $arrRet[$key]['children'][$key1]['children'] = array();
                    $arrRet[$key]['children'][$key1]['id'] = $value1['c_id'];
                    $arrRet[$key]['children'][$key1]['name'] = $classInfo[$value1['g_id']];
                    $arrRet[$key]['children'][$key1]['pId'] = $value['g_id'];
                    $arrRet[$key]['children'][$key1]['s_type'] = 'class';
                }
            }
        } else {
            $arrRet = false;
        }

        return $arrRet;
    }

    //课程
    protected function getCourse()
    {
        $where['u_id'] = $this->getUserId();
        $arrRet = array();
        $tId = D('Teacher')->where($where)->find();
        if (!empty($tId)) {
            $where1['t_id'] = array('EQ', $tId['t_id']);
            $arrClass = D('TeacherCourse')->select();
            $courseInfo = D('course')->getField('crs_id,name');
            foreach ($arrClass as $key => $value) {
                $arrRet[$key]['id'] = $value['crs_id'];
                $arrRet[$key]['name'] = isset($courseInfo[$value['crs_id']]) ? $courseInfo[$value['crs_id']] : $value['crs_id'];
            }
        } else {
            $arrRet = false;
        }

        return $arrRet;
    }

    protected function getModule()
    {
        // $moduleName    =   defined('MODULE_ALIAS')? MODULE_ALIAS : MODULE_NAME;
        // $action = explode('/',CONTROLLER_NAME);
        // $actionName="";
        // $nCount = intval(count($action) - 1);
        // if($nCount >= 0 && $action[$nCount] != ACTION_NAME){

        //     $actionName =   '/' . ACTION_NAME;
        // }
        // $moduleName = '/' . $moduleName . '/' . CONTROLLER_NAME . $actionName;
        // $moduleName = str_replace("_handle", '', $moduleName);
        $moduleName = defined('MODULE_ALIAS') ? MODULE_ALIAS : MODULE_NAME;
        $action = explode('/', CONTROLLER_NAME);
        $moduleName = '/' . $moduleName . '/' . CONTROLLER_NAME;

        return $moduleName;
    }

    protected function getLockValue()
    {
        return $this->loginUser['lock'];
    }

    protected function getDataAccess()
    {
        return $this->loginUser['bm2'];
    }

    //获取学生姓名
    protected function get_stu_name($stu_id)
    {
        $where['stu_id'] = $stu_id;
        $studentInfo = D('Student')->where($where)->getField('stu_id,stu_no,stu_name,sex');
        return $studentInfo;
    }

    //获取课程
    protected function get_course_name($crs_id)
    {
        $where['crs_id'] = $crs_id;
        $courseInfo = D('Course')->where($where)->getField('crs_id,name');
        return $courseInfo;
    }

    //获取老师teacher 年级 grade 班级 class 名称
    protected function get_teacher_name($t_id, $type)
    {
        if ($type == 1) {
            $where['t_id'] = $t_id;
            $id = "t_id";
        } else {
            $id = "u_id";
            $where['u_id'] = $t_id;
        }

        $courseInfo = D('Teacher')->where($where)->getField($id . ',name');
        return $courseInfo;
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

    //返回家长电话
    protected function get_parent_phone($stu_id)
    {
        $where['stu_id'] = array('EQ', $stu_id);
        $parentId = D('StudentGroupAccess')->where($where)->getField('sp_id', true);

        $where1['sp_id'] = array('IN', implode(',', $parentId));
        $parentPhone = D('StudentParent')->where($where1)->select();

        $Phone = array();

        foreach ($parentPhone as $k => $v) {
            $userinfo = M('User', 'think_')->field('user_id,name,phone,sex')->where('user_id = ' . $v['u_id'])->find();
            $Phone[$k]['stu_id'] = $stu_id;
            $Phone[$k]['sp_id'] = $v['sp_id'];
            $Phone[$k]['user_id'] = $v['u_id'];
            $Phone[$k]['name'] = $userinfo['name'];
            $Phone[$k]['phone'] = $userinfo['phone'];
            $Phone[$k]['sex'] = $userinfo['sex'];
        }
        return $Phone;
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

    //获取老师任课班级年级
    public function get_teacher_course($t_id)
    {
        $courseList = D('TeacherCourse')->where(array('t_id' => $t_id))->select();
        $arr = array();
        foreach ($courseList as $key => $value) {
            $arr[$key]['name'] = $this->get_grade_name($value['g_id'])[$value['g_id']] . "-" . $this->get_class_name($value['c_id'])[$value['c_id']];
        }
        return $arr;
    }

    public function sendSms($phone, $msg)
    {
        vendor('ChuanglanSmsHelper.ChuanglanSmsApi');
        $clapi = new \ChuanglanSmsApi();
        $result = $clapi->sendSMS($phone, $msg, 'true');
        $result = $clapi->execResult($result);
        //var_dump($result);
        if ($result[1] == 0) {
            \Think\Log::write('短信发送成功', 'WARN');
            return true;
        } else {
            \Think\Log::write('短信发送失败,电话号码:' . $phone, 'WARN');
            return false;
        }
    }

    //获取教师班级年级(包括代课和班主任班级)
    public function getTeacherClass($tId)
    {
        //获取代课班级
        $teacherList = D('TeacherCourse')->where(array('t_id' => $tId))->Field('g_id,c_id')->group("c_id")->select();
        //获取班主任班级
        $headTeacher = M("Class")->where(array('t_id' => $tId))->Field('g_id,c_id')->select();

        $newList = array_merge($teacherList, $headTeacher);

        $gId = array();
        $cId = array();
        $arr = array();
        foreach ($newList as $key => $value) {
            $gId[] = $value['g_id'];
            $cId[] = $value['c_id'];
        }
        $gId = array_unique($gId);
        $cId = array_unique($cId);
        $arr['g_id'] = implode(',', $gId);
        $arr['c_id'] = implode(',', $cId);

        return $arr;
    }


    //数据权限
    protected function authRule()
    {
        //查询登录权限
        $where['u_id'] = $this->getUserId();
        $scoolInfo = D('SchoolInformation')->where($where)->find();//学校表
        $studentInfo = D('StudentParent')->where($where)->find(); //学生家长表
        $teacherInfo = D('Teacher')->where($where)->find(); //教师表
        $sId = 0;
        $aId = 0;
        $gId = 0;
        $cId = 0;
        $stu_id1 = 0;
        if (!empty($scoolInfo)) {
            $sId = $scoolInfo['s_id'];
        }
        if (!empty($studentInfo)) {
            $where2['sp_id'] = $studentInfo['sp_id'];
            $panentList = D('StudentGroupAccess')->where($where2)->select();
            $stu_id = array();
            foreach ($panentList as $key => $value) {
                $stu_id[] = $value['stu_id'];
            }
            $stuId = implode(',', $stu_id);
            $where1['stu_id'] = array('IN', $stuId);
            $stInfo = D('Student')->where($where1)->select();
            $sId = $stInfo[0]['s_id'];
            $aId = $stInfo[0]['a_id'];
            $gId = $stInfo[0]['g_id'];
            $cId = $stInfo[0]['c_id'];
            $stu_id1 = $stuId;
        }
        if (!empty($teacherInfo)) {
            $sId = $teacherInfo['s_id'];
            $aId = $teacherInfo['a_id'];
            $tId = $teacherInfo['t_id'];
        }

        return array('s_id' => $sId, 'a_id' => $aId, 'g_id' => $gId, 'c_id' => $cId, 'stu_id' => $stuId, 't_id' => $tId);

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
                    $alert['user_type'] = 3;//华为推送判定
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

}
