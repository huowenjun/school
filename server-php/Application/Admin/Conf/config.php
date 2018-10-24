<?php
return array(
    //'配置项'=>'配置值'
    'ADMIN_USER' => 'admin',
    'ADMIN_PASSWORD' => 'root',

    'AUTH_CONFIG' => array(
        'AUTH_ON' => true, //认证开关
        'AUTH_TYPE' => 1, // 认证方式，1为时时认证；2为登录认证。
        'AUTH_GROUP' => 'think_auth_group', //用户组数据表名
        'AUTH_GROUP_ACCESS' => 'think_auth_group_access', //用户组明细表
        'AUTH_RULE' => 'think_auth_rule', //权限规则表
        'AUTH_USER' => 'think_user',//用户信息表,
    ),

    //用户状态
    'USER_STATUS' => array(0=>'<span class="label label-success">有效</span>',1=>'<span class="label label-danger">无效</span>'),
	
);