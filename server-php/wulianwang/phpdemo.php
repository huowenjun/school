<?php
$mobile = $_POST['mobile'];
$content = $_POST['content'];
include_once("phpdemo_func.php");

//发送物联网卡短信
function sendMsg2($mobile,$content){

    $soap= new SoapClient($svr_url='http://121.41.17.233:7860/sms?wsdl');
   //spid:账号,password:密码,accessCode:下发号码,content:下发内容,mobileString:号码列表
    $result    = $soap->Submit($spid='660008' ,$password='nm4zWd',$accessCode='106489910201540008',
        "$content",
        "$mobile");
    return $result;
}
$res = sendMsg2($mobile,$content);
$status = substr($res,0,1);
if($status == 0){
    echo '发送提交成功'."<a href='index.html'>返回</a>";
}else{
    echo '失败'."<a href='index.html'>返回</a>";
}
/*连接失败的常见问题
1：php.ini 配置中   default_socket_timeout的值不能为零
2：php.ini 配置中启用 php_soap.dll、php_xmlrpc.dll、php_curl.dll、php_openssl.dll
  */
 
 ?>