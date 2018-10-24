/**
 *
 * @authors ljs (you@example.org)
 * @date    2016-05-07 12:21:48
 * @version $Id$
 */

// 动态添加 头部 左侧导航 底部
// document.write("<script type='text/javascript' src='../../public/header.js'></script>");
// document.write("<script type='text/javascript' src='../../public/left_nav.js'></script>");
// document.write("<script type='text/javascript' src='../../public/footer.js'></script>");

// 左侧导航 模拟滚动条插件
// document.write("<script type='text/javascript' src='../../plugins/slimScroll/jquery.slimscroll.js'></script>");

$(function() {
  // 修改密码 弹出层
  $('#changePassword').on('click', function() {
    var user_id = $('#userid').val();
    layer.open({
    type: 2,
    title: '修改密码',
    maxmin: true,
    shadeClose: true, // 点击遮罩关闭层
    area : ['60%' , '520px'],
    content: '/index.php/Admin/Login/edit?user_id='+user_id
    });
  });
})

// 当前导航样式
$(function() {
  var URL = document.URL;
  $('.js_slimScroll .treeview-menu a').each(function(index, el) {
    var curA_url = $(this).attr('href');
    if (URL.indexOf(curA_url)>-1) {
      $(this).parents('.treeview-menu').addClass('menu-open');
      $(this).parents('.treeview').addClass('active');
    };
  });
})
// 当前导航样式 end

// 实时处理树展开、折叠显示滚动条(页面左侧树)
function leftTreeScroll(){
  var wrapperH= $('.content-wrapper').outerHeight(true);
  var notH = $('.tree-search').outerHeight(true)+ $('.box-header').eq(0).outerHeight(true) + $('.content-header').outerHeight(true) +50;
  var treeH = wrapperH - notH;
  $('.js_ztree_height').parents(".tree-scroll").css("overflow-y","auto");  
  if($(".js_ztree_height").height()>treeH){
    $('.js_ztree_height').parents(".tree-scroll").css({
      'height': treeH,
      'overflow-y': "scroll"
    });
  }else if($(".js_ztree_height").height()<treeH){
    $('.js_ztree_height').parents(".tree-scroll").css({
      'height': $(".js_ztree_height").height(),
      'overflow-y': "hidden"
    });
  }
}
// 换皮肤
$('body').removeClass('skin-blue-light').addClass('skin-blue');

// 页面主容器高度
function fnwrapperH() {
  var windowH     = $(window).height();
  var headerH     = $('.main-header').outerHeight(true);
  var FooterH     = $('.main-footer').outerHeight(true);
  var userFormH   = $('.main-sidebar .user-panel').outerHeight(true) + $('.main-sidebar .sidebar-form').outerHeight(true);
  var wrapperH    = windowH - headerH - FooterH;
  var slimScrollH = windowH - userFormH - headerH - FooterH;

  var contentHeaderH = $('.content-header').outerHeight(true);
  var mapH = windowH - headerH - FooterH - contentHeaderH;
  $('#mapBox,.map-left,#allmap').css({
    'height': mapH
  });

  $('.content-wrapper').css({
    'overflow-y': 'auto',
    'height': wrapperH
  });
  $('.slimScroll').css({
    height: slimScrollH
  });
  $('.slimScrollDiv').css({
    height: slimScrollH
  });
  $('.slimScrollRail').css({
    height: slimScrollH
  });
}
fnwrapperH();
// 页面主容器高度 end

setInterval(function() {
  fnwrapperH();
}, 500)

$(window).resize(function() {
  fnwrapperH();
});

//------------ 查询条件 获取初始日期 -----------//
function fnGetData(curTime) {
  var date = new Date(curTime);
  var year = date.getFullYear();
  var month = date.getMonth() + 1;
  var day = date.getDate();
  var hour = date.getHours(); //获取系统时，
  var minute = date.getMinutes(); //分
  month = month>9?month:'0'+month;
  day = day>9?day:'0'+day;
  return {
    'date':year + '-' + month + '-' + day+' '+hour+':'+minute,
  }
}
// 默认日期（昨天）
var yStart = fnGetData(new Date().getTime() - 24 * 3600 * 1000);
var yEnd   = fnGetData(new Date().getTime() - 24 * 3600 * 1000);
$('.sydate').val(yStart.date);
$('.eydate').val(yEnd.date);
// 默认日期（今天）
var tStart = fnGetData(new Date().getTime());
var tEnd   = fnGetData(new Date().getTime());
$('.stdate').val(tStart.date);
$('.etdate').val(tEnd.date);
//------------ 查询条件 获取初始日期 end -----------//

