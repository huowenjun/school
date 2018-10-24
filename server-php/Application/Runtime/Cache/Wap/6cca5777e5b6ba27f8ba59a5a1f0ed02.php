<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>流量充值</title>
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="no">
<link rel="stylesheet" type="text/css" href="/Public/AppHook/css/phone.css" />
<script type="text/javascript" src="/Public/AppHook/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="/Public/AppHook/js/rem.js"></script>
</head>
<body class="bg">
<div class="main telephonePayment">
	<!-- 内容 -->
	<div class="content">

    <div class="top">
      <div class="numberSort">
        <span class="left">手机号码：</span>
        <span class="right js_operator"></span>
      </div>
      <div class="numberNum">
        <input type="text" class="num js_memo" value="" placeholder="请输入要充值的手机号码" />
      </div>
    </div>

    <div class="center">
      <div style="height: 2.0625rem; line-height: 2.0625rem; color: #919191; font-size: 0.875rem; background: #ffffff; padding-left: 0.875rem; border-bottom: 2px solid #fcfcfc;">充流量</div>
      <ul class="sums js_sums">
        <!-- <li class="active"><span>100M</span><span>售价(<em class="deal_price">7.00</em>元)</span></li>
        <li><span>300M</span><span>售价(<em class="deal_price">14.00</em>元)</span></li>
        <li><span>500M</span><span>售价(<em class="deal_price">22.50</em>元)</span></li>
        <li><span>1G</span><span>售价(<em class="deal_price">30.00</em>元)</span></li>
        <li><span>2G</span><span>售价(<em class="deal_price">52.50</em>元)</span></li>
        <li><span>3G</span><span>售价(<em class="deal_price">75.00</em>元)</span></li>
        <li><span>6G</span><span>售价(<em class="deal_price">135.00</em>元)</span></li>
        <li><span>15G</span><span>售价(<em class="deal_price">200.00</em>元)</span></li> -->
      </ul>
    </div>

    <div class="section">
      <div style="height: 2.0625rem; line-height: 2.0625rem; color: #919191; font-size: 0.875rem; background: #ffffff; padding-left: 0.875rem; border-bottom: 2px solid #fcfcfc;">支付方式</div>
      <ul class="ways js_ways">
        <li class="active" data-val="0">支付宝</li>
        <li data-val="1">微信</li>
      </ul>
    </div>

    <form id="hiddenForm" name="" action="" style="position: fixed; top: 0; right: 0; z-index: 99999;">
      <input type="hidden" id="user_id" name="user_id" value="" placeholder="用户id" />
      <input type="hidden" id="deal_price" name="deal_price" value="" placeholder="充值话费金额" />
      <input type="hidden" id="type" name="type" value="" placeholder="订单类型 0账户零钱充值 1学生卡余额充值 2话费充值 3提现 4流量充值" />
      <input type="hidden" id="deal_name" name="deal_name" value="" placeholder="订单名称(如话费充值)" />
      <input type="hidden" id="memo" name="memo" value="" placeholder="订单备注(话费充值请传递电话号码!!!)" />
      <input type="hidden" id="payment_id" name="payment_id" value="" placeholder="支付方式 0支付宝 1微信 2银联" />
      <input type="hidden" id="item_id" name="item_id" value="" placeholder="支付费用id" />
    </form>

    <a class="subBtn disabled js_subBtn" href="javascript:void(0);">购买</a>
  </div>
</div>

<script type="text/javascript" src="/Public/dist/js/custom.js"></script>
<script type="text/javascript" src="/Public/dist/js/dialog.js"></script>
<!-- layer弹出层 -->
<script type="text/javascript" src="/Public/plugins/layer/layer.js"></script>

<script type="text/javascript">

// input实时监听
$('.js_memo').on('input',function() {
  var phoneRe = /^1[34578]\d{9}$/;
  var value   = $(this).val();
  if (value.length == 11) {
    if (!phoneRe.test(value)) {
      dialog.notify('手机号码格式不正确，请填写正确的手机号码！');
    } else {
      // ajax 充值数据请求
      $.ajax({
        url: '/index.php/Api/MobileFlow/getItemList',
        type: 'post',
        dataType: 'json',
        data: {memo: value},
        success: function(msg) {
          var html = '';
          var obj  = msg.data;
          for (var i = 0; i < obj.length; i++) {
            console.log(typeof obj[i].faceName)
            if (typeof obj[i].faceName !== 'undefined') {
              html += '<li data-itemId="' + obj[i].itemId + '">' +
                        '<span>' +
                          obj[i].faceName +
                        '</span>' +
                        '<span>';
              if (obj[i].faceName == '30M') {
                html +=   '售价(<em class="deal_price">0.01</em>元)';
              } else {
                html +=   '售价(<em class="deal_price">' + obj[i].inPrice + '</em>元)';
              };
              html +=   '</span>' +
                      '</li>'
            };
          };
          $('.js_sums').html(html);
          $('.js_operator').text(msg.data[0].brand);
          $('.js_subBtn').removeClass('disabled');
          $('.js_sums li').eq(0).addClass('active');
        },
        error: function() {
          dialog.error('请求服务器异常！')
        }
      })
    };
  } else {
    $('.js_sums').html('');
    $('.js_operator').text('');
    $('.js_subBtn').addClass('disabled');
  };
})

// 隐藏域赋值
function hiddenForm() {
  // $('#user_id').val(getUrlParam('user_id'));
  $('#user_id').val('8');
  $('#deal_price').val($('.js_sums .active .deal_price').text());
  $('#type').val('4');
  $('#deal_name').val('流量充值');
  $('#memo').val($('.js_memo').val());
  $('#payment_id').val($('.js_ways .active').data('val'));
  $('#item_id').val($('.js_sums .active').attr('data-itemId'));
}

// 点击购买
$('.js_subBtn').click(function() {
  if (!$(this).hasClass('disabled')) {
    hiddenForm();
    var url = g_config.host + '/index.php/Api/Order/getOrder';
    var paymentId = $('#payment_id').val();
    fpost(url,'hiddenForm',function(data) {
      paramToApp(paymentId,data)
    });
  };
})

// 给app（Android & IOS）传参
function paramToApp(paymentId,data) {
  var u = navigator.userAgent, app = navigator.appVersion;
  var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Linux') > -1; // g
  var isIOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); // ios终端
  if (isAndroid) {
    Android.callAndroid(paymentId,JSON.stringify(data));
  } else if (isIOS) {
    IOS.callIphone(paymentId,data);
  }
}

// 选择充值金额/选择支付方式
$('.js_sums, .js_ways').delegate('li', 'click', function() {
  $(this).addClass('active').siblings('li').removeClass('active');
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