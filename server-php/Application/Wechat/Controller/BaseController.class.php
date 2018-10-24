<?php
/**
 * Base 基类
 * 生成时间：2015-01-01
 * 作者：qianzhiqiang
 * 修改时间：
 * 修改备注：
 */
namespace Wechat\Controller;
use Think\Controller;
class BaseController extends Controller {
    
    public $PAGE_SIZE = 15;
    protected $loginUser = array();
    
    public function __construct()
    {
        parent::__construct();
        $this->init();      
    }
    
    /**
     * 后台控制器初始化
     */
  
   protected function init(){
        //检测用户登录
        define('ADMIN_ID',$this->isLogin());
        $login = substr(CONTROLLER_NAME, 0,5);
        //dump($login);exit;
        if( !ADMIN_ID && ('Login' <> $login)){
            if(IS_AJAX){
                $this->error('请先登录');
            }else{
                C('CONTROLLER_LEVEL',2);
                if('Index' == CONTROLLER_NAME){
                    redirect(U('/Wechat/Bind'));
                }else{
                    redirect(U('/Wechat/Bind'));
//                    $this->error('用户未登录请先登录',U('/Wechat/Bind'),0);
                }

            }
        }
        if('Login' <> $login){
            //设置登录用户信息
            $this->loginUser = session('admin_user');
            //检测权限 //赋值当前菜单
           // $this->checkAuth();

        }
        // dump($this->authRule());
        $this->assign('username',$this->getUserName());  //用户名
        $this->assign('name',$this->getName());  //用户姓名
        $this->assign('userid',$this->getUserId()); //用户ID
        $this->assign('isadmin',$this->loginUser['admin']);
        $this->assign('group_id',$this->getGroupId()); //用户角色id
        $this->assign('user_type',$this->getType()); //用户类型 1 系统用户 0 学校用户
        $this->ruleList=$this->ruleList();
        $arrModule=  explode('/', CONTROLLER_NAME);
        //dump(session('admin_user'));
        $this->assign('modulename',$arrModule[0]);
        //trace($arrModule[0]);

    }