$(function() {
  fniframeHeight();

  //------------ 搜索栏 输入框宽度 -----------//
  var keywordW = $('#keyword1').width();
  var txtNum   = $('#keyword1').attr('placeholder');
  $('body').append('<div id="txtNumW" style="position:fixed; left:-999px; top:-999px; opacity: 0;"><span>'+txtNum+'</span></div>');
  var minW = $('#txtNumW span').width();
  $('#txtNumW').remove();
  if (keywordW < minW) {
    $('#keyword1').width(minW);
  };
  //------------ 搜索栏 输入框宽度 end -----------//

  // 新增
  $('.add').click(function() {
    $('.primaryKey').val('0');
    $('.upload_img').attr('src','');
    edit(0);
  });
  $('.add2').click(function() {
    $('.primaryKey').val('0');
    $('.upload_img').attr('src','');
    edit2(0);
  });

  // 删除
  $('#remove').click(function() {
    del();
  });
  $('#remove2').click(function() {
    del2();
  });

  // // (新增/编辑)重置
  // $(document).delegate('.reset', 'click', function() {
  //   var modalTitleTxt = $('.layui-layer-title').text();
  //   console.log(modalTitleTxt)
  //   if (modalTitleTxt == '新增') {
  //     $('#modalWindow').find('.form-control').val('');
  //   } else {
  //     var row = $('#modalWindow').data('thisData');
  //     for(var key in row) {
  //       console.log(key+':'+row[key]);
  //       $('#' + key).val(row[key]);
  //     }
  //   };
  // });

  // 查看
  // $('.js-custom-table-data').delegate('.view', 'click', function(event) {
  //   var aInfo = $('.js-custom-table-data').bootstrapTable('getSelections');
  //   for (var i = 0; i < aInfo.length; i++) {
  //     console.group('ID = '+aInfo[i]['id']+':');
  //     for(var key in aInfo[i]) {
  //       console.log(key+':'+aInfo[i][key]);
  //     }
  //     console.groupEnd();
  //   };
  // })

  window.operateEvents = {
    // 点击查看
    'click .view': function (e, value, row, index) {
      var $this = $(this).parents('.bootstrap-table');
      fnpopupTableData(row,$this)
      layer.open({
        type: 1,
        title: '查看',
        shadeClose: true,
        shade: 0.2,
        area: ['50%', '60%'],
        content:$('#modalView')
      });
      $('.form-horizontal .form-control').each(function(){
        var val = $(this).val();
        $('body').append('<span id="js_txt">'+val+'</span>');
        var txt_width = $('#js_txt').width();
        $('#js_txt').remove();
        var box_width = $('.form-horizontal .form-group .col-md-10').width();
        if (txt_width>box_width) {
          $(this).before('<textarea class="form-control" disabled="">'+val+'</textarea>');
          $(this).remove();
        };
      });
    },
    'click .view2': function (e, value, row, index) {
      var $this = $(this).parents('.bootstrap-table');
      fnpopupTableData(row,$this)
      layer.open({
        type: 1,
        title: '查看',
        shadeClose: true,
        shade: 0.2,
        area: ['50%', '60%'],
        content:$('#modalView')
      });
      $('.form-horizontal .form-control').each(function(){
        var val = $(this).val();
        $('body').append('<span id="js_txt">'+val+'</span>');
        var txt_width = $('#js_txt').width();
        $('#js_txt').remove();
        var box_width = $('.form-horizontal .form-group .col-md-10').width();
        console.log(txt_width,box_width)
        if (txt_width>box_width) {
          $(this).before('<textarea class="form-control" disabled="">'+value+'</textarea>');
          $(this).remove();
        };
      });
    },
    // 点击详情
    'click .viewDetails': function (e, value, row, index) {
      for(var key in row) {
        console.log(key+':'+row[key]);
      }
      var key = Object.keys(row)[0]; // 主键第一个值
      edit(row[key]);
    },
    // 点击编辑
    'click .edit': function (e, value, row, index) {
      for(var key in row) {
        console.log(key+':'+row[key]);
      }
      var key = Object.keys(row)[0]; // 主键第一个值
      edit(row[key]);
      if (row.region_id) { // 学校管理页面 编辑时给地区赋值
        $('#regionname').val(row.region_id);
      };
    },
    // tab2 点击编辑
    'click .edit2': function (e, value, row, index) {
      for(var key in row) {
        console.log(key+':'+row[key]);
      }
      var key = Object.keys(row)[0]; // 主键第一个值
      edit2(row[key]);
    },
    // 发布广告页面 用户代理商登录 查看详情弹出层
    'click .details': function (e, value, row, index) {
      $('#banner_id2').val(row.id);
      layer.open({
        type: 1,
        title: '提成详情',
        shadeClose: true,
        shade: 0.2,
        area: ['50%', '60%'],
        content:$('#viewDetailsModal')
      });
      fnlineChartAjax();
    },
    // 发布广告页面 用户代理商登录 删除已发布的广告
    'click .removeAd': function (e, value, row, index) {
      for(var key in row) {
        console.log(key+':'+row[key]);
      }
      var key = Object.keys(row)[0]; // 主键第一个值
      del(row[key]);
    }
  };

  // 点击查询
  $('.query').click(function(){search();});
  $('.query2').click(function(){search2();});

  // 回车查询
  $('.navbar-default .form-control').focus(function() {
    $(this).keydown(function(event) {
      if (event.keyCode==13) {
        search();
      };
    });
  });

  // 点击刷新
  $('.refresh').click(function() {
    window.location.reload(true);
    // refresh();
  });


  // 点击保存
  $(document).delegate('.save', 'click', function() {
    save();
  });

  // 点击保存
  $(document).delegate('.save2', 'click', function() {
    save2();
  });

});

