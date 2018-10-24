<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-设备管理
 */

namespace Admin\Controller\Device;

use Admin\Controller\BaseController;

class DeviceManageController extends BaseController
{
    /**
     *设备管理
     */
    public function index()
    {

        $this->display();
    }


    //查询数据
    public function query()
    {

        $keyword = I('get.keyword');
        $pagesize = I("get.pagesize", $this->PAGE_SIZE);
        $page = I('get.page');
        $sort = I('get.sort', 'dc_id');
        $order = $sort . ' ' . I('get.order', 'desc');
        $Device = D('DeviceManage');

        if (!empty($keyword)) {
            $where['imei'] = array("like", "%$keyword%");
        }

        $result = $Device->queryListEX('*', $where, $order, $page, $pagesize, '');
        //未绑定 的设备数！
        $where1['stu_id'] = 0;
        $res1 = $Device->where($where1)->count('stu_id');
        //设备总数
        $res2 = $Device->where($where)->count('stu_id');
        $result['weibangding'] = $res1;
        $result['all'] = $res2;
        $this->success($result);
    }


    //设备的绑定
    public function edit()
    {

        $dc_id = I('post.dc_id/d', 0);
        $stu_id = I('post.stu_id/d', 0);
        $data['stu_id'] = $stu_id;
        $data['status'] = 1;
        $where['dc_id'] = $dc_id;

        $Device = D('DeviceManage');
        $studentInfo = D('Student')->getInfo($stu_id);
        $data['phone'] = $studentInfo['stu_phone'];
        $deviceInfo = $Device->getInfo($dc_id);
        if (empty($deviceInfo)) {
            $this->error('设备不存在');
        }
        $res = $Device->where(array('dc_id' => $dc_id, 'status' => 1))->find();
        if (empty($res)) {
            $ret = $Device->where($where)->save($data);
        } else {
            $this->error('该设备已绑定');
        }
        $content = '绑定成功';
        if ($ret === false) {
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
            $this->error(D('SystemLog')->getError());
        } else {
            $data1['imei_id'] = $deviceInfo['imei'];
            $data1['rfid_id'] = $deviceInfo['rfid'];
            $data1['status'] = 1;
            $where1['stu_id'] = I('post.stu_id/d', 0);
            D('Student')->where($where1)->save($data1);
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
            $this->success($content, U('/Admin/Device/DeviceManage/index'));
        }
    }


    //设备的解绑
    public function unwrap()
    {

        // $dc_id   = I('post.dc_id/d',0);
        // $where['stu_id'] = I('post.stu_id/d',0);
        // var_dump($stu_id);die;
        $stu_id = I('post.stu_id');

        $arrUids = explode(',', $stu_id);
        if (empty($arrUids)) {
            $this->error('参数错误');
        }
        $Device = D('DeviceManage');
        $Student = D('Student');
        foreach ($arrUids as $k => $v) {

            $where['stu_id'] = $v;
            $data['status'] = '0';
            $data['stu_id'] = '0';
            $data['phone'] = ' ';
            $data1['imei_id'] = ' ';
            $data1['rfid_id'] = ' ';
            $data1['status'] = '0';
            $res = $Device->where($where)->save($data);
            $okCount = 0;
            if ($res == 1) {
                $result = $Student->where($where)->save($data1);
                $okCount++;  //处理成功记录数
            }
        }
        $content = "解绑" . $okCount . "条信息";
        if ($result === false) {
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
            $this->error($Device->getError());
        } else {
            // $data1['imei_id'] = $deviceInfo['imei'];
            // $data1['rfid_id'] = $deviceInfo['rfid'];
            // $where1['stu_id'] = I('post.stu_id/d',0);
            // D('Student')->where($where1)->save($data1);
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
            $this->success($content, U('/Admin/Device/DeviceManage/index'));
        }
    }


