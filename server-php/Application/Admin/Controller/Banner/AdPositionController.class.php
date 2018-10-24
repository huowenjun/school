<?php
/**
 * Created by PhpStorm.
 * User: Mengfanmin
 * Date: 2017/3/20 0020
 * Time: 上午 10:24
 */
namespace Admin\Controller\Banner;

use Admin\Controller\BaseController;


class AdPositionController extends BaseController
{
    public function index()
    {
        $this->display();
    }

    //查询
    public function query()
    {
        $AdPosition = D("AdPosition");
        $s_id = trim(I('get.s_id_search'));//学校id
        $regionKeywords = trim(I('get.prov_id_search'));//关键字搜索

        $page = I('get.page');
        $pagesize = I('get.pagesize');
        $order = "id";

        //根据城市名/学校名称搜索显示的轮播
        if (!empty($regionKeywords)) {
            //获取与关键字相关所有region_id
            $regionStr = M("AreaRegion")->where("region_name like '%{$regionKeywords}%'")->getField('region_id');
            $where['prov_id'] = array('like', "%{$regionStr}%");
        }
        if (!empty($s_id)) {
            //与关键字相关的所有的s_id
            $schoolStr = M("SchoolInformation")->where("name like '%{$s_id}%'")->getField('s_id');
            $where['s_id'] = array('like', "%{$schoolStr}%");
        }

        $result = $AdPosition->queryListEX('*', $where, $order, $page, $pagesize, '');

        $this->success($result);

    }

    //新增/编辑
    public function edit()
    {
        if(!session('admin_user')){
            $this->error('请先登录',U('/Admin/Login'));
        }
        $AdPosition = D("AdPosition");

        $id = I('post.id');

        $data['url'] = I('post.url');
        $data['s_id'] = I('post.s_id');
        $data['begin_time'] = I('post.begin_time');
        $data['end_time'] = I('post.end_time');
        $data['create_time'] = date("Y-m-d H:i:s");
        $data['is_effect'] = I('post.is_effect');
        $data['memo'] = I('post.memo');//备注
        $data['position'] = I('post.position');//位置

        $data['prov_id'] = I("post.prov_id");
        $data['city_id'] = I("post.city_id");
        $data['county_id'] = I("post.county_id");
        $data['u_id'] = session('admin_user')['user_id'];

        $content = '新建';
        if (!$AdPosition->create($data)) {
            $this->error($AdPosition->getError());
        } else {
            if ($id) {
                $data['id'] = $id;
                $content = '编辑';
                $ret = $AdPosition->save($data);
            } else {

                $ret = $AdPosition->add($data);
            }
        }
        if ($ret === false) {
            $this->error($AdPosition->getError());
        } else {
            $this->success($content . '成功', U('/Admin/Banner/AdPosition/index'));
        }
    }

    //编辑获取
    public function get()
    {
        $id = I('get.id');
        if (!$id) {
            $this->error('参数错误');
        }

        //banner详情
        $res = M('AdPosition')->where(" id = '{$id}' ")->find();

        if (!$res) {
            $this->error('操作错误');
        }
        $this->success($res);
    }

