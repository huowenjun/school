<?php if (!defined('THINK_PATH')) exit();?><!-- Main Header -->
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>信平台安防校园管理系统</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="/Public/bootstrap/css/bootstrap.min.css" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="/Public/dist/font/font-awesome/4.5.0/css/font-awesome.min.css" />
  <!-- Ionicons -->
  <link rel="stylesheet" href="/Public/dist/font/ionicons/2.0.1/css/ionicons.min.css" />
  <!-- Theme style -->
  <link rel="stylesheet" href="/Public/dist/css/AdminLTE.min.css" />
  <!-- AdminLTE Skins. Choose a skin from the css/skins
       folder instead of downloading all of them to reduce the load. -->
  <link rel="stylesheet" href="/Public/dist/css/skins/_all-skins.min.css" />
  <!-- AdminLTE皮肤默认选择的skin-blue。也可以选择其他的皮肤。确保你应用皮肤类body标签更改生效 -->
  <link rel="stylesheet" href="/Public/dist/css/skins/skin-blue.min.css" />
  <!-- 自定义公共css -->
  <link rel="stylesheet" href="/Public/dist/css/custom.css" />
  <!-- 表格 css -->
  <link rel="stylesheet" href="/Public/plugins/bootstrap-table/bootstrap-table.css" />
  <!-- 开关按钮 css -->
  <link rel="stylesheet" href="/Public/plugins/switch/bootstrap-switch.css" />
  <!-- ztree -->
  <link rel="stylesheet" href="/Public/plugins/ztree/zTreeStyle.css" />
  <!-- 日期 时间 -->
  <link rel="stylesheet" href="/Public/plugins/datetimepicker/jquery.datetimepicker.css" />
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="/Public/plugins/iCheck/all.css" />
</head>
<body class="skin-blue sidebar-mini">
<div class="wrapper">
  <!-- Main Header -->
  <header class="main-header">

    <!-- Logo -->
    <a href="index.html" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>安防校园</b>管理系统</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>安防校园</b>管理系统</span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">切换导航</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- Messages: style can be found in dropdown.less-->
          <!-- <li class="dropdown messages-menu"> -->
            <!-- Menu toggle button -->
            <!-- <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-envelope-o"></i>
              <span class="label label-success">4</span>
            </a>
          </li> -->
          <!-- /.messages-menu -->

          <!-- Notifications Menu -->
          <!-- <li class="dropdown notifications-menu"> -->
            <!-- Menu toggle button -->
            <!-- <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
              <span class="label label-warning">10</span>
            </a>
          </li> -->
          <!-- Tasks Menu -->
          <!-- <li class="dropdown tasks-menu"> -->
            <!-- Menu Toggle Button -->
            <!-- <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-flag-o"></i>
              <span class="label label-danger">9</span>
            </a>
          </li> -->
          <!-- User Account Menu -->
          <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <!-- The user image in the navbar-->
              <img src="/Public/dist/img/user.png" class="user-image" alt="User Image" />
              <!-- hidden-xs hides the username on small devices so only the image appears. -->
              <span class="hidden-xs"><?php echo ($name); ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- The user image in the menu -->
              <li class="user-header">
                <img src="/Public/dist/img/user.png" class="img-circle" alt="User Image" />
                <p>
                  <?php echo ($username); ?> - <?php echo ($name); ?>
                  <!-- <small>11111111@qq.com</small> -->
                </p>
              </li>
              <!-- Menu Body -->

              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                <input type="hidden" name="userid" id="userid" value="<?php echo ($userid); ?>">
                  <a href="#" class="btn btn-default btn-flat" id="changePassword">修改密码</a>
                </div>
                <div class="pull-right">
                  <a href="#" class="btn btn-default btn-flat" onclick="logout();">退出</a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
          <li>
           <a href="/index.php/Admin/Help/Index"  target="_black">帮助中心</a>
          </li>
          <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li>
        </ul>
      </div>
    </nav>
  </header>

  <!-- 修改密码 弹出层 -->
