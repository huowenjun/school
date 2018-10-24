<?php

namespace Common\Model;

use Common\Model\BaseModel;

class StudentModel extends BaseModel
{
    protected $_validate = array(
        // array('g_id','require','请选择年级'),
        // array('c_id','require','请选择班级'),
        // array('a_id','require','请选择校区'),
        array('stu_no', 'require', '请输入学号'),
        // array('icc_id','require', '请输入ICCID号'),
        // array('nfc_id','require', '请输入NFCID号'),

        array('icc_id', 'validStorage', '请输入ICCID号', 1, 'callback', 1),  //使用回调函数validStorage icc_id
        // array('nfc_id','validStorage','请输入NFCID号',1,'callback',1),  //使用回调函数validStorage nfc_id

        array('stu_no', '', '学号已存在！', 0, 'unique', 3),
        // array('stu_no','validlist','学号已存在！',1,'callback',3),
        // array('icc_id','','ICCID已存在',1,'unique',3),
        array('stu_name', 'require', '请输入学生姓名'),
        // array('imei_id', 'require', '请输入IMEI号码'),
        // array('imei_id','','IMEI号已存在',1,'unique',1),
        // array('imei_id','','IMEI号已存在',1,'unique',1),
        // array('stu_phone','require', '请输入学生卡手机号码'),
        // array('stu_phone', 'validStorage', '请输入学生卡手机号码', 1, 'callback', 1),
        // array('stu_phone', 'isTel1', '学生卡手机号码格式不正确', 0, 'callback', 3),

        // array('stu_phone','validStorage','学生卡手机号码不能为空',1,'callback',1),  //使用回调函数validStorage 
        array('parent_phone', 'validStorage', '联系电话不能为空', 1, 'callback', 1),  //使用回调函数validStorage parent_phone
        array('address', 'validStorage', '常驻地址不能为空', 1, 'callback', 1),  //使用回调函数validStorage
        array('stu_no', 'validStorage', '学生学号不能为空', 1, 'callback', 1),  //使用回调函数validStorage
        array('stu_name', 'validStorage', '学生姓名不能为空', 1, 'callback', 1),  //使用回调函数validStorage
        // array('imei_id','validStorage','imei号不能为空',1,'callback',1),  //使用回调函数validStorage
        array('username', 'validStorage', '登录帐号不能为空', 1, 'callback', 1),  //使用回调函数validStorage
        array('relation', 'validStorage', '请选择学生与监护人关系', 1, 'callback', 1),  //使用回调函数validStorage
        array('parent_name', 'validStorage', '监护人姓名不能为空', 1, 'callback', 1),  //使用回调函数validStorage
        // array('stu_phone','','学生卡手机号码已存在',1,'unique',3),
        // array('rfid_id','require','请输入RFID号'),
        // array('rfid_id','','RFID号已存在',1,'unique',1),
        array('birth_date', 'require', '请选择出生日期'),
        // array('birth_date', "^(^(\d{4}|\d{2})(\-|\/|\.)\d{1,2}\3\d{1,2}$)|(^\d{4}年\d{1,2}月\d{1,2}日$)$", '正确的时间格式 0000-00-00 00:00:00', 1, 'regex', 3),

        array('rx_date', 'require', '请选择入学日期'),
        array('card_id', 'require', '请输入身份证号'),
        // array('card_id','(/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{4}$/)', '身份证号不合法！', 1, 'regex', 3),
//        array('card_id','/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/','身份证号格式不正确',self::EXISTS_VALIDATE),// 暂时关掉
        // array('guardian_name','require','请输入监护人姓名'),  
        // array('phone','require','请输入监护人电话'),
        // array('card_id','','身份证号已存在',0,'unique',3),
//        array('card_id','','身份证号已存在',0,'unique',3), // 在新增的时候验证card_id字段是否唯一 // 暂时关掉
        // array('relation','require','请选择学生与监护人关系'),
        // array('work_unit','require','请输入监护人工作单位'),
        // array('address','require','请家庭地址'),
        // array('family_tel','/^\d{4}\-\d{7}$/','家庭电话号码格式不正确',2),
        array('family_tel', 'isTel', '家庭电话号码格式不正确,格式(0519-88882883)', 2, 'callback', 3),
        // array('family_tel','require','请输入家庭电话'),   /^\d{4}\-\d{7}$/
        // array('email','require','请输入邮箱'),
//        array('email','email','邮箱格式不正确',2),
        // array('email','','邮箱已经存在！',2,'unique',3),
        // array('email','','邮箱已经存在！',1,'unique',self::EXISTS_VALIDATE),
        // array('user_name','require','请输入登录帐号'),
        // array('user_name','','登录帐号已存在，请设置其它帐号',1,'unique',3),
    );

    //使用回调函数validStorage
    protected function validStorage($value)
    {
        // var_dump($value);die;
        if (empty($value)) {
            return false;
        } else {
            return true;
        }
    }


