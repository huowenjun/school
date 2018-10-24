/**
 *
 * @authors ljs (you@example.org)
 * @date    2016-05-07 12:21:48
 * @version $Id$
 */

////------------ 查询条件 获取初始日期和时间 -----------//
function fnGetData(curTime) {
  var date   = new Date(curTime);
  var year   = date.getFullYear();
  var month  = date.getMonth() + 1;
  var day    = date.getDate();
  var hour   = date.getHours();
  var minute = date.getMinutes();
  var second = date.getSeconds();
  month       = month>9?month:'0'+month;
  day        = day>9?day:'0'+day;
  hour       = hour>9?hour:'0'+hour;
  minute     = minute>9?minute:'0'+minute;
  second     = second>9?second:'0'+second;
  return {
    'date':year + '-' + month + '-' + day,
    'time':hour + ':' + minute +':' +second
  }
}

// 开始日期时间 结束日期时间
var start = fnGetData(new Date().getTime() - 7 * 24 * 3600 * 1000);
var end   = fnGetData(new Date());
//console.table([['开始时间',start.date+' '+start.time],[ '结束时间',end.date+' '+end.time]])

$('.navbar-form #sdate1').val(start.date); // 开始日期
$('.navbar-form #stime1').val(start.time); // 开始时间
$('.navbar-form #edate1').val(end.date);   // 结束日期
$('.navbar-form #etime1').val(end.time);   // 结束时间
$('.sdate-time').val(start.date + ' ' + start.time); // 开始日期时间
$('.edate-time').val(end.date + ' ' + end.time);     // 结束日期时间
//------------ 查询条件 获取初始日期和时间 end -----------//

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

  // 删除
  $('#remove').click(function () {
    del();
  });

  // (新增/编辑)重置
  $(document).delegate('.reset', 'click', function() {
    var modalTitleTxt = $('.layui-layer-title').text();
    console.log(modalTitleTxt)
    if (modalTitleTxt == '新增') {
      $('#modalWindow').find('.form-control').val('');
    } else {
      var row = $('#modalWindow').data('thisData');
      for(var key in row) {
        console.log(key+':'+row[key]);
        $('#' + key).val(row[key]);
      }
    };
  });

  // 查看
  $('.js-custom-table-data').delegate('.view', 'click', function(event) {
    var aInfo = $('.js-custom-table-data').bootstrapTable('getSelections');
    for (var i = 0; i < aInfo.length; i++) {
      console.group('ID = '+aInfo[i]['id']+':');
      for(var key in aInfo[i]) {
        console.log(key+':'+aInfo[i][key]);
      }
      console.groupEnd();
    };
  })
  window.operateEvents = {
    // 点击查看
    'click .view': function (e, value, row, index) {
      console.log(row)
      fnpopupTableData(row)
    },
    // 点击编辑
    'click .edit': function (e, value, row, index) {
      console.log(row)
      var key = Object.keys(row)[0]; // 主键第一个值
      edit(row[key]);
      $('#modalWindow').data('thisData',row);
    }
  };

  // 点击查询
  $('.query').click(function(){search();});

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


  // 启用
  $('.enable').click(function() {
    enable(1);
  });

  // 禁用
  $('.disable').click(function() {
    enable(2);
  });

  // 启用
  $('.activation').click(function() {
    activation(1);
  });

  // 禁用
  $('.disactivation').click(function() {
    activation(2);
  });

  // 点击保存
  $(document).delegate('.save', 'click', function() {
    save();
  });

});

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
function initSelect(id,url,select_id,params,all,title) {
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
        if(typeof(title) == 'undefined') title = '全部';
        if(all) $("#"+id).append('<option value="0">'+title+'</option>');
        //var len = res.data.length;
        $.each(res.data, function(key, value){  
            console.log(key, value);
            $("#"+id).append('<option value="'+key+'">'+value+'</option>');  
        });

        // for(var i=0; i < len; i ++){
        //   $("#"+id).append('<option value="'+res.data[i].id+'">'+res.data[i].value+'</option>');
        // };
      } else {
        dialog.error(res.info);
      }
    }
  });
}
//------------ 初始化下拉框数据 end -----------//