// 查看 弹出层获取的table表格数据
function fnpopupTableData(obj,_this) {
  $('#modalView').remove();
  var oTits  = _this.find('.keep-open .dropdown-menu input[data-field]');
  var oInfo  = {};
  oTits.each(function(index, el) {
    var text = $(this).parent().text();
    var name = $(this).attr('data-field');
    var pos  = name.indexOf('_brief');
    if(pos!= -1) {
      name = name.substr(0,pos);
    }
    oInfo[text] = obj[name];
  });
  console.log(oInfo)
  var html = '';

  html+='  <div class="shade" id="modalView">';
  html+='  <div class="modal-body">';
  html+='  <div class="container-fluid">';
  html+='  <form class="form-horizontal" action="" method="post" onsubmit="return false">';
  html+='  <div class="row">';
  html+='  <div class="col-md-12">';
  html+='  <fieldset class="stepy-step">';
  html+='  <legend>全部详情</legend>';
  html+='  <div class="row">';
  html+='  <div class="col-md-12">';
  for(var key in oInfo) {
    // console.log(key+':'+oInfo[key]);
    if (key.trim()!='操作' && key.trim()!='' && key.trim()!='查看') {
      var txt = $('<span>'+oInfo[key]+'</span>').text();
      html+='<div class="form-group"><label class="col-md-2 control-label">'+key+'</label> <div class="col-md-10"><input type="text" class="form-control" value="'+txt+'" /></div></div>';
    };
  }
  html+='  </div>';
  html+='  </div>';
  html+='  </fieldset>';
  html+='  </div>';
  html+='  </div>';
  html+='  </form>';
  html+='  </div>';
  html+='  </div>';
  html+='  <div class="modal-footer" style="text-align:center;">';
  html+='  <button type="button" class="btn btn-default btn-sm layui-layer-close">';
  html+='  <span class="glyphicon glyphicon-remove"></span> 取消';
  html+='  </button>';
  html+='  </div>';
  html+='  </div>';
  $('body').append(html);
  $('#modalView input').attr('readonly',true);
}

//------------ iframe css -----------//
function fniframeHeight() {
  var oFrame = $('.myFrameName',window.parent.document); // 上一级页面iframe
  if (oFrame.length) {
    var activeHtmlH = $('html').height(); // 当前页html高度
    var offsetTop   = oFrame.offset().top; // 距离顶部的距离(高度)
    var parHeight   = $(window.parent).height();
    var paddTop     = parseInt(oFrame.parents('.body-nest').css('paddingTop'));
    var paddBot     = parseInt(oFrame.parents('.body-nest').css('paddingBottom'));
    var minIframeH  = parHeight - offsetTop - paddTop - paddBot;
    if (activeHtmlH < minIframeH) {
      activeHtmlH = minIframeH;
    };
    oFrame.css({
      'width': '100%',
      'height': activeHtmlH
    });
  };
};
//------------ iframe css end -----------//

//------------ 初始化下拉框数据 -----------//
function initSelect(id,url,select_id,params,all,title,fnn) {
  //ajax请求数据
  $.ajax({
    url:url,
    data:params,
    type:'get',
    cache:false,
    dataType:'json',
    success:function(res) {
      if (res.status == 1 ) {
        $("#"+id).empty();
        if(typeof(title) == 'undefined') title = '请选择';
        if(all) $("#"+id).append('<option value="">'+title+'</option>');
        //var len = res.data.length;
        $.each(res.data, function(key, value){
            // console.log(key, value);
            $("#"+id).append('<option value="'+key+'">'+value+'</option>');
        });
        // console.log('fnn:'+fnn)
        if (!!fnn) { // 如果selcet有默认值时，需回调函数
          fnn();
          // console.log('fnn:'+fnn)
        };
        // for(var i=0; i < len; i ++){
        //   $("#"+id).append('<option value="'+res.data[i].id+'">'+res.data[i].value+'</option>');
        // };
      } else {
        dialog.error(res.info);
      }
    }
  });
}
function initSelectClass(id,url,select_id,params,all,title,fnn) {
  //ajax请求数据
  $.ajax({
    url:url,
    data:params,
    type:'get',
    cache:false,
    dataType:'json',
    success:function(res) {
      if (res.status == 1 ) {
        $("select."+id).empty();
        if(typeof(title) == 'undefined') title = '请选择';
        if(all) $("select."+id).append('<option value="">'+title+'</option>');
        $.each(res.data, function(key, value){
          // console.log(key, value);
          $("select."+id).append('<option value="'+key+'">'+value+'</option>');
        });
        if (!!fnn) { // 如果selcet有默认值时，需回调函数
          fnn();
        };
      } else {
        dialog.error(res.info);
      }
    }
  });
}
//------------ 初始化下拉框数据 end -----------//

