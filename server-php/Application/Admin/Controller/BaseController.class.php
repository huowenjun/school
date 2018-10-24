<?php
/**
 * Base 基类
 * 生成时间：2015-01-01
 * 作者：qianzhiqiang
 * 修改时间：
 * 修改备注：
 */

namespace Admin\Controller;

use Think\Controller;

class BaseController extends Controller
{

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
    protected function init()
    {
        //检测用户登录
        define('ADMIN_ID', $this->isLogin());
        $login = substr(CONTROLLER_NAME, 0, 5);

        if (!ADMIN_ID && ('Login' <> $login)) {
            if (IS_AJAX) {
                $this->error('请先登录');
            } else {
                C('CONTROLLER_LEVEL', 2);
                if ('Index' == CONTROLLER_NAME) {
                    redirect(U('/Admin/Login'));
                } else {
                    $this->error('用户未登录请先登录', U('/Admin/Login'), 0);
                }
            }
        }
        if ('Login' <> $login) {
            //设置登录用户信息
            $this->loginUser = session('admin_user');
        }

        if ($this->getUserId()) {
            $this->assign('username', $this->getUserName());  //用户名
            $this->assign('name', $this->getName());  //用户姓名
            $this->assign('userid', $this->getUserId()); //用户ID
            $this->assign('isadmin', $this->loginUser['admin']);
            $this->assign('group_id', $this->getGroupId()); //用户角色id
            $this->assign('user_type', $this->getType()); //用户类型 1 系统用户 0 学校用户
            $this->ruleList = $this->ruleList();
            $arrModule = explode('/', CONTROLLER_NAME);
            //dump(session('admin_user'));
            $this->assign('modulename', $arrModule[0]);
            //trace($arrModule[0]);
            //使用redis存储统计信息
            if ($_SERVER['SERVER_ADDR'] == C('SERVERADDR')) {
                $this->requestCount();
            }
        }

    }

