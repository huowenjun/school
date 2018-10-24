<?php

/**
 * 数据签名认证
 * @param  array $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data)
{
    //数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}


function checkEmail($email)
{
    return (bool)preg_match('/^[-_a-z0-9\'+*$^&%=~!?{}]++(?:\.[-_a-z0-9\'+*$^&%=~!?{}]+)*+@(?:(?![-.])[-a-z0-9.]+(?<![-.])\.[a-z]{2,6}|\d{1,3}(?:\.\d{1,3}){3})(?::\d++)?$/iD', (string)$email);
}

//检查密码正则
//齐超
function checkPwd($pwd)
{ //密码字母开头，允许5-16字节，允许字母数字下划线
    if (preg_match('/^[a-zA-Z][a-zA-Z0-9_]{4,15}$/', $pwd)) {
        return true;
    } else {
        return false;
    }
}

/*
    获取顶级域名 by 申瑞平  2015-9-25
    */
function get_domain($host)
{
    // $host=$_SERVER[HTTP_HOST];
    $host = strtolower($host);
    if (strpos($host, '/') !== false) {
        $parse = @parse_url($host);
        $host = $parse['host'];
    }
    $topleveldomaindb = array('com', 'edu', 'gov', 'int', 'mil', 'net', 'org', 'biz', 'info', 'pro', 'name', 'museum', 'coop', 'aero', 'xxx', 'idv', 'mobi', 'cc', 'me');
    $str = '';
    foreach ($topleveldomaindb as $v) {
        $str .= ($str ? '|' : '') . $v;
    }
    $matchstr = "[^\.]+\.(?:(" . $str . ")|\w{2}|((" . $str . ")\.\w{2}))$";
    if (preg_match("/" . $matchstr . "/ies", $host, $matchs)) {
        $domain = $matchs['0'];
    } else {
        $domain = $host;
    }
    return $domain;
}

/**
 * 数据导出到Excel中
 *
 * @param array $data_list 二维数组
 * @param string $col_list 表头
 * @param array $filename 文件名
 */
function export_to_excel($data_list, $col_list, $filename)
{
    // 包含PHPExcel类文件
    import('Vendor.PHPExcel.PHPExcel');

    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();
    // Set document properties
    $objPHPExcel->setActiveSheetIndex(0);
    $sheet = $objPHPExcel->getActiveSheet();
    // 第一行
    $k = 65;
    foreach ($col_list as $key => $v) {
        $i = 1;
        $field = chr($k) . $i;
        $sheet->setCellValue($field, $key);
        $k++;
    }
    // 数据
    foreach ($data_list as $value) {
        $k = 65;
        $i++;
        foreach ($col_list as $key => $v) {
            $field = chr($k) . $i;
            if (isset($value[$v])) {
                $sheet->setCellValueExplicit($field, $value[$v]);
            } else {
                $sheet->setCellValue($field, '');
            }
            $k++;
        }
    }

    // Rename worksheet
    $objPHPExcel->getActiveSheet()->setTitle($filename);

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    $filename = $filename . '_' . date("Y-m-d", time()) . '_' . time() . '.xls';
    header('pragma:public');
    header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $filename . '"');
    header("Content-Disposition:attachment;filename=$filename");//attachment新窗口打印inline本窗口打印
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;
}


/**
 * 数据导出到Excel中
 *
 * @param string $col_list 表头 array('姓名'=>'name',...)
 * @param array $filename 文件名
 */
function import_excel($col_list, $filename)
{
    // 包含PHPExcel类文件
    import('Vendor.PHPExcel.PHPExcel');

    // Create new PHPExcel object
    $objPHPExcel = PHPExcel_IOFactory::load($filename);
    $sheet = $objPHPExcel->getSheet(0);
    $rowCount = $sheet->getHighestRow(); // 取得总行数
    $columnCount = $sheet->getHighestColumn(); // 取得总列数
    $arrRet = array();
    //对应字段名称
    $arrTitle = array();
    for ($i = 'A'; $i <= $columnCount; $i++) {
        $arrTitle[$i] = $objPHPExcel->getActiveSheet()->getCell($i . '1')->getValue();
    }
    //循环读取excel文件
    for ($j = 2; $j <= $rowCount; $j++) {
        $arrTemp = array();
        for ($k = 'A'; $k <= $columnCount; $k++) {
            //读 取单元格
            $value = $objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue();
            if (is_object($value)) {
                $value = $value->__toString();
            }
            if (isset($col_list[$arrTitle[$k]])) {
                $arrTemp[$col_list[$arrTitle[$k]]] = $value;
            }
        }
        $arrRet[] = $arrTemp;
    }
    return $arrRet;
}

/**
 * 获取excel中的数据
 * @param $path 路径
 *
 * */
function excel_load($path)
{
    import('Vendor.PHPExcel.PHPExcel');
    $excel = \PHPExcel_IOFactory::load($path);//把导入的文件目录传入，系统会自动找到对应的解析类
    $sheet = $excel->getSheet(0);//选择第几个表，如下面图片，默认有三个表
    //获取表格文本数据
    $data = $sheet->toArray();
    if ($data) {
        foreach ($data as $key => $value) {
            if ($value[0] === NULL) {
                unset($data[$key]);
            }
        }
    }
    array_shift($data);
    return $data;
}


/**
 * 截取UTF-8编码下字符串的函数
 *
 * @param   string $str 被截取的字符串
 * @param   int $length 截取的长度
 * @param   bool $append 是否附加省略号
 *
 * @return  string
 */
function sub_str($string, $length = 0, $charset = 'utf-8', $append = true)
{

    if (strlen($string) <= $length) {
        return $string;
    }

    $string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);

    $strcut = '';

    if (strtolower($charset) == 'utf-8') {
        $n = $tn = $noc = 0;
        while ($n < strlen($string)) {

            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1;
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223) {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t < 239) {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247) {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251) {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } elseif ($t == 252 || $t == 253) {
                $tn = 6;
                $n += 6;
                $noc += 2;
            } else {
                $n++;
            }

            if ($noc >= $length) {
                break;
            }

        }
        if ($noc > $length) {
            $n -= $tn;
        }

        $strcut = substr($string, 0, $n);

    } else {
        for ($i = 0; $i < $length; $i++) {
            $strcut .= ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
        }
    }

    $strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

    if ($append && $string != $strcut) {
        $strcut .= '...';
    }

    return $strcut;

}

/*
 * get method
 */
