<?php
/*
*我的班级
*by Mengfanmin date 20170323
*/

namespace Api\Controller;

use Api\Controller\BaseController;

class BannerController extends BaseController
{
    /*
     * 重写父类方法
     * */
    protected function init()
    {

    }

    /*
     *数据查询 二合一项目
     *
     * */
    public function query()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $token = I('post.token');
        //校验token访问是否合法
        $flag = $this->checkToken($token);
        if (!$flag) {
            $this->error('接口访问不合法');
        }

        $area_name = I('post.area_name');//城市名称
        $s_id = I('post.s_id');//学校ID

        //获取城市region_code
        if ($s_id) {
            //获取学校的region_code
            $county_id = M('SchoolInformation')
                ->cache(true)
                ->where("s_id in ({$s_id})")
                ->getField('county_id', true);
            $county_code = M('AreaRegion')
                ->cache(true)
                ->where(array('region_id' => array('in', $county_id)))
                ->getField('region_code', true);
        } else {
            if ($area_name) {
                $county_code = M('AreaRegion')
                    ->cache(true)
                    ->where("region_name like '%{$area_name}%'")
                    ->getField('region_code', true);
            }
        }
        //获取region_code下banner
        if ($county_code) {
            foreach ($county_code as $k => $v) {
                $county_code[$k] = '%' . $v . '%';
            }
            $map['county_code'] = array('like', $county_code);
            $map['_logic'] = 'or';
            $whereStr['_complex'] = $map;
            $whereStr['is_effect'] = 1;
            $whereStr['ad_type'] = 1;
            $whereStr['begin_time'] = array('ELT', date('Y-m-d H:i:s'));
            $whereStr['end_time'] = array('EGT', date('Y-m-d H:i:s'));
            //获取市级下的所有banner
            $banner_list = M('Banner')
                ->field('id,img,url,option1 as title,type,turn_type,
                modelname_app,modelname_app as modelname_ios,modelname_app as modelname_android,region_id,s_id as sid')
                ->where($whereStr)
                ->order('id desc')
                ->select();
        }
        if (!$banner_list) {
            $banner_list = C('BANNER_DATA');
        }
        $this->success($banner_list);
    }

}