    //redis请求统计 计数器
    public function requestCount()
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
    }

    //教师用户树桩
    protected function getTreeList1($param)
    {
        $where['u_id'] = $this->getUserId();
        $arrRet = array();
        $Grade = D('Grade');
        $Class = D('Class');
        $Student = D('Student');
        $tId = D('Teacher')->where($where)->find();
        // echo 123123;die;

        // var_dump($tId);die;
        if (!empty($tId)) {
            $where1['t_id'] = array('EQ', $tId['t_id']);
            $arrClass = D('TeacherCourse')->field("distinct g_id as g_id,c_id")->where($where1)->select();

            $Classlist = D('Class')->field("distinct g_id as g_id,c_id")->where($where1)->select();

            $gId = array();
            $cId = array();
            foreach ($Classlist as $key => $value) {
                $gId[] = $value['g_id'];
                $g_id1 = implode(',', $gId);
                $cId[] = $value['c_id'];
                $c_id1 = implode(',', $cId);

            }

            $arr = "";
            $where1 = "";
            $gId = array();
            $cId = array();
            foreach ($arrClass as $key => $value) {
                $gId[] = $value['g_id'];
                $g_id = implode(',', $gId);


                $cId[] = $value['c_id'];
                $c_id = implode(',', $cId);
                // var_dump($c_id);die;
            }

            $where1['g_id'] = array('IN', $g_id . "," . $g_id1);
            $where2['c_id'] = array('IN', $c_id . "," . $c_id1);
            // var_dump($where2);die;
            //年级
            // $where1['g_id'] = array('EQ',$value['g_id']);
            $gradeList = $Grade->where($where1)->select();
            foreach ($gradeList as $key => $value) {
                $arr[$key]['children'] = array();
                $arr[$key]['id'] = $value['g_id'];
                $arr[$key]['isStudent'] = 0;
                $arr[$key]['name'] = "{$value['name']}";
                $arr[$key]['pId'] = 0;
                $arr[$key]['type'] = 0;
                $arr[$key]['typeFlag'] = "grade";
                //班级
                $where2['g_id'] = array('EQ', $value['g_id']);
                // var_dump($where2);die;
                // $where2['a_id'] = array('EQ',$value2['a_id']);
                $classList = $Class->where($where2)->select();
                // echo M()->_sql();
                // var_dump($classList);die;
                foreach ($classList as $key1 => $value1) {
                    $arr[$key]['children'][$key1]['children'] = array();
                    $arr[$key]['children'][$key1]['id'] = $value1['c_id'];
                    $arr[$key]['children'][$key1]['isStudent'] = 0;
                    $arr[$key]['children'][$key1]['name'] = "{$value1['name']}";
                    $arr[$key]['children'][$key1]['pId'] = $value['g_id'];
                    $arr[$key]['children'][$key1]['type'] = 0;
                    $arr[$key]['children'][$key1]['typeFlag'] = "class";
                    //学生
                    if ($param != 'grade') {
                        $where3['c_id'] = array('EQ', $value1['c_id']);
                        $where3['g_id'] = array('EQ', $value['g_id']);
                        $studentList = $Student->where($where3)->select();
                        foreach ($studentList as $key2 => $value2) {
                            if ($value2['sex'] == 1) {
                                $value2['sex'] = "man";
                            } else {
                                $value2['sex'] = "women";
                            }
                            $arr[$key]['children'][$key1]['children'][$key2]['id'] = $value2['stu_id'];
                            $arr[$key]['children'][$key1]['children'][$key2]['isStudent'] = 0;
                            $arr[$key]['children'][$key1]['children'][$key2]['name'] = "{$value2['stu_name']}-{$value2['imei_id']}";
                            $arr[$key]['children'][$key1]['children'][$key2]['pId'] = $value1['c_id'];
                            $arr[$key]['children'][$key1]['children'][$key2]['type'] = 0;
                            $arr[$key]['children'][$key1]['children'][$key2]['iconSkin'] = $value2['sex'];

                            $arr[$key]['children'][$key1]['children'][$key2]['typeFlag'] = "student";
                        }
                    }
                }
            }
            // $cId[] = $value['c_id'];

            // $arrRet['c_id']  = implode(',', $cId);
            // $arrRet['g_id']  = implode(',', $gId);
            // }
        } else {
            $arrRet = false;
        }
        return $arr;
    }

    //系统管理员树桩
    protected function getTreeList2($param, $type)
    {
        header("content-type:text/html;charset=utf8");
        //树状结构
        $School = D('SchoolInformation');
        $Area = D('SchoolArea');
        $Grade = D('Grade');
        $Class = D('Class');
        $Student = D('Student');
        $where = "";
        /////////////////////////////////////////////////////////////
        //轮播图默认赋值
        $banner_id = I('get.banner_id');
        if ($banner_id) {
            $bannerInfo = M("Banner")->field("prov_id,city_id,county_id,s_id")->where(" id = '{$banner_id}' ")->find();

            $provBanner = explode(',', $bannerInfo['prov_id']);
            $cityBanner = explode(',', $bannerInfo['city_id']);
            $countyBanner = explode(',', $bannerInfo['county_id']);
            $schoolBanner = explode(',', $bannerInfo['s_id']);
        } else {
            $provBanner = array();
            $cityBanner = array();
            $countyBanner = array();
            $schoolBanner = array();
        }
        //省市县权限默认赋值
        $user_id = I('get.user_id');
        if ($user_id) {
            $userRegion = M("RegionUser")->field("prov_id,city_id,county_id")->where("u_id = '{$user_id}'")->find();

            $provRegion = explode(',', $userRegion['prov_id']);
            $cityRegion = explode(',', $userRegion['city_id']);
            $countyRegion = explode(',', $userRegion['county_id']);
        } else {
            $provRegion = array();
            $cityRegion = array();
            $countyRegion = array();
        }
        //学习服务赋值
        $learning_id = I('get.learning_id');
        if ($learning_id) {
            $learningInfo = M("Learning")->field("prov_id,city_id,county_id,s_id")->where(" id = '{$learning_id}' ")->find();

            $provLearn = explode(',', $learningInfo['prov_id']);
            $cityLearn = explode(',', $learningInfo['city_id']);
            $countyLearn = explode(',', $learningInfo['county_id']);
            $schoolLearn = explode(',', $learningInfo['s_id']);
        } else {
            $provLearn = array();
            $cityLearn = array();
            $countyLearn = array();
            $schoolLearn = array();
        }
        //广告位默认值赋值
        $ad_id = I('get.id');
        if ($ad_id) {
            $AdInfo = M("AdPosition")->field("prov_id,city_id,county_id,s_id")->where(" id = '{$ad_id}' ")->find();

            $provAd = explode(',', $AdInfo['prov_id']);
            $cityAd = explode(',', $AdInfo['city_id']);
            $countyAd = explode(',', $AdInfo['county_id']);
            $schoolAd = explode(',', $AdInfo['s_id']);
        } else {
            $provAd = array();
            $cityAd = array();
            $countyAd = array();
            $schoolAd = array();
        }

        $provArr = array_merge($provBanner, $provRegion, $provLearn, $provAd);//省数组
        $cityArr = array_merge($cityRegion, $cityBanner, $cityLearn, $cityAd);            //市数组
        $countyArr = array_merge($countyRegion, $countyBanner, $countyLearn, $countyAd);        //县数组
        $schoolArr = array_merge($schoolLearn, $schoolBanner, $schoolAd);

        ///////////////////////////////////////////////////////////////
        //获取学校省列表

        if ($type == 'all') {
            $where['parent_id'] = array('EQ', 0);
            $region = M("AreaRegion")->field("distinct region_id as prov_id")->where($where)->select();
        } else {
            $where['prov_id'] = array('NEQ', 0);
            $region = $School->field("distinct prov_id")->where($where)->select();
        }

        $arr = "";
        $where1 = "";
        //地区 省级
        foreach ($region as $key => $value) {
            $arr[$key]['id'] = $value['prov_id'];
            $arr[$key]['isStudent'] = 0;
            $arr[$key]['name'] = get_region_name($value['prov_id']);
            $arr[$key]['pId'] = 0;
            $arr[$key]['type'] = 0;
            $arr[$key]['typeFlag'] = "prov";
            if (in_array($value['prov_id'], $provArr)) {
                $arr[$key]['checked'] = "true";
            }

            if ($param != 'prov') {//显示到省级

                if ($type == 'all') {
                    $where1['parent_id'] = array('EQ', $value['prov_id']);
                    $cityInfo = M("AreaRegion")->field(" distinct region_id as city_id ")->where($where1)->select();
                } else {
                    $where1['prov_id'] = array('EQ', $value['prov_id']);
                    $cityInfo = $School->field(" distinct city_id ")->where($where1)->select();
                }

                //市级
                foreach ($cityInfo as $key1 => $value1) {
                    $arr[$key]['children'][$key1]['id'] = $value1['city_id'];
                    $arr[$key]['children'][$key1]['isStudent'] = 0;
                    $arr[$key]['children'][$key1]['name'] = get_region_name($value1['city_id']);
                    $arr[$key]['children'][$key1]['pId'] = $value['prov_id'];
                    $arr[$key]['children'][$key1]['type'] = 0;
                    $arr[$key]['children'][$key1]['typeFlag'] = "city";
                    if (in_array($value1['city_id'], $cityArr)) {
                        $arr[$key]['children'][$key1]['checked'] = "true";
                    }

                    if ($param != 'city') {//显示到市级
                        if ($type == 'all') {
                            $where2['parent_id'] = array('EQ', $value1['city_id']);
                            $countyInfo = M("AreaRegion")->field(" distinct region_id as county_id ")->where($where2)->select();
                        } else {
                            $where2['city_id'] = array('EQ', $value1['city_id']);
                            $countyInfo = $School->field(" distinct county_id ")->where($where2)->select();
                        }

                        //县级
                        foreach ($countyInfo as $key2 => $value2) {
                            $arr[$key]['children'][$key1]['children'][$key2]['id'] = $value2['county_id'];
                            $arr[$key]['children'][$key1]['children'][$key2]['isStudent'] = 0;
                            $arr[$key]['children'][$key1]['children'][$key2]['name'] = get_region_name($value2['county_id']);
                            $arr[$key]['children'][$key1]['children'][$key2]['pId'] = $value1['city_id'];
                            $arr[$key]['children'][$key1]['children'][$key2]['type'] = 0;
                            $arr[$key]['children'][$key1]['children'][$key2]['typeFlag'] = "county";
                            if (in_array($value2['county_id'], $countyArr)) {
                                $arr[$key]['children'][$key1]['children'][$key2]['checked'] = "true";
                            }

                            if ($param != 'county') {//显示到县区级
                                $where3['county_id'] = array('EQ', $value2['county_id']);
                                $schoolInfo = $School->where($where3)->select();
                                //学校
                                foreach ($schoolInfo as $key3 => $value3) {
                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['id'] = $value3['s_id'];
                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['isStudent'] = 0;
                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['name'] = "{$value3['name']}";
                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['pId'] = $value2['county_id'];
                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['type'] = 0;
                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['typeFlag'] = "school";
                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'] = array();
                                    if (in_array($value3['s_id'], $schoolArr)) {
                                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['checked'] = 'true';
                                    }

                                    if ($param != 'school') {//显示到学校级
                                        //校区
                                        $where4['s_id'] = array('EQ', $value3['s_id']);
                                        $areaList = $Area->where($where4)->select();
                                        foreach ($areaList as $key4 => $value4) {
                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['id'] = $value4['a_id'];
                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['isStudent'] = 0;
                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['name'] = "{$value4['name']}";
                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['pId'] = $value3['s_id'];
                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['type'] = 0;
                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['typeFlag'] = "area";
                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'] = array();

                                            if ($param != 'area') {//显示到校区
                                                //年级
                                                $where5['a_id'] = array('EQ', $value4['a_id']);
                                                $gradeList = $Grade->where($where5)->select();
                                                foreach ($gradeList as $key5 => $value5) {
                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['id'] = $value5['g_id'];
                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['isStudent'] = 0;
                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['name'] = "{$value5['name']}";
                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['pId'] = $value4['a_id'];
                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['type'] = 0;
                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['typeFlag'] = "grade";
                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'] = array();

                                                    if ($param != 'grade') {//显示到年级
                                                        $where6['g_id'] = array('EQ', $value5['g_id']);
                                                        $where6['a_id'] = array('EQ', $value4['a_id']);
                                                        $classList = $Class->where($where6)->select();
                                                        foreach ($classList as $key6 => $value6) {
                                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['id'] = $value6['c_id'];
                                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['isStudent'] = 0;
                                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['name'] = "{$value6['name']}";
                                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['pId'] = $value5['g_id'];
                                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6][$key4]['type'] = 0;
                                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['typeFlag'] = "class";
                                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'] = array();

                                                            if ($param != 'class') {//显示到班级
                                                                //学生
                                                                $where7['c_id'] = array('EQ', $value6['c_id']);
                                                                $where7['g_id'] = array('EQ', $value5['g_id']);
                                                                $where7['a_id'] = array('EQ', $value4['a_id']);
                                                                $studentList = $Student->where($where7)->select();
                                                                foreach ($studentList as $key7 => $value7) {
                                                                    if ($value7['sex'] == 1) {
                                                                        $value7['sex'] = "man";
                                                                    } else {
                                                                        $value7['sex'] = "women";
                                                                    }
                                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['id'] = $value7['stu_id'];
                                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['isStudent'] = 0;
                                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['name'] = "{$value7['stu_name']}-{$value7['imei_id']}";
                                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['pId'] = $value6['c_id'];
                                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['type'] = 0;
                                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['iconSkin'] = $value7['sex'];
                                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['typeFlag'] = "student";
                                                                }
                                                            }

                                                        }
                                                    }
                                                    //班级

                                                }
                                            }

                                        }
                                    }

                                }
                            }
                        }
                    }

                }
            }

        }
        return $arr;
    }

    //学校管理员树桩
    protected function getTreeList3($param)
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

        $arr = "";
        $where1 = "";
        //学校
        foreach ($schoolInfo as $key => $value) {
            $arr[$key]['children'] = array();
            $arr[$key]['id'] = $value['s_id'];
            $arr[$key]['isStudent'] = 0;
            $arr[$key]['name'] = "{$value['name']}";
            $arr[$key]['pId'] = 0;
            $arr[$key]['type'] = 0;
            $arr[$key]['typeFlag'] = "school";

            if ($param != 'school') {
                $where2['s_id'] = array('EQ', $value['s_id']);
                $areaList = $Area->where($where2)->select();
                //校区
                foreach ($areaList as $key1 => $value1) {
                    $arr[$key]['children'][$key1]['children'] = array();
                    $arr[$key]['children'][$key1]['id'] = $value1['a_id'];
                    $arr[$key]['children'][$key1]['isStudent'] = 0;
                    $arr[$key]['children'][$key1]['name'] = "{$value1['name']}";
                    $arr[$key]['children'][$key1]['pId'] = $value['s_id'];
                    $arr[$key]['children'][$key1]['type'] = 0;
                    $arr[$key]['children'][$key1]['typeFlag'] = "area";

                    if ($param != 'area') {
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

                            if ($param != 'grade') {
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

                                    if ($param != 'class') {
                                        //学生
                                        $where5['c_id'] = array('EQ', $value3['c_id']);
                                        $where5['g_id'] = array('EQ', $value2['g_id']);
                                        $where5['a_id'] = array('EQ', $value1['a_id']);
                                        $studentList = $Student->where($where5)->select();
                                        foreach ($studentList as $key4 => $value4) {

                                            if ($value4['sex'] == 1) {
                                                $value4['sex'] = "man";
                                            } else {
                                                $value4['sex'] = "women";
                                            }
                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['id'] = $value4['stu_id'];
                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['isStudent'] = 0;
                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['name'] = "{$value4['stu_name']}-{$value4['imei_id']}";
                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['type'] = 0;
                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['iconSkin'] = $value4['sex'];
                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['typeFlag'] = "student";
                                        }
                                    }

                                }
                            }

                        }
                    }

                }
            }

        }

        // }
        return $arr;
    }

    //家长用户树桩
    protected function getTreeList4($param)
    {
        $ids = $this->authRule();//权限
        $where = "";
        if ($this->getType() == 1) {
            $where['s_id'] = $ids['s_id'];
        } elseif ($this->getType() == 3) {
            $where['s_id'] = $ids['s_id'];
            $where['a_id'] = $ids['a_id'];
        } elseif ($this->getType() == 4) {
            $where['stu_id'] = array('IN', $ids['stu_id']);
        } else {

        }
        $Student = D('Student');
        $studentList = $Student->where($where)->select();
        foreach ($studentList as $key => $value) {
            if ($value['sex'] == 1) {
                $value['sex'] = "man";
            } else {
                $value['sex'] = "women";
            }
            $arr[$key]['id'] = $value['stu_id'];
            $arr[$key]['isStudent'] = 0;
            $arr[$key]['name'] = "{$value['stu_name']}-{$value['imei_id']}";
            $arr[$key]['pId'] = 0;
            $arr[$key]['type'] = 0;
            $arr[$key]['iconSkin'] = "{$value['sex']}";
            $arr[$key]['typeFlag'] = "student";
        }
        return $arr;
    }

    //省教委管理员树桩
    protected function getTreeList5($param, $type)
    {
        header("content-type:text/html;charset=utf8");
        //树状结构
        $School = D('SchoolInformation');
        $Area = D('SchoolArea');
        $Grade = D('Grade');
        $Class = D('Class');
        $Student = D('Student');
        $u_id = $this->getUserId();
        $prov = M("RegionUser")->where(" u_id = '{$u_id}' ")->getField("prov_id");
        $where = '';
        if (!$prov) {
            return '参数错误';
        }
        $where .= " prov_id in ($prov) ";
        //获取学校省列表

        $region = $School->field("distinct prov_id")->where($where)->select();


        $arr = "";
        $where1 = "";
        //地区 省级
        foreach ($region as $key => $value) {
            $arr[$key]['id'] = $value['prov_id'];
            $arr[$key]['isStudent'] = 0;
            $arr[$key]['name'] = get_region_name($value['prov_id']);
            $arr[$key]['pId'] = 0;
            $arr[$key]['type'] = 0;
            $arr[$key]['typeFlag'] = "prov";

            if ($param != 'prov') {
                if ($type == 'all') {
                    $where1['parent_id'] = array('EQ', $value['prov_id']);
                    $cityInfo = M("AreaRegion")->field(" distinct region_id as city_id ")->where($where1)->select();
                } else {
                    $where1['prov_id'] = array('EQ', $value['prov_id']);
                    $cityInfo = $School->field(" distinct city_id ")->where($where1)->select();
                }

                //市级
                foreach ($cityInfo as $key1 => $value1) {
                    $arr[$key]['children'][$key1]['id'] = $value1['city_id'];
                    $arr[$key]['children'][$key1]['isStudent'] = 0;
                    $arr[$key]['children'][$key1]['name'] = get_region_name($value1['city_id']);
                    $arr[$key]['children'][$key1]['pId'] = $value['prov_id'];
                    $arr[$key]['children'][$key1]['type'] = 0;
                    $arr[$key]['children'][$key1]['typeFlag'] = "city";

                    if ($param != 'city') {
                        if ($type == 'all') {
                            $where2['parent_id'] = array('EQ', $value1['city_id']);
                            $countyInfo = M("AreaRegion")->field(" distinct region_id as county_id ")->where($where2)->select();
                        } else {
                            $where2['city_id'] = array('EQ', $value1['city_id']);
                            $countyInfo = $School->field(" distinct county_id ")->where($where2)->select();
                        }

                        //县级
                        foreach ($countyInfo as $key2 => $value2) {
                            $arr[$key]['children'][$key1]['children'][$key2]['id'] = $value2['county_id'];
                            $arr[$key]['children'][$key1]['children'][$key2]['isStudent'] = 0;
                            $arr[$key]['children'][$key1]['children'][$key2]['name'] = get_region_name($value2['county_id']);
                            $arr[$key]['children'][$key1]['children'][$key2]['pId'] = $value1['city_id'];
                            $arr[$key]['children'][$key1]['children'][$key2]['type'] = 0;
                            $arr[$key]['children'][$key1]['children'][$key2]['typeFlag'] = "county";

                            if ($param != 'county') {
                                $where3['county_id'] = array('EQ', $value2['county_id']);
                                $schoolInfo = $School->where($where3)->select();
                                //学校
                                foreach ($schoolInfo as $key3 => $value3) {
                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['id'] = $value3['s_id'];
                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['isStudent'] = 0;
                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['name'] = "{$value3['name']}";
                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['pId'] = $value2['county_id'];
                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['type'] = 0;
                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['typeFlag'] = "school";
                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'] = array();

                                    if ($param != 'school') {
                                        //校区
                                        $where4['s_id'] = array('EQ', $value3['s_id']);
                                        $areaList = $Area->where($where4)->select();
                                        foreach ($areaList as $key4 => $value4) {
                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['id'] = $value4['a_id'];
                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['isStudent'] = 0;
                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['name'] = "{$value4['name']}";
                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['pId'] = $value3['s_id'];
                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['type'] = 0;
                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['typeFlag'] = "area";
                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'] = array();

                                            if ($param != 'area') {
                                                //年级
                                                $where5['a_id'] = array('EQ', $value4['a_id']);
                                                $gradeList = $Grade->where($where5)->select();
                                                foreach ($gradeList as $key5 => $value5) {
                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['id'] = $value5['g_id'];
                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['isStudent'] = 0;
                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['name'] = "{$value5['name']}";
                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['pId'] = $value4['a_id'];
                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['type'] = 0;
                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['typeFlag'] = "grade";
                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'] = array();

                                                    if ($param != 'grade') {
                                                        //班级
                                                        $where6['g_id'] = array('EQ', $value5['g_id']);
                                                        $where6['a_id'] = array('EQ', $value4['a_id']);
                                                        $classList = $Class->where($where6)->select();
                                                        foreach ($classList as $key6 => $value6) {
                                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['id'] = $value6['c_id'];
                                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['isStudent'] = 0;
                                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['name'] = "{$value6['name']}";
                                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['pId'] = $value5['g_id'];
                                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6][$key4]['type'] = 0;
                                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['typeFlag'] = "class";
                                                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'] = array();

                                                            if ($param != 'class') {
                                                                //学生
                                                                $where7['c_id'] = array('EQ', $value6['c_id']);
                                                                $where7['g_id'] = array('EQ', $value5['g_id']);
                                                                $where7['a_id'] = array('EQ', $value4['a_id']);
                                                                $studentList = $Student->where($where7)->select();
                                                                foreach ($studentList as $key7 => $value7) {

                                                                    if ($value7['sex'] == 1) {
                                                                        $value7['sex'] = "man";
                                                                    } else {
                                                                        $value7['sex'] = "women";
                                                                    }
                                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['id'] = $value7['stu_id'];
                                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['isStudent'] = 0;
                                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['name'] = "{$value7['stu_name']}-{$value7['imei_id']}";
                                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['pId'] = $value6['c_id'];
                                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['type'] = 0;
                                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['iconSkin'] = $value7['sex'];
                                                                    $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['typeFlag'] = "student";
                                                                }
                                                            }

                                                        }
                                                    }

                                                }
                                            }
                                        }
                                    }

                                }
                            }
                        }
                    }

                }
            }

        }
        return $arr;
    }

    //市教委管理员树桩
    protected function getTreeList6($param, $type)
    {
        header("content-type:text/html;charset=utf8");
        //树状结构
        $School = D('SchoolInformation');
        $Area = D('SchoolArea');
        $Grade = D('Grade');
        $Class = D('Class');
        $Student = D('Student');
        $u_id = $this->getUserId();
        $city = M("RegionUser")->where(" u_id = '{$u_id}' ")->getField("city_id");
        $where = '';
        if (!$city) {
            return '参数错误';
        }
        $where .= " city_id in ($city) ";
        //获取学校省列表

        $cityInfo = $School->field(" distinct city_id ")->where($where)->select();
        //市级
        foreach ($cityInfo as $key1 => $value1) {
            $arr[$key1]['id'] = $value1['city_id'];
            $arr[$key1]['isStudent'] = 0;
            $arr[$key1]['name'] = get_region_name($value1['city_id']);
            $arr[$key1]['pId'] = 0;
            $arr[$key1]['type'] = 0;
            $arr[$key1]['typeFlag'] = "city";

            if ($param != 'city') {
                if ($type == 'all') {
                    $where2['parent_id'] = array('EQ', $value1['city_id']);
                    $countyInfo = M("AreaRegion")->field(" distinct region_id as county_id ")->where($where2)->select();
                } else {
                    $where2['city_id'] = array('EQ', $value1['city_id']);
                    $countyInfo = $School->field(" distinct county_id ")->where($where2)->select();
                }

                //县级
                foreach ($countyInfo as $key2 => $value2) {
                    $arr[$key1]['children'][$key2]['id'] = $value2['county_id'];
                    $arr[$key1]['children'][$key2]['isStudent'] = 0;
                    $arr[$key1]['children'][$key2]['name'] = get_region_name($value2['county_id']);
                    $arr[$key1]['children'][$key2]['pId'] = $value1['city_id'];
                    $arr[$key1]['children'][$key2]['type'] = 0;
                    $arr[$key1]['children'][$key2]['typeFlag'] = "county";

                    if ($param != 'county') {
                        $where3['county_id'] = array('EQ', $value2['county_id']);
                        $schoolInfo = $School->where($where3)->select();
                        //学校
                        foreach ($schoolInfo as $key3 => $value3) {
                            $arr[$key1]['children'][$key2]['children'][$key3]['id'] = $value3['s_id'];
                            $arr[$key1]['children'][$key2]['children'][$key3]['isStudent'] = 0;
                            $arr[$key1]['children'][$key2]['children'][$key3]['name'] = "{$value3['name']}";
                            $arr[$key1]['children'][$key2]['children'][$key3]['pId'] = $value2['county_id'];
                            $arr[$key1]['children'][$key2]['children'][$key3]['type'] = 0;
                            $arr[$key1]['children'][$key2]['children'][$key3]['typeFlag'] = "school";
                            $arr[$key1]['children'][$key2]['children'][$key3]['children'] = array();

                            if ($param != 'school') {
                                //校区
                                $where4['s_id'] = array('EQ', $value3['s_id']);
                                $areaList = $Area->where($where4)->select();
                                foreach ($areaList as $key4 => $value4) {
                                    $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['id'] = $value4['a_id'];
                                    $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['isStudent'] = 0;
                                    $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['name'] = "{$value4['name']}";
                                    $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['pId'] = $value3['s_id'];
                                    $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['type'] = 0;
                                    $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['typeFlag'] = "area";
                                    $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'] = array();

                                    if ($param != 'area') {
                                        //年级
                                        $where5['a_id'] = array('EQ', $value4['a_id']);
                                        $gradeList = $Grade->where($where5)->select();
                                        foreach ($gradeList as $key5 => $value5) {
                                            $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['id'] = $value5['g_id'];
                                            $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['isStudent'] = 0;
                                            $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['name'] = "{$value5['name']}";
                                            $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['pId'] = $value4['a_id'];
                                            $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['type'] = 0;
                                            $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['typeFlag'] = "grade";
                                            $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'] = array();

                                            if ($param != 'grade') {
                                                //班级
                                                $where6['g_id'] = array('EQ', $value5['g_id']);
                                                $where6['a_id'] = array('EQ', $value4['a_id']);
                                                $classList = $Class->where($where6)->select();
                                                foreach ($classList as $key6 => $value6) {
                                                    $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['id'] = $value6['c_id'];
                                                    $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['isStudent'] = 0;
                                                    $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['name'] = "{$value6['name']}";
                                                    $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['pId'] = $value5['g_id'];
                                                    $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6][$key4]['type'] = 0;
                                                    $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['typeFlag'] = "class";
                                                    $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'] = array();

                                                    if ($param != 'class') {
                                                        //学生
                                                        $where7['c_id'] = array('EQ', $value6['c_id']);
                                                        $where7['g_id'] = array('EQ', $value5['g_id']);
                                                        $where7['a_id'] = array('EQ', $value4['a_id']);
                                                        $studentList = $Student->where($where7)->select();
                                                        foreach ($studentList as $key7 => $value7) {

                                                            if ($value7['sex'] == 1) {
                                                                $value7['sex'] = "man";
                                                            } else {
                                                                $value7['sex'] = "women";
                                                            }
                                                            $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['id'] = $value7['stu_id'];
                                                            $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['isStudent'] = 0;
                                                            $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['name'] = "{$value7['stu_name']}-{$value7['imei_id']}";
                                                            $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['pId'] = $value6['c_id'];
                                                            $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['type'] = 0;
                                                            $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['iconSkin'] = $value7['sex'];
                                                            $arr[$key1]['children'][$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['typeFlag'] = "student";
                                                        }
                                                    }

                                                }
                                            }

                                        }
                                    }


                                }
                            }

                        }
                    }

                }
            }

        }
        return $arr;
    }

    //县教委管理员树桩
    protected function getTreeList7($param)
    {
        header("content-type:text/html;charset=utf8");
        //树状结构
        $School = D('SchoolInformation');
        $Area = D('SchoolArea');
        $Grade = D('Grade');
        $Class = D('Class');
        $Student = D('Student');
        $u_id = $this->getUserId();
        $county = M("RegionUser")->where(" u_id = '{$u_id}' ")->getField("county_id");
        $where = '';
        if (!$county) {
            return '参数错误';
        }
        $where .= " county_id in ($county) ";
        $countyInfo = $School->field(" distinct county_id ")->where($where)->select();
        //县级
        foreach ($countyInfo as $key2 => $value2) {
            $arr[$key2]['id'] = $value2['county_id'];
            $arr[$key2]['isStudent'] = 0;
            $arr[$key2]['name'] = get_region_name($value2['county_id']);
            $arr[$key2]['pId'] = 0;
            $arr[$key2]['type'] = 0;
            $arr[$key2]['typeFlag'] = "county";

            if ($param != 'county') {
                $where3['county_id'] = array('EQ', $value2['county_id']);
                $schoolInfo = $School->where($where3)->select();
                //学校
                foreach ($schoolInfo as $key3 => $value3) {
                    $arr[$key2]['children'][$key3]['id'] = $value3['s_id'];
                    $arr[$key2]['children'][$key3]['isStudent'] = 0;
                    $arr[$key2]['children'][$key3]['name'] = "{$value3['name']}";
                    $arr[$key2]['children'][$key3]['pId'] = $value2['county_id'];
                    $arr[$key2]['children'][$key3]['type'] = 0;
                    $arr[$key2]['children'][$key3]['typeFlag'] = "school";
                    $arr[$key2]['children'][$key3]['children'] = array();

                    if ($param != 'school') {
                        //校区
                        $where4['s_id'] = array('EQ', $value3['s_id']);
                        $areaList = $Area->where($where4)->select();
                        foreach ($areaList as $key4 => $value4) {
                            $arr[$key2]['children'][$key3]['children'][$key4]['id'] = $value4['a_id'];
                            $arr[$key2]['children'][$key3]['children'][$key4]['isStudent'] = 0;
                            $arr[$key2]['children'][$key3]['children'][$key4]['name'] = "{$value4['name']}";
                            $arr[$key2]['children'][$key3]['children'][$key4]['pId'] = $value3['s_id'];
                            $arr[$key2]['children'][$key3]['children'][$key4]['type'] = 0;
                            $arr[$key2]['children'][$key3]['children'][$key4]['typeFlag'] = "area";
                            $arr[$key2]['children'][$key3]['children'][$key4]['children'] = array();

                            if ($param != 'area') {
                                //年级
                                $where5['a_id'] = array('EQ', $value4['a_id']);
                                $gradeList = $Grade->where($where5)->select();
                                foreach ($gradeList as $key5 => $value5) {
                                    $arr[$key2]['children'][$key3]['children'][$key4]['children'][$key5]['id'] = $value5['g_id'];
                                    $arr[$key2]['children'][$key3]['children'][$key4]['children'][$key5]['isStudent'] = 0;
                                    $arr[$key2]['children'][$key3]['children'][$key4]['children'][$key5]['name'] = "{$value5['name']}";
                                    $arr[$key2]['children'][$key3]['children'][$key4]['children'][$key5]['pId'] = $value4['a_id'];
                                    $arr[$key2]['children'][$key3]['children'][$key4]['children'][$key5]['type'] = 0;
                                    $arr[$key2]['children'][$key3]['children'][$key4]['children'][$key5]['typeFlag'] = "grade";
                                    $arr[$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'] = array();

                                    if ($param != 'grade') {
                                        //班级
                                        $where6['g_id'] = array('EQ', $value5['g_id']);
                                        $where6['a_id'] = array('EQ', $value4['a_id']);
                                        $classList = $Class->where($where6)->select();
                                        foreach ($classList as $key6 => $value6) {
                                            $arr[$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['id'] = $value6['c_id'];
                                            $arr[$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['isStudent'] = 0;
                                            $arr[$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['name'] = "{$value6['name']}";
                                            $arr[$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['pId'] = $value5['g_id'];
                                            $arr[$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6][$key4]['type'] = 0;
                                            $arr[$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['typeFlag'] = "class";
                                            $arr[$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'] = array();

                                            if ($param != 'class') {
                                                //学生
                                                $where7['c_id'] = array('EQ', $value6['c_id']);
                                                $where7['g_id'] = array('EQ', $value5['g_id']);
                                                $where7['a_id'] = array('EQ', $value4['a_id']);
                                                $studentList = $Student->where($where7)->select();
                                                foreach ($studentList as $key7 => $value7) {

                                                    if ($value7['sex'] == 1) {
                                                        $value7['sex'] = "man";
                                                    } else {
                                                        $value7['sex'] = "women";
                                                    }
                                                    $arr[$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['id'] = $value7['stu_id'];
                                                    $arr[$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['isStudent'] = 0;
                                                    $arr[$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['name'] = "{$value7['stu_name']}-{$value7['imei_id']}";
                                                    $arr[$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['pId'] = $value6['c_id'];
                                                    $arr[$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['type'] = 0;
                                                    $arr[$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['iconSkin'] = $value7['sex'];
                                                    $arr[$key2]['children'][$key3]['children'][$key4]['children'][$key5]['children'][$key6]['children'][$key7]['typeFlag'] = "student";
                                                }
                                            }

                                        }
                                    }

                                }
                            }

                        }
                    }

                }
            }

        }
        return $arr;
    }


    //课程
    protected function getCourse($userId)
    {
        $where['u_id'] = $userId;
        $arrRet = array();
        $tId = D('Teacher')->where($where)->find();
        if (!empty($tId)) {
            $where1['t_id'] = array('EQ', $tId['t_id']);
            $arrClass = D('TeacherCourse')->select();
            $crsId = array();
            foreach ($arrClass as $key => $value) {
                $crsId[] = $value['crs_id'];
            }
            $arrRet['crs_id'] = implode(',', $crsId);
        } else {
            $arrRet = false;
        }

        return $arrRet;
    }

    /**
     * 检测用户是否登录
     * @return int 用户IP
     */
    protected function isLogin()
    {
        $user = session('admin_user');
        if (empty($user)) {
            return 0;
        } else {
            $uid = session('admin_user_sign') == data_auth_sign($user) ? $user['user_id'] : 0;
            if ($uid == 0) {
                return 0;
            } else {
                F('admin_user_' . $uid);
                return $uid;

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

    protected function getType()
    {
        return $this->loginUser['type'];
    }

    protected function getModuleName()
    {
        return CONTROLLER_NAME;
    }


    //角色
    protected function getGroupId()
    {
        $where['uid'] = $this->getUserId();
        $groupId = M('auth_group_access', 'think_')->where($where)->find();
        return $groupId['group_id'];
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

    protected function getModule()
    {
        $moduleName = defined('MODULE_ALIAS') ? MODULE_ALIAS : MODULE_NAME;
        $action = explode('/', CONTROLLER_NAME);
        $actionName = "";
        $nCount = intval(count($action) - 1);
        if ($nCount >= 0 && $action[$nCount] != ACTION_NAME) {

            $actionName = '/' . ACTION_NAME;
        }
        $moduleName = '/' . $moduleName . '/' . CONTROLLER_NAME . $actionName;
        $moduleName = str_replace("_handle", '', $moduleName);
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




    // <ul class="sidebar-menu">
    //      <li class="header">校务管理</li>
    //      <!-- Optionally, you can add icons to the links -->
    //      <li>
    //        <a href="/index.php/Admin/School/SchInformation/index">
    //          <i class="fa fa-circle-o"></i>
    //          <span>学校信息</span>
    //        </a>
    //      </li>
    //      <li>
    //        <a href="/index.php/Admin/School/SchArea/index">
    //          <i class="fa fa-circle-o"></i>
    //          <span>校区管理</span>
    //        </a>
    //      </li>
    //      <li>
    //        <a href="/index.php/Admin/School/Dept/index">
    //          <i class="fa fa-circle-o"></i>
    //          <span>部门管理</span>
    //        </a>
    //      </li>
    //      <li>
    //        <a href="/index.php/Admin/School/Grade/index">
    //          <i class="fa fa-circle-o"></i>
    //          <span>年级管理</span>
    //        </a>
    //      </li>
    //      <li>
    //        <a href="/index.php/Admin/School/Course/index">
    //          <i class="fa fa-circle-o"></i>
    //          <span>课程管理</span>
    //        </a>
    //      </li>
    //      <li>
    //        <a href="/index.php/Admin/School/Class/index">
    //          <i class="fa fa-circle-o"></i>
    //          <span>班级管理</span>
    //        </a>
    //      </li>
    //      <li>
    //        <a href="/index.php/Admin/School/Student/index">
    //          <i class="fa fa-circle-o"></i>
    //          <span>学生管理</span>
    //        </a>
    //      </li>
    //      <li>
    //        <a href="/index.php/Admin/School/Teacher/index">
    //          <i class="fa fa-circle-o"></i>
    //          <span>教师管理</span>
    //        </a>
    //      </li>
    //    </ul>


    protected function ruleList()
    {
        $where['ismenu'] = array('EQ', 2);
        $ulList = D('Rule')->where($where)->order('sort asc')->select();
        $arrTemp = "";
        foreach ($ulList as $key => $value) {
            $arrTemp = $arrTemp . "<ul class=\"sidebar-menu\">";
            if (!empty($this->userRule($value['id']))) {
                $arrTemp = $arrTemp . "<li class=\"treeview\">";
                $arrTemp = $arrTemp . "<a href=\"#\">";
                $arrTemp = $arrTemp . "<span>{$value['title']}</span>";
                $arrTemp = $arrTemp . "<span class=\"pull-right-container\">";
                $arrTemp = $arrTemp . "<i class=\"fa fa-angle-left pull-right\"></i>";
                $arrTemp = $arrTemp . "</span>";
                $arrTemp = $arrTemp . "</a>";
                $arrTemp = $arrTemp . "<ul class=\"treeview-menu\">";
            }
            foreach ($this->userRule($value['id']) as $k => $v) {
                $arrTemp = $arrTemp . "<li>";
                $arrTemp = $arrTemp . "<a href=\"/index.php{$v['name']}\" title=\"{$v['title']}\">";
                $arrTemp = $arrTemp . "<i class=\"fa fa-circle-o\"></i>";
                $arrTemp = $arrTemp . "<span>{$v['title']}</span>";
                $arrTemp = $arrTemp . "</a>";
                $arrTemp = $arrTemp . "</li>";
            }
            $arrTemp = $arrTemp . "</ul>";
            $arrTemp = $arrTemp . "</li>";
            $arrTemp = $arrTemp . "</ul>";
        }

        return $arrTemp;
    }

    //用户菜单
    protected function userRule($pid)
    {
        //查询用户所在组
        $whereAccess['id'] = $this->getType();
        $userRule = M('auth_group', 'think_')->where($whereAccess)->find();
        if ($this->getType() == 2 && session('admin_user')['username'] == 'admin') {//超管登录
            $whereRule['ismenu'] = array('NEQ', 2);
            $whereRule['pid'] = $pid;
            $whereRule['status'] = 1;
            $ruleList = M('auth_rule', 'think_')->where($whereRule)->order('sort asc')->select();
        } else {
            if ($userRule) {
                if ($userRule['rules']) {
                    $whereRule['id'] = array('IN', $userRule['rules']);
                    $whereRule['ismenu'] = array('NEQ', 2);
                    $whereRule['pid'] = $pid;
                    $whereRule['status'] = 1;
                    $ruleList = M('auth_rule', 'think_')->where($whereRule)->select();
                }

            } else {
                return '没有操作权限';
            }
        }
        //查询导航菜单
        return $ruleList;
    }


    //获取课程
    protected function get_course_name($crs_id)
    {
        $where['crs_id'] = $crs_id;
        $courseInfo = D('Course')->where($where)->getField('crs_id,name');
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

//$param显示层级 area
    public function getTreeList($param = '', $type = '')
    {
        if ($this->getType() == 1) {
            //学校管理员
            $arr = $this->getTreeList3($param, $type);
        } elseif ($this->getType() == 3) {
            //教师用户
            $arr = $this->getTreeList1($param, $type);
        } elseif ($this->getType() == 4) {
            //家长用户
            $arr = $this->getTreeList4($param, $type);
        } elseif ($this->getType() == 5) {
            //省教委管理
            $arr = $this->getTreeList5($param, $type);
        } elseif ($this->getType() == 6) {
            //市教委管理
            $arr = $this->getTreeList6($param, $type);
        } elseif ($this->getType() == 7) {
            //县教委管理
            $arr = $this->getTreeList7($param, $type);
        } else {
            $arr = $this->getTreeList2($param, $type);
        }
        return $arr;
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

    //获取家长孩子的年班级
    public function getStudentClass($stuId)
    {
        // var_dump($stuId);
        $where['stu_id'] = array('IN', $stuId);
        $studentList = D('Student')->where($where)->Field('g_id,c_id')->select();
        // return $studentList;
        $gId = array();
        $cId = array();
        $arr = array();
        foreach ($studentList as $key => $value) {
            $gId[] = $value['g_id'];
            $cId[] = $value['c_id'];
        }

        $arr['g_id'] = implode(',', $gId);
        $arr['c_id'] = implode(',', $cId);

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

}