//------------ 获取表格数据 -----------//
function queryList(url,params) {
  $.ajax({
    url:url,
    data:params.data,
    type:'post',
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
        dialog.error(res.message);
      }
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


//------------ table (增删改查)功能 -----------//

// 自定义编辑表格中的操作内容
function operateFormatter(value, row, index) {
  return [
    '<button type="button" class="btn btn-primary edit" data-color="#39B3D7" data-opacity="0.95">',
    '<span class="glyphicon glyphicon-edit"></span> 编辑',
    '</button>',
    '<button type="button" class="btn btn-primary view" data-color="#39B3D7" data-opacity="0.95">',
    '<span class="glyphicon glyphicon-eye-open"></span> 查看',
    '</button>'
  ].join('');
}
// 自定义编辑表格中的查看按钮
function viewFormatter(value, row, index) {
  return [
    '<button type="button" class="btn btn-primary view" data-color="#39B3D7" data-opacity="0.95">',
    '<span class="glyphicon glyphicon-eye-open"></span> 查看',
    '</button>'
  ].join('');
}
// 自定义编辑表格中的编辑按钮
function editFormatter(value, row, index) {
  return [
    '<button type="button" class="btn btn-primary edit" data-color="#39B3D7" data-opacity="0.95">',
    '<span class="glyphicon glyphicon-edit"></span> 编辑',
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



//显示导入文件窗口
function showImportModal(url){
  if($("importDialogModal").length == 0) {
    var html = '';
    html += '  <form class="form_import_file" id="form_import_file" action="" enctype="multipart/form-data" method="post" >';
    html += '  <div class="modal fade bs-example-modal-ms" id="importDialogModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">';
    html += '  <div class="modal-dialog modal-md" role="document">';
    html += '  <div class="modal-content">';
    html += '  <div class="modal-header">';
    html += '  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
    html += '  <h4 class="modal-title">导入</h4>';
    html += '  </div>';
    html += '  <div class="modal-body">';
    html += '  <div class="container-fluid">';
    // html+='  ... ';
    html += '  <div class="">';
    html += '  <span>上传文件名</span>';
    html += '  <input type="file" id="import_file" multiple />';
    html += '  <input type="hidden" id="import_file_url" name="file_url"/>';
    html += '  </label>';
    html += '  <span>（上传文件必须是xlsx格式）</span>';
    html += '  <div id="file_name" class="file_name"></div>';
    html += '  </div>';

    html += '  </div>';
    html += '  </div>';
    html += '  <div class="modal-footer" style="text-align:center;">';
    html += '  <button type="button" class="btn btn-primary sub_import_file"><span class="glyphicon glyphicon-ok"></span> 导入</button>';
    html += '  <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close"><span class="glyphicon glyphicon-off"></span> 关闭</button>';
    html += '  </div>';
    html += '  </div>';
    html += '  </div>';
    html += '  </div>';
    html += '  </form>';
    $('body').append(html);
    //上传电子表格文件
    $('#import_file').uploadify({
      'swf': '/Public/assets/js/misc/uploadify.swf',
      'uploader' : '/index.php/Audit/Common/upload_excel',
      'buttonText': "设备文件上传",
      'fileTypeExts': "*.xlsx",
      'method': 'post',
      onUploadSuccess: function (file, data, response) {
        var res = JSON.parse(data);
        if (res.status == 1 ) {
          $('#import_file_url').val(res.data.url);
          dialog.success('文件上传成功');
        } else {
          dialog.error(res.info);
        }
      },
      'onUploadError': function (file, errorCode, errorMsg, errorString) {
        dialog.error('文件：' + file.name + ' 上传失败: ' + errorString);
      }
    });
  }
  $('#importDialogModal').modal('show');
  $('#form_import_file').attr('action',url);
}
// 确认(提交)上传文件
$(document).delegate('.sub_import_file', 'click', function() {
  if($('#import_file_url').val() == ''){
    dialog.notify('请先上传文件');
    return;
  }
  var url = $('#form_import_file').attr('action');
  fpost(url,'form_import_file',btnCallbackRefresh);
});


// 查看弹出框高度
function fnModalHeight() {
  var windowH = $(window.parent.parent.document).height();
  $('.modal .container-fluid',window.parent.parent.document).css({
    'max-height': (windowH - 260) + 'px',
    'overflow': 'hidden',
    'overflow-y': 'auto'
  });
}
// fnModalHeight();

// 当前页面(新增/编辑) 弹出框高度
function fncurrentPageModalHeight() {
  var windowH = $(window).height();
  $('.modal .container-fluid').css({
    'max-height': (windowH - 260) + 'px',
    'overflow': 'hidden',
    'overflow-y': 'auto'
  });
}
// fncurrentPageModalHeight();

// 查看 弹出层获取的table表格数据
function fnpopupTableData(obj) {
  $('#viewDialogModal').remove();
  $('#viewDialogModal',window.parent.document).remove();
  var oTits = $('.keep-open .dropdown-menu input[data-field]');
  var oInfo = {};
  oTits.each(function(index, el) {
    var text    = $(this).parent().text();
    var name    = $(this).attr('data-field');
    oInfo[text] = obj[name];
  });
  // console.log(oInfo)
  var html = '';
  html+='  <div class="modal fade bs-example-modal-ms" id="viewDialogModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">';
  html+='  <div class="modal-dialog modal-md" role="document">';
  html+='  <div class="modal-content">';
  html+='  <div class="modal-header">';
  html+='  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
  html+='  <h4 class="modal-title" id="dialog_title">查看</h4>';
  html+='  </div>';
  html+='  <div class="modal-body">';
  html+='  <div class="container-fluid">';
  // html+='  ... ';

  html+='  <form class="form-horizontal" id="formSite" action="" method="post" onsubmit="return false;">';
  html+='  <div class="row">';
  html+='  <div class="col-md-12">';
  html+='  <fieldset class="stepy-step">';
  html+='  <legend>全部详情</legend>';
  html+='  <div class="row">';
  html+='  <div class="col-md-12">';

  for (var key in oInfo) {
    if (key.trim() != '操作' && key.trim() != '' && key.trim() != '查看') {
      // if (key.trim() == '状态') {
      if (oInfo[key].search('<') >= 0) {
        html += '  <div class="form-group"><label class="col-md-2 control-label">' + key + '</label> <div class="col-md-10 info">' + oInfo[key] + '</div></div>'
      } else {
        html += '  <div class="form-group"><label class="col-md-2 control-label">' + key + '</label> <div class="col-md-10 info"><input type="text" class="form-control" value="' + oInfo[key] + '" /></div></div>'
      };
    }
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
  html+='  <button type="button" class="btn btn-default" data-dismiss="modal" aria-label="Close"><span class="glyphicon glyphicon-off"></span> 关闭</button>';
  html+='  </div>';
  html+='  </div>';
  html+='  </div>';
  html+='  </div>';
  $('body',window.parent.parent.document).append(html);
  $('#viewDialogModal input',window.parent.parent.document).attr('readonly',true);
  fnModalHeight();
  $('#viewDialogModal',window.parent.parent.document).modal('show');
}
//------------ table (增删改查)功能 end -----------//

//------------ 编辑数据 -----------//
function custom_edit(id,url,params,form_id,modal_id) {
  if (id>0) {
    fget(url,params,function(res){
      if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
      if (res.status == 1 ) {
        setEditData(res.data,modal_id);
      } else {
        dialog.error(res.info);
      }
    });
  } else {
    $("#" + form_id)[0].reset();
    layer.open({
      type: 1,
      title: '新增',
      shadeClose: true,
      shade: 0.2,
      area: ['80%', '80%'],
      content:$('#'+modal_id)
    });
  }
}
//特殊字段赋值
function setEditDataEx(data){};

function setEditData(data,modal_id) {
  $.each(data,function(key,value) {
    $("#" + key).val(value);
  });

  layer.open({
    type: 1,
    title: '编辑',
    shadeClose: true,
    shade: 0.2,
    area: ['80%', '80%'],
    content: $('#'+modal_id)
  });

  setEditDataEx(data);
}
//------------ 编辑数据 end -----------//

//------------ 点击简称查看数据 start -----------//

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
    area: ['80%', '80%'],
    content: $('#'+modal_id)
  });

  setViewDataEx(data);
}

function setViewDataEx(data){};
//------------ 点击简称查看数据 end -----------//

//上传图片
function uploadImage(id,width,height,url_id,img){
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
    'onUploadError': function (file, errorCode, errorMsg, errorString) {
      dialog.error('文件：' + file.name + ' 上传失败: ' + errorString);
    }
  });
}

//点击简称后颜色字段,鼠标移动颜色提示功能
function colorTip() {
 $(".colortip").mouseover(function(e){
    this.myTitle = this.title;
    this.title = "";  
    var colortip = "<div id='colorTip'>"+ this.myTitle +"</div>"; 
    $("#viewFormWindow").append(colortip);  
    $("#colorTip")
      .css({
        "top": (e.pageY)-100+"px",
        "left": (e.pageX)-100+ "px"
      }).show("fast");  
     }).mouseout(function(){   
    this.title = this.myTitle;
    $("#colorTip").remove(); 
    }).mousemove(function(e){
    $("#colorTip")
      .css({
        "top": (e.pageY)-100+ "px",
        "left": (e.pageX)-100+ "px"
    });
  });
};


/*使用layer 弹出窗口  提交表单 关闭*/
function btnCallbackRefresh1(data) {
  if( data.code == 0 ) {
    showMessage('提示',data.message,function(){parent.window.location.reload();});
    //parent.layer.closeAll();
  } else {
    showMessage('错误',data.message);
  }
}


function closes () {
    parent.layer.closeAll();
  }