//------------ 获取表格数据 -----------//
function queryList(url,params,fnn) {
  $.ajax({
    url:url,
    data:params.data,
    type:'get',
    cache:false,
    dataType:'json',
    success:function(res) {
      if (res.status == 1 ) {
        params.success({
          total: res.data.count,
          rows: res.data.list,
          data:res.data.list,
          fixedScroll:true
        });
      } else {
        dialog.error(res.info);  //message  20161130
      }
      if (!!fnn) { // 如果有ajax加载完之后需要执行的js 就回调此函数
        fnn(res);
      };
    },
    error : function() {
      dialog.error("请求服务器异常！");
    },
    complete:function() {
      fniframeHeight();
    }
  });
}
//------------ 获取表格数据 end -----------//

// 自定义编辑表格中的操作内容
function operateFormatter(value, row, index) {
  return [
    '<button type="button" class="btn btn-sm btn-primary edit" data-color="#39B3D7" data-opacity="0.95">',
    '<span class="glyphicon glyphicon-edit"></span> 编辑',
    '</button>',
    '<button type="button" class="btn btn-sm btn-primary view" data-color="#39B3D7" data-opacity="0.95">',
    '<span class="glyphicon glyphicon-eye-open"></span> 查看',
    '</button>'
  ].join('');
}
// 自定义编辑表格中的查看按钮
function viewFormatter(value, row, index) {
  return [
    '<button type="button" class="btn btn-sm btn-primary view" data-color="#39B3D7" data-opacity="0.95">',
    '<span class="glyphicon glyphicon-eye-open"></span> 查看',
    '</button>'
  ].join('');
}
// 自定义编辑表格中的编辑按钮
function editFormatter(value, row, index) {
  return [
    '<button type="button" class="btn btn-sm btn-primary edit" data-color="#39B3D7" data-opacity="0.95">',
    '<span class="glyphicon glyphicon-edit"></span> 编辑',
    '</button>'
  ].join('');
}
// 自定义编辑表格中的详情按钮
function viewDetailsFormatter(value, row, index) {
  return [
    '<button type="button" class="btn btn-sm btn-primary viewDetails" data-color="#39B3D7" data-opacity="0.95">',
    '<span class="glyphicon glyphicon-eye-open"></span> 详情',
    '</button>'
  ].join('');
}
// tab切换 tab2的表格操作按钮
function operateFormatter2(value, row, index) {
  return [
    '<button type="button" class="btn btn-sm btn-primary edit2" data-color="#39B3D7" data-opacity="0.95">',
    '<span class="glyphicon glyphicon-edit"></span> 编辑',
    '</button>',
    '<button type="button" class="btn btn-sm btn-primary view2" data-color="#39B3D7" data-opacity="0.95">',
    '<span class="glyphicon glyphicon-eye-open"></span> 查看',
    '</button>'
  ].join('');
}

// 获取所有选中的行的所有id
function getIdSelections(table, field) {
  return $.map(table.bootstrapTable('getSelections'), function (row) {
    if (field) {
      return row[field];
    } else {
      return row;
    };
    // return row[field];
  });
}