      // 老师树桩
    protected function getTreeList1(){
          

        $where['u_id'] = $this->getUserId();
        $arrRet = array();
        $Grade = D('Grade');
        $Class = D('Class');
        $Student = D('Student');
        $tId = D('Teacher')->where($where)->find();
                  // echo 123123;die;

        // var_dump($tId);die;
        if (!empty($tId)) {
        $where1['t_id'] = array('EQ',$tId['t_id']);
        $arrClass = D('TeacherCourse')->field("distinct g_id as g_id,c_id")->where($where1)->select();
        // if (empty($arrClass)) {
        // $arrClass = '';
        $Classlist = D('Class')->field("distinct g_id as g_id,c_id")->where($where1)->select();
        $gId = array();$cId = array();
        foreach ($Classlist as $key => $value) {
            $gId[] = $value['g_id'];
            $g_id1 = implode(',', $gId);
         

            $cId[] = $value['c_id'];
            $c_id1 = implode(',', $cId);
            // var_dump($c_id);die;
            }
            
            $arr="";$where1="";
            $gId = array();$cId = array();
            foreach ($arrClass as $key => $value) {
                $gId[] = $value['g_id'];
                $g_id = implode(',', $gId);
             

                $cId[] = $value['c_id'];
                $c_id = implode(',', $cId);
                // var_dump($c_id);die;
                }

                $where1['g_id'] = array('IN',$g_id.",".$g_id1);
                $where2['c_id'] = array('IN',$c_id.",".$c_id1);
                // var_dump($where2);die;
                    //年级
                    // $where1['g_id'] = array('EQ',$value['g_id']);
                    $gradeList = $Grade->where($where1)->select();
                    foreach ($gradeList as $key => $value) {
                        $arr[$key]['children'] = array();
                        $arr[$key]['id']=$value['g_id'];
                        $arr[$key]['isStudent']=0;
                        $arr[$key]['name']="{$value['name']}";
                        $arr[$key]['pId']=0;
                        $arr[$key]['type']=0;
                        $arr[$key]['typeFlag']="grade";
                        //班级
                        $where2['g_id'] = array('EQ',$value['g_id']);
                        // var_dump($where2);die;
                        // $where2['a_id'] = array('EQ',$value2['a_id']);
                        $classList = $Class->where($where2)->select();
                        // echo M()->_sql();
                        // var_dump($classList);die;
                        foreach ($classList as $key1 => $value1) {
                            $arr[$key]['children'][$key1]['children'] = array();
                            $arr[$key]['children'][$key1]['id']=$value1['c_id'];
                            $arr[$key]['children'][$key1]['isStudent']=0;
                            $arr[$key]['children'][$key1]['name']="{$value1['name']}";
                            $arr[$key]['children'][$key1]['pId']=$value['g_id'];
                            $arr[$key]['children'][$key1]['type']=0;
                            $arr[$key]['children'][$key1]['typeFlag']="class";
                            //学生
                            $where3['c_id'] = array('EQ',$value1['c_id']);
                            $where3['g_id'] = array('EQ',$value['g_id']);
                            $studentList = $Student->where($where3)->select();
                            foreach ($studentList as $key2 => $value2) {
                                  if ($value2['sex'] == 1) {
                                    $value2['sex'] = "man";
                                }else{
                                    $value2['sex']  = "women";
                                }
                                $arr[$key]['children'][$key1]['children'][$key2]['id']=$value2['stu_id'];
                                $arr[$key]['children'][$key1]['children'][$key2]['isStudent']=0;
                                $arr[$key]['children'][$key1]['children'][$key2]['name']="{$value2['stu_name']}";
                                $arr[$key]['children'][$key1]['children'][$key2]['pId']=$value1['c_id'];
                                $arr[$key]['children'][$key1]['children'][$key2]['type']=0;
                                $arr[$key]['children'][$key1]['children'][$key2]['iconSkin']=$value2['sex'];

                                $arr[$key]['children'][$key1]['children'][$key2]['typeFlag']="student";
                            }
                        }
                    }
                // $cId[] = $value['c_id'];
                
                // $arrRet['c_id']  = implode(',', $cId);
                // $arrRet['g_id']  = implode(',', $gId);
                // }
            }else{
             $arrRet = false;
         }
        $data['code'] = 0;
        $data['data'] = $arr;
        return json_encode($data);
    }
    



    //课程
    protected function getCourse($userId){
        $where['u_id'] = $userId;
        $arrRet = array();
        $tId = D('Teacher')->where($where)->find();
        if (!empty($tId)) {
            $where1['t_id'] = array('EQ',$tId['t_id']);
            $arrClass = D('TeacherCourse')->select();
            $crsId = array();
            foreach ($arrClass as $key => $value) {
                $crsId[] = $value['crs_id'];
            }
            $arrRet['crs_id']  = implode(',',$crsId);
        }else{
            $arrRet = false;
        }

        return $arrRet;
    }

    
    /**
     * 用户权限检测
     */
    // protected function checkAuth()
    // {
    //     $Rule = D('Rule');
    //     $auth = new \Think\Auth();
    //     $rule = $this->getModule();
    //     if ($this->loginUser['admin'] == 1 ) {
    //         $menuList = $Rule->getMenuList($auth,$this->getUserId(),'Admin',true);
    //     }else{
    //         $ruleInfo = $Rule->getInfoEx('*',array('name'=>$rule));
    //         if($ruleInfo) {
    //             $ret = $auth->check($rule, $this->getUserId(), 'Admin');
    //             if (!$ret) {
    //                 trace($rule);
    //                 //dump($rule);exit;
    //                 $this->error('没有该模块功能权限');
    //             }
    //         }
    //         $menuList = $Rule->getMenuList($auth,$this->getUserId(),'Admin',false);
    //     }

