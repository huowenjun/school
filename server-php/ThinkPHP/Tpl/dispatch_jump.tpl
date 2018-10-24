<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>跳转提示</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <script type="text/javascript" src="/Public/assets/js/jquery.min.js"></script>
    <script type="text/javascript" src="/Public/assets/js/rem.js"></script>
    <link rel="shortcut icon" href="/Public/assets/ico/minus.png">
    <style>
        body { background: url(/Public/assets/img/login_out_bg.png) repeat-x; }
        .bg1 { background: url(/Public/assets/img/login_out_bg1.png) no-repeat center center; position: absolute; left: 0; top: 0; right: 0; bottom: 0; background-size: 10.666667rem 10.666667rem; }
        .rocket {
            background: url(/Public/assets/img/login_out_rocket.png) no-repeat;
            width: 1.083333rem;
            height: 1.020833rem;
            position: absolute;
            left: 50%;
            top: 50%;
            background-size: 100% 100%;
            margin-top: 1.520833rem;
            margin-left: -3.75rem;
            -webkit-transform: scale(1);
            -moz-transform: scale(1);
            -ms-transform: scale(1);
            -o-transform: scale(1);
            transform: scale(1);
            opacity: 1;
        }
        .active.rocket {
            -webkit-transition-property: all;
            -moz-transition-property: all;
            -ms-transition-property: all;
            -o-transition-property: all;
            transition-property: all;
            -webkit-transition-duration: 1.5s;
            -moz-transition-duration: 1.5s;
            -ms-transition-duration: 1.5s;
            -o-transition-duration: 1.5s;
            transition-duration: 1.5s;
            -webkit-transition-delay: 2.5s;
            -moz-transition-delay: 2.5s;
            -ms-transition-delay: 2.5s;
            -o-transition-delay: 2.5s;
            transition-delay: 2.5s;
            margin-top: -3.125rem;
            margin-left: 3.125rem;
            -webkit-transform: scale(0.2);
            -moz-transform: scale(0.2);
            -ms-transform: scale(0.2);
            -o-transform: scale(0.2);
            transform: scale(0.2);
            opacity: 0;
        }
        .countTxt {
            color: #FFF;
            font-size: 0.25rem;
            line-height: 0.416667rem;
            position: absolute;
            top: 50%;
            left: 50%;
            -webkit-transform: translate(-50%, -50%);
            -ms-transform: translate(-50%, -50%);
            -o-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
            text-align: center;
        }
        .countTxt .oneRow { font-weight: bold; }
        .countTxt .color1 { color: #00a0e9; }
    </style>
</head>
<body>

<div class="bg1"></div>
<div class="rocket"></div>
<div class="countTxt">
    <span class="oneRow">跳转提示</span><br/>
    <?php if(isset($message)){?>
    <?php echo($message)?><br/>
    <?php }else{?>
    <?php echo($error)?><br/>
    <?php }?>

    页面自动<a class="color1" id="href" href="<?php echo($jumpUrl); ?>">跳转</a>请等待时间：<span class="js-time"><?php echo($waitSecond); ?></span>
</div>


<!-- content end -->
</body>
<script type="text/javascript" src="/Public/assets/js/custom.js"></script>
<!-- /MAIN EFFECT -->

<script type="text/javascript">

    // 倒计时
    $(function() {
        $('.rocket').addClass('active');
        // 获取验证码 倒计时
        var iTime = 3; // 设置倒计时3秒
        var num   = iTime;
        $('.js-time').text(num);
        var oTimer = setInterval(fnAutoTime,1000);

        function fnAutoTime() {
            num--;
            if (num < 0) {
                clearInterval(oTimer);
                //window.location.href="123.html";
                location.href = href;
            } else {
                $('.js-time').text(num);
            };
        }
        // 获取验证码 倒计时 end
    })
</script>

<script type="text/javascript">
    // (function(){
    // var wait = document.getElementById('wait'),href = document.getElementById('href').href;
    // var interval = setInterval(function(){
    //   var time = --wait.innerHTML;
    //   if(time <= 0) {
    //     location.href = href;
    //     clearInterval(interval);
    //   };
    // }, 1000);
    // })();
</script>
</html>