function get($url, $param = array())
{
    if (!is_array($param)) {
        throw new Exception("参数必须为array");
    }
    $p = '';
    foreach ($param as $key => $value) {
        $p = $p . $key . '=' . $value . '&';
    }
    if (preg_match('/\?[\d\D]+/', $url)) {//matched ?c
        $p = '&' . $p;
    } else if (preg_match('/\?$/', $url)) {//matched ?$
        $p = $p;
    } else {
        $p = '?' . $p;
    }
    $p = preg_replace('/&$/', '', $p);
    $url = $url . $p;
    //echo $url;
    $httph = curl_init($url);
    curl_setopt($httph, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($httph, CURLOPT_SSL_VERIFYHOST, 1);
    curl_setopt($httph, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($httph, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");

    curl_setopt($httph, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($httph, CURLOPT_HEADER, 1);
    $rst = curl_exec($httph);
    curl_close($httph);
    return $rst;
}

/*
 * post method
 */
function post($url, $param = array())
{
    if (!is_array($param)) {
        throw new Exception("参数必须为array");
    }
    $httph = curl_init($url);
    curl_setopt($httph, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($httph, CURLOPT_SSL_VERIFYHOST, 1);
    curl_setopt($httph, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($httph, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
    curl_setopt($httph, CURLOPT_POST, 1);//设置为POST方式
    curl_setopt($httph, CURLOPT_POSTFIELDS, $param);
    curl_setopt($httph, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($httph, CURLOPT_HEADER, 1);
    $rst = curl_exec($httph);
    curl_close($httph);
    return $rst;
}


/**
 * 获取班级详情
 *
 * @param   int $c_id
 *
 * @return  array       班级信息
 */
function getClassInfo($c_id = 0)
{
    if (empty($c_id)) {
        return array();
    }
    $classInfo = M("class")->alias("a")
        ->field('a.c_id,a.g_id,a.name as c_name,b.name as g_name')
        ->join(" __GRADE__ b ON  a.g_id = b.g_id ")
        ->where(" a.c_id = '{$c_id}' ")
        ->find();
    if (empty($classInfo)) {
        $classInfo = array();
    }
    return $classInfo;
}


function upload_file()
{
    $result = array();
    $upload = new \Think\Upload();// 实例化上传类
    $upload->maxSize = 2097150;// 设置附件上传大小  2M
    $upload->exts = array('jpg', 'gif', 'png', 'jpeg', 'amr');// 设置附件上传类型
    $upload->rootPath = './Public/Uploads/'; // 设置附件上传根目录
    $upload->savePath = 'imgage/'; // 设置附件上传（子）目录

    // 上传文件
    $info = $upload->upload();
    if (!$info) {// 上传错误提示错误信息
        $result['status'] = 0;
        $result['info'] = $upload->getError();
    } else {// 上传成功
        foreach ($info as $file) {
            $url = '/Public/Uploads/' . $file['savepath'] . $file['savename'];
            $result['status'] = 1;
            $result['info'] = $url;
        }
    }
    return $result;
}

//生成订单号
function build_order_no()
{
    return date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}

//获取城市顶级id
function get_top_region($region_id)
{
    //获取市id
    $region_info = M("AreaRegion")->alias("a1")
        ->field(" a1.region_id as county_id,a1.parent_id as city_id ,a2.parent_id as prov_id")
        ->join("__AREA_REGION__ a2 ON a1.parent_id = a2.region_id")
        ->where("a1.region_id = '{$region_id}'")->find();

    return $region_info;
}

//获取城市名称
function get_region_name($region_id)
{
    $name = M("AreaRegion")->where("region_id = '{$region_id}'")->getField("region_name");
    return $name;
}

/**
 * 修改订单状态
 * @param $trade_no 本平台订单号
 * @param $out_trade_no 外部平台订单号
 * $status 4:充值中
 * */
function payment_paid($trade_no, $out_trade_no, $status = 0)
{
    //获取订单详情(未支付)
    $orderInfo = M("PaymentNotice")
        ->alias("a")
        ->field("b.*")
        ->join("__DEAL_ORDER__ b on a.order_id = b.id")
        ->where("a.notice_sn = '{$trade_no}' and a.is_paid != 1")
        ->find();

    if (!empty($orderInfo)) {
        //充值金额 total_amount
        $account = $orderInfo['total_price'];

        $Account = M("UserAccount");
        $Account->startTrans();
        $u_id = $orderInfo['user_id'];
        $info = $Account->where(array("u_id" => $u_id))->find();
        if ($orderInfo['type'] == 0) {
            //账户充值订单
            $money = $info['account'] + $account;
            $data['account'] = $money;
            $data['u_id'] = $u_id;
            if (empty($info)) {
                $res = $Account->add($data);
            } else {
                $res = $Account->where(array("u_id" => $u_id))->save($data);
            }
        } else {
            //话费充值订单
            if (($orderInfo['type'] == 2) && ($status == 4)) {
                //调用话费充值接口
                import('Vendor.Elife.TelephoneCharge');
                $TelephoneCharge = new \TelephoneCharge();
                $telephoneChargeInfo = $TelephoneCharge->payBill($orderInfo['memo'], $orderInfo['total_price'], $trade_no);
            }
            //流量充值订单
            if (($orderInfo['type'] == 4) && ($status == 4)) {
                //调用流量充值接口
                import('Vendor.Elife.MobileFlow');
                $MobileFlow = new \MobileFlow();
                $MobileFlowInfo = $MobileFlow->payBill($orderInfo['memo'], $orderInfo['item_id'], $trade_no);
            }
            //生活缴费充值订单
            if (($orderInfo['type'] == 5) && ($status == 4)) {
                import('Vendor.Elife.WaterElectric');
                $WaterElectric = new \WaterElectric();
                $dataObj = $WaterElectric->payBill($orderInfo['item_id'], $orderInfo['memo'], $orderInfo['total_price'], $trade_no);
            }
            //火车票
            if ($orderInfo['type'] == 6) {
                import('Vendor.Elife.TrainTickes');
                $TrainTickets = new \TrainTickes();
                $dataObj = $TrainTickets->payTrainTickes($orderInfo['memo']);
                if ($dataObj <= 0) {
                    $status = 5;
                }
            }

            //消费订单
            $res = 1;

        }

        if ($res) {
            //余额表添加成功
            if ($status > 0) {
                $data2['order_status'] = $status;//4:充值中 0:待支付 5失败
            } else {
                $data2['order_status'] = 3;
                $data2['is_success'] = 1;
            }


            $data2['pay_time'] = date("Y-m-d H:i:s");
            $res2 = M('DealOrder')->where(" id = '{$orderInfo['id']}' ")->save($data2);

            if ($res2) {
                //更新外部订单表
                $data3['outer_notice_sn'] = $out_trade_no;


                if ($status > 0) {
//                    $data3['is_paid'] = $status;//4:充值中 0:待支付
                } else {
                    $data3['is_paid'] = 1;

                    //更新账户日志
                    $result = save_log($orderInfo['type'], $orderInfo);
                }
                $data3['pay_time'] = date("Y-m-d H:i:s");
                $res3 = M("PaymentNotice")->where("order_id = '{$orderInfo['id']}'")->save($data3);

                if ($res3 || $result) {
                    $Account->commit();
                    if ($status > 0) {
                        return $res3;
                    } else {
                        return $result;
                    }
                } else {
                    $Account->rollback();
                }
            } else {
                $Account->rollback();
            }
        } else {
            //余额变更失败
            $Account->rollback();
        }
    }

}

/**
 *账户金额回滚 ,订单失败
 * @param $notice_sn 本平台订单号
 *
 * */
function roll_back_order($notice_sn)
{
    $id = M('PaymentNotice')->where('notice_sn = ' . $notice_sn)->getField('order_id');
    //获取订单详情
    $order_info = M('DealOrder')->where('id = ' . $id . ' and is_refund = 0')->find();
    if ($order_info) {
        M("UserAccount")->startTrans();
        //更新零钱余额表 进行账户加操作
        $whereStr['u_id'] = $order_info['user_id'];
        $account = M("UserAccount")->where($whereStr)->getField("account");
        $accountData['account'] = $account + $order_info['total_price'];
        $accountRes = M("UserAccount")->where($whereStr)->save($accountData);

        //修改退款状态
        $orderData['is_refund'] = 1;
        $orderRes = M('DealOrder')->where('id = ' . $id)->save($orderData);
        if ($accountRes && $orderRes) {
            $logRes = save_log($order_info['type'], $order_info, 1);
            if ($logRes) {
                M("UserAccount")->commit();
            } else {
                M("UserAccount")->rollback();
            }
        } else {
            M("UserAccount")->rollback();
        }

    }
}


/**
 * 保存账户日志
 * @param $type int 类型 0表示账户零钱充值 1学生卡余额充值 2话费充值 3提现
 * @param $Info array 订单详情(充值消费订单及提现订单)
 * */
function save_log($type, $Info, $rollBack = 0)
{
    $data['log_info'] = $Info['deal_name'];
    $data['log_time'] = date("Y-m-d H:i:s");
    $data['user_id'] = $Info['user_id'];
    if ($type == 0) {
        //零钱充值
        $data['money'] = $Info['total_price'];
    } elseif ($type == 1) {
        //学生卡充值
        $data['money'] = -$Info['total_price'];
    } elseif ($type == 2) {
        //话费充值
        if ($rollBack > 0) {
            $data['money'] = $Info['total_price'];
            $data['log_info'] = $Info['deal_name'] . '退款';
        } else {
            $data['money'] = -$Info['total_price'];
        }

    } elseif ($type == 3) {
        $data['log_info'] = "用户提现";
        $data['money'] = -$Info['money'];
    }

    $data['type'] = $type;
    $data['order_id'] = $Info['id'];
    if (M("UserBill")->create($data)) {
        $res = M("UserBill")->add($data);
    }
    return $res;


}

//获取 系统管理员/省市县管理员名下的学校id
function getSchoolId($arr)
{
    $userInfo = session('admin_user');
    $u_id = $userInfo['user_id'];
    $type = $userInfo['type'];
    $whereArr = array();
    if ($type && $type != 2) { // 省市县管理
        if ($u_id) {
            $userRegion = M("RegionUser")->field("prov_id,city_id,county_id")->where("u_id = '{$u_id}'")->find();
            $regionProv = explode(',', $userRegion['prov_id']);
            $regionCity = explode(',', $userRegion['city_id']);
            $regionCounty = explode(',', $userRegion['county_id']);
        } else {
            $regionProv = array();
            $regionCity = array();
            $regionCounty = array();
        }
        if ($type == 5) {//省委
            $whereArr['prov_id'] = array("in", $regionProv);
        } elseif ($type == 6) {//市委
            $whereArr['city_id'] = array("in", $regionCity);
        } else {//县委
            $whereArr['county_id'] = array("in", $regionCounty);
        }
    }
    $prov_id = $arr['prov_id'];
    $city_id = $arr['city_id'];
    $county_id = $arr['county_id'];
    if ($prov_id) {
        $whereArr['prov_id'] = $prov_id;
    }
    if ($city_id) {
        $whereArr['city_id'] = $city_id;
    }
    if ($county_id) {
        $whereArr['county_id'] = $county_id;
    }
    $sthInfo = D('SchoolInformation')->where($whereArr)->getField('s_id', true);
    if (!$sthInfo) {
        $sthInfo = '';
    }
    return $sthInfo;
}

//银联调试方法
function printResult($url, $req, $resp)
{
    echo "=============<br>\n";
    echo "地址：" . $url . "<br>\n";
    echo "请求：" . str_replace("\n", "\n<br>", htmlentities(com\unionpay\acp\sdk\createLinkString($req, false, true))) . "<br>\n";
    echo "应答：" . str_replace("\n", "\n<br>", htmlentities(com\unionpay\acp\sdk\createLinkString($resp, false, false))) . "<br>\n";
    echo "=============<br>\n";
}

/**
 * 查询列表
 * @param string $fields 字段
 * @param array $where where条件数组: array('field1'=>'value1','field2'=>'value2')
 * @param array $orderby orderby数组: array('field1'=>'ASC','field2'=>'DESC')
 * @param int $page 页码
 * @param int $pagesize 每页数量
 * @param array $groupby
 * @param array $data_auth 数据权限
 *  * @return uret['count']  总数    $ret['list']  查询结果列表
 */
function queryList($obj, $fields, $where = null, $orderby = null, $page = 0, $pagesize = 0, $groupby = null, $data_auth = null)
{
    if ($page) {
        $fields = 'SQL_CALC_FOUND_ROWS  ' . $fields;
    }
    if ($page) {
        $ret['list'] = $obj->field($fields)->where($where)->order($orderby)->page($page, $pagesize)->group($groupby)->select();
    } else {
        $ret['list'] = $obj->field($fields)->where($where)->order($orderby)->group($groupby)->select();
    }

    // dump(self::getLastSql());

    if ($page == 0) {
        $ret ["count"] = count($ret ["list"]);
    } else {
        $ret ["count"] = getCount($obj); //获得记录总数
    }
    $List = $ret['list'];
    $Count = $ret['count'];
    $arr = array();

    $arr['total_page'] = ceil($Count / $pagesize);
    $arr['total_count'] = $Count;
    $arr['page'] = $page;
    $arr['content'] = $List;
    return $arr;
}

/**
 * 当查询中使用了 SQL_CALC_FOUND_ROWS 时,调用本方法可得到记录总数
 *
 */
function getCount($obj)
{
    $result = $obj->query("select FOUND_ROWS() as count");
    return $result[0]["count"];
}

/**
 * 广告系统 代理商提成公式
 *$banner_info 广告详情
 * */
function getShare($banner_info, $where = '')
{
    $user_info = session("admin_user");
    if ($user_info['type'] == 8) {
        $where['adid'] = $banner_info['id'];

        //判断是否为自己发布
        if ($banner_info['u_id'] == $user_info['user_id']) {
            $show_percent = C('PRIV_SHOW_PERCENT');
            $click_percent = C('PRIV_CLICK_PERCENT');

            $where['type'] = 1;
            //计算图片所有点点击量及展示量
            $show_count = M("StaticsUion")->where($where)->count('media_id');

            $where['type'] = 2;
            $click_count = M("StaticsUion")->where($where)->count('media_id');
        } else {
            $show_percent = C('SHOW_PERCENT');
            $click_percent = C('CLICK_PERCENT');

            $show_count = 0;
            $click_count = 0;
            //查询用户所管理区域
            $provStr = M("RegionUser")->where("u_id = '{$user_info['user_id']}'")->getField("prov_id");
            $userProv = explode(',', $provStr);

            //计算广告在管辖区域下的点击量及展示量
            foreach ($userProv as $key => $value) {//管辖id
                $where['type'] = 1;
                $where['region_id'] = array('like', "%" . $value . "%");
                //计算图片所有点点击量及展示量
                $show_info = M("StaticsUion")->field('media_id,region_id')->where($where)->select();
                $where['type'] = 2;
                $click_info = M("StaticsUion")->field('media_id,region_id')->where($where)->select();
                //去除模糊查询不准确的值
                foreach ($show_info as $k => $v) {
                    $bannerProv = explode(',', $v['region_id']);
                    if (in_array($value, $bannerProv)) {
                        $tmp[] = $v;
                    }
                }
                foreach ($click_info as $k1 => $v1) {
                    $bannerProv2 = explode(',', $v1['region_id']);
                    if (in_array($value, $bannerProv2)) {
                        $tmp2[] = $v1;
                    }
                }
                // $temp 当前区域id下准确的展示
                // $temp2 当前区域id下准确的点击
                //去重
                foreach ($tmp as $v2) {
                    $v3 = implode('##', $v2);
                    $temp[] = $v3;
                }
                foreach ($tmp2 as $v4) {
                    $v5 = implode('##', $v4);
                    $temp2[] = $v5;
                }
                $temp = array_unique($temp);
                $temp2 = array_unique($temp2);

                $show_count += count($temp);

                $click_count += count($temp2);
            }


        }

        //计算到目前为止的总的收益
        //计算总价
        if ($banner_info['type'] == 1) {
            //展示计费
            $now_total_price = ($show_count) * $banner_info['unit_price'];
        } elseif ($banner_info['type'] == 0) {
            //获取展示天数
            $beginTmp = strtotime($banner_info['begin_time']);
            $endTmp = time();
            $dayCount = ceil(($endTmp - $beginTmp) / 86400);
            $now_total_price = $dayCount * $banner_info['unit_price'];
        } else {
            //点击量计费
            $now_total_price = $click_count * $banner_info['unit_price'];
        }

        //计算总的提成金额
        $ret['show_money'] = ($now_total_price * $show_percent);
        $ret['click_money'] = ($now_total_price * $click_percent);
        $ret['share_money'] = ($now_total_price * ($show_percent + $click_percent));

    }

    return $ret;
}

/**
 * curl请求
 * */
function reqURL($url, $data = null, $type = 'POST', $is_json = 0)
{
    $ch = curl_init();  // 初始化一个curl会话
    $header = "Accept-Charset: utf-8";
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    // curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $output = curl_exec($ch); //执行一个cURL会话
    curl_close($ch);//关闭一个cURL会话
    if ($is_json) {
        return $output;
    }
    return json_decode($output, true); //对 JSON 格式的字符串进行编码
}

//添加视频通讯录 BY:Meng Fanmin 2017.06.15
function video_add_user($user_id)
{
//    $url = "http://video.pcuion.com/videoadduser.php";
//    $data = array('u_id' => $user_id);
//    $res = reqURL($url, $data);
//    return $res;
}

//添加视频通讯录 BY:Meng Fanmin 2017.06.15
function easemob_add_user($user_id="",$imeiID="")
{
    $orgName = '1152171201178499';
    $appName = 'shuhai-anfang';
    if($user_id){
        $where['user_id'] = $user_id;
        $userList = M('User', 'think_')->where($where)->field('user_id ,name')->find();
        if (!$userList) {
            return false;
        }
        $nickname = $userList['name'];
    }elseif ($imeiID){
        $where['imei'] = $imeiID;
        $deviceList = D('device')->where($where)->field('imei ,phone')->find();
        if (!$deviceList) {
            return false;
        }
        $nickname = $deviceList['imei'];
    }
    $data = array(
        'username' => $user_id?$user_id:$imeiID,
        'password' => md5('SHUHAIXINXI' . ($user_id?$user_id:$imeiID)),
        'nickname' => $nickname,
    );
    $jsonStr = json_encode($data);
    $url = 'https://a1.easemob.com/' . $orgName . '/' . $appName . '/users';
    $result = curl_ease($url, $jsonStr, 'POST');
    return ($result);
}

function curl_ease($url, $data, $type)
{
    $ch = curl_init();  // 初始化一个curl会话
    $header[] = "Accept-Charset:utf-8";
    $header[] = "Content-Type:application/json";
//    $header[] = "Authorization:Bearer {$this->getToken()}";
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    // curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $output = curl_exec($ch); //执行一个cURL会话
    curl_close($ch);//关闭一个cURL会话

    return json_decode($output, true);
}

//发送短信息
function sendSms($phone, $msg)
{
    vendor('ChuanglanSmsHelper.ChuanglanSmsApi');
    $clapi = new \ChuanglanSmsApi();
    $result = $clapi->sendSMS($phone, $msg, 'true');
    $result = $clapi->execResult($result);
    if ($result[1] == 0) {
        \Think\Log::write('短信发送成功', 'WARN');
        return true;
    } else {
        \Think\Log::write('短信发送失败,电话号码:' . $phone, 'WARN');
        return false;
    }
}

/**
 * 发送物联网卡短信息
 * 物联网短信通道只允许发送英文、数字及标点符号，不支持中文字符，短信长度70字以内计费1条，超过70字按每67个字计费1条
 * */
function sendIOTMsg($phone, $msg)
{
    import('Vendor.IOTSms.IOTSms');
    $SmsObj = new IOTSms();
    $res = $SmsObj->sendIOTMsg($phone, $msg);
    $status = substr($res, 0, 1);
    if ($status != 0) {
        $status2 = 2;//失败
        $returnMsg = false;
    } else {
        $status2 = 1;//成功
        $returnMsg = true;
    }
    saveMsgLog($phone, $msg, $type = 2, $status2);
    return $returnMsg;
}


/**
 * 下发APP公告
 * */
function sendAppMsg($pushArr)
{
    set_time_limit(0);
    import('Vendor.MsgPush.Push');
    //app消息推送 By:Meng Fanmin 2017.06.22
    $niticeArr = explode(',', $pushArr['fsdx']);//0教师 1家长
    //消息内容
    $alert = array(
        'ticker' => $pushArr['ticker'],
        'title' => $pushArr['title'],
        'text' => $pushArr['text'],
        'after_open' => $pushArr['after_open'],
        'activity' => $pushArr['activity'],
        'id' => $pushArr['id'],
    );
    if (!is_array($pushArr['u_id'])) {
        $pushArr['u_id'] = array($pushArr['u_id']);
    }
    $whereStr['user_id'] = array('in', $pushArr['u_id']);
    $whereStr['system_model'] = 1;
    $androidToken = M('MsgpushToken')->field('device_token,push_name')->where($whereStr)->select();
    $whereStr['system_model'] = 0;
    $iosToken = M('MsgpushToken')->where($whereStr)->getField('device_token', true);
    if ($niticeArr[0]) {//通知教师
        //获取教师手机device_token
        if ($androidToken) {
            //Umeng
            foreach ($androidToken as $key => $value) {
                if ($value['push_name'] == 'MIPush') {
                    $tokenArrMIT[] = $value['device_token'];
                } else if ($value['push_name'] == 'HWPush') {
                    $tokenArrHWT[] = $value['device_token'];
                } else if ($value['push_name'] == 'MZPush') {
                    $tokenArrMZT[] = $value['device_token'];
                } else {
                    $tokenArrUMT[] = $value['device_token'];
                }
            }
            if ($tokenArrMIT) {

                $pushObj = new \Push('MIPush');
                $tokenArr = $tokenArrMIT;
                $pushObj->sendAndroidPushT($tokenArr, $alert);
            }
            if ($tokenArrHWT) {
                $pushObj = new \Push('HWPush');
                $tokenArr = $tokenArrHWT;
                $alert['user_type'] = 3;//华为推送判定
                $pushObj->sendAndroidPushT($tokenArr, $alert);
            }
            if ($tokenArrUMT) {
                $pushObj = new \Push('UPush');
                $tokenArr = $tokenArrUMT;
                $pushObj->sendAndroidPushT($tokenArr, $alert);
            }
            if ($tokenArrMZT) {
                $pushObj = new \Push('MZPush');
                $tokenArr = $tokenArrMZT;
                $pushObj->sendAndroidPushT($tokenArr, $alert);
            }
        }
        if ($iosToken) {
            $pushObj = new \Push('UPush');
            $tokenArr = $iosToken;
            $pushObj->sendIosPushT($tokenArr, $alert);
        }

    }
    if ($niticeArr[1]) {//通知家长
        //获取家长手机device_token
        if ($androidToken) {
            //Umeng
            foreach ($androidToken as $key => $value) {
                if ($value['push_name'] == 'MIPush') {
                    $tokenArrMI[] = $value['device_token'];
                } elseif ($value['push_name'] == 'HWPush') {
                    $tokenArrHW[] = $value['device_token'];
                } elseif ($value['push_name'] == 'MZPush') {
                    $tokenArrMZ[] = $value['device_token'];
                } else {
                    $tokenArrUM[] = $value['device_token'];
                }
            }

            if ($tokenArrMI) {
                $pushObj = new \Push('MIPush');
                $tokenArr = $tokenArrMI;
                $pushObj->sendAndroidPushP($tokenArr, $alert);
            }
            if ($tokenArrHW) {
                $pushObj = new \Push('HWPush');
                $tokenArr = $tokenArrHW;
                $pushObj->sendAndroidPushP($tokenArr, $alert);
            }
            if ($tokenArrUM) {
                $pushObj = new \Push('UPush');
                $tokenArr = $tokenArrUM;
                $pushObj->sendAndroidPushP($tokenArr, $alert);
            }
            if ($tokenArrMZ) {
                $pushObj = new \Push('MZPush');
                $tokenArr = $tokenArrMZ;
                $pushObj->sendAndroidPushP($tokenArr, $alert);
            }
        }
        if ($iosToken) {
            $pushObj = new \Push('UPush');
            $tokenArr = $iosToken;
            $pushObj->sendIosPushP($tokenArr, $alert);
        }
    }
    if ($niticeArr[2]) {
        if ($androidToken) {
            //Umeng
            foreach ($androidToken as $key => $value) {
                if ($value['push_name'] == 'MIPush') {
                    $tokenArrMI[] = $value['device_token'];
                } elseif ($value['push_name'] == 'HWPush') {
                    $tokenArrHW[] = $value['device_token'];
                } elseif ($value['push_name'] == 'MZPush') {
                    $tokenArrMZ[] = $value['device_token'];
                } else {
                    $tokenArrUM[] = $value['device_token'];
                }
            }

            if ($tokenArrMI) {
                $pushObj = new \Push('MIPush');
                $tokenArr = $tokenArrMI;
                $pushObj->sendAndroidPushP($tokenArr, $alert);
            }
            if ($tokenArrHW) {
                $pushObj = new \Push('HWPush');
                $tokenArr = $tokenArrHW;
                $pushObj->sendAndroidPushP($tokenArr, $alert);
            }
            if ($tokenArrUM) {
                $pushObj = new \Push('UPush');
                $tokenArr = $tokenArrUM;
                $pushObj->sendAndroidPushP($tokenArr, $alert);
            }
            if ($tokenArrMZ) {
                $pushObj = new \Push('MZPush');
                $tokenArr = $tokenArrMZ;
                $pushObj->sendAndroidPushP($tokenArr, $alert);
            }
        }
        if ($iosToken) {
            $pushObj = new \Push('UPush');
            $tokenArr = $iosToken;

            $pushObj->sendIosPushP($tokenArr, $alert);
        }
    }
}

/**
 * 保存消息日志
 * @param $phone 手机号
 * @param $msg 内容
 * @param $type 发送类型1普通卡发送  2物联网卡发送
 * @param $status 状态 0未发送 1已发送 2发送失败
 * */
function saveMsgLog($phone, $msg, $type = 1, $status = 1)
{
    $data['stu_phone'] = $phone;
    $data['order'] = $msg;
    $data['status'] = $status;
    $data['create_time'] = date('Y-m-d H:i:s');
    $data['msg_type'] = $type;

    M('StuCardCommand')->create($data);
    $res = M('StuCardCommand')->add($data);
    if ($res) {
        return true;
    } else {
        return false;
    }
}

//短信验证码
function random($length = 6, $numeric = 0)
{
    PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
    if ($numeric) {
        $hash = sprintf('%0' . $length . 'd', mt_rand(0, pow(10, $length) - 1));
    } else {
        $hash = '';
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
    }
    return $hash;
}

/**
 * 获取监护人user_id
 * @param $c_id int 班级id
 * @param return array 监护人userid 数组
 * */
function getParentId($c_id)
{
    $tmp_arr = M('Student')->alias('a')
        ->field('c.u_id')
        ->join(' __STUDENT_GROUP_ACCESS__ b on a.stu_id = b.stu_id ')
        ->join(' __STUDENT_PARENT__ c on b.sp_id = c.sp_id ')
        ->where('a.c_id=' . $c_id)
        ->select();
    $pid_arr = array();
    foreach ($tmp_arr as $key => $value) {
        $pid_arr[] = $value['u_id'];
    }
    return $pid_arr;
}

/**
 * 获取城市代码ID
 * @param $cityName string 城市名
 *
 * */
function getCityId($cityName)
{
    //检测数据库中的城市代码
    $cityid = M("SchoolWeather")->where("city = '{$cityName}'")->getField('cityid');
    if ($cityid > 0) {
        $result[0] = $cityid;
    } else {
        $url = 'http://toy1.weather.com.cn/search?cityname=' . urlencode($cityName) . '&callback=jsonp' . time() . mt_rand(100, 999) . '&_=' . time() . mt_rand(100, 999);
        $result = explode('~', substr(strtolower(curl_city($url)), 28, -4));
    }

    return $result[0];
}

/**
 * 天气预报curl请求
 * */
function curl_city($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_REFERER, 'http://www.weather.com.cn/forecast/index.shtml');//必须滴
    curl_setopt($ch, CURLOPT_COOKIE, 'isexist=1');//最好带上 比较稳定
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:25.0) Gecko/20100101 Firefox/25.0');
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

/**
 * 获取当天天气
 * @param $cityName string 城市名
 * */
function getNowWeather($cityName)
{
    $cityId = getCityId($cityName);
//    $url = "http://www.weather.com.cn/data/cityinfo/{$cityId}.html";
    $url = 'http://wthrcdn.etouch.cn/weather_mini?citykey=' . $cityId;
    $info = json_decode(gzdecode(curl_city($url, '', 'GET')), true);
    $info['data']['cityid'] = $cityId;
    return $info['data'];
}

/**
 * utf8转小端Unicode
 * */
function utf8_unicode($name, $is_rev = 0)
{
    $name = iconv('UTF-8', 'UCS-2', $name);
    $len = strlen($name);
    $str = '';
    for ($i = 0; $i < $len - 1; $i = $i + 2) {
        $c = $name[$i];
        $c2 = $name[$i + 1];
        if (ord($c) > 0) {   //两个字节的文字
            $str .= '\u' . base_convert(ord($c), 10, 16) . str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
            //$str .= base_convert(ord($c), 10, 16).str_pad(base_convert(ord($c2), 10, 16), 2, 0, STR_PAD_LEFT);
        } else {
            $str .= '\u' . str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
            //$str .= str_pad(base_convert(ord($c2), 10, 16), 4, 0, STR_PAD_LEFT);
        }
    }

    $tmp = trim($str, '\\');
    $tmpArr = explode('\\', $tmp);
    $tmp = array();

    foreach ($tmpArr as $k => $v) {
        $tmpStr = trim($v, 'u');
        if ($is_rev) {
            //字串反转 获取前两个字符
            $str1 = substr($tmpStr, 0, 2);

            $str2 = substr($tmpStr, -2);
            $tmp[] = $str2 . $str1;
        } else {
            $tmp[] = $tmpStr;
        }
    }

    $strtrue = implode('', $tmp);
    return $strtrue;
}

function Dispose($url, $post_data)
{
    //get请求
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    // post的变量
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

/*
 * 转unicode编码
 * */
function unicode_encode($str, $encoding = 'utf-8', $prefix = '&#', $postfix = ';')
{
    //将字符串拆分
    $str = iconv("UTF-8", "gb2312", $str);
    $cind = 0;
    $arr_cont = array();

    for ($i = 0; $i < strlen($str); $i++) {
        if (strlen(substr($str, $cind, 1)) > 0) {
            if (ord(substr($str, $cind, 1)) < 0xA1) { //如果为英文则取1个字节
                array_push($arr_cont, substr($str, $cind, 1));
                $cind++;
            } else {
                array_push($arr_cont, substr($str, $cind, 2));
                $cind += 2;
            }
        }
    }
    foreach ($arr_cont as &$row) {
        $row = iconv("gb2312", "UTF-8", $row);
    }
    $unicodestr = '';
    //转换Unicode码
    foreach ($arr_cont as $key => $value) {
        $unicodestr .= $prefix . base_convert(bin2hex(iconv('utf-8', 'UCS-4', $value)), 16, 10) . $postfix;
    }

    return $unicodestr;
}

/**
 * js escape phputf8编码 实现
 * @param $string           the sting want to be escaped
 * @param $in_encoding
 * @param $out_encoding
 */
function escapeutf8($string, $in_encoding = 'UTF-8', $out_encoding = 'UCS-2')
{
    $return = '';
    if (function_exists('mb_get_info')) {
        for ($x = 0; $x < mb_strlen($string, $in_encoding); $x++) {
            $str = mb_substr($string, $x, 1, $in_encoding);

            if (strlen($str) > 1) { // 多字节字符
                $return .= '' . strtoupper(bin2hex(mb_convert_encoding($str, $out_encoding, $in_encoding))) . ';';
            } else {
                $return .= $str;
            }
        }
    }
    return $return;
}

//轨迹回放

function TraceReplay($res, $state, $res1, $start, $end)
{
//    $count = ceil(count($res) / 100); //整除100
//    $array1 = array();
//    for ($x = 0; $x < $count; $x++) {
//        $array1[] = array_splice($res, 0, 100);
//
//    }
    $array1 = array_chunk($res, 100);

    $arrlist = array();
    $value1 = array();
    $ak = 'pObgXUnySvcX39VAqroiOt6xqUrNoOzK'; //ak 值
    $service_id = '146714'; // service_id 值
    foreach ($array1 as $key => $value) {
        foreach ($value as $k => $v) {
            $starttime = strtotime($v['create_time']);
            $value1['entity_name'] = $res1['imei_id'];
            $value1['latitude'] = $v['latitude'];
            $value1['longitude'] = $v['longitude'];
            $value1['loc_time'] = $starttime;
            $value1['coord_type_input'] = "wgs84";
            $value1['imei'] = $res1['imei_id'];
            $value1['sex'] = $res1['sex'];
            if ($v['mcc'] > 0) {
                $value1['type'] = '1';
            } else {
                $value1['type'] = '0';
            }
//                 $value1['radius'] = 3;
            $arrlist[] = $value1;
        }

        $arrlist = json_encode($arrlist);
        $url = "http://yingyan.baidu.com/api/v3/track/addpoints";
        $post_data = array(
            "ak" => $ak,
            "service_id" => $service_id,
            "point_list" => $arrlist,
            // "object_name"=>'xiaoming',// 自定义字段
        );
        $arrlist = array();
        $value1 = array();
        $name = Dispose($url, $post_data);
        $arr1 = json_decode($name, true);
        if ($arr1['message'] != "成功") {
            return $arr1['message'];
        } else {
        }

    }
    $strtime = strtotime($start);//开始时间
    $endtime = strtotime($end);//结束时间
    $url = "http://yingyan.baidu.com/api/v3/track/gettrack?ak=" . $ak . "&service_id=" . $service_id . "&entity_name=" . $res1['imei_id'] . "&start_time=" . $strtime . "&end_time=" . $endtime . "&is_processed=1&process_option=need_denoise=1,radius_threshold=0,need_vacuate=1,need_mapmatch=" . $state . ",radius_threhold=0,transport_mode=riding&page_size=5000";
    $arr = reqURL($url, '', "GET", 0);
    $GPS_TYPE = C('TYPE');
    $arrlist1 = array();
    foreach ($arr['points'] as $key => $value) {
        $arrlist1[$key]['create_time'] = date("Y-m-d H:i:s", $value['loc_time']);
        $arrlist1[$key]['latitude'] = $value['latitude'];
        $arrlist1[$key]['longitude'] = $value['longitude'];
        $arrlist1[$key]['imei'] = $value['imei'];
        $arrlist1[$key]['gps_type'] = $GPS_TYPE[$value['type']] ? $GPS_TYPE[$value['type']] : 'GPS定位';
        $arrlist1[$key]['sex'] = $res1['sex'];
    }
    if ($arr['message'] != "成功") {
        return $arr['message'];// 起始和结束时间必须在24小时之内
    }
    return $arrlist1;
}

//手机号码归属地 $mobile  手机号拍拍网
function getMobileArea2($mobile)
{
    if (!S('operator:' . $mobile)) {
        $resStr = reqURL('https://www.baifubao.com/callback?cmd=1059&callback=phone&phone=' . $mobile, '', 'GET', '1');
        $res = trim(trim($resStr, '/*fgg_again*/phone('), ')');
        $data = json_decode($res, true);
        $area = $data['data']['area_operator'];
        if (!$area) {
            $area = '物联网';
        }
        S('operator:' . $mobile, $area);
    }

    return S('operator:' . $mobile);
}

/**
 * 获取lbs经纬度信息
 * */
function getLongtiLati($arr = array())
{
    $arr['time'] = date('Y-m-d H:i:s');
    $logStr = json_encode($arr) . "\n";
    file_put_contents('lbs2gps.txt', $logStr, FILE_APPEND);
    $url = "http://v.juhe.cn/cell/get";
    // 	移动基站：0 联通基站:1   聚合->移动基站：0     联通基站:1 默认:0
    if ($arr['mnc'] == '00' || $arr['mnc'] == '0') {//联通定位
        $mnc = 1;
    } else {
        $mnc = 0;
    }
    $data = "mnc=$mnc&lac=" . $arr['lac'] . "&cell=" . $arr['cid'] . "&hex=&dtype=&callback=&key=5347083b74940637bc995086397029b0";
    $res = reqURL($url, $data, "POST");

    if ($res['resultcode'] == 200) {
        $place['stats'] = 1;
        $place['longti'] = $res['result']['data'][0]['LNG'];
        $place['lati'] = $res['result']['data'][0]['LAT'];
        $place['addr'] = $res['result']['data'][0]['ADDRESS'];
    } else {
        $place['stats'] = 0;
        $place['reason'] = '位置接口调用失败:' . $res['reason'];
    }

    return $place;
}

/**
 * 获取县级城市对应的region_code
 * @param $county_id ,拼接的字串 383,382,839,1727,1875
 * */
function get_region_code($county_id = '383,382,839,1727,1875')
{
    if ($county_id) {
        $region = M('AreaRegion')
            ->where("region_id in ({$county_id})")
            ->getField('region_code', true);
        if ($region) {
            $region = implode(',', $region);
        }
        return $region;
    } else {
        return false;
    }
}

/**
 * GPS坐标转百度坐标
 * @param $str 经度,纬度拼接
 * */
function gps2baidu($str)
{
    if (!S($str)) {
        $url = 'http://api.map.baidu.com/geoconv/v1/?ak=pObgXUnySvcX39VAqroiOt6xqUrNoOzK&coords=' . $str;
        $info = reqURL($url);
        S($str, $info['result'][0]);
    }
    return S($str);
}

/**
 * 判断点是否在围栏里
 * @param $pointStr GPS坐标点 经度,纬度 拼接
 * @param $mapStr 百度围栏坐标点 经度,纬度,经度,纬度,经度,纬度 拼接
 * @return bool
 * */
function is_inside($pointStr, $mapStr)
{
    $cacheStr = md5($pointStr . $mapStr);
    if (!S($cacheStr)) {
        //GPS转换成百度坐标点
        $pointArr = gps2baidu($pointStr);
        $point = ["lng" => $pointArr['x'], "lat" => $pointArr['y']];
        //$point = ["lng" => '116.292384', "lat" => '39.8412'];
        //获取围栏数组 [["lng" => 116.291284, "lat" => 39.841131],["lng" => 116.291284, "lat" => 39.841131]]
        $arr = explode(',', $mapStr);
        $tmpArr = array_chunk($arr, 2);
        $mapArr = array();
        $tmp = array();
        foreach ($tmpArr as $k => $v) {
            $tmp['lng'] = $v[0];
            $tmp['lat'] = $v[1];
            $mapArr[] = $tmp;
        }

        import('Vendor.Arithmetic.ValidationMap');
        //生成百度围栏
        \validationMap::setCoordArray($mapArr);

        //进行验证的区域
        $result = \validationMap::isCityCenter($point);
        if ($result) {
            $result = 1;
        } else {
            $result = 2;
        }
        S($cacheStr, $result);
    }
    return S($cacheStr);
}

//原始坐标转gps算法
function origin2gps($lng, $lat)
{
    if (!$lng || !$lat) {
        return false;
    }
    $lngD = substr($lng, 0, 3);
    $latD = substr($lat, 0, 2);
    $lngF = substr($lng, 3, 10) * 1000000 / 60 / 1000000;
    $latF = substr($lat, 2, 10) * 1000000 / 60 / 1000000;
    $arr = array(
        $lngD + $lngF,
        $latD + $latF,
    );
    return ($arr);
}

//基站信息转gps信息
function lbs2gps($lbsdata)
{
    $key = implode('', $lbsdata);
    if (!S('lbs:' . $key)) {
        if (!$lbsdata['lac'] || !$lbsdata['cid']) {
            return ('param error!');
        }
        $gpsData = M('Lbs2gps')->cache(true)->field('longti,lati,addr')->where($lbsdata)->find();
        //未取到经纬度数据
        if (!$gpsData) {
            //判断是否已经查询过数据接口
            $place_status = M('Trail')
                ->where("mcc = '{$lbsdata['mcc']}' and lac = '{$lbsdata['lac']}' and cid = '{$lbsdata['cid']}' and type = 2")
                ->getField('type');
            //之前未获取定位信息
            if ($place_status) {
                $place['stats'] = 0;
                $place['reason'] = '未获取定位信息';
            } else {
                //调用lbs基站接口
                $place = getLongtiLati($lbsdata);
            }

            //未获取位置信息
            if ($place['stats'] == 0 || $place['longti'] == '') {
                $data['type'] = 2;//未获取位置
            } else {
                //获取位置信息
                //将位置信息存入lbs2gps
                $gpsData['mnc'] = $lbsdata['mnc'];
                $gpsData['mcc'] = 460;
                $gpsData['lac'] = $lbsdata['lac'];
                $gpsData['cid'] = $lbsdata['cid'];
                $gpsData['longti'] = $place['longti'];
                $gpsData['lati'] = $place['lati'];
                $gpsData['addr'] = $place['addr'];
                M('Lbs2gps')->add($gpsData);

                $data[0] = $gpsData['longti'];
                $data[1] = $gpsData['lati'];
            }

        } else {
            //在位置库中存在经纬度
            $data[0] = $gpsData['longti'];
            $data[1] = $gpsData['lati'];
        }
        S('lbs:' . $key, $data);
    }
    return S('lbs:' . $key);
}

//根据imei获取学生信息及关联父母ID
function get_stu_info($imei)
{
    if (!$imei) {
        return false;
    }
    //定义缓存名称 stuinfo:imei号
    if (!S('stuinfo:' . $imei)) {
        //获取学生详情
        $student = M('Student')
            ->field('stu_id,s_id,a_id,g_id,c_id,stu_no,stu_name,imei_id,sex,stu_phone')
            ->where("imei_id = '$imei'")
            ->find();

        if ($student) {
            //获取家长信息
            $parent = M('StudentGroupAccess')
                ->where("stu_id = '{$student['stu_id']}'")
                ->getField('user_id', true);
            $student['parent_id'] = $parent;
            $stu_info = $student;
        } else {
            return false;
        }
        //设置缓存
        S('stuinfo:' . $imei, $stu_info);
    }
    return S('stuinfo:' . $imei);
}

/**
 * 坐标位置转换
 * 1: lbs转gps  2: 原始坐标转gps
 * @param $info array 位置详情
 * @param $type int    1:lbs  3:原始坐标
 *
 * */
function get_gps_point($info, $type)
{
    if ($type == 3) {//原始坐标
        $point = origin2gps($info[0], $info[1]);
    } elseif ($type == 1) {//lbs坐标
        $point = lbs2gps($info);
    }
    if (!$point || ($point['type'] == 2)) {
        return false;
    }
    $point['point_type'] = $type;
    return $point;
}

/**
 *获取redis实例
 * */
function redis_instance($host = '127.0.0.1', $port = '6379', $pwd = 'School1502')
{
    $redis = new \Redis();
    $redis->connect($host, $port);
    $redis->auth($pwd);

    return $redis;
}

function taskRedis($imei_id, $order)
{
    $redis = redis_instance($host = '39.107.98.114', $port = '6379', $pwd = 'School1502');
    $redis->sAdd("task:$imei_id", $order);
    unset($redis);
    return true;
}

/**
 * 根据stu_id 获取定位表 分表id
 * @param $stu_id
 * */
function get_trail_id($imei)
{
    if (!$imei) {
        return false;
    }
    $id = intval(substr($imei, -2));
    return $id;
}

/**
 * 验证身份证号
 * @param $vStr
 * @return bool
 */
function isCreditNo($vStr)
{
    $vCity = array(
        '11', '12', '13', '14', '15', '21', '22',
        '23', '31', '32', '33', '34', '35', '36',
        '37', '41', '42', '43', '44', '45', '46',
        '50', '51', '52', '53', '54', '61', '62',
        '63', '64', '65', '71', '81', '82', '91'
    );

    if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)) return false;

    if (!in_array(substr($vStr, 0, 2), $vCity)) return false;

    $vStr = preg_replace('/[xX]$/i', 'a', $vStr);
    $vLength = strlen($vStr);

    if ($vLength == 18) {
        $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
    } else {
        $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
    }

    if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday) return false;
    if ($vLength == 18) {
        $vSum = 0;

        for ($i = 17; $i >= 0; $i--) {
            $vSubStr = substr($vStr, 17 - $i, 1);
            $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr, 11));
        }

        if ($vSum % 11 != 1) return false;
    }

    return true;
}

/**
 * 验证学号
 * @param $vStr
 * @return bool
 */
function check_stu_no($stu_no, $stu_id, $c_id)
{
    if (!$stu_no || !$c_id) {
        return false;
    }
    if ($stu_id > 0) {//编辑
        $is_exist = M('Student')->where("stu_id != '{$stu_id}' and c_id = '{$c_id}' and stu_no = '{$stu_no}' ")->getField('stu_id');
    } else {
        $is_exist = M('Student')->where("c_id = '{$c_id}' and stu_no = '{$stu_no}' ")->getField('stu_id');
    }
    if ($is_exist) {
        return false;
    } else {
        return true;
    }

}

/*
 * 判断手机号函数
 * */
function is_mobile($text)
{
    $search = '/^0?1[3|4|5|6|7|8][0-9]\d{8}$/';
    if (preg_match($search, $text)) {
        return (true);
    } else {
        return (false);
    }
}

/*
 * 获取/生成二维码
 * */
function getQrCode($content, $fileName)
{
    $fileName = 'Public/Uploads/qrcode/' . $fileName;
    $is_exist = is_file($fileName);
    if ($is_exist) {
        return $fileName;
    }
    import('Vendor.phpqrcode.phpqrcode');

    $errorCorrectionLevel = 'L';    //容错级别
    $matrixPointSize = 9;           //生成图片大小

    //生成二维码图片
    \QRcode::png($content, $fileName, $errorCorrectionLevel, $matrixPointSize, 2);

    $QR = $fileName;                //已经生成的原始二维码图片文件


    $QR = imagecreatefromstring(file_get_contents($QR));

    //输出图片
    imagepng($QR, $fileName);
    imagedestroy($QR);
    return $fileName;
}

//获取分页数据  统一分页返回风格
function getPageList($list, $count, $page = 1, $pageSize = 20, $is_PC = 0)
{
    if ($is_PC == 1) {//PC端
        $arr['total_page'] = ceil($count / $pageSize);
        $arr['count'] = $count;
        $arr['page'] = $page;
        $arr['list'] = $list;
    } else {
        $arr['total_page'] = ceil($count / $pageSize);
        $arr['total_count'] = $count;
        $arr['page'] = $page;
        $arr['content'] = $list;
    }
    return $arr;
}

//获取字串16进制长度
function get_len_16($str)
{
    $str_len = strlen($str);//原始字串长度
    $str_len_16 = base_convert($str_len, 10, 16);
    if (strlen($str_len_16) < 4) {
        $str_len_16 = str_pad($str_len_16, 4, '0', STR_PAD_LEFT);
    }
    return $str_len_16;
}

//平台命令存储
function save_command($imei, $d_type, $d_value)
{
    // 1.对返回值进行初始化 retInfo['flag']=0/1(0代表失败,1代表成功) retInfo['message']="存放报错内容",初始化flag = 1 message=>success
    $retInfo['flag'] = 1;
    $retInfo['message'] = "success:";

    // 2.从student表中查找参数imei所对应的数据
    $stuData = M('Student')->field('stu_id,s_id,a_id,g_id,c_id,stu_no,stu_name,imei_id,sex,stu_phone,devicetype')->where(['imei_id' => $imei])->find();
    if (empty($stuData)) {
        $retInfo['flag'] = 0;
        $retInfo['message'] = "failed:the message of " . $imei . " from table named student is null";
        return $retInfo;
    }
//     if (empty($stuData['stu_phone'])) {
//         $retInfo['flag'] = 0;
//         $retInfo['message'] = "failed:the message of " . $imei . " from table named student where phone is null";
//         return $retInfo;
//     }
    //3 根据相应的类型做判断处理:学生卡 手表
    //给commond表插入的部分数据
    $CommData['d_type'] = $d_type;
    $CommData['status'] = 0;
    $CommData['stu_phone'] = $stuData['stu_phone'];
    $CommData['create_time'] = date("Y-m-d H:i:s");
    $CommData['imei'] = $imei;
    $CommData['msg_type'] = 1;
    //给set表准备数据
    $SetData['a_id'] = $stuData['a_id'];
    $SetData['s_id'] = $stuData['s_id'];
    $SetData['g_id'] = $stuData['g_id'];
    $SetData['c_id'] = $stuData['c_id'];
    $SetData['stu_id'] = $stuData['stu_id'];
    $SetData['imei'] = $imei;
    $SetData['d_type'] = $d_type;
    $SetData['d_value'] = $d_value;
    $SetData['status'] = 0;
    $SetData['create_time'] = date("Y-m-d H:i:s");
    //判断类型及处理相应功能
    $devicetype = $stuData['devicetype'];
    if ($devicetype == 1) {//1为学生卡
        switch ($d_type) {
            case 'whitelist': // 白名单
                $whiteInfo = explode(',', $d_value);
                $phone = '';
                foreach ($whiteInfo as $key => $value) {
                    $valInfo = explode(':', $value);//0姓名 1电话
                    $k = $key + 1;
                    $phone = $phone . $k . ',' . $valInfo[1] . ',' . ',';
                }
                $phone1 = rtrim($phone, ',');
                //存入StuCardCommand表中
                $CommData['order'] = '*HQ,' . $imei . ',WLALL,' . date('His') . ',' . $phone1 . ',#';
                break;
            case 'soundsize': // 喇叭音量
                $CommData['order'] = '*HQ,' . $imei . ',SPEAKER,' . date('His') . ',' . $d_value . '#';
                break;

            case 'tealthtime': // 隐身时间(上课时间段)
                $timeInfo = explode('|', $d_value);
                $tInfo = explode(',', str_replace(":", "", str_replace("-", "", $timeInfo[0])));
                $wInfo = explode(',', $timeInfo[1]);
                $wInfo1 = "";
                $tInfo1 = "";
                foreach ($wInfo as $key => $value) {
                    $k = $key + 1;
                    $val = 0;
                    if ($value == "ON") {
                        $val = 1;
                    }
                    $wInfo1 = $wInfo1 . $k . $val;
                }
                foreach ($tInfo as $key => $value) {
                    $tInfo1 = $tInfo1 . $value . $wInfo1 . ',';
                }
                $tealthInfo = rtrim($tInfo1, ',');
                //存入StuCardCommand表中
                $CommData['order']['order'] = '*HQ,' . $imei . ',CLASS,' . date('His') . ',' . $tealthInfo . '#';
                break;

            case 'working': // 工作模式
                $workInfo = explode(',', $d_value);
                //存入StuCardCommand表中
                $CommData['order'] = '*HQ,' . $imei . ',MODE,' . date('His') . ',' . $workInfo[0] . ',' . $workInfo[1] . '#';
                break;

            case 'monitor': // 监听号码
                $monitorInfo = explode(',', $d_value);
                //存入StuCardCommand表中
                $CommData['order'] = '*HQ,' . $imei . ',MONITOR,' . date('His') . ',' . $monitorInfo[1] . '#';
                break;

            case 'domain': // 域名和端口
                $domainInfo = explode(',', $d_value);
                //存入StuCardCommand表中
                $CommData['order'] = '*HQ,' . $imei . ',S2DOMAIN,' . date('His') . ',' . $domainInfo[0] . ',' . $domainInfo[1] . ',5#';
                break;

            case 'ipport': // IP和端口
                $ipportInfo = explode(',', $d_value);
                $ip = str_replace(".", ",", $ipportInfo[0]);
                $port = $ipportInfo[1];
                //存入StuCardCommand表中
                $CommData['order'] = '*HQ,' . $imei . ',S23,' . date('His') . ',' . $ip . ',' . $port . ',5#';
                break;

            case 'rfid': //RFID设置
                $rfidInfo = explode(',', $d_value); //0-RFIDSEQ：天线组序号   1-IN_ID：进天线ID   2-OUT_ID：出天线ID
                //存入StuCardCommand表中
                $CommData['order'] = '*HQ,' . $imei . ',RFID,' . date('His') . ',' . $rfidInfo[0] . ',' . str_replace(":", "", $rfidInfo[1]) . ',' . str_replace(":", "", $rfidInfo[2]) . '#';
                break;
            case 'rfidgps': //RFID GPS设置
                $rfidInfo = explode(',', $d_value); // 0天线组序号 1进id 2出id 3经度 4纬度 5半径
                //存入StuCardCommand表中
                $CommData['order'] = '*HQ,'
                    . $imei
                    . ',RFID,'
                    . date('His') . ','
                    . $rfidInfo[0] . ','
                    . $rfidInfo[1] . ','
                    . $rfidInfo[2] . ','
                    . $rfidInfo[3] . ','
                    . $rfidInfo[4] . ','
                    . $rfidInfo[5]
                    . '#';
                $dataCard['d_type'] = 'rfid';
                $data['d_type'] = 'rfid';
                break;

            case 'callfilter': //来电过滤开关功能设置
                $val = 0;
                if ($d_value == "ON") {
                    $val = 1;
                }
                //存入StuCardCommand表中
                $CommData['order'] = '*HQ,' . $imei . ',CALLFILTER,' . date('His') . ',' . $val . '#';
                break;

            case 'calldisplay': //来电显示开关功能设置
                $val = 0;
                if ($d_value == "ON") {
                    $val = 1;
                }
                //存入StuCardCommand表中
                $CommData['order'] = '*HQ,' . $imei . ',CALLIDSTATUS,' . date('His') . ',' . $val . ',,#';
                break;

            case 'wirelessatte': //无线考勤开关功能设置
                $val = 0;
                if ($d_value == "ON") {
                    $val = 1;
                }
                //存入StuCardCommand表中
                $CommData['order'] = '*HQ,' . $imei . ',RFSWITCH,' . date('His') . ',' . $val . '#';
                break;

            case 'stepcounter': //计步开关功能设置
                $val = 0;
                if ($d_value == "ON") {
                    $val = 1;
                }
                //存入StuCardCommand表中
                $CommData['order'] = '*HQ,' . $imei . ',STEPSWITCH,' . date('His') . ',' . $val . '#';
                break;

            case 'inschool': //进校自动屏蔽开关设置
                $val = $d_value;
                //存入StuCardCommand表中
                $CommData['order'] = '*HQ,' . $imei . ',CALLFUN,' . date('His') . ',' . $val . '#';
                break;

            default:
                $retInfo['flag'] = 0;
                $retInfo['message'] = "failed:The watch does not have the command";
                return $retInfo;
        }
    } elseif ($devicetype == 2) {//2 为手表
        switch ($d_type) {
            case 'phb':
                $whiteInfo = explode(',', $d_value);
                foreach ($whiteInfo as $key => $value) {
                    $phone_info = explode(':', $value);
                    $phone_book_arr[] = $phone_info[1] . ',' . utf8_unicode($phone_info[0]);
                }
                $phone_book_str = 'PHB,' . implode(',', $phone_book_arr);
                $phone_book_len_16 = get_len_16($phone_book_str);
                //存入StuCardCommand表中
                //[3G*8800000015*len*PHB,号码,名字,号码,名字,号码,名字,号码,名字,号码,名字,号码,名字]
                $CommData['order'] = '[3G*' . $imei . '*' . $phone_book_len_16 . '*' . $phone_book_str . ']';//电话本
                break;
            case 'phb2':
                $whiteInfo = explode(',', $d_value);
                foreach ($whiteInfo as $key => $value) {
                    $phone_info = explode(':', $value);
                    $phone_book_arr[] = $phone_info[1] . ',' . utf8_unicode($phone_info[0]);
                }
                $phone_book_str = 'PHB2,' . implode(',', $phone_book_arr);
                $phone_book_len_16 = get_len_16($phone_book_str);
                //存入StuCardCommand表中
                //[3G*8800000015*len*PHB,号码,名字,号码,名字,号码,名字,号码,名字,号码,名字,号码,名字]
                $CommData['order'] = '[3G*' . $imei . '*' . $phone_book_len_16 . '*' . $phone_book_str . ']';//电话本
                break;
            case 'sos':
                $phoneArr = explode(',', $d_value);
                foreach ($phoneArr as $key => $value) {
                    if ($key < 3) {//SOS设置
                        $i = $key + 1;
                        $CommData['order'] = '[3G*' . $imei . '*0010*SOS' . $i . ',' . $value . ']';
                        //存储到commond表
                        $res = M('StuCardCommand')->add($CommData);
                        //存入set表中
                        $SetDataWhere['imei'] = $imei;
                        $SetDataWhere['d_type'] = $d_type;
                        $CheckSet = M('StuCardSet')->where($SetDataWhere)->find();

                        if (empty($CheckSet)) {//判断set表
                            M('StuCardSet')->add($SetData);
                        } else {
                            $updata['id'] = $CheckSet['id'];
                            M('StuCardSet')->where($updata)->save($SetData);
                        }
                        if (taskRedis($imei, $CommData['order'])) {
                            $retInfo['flag'] = 1;
                            $retInfo['message'] = "redis commode " . $d_type . " store success";

                        } else {
                            $retInfo['flag'] = 0;
                            $retInfo['message'] = "failed:redis commond " . $d_type . " failed";

                        }
                    }
                }
                return $retInfo;
                break;
            case 'soundsize': // 喇叭音量
                $this->error('您的设备暂不支持');
                break;

            case 'tealthtime': // 隐身时间(上课时间段)[3g*5678901234*0037*SILENCETIME,21:10-7:30,21:10-7:30,21:10-7:30,21:10-7:30]
                $tealthtime = explode(',', $d_value);
                $timeContent = 'SILENCETIME,' . $tealthtime[0] . ',' . $tealthtime[1] . ',' . rtrim($tealthtime[2], '|ON') . ',00:01-00:02';
                $time_len_16 = get_len_16($timeContent);
                $CommData['order'] = '[3G*' . $imei . '*' . $time_len_16 . '*' . $timeContent . ']';
                break;

            case 'working': // 工作模式
                $workingInfo = explode(',', $d_value);
                if ($workingInfo[0] == 'm1') {//追踪模式:[3g*5678901234*0002*CR]
                    $CommData['order'] = '[3G*' . $imei . '*0002*CR]';
                }
                if ($workingInfo[1] > 0) {//设置上报时间间隔[3g*8800000015*0009*UPLOAD,10]
                    $timeContent = 'UPLOAD,' . $workingInfo[1];
                    $time_len_16 = get_len_16($timeContent);
                    $CommData['order'] = '[3G*' . $imei . '*' . $time_len_16 . '*' . $timeContent . ']';

                }
                break;

            case 'monitor': // 监听号码 中心号码
                //存入StuCardCommand表中 [3g*8800000015*0012*CENTER,00000000000]
                $CommData['order'] = '[3G*' . $imei . '*0013*MONITOR,' . $d_value . ']';
                break;

            case 'domain': // 域名和端口
                $this->error('您的设备暂不支持');
                break;

            case 'ipport': // IP和端口
                $ipportInfo = explode(',', $d_value);
                $ip = $ipportInfo[0];
                $port = $ipportInfo[1];
                //存入StuCardCommand表中 [3g*8800000015*0014*IP,113.81.229.9,5900]
                $ip_content = 'IP,' . $ip . ',' . $port;
                $ip_len_16 = get_len_16($ip_content);
                $CommData['order'] = '[3G*' . $imei . '*' . $ip_len_16 . '*' . $ip_content . ']';
                break;

            case 'rfid': //RFID设置
                $this->error('您的设备暂不支持');
                break;
            case 'rfidgps': //RFID GPS设置
                $this->error('您的设备暂不支持');
                break;

            case 'callfilter': //来电过滤开关功能设置
                $this->error('您的设备暂不支持');
                break;

            case 'calldisplay': //来电显示开关功能设置
                $this->error('您的设备暂不支持');
                break;

            case 'wirelessatte': //无线考勤开关功能设置
                $this->error('您的设备暂不支持');
                break;

            case 'stepcounter': //计步开关功能设置
                if ($d_value == 'ON') {
                    $num = 1;
                } elseif ($d_value == 'OFF') {
                    $num = 0;
                }
                //存入StuCardCommand表中
                $CommData['order'] = '[3G*' . $imei . '*0004*PEDO,' . $num . ']';
                break;

            case 'inschool': //进校自动屏蔽开关设置
                $this->error('您的设备暂不支持');
                break;
            case 'clock'://闹钟
                $AlarmTime = 'REMIND,' . $d_value;//获取长度参数
                $ArgueLen = get_len_16($AlarmTime);//转换为16进制
                $CommData['order'] = '[3G*' . $imei . '*' . $ArgueLen . '*' . 'REMIND,' . $d_value . ']';
                break;
            case 'poweroff'://关机
                $CommData['order'] = '[3G*' . $imei . '*0008*POWEROFF]';//拼接命令
                break;
            default:
                $retInfo['flag'] = 0;
                $retInfo['message'] = "failed:The watch does not have the command";
                return $retInfo;
        }
    }
    //存入command表
    $res = M('StuCardCommand')->add($CommData);
    if (!$res) {
        $retInfo['flag'] = 0;
        $retInfo['message'] = "failed:mysql commond add failed";
        return $retInfo;
    }
    //存入set表中
    $SetDataWhere['imei'] = $imei;
    $SetDataWhere['d_type'] = $d_type;
    $CheckSet = M('StuCardSet')->where($SetDataWhere)->find();

    if (empty($CheckSet)) {//判断set表里面是否有IMEI ->monitor
        $res = M('StuCardSet')->add($SetData);
        if (!$res) {
            $retInfo['flag'] = 0;
            $retInfo['message'] = "failed:mysql commond add failed";
            return $retInfo;
        }
    } else {
        $updata['id'] = $CheckSet['id'];
        $res = M('StuCardSet')->where($updata)->save($SetData);
        if (!$res) {
            $retInfo['flag'] = 0;
            $retInfo['message'] = "failed:mysql commond save failed";
            return $retInfo;
        }
    }
    if (!taskRedis($imei, $CommData['order'])) {
        $retInfo['flag'] = 0;
        $retInfo['message'] = "failed:redis commond " . $d_type . " failed";
        return $retInfo;
    }
    $retInfo['flag'] = 1;
    $retInfo['message'] = "operate success ";
    return $retInfo;

}

//加密算法
function encrypt_ase($str)
{
    import('Vendor.CryptASE.CryptASE');
    $cryptASE = new \CryptAES();
    $res = $cryptASE->encrypt($str);
    return $res;
}

//解密算法
function decrypt_ase($str)
{
    import('Vendor.CryptASE.CryptASE');
    $cryptASE = new \CryptAES();
    $res = $cryptASE->decrypt($str);
    return $res;
}

//随机生成用户昵称

function randomUserName()
{
    $nicheng_tou=array('快乐的','冷静的','醉熏的','潇洒的','糊涂的','积极的','冷酷的','深情的','粗暴的','温柔的','可爱的','愉快的','义气的','认真的','威武的','帅气的','传统的','潇洒的','漂亮的','自然的','专一的','听话的','昏睡的','狂野的','等待的','搞怪的','幽默的','魁梧的','活泼的','开心的','高兴的','超帅的','留胡子的','坦率的','直率的','轻松的','痴情的','完美的','精明的','无聊的','有魅力的','丰富的','繁荣的','饱满的','炙热的','暴躁的','碧蓝的','俊逸的','英勇的','健忘的','故意的','无心的','土豪的','朴实的','兴奋的','幸福的','淡定的','不安的','阔达的','孤独的','独特的','疯狂的','时尚的','落后的','风趣的','忧伤的','大胆的','爱笑的','矮小的','健康的','合适的','玩命的','沉默的','斯文的','香蕉','苹果','鲤鱼','鳗鱼','任性的','细心的','粗心的','大意的','甜甜的','酷酷的','健壮的','英俊的','霸气的','阳光的','默默的','大力的','孝顺的','忧虑的','着急的','紧张的','善良的','凶狠的','害怕的','重要的','危机的','欢喜的','欣慰的','满意的','跳跃的','诚心的','称心的','如意的','怡然的','娇气的','无奈的','无语的','激动的','愤怒的','美好的','感动的','激情的','激昂的','震动的','虚拟的','超级的','寒冷的','精明的','明理的','犹豫的','忧郁的','寂寞的','奋斗的','勤奋的','现代的','过时的','稳重的','热情的','含蓄的','开放的','无辜的','多情的','纯真的','拉长的','热心的','从容的','体贴的','风中的','曾经的','追寻的','儒雅的','优雅的','开朗的','外向的','内向的','清爽的','文艺的','长情的','平常的','单身的','伶俐的','高大的','懦弱的','柔弱的','爱笑的','乐观的','耍酷的','酷炫的','神勇的','年轻的','唠叨的','瘦瘦的','无情的','包容的','顺心的','畅快的','舒适的','靓丽的','负责的','背后的','简单的','谦让的','彩色的','缥缈的','欢呼的','生动的','复杂的','慈祥的','仁爱的','魔幻的','虚幻的','淡然的','受伤的','雪白的','高高的','糟糕的','顺利的','闪闪的','羞涩的','缓慢的','迅速的','优秀的','聪明的','含糊的','俏皮的','淡淡的','坚强的','平淡的','欣喜的','能干的','灵巧的','友好的','机智的','机灵的','正直的','谨慎的','俭朴的','殷勤的','虚心的','辛勤的','自觉的','无私的','无限的','踏实的','老实的','现实的','可靠的','务实的','拼搏的','个性的','粗犷的','活力的','成就的','勤劳的','单纯的','落寞的','朴素的','悲凉的','忧心的','洁净的','清秀的','自由的','小巧的','单薄的','贪玩的','刻苦的','干净的','壮观的','和谐的','文静的','调皮的','害羞的','安详的','自信的','端庄的','坚定的','美满的','舒心的','温暖的','专注的','勤恳的','美丽的','腼腆的','优美的','甜美的','甜蜜的','整齐的','动人的','典雅的','尊敬的','舒服的','妩媚的','秀丽的','喜悦的','甜美的','彪壮的','强健的','大方的','俊秀的','聪慧的','迷人的','陶醉的','悦耳的','动听的','明亮的','结实的','魁梧的','标致的','清脆的','敏感的','光亮的','大气的','老迟到的','知性的','冷傲的','呆萌的','野性的','隐形的','笑点低的','微笑的','笨笨的','难过的','沉静的','火星上的','失眠的','安静的','纯情的','要减肥的','迷路的','烂漫的','哭泣的','贤惠的','苗条的','温婉的','发嗲的','会撒娇的','贪玩的','执着的','眯眯眼的','花痴的','想人陪的','眼睛大的','高贵的','傲娇的','心灵美的','爱撒娇的','细腻的','天真的','怕黑的','感性的','飘逸的','怕孤独的','忐忑的','高挑的','傻傻的','冷艳的','爱听歌的','还单身的','怕孤单的','懵懂的');

    $nicheng_wei=array('嚓茶','凉面','便当','毛豆','花生','可乐','灯泡','哈密瓜','野狼','背包','眼神','缘分','雪碧','人生','牛排','蚂蚁','飞鸟','灰狼','斑马','汉堡','悟空','巨人','绿茶','自行车','保温杯','大碗','墨镜','魔镜','煎饼','月饼','月亮','星星','芝麻','啤酒','玫瑰','大叔','小伙','哈密瓜，数据线','太阳','树叶','芹菜','黄蜂','蜜粉','蜜蜂','信封','西装','外套','裙子','大象','猫咪','母鸡','路灯','蓝天','白云','星月','彩虹','微笑','摩托','板栗','高山','大地','大树','电灯胆','砖头','楼房','水池','鸡翅','蜻蜓','红牛','咖啡','机器猫','枕头','大船','诺言','钢笔','刺猬','天空','飞机','大炮','冬天','洋葱','春天','夏天','秋天','冬日','航空','毛衣','豌豆','黑米','玉米','眼睛','老鼠','白羊','帅哥','美女','季节','鲜花','服饰','裙子','白开水','秀发','大山','火车','汽车','歌曲','舞蹈','老师','导师','方盒','大米','麦片','水杯','水壶','手套','鞋子','自行车','鼠标','手机','电脑','书本','奇迹','身影','香烟','夕阳','台灯','宝贝','未来','皮带','钥匙','心锁','故事','花瓣','滑板','画笔','画板','学姐','店员','电源','饼干','宝马','过客','大白','时光','石头','钻石','河马','犀牛','西牛','绿草','抽屉','柜子','往事','寒风','路人','橘子','耳机','鸵鸟','朋友','苗条','铅笔','钢笔','硬币','热狗','大侠','御姐','萝莉','毛巾','期待','盼望','白昼','黑夜','大门','黑裤','钢铁侠','哑铃','板凳','枫叶','荷花','乌龟','仙人掌','衬衫','大神','草丛','早晨','心情','茉莉','流沙','蜗牛','战斗机','冥王星','猎豹','棒球','篮球','乐曲','电话','网络','世界','中心','鱼','鸡','狗','老虎','鸭子','雨','羽毛','翅膀','外套','火','丝袜','书包','钢笔','冷风','八宝粥','烤鸡','大雁','音响','招牌','胡萝卜','冰棍','帽子','菠萝','蛋挞','香水','泥猴桃','吐司','溪流','黄豆','樱桃','小鸽子','小蝴蝶','爆米花','花卷','小鸭子','小海豚','日记本','小熊猫','小懒猪','小懒虫','荔枝','镜子','曲奇','金针菇','小松鼠','小虾米','酒窝','紫菜','金鱼','柚子','果汁','百褶裙','项链','帆布鞋','火龙果','奇异果','煎蛋','唇彩','小土豆','高跟鞋','戒指','雪糕','睫毛','铃铛','手链','香氛','红酒','月光','酸奶','银耳汤','咖啡豆','小蜜蜂','小蚂蚁','蜡烛','棉花糖','向日葵','水蜜桃','小蝴蝶','小刺猬','小丸子','指甲油','康乃馨','糖豆','薯片','口红','超短裙','乌冬面','冰淇淋','棒棒糖','长颈鹿','豆芽','发箍','发卡','发夹','发带','铃铛','小馒头','小笼包','小甜瓜','冬瓜','香菇','小兔子','含羞草','短靴','睫毛膏','小蘑菇','跳跳糖','小白菜','草莓','柠檬','月饼','百合','纸鹤','小天鹅','云朵','芒果','面包','海燕','小猫咪','龙猫','唇膏','鞋垫','羊','黑猫','白猫','万宝路','金毛','山水','音响');
    $tou_num=rand(0,331);
    $wei_num=rand(0,325);
    $nicheng=$nicheng_tou[$tou_num].$nicheng_wei[$wei_num];
    return $nicheng;
}
// 过滤掉emoji表情
function filter_Emoji($str)
{
    $str = preg_replace_callback(    //执行一个正则表达式搜索并且使用一个回调进行替换
        '/./u',
        function (array $match) {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        },
        $str);
        if(strlen($str)==0){
            $str = randomUserName();
        }
    return $str;
}


?>