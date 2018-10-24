<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="/Public/bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/Public/dist/font/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="/Public/dist/font/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="/Public/dist/css/AdminLTE.min.css">

  <!-- 表格 css -->
  <link rel="stylesheet" href="/Public/plugins/bootstrap-table/bootstrap-table.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="/Public/plugins/iCheck/all.css">
  <!-- switch css-->
  <link rel="stylesheet" href="/Public/plugins/switch/bootstrap-switch.css">
  <!-- datetimepicker css--> 
  <link rel="stylesheet" href="/Public/plugins/datetimepicker/jquery.datetimepicker.css">
  <!-- ztree style  -->
  <link rel="stylesheet" href="/Public/plugins/ztree/zTreeStyle.css">
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="/Public/dist/css/skins/_all-skins.min.css" />
  <!-- AdminLTE皮肤默认选择的skin-blue。也可以选择其他的皮肤。确保你应用皮肤类body标签更改生效 -->
  <link rel="stylesheet" href="/Public/dist/css/skins/skin-blue.min.css" />
  <!-- 自定义公共css -->
  <link rel="stylesheet" href="/Public/dist/css/custom.css" />
	<title>修改密码</title>
</head>
<body>
	<div class="shade" style="" id="modalPassword">
    <div class="modal-body">
      <div class="container-fluid">
        <form class="form-horizontal" action="" method="post" onsubmit="return false" id="edit_password">
        <input type="hidden" name="user_id" value="<?php echo ($_GET['user_id']); ?>">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="col-md-3 control-label" for="ypassword"><span class="xing">*</span> 原密码：</label>
                <div class="col-md-8">
                  <input type="password" placeholder="请输入原密码" id="ypassword" name="ypassword" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label" for="password"><span class="xing">*</span> 新密码：</label>
                <div class="col-md-8">
                  <input type="password" placeholder="请输入新密码" id="password" name="password" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label" for="right_password"><span class="xing">*</span> 确认密码：</label>
                <div class="col-md-8">
                  <input type="password" placeholder="请输入确认密码" id="right_password" name="right_password" class="form-control" />
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
    <div class="modal-footer" style="text-align:center;">
      <button type="button" class="btn btn-success btn-sm save">
        <span class="glyphicon glyphicon-save"></span> 保存
      </button>
      <button type="button" class="btn btn-default btn-sm " onclick="close1()">
        <span class="glyphicon glyphicon-remove"></span> 取消
      </button>
    </div>
  </div>
</body>
<!-- jQuery 2.2.3 -->
<script type="text/javascript" src="/Public/plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script type="text/javascript" src="/Public/bootstrap/js/bootstrap.min.js"></script>

<script type="text/javascript" src="/Public/plugins/layer/layer.js"></script>
<script type="text/javascript" src="/Public/dist/js/dialog.js"></script>
<script type="text/javascript" src="/Public/dist/js/app.js"></script>

<script type="text/javascript" src="/Public/plugins/datetimepicker/jquery.datetimepicker.full.js"></script>
<script type="text/javascript" src="/Public/plugins/switch/bootstrap-switch.js"></script>
<!-- AdminLTE App -->
<script type="text/javascript" src="/Public/dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script type="text/javascript" src="/Public/dist/js/demo.js"></script>
<!-- Slimscroll -->
<script type="text/javascript" src="/Public/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- 表格功能 -->
<script type="text/javascript" src="/Public/plugins/bootstrap-table/bootstrap-table.js"></script>
<script type="text/javascript" src="/Public/plugins/bootstrap-table/bootstrap-table-zh-CN.js"></script>
<!-- ztree -->
<script type="text/javascript" src="/Public/plugins/ztree/jquery.ztree.all.js"></script>
<!-- iCheck 1.0.1 -->
<script type="text/javascript" src="/Public/plugins/iCheck/icheck.min.js"></script>
<!-- 自定义公共js -->
<script type="text/javascript" src="/Public/dist/js/custom.js"></script>
<script type="text/javascript">
	function close1(){
		parent.layer.closeAll(); 
	}
	function btnCallbackRefresh(data) {
	  if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
	  g_loadingIndex = -1;
	  if( data.status == 1 ) {
	    dialog.success(data.info,function(){ parent.window.location.href=data.url;});
	  } else {
	    dialog.error(data.info);
	  }
	}
	function save() {
    if ($('#right_password').val() === $('#password').val()) { // 验证确认密码是否与新密码输入内容一致
      var url = g_config.host + '/index.php/Admin/Login/edit_password';
      fpost(url,'edit_password',btnCallbackRefresh);
    } else {
      dialog.error('确认密码和新密码输入不一致，请重新输入！');
    };
	}
</script>
</html>