    //     //dump($menuList);
    //     $this->assign('menu_list',$menuList);
    //     $this->assign('rule',$rule);
    //     $this->ruleName = str_replace('/','_',$rule);
    //     return true;
    // }
    
    /**
     * 检测用户是否登录
     * @return int 用户IP
     */
    protected function isLogin(){
        $user = session('admin_user');
        if (empty($user)) {
            return 0;
        } else {
            $uid = session('admin_user_sign') == data_auth_sign($user) ? $user['user_id'] : 0;
            if($uid == 0){
                return 0;
            }else{
                $ip = F('admin_user_'.$uid);
                return $uid;
                if($ip == get_client_ip())
                    return $uid;
                else 
                    return 0;
            }
                
        }
    }
    
    protected function getUserId(){
        return $this->loginUser['user_id'];
    }
    
    protected function getUserName(){
        return $this->loginUser['username'];
    }

    protected function getName(){
        return $this->loginUser['name'];
    }

     protected function getType(){
        return $this->loginUser['type'];
    }
    
    protected function getModuleName(){
        return CONTROLLER_NAME;
    }

    //树状菜单系统管理员
    protected function getTreeList2(){
        //树状结构
        $School = D('SchoolInformation');
        $Area = D('SchoolArea');
        $Grade = D('Grade');
        $Class = D('Class');
        $Student = D('Student');
        $where = "";
        $regionList = D('AreaRegion')->getField('region_code,region_name');
        $region = $School->field("distinct region_id as region_id")->where($where)->select();
       
        $arr="";$where1="";
        //地区
        foreach ($region as $key => $value) {
            $arr[$key]['children'] = array();
            $arr[$key]['id']=$value['region_id'];
            $arr[$key]['isStudent'] = 0;
            $arr[$key]['name'] = "{$regionList[$value['region_id']]}";
            $arr[$key]['pId'] = 0;
            $arr[$key]['type'] = 0;
            $arr[$key]['typeFlag'] = "region";
            $where1['region_id'] = array('EQ',$value['region_id']);
            $schoolInfo = $School->where($where1)->select();
            //学校
            foreach ($schoolInfo as $key1 => $value1) {
                $arr[$key]['children'][$key1]['children'] = array();
                $arr[$key]['children'][$key1]['id']=$value1['s_id'];
                $arr[$key]['children'][$key1]['isStudent']=0;
                $arr[$key]['children'][$key1]['name']="{$value1['name']}";
                $arr[$key]['children'][$key1]['pId']=$value['region_id'];
                $arr[$key]['children'][$key1]['type']=0;
                $arr[$key]['children'][$key1]['typeFlag']="shool";
                //校区
                $where2['s_id'] = array('EQ',$value1['s_id']);
                $areaList = $Area->where($where2)->select();
                foreach ($areaList as $key2 => $value2) {
                    $arr[$key]['children'][$key1]['children'][$key2]['children'] = array();
                    $arr[$key]['children'][$key1]['children'][$key2]['id']=$value2['a_id'];
                    $arr[$key]['children'][$key1]['children'][$key2]['isStudent']=0;
                    $arr[$key]['children'][$key1]['children'][$key2]['name']="{$value2['name']}";
                    $arr[$key]['children'][$key1]['children'][$key2]['pId']=$value1['s_id'];
                    $arr[$key]['children'][$key1]['children'][$key2]['type']=0;
                    $arr[$key]['children'][$key1]['children'][$key2]['typeFlag']="area";
                    //年级
                    $where3['a_id'] = array('EQ',$value2['a_id']);
                    $gradeList = $Grade->where($where3)->select();
                    foreach ($gradeList as $key3 => $value3) {
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'] = array();
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['id']=$value3['g_id'];
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['isStudent']=0;
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['name']="{$value3['name']}";
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['pId']=$value2['a_id'];
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['type']=0;
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['typeFlag']="grade";
                        //班级
                        $where4['g_id'] = array('EQ',$value3['g_id']);
                        $where4['a_id'] = array('EQ',$value2['a_id']);
                        $classList = $Class->where($where4)->select();
                        foreach ($classList as $key4 => $value4) {
                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'] = array();
                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['id']=$value4['c_id'];
                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['isStudent']=0;
                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['name']="{$value4['name']}";
                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['pId']=$value3['g_id'];
                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['type']=0;
                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['typeFlag']="class";
                            //学生
                            $where5['c_id'] = array('EQ',$value4['c_id']);
                            $where5['g_id'] = array('EQ',$value3['g_id']);
                            $where5['a_id'] = array('EQ',$value2['a_id']);
                            $studentList = $Student->where($where5)->select();
                            foreach ($studentList as $key5 => $value5) {
                                
                                if ($value5['sex'] == 1) {
                                    $value5['sex'] = "man";
                                }else{
                                    $value5['sex']  = "women";
                                }
                                $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['id']=$value5['stu_id'];
                                $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['isStudent']=0;
                                $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['name']="{$value5['stu_name']}";
                                $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['pId']=$value4['c_id'];
                                $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['type']=0;
                                $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['iconSkin']=$value5['sex'];

                                $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['typeFlag']="student";
                            }
                        }
                    }
                }
            }

        }
        $data['code'] = 0;
        $data['data'] = $arr;
        return json_encode($data);
    }
    