// ---------------------------------------- #导入文件 --------------------------------------------- //
// 显示导入文件窗口
function showImportModal(url,refreshDataMethod) {
  var html = '';
  html += '  <div class="shade" id="importDialogModal">';
  html += '  <div class="modal-body">';
  html += '  <div class="container-fluid">';
  // html += '  <form class="form_import_file" id="form_import_file" action="" enctype="multipart/form-data" method="post">';
  html += '  <form class="form_import_file" id="form_import_file" action="/index.php/Admin/Common/upload_excel" enctype="multipart/form-data" method="post">';
  html += '  <div class="row">';
  html += '  <div class="col-md-12">';
  html += '  <div class="uploadFile">';
  html += '  <div>上传文件</div>';
  html += '  <div style="margin: 8px 0">';
  html += '  <label class="upload_btn" for="upload_files"><i class="fa fa-fw fa-upload"></i>点击上传</label>';
  // html += '  <input type="file" id="import_file" multiple />';
  html += '  <input type="hidden" id="type2_hide" name="type" value="" placeholder="导入获取的返回数据" />';
  html += '  <input type="file" id="upload_files" name="upload_files" value="" style="display: none;" />';
  html += '  <input type="hidden" id="import_file_url" name="file_url" />';
  html += '  <span id="file_name" class="file_name" style="margin-left: 8px;"></span>';
  html += '  </div>';
  html += '  <div>注：上传文件必须是xls格式</div>';
  html += '  </div>';
  html += '  </div>';
  html += '  </div>';
  html += '  </form>';
  html += '  </div>';
  html += '  </div>';
  html += '  <div class="modal-footer" style="text-align:center;">';
  html += '  <button type="button" class="btn btn-sm btn-primary sub_import_file">';
  html += '  <span class="glyphicon glyphicon-ok"></span> 导入';
  html += '  </button>';
  html += '  <button type="button" class="btn btn-default btn-sm layui-layer-close">';
  html += '  <span class="glyphicon glyphicon-remove"></span> 取消';
  html += '  </button>';
  html += '  </div>';
  html += '  </div>';
  $('body').append(html);

  // $('#import_file').uploadify({
  //   'swf': '/Public/assets/js/misc/uploadify.swf',
  //   'uploader' : '/index.php/Admin/Common/upload_excel',
  //   'buttonText': "设备文件上传",
  //   'fileTypeExts': "*.xls",
  //   'method': 'post',
  //   onUploadSuccess: function (file, data, response) {
  //     console.log(data)
  //     var res = JSON.parse(data);
  //     if (res.status == 1 ) {
  //       $('#import_file_url').val(res.info);
  //       dialog.success2("文件上传成功","文件上传成功");
  //     } else {
  //       dialog.error(res.info);
  //     }
  //   },
  //   onUploadError: function (file, errorCode, errorMsg, errorString) {
  //     dialog.error('文件：' + file.name + ' 上传失败: ' + errorString);
  //   }
  // });
  
  layer.open({
    type: 1,
    title: '导入',
    shadeClose: true,
    shade: 0.2,
    area: ['60%', '60%'],
    content: $('#importDialogModal')
  });
  // $('#form_import_file').attr('action',url);

  // 确认(提交)上传文件
  $(document).undelegate('.sub_import_file', 'click'); // 注册事件之前先清除以前注册的事件（解决多次调用函数出现事件累加的问题）
  $(document).delegate('.sub_import_file', 'click', function() {
    if($('#import_file_url').val() == '') {
      dialog.notify('请先上传文件');
      return;
    } else {
      fpost(url,'form_import_file',function(data) {
        if (!!data.data && data.data.type == 1) {
          $('#type2_hide').val(data.data.type);
          dialog.confirm('您添写的' + data.data.name + '已存在，是否确定绑定此登陆账号？',function() {
            fpost(url,'form_import_file',refreshDataMethod);
            $('#type2_hide').val('');
            $('#upload_files').val('');
            $('#import_file_url').val('');
            $('#file_name').text('');
          });
        } else {
          refreshDataMethod(data);
          $('#upload_files').val('');
          $('#import_file_url').val('');
          $('#file_name').text('');
        };
      });
    }
  });
}
// H5上传电子表格文件
$(document).delegate('#upload_files', 'change', function() {
  var file = this.files[0];             // 获取input type=file文件所有数据，因为input type=file 可以多选，所以files是个数组
  var fr   = new FileReader();          // H5的新api，文件预览对象
  fr.onloadend = function(e) {          // 当转换完成之后fr.onloadend，执行函数，转换完的数据就是e
    var imgSrc = e.target.result;       // file数据中的图片信息就是e.target.result，这是图片预览地址
    // 提交图片表单
    $('#form_import_file').ajaxSubmit(function(msg) {
      console.log(msg)
      // 成功返回的数据
      if (msg.status==1) {
        var imgSrc = msg.info;
        $('#import_file_url').val(imgSrc);
        $('#file_name').text(file.name);
        // $('#file_name').text($('#upload_files').val());
        dialog.notify('文件上传成功');
      } else {
        dialog.notify('上传失败，请重新上传');
        $('#upload_files').val('');
        $('#import_file_url').val('');
        $('#file_name').text('');
      };
    });
  }
  fr.readAsDataURL(file);               // 把file数据转为DataURL数据
});
// ---------------------------------------- 导入文件 end --------------------------------------------- //

// 上传图片
function uploadImage(id,width,height,url_id,img) {
  $('#'+id).uploadify({
    'swf': '/Public/assets/js/misc/uploadify.swf',
    'uploader' : '/index.php/Admin/Upload/upload_image.html?w='+width+'&h='+height,
    'buttonText': "上传图片",
    'fileTypeExts': "*.png;*.jpg;*.gif;*.jpeg",
    'method': 'post',
    onUploadSuccess: function (file, data, response) {
      var res = JSON.parse(data);
      if (res.status == 1 ) {
        $('#'+url_id).val(res.data.url);
        if($.type(img) !== 'undefined') $('#'+img).attr('src',res.data.url);
        dialog.notify('文件上传成功');
      } else {
        dialog.error(res.info);
      }
    },
    onUploadError: function (file, errorCode, errorMsg, errorString) {
      dialog.error('文件：' + file.name + ' 上传失败: ' + errorString);
    }
  });
}

