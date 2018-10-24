<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
<title>绑定页面</title>
<link rel="stylesheet" href="/Public/Wechat/css/weui.css" />
<link rel="stylesheet" href="/Public/Wechat/css/weui2.css" />
<link rel="stylesheet" href="/Public/Wechat/css/weui3.css" />
<!-- 自定义css文件 -->
<link rel="stylesheet" href="/Public/Wechat/css/phone.css" />
</head>
<body>
<div class="header">
  <div class="custom_cell">
    <div class="weui-cell__bd" style="text-align: center;">
      <p>绑定页面</p>
    </div>
  </div>
</div>

<div id="countDown" class="page-hd" style="text-align: center; display:none;">
<div class="page-bd-15">
  <i class="weui_icon_msg weui_icon_success"></i>
  <p class="f22" style=" margin:0 0 5px 0;">已绑定成功</p>
  <p class="f18"><span class="f-red"></span>后跳转到主界面</p>
</div>
</div>
<div class="container custom_AtndRecord" id="container">
  <div class="weui_cells weui_cells_form">
    <div class="weui_cell">
      <div class="weui_cell_hd"><label for="" class="weui_label">登陆账号：</label></div>
      <div class="weui_cell_bd weui_cell_primary">
        <input class="weui_input" type="text" id="username" value="" placeholder="请输入手机号" />
      </div> 
    </div>
    <div class="weui_cell">
      <div class="weui_cell_hd"><label for="" class="weui_label">密码：</label></div>
      <div class="weui_cell_bd weui_cell_primary">
        <input class="weui_input" type="password" id="password" value="" placeholder="请输入密码" />
      </div> 
    </div>
  </div>
  <div class="custom_height_10px"></div>
  <div class="page-bd-15">
    <a href="javascript:;" class="weui_btn bg-blue" onclick="fnBinding();"><i class="weui-icon-success-no-circle"></i> 确认绑定</a>
  </div>
</div>
<script type="text/javascript" src="/Public/Wechat/js/zepto.js"></script>
<!-- 自定义js文件 -->
<script type="text/javascript" src="/Public/Wechat/js/phone.js"></script>
<!-- 选择日期时间 -->
<script type="text/javascript" src="/Public/Wechat/js/picker.js"></script>
<!-- 下拉单选 下拉多选 -->
<script type="text/javascript" src="/Public/Wechat/js/select.js"></script>

<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>

<script type="text/javascript">
// 绑定
function fnBinding() {
  var username = $('#username').val();
  var password = $('#password').val();
  $.ajax({
    type : "get",
    data:{username:username,password:password},
    dataType : 'json',
    url : '/index.php/Wechat/Bind/login_handle',
    success : function(res) {
      console.log(res);
      if(res.status == 1 ){
        // $.toast(res.info);
        console.log(res.data.data.appId);
        countDown();

        wx.config({
          debug: false,
          appId: res.data.data.appId,
          timestamp: res.data.data.timestamp,
          nonceStr: res.data.data.nonceStr,
          signature: res.data.data.signature,
          jsApiList: [
            // 所有要调用的 API 都要加到这个列表中
              'closeWindow'
          ]
        });



      }else{
        $.toast(res.info,"forbidden");
      }
    },
    error: function(msg) {
      $.toast("请求服务器异常！", "forbidden");
    }
  });
}

// 倒计时
var timer=null;
var countDownNum=5;
function countDown(){  
  $('#container').hide();
  $('#countDown').show();    
  $('#countDown span').html(countDownNum);  
  if(countDownNum==0){  
    clearTimeout(timer);
    wx.ready(function () {
      // 在这里调用 API
      wx.closeWindow();
    });
    return false;
  }  
  countDownNum -= 1;  
  timer=setTimeout("countDown()",1000);  
}   
</script>
</body>
</html>