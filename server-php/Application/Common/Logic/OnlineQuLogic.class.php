<?php
namespace Common\Logic;
use Common\Model\OnlineQuModel;
class OnlineQuLogic extends OnlineQuModel {

    /**
    *提问数据处理
    * @param $orderInfo
    * @param $b_id
    */
    public function addOnline($data,$type){
        if($this->create($data)){
            if($data['id'] > 0 ){//编辑,换产品
                $ret = $this->save($data);
                if($ret === false)  return false;
                return array('msg'=>'编辑成功！');
            }else if($type == 1){
                    // $where['t_id'] = $data['form_user'];
                    // $TeacherInfo = D('Teacher')->where($where)->find();
                    // if (empty($TeacherInfo)) {
                    // $data['form_user'] = $TeacherInfo['u_id'];
                // }
                $ret = $this->add($data);
                if($ret === false){
                    $this->error = $this->getError();
                    return false;
                }
                return array('msg'=>'在线提问成功');
            }else  if($type == 2){
                $ret = $this->add($data);
                if($ret === false)  return false;
                return array('msg'=>'回复成功');
            }
        }else{
            return $this->error = $this->getError();
        }

    }
       

}