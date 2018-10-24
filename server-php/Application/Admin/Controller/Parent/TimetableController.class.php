<?php
/**
 * 首页
 * 生成时间：2016-09-26
 * 作者：shenruiping
 * 修改时间：
 * 修改备注：平安校园-课程表管理
 */
namespace Admin\Controller\Parent;

use Admin\Controller\BaseController;

class TimetableController extends BaseController
{
    /**
     *课程表管理
     */
    public function index()
    {
        // $Timetable = D('Timetable')->where('id = 57')->find();
        //  dump(json_encode(unserialize($Timetable['timetable'])));
        $this->display();
    }

    public function get()
    {
        $Timetable = D('Timetable');
        if ($this->getType() == 4) {// 家长
            $stu_id = I('get.stu_id/d');
            $classList = M('Student')->where(array('stu_id'=>$stu_id))->find();
            $where['c_id'] = array('EQ', $classList['c_id']);
        } else {
            $c_id = I('get.c_id/d');
            $where['c_id'] = array('EQ', $c_id);
        }
        $timetable_info = $Timetable->where($where)->find();
        $timeTable = array();
        if ($c_id > 0 && empty($timetable_info)) {
            $this->error('不存在');
        } else {
            $timeTable['id'] = $timetable_info['id'];
            $timeTable['a_id'] = $timetable_info['a_id'];
            $timeTable['s_id'] = $timetable_info['s_id'];
            $timeTable['g_id'] = $timetable_info['g_id'];
            $timeTable['c_id'] = $timetable_info['c_id'];
            $timeTable['timetable'] = unserialize($timetable_info['timetable']);

        }
        $this->success($timeTable);
    }

    //新增/编辑
    public function edit()
    {
        $Timetable = D('Timetable');
        $id = I('post.id/d', 0);
        $data['id'] = $id;
        $data['s_id'] = I('post.s_id');
        $data['a_id'] = I('post.a_id');
        $data['g_id'] = I('post.g_id');
        $data['c_id'] = I('post.c_id');
        $datas = $_POST;
        unset($datas['id'], $datas['s_id'], $datas['a_id'], $datas['g_id'], $datas['c_id']);
        $data['timetable'] = serialize($datas);
        $data['crs_id'] = I('post.crs_id');
        $data['section'] = I('post.section');
        $data['create_time'] = date('Y-m-d H:i:s');
        $data['user_id'] = $this->getUserId();


        // daemo
        // $sql ="insert into Timetable where id=".$id.",a_id=".I('post.a_id')
        // $sql 合成一条sql语句 直接入库
        if (!$Timetable->create($data)) {
            $this->error($Timetable->getError());
        } else {
            if ($id > 0) {
                $ret = $Timetable->save($data);
            } else {
                $ret = $Timetable->add($data);
            }
            $content = ($id > 0 ? '编辑' : '新建') . '成功';
            if ($ret === false) {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 2);
                $this->error($Timetable->getError());
            } else {
                D('SystemLog')->writeLog($this->getModule(), $content, $this->getUserId(), $this->getType(), $this->getGroupId(), 0, 1);
                $this->success($content, U('/Admin/Parent/Timetable/index'));
            }
        }
    }
}