//------------ table表格信息导出成excel表格 start ------------//
var idTmr;
function getExplorer() {
  var explorer = window.navigator.userAgent;
  // ie
  if (explorer.indexOf("MSIE") >= 0) {
    return 'ie';
  }
  // firefox
  else if (explorer.indexOf("Firefox") >= 0) {
    return 'Firefox';
  }
  // Chrome
  else if(explorer.indexOf("Chrome") >= 0) {
    return 'Chrome';
  }
  // Opera
  else if(explorer.indexOf("Opera") >= 0) {
    return 'Opera';
  }
  // Safari
  else if(explorer.indexOf("Safari") >= 0) {
    return 'Safari';
  }
}
function method(tableid) {
  if(getExplorer()=='ie') {
    var curTbl = document.getElementById(tableid);
    var oXL = new ActiveXObject("Excel.Application");
    var oWB = oXL.Workbooks.Add();
    var xlsheet = oWB.Worksheets(1);
    var sel = document.body.createTextRange();
    sel.moveToElementText(curTbl);
    sel.select();
    sel.execCommand("Copy");
    xlsheet.Paste();
    oXL.Visible = true;
    try {
      var fname = oXL.Application.GetSaveAsFilename("Excel.xls", "Excel Spreadsheets (*.xls), *.xls");
    } catch (e) {
      print("Nested catch caught " + e);
    } finally {
      oWB.SaveAs(fname);
      oWB.Close(savechanges = false);
      oXL.Quit();
      oXL = null;
      idTmr = window.setInterval("Cleanup();", 1);
    }
  } else {
    tableToExcel(tableid)
  }
}
function Cleanup() {
  window.clearInterval(idTmr);
  CollectGarbage();
}
var tableToExcel = (function() {
  var uri  = 'data:application/vnd.ms-excel;base64,',
  template = '<html><head><meta charset="UTF-8"></head><body><table>{table}</table></body></html>',
  base64 = function(s) {return window.btoa(unescape(encodeURIComponent(s)))},
  format = function(s, c) {
    return s.replace(/{(\w+)}/g,
    function(m, p) {return c[p];})
  }
  return function(table, name) {
    if (!table.nodeType) table = document.getElementById(table)
    var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
    window.location.href = uri + base64(format(template, ctx))
  }
})()
//------------ table表格信息导出成excel表格 end ------------//

//------------ 编辑/详情 数据 -----------//
function custom_edit(id,url,params,form_id,modal_id,fnn) {
  if (id>0) {
    fget(url,params,function(res){
      if(!!fnn) {
        fnn(res)
      }
      if (res.data.data) {
        if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
        if (res.status == 1 ) {
          setEditData(res.data.data,modal_id);
        } else {
          dialog.error(res.info);
        }
      } else {
        if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
        if (res.status == 1 ) {
          setEditData(res.data,modal_id);
        } else {
          dialog.error(res.info);
        }
      };
    });
  } else {
    $("#" + form_id)[0].reset();
    layer.open({
      type: 1,
      title: '新增',
      shadeClose: true,
      shade: 0.2,
      area: ['60%', '60%'],
      content:$('#'+modal_id)
    });
  }
}

// 详情数据
function custom_viewDetails(id,url,params,form_id,modal_id,fnn) {
  if (id>0) {
    fget(url,params,function(res){
      if(!!fnn) {
        fnn(res)
      }
      if (res.data.data) {
        if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
        if (res.status == 1 ) {
          setDetailsData(res.data.data,modal_id);
        } else {
          dialog.error(res.info);
        }
      } else {
        if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
        if (res.status == 1 ) {
          setDetailsData(res.data,modal_id);
        } else {
          dialog.error(res.info);
        }
      };
    });
  }
}

// 特殊字段赋值
function setEditDataEx(data){};

// 编辑弹出层
function setEditData(data,modal_id) {
  $.each(data,function(key,value) {
    $("#" + key).val(value);
  });
  layer.open({
    type: 1,
    title: '编辑',
    shadeClose: true,
    shade: 0.2,
    area: ['60%', '60%'],
    content: $('#'+modal_id)
  });
  setEditDataEx(data);
}

// 详情弹出层
function setDetailsData(data,modal_id) {
  $.each(data,function(key,value) {
    $("#" + key).val(value);
  });
  layer.open({
    type: 1,
    title: '详情',
    shadeClose: true,
    shade: 0.2,
    area: ['60%', '60%'],
    content: $('#'+modal_id)
  });
  setEditDataEx(data);
}
//------------ 编辑/详情 数据 end -----------//

