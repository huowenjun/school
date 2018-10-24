<?php
$server_ip = $_SERVER['SERVER_ADDR'];
if ($server_ip == '59.110.0.250') {//线上环境
    $db_host = '59.110.0.250';
} else {
    $db_host = '59.110.0.250';
}
return array(
    //'配置项'=>'配置值'
    'DEFAULT_MODULE' => 'Admin',  // 默认模块
    //页面Trace false
    'SHOW_PAGE_TRACE' => false,
    'TAGLIB_PRE_LOAD' => 'html',
    'URL_CASE_INSENSITIVE' => false,
    'CONTROLLER_LEVEL' => 2,
    'DB_TYPE' => 'mysql', // 数据库类型

    //  此处为线上服务器配置
    'DB_HOST' => $db_host, // 服务器地址
    'DB_NAME' => 'school', // 数据库名
    'DB_USER' => 'school', // 用户名
    'DB_PWD' => 'school1502', // 密码

    'DB_PORT' => 3306, // 端口
    'DB_PREFIX' => 'sch_', // 数据库表前缀
    'DB_CHARSET' => 'utf8', // 字符集
    'DB_DEBUG' => TRUE, // 数据库调试模式 开启后可以记录SQL日志 3.2.3新增
    'DATA_CACHE_TYPE' => 'file',//设置缓存方式为file
    'DATA_CACHE_TIME' => '600',//缓存周期600秒

    //数据库配置2
    'DB_CONFIG2' => array(
        'db_type' => 'mysql',
        'db_user' => 'schoolnews',
        'db_pwd' => 'school1502',
        'db_host' => '182.92.113.203',
        'db_port' => '3306',
        'db_name' => 'schoolnews',
        'db_charset' => 'utf8',
    ),

    //线上服务器ip
    'SERVERADDR' => '59.110.0.250',

    //报警类型
    'BAOJING_TYPE' => array(1 => 'SOS紧急', 2 => '低电提醒', 3 => '学生卡未佩戴', 4 => '电子围栏'),
    // 'BAOJING_TYPE'           => array(1=>'SOS报警',2=>'换卡提醒',3=>'低电提醒',4=>'盲区提醒',5=>'开机提醒',6=>'关机提醒',7=>'卫星定位'),

    //考试类型
    'EXAM_TYPE' => array(1 => '单元测试', 2 => '周考', 3 => '月考', 4 => '期中考试', 5 => '期末考试', 0 => '其它考试'),
    //单考成绩录入情况
    'INPUT_STATE' => array(0 => '未录入', 1 => '已录入'),
    //请假类型
    'LEAVE_TYPE' => array(1 => '事假', 2 => '病假', 0 => '其它'),
    //请假审批状态
    'LEAVE_STATUS_NAME' => array(1 => '已批准', 2 => '已驳回', 0 => '已提交'),
    'LEAVE_STATUS' => array(1 => '<span class="label label-success">已批准</span>', 2 => '<span class="label label-danger">已驳回</span>', 0 => '<span class="label label-primary ">已提交</span>'),
    //奖励类型
    'REWARD_TYPE' => array(1 => '班级奖励', 2 => '年级奖励', 3 => '校级奖励', 4 => '市级奖励', 5 => '省级奖励', 6 => '国家奖励'),
    //关系  学生与家长
    'RELATION' => array(1 => '爸爸', 2 => '妈妈', 3 => '爷爷', 4 => '奶奶', 5 => '外公', 6 => '外婆',7=>'哥哥',8=>'姐姐',9=>'叔叔',10=>'舅舅',11=>'阿姨',12=>'婶婶', 0 => '其它'),
    'RELATION_MALE' => array('爸爸', '爷爷', '外公', '其它'),
    'RELATION_FEMALE' => array('妈妈', '奶奶', '外婆'),
    //学历
    'EDUCATION' => array(1 => '初中', 2 => '中技', 3 => '高中', 4 => '中专', 5 => '大专', 6 => '本科', 7 => '硕士', 8 => 'EMBA', 9 => '博士', 0 => '其它'),
    'USER_TYPE' => array(0 => '游客', 1 => '学校管理员', 2 => '系统用户', 3 => '老师', 4 => '家长'),
    'USER_TYPE_EN' => array(1 => 'school', 2 => 'systemUser', 3 => 'teacher', 4 => 'parents'),
    //评语类型
    'REMARK_TYPE' => array(1 => '平时评语', 2 => '每周评语', 3 => '每月评语', 4 => '期中评语', 5 => '期末评语'),
    //日志记录类型
    'RECORD_TYPE' => array(0 => '平台', 1 => 'APP'),
    'SEX_TYPE' => array(0 => '女', 1 => '男'),
    'DEVICE' => array(0 => '未激活', 1 => '已激活'),
    //消息类型
    'MESSAGE_TYPE' => array(1 => '系统消息', 2 => '公告', 3 => '私信'),
    //紧急程度
    'URGENCY' => array(1 => '普通', 2 => '紧急'),
    //进出校状态
    'SCHOOL_TYPE' => array(1 => '进校', 0 => '出校'),
    //签到状态
    'SIGNIB_TYPE' => array(0 => '异常', 1 => '正常'),
    //设备状态
    'DEVICE_TYPE' => array(0 => '离线', 1 => '在线'),
    //定位类型
    'TYPE' => array(0 => 'GPS', 1 => '基站定位'),
    'IS_EFFECT' => array(0 => '否', 1 => '是'),
    'Learning_status' => array(0 => '<span class="label label-success">上线</span>', 1 => '<span class="label label-danger">下线</span>'),
    'BANNER_TYPE' => array(0 => '天数计费', 1 => '展示量计费', 2 => '点击量计费'),
    'DB_PARAMS' => array(\PDO::ATTR_CASE => \PDO::CASE_NATURAL),
    'ONLINE_NAME' => 'http://school.xinpingtai.com',
    //支付宝公钥
    'ALIPAY_RSA_PUBLIC_KEY' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAmB7HwH4wh2zxcuMXanVVXVII6rBr+Jm898P1Xm5Iyrcr4K8CCJnzOZBlKf5Nwujz6Pc8A02GYrbxSBVOF6tyGTq9g5UQndflqglxl4x2HEHjAMzPRwjsVj6H6VgZHBppEvhlXDpOHrmTiYW+3cKCCk+NsxO80kJ66AMmqKtzlEwz6NDKhHva1lEd3lUmis5MzAhzaW2Fm4PA6nB9jnWhhvP+A1VULyY9BO9gNiz7Apt0989ON86QQbpRQnbr7LxR5Y/6tom8ZB3T5VqoYd+HiclGBHDl4vlGv3zU6mF83VMXEqhRCnH5U5P9YqcBler2YscmQZ/ZsG+rQjNnP+EgiwIDAQAB",
    //家长端 应用公钥
    'ALIPAY_RSA_PUBLIC_KEY2' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAvHNOcAOXmTyttuHRgVjTLg48OOmEEi3T5zcAZbgTRoLAKLkN3+i9DCeUSFTx+fuyJLsCdJVRdPdy2zC/SwCTcjplWdEbOcLU+FYHExViYHH5vrL0sgrX0jZTf3R97zNz1lumNUmmNnHooX9x029mC+IzUUGlrnYKDrpq4opBsA6GuDdRPIKB+g2LbdH3/upHqEoEVwzYyvJghaq0EcmDdfdAm7APnbqIP6F/4G6CtGbZ0KGuqh01DWeI6RuELT3Yfof5N19WO4Rk6SjT1OMtG13aOxi1S/5o8iyQyAVOhqsnazioyGd7OmoLOThPWmzWMpul2DEfyRTc2ewUUnGrRQIDAQAB",
    //家长端 应用私钥
    'RSA_PRIVATE_KEY' => "MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQC8c05wA5eZPK224dGBWNMuDjw46YQSLdPnNwBluBNGgsAouQ3f6L0MJ5RIVPH5+7IkuwJ0lVF093LbML9LAJNyOmVZ0Rs5wtT4VgcTFWJgcfm+svSyCtfSNlN/dH3vM3PWW6Y1SaY2ceihf3HTb2YL4jNRQaWudgoOumriikGwDoa4N1E8goH6DYtt0ff+6keoSgRXDNjK8mCFqrQRyYN190CbsA+duog/oX/gboK0ZtnQoa6qHTUNZ4jpG4QtPdh+h/k3X1Y7hGTpKNPU4y0bXdo7GLVL/mjyLJDIBU6GqydrOKjIZ3s6ags5OE9abNYym6XYMR/JFNzZ7BRScatFAgMBAAECggEAaL7Ci1pDyiXC/JLZy0Ze4wuAh7Wr9hrI3IxiySced6O3QStSvfD0GyxorCei8+rloqrbe4d/Zj8f9RtMSFkCm4w/x0OGGX3kuD/A4OeS7b6MLWX0wn1qZmpR0NckJG955Fy+roHIRBzeS921m+sgUlyhX3nYqHbtsjAFtvNX/Y2xKPf5WNa4glFk7aij/iPX6pXrHdxdo/rpD6Zt11EpnAk8aGKDunDResJ98OcSA0myPFC51TumePCdDQJyvuwnrKgwQMrI6OZO8xeiiOxJRE0wMCE3RfJjMoCzmB8cwq5EWTiZjDILuLdSBG6Exj28zNYcAQHcdCHE+SfXwWmBwQKBgQDiAmqsmlLxFufeuAHlZR9ocAI+EwhB/ZwiJqzKjCbg5QJqcaVpdJET7kTMfrpL3CdhezdlbDS530SEwrKjKDt+FOHJ5ezF6PTOTxK4KrIE2ZPr7dL9xMKf2Alegj9vrR5yIkoKL9j6vaUu4ePy3mu0/7JgLoZymkHebBBl8xEvkQKBgQDVdQCnPf2abHaP92CzAsKZkmCZPYyLYvfMH7tJH1F1PF8WCP2ZBOp8euHdXNkDHGQduYhn8HkSf2e2ay/ShpZuSA61kclNadluEe0x8iaEEgn+EBfH2kMF68pH0iW5DlW8oer7E1kybL0G0ZEHyZ9S+r0wKf6T3nXTgQ8qxq0OdQKBgQDKcR26M5WdrEXPgoT4REcI1mO71HJuIcvL71aRK07b3WX3kIp41lfpQWDQx6b5sl53+9WX/H+SCoImZPt8F9qKSgwhO9mFQPCfJ8b9vgitPXM5PlLiym8GnI1v4T0PPENsOniVfVxe5KZkQyRadI6Hlw3hB2uYlcHwiF175Gh9cQKBgDBmUzuYpsQ5C7khEmAEpDNGKXkVp6SDUESMfV7bJxE6GyVX7IihwLlw833J67r02Q6UXwWSVSGIme+W5kUKF1nyJMOuxsIy2gZHMk085tbTcEiXRY0fREs3Z6pZUAxh37bhz/IWNQdl+IZvRj9JzEJ4cCVXoE3PB1Bp1xKP8fVxAoGBANOa07QOJ5CmngAKpDlzfsyKfW3CIoeJf/0pSV1LzLgI36lNaiLHEW7HkQAVCkc5E3fzi3LOSeaaQ6vvAe+MWQgkaSpa447HDVHWtXvmgtkSb3ntVP/kMnOtx60UPGfpO2F2xVelAoMv4C5BpFHj0FzeGgBEdhZqUHp23dc3C+tb",
    'PAYMENT_TYPE' => array('支付宝', '微信', '银联', '零钱'),
    'ORDER_STATUS' => array('未支付', '已支付(过期)', '已支付(无库存)', '交易成功', '充值中', '充值失败'),
    //订单类型 0账户零钱充值 1学生卡余额充值 2话费充值 3提现 4流量充值 5生活缴费(水电煤) 6火车票7飞机票
    'ORDER_TYPE' => array('账户零钱充值', '学生卡余额充值', '话费充值', '提现','流量充值','生活缴费(水电煤)','火车票','飞机票'),
    'REFUND_STATUS' => array(0=>'<span class="label label-danger">未审核</span>',
        1=>'<span class="label label-success">通过</span>',
        2=>'<span class="label label-danger">驳回</span>',
        3=>'<span class="label label-success">成功</span>',
    ),

    //非自己发布展示提成
    'SHOW_PERCENT' => 0.01,
    //非自己发布点击提成
    'CLICK_PERCENT' => 0.02,
    //自己发布展示提成
    'PRIV_SHOW_PERCENT' => 0.03,
    //自己发布点击提成
    'PRIV_CLICK_PERCENT' => 0.04,
    //话费充值KEY
    'TELTOPUP' => '8da0c5cc1c14594402542d0e142246e0',
    //APP推送时间区间6,20 -> 6:00 -20:00接收警报推送
    'APP_PUSH_TIME' => array(6, 20),
    //推送频率30分钟
    'APP_PUSH_TIME_STEP' => 30,
    'AD_POSITION' => array(
        1 => 'Top1',
        2 => 'Top2',
        3 => 'Right1',
        4 => 'Right2',
        5 => 'Bottom1',
        6 => 'Bottom2',
    ),
    //提现管理  是否审核  
    'AUDIT' => array(0 => '未审核', 1 => '同意', 2 => '驳回'),
    //Session配置
    'SESSION_TYPE' => 'db',            //数据库存储session
    'SESSION_TABLE' => 'think_session',    //存session的表
    'SESSION_EXPIRE' => 3600,                //session过期时间

    'SYSTEM_MODEL' => array('IOS', 'Android', 'PC'),
    'COUNT_TYPE' => 'false',
    'BANNER_DATA' => array(
        array(
            'id' => "",
            "img" => "http://school.xinpingtai.com/1.jpg",
            'url' => "",
            'title' => "",
            'type' => "",
            'turn_type' => 1,
            'modelname_app' => "",
            'modelname_ios' => "",
            'modelname_android' => "",
            'region_id' => "",
            'sid' => "",
        ),
        array(
            'id' => "",
            "img" => "http://school.xinpingtai.com/2.jpg",
            'url' => "",
            'title' => "",
            'type' => "",
            'turn_type' => 1,
            'modelname_app' => "",
            'modelname_ios' => "",
            'modelname_android' => "",
            'region_id' => "",
            'sid' => "",
        ),
        array(
            'id' => "",
            "img" => "http://school.xinpingtai.com/3.jpg",
            'url' => "",
            'title' => "",
            'type' => "",
            'turn_type' => 1,
            'modelname_app' => "",
            'modelname_ios' => "",
            'modelname_android' => "",
            'region_id' => "",
            'sid' => "",
        ),
        array(
            'id' => "",
            "img" => "http://school.xinpingtai.com/4.jpg",
            'url' => "",
            'title' => "",
            'type' => "",
            'turn_type' => 1,
            'modelname_app' => "",
            'modelname_ios' => "",
            'modelname_android' => "",
            'region_id' => "",
            'sid' => "",
        ),

    ),
    'PRODUCT_NAME' => array('全民阅读', '智慧金融', '生活缴费', '合作产品', '校园购','教育头条'),
    'PRODUCT_PATH' =>array('','/Public/Uploads/app/imgage/jinrong.png','','/Public/Uploads/app/imgage/hezuo.png',''),
    'ARTICLE_CATE'=>array(2=>'tuijian','zhengce','redian','xiaoyuan','kaoshi','difang','keji','jiedu','guoji'),
    'USER_ROLE'=>array('储户','学校管理员','系统管理员','教师',
                        '家长','省教委管理员','市教委管理员','县教委管理员',
                        '发布 广告','第三方代理公司','第三方代理商','区县代理商',
                        '会员','市代理总监','区代理总监',
        ),//0会员(第一版为会员，第二版为储户) 1学校管理员 2 系统用户 3 老师
         // 4 家长  5省委 6市委 7县委
        // 9第三方代理公司 10分销代理商 11区县代理商
        // 12会员 13市代理总监 14区代理总监
    'DEVICE_TYPE'=>array(1=>'学生卡',2=>'手表'),
    'WECHAT'=>array('appid'=>'wx229e8482e32502ed','appsecret'=>'53ce0566be98d9ec6539b98655ac580d','key'=>'53ce0566be98d9ec6539b98655ac580d','templateId'=>'FfAfdZ1DUsbI6Pds2-BL5ig1YYE67jd3kXRFa5UCiUM'),//wechat开发者的appid和secret和模板
    'WXSP'=>array('appid'=>'wxd372233a9cdfe598','appsecret'=>'c344933f9249ec29181f05deab9c1f44'),//微信小程序配置

);