<!--   <div class="shade" style="" id="modalPassword">
    <div class="modal-body">
      <div class="container-fluid">
        <form class="form-horizontal" action="" method="post" onsubmit="return false" id="">
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                <label class="col-md-3 control-label" for=""><span class="xing">*</span> 原密码：</label>
                <div class="col-md-8">
                  <input type="text" placeholder="请输入原密码" id="" name="" class="form-control" />
                </div>
              </div>
              <div class="form-group">
                <label class="col-md-3 control-label" for=""><span class="xing">*</span> 新密码：</label>
                <div class="col-md-8">
                  <input type="text" placeholder="请输入新密码" id="" name="" class="form-control" />
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
      <button type="button" class="btn btn-default btn-sm layui-layer-close">
        <span class="glyphicon glyphicon-remove"></span> 取消
      </button>
    </div>
  </div>
 -->
<!--Main Sidebar-->
<style type="text/css">
  .tex{
    width: 100%;height: 100%;text-align: center;margin-top: 18%;
  }
</style>
<!DOCTYPE html>
  <!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      <!-- Sidebar user panel (optional) -->
      <!-- <div class="user-panel">
        <div class="pull-left image">
          <img src="/Public/dist/img/user.png" class="img-circle" alt="User Image" />
        </div>
        <div class="pull-left info">
          <p><?php echo ($name); ?></p>
          <a href="#"><i class="fa fa-circle text-success"></i> 在线</a>
        </div>
      </div> -->

      <!-- search form (Optional) -->
      <!-- <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="搜索...">
          <span class="input-group-btn">
            <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
            </button>
          </span>
        </div>
      </form> -->
      <!-- /.search form -->

      <!-- Sidebar Menu -->
      <div class="slimScroll js_slimScroll">
      <?php echo ($ruleList); ?>
        <!-- <ul class="sidebar-menu">
          <li class="header">校务管理</li>
          <li>
            <a href="/index.php/Admin/School/SchInformation/index">
              <i class="fa fa-circle-o"></i>
              <span>学校信息</span>
            </a>
          </li>
          <li>
            <a href="/index.php/Admin/School/SchArea/index">
              <i class="fa fa-circle-o"></i>
              <span>校区管理</span>
            </a>
          </li>
          <li>
            <a href="/index.php/Admin/School/Dept/index">
              <i class="fa fa-circle-o"></i>
              <span>部门管理</span>
            </a>
          </li>
          <li>
            <a href="/index.php/Admin/School/Grade/index">
              <i class="fa fa-circle-o"></i>
              <span>年级管理</span>
            </a>
          </li>
          <li>
            <a href="/index.php/Admin/School/Course/index">
              <i class="fa fa-circle-o"></i>
              <span>课程管理</span>
            </a>
          </li>
          <li>
            <a href="/index.php/Admin/School/Class/index">
              <i class="fa fa-circle-o"></i>
              <span>班级管理</span>
            </a>
          </li>
          <li>
            <a href="/index.php/Admin/School/Student/index">
              <i class="fa fa-circle-o"></i>
              <span>学生管理</span>
            </a>
          </li>
          <li>
            <a href="/index.php/Admin/School/Teacher/index">
              <i class="fa fa-circle-o"></i>
              <span>教师管理</span>
            </a>
          </li>
        </ul>

        <ul class="sidebar-menu">
          <li class="header">家校互动</li>
          <li>
            <a href="/index.php/Admin/Parent/Online/index">
              <i class="fa fa-circle-o"></i>
              <span>在线咨询</span>
            </a>
          </li>
          <li>
            <a href="/index.php/Admin/Parent/Homework/index">
              <i class="fa fa-circle-o"></i>
              <span>作业管理</span>
            </a>
          </li>
          <li>
            <a href="/index.php/Admin/Parent/Reward/index">
              <i class="fa fa-circle-o"></i>
              <span>奖励管理</span>
            </a>
          </li>
          <li>
            <a href="/index.php/Admin/Parent/Examination/index">
              <i class="fa fa-circle-o"></i>
              <span>考试管理</span>
            </a>
          </li>
          <li>
            <a href="/index.php/Admin/Parent/Result/index">
              <i class="fa fa-circle-o"></i>
              <span>成绩管理</span>
            </a>
          </li>
          <li>
            <a href="/index.php/Admin/Parent/Comment/index">
              <i class="fa fa-circle-o"></i>
              <span>评语管理</span>
            </a>
          </li>
          <li>
            <a href="/index.php/Admin/Parent/Timetable/index">
              <i class="fa fa-circle-o"></i>
              <span>课程表管理</span>
            </a>
          </li>
          <li>
            <a href="/index.php/Admin/Parent/MyMessage/index">
              <i class="fa fa-circle-o"></i>
              <span>我的消息</span>
            </a>
          </li>
          <li>
            <a href="/index.php/Admin/Parent/Message/index">
              <i class="fa fa-circle-o"></i>
              <span>短信管理</span>
            </a>
          </li>
        </ul>

        <ul class="sidebar-menu">
          <li class="header">电子学生证设置</li>
          <li>
            <a href="/index.php/Admin/StuCard/SthCardSet/index">
              <i class="fa fa-circle-o"></i>
              <span>学生证设置</span>
            </a>
          </li>
        </ul>

        <ul class="sidebar-menu">
          <li class="header">考勤管理</li>
          <li>
            <a href="/index.php/Admin/Atnd/RealTime/index">
              <i class="fa fa-circle-o"></i>
              <span>实时签到</span>
            </a>
          </li>
          <li>
            <a href="/index.php/Admin/Atnd/AtndRecord/index">
              <i class="fa fa-circle-o"></i>
              <span>考勤记录</span>
            </a>
          </li>
          <li>
            <a href="/index.php/Admin/Atnd/AtndManage/index">
              <i class="fa fa-circle-o"></i>
              <span>考勤规则</span>
            </a>
          </li>
          <li>
            <a href="/index.php/Admin/Atnd/LeaveManage/index">
              <i class="fa fa-circle-o"></i>
              <span>请假管理</span>
            </a>
          </li>
        </ul>

        <ul class="sidebar-menu">
          <li class="header">安全管理</li>
          <li>
            <a href="/index.php/Admin/Scy/Monitor/index">
              <i class="fa fa-circle-o"></i>
              <span>实时监控</span>
            </a>
          </li>
          <li>
            <a href="/index.php/Admin/Scy/TrackPlayback/index">
              <i class="fa fa-circle-o"></i>
              <span>足迹回放</span>
            </a>
          </li>
          <li>
            <a href="/index.php/Admin/Scy/SafeArea/index">
              <i class="fa fa-circle-o"></i>
              <span>安全区域</span>
            </a>
          </li>
          <li>
            <a href="/index.php/Admin/Scy/WarnInfo/index">
              <i class="fa fa-circle-o"></i>
              <span>报警信息</span>
            </a>
          </li>
        </ul>

        <ul class="sidebar-menu">
          <li class="header">设备管理</li>
          <li>
            <a href="/index.php/Admin/Device/DeviceManage/index">
              <i class="fa fa-circle-o"></i>
              <span>设备管理</span>
            </a>
          </li>
        </ul>

        <ul class="sidebar-menu">
          <li class="header">系统管理</li>
          <li>
            <a href="/index.php/Admin/Sys/Users/index">
              <i class="fa fa-circle-o"></i>
              <span>用户管理</span>
            </a>
          </li>
          <li>
            <a href="/index.php/Admin/Sys/Group/index">
              <i class="fa fa-circle-o"></i>
              <span>角色管理</span>
            </a>
          </li>
          <li>
            <a href="/index.php/Admin/Sys/Rule/index">
              <i class="fa fa-circle-o"></i>
              <span>角色权限</span>
            </a>
          </li>
          <li>
            <a href="/index.php/Admin/Sys\AdminUser/index">
              <i class="fa fa-circle-o"></i>
              <span>新建系统用户</span>
            </a>
          </li>
          <li>
            <a href="/index.php/Admin/Sys/Log/index">
              <i class="fa fa-circle-o"></i>
              <span>日志管理</span>
            </a>
          </li>
        </ul>

        <ul class="sidebar-menu">
          <li class="header">学校管理</li>
          <li>
            <a href="/index.php/Admin/School/SchManage/index">
              <i class="fa fa-circle-o"></i>
              <span>学校管理</span>
            </a>
          </li>
        </ul> -->
      </div>
    </section>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div id="content-wrapper" class="content-wrapper" >
    <!-- Content Header (Page header) -->
    <?php if(($user_type != 9) ): ?><section class="content-header">
        <ol class="breadcrumb">
          <li class="active"><i class="fa fa-home"></i> 首页</li>
        </ol>
      </section>

    <!-- Main content -->
      <section class="content"> 
        <!-- Your Page Content Here -->
        <div class="row">
          <div class="col-md-12">
            <div class="box box-primary">
              <div class="box-header with-border">
                <h3 class="box-title">安防校园</h3>
                <div class="box-tools pull-right">
                  <button type="button" class="btn btn-block btn-primary btn-sm js_edit">
                    <i class="fa fa-edit"></i> 编辑
                  </button>
                </div>
                <!-- /.box-tools -->
              </div>
              <!-- /.box-header -->
              <div class="box-body" id="modalWindow">
                <form class="form-horizontal js_form_val" action="" method="post" onsubmit="return false" id="formWindow">
                  <input class="js_txt" type="hidden" name="s_id" id="s_id" />
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="name" class="col-sm-4 control-label"><span class="xing">*</span> 学校名称：</label>
                        <div class="col-sm-8 control-div">
                          <input type="text" class="form-control js_val" id="name" name="name" value="<?php echo ($School['name']); ?>" placeholder="学校名称" />
                          <div class="js_txt txt"><?php echo ($School['name']); ?></div>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="main_zrr" class="col-sm-4 control-label"><span class="xing">*</span> 主负责人：</label>
                        <div class="col-sm-8 control-div">
                          <input type="text" class="form-control js_val" id="main_zrr" name="main_zrr" value="<?php echo ($School['main_zrr']); ?>" placeholder="主负责人" />
                          <div class="js_txt txt"><?php echo ($School['main_zrr']); ?></div>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="sub_zzr" class="col-sm-4 control-label"><span class="xing">*</span> 副负责人：</label>
                        <div class="col-sm-8 control-div">
                          <input type="text" class="form-control js_val" id="sub_zzr" name="sub_zzr" value="<?php echo ($School['sub_zzr']); ?>" placeholder="副负责人" />
                          <div class="js_txt txt"><?php echo ($School['sub_zzr']); ?></div>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="tel" class="col-sm-4 control-label"><span class="xing">*</span> 电话（内线）：</label>
                        <div class="col-sm-8 control-div">
                          <input type="text" class="form-control js_val" id="tel" name="tel" value="<?php echo ($School['tel']); ?>" placeholder="格式：0427-43275670 或 010-23237788" />
                          <div class="js_txt txt"><?php echo ($School['tel']); ?></div>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="memo" class="col-sm-4 control-label"><span class="xing"></span> 备注：</label>
                        <div class="col-sm-8 control-div">
                          <textarea class="form-control js_val" id="memo" name="memo" value="<?php echo ($School['memo']); ?>" placeholder="备注"></textarea>
                          <div class="js_txt txt"><?php echo ($School['memo']); ?></div>
                        </div>
                      </div>
                    </div>
                    <!-- /.col -->

                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="build_time" class="col-sm-4 control-label"><span class="xing">*</span> 成立时间：</label>
                        <div class="col-sm-8 control-div">
                          <input type="text" class="form-control dataTime js_val" id="build_time" value="<?php echo ($School['build_time']); ?>" name="build_time" placeholder="成立时间" />
                          <div class="js_txt txt"><?php echo ($School['build_time']); ?></div>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="main_phone" class="col-sm-4 control-label"><span class="xing">*</span> 主负责人电话：</label>
                        <div class="col-sm-8 control-div">
                          <input type="text" class="form-control js_val" id="main_phone" name="main_phone" value="<?php echo ($School['main_phone']); ?>" placeholder="主负责人电话" />
                          <div class="js_txt txt"><?php echo ($School['main_phone']); ?></div>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="sub_phone" class="col-sm-4 control-label"><span class="xing">*</span> 副负责人电话：</label>
                        <div class="col-sm-8 control-div">
                          <input type="text" class="form-control js_val" id="sub_phone" name="sub_phone" value="<?php echo ($School['sub_phone']); ?>" placeholder="副负责人电话" />
                          <div class="js_txt txt"><?php echo ($School['sub_phone']); ?></div>
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="address" class="col-sm-4 control-label"><span class="xing">*</span> 地址：</label>
                        <div class="col-sm-8 control-div">
                          <input type="text" class="form-control js_val" id="address" name="address" value="<?php echo ($School['address']); ?>" placeholder="地址" />
                          <div class="js_txt txt"><?php echo ($School['address']); ?></div>
                        </div>
                      </div>
                    </div>
                    <!-- /.col -->
                  </div>
                </form>
              </div>
              <!-- /.box-body -->
              <div class="box-footer text-center" style="display:none">
                <button type="button" class="btn btn-primary btn-sm save">
                  <i class="fa fa-save"></i> 保存
                </button>
                <button type="button" class="btn btn-default btn-sm cancel">
                  <i class="fa fa-close"></i> 取消
                </button>
              </div>
              <!-- /.box-footer -->
            </div>
          </div>
        </div>
      </section><?php endif; ?>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <!-- Main Footer -->
  <!DOCTYPE html>

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs"></div>
    <!-- Default to the left -->
    <strong>Copyright &copy; <?php echo date('Y')?> <a href="#">信平台安防校园管理系统</a>，</strong> 版权所有。
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
      <li class="tab-pane active">
        <a href="#control-sidebar-theme-demo-options-tab" data-toggle="tab"><i class="fa fa-wrench"></i></a>
      </li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
      <!-- Home tab content -->
      <div class="tab-pane active" id="control-sidebar-home-tab"></div>
      <!-- Stats tab content -->
    </div>
    <!-- /.tab-pane -->
  </aside>
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>

