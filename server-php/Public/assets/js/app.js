 
//BACKGROUND CHANGER
var g_config = {host:''};
  $(function() {
      $("#button-bg").click(function() {
          $("body").css({
              "background": "url('/Public/assets/img/bg5.jpg')no-repeat center center fixed",
              "background-size": "100% 100%"
          });
      });
      $("#button-bg2").click(function() {
          $("body").css({
              "background": "url('/Public/assets/img/bg2.jpg')no-repeat center center fixed",
              "background-size": "100% 100%"
          });
      });


      $("#button-bg3").click(function() {
          $("body").css({
              "background": "url('/Public/assets/img/bg.jpg')no-repeat center center fixed",
              "background-size": "100% 100%"
          });


      });

      $("#button-bg5").click(function() {
          $("body").css({
              "background": "url('/Public/assets/img/giftly.png')repeat",
              "background-size": "100% 100%"
          });

      });

      $("#button-bg6").click(function() {
          $("body").css({
              "background": "#2c3e50",
              "background-size": "100% 100%"
          });

      });

      $("#button-bg7").click(function() {
          $("body").css({
              "background": "url('/Public/assets/img/bg3.png')repeat",
              "background-size": "100% 100%"
          });

      });
      $("#button-bg8").click(function() {
          $("body").css({
              "background": "url('/Public/assets/img/bg8.jpg')no-repeat center center fixed",
              "background-size": "100% 100%"
          });
      });
      $("#button-bg9").click(function() {
          $("body").css({
              "background": "url('/Public/assets/img/bg9.jpg')no-repeat center center fixed",
              "background-size": "100% 100%"
          });
      });

      $("#button-bg10").click(function() {
          $("body").css({
              "background": "url('/Public/assets/img/bg10.jpg')no-repeat center center fixed",
              "background-size": "100% 100%"
          });
      });
      $("#button-bg11").click(function() {
          $("body").css({
              "background": "url('/Public/assets/img/bg11.jpg')no-repeat center center fixed",
              "background-size": "100% 100%"
          });
      });
      $("#button-bg12").click(function() {
          $("body").css({
              "background": "url('/Public/assets/img/bg12.jpg')no-repeat center center fixed",
              "background-size": "100% 100%"
          });
      });

      $("#button-bg13").click(function() {
          $("body").css({
              "background": "url('/Public/assets/img/bg13.jpg')repeat",
              "background-size": "100% 100%"
          });

      });
      /**
       * Background Changer end
       */
  });

//TOGGLE CLOSE
    $('.nav-toggle').click(function() {
        //get collapse content selector
        var collapse_content_selector = $(this).attr('href');

        //make the collapse content to be shown or hide
        var toggle_switch = $(this);
        $(collapse_content_selector).slideToggle(function() {
            if ($(this).css('display') == 'block') {
                //change the button label to be 'Show'
                toggle_switch.html('<span class="entypo-minus-squared"></span>');
            } else {
                //change the button label to be 'Hide'
                toggle_switch.html('<span class="entypo-plus-squared"></span>');
            }
        });
    });


    $('.nav-toggle-alt').click(function() {
        //get collapse content selector
        var collapse_content_selector = $(this).attr('href');

        //make the collapse content to be shown or hide
        var toggle_switch = $(this);
        $(collapse_content_selector).slideToggle(function() {
            if ($(this).css('display') == 'block') {
                //change the button label to be 'Show'
                toggle_switch.html('<span class="entypo-up-open"></span>');
            } else {
                //change the button label to be 'Hide'
                toggle_switch.html('<span class="entypo-down-open"></span>');
            }
        });
        return false;
    });
    //CLOSE ELEMENT
    $(".gone").click(function() {
        var collapse_content_close = $(this).attr('href');
        $(collapse_content_close).hide();



    });

//tooltip
    $('.tooltitle').tooltip();

/////////////////////////////////////页面提交全局函数-start//////////////////////////////////////////////////////
var g_config = {host:''};
var g_loadingIndex = -1;
function postCallBack(data) {
    if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
    g_loadingIndex = -1;
    if( data.status == 1 ) {
        dialog.success(data.info,data.url);
    }else{
        dialog.error(data.info);
    }
}

function postCallBack1(data) {
    if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
    g_loadingIndex = -1;
    if( data.status == 1 ) {
        dialog.success1(data.info,data.url);
    }else{
        dialog.error(data.info);
    }
}

function btnCallbackRefresh(data){
    if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
    g_loadingIndex = -1;
    if( data.status == 1 ) {
        dialog.success(data.info,function(){ window.location.reload();});
    }else{
        dialog.error(data.info);
    }
}

function btnCallbackRefresh1(data){
    if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
    g_loadingIndex = -1;
    if( data.status == 1 ) {
        dialog.success(data.info,function(){ parent.window.location.reload();});
    }else{
        dialog.error(data.info);
    }
}

function fajax(url,params,callback,type,ifcache) {
    type    = typeof(type) =="undefined" ? 'POST':type;
    ifcache = typeof(ifcache) =="undefined" ? false:ifcache;
    jQuery.ajaxSetup({
        cache: ifcache
    });
    jQuery.ajax({
        type: type,
        url: url,
        data: params,
        cache: false,
        dataType: 'json',
        success: callback
    });
}


function fpost(url,form_id,callback)
{
    var data = $('#'+form_id).serialize();
    if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
    g_loadingIndex = dialog.showLoading();
    fajax(url,data,callback);
}

function fget(url,data,callback) {
    if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
    g_loadingIndex = dialog.showLoading();
    fajax(url,data,callback,'GET');
}

function dpost(url,data,callback) {
    if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
    g_loadingIndex = dialog.showLoading();
    fajax(url,data,callback);
}

/////////////////////////////////////页面提交全局函数-end//////////////////////////////////////////////////////

