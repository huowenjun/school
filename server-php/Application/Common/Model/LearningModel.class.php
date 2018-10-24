<?php
/**
 * Created by PhpStorm.
 * User: Mengfanmin
 * Date: 2017/3/20 0020
 * Time: 上午 10:32
 */
namespace Common\Model;

use Common\Model\BaseModel;

class LearningModel extends BaseModel
{
    protected $_validate =array(
        array('icon_url','require','图标路径不能为空'),
        // array('region_id','require','请选择服务区域'),
        array('web_url','require','图标跳转链接'),
        // array('end_time','require','请选择结束展示日期'),
        // array('unit_price','require','请选择展示单价'),
    );

    public function queryListEX($fields, $where = null, $orderby = null, $page = 0, $pagesize = 0, $groupby = null, $data_auth = null)
    {
        if ($page) {
            $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
        }
        if ($page) {
            $ret['list'] = self::field($fields)->where($where)->order($orderby)->page($page, $pagesize)->group($groupby)->select();
        } else {
            $ret['list'] = self::field($fields)->where($where)->order($orderby)->group($groupby)->select();
        }

        if ($page == 0) {
            $ret ["count"] = count($ret ["list"]);
        } else {
            $ret ["count"] = $this->getCount(); //获得记录总数
        }
        $Learning_status = C('Learning_status');//状态
        foreach($ret['list'] as $key=>$value){
            if(!empty($value['icon_url'])){
                $is_file = file_exists($_SERVER['DOCUMENT_ROOT'].$value['icon_url']);
                if($is_file){
                    $ret['list'][$key]['icon_url'] = "<a href='".$value['icon_url']."' target='_blank'><img width='100' src='".$value['icon_url']."'></a>";
                }else{
                    $ret['list'][$key]['icon_url'] = "<a href='".C('ONLINE_NAME').$value['icon_url']."' target='_blank'><img width='100' src='".C('ONLINE_NAME').$value['icon_url']."'></a>";
                }

            }
            if($value['region_id']){
                $regionArr = M("AreaRegion")->where(" region_id in ({$value['region_id']}) ")->getField("region_name",true);
            }
            if($value['s_id']){
                $schoolArr = M("SchoolInformation")->where(" s_id in ({$value['s_id']}) ")->getField("name",true);
            }

            $ret['list'][$key]['region_id'] = "<span >".$regionArr[0]."</span>"."...<a style='cursor:pointer' title='".implode(',',$regionArr)."'>全部</a>";
            $ret['list'][$key]['s_id'] = "<span >".$schoolArr[0]."</span>"."...<a style='cursor:pointer' title='".implode(',',$schoolArr)."'>全部</a>";
            $ret['list'][$key]['status'] = $Learning_status[$value['status']]?$Learning_status[$value['status']]:'';
           
        }
        return $ret;
    }
}