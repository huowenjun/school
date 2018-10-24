<?php
/**
 * Created by PhpStorm.
 * User: Mengfanmin
 * Date: 2017/3/20 0020
 * Time: 上午 10:32
 */

namespace Common\Model;

use Common\Model\BaseModel;

class BannerModel extends BaseModel
{
    protected $_validate = array(
        array('img', 'require', '图片路径不能为空'),
        array('prov_id', 'require', '请选择展示投放区域'),
        array('begin_time', 'require', '请选择开始展示日期'),
        array('end_time', 'require', '请选择结束展示日期'),
        array('unit_price', 'require', '请选择展示单价'),
    );

    public function queryListEX($fields, $where = null, $orderby = null, $page = 0, $pagesize = 0, $groupby = null, $data_auth = null)
    {
        if ($page) {
            $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
        }
        $user_info = session("admin_user");
        if ($user_info['type'] == 8) {
            //获取用户所在省份
            $tmp_prov = M("RegionUser")->where("u_id = '{$user_info['user_id']}'")->getField("region_code");
            $provArr = explode(',', $tmp_prov);
            foreach ($provArr as $prov_id) {
                $tmp[] = array('like', '%' . $prov_id . '%');
            }

            $tmp[] = 'or';
            $where['county_code'] = $tmp;
            $map['u_id'] = $user_info['user_id'];
            $where['_logic'] = 'or';
            $where['_complex'] = $map;

        }
        if ($page) {
            $ret['list'] = self::field($fields)->where($where)->order($orderby)->page($page, $pagesize)->group($groupby)->select();
        } else {
            $ret['list'] = self::field($fields)->where($where)->order($orderby)->group($groupby)->select();
        }

        $ret ["count"] = $this->getCount(); //获得记录总数

        $IS_EFFECT = C('IS_EFFECT');
        $BANNER_TYPE = C('BANNER_TYPE');
        foreach ($ret['list'] as $key => $value) {
            if (!empty($value['img'])) {
                $is_file = file_exists($_SERVER['DOCUMENT_ROOT'] . $value['img']);
                if ($is_file) {
                    $ret['list'][$key]['img'] = "<a href='" . $value['img'] . "' target='_blank'><img width='100' src='" . $value['img'] . "'></a>";
                } else {
                    $ret['list'][$key]['img'] = "<a href='" . C('ONLINE_NAME') . $value['img'] . "' target='_blank'><img width='100' src='" . C('ONLINE_NAME') . $value['img'] . "'></a>";
                }

            }
            if ($value['region_id']) {
                $regionArr = M("AreaRegion")->where(" region_id in ({$value['region_id']}) ")->getField("region_name", true);
            }
            if ($value['s_id']) {
                $schoolArr = M("SchoolInformation")->where(" s_id in ({$value['s_id']}) ")->getField("name", true);
            }

            $ret['list'][$key]['region_id'] = "<span >" . $regionArr[0] . "</span>" . "...<a style='cursor:pointer' title='" . implode(',', $regionArr) . "'>全部</a>";
            $ret['list'][$key]['s_id'] = "<span >" . $schoolArr[0] . "</span>" . "...<a style='cursor:pointer' title='" . implode(',', $schoolArr) . "'>全部</a>";
            $ret['list'][$key]['is_effect'] = $IS_EFFECT[$value['is_effect']];
            $ret['list'][$key]['type'] = $BANNER_TYPE[$value['type']];
            $ret['list'][$key]['begin_time'] = explode(' ', $value['begin_time'])[0];
            $ret['list'][$key]['end_time'] = explode(' ', $value['end_time'])[0];
            if ($ret['list'][$key]['turn_type'] == 1) {
                $ret['list'][$key]['turn_type'] = '网页跳转';
            } else {
                $ret['list'][$key]['turn_type'] = '程序功能跳转';
            }
            if ($value['u_id'] == $user_info['user_id']) {
                $ret['list'][$key]['is_self'] = 1;
            } else {
                $ret['list'][$key]['is_self'] = 0;
            }
        }

        return $ret;
    }
}