    //删除
    public function del()
    {
        $uIds = I('post.id');

        $arrUids = explode(',', $uIds);
        if (empty($arrUids)) {
            $this->error('参数错误');
        }
        $devicedata = M('Device');
        $Stu = M('Student');
        $okCount = 0;
        foreach ($arrUids as $k => $v) {

            //查看里面是否有相应的dc_id的数据;
            $whereDcid['dc_id'] = $v;
            $dcidInfo = $devicedata->where($whereDcid)->find();
            if (empty($dcidInfo)) {
                $this->error('没有dcid该数据');
            }
            //从student表里面获取stu_id

            $StuWhere['imei_id'] = $dcidInfo['imei'];
            $stu_info = $Stu->field('stu_id')->where($StuWhere)->find();
            if (!empty($stu_info['stu_id']))//判断所有不可能条件
            {
                //检查access表
                $StuGroup = M('StudentGroupAccess');
                $group_info = $StuGroup->where("stu_id = '{$stu_info['stu_id']}'")->find();
                if (!empty($group_info)) {
                    $this->error("请先解绑");
                }
            }

            $ret = $devicedata->where(array('dc_id' => $v))->delete();
            if ($ret == 1) {

                $okCount++;  //处理成功记录数
            }

        }
        //写log
        $content = "删除" . $okCount . "条信息";
        $state = $okCount > 0 ? 1 : 2;
        D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, $state);
        $this->success('删除成功' . $okCount . '条记录');
    }


    /**
     *  设备导入数据
     */
    public function import()
    {


        // $file_url = 
        // $file_url ="E:/work/58313d95eb180.xlsx";
        $file_url = I('post.file_url');
        // var_dump($file_url);die;
        $s_id = I('get.s_id');//学校id
        $a_id = I('get.a_id');//校区id
        $g_id = I('get.g_id');//年级id
        $c_id = I('get.c_id');//班级id
        // var_dump($file_url);die;
        $Device = D('DeviceManage');//设备表
        $StudentParent = D('StudentParent');//家长表
        $Student = D('Student');//学生表
        $StuCardSet = D('StuCardSet');//学生卡设备表
        $AdminUser = D('AdminUser');//用户表
        // var_dump($s_id);die;

        $file_url = $_SERVER['DOCUMENT_ROOT'] . $file_url;
        $arrField = array('设备IMEI' => 'imei', 'NFCID' => 'nfc_id', '学生学号' => 'stu_no', '学生姓名' => 'stu_name', "学生卡手机号" => "stu_phone", "RFID号" => "rfid_id");

        // var_dump($arrField);
        $arrList_tmp = import_excel($arrField, $file_url);
        if ($arrList_tmp == false) {
            $this->error('表格不能为空');
            # code...
        }
        $arrList = array();
        foreach ($arrList_tmp as $key => $value) {
            if ($value['stu_no'] != '' || $value['stu_name'] != '') {
                $arrList[] = $value;
            }
        }

//        var_dump($arrList);die;
        // $arrList = array(0=>array("imei"=>"1111177777","rfid"=>"66666363663","stu_no"=>"123123123123","stu_name"=>"小钢炮002"));
        // echo "<pre>";
        // var_dump($arrList);die;
        // echo "<pre>";
        // var_dump($arrList);die;
        // unlink($file_url);
        if (count($arrList) > 500) {
            $content = '';
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
            // $this->operatorLogInfo($this->getUserName(),get_client_ip(),C('BEHAVIORTYPE5'),1,'设备管理，导入失败,导入数据不能超过500条');
            // \Response::json(\Base_Error::ERROR_DATA, '导入数据不能超过500条');
            $this->error('导入数据不能超过500条');
        }


        //检查数据有效性
        foreach ($arrList as $key => $value) {
            $value['dc_id'] = 0;
            if (!$Device->create($value)) {
                $key += 2;
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                // $this->operatorLogInfo($this->getUserName(),get_client_ip(),C('BEHAVIORTYPE5'),1,'设备管理，导入失败,第'.$key. '行数据错误:' . $this->error($Device->getError());
                $this->error('第' . $key . '行数据错误:' . $Device->getError());
            }
        }

        //检查dev_code是否重复
        // $devList = array();
        // foreach ($arrList as $value) {
        //     $devList[$value['name']] = 1;
        // }
        // if (count($devList) < count($arrList)) {
        //     D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
        //     // $this->operatorLogInfo($this->getUserName(),get_client_ip(),C('BEHAVIORTYPE5'),1,'设备管理，导入失败,导入数据设备编码不能重复');
        //     // \Response::json(\Base_Error::ERROR_DATA, '导入数据设备编码不能重复');
        //     $this->error($Device->getError());
        // }
        // echo 123;die;

        //插入数据

        $Device->startTrans();
        $state = "";
        $state1 = "";
        foreach ($arrList as $value) {
            $data['s_id'] = $s_id;//学校sid
            $data['a_id'] = $a_id;//学校aid
            $data['g_id'] = $g_id;//学校gid
            $data['c_id'] = $c_id;//学校cid
            $data['create_time'] = date('Y-m-d H:i:s');
            $value['s_id'] = $s_id;//学校sid
            $value['a_id'] = $a_id;//学校aid
            $value['g_id'] = $g_id;//学校aid
            $value['create_time'] = date('Y-m-d H:i:s');
            $where['stu_no'] = $value['stu_no'];
            $where['stu_name'] = $value['stu_name'];
            $stuInfo = $Student->where($where)->find();//获取stu_id
            $spID = D('StudentGroupAccess')->where(array('stu_id' => $stuInfo['stu_id']))->find();
            // $sp_id = "";
            // foreach ($spID as $k => $v) {
            //     $sp_id = $sp_id . $v . ",";
            // }
            // var_dump($sp_id);die;
            $where1['sp_id'] = $spID['sp_id'];
            $ParentInfo = $StudentParent->where($where1)->find(); // 获取家长信息
            $UserInfo = $AdminUser->where(array('user_id' => $ParentInfo['u_id']))->find(); //获取家长相关的用户信息

            // if (empty($value['stu_no']) || empty($value['stu_name'])) {
            //     $this->error('学生学号 或者学生姓名不能为空');
            //     # code...
            // }
            if (empty($stuInfo["stu_id"])) {
                $this->error("该" . "{$value['stu_no']}" . '不存在');
            }
            $DeviceInfo = $Device->where(array('imei' => $value['imei'], 'status' => '1'))->find();
            if ($DeviceInfo) {
                $this->error("" . "{$value['imei']}" . "　imei号已绑定");
                # code...
            }
            $DeviceInfo1 = $Device->where(array('imei' => $value['imei']))->find();
            if (!empty($DeviceInfo1)) {
                $data['imei_id'] = $value['imei'];//imei_id
                $data['stu_phone'] = $value['stu_phone'];//stu_phone
                $data['rfid_id'] = $value['rfid_id'];//rfid_id
                $data['nfc_id'] = $value['nfc_id'];//nfc_id
                $data['stu_id'] = $stuInfo["stu_id"];//rfid
                $data['status'] = 1;//状态
                $value['status'] = 1;//状态
                $value['stu_id'] = $stuInfo["stu_id"];
                $value['phone'] = $value['stu_phone'];
                // var_dump($value['imei']);die;
                // echo "<pre>";
                // var_dump($data);die;
                $res = $Student->where(array('stu_id' => $stuInfo["stu_id"]))->save($data);
                // echo 12312;die;

                // if ($res) {
                $res1 = $Device->where(array('imei' => $value['imei']))->save($value);
                // }
//                var_dump($res);die;
                //添加学生的IMEI号  和 RFID号
            } else {
                // echo 12312;die;
                $data['imei_id'] = $value['imei'];
                $data['stu_phone'] = $value['stu_phone'];//stu_phone
                $data['rfid_id'] = $value['rfid_id'];
                $data['nfc_id'] = $value['nfc_id'];//nfc_id
                $data['stu_id'] = $stuInfo["stu_id"];
                $data['status'] = 1;//状态
                $value['status'] = 1;//状态
                $value['stu_id'] = $stuInfo["stu_id"];
                $value['phone'] = $value["stu_phone"];
                $res = $Student->where(array('stu_id' => $stuInfo["stu_id"]))->save($data);
                $res1 = $Device->add($value);
                // echo M()->_sql();


            }

            $phoneStr = "*HQ," . $value['imei'] . ",WLALL," . date('His') . ",1," . $UserInfo['phone'] . ",,2," . $UserInfo['phone'] . ",,3,,,4,,,5,,,6,,,7,,,8,,,9,,,10,#";
            $dataCard = $StuCardSet->create($value);
            $dataCard['imei'] = $value['imei'];
            $dataCard['stu_id'] = $stuInfo["stu_id"];
            $dataCard['d_type'] = "whitelist";
            $dataCard['d_value'] = $UserInfo['name'] . ":" . $UserInfo['phone'] . "," . ",:,:,:,:,:,:,:,:";
            $res2 = $StuCardSet->add($dataCard);
            // var_dump($res2);die;
            if ($res2) {
//                $sendSmsInfo = $this->sendSms($value['stu_phone'], $phoneStr);
                // var_dump($state2);die;
                // if ($sendSmsInfo === true ) {
                //     return true;
                // } else {
                //     return false;
                // }
            }


            // *HQ, MEID,RFID,seq,RFIDSEQ,IN_ID,OUT_ID#
            // RFID：命令字；
            // RFIDSEQ：天线组序号，如110043；
            // IN_ID：进天线ID，如：112233；
            // OUT_ID：出天线ID，如：223344；
            if (!empty($value['rfid_id'])) {

                //dump($data['d_value']);die;*HQ, MEID,RFID,seq,RFIDSEQ,IN_ID,OUT_ID#
                $rfidInfo = explode(',', $value['rfid_id']); //0-RFIDSEQ：天线组序号   1-IN_ID：进天线ID   2-OUT_ID：出天线ID
                // if ($value['rfid_id'] != $res['d_value']) {
                $dataCard['imei'] = $value['imei'];
                $dataCard['stu_id'] = $stuInfo["stu_id"];
                $dataCard['d_type'] = "rfid";
                $dataCard['d_value'] = $value['rfid_id'];
                $res2 = $StuCardSet->add($dataCard);
                if ($res2) {
                    //$sendSmsInfo = $this->sendSms($value['stu_phone'], '*HQ,' . $value['imei'] . ',RFID,' . date('His') . ',' . $rfidInfo[0] . ',' . str_replace(":", "", $rfidInfo[1]) . ',' . str_replace(":", "", $rfidInfo[2]) . '#');
                    // var_dump($state2);die;
                    // if ($sendSmsInfo === true ) {
                    //     return true;
                    // } else {
                    //     return false;
                    // }
                }

                # code...
            }


            // if ($res === false || $res1 === false) {
            //     $Device->rollback();
            // } else {
            //     // echo 123112321;die;
            //     $Device->commit();
            // }

            // if($state ==1 && $state1 ==1){
            //     echo "fuqian";die;
            //    $Device->commit();
            //  }else{
            //    $Device->rollback();
            //  }
        }
        if ($res === false || $res1 === false) {
            $Device->rollback();
        } else {
            // echo 123112321;die;
            $Device->commit();
        }

        if ($res === false) {
            $content = '导入失败';
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
            $this->error($Device->getError());
        } else {
            $content = '导入成功';
            D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
            $this->success($content, U('/Admin/School/Student/index'));
        }
        // \Response::json(\Base_Error::SUCCESS,  \Base_Error::getMessage('设备数据导入成功'));
    }


    /**
     *  设备管理模块
     *  从前端获取数据对device进行添加
     *  2017-5-17
     */
    public function AddDeviceData()
    {
        if (IS_POST === false) {
            $this->error('the request method is not post');
        }
        $dc_id = I('post.dc_id');

        $getImei = I('post.imei');//获取imei
        if (!$getImei) {//IMEI容错
            $this->error('imei不能为空');
        }
        $getRfid = I('post.rfid');//获取rfid
        $getModel = I('post.model');//获取机型
        $getSw = I('post.sw_version');//获取软件版本号
        $getHw = I('post.hw_version');//获取硬件版本号
        $getProTime = I('post.produce_time');//获取生产日期
        $getOut = I('post.out_time');//获取出货日期
        $getExpire = I('post.expire_time');//获取到期日期
        $getStatus = I('post.status', 0, 'int');//获取状态
        $getDeviceType = I('post.devicetype');//获取设备类型

        $devicedata['imei'] = $getImei;
        $devicedata['rfid'] = $getRfid;
        $devicedata['model'] = $getModel;
        $devicedata['sw_version'] = $getSw;
        $devicedata['hw_version'] = $getHw;
        $devicedata['produce_time'] = $getProTime;
        $devicedata['out_time'] = $getOut;
        $devicedata['expire_time'] = $getExpire;
        $devicedata['status'] = $getStatus;
        $devicedata['devicetype'] = $getDeviceType;
        $device = M('Device');
        if ($dc_id > 0) {//修改
            $devicedata['dc_id'] = $dc_id;
            $check_imei = $device->where("imei = '{$getImei}' and dc_id != '{$dc_id}'")->find();
            if ($check_imei) {
                $this->error("设备已存在");
            }
            $stu_id = M('Student')->where("imei_id = '{$getImei}'")->getField('stu_id');
            //检查access表
            if ($stu_id) {
                $StuGroup = M('StudentGroupAccess');
                $group_info = $StuGroup->where("stu_id = '{$stu_id}'")->find();
                if (!empty($group_info)) {
                    $this->error("请先解绑");
                }
            }

            $res = $device->save($devicedata);
        } else {//添加
            $where['imei'] = $getImei;
            $deviceInfo = $device->where($where)->find();
            if ($deviceInfo) {
                $this->error("设备已存在");
            } else {
                $res = $device->add($where);
            }
        }
        if ($res) {
            $this->success("操作成功");
        } else {
            $this->error('操作失败');
        }


    }

    /*
     *
     *   通过dc_id 获取数据给前端显示
     *
     */
    public function webDisplay()
    {
//        if (IS_POST === false) {
//            $this->error('the request method is not post');
//        }
        $getDcid = I('get.dc_id');//获取dc_id
        if (!$getDcid) {//IMEI容错
            $this->error('getDcid不能为空');
        }
        $devicedata = M('Device');
        $where['dc_id'] = $getDcid;
        $deviceinfo = $devicedata->where($where)->find();
        if (empty($deviceinfo)) {
            $this->error("没有该设备信息");
        } else {
            $this->success($deviceinfo);
        }
    }

}




