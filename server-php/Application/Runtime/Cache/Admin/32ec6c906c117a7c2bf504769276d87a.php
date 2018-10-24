<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>信平台安防校园管理系统</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Le styles -->
    <script type="text/javascript" src="/Public/assets/js/jquery.js"></script>
    <link rel="stylesheet" href="/Public/assets/css/loader-style.css">
    <link rel="stylesheet" href="/Public/assets/css/bootstrap.css">
    <link rel="stylesheet" href="/Public/assets/css/signin.css">
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="/Public/assets/js/html5.js"></script>
    <![endif]-->
    <!-- Fav and touch icons -->
    <!-- <link rel="shortcut icon" href="/Public/assets/ico/minus.png"> -->
</head>
<style>
*{
    margin: 0;
    padding: 0;
}
ul,li{ list-style: none;}
.twe-code{
  width: 540px;
  margin: 0 auto;
  overflow: hidden;
}
.twe-code li{
    float: left;
    width: 256px;
    text-align:center;
}
.twe-code li p{ font-size: 18px; color: #fff; font-weight: bold;}
.twe-code li img { display: block;  width: 256px; height: 256px; margin-bottom: 15px;}
.twe-code li.marginright{
    margin-right:28px;
}
.login-footer{
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    text-align: center;
}
.login-footer p{
    font-size: 14px;
    color: #fff;  
}
@media screen and (max-width: 1700px) {
    .twe-code{
        width: 320px;
    }
    .twe-code li{
        width: 150px;
    }
    .twe-code li.marginright{
       margin-right:20px;
    }
    .twe-code li p{ font-size: 14px;}
    .twe-code li img { display: block;  width: 150px; height: 150px;}
}
</style>
<body>
<!-- Preloader -->
<div id="preloader">
    <div id="status">&nbsp;</div>
</div>
<div class="container">
    <div class="" id="login-wrapper">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div id="logo-login">
                    <h1>信平台安防校园管理系统
                        <span>v1.0</span>
                    </h1>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="account-box">
                    <form id="formLogin" action="" method="post" onsubmit="return false;">
                        <div class="form-group">
                            <label for="username">用户名</label>
                            <input type="text" id="aaa" name="username" style="width:1px;  visibility: hidden;" />  
                            <input type="password" id="bbb"   name="password"style="width:1px; visibility: hidden;" /> 
                            <input type="text" id="username" class="form-control" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">密码</label>
                            <input type="password" id="password" class="form-control" name="password" required >
                        </div>
                         <div class="form-group" style="overflow:hidden;">
                            <a class="pull-right" style="color: #9ea7b3; outline:none;" href="/index.php/Admin/ForgetPw/AccountValidate/index" target="_blank">忘记密码？</a>
                        </div>
                        <div class="checkbox pull-left">
                            <label>
                                <input type="checkbox" id="rememberUser" />记住密码
                            </label>
                        </div>
                        <button type="button" class="btn btn-primary pull-right" id="btn_login" onclick="javascript:login();">登 录</button>

                    </form>
                    <a class="forgotLnk" href="index.html"></a>
                    <div class="row-block">
                        <div class="row">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <p>&nbsp;</p>
    <ul class="twe-code">
        <li class="marginright">
            <img src="/Public/dist/img/code/twe-code.png">
            <p>APP下载</p>
        </li>
        <li>
            <img src="/Public/dist/img/code/we-code.jpg">
            <p>关注微信公众号</p>
        </li>
    </ul>
    <div class="login-footer">
        <p>信平台安防校园管理系统<br style="margin-bottom:10px;'">备案/许可证编号为：京ICP备15018821号</p>
    </div>
    
</div>

<!--  END OF PAPER WRAP -->

<!-- MAIN EFFECT -->
<script type="text/javascript" src="/Public/assets/js/preloader.js"></script>
<script type="text/javascript" src="/Public/assets/js/bootstrap.js"></script>
<script type="text/javascript" src="/Public/assets/js/layer/layer.js"></script>
<script type="text/javascript" src="/Public/assets/js/dialog.js"></script>
<script type="text/javascript" src="/Public/assets/js/app.js"></script>
<script type="text/javascript" src="/Public/assets/js/load.js"></script>
<script type="text/javascript" src="/Public/assets/js/main.js"></script>
<!-- /MAIN EFFECT -->

<!-- <script src="http://maps.googleapis.com/maps/api/js?sensor=false" type="text/javascript"></script> -->

<script type="text/javascript" src="/Public/assets/js/custom.js"></script>
<!-- cookie -->
<script type="text/javascript" src="/Public/dist/js/jquery.cookie.js"></script>

<script type="text/javascript">
    if($.cookie("userName") && $.cookie("passWord")) {
        $("#username").val($.cookie("userName"));
        $("#password").val($.cookie("passWord"));
    } else {
        $("#username").val("");
        $("#password").val("");
    }
    // 回车登录
    $(document).ready(function() {
       $("body").keydown(function(event) {
            if(event.keyCode == "13"){ 
                search();
            }
        })
    }); 
    function postCallBack(data) {
        if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
        g_loadingIndex = -1;
        if( data.status == 1 ) {
            dialog.notify1(data.info,data.url);
        }else{
            dialog.notify1(data.info);
        }
    }
    // 点击登录
    function login() {   
        // 判断记住用户名checkbox是否被选中
        if ($('#rememberUser').prop('checked') == true) {
            // 已选中
            $.cookie("userName",$("#username").val(),{expires:1}); // 储存登陆用户名
            $.cookie("passWord",$("#password").val(),{expires:1}); // 储存登陆密码
        } else {
            // 未选中
            $.cookie("userName",null,{expires:1}); // 清除登陆用户名
            $.cookie("passWord",null,{expires:1}); // 清除登陆密码
        };
        // 登陆
        var url = g_config.host + "/index.php/Admin/Login/login_handle";
        fpost(url,'formLogin',postCallBack);
    }

    function search() {
        var bFind = false;
        $("input[type=text],input[type=password],input[type=textarea]").each(function(){
            if($(this).val() == '' && $(this).attr('required') !== undefined && $(this).attr('required') !== false) {
                $(this).focus();
                bFind = true;
                return false;
            }
        });
        if((bFind === false) && ($("#btn_login") !== undefined)){
            $("#btn_login").click();
        }
    }
</script>

</body>
</html>