<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-学生卡设置
 */
namespace Admin\Controller\StuCard;

use Admin\Controller\BaseController;

class RfidGpsSetController extends BaseController
{

    public function index()
    {

        $this->display();
    }

  
      public function get()
    {
        $sid = I('get.s_id/d', 68);
        $SchRfidSet = D('SchRfidSet');
        $resInfo = $SchRfidSet->where(array('sid'=>$sid))->find();
        $data['s_id'] = $resInfo['sid'];
        // $data['cmd'] = $resInfo['cmd'];
//        $resInfo['content'] = trim(strstr($resInfo['content'],','),',');
        $resInfo['content'] = '';
        $data['config'] = array('rfid'=>$resInfo['content']);
        $this->success($data);
    }


    //新增/编辑 IP端口号设置
    public function edit()
    {
        $sId = I('post.s_id');
        $content = I('post.content');//1,915364758697A8B9,925364758697A8B9#
        //*HQ,867587027732484,RFID,161723,1,135162738495A6B7,125162738495A6B7#
        if (!$sId || !$content) {
            $this->error('参数错误');
        }

        //获取班级下的所有imei
        $stu_info = M('Student')
            ->field('s_id,a_id,g_id,c_id,stu_id,stu_phone,imei_id')
            ->where("s_id = '{$sId}' and imei_id > 0")
            ->select();
        if ($stu_info) {
            //*HQ,867587027713500,S23,175058,59,110,42,149,50200,5#
            $data['d_type'] = 'rfid';
            $data['create_time'] = date('Y-m-d H:i:s');
            $timeStr = date('His');
            //开启事务处理
            M('StuCardCommand')->startTrans();
            foreach ($stu_info as $key => $value) {
                $data['stu_phone'] = $value['stu_phone'];
                $data['imei'] = $value['imei_id'];
                $data['order'] = '*HQ,'.$value['imei_id'].',RFID,'.$timeStr.','.$content;

                $cardSet['s_id'] = $value['s_id'];
                $cardSet['a_id'] = $value['a_id'];
                $cardSet['g_id'] = $value['g_id'];
                $cardSet['c_id'] = $value['c_id'];
                $cardSet['stu_id'] = $value['stu_id'];
                $cardSet['imei'] = $value['imei_id'];
                $cardSet['d_type'] = $data['d_type'];
                $cardSet['d_value'] = trim($content,'#');

                $commandId = M('StuCardCommand')->add($data);
                $setId = M('StuCardSet')->add($cardSet);
                if(!$commandId || !$setId){
                    M('StuCardCommand')->rollback();
                    $this->success('批量设置失败,请重新设置');
                }
            }
            if($commandId && $setId){
                M('StuCardCommand')->commit();
            }
            $this->success('批量设置成功');
        }
    }

}