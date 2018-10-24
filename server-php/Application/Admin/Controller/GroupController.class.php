<?php
/*
 * 登录
 * 生成时间：2016-01-01
 * 作者：qianzhiqiang
 * 修改时间：
 * 修改备注：
 */
namespace Admin\Controller;
use Admin\Controller\BaseController;
class GroupController extends BaseController{
    
    public function Index(){
        $where['sp_id'] = 127;//$getUserId
        $sthInfo =D('StudentGroupAccess')->where($where)->getField("stu_id",true);
        foreach ($sthInfo as $key => $value) {
            $stu_id = $stu_id.$value.",";
          }
        // var_dump($stu_id);die;
        $where1['stu_id'] = array('IN',rtrim($stu_id,','));
        $list = D('Student')->where($where1)->select();
        $this->list=$list;
        $this->display();
    }

    public function get_list(){
        // echo "1231231";die;
        $_SESSION['type'] = I('get.type');//类型
        $_SESSION['stu_id'] = I('get.group');//学生id
        $this->success('登录成功',U('/Admin/Index'));
    }
}