    function isTel($tel, $type = 'tel')
    {
        $regxArr = array(
            // 'sj' => '/^(\+?86-?)?(18|15|13)[0-9]{9}$/',
            'tel' => '/^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$/',//座机验证
            // '400' => '/^400(-\d{3,4}){2}$/',
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

    function isTel1($tel, $type = 'sj')
    {
        $regxArr = array(
            'sj' => '/^1[3578]\d{9}$/',
            // 'tel' => '/^(010|02\d{1}|0[3-9]\d{2})-\d{7,9}(-\d+)?$/',
            // '400' => '/^400(-\d{3,4}){2}$/',
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

        // dump(self::getLastSql());

        if ($page == 0) {
            $ret ["count"] = count($ret ["list"]);
        } else {
            $ret ["count"] = $this->getCount(); //获得记录总数
        }

        $SEX_TYPE = C("SEX_TYPE");
        $RELATION = C("RELATION");
        $DEVICE = C("DEVICE");//绑定状态
        $DEVICE_TYPE = C('DEVICE_TYPE');

        $Grade = D('Grade')->getField("g_id,name");//年级
        $Class = D('Class')->getField("c_id,name");//班级
        // $Device = D('DeviceManage')->getField("stu_id,imei,rfid");//设备
        // var_dump($Device);
        // echo "<pre>";
        // var_dump($AreaRegion);
        $studentGroup = D('StudentGroupAccess');
        $studentParen = D('StudentParent');
        $AdminUser = D('AdminUser');
        foreach ($ret['list'] as $k => $v) {
            $where['stu_id'] = $v['stu_id'];

            $studenInfo = $studentGroup->where($where)->getField('sp_id', true);
            // var_dump($studenInfo);

            $spId = "";
            foreach ($studenInfo as $key => $value) {
                $spId = $spId . $value . ',';
            }
            $where2['sp_id'] = array('IN', rtrim($spId, ','));
            $u_id = $studentParen->where($where2)->getField('u_id', true);//用户表uid

            $UId = "";
            foreach ($u_id as $key => $value) {
                $UId = $UId . $value . ',';
            }
            $where3['user_id'] = array('IN', rtrim($UId, ','));
            // var_dump($where3);die;
            $studentParent = $AdminUser->where($where3)->select();//用户表uid
            $parentName = "";
            $parentPhone = "";
            $Phone = "";
            foreach ($studentParent as $key => $value) {
                $parentName = $parentName . $value['name'] . '<br>';
                $parentPhone = $parentPhone . $value['username'] . '<br>';
                $Phone = $Phone . $value['phone'] . '<br>';
            }
            $ret['list'][$k]['parent_name'] = rtrim($parentName, '<br>');
            $ret['list'][$k]['username'] = rtrim($parentPhone, '<br>');
            $ret['list'][$k]['phone'] = rtrim($Phone, '<br>');

            $ret['list'][$k]['sex'] = $SEX_TYPE[$v['sex']] ? $SEX_TYPE[$v['sex']] : '';
            $ret['list'][$k]['g_id'] = $Grade[$v['g_id']] ? $Grade[$v['g_id']] : '';
            $ret['list'][$k]['c_id'] = $Class[$v['c_id']] ? $Class[$v['c_id']] : '';
            // $ret['list'][$k]['imei_id'] = $Device[$v['stu_id']]['imei']?$Device[$v['stu_id']]['imei']:'';
            // $ret['list'][$k]['rfid_id'] = $Device[$v['stu_id']]['rfid']?$Device[$v['stu_id']]['rfid']:'';
            $ret['list'][$k]['relation'] = $RELATION[$v['relation']] ? $RELATION[$v['relation']] : '';
            $ret['list'][$k]['status'] = $DEVICE[$v['status']] ? $DEVICE[$v['status']] : '';
            $ret['list'][$k]['devicetype'] = $DEVICE_TYPE[$v['devicetype']];
        }

        return $ret;
    }


    /* 查询列表
	 * @param string $fields 字段
	 * @param array $where where条件数组: array('field1'=>'value1','field2'=>'value2')
	 * @param array $orderby orderby数组: array('field1'=>'ASC','field2'=>'DESC')
	 * @param int $page 页码
	 * @param int $pagesize 每页数量
	 * @param array $groupby
	 * @param array $data_auth 数据权限
	 *  * @return uret['count']  总数    $ret['list']  查询结果列表
	 */
    public function queryListA($fields, $where = null, $orderby = null, $page = 0, $pagesize = 0)
    {

        if ($page) {
            $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
        }
        if ($page) {
            $ret['list'] = self::alias('a')->join('LEFT JOIN sch_attendance b ON a.stu_id=b.stu_id')->field($fields)->where($where)->order($orderby)->page($page, $pagesize)->group('')->select();
        } else {
            $ret['list'] = self::alias('a')->join('LEFT JOIN sch_attendance b ON a.stu_id=b.stu_id')->field($fields)->where($where)->order($orderby)->group('')->select();
        }
        if ($page == 0) {
            $ret ["count"] = count($ret ["list"]);
        } else {
            $ret ["count"] = $this->getCount(); //获得记录总数
        }
        return $ret;
    }


    function getStuClassByParentUserId($user_id)
    {
        $where['sp.u_id'] = $user_id;

        $studentGroup = D('StudentGroupAccess');
        $ret = $studentGroup->alias('stu_acc')
            ->field('stu.g_id,stu.c_id,stu.a_id,stu.s_id ')
            ->join('sch_student stu on stu.stu_id = stu_acc.stu_id ')
            ->join('sch_student_parent sp on stu_acc.sp_id = sp.sp_id')
            ->group(" stu.c_id ")
            ->where($where)->select();

        return $ret;
    }


}