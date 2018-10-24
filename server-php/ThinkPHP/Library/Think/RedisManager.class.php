<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Think;
class RedisManager
{
    private static $_instance = null; //静态实例

    private function __construct()
    { //私有的构造方法
        self::$_instance = new \Redis();
        self::$_instance->connect('127.0.0.1', '6379');
        if (isset($config['password'])) {
            self::$_instance->auth('School1502');
        }
    }

    //获取静态实例
    public static function getRedis()
    {
        if (!self::$_instance) {
            new self;
        }

        return self::$_instance;
    }

    /*
     * 禁止clone
     */
    private function __clone()
    {
    }
}
