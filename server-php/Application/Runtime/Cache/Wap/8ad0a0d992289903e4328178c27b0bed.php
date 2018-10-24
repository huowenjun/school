<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>生活缴费</title>
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="no">
<link rel="stylesheet" type="text/css" href="/Public/AppHook/css/phone.css" />
<script type="text/javascript" src="/Public/AppHook/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="/Public/AppHook/js/rem.js"></script>
</head>
<body class="bg">
<div class="main livingPayment">
  <div class="header">
    <div class="cont">
      <img class="icon" src="/Public/AppHook/img/icon.png" alt="" />
      <span class="txt">北京</span>
    </div>
  </div>
	<!-- 内容 -->
	<div class="content">
    <div class="list">
      <ul class="classify js_classify">
        <li>
          <a data-href="/index.php/Wap/AppHook/telephonePayment">
            <!-- <i class="icon_list1"></i> -->
            <img src="/Public/AppHook/img/icon_list1.png" alt="" />
            <span class="txt">话费</span>
          </a>
        </li>
        <li>
          <a data-href="/index.php/Wap/AppHook/flowPayment">
            <!-- <i class="icon_list2"></i> -->
            <img src="/Public/AppHook/img/icon_list2.png" alt="" />
            <span class="txt">流量</span>
          </a>
        </li>
        <li>
          <a data-href="/index.php/Wap/AppHook/propertyPayment">
            <!-- <i class="icon_list3"></i> -->
            <img src="/Public/AppHook/img/icon_list3.png" alt="" />
            <span class="txt">物业费</span>
          </a>
        </li>
        <li>
          <a data-href="/index.php/Wap/AppHook/waterPayment">
            <!-- <i class="icon_list4"></i> -->
            <img src="/Public/AppHook/img/icon_list4.png" alt="" />
            <span class="txt">水费</span>
          </a>
        </li>
        <li>
          <a data-href="/index.php/Wap/AppHook/electricPayment">
            <!-- <i class="icon_list5"></i> -->
            <img src="/Public/AppHook/img/icon_list5.png" alt="" />
            <span class="txt">电费</span>
          </a>
        </li>
        <li>
          <a data-href="/index.php/Wap/AppHook/gasPayment">
            <!-- <i class="icon_list6"></i> -->
            <img src="/Public/AppHook/img/icon_list6.png" alt="" />
            <span class="txt">燃气费</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</div>

<script type="text/javascript">
$('.js_classify li').each(function(index, el) {
  var href = $(this).find('a').data('href');
  $(this).find('a').attr('href', href + '?user_id=' + getUrlParam('user_id'));
});

// 获取当前页面网址参数
function getUrlParam(name) {
  var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); // 构造一个含有目标参数的正则表达式对象
  var r   = window.location.search.substr(1).match(reg);  // 匹配目标参数
  if (r != null) {
    return decodeURI(r[2]);
  };
  return null; // 返回参数值
}
</script>
</body>
</html>