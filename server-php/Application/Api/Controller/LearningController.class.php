<?php
/*
*学习服务
*by Yumeng date 20170406
*/
namespace Api\Controller;

use Api\Controller\BaseController;

class LearningController extends BaseController
{
    /*
    *数据查询
    *@param
    */
    public function query()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }

        $s_id = I('post.s_id',35);
        if (!$s_id) {
            $this->error("参数错误");
        }

        $sIdTmp = explode(',', $s_id);
        $where['status'] = 0;
        foreach ($sIdTmp as $key => $value) {
            $where['s_id'] = array('like', "%{$value}%");
            $result[] = M("Learning")->field("id,title,icon_url,web_url,region_id,s_id")->order("id desc")->where($where)->select();

            //过滤不存在的s_id
            foreach ($result[$key] as $k => $v) {
                $sArr = explode(',', $v['s_id']);
                if (in_array($value, $sArr)) {
                    $data[] = $v;
                }
            }
        }
        //去重
        foreach ($data as $v) {
            $v = implode('###', $v); //降维,将一维数组转换为用逗号连接的字符串
            $temp[] = $v;
        }
        $temp = array_unique($temp); //去掉重复的字符串,也就是重复的一维数组
        sort($temp);
        foreach ($temp as $k => $v) {
            $tmp = explode('###', $v);

            $arr[$k]['id'] = $tmp[0];
            $arr[$k]['title'] = $tmp[1];
            $arr[$k]['icon_url'] = $tmp[2];
            $arr[$k]['web_url'] = $tmp[3];
            $arr[$k]['region_id'] = $tmp[4];
            $arr[$k]['sid']             = $s_id;
        }
        $data = $arr;
        $this->success($data);

    }

}