//------------ 查看数据 start -----------//
function custom_view(id,url,params,form_id,modal_id) {
  if (id>0) {
    fget(url,params,function(res){
      if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
      if (res.status == 1 ) {
        setViewData(res.data,modal_id);
      } else {
        dialog.error(res.info);
      }
    });
  }
}

function setViewData(data,modal_id) {
  $.each(data,function(key,value) {
    $("#" + key).val(value);
  });

  layer.open({
    type: 1,
    title: '查看',
    shadeClose: true,
    shade: 0.2,
    area: ['60%', '60%'],
    content: $('#'+modal_id)
  });

  setViewDataEx(data);
}
//------------ 查看数据 end -----------//

// 通过点击展开左边栏
$(".side-icon").click(function() {
  var $arrow = $(this).children();
  if($arrow.hasClass("fa-chevron-left")){
    $("#mapBox").addClass("nosidebar");
    $(this).addClass("nosideicon");
    $arrow.attr("class","fa fa-chevron-right");
    $(".map-search").css('left', '320px');
  }else{
    $("#mapBox").removeClass("nosidebar");
    $(this).removeClass("nosideicon");
    $arrow.attr("class","fa fa-chevron-left")
    $(".map-search").css('left', '640px');
  }
});

/////////////////////////////////////页面提交全局函数-start//////////////////////////////////////////////////////
var g_config = {host:''};
var g_loadingIndex = -1;
function postCallBack(data) {
  if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
  g_loadingIndex = -1;
  if( data.status == 1 ) {
    dialog.success(data.info,data.url);
  } else {
    dialog.error(data.info);
  }
}

function postCallBack1(data) {
  if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
  g_loadingIndex = -1;
  if( data.status == 1 ) {
    dialog.success1(data.info,data.url);
  } else {
    dialog.error(data.info);
  }
}

function btnCallbackRefresh(data) {
  if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
  g_loadingIndex = -1;
  if( data.status == 1 ) {
    dialog.success(data.info,function(){ window.location.reload();});
  } else {
    dialog.error(data.info);
  }
}

function btnCallbackConfirmLayerClose(data) {
  if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
  g_loadingIndex = -1;
  if( data.status == 1 ) {
    dialog.success(data.info,function(){ 
      layer.closeAll();
    });
  } else {
    dialog.error(data.info);
  }
}

function btnCallbackRefreshTable1(data) {
  if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
  g_loadingIndex = -1;
  if( data.status == 1 ) {
    dialog.success(data.info,function() {
      search();
      parent.layer.closeAll();
    });
  } else {
    dialog.error(data.info);
  }
}

function btnCallbackRefreshTable2(data) {
  if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
  g_loadingIndex = -1;
  if( data.status == 1 ) {
    dialog.success(data.info,function() {
      search2();
      parent.layer.closeAll();
    });
  } else {
    dialog.error(data.info);
  }
}

function btnCallbackRefreshWindowTopTable1(data) {
  if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
  g_loadingIndex = -1;
  if( data.status == 1 ) {
    dialog.success(data.info,function() {
      window.parent.search();
      parent.layer.closeAll();
    });
  } else {
    dialog.error(data.info);
  }
}

function btnCallbackRefreshWindowTopTable2(data) {
  if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
  g_loadingIndex = -1;
  if( data.status == 1 ) {
    dialog.success(data.info,function() {
      window.parent.search2();
      parent.layer.closeAll();
    });
  } else {
    dialog.error(data.info);
  }
}