</div>
<!-- ./wrapper -->
<!-- jQuery 2.2.3 -->
<script type="text/javascript" src="/Public/plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script type="text/javascript" src="/Public/bootstrap/js/bootstrap.min.js"></script>
<!-- Slimscroll -->
<script type="text/javascript" src="/Public/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script type="text/javascript" src="/Public/plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script type="text/javascript" data-src="/Public/dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script type="text/javascript" data-src="/Public/dist/js/demo.js"></script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
<!-- cookie -->
<script type="text/javascript" src="/Public/dist/js/jquery.cookie.js"></script>

<!-- 自定义公共js -->
<script type="text/javascript" src="/Public/dist/js/custom.js"></script>
<script type="text/javascript" src="/Public/dist/js/dialog.js"></script>

<!-- 表格功能 -->
<script type="text/javascript" src="/Public/plugins/bootstrap-table/bootstrap-table.js"></script>
<script type="text/javascript" src="/Public/plugins/bootstrap-table/bootstrap-table-zh-CN.js"></script>

<!-- 表格导出数据 -->
<script type="text/javascript" src="/Public/plugins/bootstrap-table/bootstrap-table-export.js"></script>
<script type="text/javascript" src="/Public/plugins/bootstrap-table/tableExport.js"></script>

<!-- 开关按钮 -->
<script type="text/javascript" src="/Public/plugins/switch/bootstrap-switch.js"></script>

