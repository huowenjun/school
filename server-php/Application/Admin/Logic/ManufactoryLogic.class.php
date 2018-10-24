<?php
namespace Admin\Logic;
use Common\Model\ManufactoryModel;
class ManufactoryLogic extends ManufactoryModel {

    public function edit($data){

        if(!$this->create($data)){
            return false;
        }
        if(isset($data['f_mm']))
            $data['f_mm'] = md5($data['f_mm']);
        if($data['f_id'] > 0){
            $ret = $this->save($data);
        }else{
            $ret = $this->add($data);
        }
        return $ret;
    }
    
}