    //角色
    protected function getGroupId(){
        $where['uid'] = $this->getUserId();
        $groupId = M('auth_group_access','think_')->where($where)->find();
        return $groupId['group_id'];
    }

    //数据权限
    protected function authRule(){
        //查询登录权限
       $where['u_id'] = $this->getUserId();
        // var_dump($where);
       $scoolInfo = D('SchoolInformation')->where($where)->find();//学校表    
       $studentInfo = D('StudentParent')->where($where)->find(); //学生家长表 
       $teacherInfo = D('Teacher')->where($where)->find(); //教师表
       $sId=0;$aId=0;$gId=0;$cId=0;$stu_id1=0;
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
           $where1['stu_id'] = array('IN',$stuId);
           $stInfo = D('Student')->where($where1)->select();
           $sId = $stInfo[0]['s_id'];$aId = $stInfo[0]['a_id'];$gId = $stInfo[0]['g_id'];$cId = $stInfo[0]['c_id'];$stu_id1=$stuId;
       }
       if (!empty($teacherInfo)) {
           $sId = $teacherInfo['s_id'];$aId = $teacherInfo['a_id'];$tId = $teacherInfo['t_id'];
       }
       if (empty($sId)) {
           $sId = '';
       }
       if (empty($aId)) {
           $aId = '';
       }
       if (empty($gId)) {
           $gId = '';
       }
       if (empty($cId)) {
           $cId = '';
       }
       if (empty($stuId)) {
           $stuId = '';
       }
        if (empty($tId)) {
           $tId = '';
       }
       return array('s_id'=>$sId,'a_id'=>$aId,'g_id'=>$gId,'c_id'=>$cId,'stu_id'=>$stuId,'t_id'=>$tId);

    }
    protected function getModule(){
        $moduleName    =   defined('MODULE_ALIAS')? MODULE_ALIAS : MODULE_NAME;
        $action = explode('/',CONTROLLER_NAME);
        $actionName="";
        $nCount = intval(count($action) - 1);
        if($nCount >= 0 && $action[$nCount] != ACTION_NAME){

            $actionName =   '/' . ACTION_NAME;
        }
        $moduleName = '/' . $moduleName . '/' . CONTROLLER_NAME . $actionName;
        $moduleName = str_replace("_handle", '', $moduleName);
        return $moduleName;
    }

    protected function getLockValue(){
        return $this->loginUser['lock'];
    }

    protected function getDataAccess(){
        return $this->loginUser['bm2'];
    }


    //用户菜单
     protected function userRule($pid){
        //查询用户所在组
        $whereGroup['uid'] = $this->getUserId();
        $userGroup = M('auth_group_access','think_')->where($whereGroup)->find();
        //查询组权限
        // $whereAccess['id'] = $userGroup['group_id'];
        $whereAccess['id'] = $this->getType();
        $userRule = M('auth_group','think_')->where($whereAccess)->find();
        if ($this->getType() ==2 ) {
            $whereRule['ismenu'] = array('NEQ',2);
            $whereRule['pid'] = $pid;
            $ruleList = M('auth_rule','think_')->where($whereRule)->select();
        }else{
            if ($userRule) {
                $whereRule['id'] = array('IN',$userRule['rules']); 
                $whereRule['ismenu'] = array('NEQ',2);
                $whereRule['pid'] = $pid;
                $ruleList = M('auth_rule','think_')->where($whereRule)->select();
            }else{
                return  '没有操作权限';
            }
        }
        
        //查询导航菜单
        

        return $ruleList;
     }
    
     // <ul class="sidebar-menu">
     //      <li class="header">校务管理</li>
     //      <!-- Optionally, you can add icons to the links -->
     //      <li>
     //        <a href="/a.php/Admin/School/SchInformation/index">
     //          <i class="fa fa-circle-o"></i>
     //          <span>学校信息</span>
     //        </a>
     //      </li>
     //      <li>
     //        <a href="/a.php/Admin/School/SchArea/index">
     //          <i class="fa fa-circle-o"></i>
     //          <span>校区管理</span>
     //        </a>
     //      </li>
     //      <li>
     //        <a href="/a.php/Admin/School/Dept/index">
     //          <i class="fa fa-circle-o"></i>
     //          <span>部门管理</span>
     //        </a>
     //      </li>
     //      <li>
     //        <a href="/a.php/Admin/School/Grade/index">
     //          <i class="fa fa-circle-o"></i>
     //          <span>年级管理</span>
     //        </a>
     //      </li>
     //      <li>
     //        <a href="/a.php/Admin/School/Course/index">
     //          <i class="fa fa-circle-o"></i>
     //          <span>课程管理</span>
     //        </a>
     //      </li>
     //      <li>
     //        <a href="/a.php/Admin/School/Class/index">
     //          <i class="fa fa-circle-o"></i>
     //          <span>班级管理</span>
     //        </a>
     //      </li>
     //      <li>
     //        <a href="/a.php/Admin/School/Student/index">
     //          <i class="fa fa-circle-o"></i>
     //          <span>学生管理</span>
     //        </a>
     //      </li>
     //      <li>
     //        <a href="/a.php/Admin/School/Teacher/index">
     //          <i class="fa fa-circle-o"></i>
     //          <span>教师管理</span>
     //        </a>
     //      </li>
     //    </ul>


    protected function ruleList(){
        $where['ismenu'] = array('EQ',2);
        $ulList = D('Rule')->where($where)->select();
        $arrTemp = "";
        foreach ($ulList as $key => $value) {
            $arrTemp = $arrTemp."<ul class=\"sidebar-menu\">";
            if (!empty($this->userRule($value['id']))) {
                $arrTemp = $arrTemp."<li class=\"header\">";
                  $arrTemp = $arrTemp."{$value['title']}";
               $arrTemp = $arrTemp."</li>";
            }
              foreach ($this->userRule($value['id']) as $k => $v) {
                  $arrTemp = $arrTemp."<li>";
                    $arrTemp = $arrTemp."<a href=\"/a.php{$v['name']}\" title=\"{$v['title']}\">";
                      $arrTemp = $arrTemp."<i class=\"fa fa-circle-o\"></i>";
                      $arrTemp = $arrTemp."<span>{$v['title']}</span>";
                    $arrTemp = $arrTemp."</a>";
                  $arrTemp = $arrTemp."</li>";
              }
            $arrTemp = $arrTemp."</ul>";
        }

        return $arrTemp;
    }


    //获取课程
    protected function get_course_name($crs_id){
        $where['crs_id'] = $crs_id;
        $courseInfo = D('Course')->where($where)->getField('crs_id,name');
        return $courseInfo;
    }

    protected function get_grade_name($g_id){
        $where['g_id'] = array('EQ',$g_id);
        $courseInfo = D('Grade')->where($where)->getField('g_id,name');
        return $courseInfo;
    }

    protected function get_class_name($c_id){
        $where['c_id'] = array('EQ',$c_id);
        $courseInfo = D('Class')->where($where)->getField('c_id,name');
        return $courseInfo;
    }

    //查询学校名称
    protected function get_school_name($s_id){
        $where['s_id'] = $s_id;
        $schoolName = D('SchoolInformation')->where($where)->getField('s_id,name');
        return $schoolName;
    }
    //查询校区名称 
    protected function get_area_name($a_id){
        $where['a_id'] = $a_id;
        $areaName = D('SchoolArea')->where($where)->getField('a_id,name');
        return $areaName;
    }
    //查询部门名称 
    protected function get_dept_name($a_id){
        $where['a_id'] = $a_id;
        $deptName = D('Dept')->where($where)->getField('d_id,name');
        return $deptName;
    }

      //树状菜单学校管理员
    protected function getTreeList3(){
        
        $ids = $this->authRule();
        $where="";
        if ($this->getType()==1) {
          $where['s_id'] = $ids['s_id'];
        }elseif ($this->getType()==3) {
          $where['s_id'] = $ids['s_id'];
          $where['a_id'] = $ids['a_id'];
        }elseif ($this->getType()==4) {
          $where['s_id'] = $ids['s_id'];
          $where['a_id'] = $ids['a_id'];
          $where['stu_id'] = array('IN',$ids['stu_id']);
        }else{

        }
         //树状结构
        $School = D('SchoolInformation');
        $Area = D('SchoolArea');
        $Grade = D('Grade');
        $Class = D('Class');
        $Student = D('Student');
        // $where = "";
        // $regionList = D('AreaRegion')->getField('region_code,region_name');
        // $region = $School->field("distinct region_id as region_id")->where($where)->select();
        $schoolInfo = $School->where($where)->select();
       
        $arr="";$where1="";
        //学校
        foreach ($schoolInfo as $key => $value) {
            $arr[$key]['children'] = array();
            $arr[$key]['id']=$value['s_id'];
            $arr[$key]['isStudent'] = 0;
            $arr[$key]['name'] = "{$value['name']}";
            $arr[$key]['pId'] = 0;
            $arr[$key]['type'] = 0;
            $arr[$key]['typeFlag'] = "shool";
            $where2['s_id'] = array('EQ',$value['s_id']);
            $areaList = $Area->where($where2)->select();
            //校区
            foreach ($areaList as $key1 => $value1) {
                $arr[$key]['children'][$key1]['children'] = array();
                $arr[$key]['children'][$key1]['id']=$value1['a_id'];
                $arr[$key]['children'][$key1]['isStudent']=0;
                $arr[$key]['children'][$key1]['name']="{$value1['name']}";
                $arr[$key]['children'][$key1]['pId']=$value['s_id'];
                $arr[$key]['children'][$key1]['type']=0;
                $arr[$key]['children'][$key1]['typeFlag']="area";
                //年级
                $where3['a_id'] = array('EQ',$value1['a_id']);
                $gradeList = $Grade->where($where3)->select();
                foreach ($gradeList as $key2 => $value2) {
                    $arr[$key]['children'][$key1]['children'][$key2]['children'] = array();
                    $arr[$key]['children'][$key1]['children'][$key2]['id']=$value2['g_id'];
                    $arr[$key]['children'][$key1]['children'][$key2]['isStudent']=0;
                    $arr[$key]['children'][$key1]['children'][$key2]['name']="{$value2['name']}";
                    $arr[$key]['children'][$key1]['children'][$key2]['pId']=$value1['a_id'];
                    $arr[$key]['children'][$key1]['children'][$key2]['type']=0;
                    $arr[$key]['children'][$key1]['children'][$key2]['typeFlag']="grade";
                        // 班级
                        $where4['g_id'] = array('EQ',$value2['g_id']);
                        $where4['a_id'] = array('EQ',$value1['a_id']);
                        $classList = $Class->where($where4)->select();
                    foreach ($classList as $key3 => $value3) {
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'] = array();
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['id']=$value3['c_id'];
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['isStudent']=0;
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['name']="{$value3['name']}";
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['pId']=$value2['g_id'];
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['type']=0;
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['typeFlag']="class";
                            //学生
                            $where5['c_id'] = array('EQ',$value3['c_id']);
                            $where5['g_id'] = array('EQ',$value2['g_id']);
                            $where5['a_id'] = array('EQ',$value1['a_id']);
                            $studentList = $Student->where($where5)->select();
                            foreach ($studentList as $key4 => $value4) {

                                if ($value4['sex'] == 1) {
                                    $value4['sex'] = "man";
                                }else{
                                    $value4['sex']  = "women";
                                }
                                $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['id']=$value4['stu_id'];
                                $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['isStudent']=0;
                                $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['name']="{$value4['stu_name']}";
                                $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['type']=0;
                                $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['iconSkin']=$value4['sex'];
                                $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['typeFlag']="student";
                             }
                        }
                    }
                }
            }

        // }
        $data['code'] = 0;
        $data['data'] = $arr;

        return json_encode($data);
    }

      
    public function getTreeList(){

        // $ids = $this->authRule();
        // $where="";
        if ($this->getType()==1) {
            //学校管理员
          $arr = $this->getTreeList3();
        }elseif ($this->getType()==3) {
            //教师
          $arr = $this->getTreeList1();
        }elseif ($this->getType()==4) {
          $arr = $this->getTreeList4();
          // $arr = '0';
        }else{
          $arr = $this->getTreeList2();
        }
        return  $arr;
    }


      // 家长树桩
    protected function getTreeList4(){

        $ids = $this->authRule();
        $where="";
        if ($this->getType()==1) {
          $where['s_id'] = $ids['s_id'];
        }elseif ($this->getType()==3) {
          $where['s_id'] = $ids['s_id'];
          $where['a_id'] = $ids['a_id'];
        }elseif ($this->getType()==4) {
          $where['s_id'] = $ids['s_id'];
          $where['a_id'] = $ids['a_id'];
          $where['stu_id'] = array('IN',$ids['stu_id']);
        }else{

        }
        $Student = D('Student');
        $studentList = $Student->where($where)->select();
          foreach ($studentList as $key => $value) {
            $arr[$key]['id']=$value['stu_id'];
            $arr[$key]['isStudent']=0;
            $arr[$key]['name']="{$value['stu_name']}";
            $arr[$key]['pId']=0;
            $arr[$key]['type']=0;
            $arr[$key]['typeFlag']="student";
            }
        // var_dump($tId);die;
        $data['code'] = 0;
        $data['data'] = $arr;
        return json_encode($data);
    }
    
    
    //获取教师班级年级(包括代课和班主任班级)
    public function getTeacherClass($tId){
        //获取代课班级
        $teacherList = D('TeacherCourse')->where(array('t_id'=>$tId))->Field('g_id,c_id')->group("c_id")->select();
        //获取班主任班级
        $headTeacher = M("Class")->where(array('t_id'=>$tId))->Field('g_id,c_id')->select();

        $newList = array_merge($teacherList,$headTeacher);

        $gId = array();$cId = array();$arr = array();
        foreach ($newList as $key => $value) {
            $gId[] = $value['g_id'];
            $cId[] = $value['c_id'];
        }

        $arr['g_id'] = implode(',', $gId);
        $arr['c_id'] = implode(',', $cId);

        return $arr;
    }

    //获取家长孩子的年班级
    public function getStudentClass($stuId){
        // var_dump($stuId);
        $where['stu_id'] = array('IN',$stuId);
        $studentList = D('Student')->where($where)->Field('g_id,c_id')->select();
         // return $studentList;
        $gId = array();$cId = array();$arr = array();
        foreach ($studentList as $key => $value) {
            $gId[] = $value['g_id'];
            $cId[] = $value['c_id'];
        }

        $arr['g_id'] = implode(',', $gId);
        $arr['c_id'] = implode(',', $cId);

        return $arr;
    }
 	


    public function sendSms($phone,$msg){

        vendor('ChuanglanSmsHelper.ChuanglanSmsApi');
        $clapi  = new \ChuanglanSmsApi();
        $result = $clapi->sendSMS($phone, $msg,'true');
        $result = $clapi->execResult($result);
        //var_dump($result);
        if($result[1]==0){
            \Think\Log::write('短信发送成功','WARN');
            return true;
        }else{
            \Think\Log::write('短信发送失败,电话号码:'.$phone,'WARN');
            return false;
        }
    }


     


    //电子学生证 设置 左侧树状
     protected function getTreeList_student(){
        //树状结构
        $School = D('SchoolInformation');
        $Area = D('SchoolArea');
        $Grade = D('Grade');
        $Class = D('Class');
        $Student = D('Student');
        $where = "";
        $regionList = D('AreaRegion')->getField('region_code,region_name');
        $region = $School->field("distinct region_id as region_id")->where($where)->select();
       
        $arr="";$where1="";
        //地区
        foreach ($region as $key => $value) {
            $arr[$key]['children'] = array();
            $arr[$key]['id']=$value['region_id'];
            $arr[$key]['isStudent'] = 0;
            $arr[$key]['name'] = "{$regionList[$value['region_id']]}";
            $arr[$key]['pId'] = 0;
            $arr[$key]['type'] = 0;
            $arr[$key]['typeFlag'] = "region";
            $where1['region_id'] = array('EQ',$value['region_id']);
            $schoolInfo = $School->where($where1)->select();
            //学校
            foreach ($schoolInfo as $key1 => $value1) {
                $arr[$key]['children'][$key1]['children'] = array();
                $arr[$key]['children'][$key1]['id']=$value1['s_id'];
                $arr[$key]['children'][$key1]['isStudent']=0;
                $arr[$key]['children'][$key1]['name']="{$value1['name']}";
                $arr[$key]['children'][$key1]['pId']=$value['region_id'];
                $arr[$key]['children'][$key1]['type']=0;
                $arr[$key]['children'][$key1]['typeFlag']="shool";
                //校区
                $where2['s_id'] = array('EQ',$value1['s_id']);
                $areaList = $Area->where($where2)->select();
                foreach ($areaList as $key2 => $value2) {
                    $arr[$key]['children'][$key1]['children'][$key2]['children'] = array();
                    $arr[$key]['children'][$key1]['children'][$key2]['id']=$value2['a_id'];
                    $arr[$key]['children'][$key1]['children'][$key2]['isStudent']=0;
                    $arr[$key]['children'][$key1]['children'][$key2]['name']="{$value2['name']}";
                    $arr[$key]['children'][$key1]['children'][$key2]['pId']=$value1['s_id'];
                    $arr[$key]['children'][$key1]['children'][$key2]['type']=0;
                    $arr[$key]['children'][$key1]['children'][$key2]['typeFlag']="area";
                    //年级
                   
                }
            }

        }
        $data['code'] = 0;
        $data['data'] = $arr;
        return json_encode($data);
    }
}
