<?php

namespace Common\Model;

use Common\Model\BaseModel;

class WarningMessageModel extends BaseModel
{

    protected $_validate = array();


    public function queryListEx($fields, $where = null, $orderby = null, $page = 0, $pagesize = 0)
    {//
        if ($page) {
            $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
        }
        if ($page) {
            $ret['list'] = self::alias('a')->field($fields)->where($where)->order($orderby)->page($page, $pagesize)->select();
        } else {
            $ret['list'] = self::alias('a')->field($fields)->where($where)->order($orderby)->select();
        }

        if ($page == 0) {
            $ret ["count"] = count($ret ["list"]);
        } else {
            $ret ["count"] = $this->getCount(); //获得记录总数
        }
        $Student = D('Student')->field('stu_id,imei_id,sex')->where("imei_id = '{$where['imei']}'")->find();

        $BAOJING_TYPE = C('BAOJING_TYPE');
        foreach ($ret['list'] as &$value) {
            $value['war_type'] = isset($BAOJING_TYPE[$value['war_type']]) ? $BAOJING_TYPE[$value['war_type']] : $value['war_type'];
            $value['imei_id'] = $where['imei'];
            $value['sex'] = $Student['sex'];

            //获取经纬度数据
            if ($value['type'] == 3) {
                $point_info = array($value['longitude'], $value['latitude']);
            } elseif ($value['type'] == 1) {
                $point_info = array(
                    'mcc' => $value['mcc'],
                    'mnc' => $value['mnc'],
                    'lac' => $value['lac'],
                    'cid' => $value['cid'],
                );
            }
            $point = get_gps_point($point_info, $value['type']);

            $value['longitude'] = $point[0];
            $value['latitude'] = $point[1];
        }
        //echo self::getLastSql();
        return $ret;
    }


}