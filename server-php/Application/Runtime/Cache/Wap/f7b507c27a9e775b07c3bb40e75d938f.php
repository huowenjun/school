<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>教育商城</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="apple-mobile-web-app-capable" content="no">
<link rel="stylesheet" href="/Public/Store/wap_store/css/search_panel.css" />
<link rel="stylesheet" href="/Public/Store/wap_store/css/idangerous.swiper.css" />
</head>
<body>
<div class="top_content">
  <!-- #header -->
  <div class="head js_head">
    <div class="store_head">
      <form class="head_right clear" action="" method="post">
        <input class="storeInput" id="keyword" type="text" name="" placeholder="搜索在线商城商品" value="" title="搜索在线商城商品" />
        <a class="sousuo js_sousuo"></a>
      </form>
    </div>
  </div>
  <!-- /header -->

  <!-- #banner -->
  <div class="device js_banner" style="display: none">
    <div class="swiper-container">
      <div class="swiper-wrapper js_swiper_wrapper"></div>
      <div class="pagination"></div>
    </div>
  </div>
  <!-- /banner -->
</div>

<div class="height_10"></div>

<!-- #content -->
<div class="wx_wrap">
  <div id="searchResBlock">
    <div class="mod_fixed_wrapper">
      <!-- mod_fixed_wrapper -->
      <div class="mod_filter">
        <div class="mod_filter_inner">
          <div class="tab" data-default>默认</div>
          <div class="tab" data-price>价格<i class="icon_sort"></i></div> <!-- filter_desc -->
          <div class="tab" data-totalsales>销量<i class="icon_sort_single"></i></div>
          <div class="tab switch"><i class="icon_switch"></i></div> <!-- switch_list -->
        </div>
      </div>
    </div>
    <!-- # 一栏/两栏 切换 mod_itemlist_small/mod_itemgrid -->
    <div class="mod_itemlist_small js-mod-item">
      <div id="js_templateInfo"></div>
    </div>
    <!-- // 一栏/两栏 切换 mod_itemlist_small/mod_itemgrid -->
  </div>
  <div class="my_collect_btn js_my_collect_btn">
    <a class="btn" href="javascript:void(0)">我的<br>收藏</a>
  </div>
</div>
<!-- /content -->

<form id="hide_form" action="" method="get">
  <input type="hidden" id="keyword_hide" name="keyword" value="" placeholder="搜索关键字" />
</form>

<!-- jQuery 2.2.3 -->
<script type="text/javascript" src="/Public/plugins/jQuery/jquery-2.2.3.min.js"></script>
<script type="text/javascript" src="/Public/Store/wap_store/js/rem.js"></script>

<!-- 轮播 -->
<script type="text/javascript" src="/Public/Store/wap_store/js/idangerous.swiper.js"></script>
<!-- 自定义公共js -->
<script type="text/javascript" src="/Public/dist/js/dialog.js"></script>
<!-- layer弹出层 -->
<script type="text/javascript" src="/Public/plugins/layer/layer.js"></script>

