<?php
/*
*   注册
*by yumeng date 20170413
*/

namespace Wap\Controller;

use Think\Controller;

class PlaneController extends Controller
{
//    //获取飞机站点信息
//    public function getStationInfo()
//    {
//        if (IS_POST === false) {
//            $this->error('the request method is not post');
//        }
//        $addr = trim(I('post.addr'));
//        $addr_info = M('PlaneStation')->field('name,code')->where("quanpin like '{$addr}%' or name like '{$addr}%'")->select();
//        $this->success($addr_info);
//    }

    //获取到达目的地-航线列表
    public function getLineList()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $from = trim(I('post.from'));//始发站
        $to = trim(I('post.to'));//目的地
        $date = trim(I('post.date'));//出行日期
        if (!$from || !$to || !$date) {
            $this->error('参数错误');
        }

        import('Vendor.Elife.Plane');
        $plane = new \Plane();
        $line_data = $plane->airLinesList($from, $to, $date);
        $this->success($line_data);
    }


}