<?php

/**
 * MyPDO
 * PDO连接类
 */
class MyPDO
{
// 保存数据库连接
    private static $_instance = null;

// 连接数据库
    public static function getInstance()
    {
        if (isset(self::$_instance) && !empty(self::$_instance)) {
            return self::$_instance;
        }
        $dbhost = '59.110.0.250';
        $dbname = 'school';
        $dbuser = 'root';
        $dbpasswd = 'Shuhaixinxi1502';
        $pconnect = true;
        $charset = 'utf8';
        $dsn = "mysql:host=$dbhost;dbname=$dbname;";
        try {
            $h_param = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            );
            if ($charset != '') {
                $h_param[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . $charset; //設置默認編碼
            }
            if ($pconnect) {
                $h_param[PDO::ATTR_PERSISTENT] = true;
            }
            $conn = new PDO($dsn, $dbuser, $dbpasswd, $h_param);
        } catch (PDOException $e) {
            throw new ErrorException('Unable to connect to db server. Error:' . $e->getMessage(), 31);
        }
        self::$_instance = $conn;
        return $conn;
    }

// 执行查询
    public static function query($dbconn, $sqlstr, $condparam = array(1))
    {
        $sth = $dbconn->prepare($sqlstr);
        try {
            $sth->execute($condparam);
        } catch (PDOException $e) {
            echo $e->getMessage() . PHP_EOL;
            self::reset_connect($e->getMessage()); // 出错时调用重置连接
        }
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * 添加
     */
    public static function insert($dbconn, $tableName, $data = array(), $condparam = array(1))
    {

        if (is_array($data) && !empty($data)) {
            $keyArr = array_keys($data);
            $key = implode(',', $keyArr);
            $val = '"' . implode('","', $data) . '"';
            $sqlstr = "insert into {$tableName}({$key}) values({$val})";
        } else {
            return false;
        }
        $sth = $dbconn->prepare($sqlstr);

        try {
            $result = $sth->execute($condparam);
        } catch (PDOException $e) {
            echo $e->getMessage() . PHP_EOL;
            self::reset_connect($e->getMessage(), $sqlstr); // 出错时调用重置连接
        }

        return $result;
    }

// 重置连接
    public static function reset_connect($err_msg, $sqlstr = '')
    {
        if (strpos($err_msg, 'MySQL server has gone away') !== false) {
            self::$_instance = null;
            if ($sqlstr) {
                file_put_contents('sqlerr.txt',$sqlstr.PHP_EOL,FILE_APPEND);
                //执行失败的sql
                $dbhost = '59.110.0.250';
                $dbname = 'school';
                $dbuser = 'root';
                $dbpasswd = 'Shuhaixinxi1502';
                $dsn = "mysql:host=$dbhost;dbname=$dbname;";
                $conn2 = new PDO($dsn, $dbuser, $dbpasswd);
                $conn2->exec($sqlstr);
                $conn2 = null;
            }
        }
    }
}

?>