<script type="text/javascript">
$(function() {
  // banner ajax
  $.ajax({
    type: 'get',
    url: '/index.php/Api/Hook/showAdShop',
    dataType: 'json',
    success: function(msg) {
      var obj = msg.data;
      if (obj.length > 0) {
        html = '';
        $('.js_banner').show();
        for (var i = 0; i < obj.length; i++) {
          html += '<div class="swiper-slide">';
          html +=   '<a href="' + obj[i].url + '">';
          html +=     '<img src="' + obj[i].img + '" />';
          html +=   '</a>'
          html += '</div>'
        };
        $('.js_swiper_wrapper').html(html);

        // 轮播 初始化
        var mySwiper = new Swiper('.swiper-container',{
          pagination: '.pagination',
          loop:true,                           // false true 为环绕模式生效
          grabCursor: true,                    // false true 为光标变为手型
          paginationClickable: true,           // false true 为小圆点点击效果
          autoplayDisableOnInteraction: false, // false true 用户操作之后每次都会禁止启动autoplay(自动播放)
        })
      } else {
        $('.js_head').addClass('background');
      }
    },
    error: function(msg) {
      dialog.error('请求服务器异常！');
    }
  });

  // 我的收藏按钮链接
  $('.js_my_collect_btn').delegate('.btn', 'click', function() {
    window.location.href = '/index.php/Wap/MyCollection?user_id=' + getUrlParam('user_id');
  });

  // ajax 商品数据请求
  var url = '/index.php/Wap/Store/query?user_id=' + getUrlParam('user_id');
  function fnAjax(url) {
    $.ajax({
      type: 'get',
      url: url,
      dataType: 'json',
      success: function(msg) {
        var obj = msg.data.list;
        html = '';
        for (var i = 0; i < obj.length; i++) {
          html+='<div class="hproduct js_commodity_chunk"> <!-- 如果此产品已经售完 就给此div再加个类名：soldout 样式已写好 -->' +
                  '<input type="hidden" class="js_hide_keyid" keyId="' + obj[i].id + '" />' +
                  '<div class="cover">';
                  if (obj[i].coupon_url == '') {
          html+=    '<a href="' + obj[i].url_pc_short + '">';
                  } else {
          html+=    '<a href="' + obj[i].coupon_url + '">';
                  }
          html+=      '<img src="' + obj[i].image + '" alt="">' +
                    '</a>' +
                  '</div>' +
                  '<div class="info_txt">' +
                    '<div class="fn">';
                    if (obj[i].coupon_url == '') {
          html+=      '<a href="' + obj[i].url_pc_short + '">';
                    } else {
          html+=      '<a href="' + obj[i].coupon_url + '">';
                    }
                      if (obj[i].coupon_url != '') {
          html+=        '<span class="hot">赠券</span>';
                      };
          html+=        obj[i].describe +
                      '</a>' +
                    '</div>' +
                    '<div class="prices">' +
                      '<strong>' +
                        '<em>¥' + obj[i].price + '</em>' +
                      '</strong>' +
                      '<span class="comment_num">' +
                        '月销:<span>' + obj[i].sales + '</span>' +
                      '</span>' +
                    '</div>' +
                    '<div class="shop_collect">';
                    if (obj[i].status == 1) {
          html+=      '<span class="collect_btn js_collect active">已收藏</span>';
                    } else {
          html+=      '<span class="collect_btn js_collect">收藏</span>';
                    }
          html+=      '<span class="fr">' + obj[i].address + '</span>' +
                    '</div>' +
                    '<div class="shop_name">' +
                      '<span class="fl">' +
                        '<img class="icon" src="/Public/Store/wap_store/img/icon_shop.png" />' + obj[i].shop_name +
                      '</span>' +
                    '</div>' +
                  '</div>' +
                '</div>'
        };
        $('#js_templateInfo').html(html);
      },
      error: function(msg) {
        dialog.error('请求服务器异常！');
      }
    });
  }
  fnAjax(url);

  // 点击tab之后的效果
  $('.mod_filter_inner .tab:eq(0)').addClass('active'); // 商品默认排序
  $('.mod_filter_inner').delegate('.tab:not(.switch)', 'click', function() {
    $(this).addClass('active').siblings('.tab:not(.switch)').removeClass('active').removeClass('state_switch');
    if ('default' in $(this).data()) {
      if ($(this).hasClass('active')) {
        fnAjax(url);
      }
    };
    if ('price' in $(this).data()) {
      if ($(this).hasClass('active')) {
        if ($(this).hasClass('state_switch')) {
          $(this).removeClass('state_switch');
          fnAjax(url+'&'+'sort=price&order=asc');
        } else {
          $(this).addClass('state_switch');
          fnAjax(url+'&'+'sort=price&order=desc');
        };
      }
    };
    if ('totalsales' in $(this).data()) {
      if ($(this).hasClass('active')) {
        if ($(this).hasClass('state_switch')) {
          $(this).removeClass('state_switch');
          fnAjax(url+'&'+'sort=sales&order=asc');
        } else {
          $(this).addClass('state_switch');
          fnAjax(url+'&'+'sort=sales&order=desc');
        };
      }
    };
  });

  // 点击切换 一栏/两栏 mod_itemlist_small/mod_itemgrid
  $('.mod_filter_inner').delegate('.tab.switch', 'click', function() {
    if ($(this).hasClass('state_switch')) {
      $(this).removeClass('state_switch');
      $('.mod_itemgrid').removeClass('mod_itemgrid').addClass('mod_itemlist_small');
      $('.hproduct').removeClass('item_long_cover');
    } else {
      $(this).addClass('state_switch');
      $('.mod_itemlist_small').removeClass('mod_itemlist_small').addClass('mod_itemgrid');
      $('.hproduct').addClass('item_long_cover');
    };
  });

  // 商品搜索
  $('.js_head').delegate('.js_sousuo', 'click', function() {
    hide_form();
    fnAjax(url + '&' + $('#hide_form').serialize());
  });

  // 隐藏域赋值
  function hide_form() {
    $("#keyword_hide").val($("#keyword").val());
  }

  // 点击收藏
  $('#js_templateInfo').delegate('.js_collect', 'click', function() {
    var obj   = $(this);
    var keyId = $(this).parents('.js_commodity_chunk').find('.js_hide_keyid').attr('keyId');
    if (obj.text() == '收藏') {
      $.ajax({
        type: 'get',
        url: '/index.php/Wap/Store/collect?user_id=' + getUrlParam('user_id') + '&id=' + keyId,
        dataType: 'json',
        success: function(msg) {
          // 此商品已收藏
          obj.addClass('active').text('已收藏');
          dialog.success(msg.info,function() {
            hide_form();
            fnAjax(url + '&' + $('#hide_form').serialize());
            parent.layer.closeAll();
          });
        },
        error: function(msg) {
          dialog.error('请求服务器异常！');
        }
      });
    };
    if (obj.text() == '已收藏') {
      $.ajax({
        type: 'get',
        url: '/index.php/Wap/Store/unfavorite?user_id=' + getUrlParam('user_id') + '&id=' + keyId,
        dataType: 'json',
        success: function(msg) {
          // 此商品已取消收藏
          obj.removeClass('active').text('收藏');
          dialog.success(msg.info,function() {
            hide_form();
            fnAjax(url + '&' + $('#hide_form').serialize());
            parent.layer.closeAll();
          });
        },
        error: function(msg) {
          dialog.error('请求服务器异常！');
        }
      });
    };
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
});
</script>
</body>
</html>