<!-- ztree -->
<script type="text/javascript" src="/Public/plugins/ztree/jquery.ztree.all.js"></script>

<!-- 日期 时间 -->
<script type="text/javascript" src="/Public/plugins/datetimepicker/jquery.datetimepicker.full.js"></script>

<!-- layer弹出层 -->
<script type="text/javascript" src="/Public/plugins/layer/layer.js"></script>

<!-- iCheck 1.0.1 -->
<script type="text/javascript" src="/Public/plugins/iCheck/icheck.min.js"></script>

<script type="text/javascript">
var user_type = "<?php echo ($user_type); ?>"; // 用户类型 1学校管理员 2系统用户 3老师 4家长 5省/直辖市教委 6 市教委 7 区/县教委  

if (user_type != '9') {

  // 根据登陆角色权限 展示相应按钮
  if (user_type == '1' || user_type == '2') {
    $('.js_edit').show();
  } else {
    $('.js_edit').hide();
  }

  $(function() {
    $('.xing').hide();
    // 表单
    $('.js_form_val .js_val').hide();
    // 编辑
    $(document).delegate('.js_edit', 'click', function() {
      $('.js_form_val .js_val').show().siblings('.js_txt').hide();
      $('.box-footer, .xing').show();
    });
    // 取消
    $('.cancel').click(function() {
      $('.js_form_val .js_val').hide().siblings('.js_txt').show().parents('.box-body').siblings('.box-footer').hide();
      $('.xing').hide();
      fnAjax();
    });

    // 日期
    $.datetimepicker.setLocale('ch'); // 设置中文
    $('.dataTime').datetimepicker({
      // lang:"ch",           // 语言选择中文
      format:"Y-m-d",      // 格式化日期
      timepicker:false,    // 关闭时间选项
      yearStart:1800,      // 设置最小年份
      yearEnd:2050,        // 设置最大年份
      todayButton:true     // 关闭选择今天按钮
    });
  })

  function fnAjax() {
    // ajax请求
    $.ajax({
      url: '/index.php/Admin/School/SchInformation/query',
      type: 'get',
      dataType: 'json',
      data: {},
      success:function(msg) {
        var obj = msg.data.list[0];
        // console.log(obj)
        for(var key in obj) {
          $('#' + key).val(obj[key]).siblings('.js_txt').text(obj[key]);
        }
      },
      error:function(msg) {
        dialog.error('请求服务器异常！');
      }
    })
  }
  fnAjax();

  //新增/编辑
  function edit(id) {
    var url = g_config.host + '/index.php/Admin/School/SchInformation/get';
    custom_edit(id,url,{s_id:id},'formWindow','modalWindow');
  }

  //保存数据
  function save() {
    var url = g_config.host + '/index.php/Admin/School/SchInformation/edit';
    fpost(url,'formWindow',btnCallbackRefresh);
  }
  // $(document).ready(function() {
  //   var url  = "/Public/plugins/echarts/indexecharts.js";
  //   var url1 = "/Public/plugins/echarts/indexecharts1.js";
  //   $(document).delegate('#changeModel1', 'click', function() {
  //     console.log($(this).hasClass('active'))
  //     mySwiper.destroy(false);
  //     if ($(this).hasClass('active')) {
  //       $(this).removeClass('active');
  //       jQuery.getScript(url, function(data, status, jqxhr) {});
  //       // jQuery.ajax({
  //       //   url: url,
  //       //   dataType: "script",
  //       //   cache: false
  //       // })
  //     } else {
  //       $(this).addClass('active');
  //       jQuery.getScript(url1, function(data, status, jqxhr) {});
  //       // jQuery.ajax({
  //       //   url: url1,
  //       //   dataType: "script",
  //       //   cache: false
  //       // })
  //     };
  //   });
  // })

}else{
  window.location.href='/index.php/Admin/Referrer/Referrer/index';
}
</script>
</body>
</html>