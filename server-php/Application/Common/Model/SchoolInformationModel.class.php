<?php
namespace Common\Model;

use Common\Model\BaseModel;

class SchoolInformationModel extends BaseModel
{

    protected $_validate = array(
        array('region_id', 'require', '请选择地区'),
        array('name', 'require', '请输入学校名称'),
        // array('name','','学校名称已存在',1,'unique',1),
        array('build_time', 'require', '请选择成立时间'),
        array('main_zrr', 'require', '请输入主负责人姓名'),
        array('main_phone', 'require', '请输入主负责人电话'),
        array('sub_zzr', 'require', '请输入副负责任人姓名'),
        array('sub_phone', 'require', '请输入副负责任人电话'),
        array('tel', 'require', '请输入学校内线电话'),
        array('address', 'require', '请输入学校地址'),
        array('user_name', 'require', '请输入登陆用户名地址'),
        array('user_name', '', '登陆用户名已存在', 1, 'unique', 1),
        array('main_phone', 'isTel', '主负责人电话格式不正确', 0, 'callback', 3),
        array('main_phone', '', '主负责人电话号已存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
        array('sub_phone', 'isTel', '副负责人电话格式不正确', 0, 'callback', 3),
        array('sub_phone', '', '副负责人电话号已存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
        array('tel', 'isTel', '内线电话号格式不正确,格式(010-12345678)', 0, 'callback', 3),
        array('tel', '', '内线电话号已存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),


    );

    function isTel($tel, $type = '')
    {
        $regxArr = array(
            'sj' => '/^(\+?86-?)?(18|15|13|17)[0-9]{9}$/',
            'tel' => '/^(010|02\d{1}|0[3-9]\d{2})-\d{7,9}(-\d+)?$/',
            '400' => '/^400(-\d{3,4}){2}$/',
        );
        if ($type && isset($regxArr[$type])) {
            return preg_match($regxArr[$type], $tel) ? true : false;
        }
        foreach ($regxArr as $regx) {
            if (preg_match($regx, $tel)) {
                return true;
            }
        }
        return false;
    }

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

        //dump(self::getLastSql());

        if ($page == 0) {
            $ret ["count"] = count($ret ["list"]);
        } else {
            $ret ["count"] = $this->getCount(); //获得记录总数
        }
        // strtotime
        $USER_STATUS = C("USER_STATUS");
        $SEX_TYPE = C("SEX_TYPE");

        $AreaRegion = M("AreaRegion")->getField("region_code,region_name");

        foreach ($ret['list'] as $k => $v) {

            $ret['list'][$k]['valid'] = $USER_STATUS[$v['valid']] ? $USER_STATUS[$v['valid']] : '';
            $ret['list'][$k]['region_id'] = $AreaRegion[$v['region_id']] ? $AreaRegion[$v['region_id']] : '';
            $ret['list'][$k]['sex'] = $SEX_TYPE[$v['sex']] ? $SEX_TYPE[$v['sex']] : '';
            $ret['list'][$k]['build_time'] = substr($ret['list'][$k]['build_time'], 0, 10);


        }

        return $ret;
    }

    function getSchoolInfoBySidAids($s_ids, $a_ids)
    {
        $where['sch_info.s_id'] = array('in', $s_ids);
        $where['sch_area.a_id'] = array('in', $a_ids);
        $where['sch_info.valid'] = 0;
        $where['sch_area.valid'] = 0;

        $ret = self::alias('sch_info')
            ->field('sch_info.s_id,sch_info.name s_name,sch_area.a_id,sch_area.name a_name,sch_info.main_zrr,sch_info.main_phone,'
                . 'sch_info.sub_zzr,sch_info.sub_phone,sch_info.address,sch_info.tel')
            ->join('sch_school_area sch_area on sch_area.s_id = sch_info.s_id')
            ->where($where)->select();
        if (empty($ret)) {
            $ret = array();
        }
        return $ret;
    }

}