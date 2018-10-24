<?php

class ElifeBase
{
    public $accessToken = '2838e92204534d03b00ebe28cc8b47d9';

    public function __construct()
    {
        header("Content-type:text/html; charset=utf-8");
        require_once("OpenSdk.php");
        $loader = new QmLoader;
        $loader->autoload_path = array(CURRENT_FILE_DIR . DS . "client");
        $loader->init();
        $loader->autoload();
    }
}

?>