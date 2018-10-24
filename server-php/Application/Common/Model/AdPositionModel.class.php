<?php
/**
 * Created by PhpStorm.
 * User: Mengfanmin
 * Date: 2017/3/20 0020
 * Time: 上午 10:32
 */
namespace Common\Model;

use Common\Model\BaseModel;

class AdPositionModel extends BaseModel
{
    protected $_validate = array(
        array('url', 'require', '图片路径不能为空'),
        array('prov_id', 'require', '请选择展示投放区域'),
        array('begin_time', 'require', '请选择开始展示日期'),
        array('end_time', 'require', '请选择结束展示日期'),
        array('position', 'require', '请选择展示位置'),
    );

    public function queryListEX($fields, $where = null, $orderby = null, $page = 0, $pagesize = 0, $groupby = null, $data_auth = null)
    {
        if ($page) {
            $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
        }
        $user_info = session("admin_user");
        if ($user_info['type'] == 8) {//代理商

            //获取用户所在省份
            $tmp_prov = M("RegionUser")->where("u_id = '{$user_info['user_id']}'")->getField("prov_id");
            $provArr = explode(',', $tmp_prov);
            foreach ($provArr as $prov_id) {
                $tmp[] = array('like', '%' . $prov_id . '%');
            }
            $tmp[] = 'or';
            $where['prov_id'] = $tmp;

        }
        if ($page) {
            $ret2['list'] = self::field($fields)->where($where)->order($orderby)->page($page, $pagesize)->group($groupby)->select();
        } else {
            $ret2['list'] = self::field($fields)->where($where)->order($orderby)->group($groupby)->select();
        }


        if ($user_info['type'] == 8) {//dailishang

            foreach ($ret2['list'] as $k => $v) {
                $Ad_prov_arr = explode(',', $v['prov_id']);

                foreach ($provArr as $k1 => $v1) {//用户管辖prov

                    if (in_array($v1, $Ad_prov_arr)) {
                        $ret['list'][] = $v;
                    }
                }
            }

            //去重
            foreach ($ret['list'] as $v2) {
                $v = implode('###', $v2); //降维,将一维数组转换为用逗号连接的字符串
                $temp[] = $v;
            }
            $temp = array_unique($temp); //去掉重复的字符串,也就是重复的一维数组
            foreach ($temp as $k3 => $v3) {
                $tmp = explode('###', $v3);

                $arr[$k3]['id'] = $tmp[0];
                $arr[$k3]['url'] = $tmp[1];
                $arr[$k3]['s_id'] = $tmp[2];
                $arr[$k3]['begin_time'] = $tmp[3];
                $arr[$k3]['end_time'] = $tmp[4];
                $arr[$k3]['create_time'] = $tmp[5];
                $arr[$k3]['prov_id'] = $tmp[6];
                $arr[$k3]['city_id'] = $tmp[7];
                $arr[$k3]['county_id'] = $tmp[8];
                $arr[$k3]['is_effect'] = $tmp[9];
                $arr[$k3]['memo'] = $tmp[10];
                $arr[$k3]['u_id'] = $tmp[11];
                $arr[$k3]['position'] = $tmp[12];
                
            }
            sort($arr);
            $ret['list'] = $arr;
        } else {
            $ret ["list"] = $ret2['list'];
        }


        $ret ["count"] = count($ret ["list"]);

        $IS_EFFECT = C('IS_EFFECT');
        $AD_POSITION = C('AD_POSITION');
        foreach ($ret['list'] as $key => $value) {
            if ($value['prov_id']) {
                $regionArr = M("AreaRegion")->where(" region_id in ({$value['prov_id']}) ")->getField("region_name", true);
            }
            if ($value['s_id']) {
                $schoolArr = M("SchoolInformation")->where(" s_id in ({$value['s_id']}) ")->getField("name", true);
            }

            $ret['list'][$key]['prov_id'] = "<span >" . $regionArr[0] . "</span>" . "...<a style='cursor:pointer' title='" . implode(',', $regionArr) . "'>全部</a>";
            $ret['list'][$key]['s_id'] = "<span >" . $schoolArr[0] . "</span>" . "...<a style='cursor:pointer' title='" . implode(',', $schoolArr) . "'>全部</a>";
            $ret['list'][$key]['is_effect'] = $IS_EFFECT[$value['is_effect']];
            $ret['list'][$key]['position'] = $AD_POSITION[$value['position']];
            $ret['list'][$key]['begin_time'] = explode(' ', $value['begin_time'])[0];
            $ret['list'][$key]['end_time'] = explode(' ', $value['end_time'])[0];

            if ($value['u_id'] == $user_info['user_id']) {
                $ret['list'][$key]['is_self'] = 1;
            } else {
                $ret['list'][$key]['is_self'] = 0;
            }
            if ($user_info['type'] == 8) {
                $ret['list'][$key]['share'] = getShare($value)['share_money'];
            }
        }

        return $ret;
    }
}