function btnCallbackRefreshOut(data) {
  if( data.status == 1 ) {
    dialog.success(data.info,function(){ window.location.href = "/index.php/Admin/Login";});
  } else {
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

function fpost(url,form_id,callback) {
  var data = $('#'+form_id).serialize();
  if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
 // g_loadingIndex = dialog.showLoading();
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

// 退出
function logout() {
  var url = g_config.host + "/index.php/Admin/Login/logout";
  fget(url,'',btnCallbackRefreshOut);
}

$(function() {
  $(window).resize();
  // 左侧导航选中状态
  var activeTxt = $('.breadcrumb li.active').text().replace(/(^\s*)|(\s*$)/g, "");
  $('.sidebar-menu li:not(.header)').each(function(index, el) {
    if ($(this).children('a').children('span').eq(0).text().replace(/(^\s*)|(\s*$)/g, "") == activeTxt) {
      $(this).addClass('active');
    };
    if ($(this).children('a').children('span').eq(0).text().replace(/(^\s*)|(\s*$)/g, "") == '首页') {
      $('.breadcrumb li').removeClass('active');
    };
  });

  // 左侧导航模拟滚动条初始化
  if ($('.slimScroll').length > 0) {
    if ($('.sidebar-menu li').hasClass("active")) { // 判断是否左侧导航有选中的项（防止home页面报错）
      $('.slimScroll').slimScroll({
        height: $('.slimScroll').height(), // 可滚动区域高度
        // height: 'auto',                 // 可滚动区域高度
        width: 'auto',                     // 可滚动区域宽度
        size: '10px',                      // 组件宽度
        color: '#FFF',                     // 滚动条颜色
        position: 'right',                 // 组件位置：left/right
        distance: '0px',                   // 组件与侧边之间的距离
        start: $(".sidebar-menu .active").first(), // 默认滚动位置：top/bottom
        opacity: .4,                       // 滚动条透明度
        alwaysVisible: true,               // 是否 始终显示组件
        disableFadeOut: false,             // 是否 鼠标经过可滚动区域时显示组件，离开时隐藏组件
        railVisible: true,                 // 是否 显示轨道
        railColor: '#eee',                 // 轨道颜色
        railOpacity: .2,                   // 轨道透明度
        railDraggable: true,               // 是否 滚动条可拖动
        railClass: 'slimScrollRail',       // 轨道div类名
        barClass: 'slimScrollBar',         // 滚动条div类名
        wrapperClass: 'slimScrollDiv',     // 外包div类名
        allowPageScroll: true,             // 是否 使用滚轮到达顶端/底端时，滚动窗口
        wheelStep: 10,                     // 滚轮滚动量
        touchScrollStep: 200,              // 滚动量当用户使用手势
        borderRadius: '7px',               // 滚动条圆角
        railBorderRadius: '7px'            // 轨道圆角
      });
    } else {
        $('.slimScroll').slimScroll({
        height: $('.slimScroll').height(), // 可滚动区域高度
        // height: 'auto',                 // 可滚动区域高度
        width: 'auto',                     // 可滚动区域宽度
        size: '10px',                      // 组件宽度
        color: '#FFF',                     // 滚动条颜色
        position: 'right',                 // 组件位置：left/right
        distance: '0px',                   // 组件与侧边之间的距离
        start: 'top',                      // 默认滚动位置：top/bottom
        opacity: .4,                       // 滚动条透明度
        alwaysVisible: true,               // 是否 始终显示组件
        disableFadeOut: false,             // 是否 鼠标经过可滚动区域时显示组件，离开时隐藏组件
        railVisible: true,                 // 是否 显示轨道
        railColor: '#eee',                 // 轨道颜色
        railOpacity: .2,                   // 轨道透明度
        railDraggable: true,               // 是否 滚动条可拖动
        railClass: 'slimScrollRail',       // 轨道div类名
        barClass: 'slimScrollBar',         // 滚动条div类名
        wrapperClass: 'slimScrollDiv',     // 外包div类名
        allowPageScroll: true,             // 是否 使用滚轮到达顶端/底端时，滚动窗口
        wheelStep: 10,                     // 滚轮滚动量
        touchScrollStep: 200,              // 滚动量当用户使用手势
        borderRadius: '7px',               // 滚动条圆角
        railBorderRadius: '7px'            // 轨道圆角
      });
    }
  };
  $('script[data-src]').each(function() {
    $(this).attr('src',$(this).data('src'));
  })
})

function btnCallbackRefreshlayer(data) {
  if( data.status == 1 ) {
    dialog.success(data.info,function(){ parent.window.location.reload();});
  } else {
    dialog.error(data.info);
  }
}

function closes() {
  parent.layer.closeAll();
}

// 搜索树形
function searchNodeAll(treeDemo,searchNodeVal) {
  var zTree       = $.fn.zTree.getZTreeObj(treeDemo);
  var value       = $.trim(searchNodeVal.val());
  var AllNodeList = zTree.transformToArray(zTree.getNodes());
  if (value!='') {
    var searchNodeList = zTree.getNodesByParamFuzzy('name', value);
    for (var i = 0; i < AllNodeList.length; i++) {
      zTree.hideNode(AllNodeList[i]);
    };
    for (var i= 0; i<searchNodeList.length; i++) {
      var parentNodes = searchNodeList[i].getPath();
      var childNodes  = [];
      childNodes = getAllChildrenNodes(searchNodeList[i],childNodes);
      // console.log(childNodes);
      zTree.showNodes(parentNodes);
      zTree.showNodes(childNodes);
    }
  } else {
    zTree.showNodes(AllNodeList);
  };
}
// 获取被搜索的节点的所有子节点
function getAllChildrenNodes(treeNode,result) {
  if (treeNode.isParent) {
    var childrenNodes = treeNode.children;
    if (childrenNodes) {
      for (var i = 0; i < childrenNodes.length; i++) {
        result.push(childrenNodes[i]);
        result = getAllChildrenNodes(childrenNodes[i], result);
      }
    }
  }
  return result;
}
