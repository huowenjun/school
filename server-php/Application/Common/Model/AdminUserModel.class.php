<?php

namespace Common\Model;

use Common\Model\BaseModel;

class AdminUserModel extends BaseModel
{

    protected $_validate = array(
        array('username', 'require', '账号不能为空'),
        array('username', '4,16', '账号只能为4-16个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('name', '2,6', '姓名只能为2-6个字符', self::EXISTS_VALIDATE, 'length', self::MODEL_BOTH),
        array('username', '', '账号已经存在', 1, 'unique', 1),
        array('password', 'require', '密码必须填写！', self::MODEL_BOTH),
        //array('password','/^[a-zA-Z]\w{5,18}/','密码格式错误'), //字母开头，长度在6-18之间，只能包含字母、数字和下划线
        array('type', 'require', '请选择角色', 0, 1),
        array('phone', 'require', '手机号必须填写', 0, 1),
        array('sex', 'require', '性别必须选择', 0, 1),
        array('name', 'require', '请输入名称', 1, 1),
        array('email', 'email', '邮箱格式不正确', 2),
        array('phone', '/(^1[3|4|5|7|8][0-9]{9}$)/', '手机号格式不正确', self::EXISTS_VALIDATE),
        array('phone', '', '手机号已存在', self::EXISTS_VALIDATE, 'unique', self::MODEL_INSERT),
        array('address', 'require', '常住地址不能为空'),
    );

    protected $trueTableName = 'think_user';

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
    public function queryListEx($fields, $where = null, $orderby = null, $page = 0, $pagesize = 0)
    {

        if ($page) {
            $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
        }
        if ($page) {
            $ret['list'] = self::alias('a')->join('LEFT JOIN think_auth_group_access b ON a.user_id = b.uid LEFT JOIN think_auth_group c ON b.group_id = c.id')->field($fields)->where($where)->order($orderby)->page($page, $pagesize)->group('')->select();
        } else {
            $ret['list'] = self::alias('a')->join('LEFT JOIN think_auth_group_access b ON a.user_id = b.uid LEFT JOIN think_auth_group c ON b.group_id = c.id')->field($fields)->where($where)->order($orderby)->group('')->select();
        }
        if ($page == 0) {
            $ret ["count"] = count($ret ["list"]);
        } else {
            $ret ["count"] = $this->getCount(); //获得记录总数
        }

        $lstStatus = C('USER_STATUS');
        $userType = C('USER_ROLE');
        $sexType = C('SEX_TYPE');
        foreach ($ret['list'] as &$value) {
            if (isset($value['status'])) {
                $value['status'] = isset($lstStatus[$value['status']]) ? $lstStatus[$value['status']] : $value['status'];
            }
            $value['user_type'] = $userType[$value['type']];
            $value['sex'] = isset($sexType[$value['sex']]) ? $sexType[$value['sex']] : $value['sex'];
            $value['ref_name'] = M('User','think_')->where("user_id = '{$value['ref_id']}'")->getField('name');
            $value['company_name'] = M('User','think_')->where("user_id = '{$value['company_id']}'")->getField('name');
        }

        // echo self::getLastSql();


        return $ret;
    }


    public function getGroup($user_id)
    {
        return self::alias('a')->join('LEFT JOIN think_auth_group_access b ON a.user_id = b.uid LEFT JOIN think_auth_group c ON b.group_id = c.id')
            ->field('c.*')->where(array('a.user_id' => $user_id))->find();
    }


}