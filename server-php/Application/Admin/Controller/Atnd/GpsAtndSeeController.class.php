<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：fuqian
 * 修改时间：
 * 修改备注：平安校园-GPS考勤记录
 */

namespace Admin\Controller\Atnd;

use Admin\Controller\BaseController;

class GpsAtndSeeController extends BaseController
{
    /**
     *GPS考勤记录
     */
    public function index()
    {

        $this->display();
    }

    public function get()
    {

        $where['c_id'] = I('get.c_id');
        $Class = D('Class');
        $resInfo = $Class->where($where)->find();
        $this->success($resInfo);

    }

    public function query()
    {
        //权限
        $ids = $this->authRule();
        $where = "";
        if ($this->getType() == 1) {
            $sId = $ids['s_id'];
        } elseif ($this->getType() == 3) {//教师
            $sId = $ids['s_id'];
            $aId = $ids['a_id'];
            $classList = $this->getTeacherClass($ids['t_id']);
            $gId = $classList['g_id'];//$ids['a_id'];
            $cId = $classList['c_id'];
        } elseif ($this->getType() == 4) {
            $stu_id = $ids['stu_id'];
        } else {
            $arr = $_GET;
            $sthInfo = getSchoolId($arr);
            $sId = $sthInfo;
        }
        $stuName = I('get.keyword');//学生姓名
        if (I('get.s_id')) {
            $sId = I('get.s_id');//学校
        }
        if (I('get.a_id')) {
            $aId = I('get.a_id');//校区
        }
        if (I('get.g_id')) {
            $gId = I('get.g_id');//年级
        }
        if (I('get.c_id')) {
            $cId = I('get.c_id');//班级
        }
        if (I('get.stu_id')) {
            $stu_id = I('get.stu_id');//学生
        }
        if ($stuName != '') {//搜索学生名称
            $where1['stu_name'] = array('like', '%' . $stuName . '%');//"stu_name like '%".$stuId."%'"
            $stu_id = M('Student')->where($where1)->getField('stu_id', true);
            if (!$stu_id) {
                $this->error('输入的姓名不存在！');
            }
        }
        if ($stu_id) {//判断stu_id
            $map['stu_id'] = array('in', $stu_id);
        } else {
            if ($cId) {//判断c_id
                $map['c_id'] = array('in', $cId);
            } else {
                if ($gId) {//判断g_id
                    $map['g_id'] = array('in', $gId);
                } else {
                    if ($aId) {//判断a_id
                        $map['a_id'] = array('in', $aId);
                    } else {
                        if ($sId) {//判断s_id
                            $map['s_id'] = array('in', $sId);
                        }
                    }
                }
            }
        }
        $imei = M('Student')->where($map)->getField('imei_id', true);

        if ($imei) {
            $where['a.imei'] = array('in', $imei);
        } else {
            $result = array(
                'list' => [],
                'count' => 0,
                'yichang' => 0,
                'zhengc' => 0,
            );
            $this->success($result);
        }

        $sign_type = I('get.sign_type');//签到类型
        $sdatetime = I('get.sdatetime');//考勤时间(开始)
        $edatetime = I('get.edatetime');//考勤时间(结束)
        if (!$sdatetime) {
            $sdatetime = date('Y-m-d 00:00:00');
        }
        if (!$edatetime) {
            $edatetime = date('Y-m-d 23:59:59');
        }

        $page = I('get.page');
        $pagesize = I('get.pagesize', $this->PAGE_SIZE);
        $sort = I('get.sort', 'gt_id');
        $order = $sort . ' ' . I('get.order', 'desc');
        $GpsAttendance = D('GpsAttendance');
        if ($sdatetime || $edatetime) {
            if (!$sdatetime || !$edatetime) {
                $this->error('时间区间不全！');
            }
            if ($sdatetime && $edatetime) {
                $where['a.create_time'] = array("BETWEEN", array($sdatetime . ':00', $edatetime . ':59'));

            }
        }
        if ($sign_type == 1 || $sign_type == 0) {
            $where['sign_type'] = $sign_type;
        }
        $result = $GpsAttendance->queryListEx('*', $where, $order, $page, $pagesize, '');
        $this->success($result);
    }

    //查看单条记录详情
    public function edit()
    {
        $where['gt_id'] = I('get.gt_id/d', 219);
        $GpsAttendance = D('GpsAttendance');
        $pagesize = I("post.pagesize", $this->PAGE_SIZE);
        $page = I('get.page');
        $sort = I('post.sort', 'gt_id');
        $order = $sort . ' ' . I('post.order', 'desc');
        // if($wm_id > 0 && empty($area_info)){
        // $this->error('不存在');
        // }
        $result = $GpsAttendance->queryListEx('*', $where, $order, $page, $pagesize, '');
        $this->success($result['list']);

    }



    //   $res1 = D('Student')->where(array("stu_id"=>$resInfo['stu_id']))->find();
    //   // 上传单个轨迹点
    //   $url = "http://yingyan.baidu.com/api/v3/track/addpoint";
    //   $post_data = array (
    //       "ak" => $ak,
    //       "service_id" => $service_id,
    //       "entity_name"=> $resInfo['imei'],// 学生imei号
    //       "entity_desc"=> $res1['stu_name'],// 学生姓名
    //       "latitude"=> $resInfo['latitude'],// 纬度
    //       "longitude"=> $resInfo['longitude'],// 经度 
    //       "loc_time"=> strtotime($resInfo['create_time']),// 定位时设备的时间
    //       "coord_type_input"=> "wgs84",// 百度坐标 类型
    //       // "direction"=> $resInfo['direction'],// 方向
    //       "sex"=>$res1['sex'],// 自定义字段 性别
    //   );
    //   // header("Content-type:text/html;charset=utf-8");
    //   $name = Dispose($url,$post_data);
    //   // var_dump($name);
    //    $query = $resInfo['imei']; //entity_name + entity_desc 的联合模糊检索  imei 号
    //     $filter = ''; //过滤条件 
    //     $url = "http://yingyan.baidu.com/api/v3/entity/search?ak=".$ak."&service_id=".$service_id."&query=".$query;
    //     //2初始化
    //     $ch = curl_init();
    //     //3.设置参数
    //     curl_setopt($ch , CURLOPT_URL, $url);
    //     curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
    //     //4.调用接口 
    //     $res = curl_exec($ch);
    //     //5.关闭curl
    //     curl_close( $ch );
    //     // if( curl_errno($ch) ){
    //     //     var_dump( curl_error($ch) );
    //     // }
    //     $arr = json_decode($res, true);
    //     $data = array();
    //     $DEVICE_TYPE = C('DEVICE_TYPE'); 
    //     $BAOJING_TYPE = C('BAOJING_TYPE');
    //     $SEX_TYPE = C("SEX_TYPE");
    //     $arr['entities']['0']['latest_location']['stu_name'] = $res1['stu_name'];
    //     $arr['entities']['0']['latest_location']['imei'] = $res1['imei_id'];
    //     $arr['entities']['0']['latest_location']['create_time'] =$resInfo['create_time'];
    //     $data[] = $arr['entities']['0']['latest_location'];
    //     $this->success($data);

    // }

}