    //获取数据树
    public function get_tree()
    {
        header("content-type:text/html;charset=utf8");
        //树状结构
        $School = D('SchoolInformation');
        $where = "";

        $banner_id = I('get.banner_id');
        if ($banner_id) {
            $bannerInfo = M("Banner")->field("region_id,s_id")->where(" id = '{$banner_id}' ")->find();
            $provArr = explode(',', $bannerInfo['region_id']);
            $schoolArr = explode(',', $bannerInfo['s_id']);
        } else {
            $provArr = array();
            $schoolArr = array();
        }

        $user_id = I('get.user_id');
        if ($user_id) {
            $userRegion = M("RegionUser")->field("prov_id,city_id,county_id")->where("u_id = '{$user_id}'")->find();
            $regionProv = explode(',', $userRegion['prov_id']);
            $regionCity = explode(',', $userRegion['city_id']);
            $regionCounty = explode(',', $userRegion['county_id']);
        } else {
            $regionProv = array();
            $regionCity = array();
            $regionCounty = array();
        }

        //获取学校省列表
        $where['prov_id'] = array('NEQ', 0);
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
            if (in_array($value['prov_id'], $provArr) || in_array($value['prov_id'], $regionProv)) {
                $arr[$key]['checked'] = "true";
            }

            $where1['prov_id'] = array('EQ', $value['prov_id']);
            $cityInfo = $School->field(" distinct city_id ")->where($where1)->select();
            //市级
            foreach ($cityInfo as $key1 => $value1) {
                $arr[$key]['children'][$key1]['id'] = $value1['city_id'];
                $arr[$key]['children'][$key1]['isStudent'] = 0;
                $arr[$key]['children'][$key1]['name'] = get_region_name($value1['city_id']);
                $arr[$key]['children'][$key1]['pId'] = $value['prov_id'];
                $arr[$key]['children'][$key1]['type'] = 0;
                $arr[$key]['children'][$key1]['typeFlag'] = "city";
                if (in_array($value1['city_id'], $regionCity)) {
                    $arr[$key]['children'][$key1]['checked'] = "true";
                }

                $where2['city_id'] = array('EQ', $value1['city_id']);
                $countyInfo = $School->field(" distinct county_id ")->where($where2)->select();
                //县级
                foreach ($countyInfo as $key2 => $value2) {
                    $arr[$key]['children'][$key1]['children'][$key2]['id'] = $value2['county_id'];
                    $arr[$key]['children'][$key1]['children'][$key2]['isStudent'] = 0;
                    $arr[$key]['children'][$key1]['children'][$key2]['name'] = get_region_name($value2['county_id']);
                    $arr[$key]['children'][$key1]['children'][$key2]['pId'] = $value1['city_id'];
                    $arr[$key]['children'][$key1]['children'][$key2]['type'] = 0;
                    $arr[$key]['children'][$key1]['children'][$key2]['typeFlag'] = "county";
                    if (in_array($value2['county_id'], $regionCounty)) {
                        $arr[$key]['children'][$key1]['children'][$key2]['checked'] = "true";
                    }

                    $where3['county_id'] = array('EQ', $value2['county_id']);
                    $schoolInfo = $School->where($where3)->select();
                    //学校
                    foreach ($schoolInfo as $key3 => $value3) {
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['id'] = $value3['s_id'];
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['isStudent'] = 0;
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['name'] = "{$value3['name']}";
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['pId'] = $value2['county_id'];
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['type'] = 0;
                        $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['typeFlag'] = "shool";
                        if (in_array($value3['s_id'], $schoolArr)) {
                            $arr[$key]['children'][$key1]['children'][$key2]['children'][$key3]['checked'] = "true";
                        }
                    }
                }
            }
        }
        $this->success($arr);
    }

    //删除
    public function del()
    {
        $id = I('post.id');
        if (!$id) {
            $this->error("参数错误");
        }
        $info = M("AdPosition")->where("id in ({$id})")->getField('id', true);

        if (!$info) {
            $this->error("参数错误");
        }
        $res = M("AdPosition")->where("id in ({$id})")->delete();
        if ($res) {
            $content = "删除banner";
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
            $this->success('删除成功');
        }

    }

    //统计详情
    public function getInfo()
    {
        $id = I("get.id");

        $banner_info = M("Banner")->field("*")->where("id = '{$id}'")->find();
        if (!$id || !$banner_info) {
            $this->error('参数错误');
        }
        $type = I("get.type");//1展示统计 2点击统计
        $map['adid'] = $id;
        $map['type'] = $type;

        $StaticsUion = M("StaticsUion");


        for ($i = 1; $i <= 5; $i++) {
            if ($i == 1) {
                //日
                $map['ntime'] = array('EGT', date("Y-m-d 00:00:00"));
                $dateStr = 'DATE_FORMAT(ntime,"%Y-%m-%d %H:00") as sdate,';
                $dateType = 'TodayNum';
            } elseif ($i == 2) {
                //周
                $time = date("Y-m-d 00:00:00", strtotime("-7 days"));
                $map['ntime'] = array("EGT", $time);
                $dateStr = 'DATE_FORMAT(ntime,"%Y-%m-%d") as sdate,';
                $dateType = 'NearlyWeek';
            } elseif ($i == 3) {
                //月
                $time = date("Y-m-d 00:00:00", strtotime("-1 month"));
                $map['ntime'] = array("EGT", $time);
                $dateStr = 'DATE_FORMAT(ntime,"%Y-%m-%d") as sdate,';
                $dateType = 'MonthAgo';
            } elseif ($i == 4) {
                //季
                $season = ceil((date('n')) / 3);//当月是第几季度
                $time = date('Y-m-d', mktime(0, 0, 0, $season * 3 - 3 + 1, 1, date('Y')));
                $map['ntime'] = array("EGT", $time);
                $dateStr = 'DATE_FORMAT(ntime,"%Y-%m") as sdate,';
                $dateType = 'ThisSeason';
            } elseif ($i == 5) {
                //年
                $year = date('Y-01-01 00:00:00');
                $map['ntime'] = array("EGT", $year);
                $dateStr = 'DATE_FORMAT(ntime,"%Y-%m") as sdate,';
                $dateType = 'ThisYear';
            }

            //计算到目前为止的总的收益
            if ($banner_info['u_id'] == $this->getUserId()) {
                //自己发布
                $show_percent = C('PRIV_SHOW_PERCENT');
                $click_percent = C('PRIV_CLICK_PERCENT');

                if ($banner_info['type'] == 1) {
                    //展示计费
                    $map['type'] = 1;
                    $sCount = $StaticsUion->where($map)->count('media_id');
                    $now_total_price = $sCount * $banner_info['unit_price'];
                } elseif ($banner_info['type'] == 0) {
                    //获取展示天数
                    $beginTmp = strtotime($banner_info['begin_time']);
                    $endTmp = time();
                    $dayCount = ceil(($endTmp - $beginTmp) / 86400);
                    $now_total_price = $dayCount * $banner_info['unit_price'];
                } else {
                    //点击量计费
                    $map['type'] = 2;
                    $cCount = $StaticsUion->where($map)->count('media_id');
                    $now_total_price = $cCount * $banner_info['unit_price'];
                }
            } else {
                //非自己发布
                //查询用户所管理区域
                $provStr = M("RegionUser")->where("u_id = '{$this->getUserId()}'")->getField("prov_id");
                $userProv = explode(',', $provStr);
                foreach ($userProv as $prov_id) {
                    $tmp2[] = array('like', '%' . $prov_id . '%');
                }
                $tmp2[] = 'or';
                $map['region_id'] = $tmp2;

                $show_percent = C('SHOW_PERCENT');
                $click_percent = C('CLICK_PERCENT');

                //计算广告在管辖区域下的点击量及展示量
                //计算图片所有点点击量及展示量
                $shareinfo = $StaticsUion->field('media_id,region_id')->where($map)->select();

                //去除模糊查询不准确的值
                foreach ($shareinfo as $k => $v) {
                    //轮播图展示的区域id
                    $bannerProv = explode(',', $v['region_id']);
                    foreach ($userProv as $prov_user) {
                        if (in_array($prov_user, $bannerProv)) {
                            $tmp[] = $v;
                        }
                    }
                }
                // $temp 当前区域id下准确的展示/点击
                //去重
                foreach ($tmp as $v2) {
                    $v3 = implode('##', $v2);
                    $temp[] = $v3;
                }
                $temp = array_unique($temp);
                //广告在所管辖的所有区域总的点击或展示量
                $count = count($temp);
                //echo $count;
                if ($banner_info['type'] == 1) {
                    //展示计费
                    $map['type'] = 1;
                    $sCount = $count;
                    $now_total_price = $sCount * $banner_info['unit_price'];

                } elseif ($banner_info['type'] == 0) {
                    //获取展示天数
                    $beginTmp = strtotime($banner_info['begin_time']);
                    $endTmp = time();
                    $dayCount = ceil(($endTmp - $beginTmp) / 86400);
                    if ($dayCount < 0) {
                        $dayCount = 0;
                    }
                    $now_total_price = $dayCount * $banner_info['unit_price'];

                } else {
                    //点击量计费
                    $map['type'] = 2;
                    $cCount = $count;
                    $now_total_price = $cCount * $banner_info['unit_price'];

                }

            }

            if ($type == 1) {
                $percent = $show_percent;
            } else {
                $percent = $click_percent;
            }
            $output[$dateType . 'Share'] = $now_total_price * $percent;
            $output[$dateType] = $StaticsUion->field($dateStr . 'count(media_id) as cnt')->group('sdate')->where($map)->select();
        }
        $this->success($output);
    }
}

?>