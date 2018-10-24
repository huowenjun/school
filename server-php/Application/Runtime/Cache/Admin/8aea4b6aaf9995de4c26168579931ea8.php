<?php if (!defined('THINK_PATH')) exit();?>
  <!-- Main Header -->
  <!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>学习服务</title>
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
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <ol class="breadcrumb">
        <li><a href="/index.php/Admin/Index.html"><i class="fa fa-home"></i> 首页</a></li>
        <li class="active">学习服务</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Your Page Content Here -->
      <div class="row">
        <div class="col-md-12">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab1" data-toggle="tab">学习服务</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab1">
                <nav class="navbar navbar-default">
                  <!-- container-fluid -->
                  <div class="container-fluid">
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="form-inline navbar-form navbar-left">
                      <div class="form-group">
                        <label for="region_id1">服务区域：</label>
                        <input id="region_id1" name="region_id" type="text" class="form-control input-sm" placeholder="请输入服务区域" />
                      </div>
                      <div class="form-group">
                        <label for="s_id1">服务学校：</label>
                        <input id="s_id1" name="s_id" type="text" class="form-control input-sm" placeholder="请输入学校名称" />
                      </div>

                      <div class="form-group">
                        <button type="submit" class="btn btn-primary query">
                          <i class="fa fa-search"></i> 查询
                        </button>
                      </div>
                      <div class="form-group">
                        <button type="button" class="btn btn-success select_add">
                          <i class="fa fa-plus"></i> 添加
                        </button>
                      </div>
                      <div class="form-group">
                        <button type="button" class="btn btn-success online " data-color="#39B3D7" data-opacity="0.95">
                          <span class="glyphicon glyphicon-ok-circle"></span> 上线
                        </button>
                      </div>
                      <div class="form-group">
                        <button type="button" class="btn btn-danger downline" data-color="#39B3D7" data-opacity="0.95">
                          <span class="glyphicon glyphicon-ban-circle"></span> 下线
                        </button>
                      </div>        
                    </div>
                  </div>
                  <!-- /.container-fluid -->
                </nav>

                <!-- table -->
                <div class="content-wrap">
                  <div class="row">
                    <div class="col-md-12">
                      <div id="toolbar">
                        <div id="remove" class="btn btn-danger btn-sm" style="margin-top:12px">
                          <i class="fa fa-trash"></i> 删除
                        </div>
                      </div>
                      <table id="table_data" class="js-custom-table-data">
                        <thead>
                          <tr>
                            <th data-field="state" data-checkbox="true"></th>
                            <th data-align="center" data-formatter="editFormatter" data-events="operateEvents" name="editFormatter">操作</th>
                            <th data-align="center" data-sortable="false" data-field="title" name="">服务标题</th>
                            <th data-align="center" data-sortable="false" data-field="icon_url" name="">服务图标</th>
                            <th data-align="center" data-sortable="false" data-field="region_id" name="">服务区域</th>
                            <th data-align="center" data-sortable="false" data-field="s_id" name="">服务学校</th>
                            <th data-align="center" data-sortable="false" data-field="web_url" name="">图标跳转链接</th>
                            <th data-align="center" data-sortable="false" data-field="status" name="">状态</th>
                            <th data-align="center" data-sortable="false" data-field="create_time" name="">创建时间</th>
                          </tr>
                        </thead>
                      </table>
                    </div>
                  </div>
                </div>

              </div>
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
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

<!-- 弹出层 -->
<div class="shade" style="position: relative!important" id="modalWindow">
  <div class="modal-body">
    <div class="container-fluid">
      <form class="form-horizontal" action="/index.php/Admin/Upload/upload_image.html" enctype="multipart/form-data" method="post" id="iconFileFrom" style="display:none;">
        <input type="file" id="icon_files" name="icon_files" value="" style="display:block" />
      </form>
      <form class="form-horizontal" action="" method="post" onsubmit="return false" id="formWindow">
        <input class="primaryKey" type="hidden" name="learning_id" id="learning_id" />
        <input type="hidden" class="primaryKeys js_input_grade prov_id_hide" id="prov_id" name="prov_id" value="" placeholder="省" />
        <input type="hidden" class="primaryKeys js_input_grade city_id_hide" id="city_id" name="city_id" value="" placeholder="市" />
        <input type="hidden" class="primaryKeys js_input_grade county_id_hide" id="county_id" name="county_id" value="" placeholder="区" />
        <input type="hidden" class="primaryKeys js_input_grade s_id_hide" id="s_id" name="s_id" value="" placeholder="学校" />
        <div class="row">
          <div class="col-md-12">
            <div class="clearfix">
              <p class="note pull-right">注：<span class="xing">*</span>标记为必填项</p>
            </div>
            <div class="row">
              <div class="col-md-4">
                <div class="box box-default">
                  <div class="box-header with-border">
                    <h3 class="box-title">校区列表</h3>
                  </div>
                  <div class="box-body">
                    <ul id="treeDemo" class="ztree"></ul>
                  </div>
                </div>
              </div>
              <div class="col-md-8">
                <div class="form-group">
                  <label class="col-md-3 control-label" for="title"><span class="xing">*</span> 服务标题：</label>
                  <div class="col-md-8">
                    <input type="text" placeholder="服务标题" id="title" name="title" class="form-control" />
                  </div>
                </div>
                <div class="form-group uploadFile">
                  <label class="col-md-3 control-label" for="icon_files"> 上传图片：</label>
                  <div class="col-md-9">
                    <label class="upload_btn" for="icon_files"><i class="fa fa-fw fa-upload"></i>点击上传</label>
                    <input type="hidden" id="f_tb" name="f_tb" />
                    <span class="hint">建议图片比例为 宽高1:1 (仅支持png/jpg/gif/jpeg格式)</span>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-3 control-label"></label>
                  <div class="col-md-9">
                    <div class="imgHide"><img id="icon_m_tb" src="" alt="" width="88" /></div>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-3 control-label" for="web_url"><span class="xing">*</span> 图标跳转链接：</label>
                  <div class="col-md-8">
                    <input type="text" placeholder="图片跳转链接" id="web_url" name="web_url" class="form-control js_url" />
                  </div>
                </div>
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

<!-- 隐藏域 -->
<form id="searchForm" name="searchForm" action="" style="position: fixed; top: 0; right: 0; z-index: 99999;">
  <input type="hidden" id="region_id_search" name="region_id_search" value="" />
  <input type="hidden" id="s_id_search" name="s_id_search" value="" />
</form>

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

<script type="text/javascript" src="/Public/dist/js/jquery.form.js"></script>
<script type="text/javascript">
  // H5上传文件
  $('#icon_files').change(function() {
    var file = this.files[0];             // 获取input type=file文件所有数据，因为input type=file 可以多选，所以files是个数组
    var fr   = new FileReader();          // H5的新api，文件预览对象
    fr.onloadend = function(e) {          // 当转换完成之后fr.onloadend，执行函数，转换完的数据就是e
      var imgSrc = e.target.result;       // file数据中的图片信息就是e.target.result，这是图片预览地址
      $('#icon_m_tb').attr('src',imgSrc); // 预览图片
      // 提交图片表单
      $('#iconFileFrom').ajaxSubmit(function(msg) {
        // 成功返回的数据
        if (msg.status==1) {
          var imgSrc = msg.data.url;
          $('#f_tb').val(imgSrc);
          $('#icon_m_tb').attr('src',imgSrc);
          dialog.notify('文件上传成功');
        }else{
          alert('上传失败，请重新上传');
          $('#icon_files').val('');
          $('#f_tb').val('');
          $('#icon_m_tb').attr('src','');
        };
      });
    }
    fr.readAsDataURL(file);               // 把file数据转为DataURL数据
  });

  // 表格数据
  var $table    = $('#table_data');
  var $g_params = {};
  // table 功能设置
  $table.bootstrapTable({
    toolbar:"#toolbar",                   // 工具栏
    toggle:"table",                       // 表格
    showToggle:true,                      // 是否显示(表格样式)切换
    showColumns:true,                     // 是否显示列(功能:[显示/隐藏]列)
    showExport:true,                      // 是否显示导出
    pagination:true,                      // 分页
    pageList:[10, 25, 50, 100],           // 每页行数
    ajax:"ajaxRequest",                   // 数据地址
    sidePagination:"server"               // 服务器
  });


  // 文件树
  var ajaxTreeNodesCheckbox = null;
  var settingCheckbox = {
    check: {
      enable: true,
      chkStyle: "checkbox",
      nocheckInherit: true,
      radioType: "all" // 在整棵树范围内当做一个分组
    },
    view: {
      dblClickExpand: true
    },
    data: {
      simpleData: {
        enable: true
      }
    },
    callback: {
      beforeExpand: beforeExpandCheckbox,
      onExpand: onExpandCheckbox,
      onCheck: zTreeOnCheckCheckbox
    }
  };
  // 保持展开单一路径 start
  var curExpandNodeCheckbox = null;
  function beforeExpandCheckbox(treeId, treeNode) {
    var pNode     = curExpandNodeCheckbox ? curExpandNodeCheckbox.getParentNode():null;
    var treeNodeP = treeNode.parentTId ? treeNode.getParentNode():null;
    var zTree     = $.fn.zTree.getZTreeObj("treeDemo");
    for(var i=0, l=!treeNodeP ? 0:treeNodeP.children.length; i<l; i++ ) {
      if (treeNode !== treeNodeP.children[i]) {
        zTree.expandNode(treeNodeP.children[i], false);
      }
    }
    while (pNode) {
      if (pNode === treeNode) {
        break;
      }
      pNode = pNode.getParentNode();
    }
    if (!pNode) {
      singlePathCheckbox(treeNode);
    }
  }
  function singlePathCheckbox(newNode) {
    if (newNode === curExpandNodeCheckbox) return;

    var zTree = $.fn.zTree.getZTreeObj("treeDemo"),
      rootNodes, tmpRoot, tmpTId, i, j, n;

    if (!curExpandNodeCheckbox) {
      tmpRoot = newNode;
      while (tmpRoot) {
        tmpTId = tmpRoot.tId;
        tmpRoot = tmpRoot.getParentNode();
      }
      rootNodes = zTree.getNodes();
      for (i=0, j=rootNodes.length; i<j; i++) {
        n = rootNodes[i];
        if (n.tId != tmpTId) {
          zTree.expandNode(n, false);
        }
      }
    } else if (curExpandNodeCheckbox && curExpandNodeCheckbox.open) {
      if (newNode.parentTId === curExpandNodeCheckbox.parentTId) {
        zTree.expandNode(curExpandNodeCheckbox, false);
      } else {
        var newParents = [];
        while (newNode) {
          newNode = newNode.getParentNode();
          if (newNode === curExpandNodeCheckbox) {
            newParents = null;
            break;
          } else if (newNode) {
            newParents.push(newNode);
          }
        }
        if (newParents!=null) {
          var oldNode = curExpandNodeCheckbox;
          var oldParents = [];
          while (oldNode) {
            oldNode = oldNode.getParentNode();
            if (oldNode) {
                oldParents.push(oldNode);
            }
          }
          if (newParents.length>0) {
            zTree.expandNode(oldParents[Math.abs(oldParents.length-newParents.length)-1], false);
          } else {
            zTree.expandNode(oldParents[oldParents.length-1], false);
          }
        }
      }
    }
    curExpandNodeCheckbox = newNode;
  }
  function onExpandCheckbox(event, treeId, treeNode) {
    curExpandNodeCheckbox = treeNode;
  }
  // 请求树
  $.ajax({
    type: "get",
    url: "/index.php/Admin/index/get_tree?type=school",
    dataType : 'json',
    success: function(msg) {
      ajaxTreeNodesCheckbox = msg.data;
      $.fn.zTree.init($("#treeDemo"), settingCheckbox, ajaxTreeNodesCheckbox);
    },
    error: function(msg) {
      dialog.error("请求服务器异常！");
    }
  });

  // 保持展开单一路径 end
  function zTreeOnCheckCheckbox(event, treeId, treeNode) {
    // 获取所有被选中的节点
    var treeObj = $.fn.zTree.getZTreeObj("treeDemo");
    var nodes   = treeObj.getCheckedNodes(true);
    var grade   = [];
    for (var i = 0; i < 8; i++) {
      grade[i] = [];
      for (var t = 0; t < nodes.length; t++) {
        if(nodes[t].level==i) {
          grade[i].push(nodes[t].id);
        }
      };
      $('.js_input_grade').eq(i).val(grade[i].join()); // 隐藏域赋值(地区、学校、校区、年级、班级、学生)
    };
  }

  // 点击添加
  $('.select_add').click(function() {
    var treeObj = $.fn.zTree.getZTreeObj("treeDemo");
    treeObj.checkAllNodes(false);
    $('.primaryKey').val('0');
    $('.primaryKeys').val('');
    $('#f_tb').val('');
    $('#icon_m_tb').attr('src','');
    edit(0);
  })

  // 隐藏域赋值
  function searchForm() {
    $("#region_id_search").val($("#region_id1").val());
    $("#s_id_search").val($("#s_id1").val());
  }

  // 自定义ajax请求
  function ajaxRequest(params) {
    $g_params = params;
    searchForm();
    // 需要的数据
    var url = "/index.php/Admin/Learning/Learning/query?" + $("#searchForm").serialize();
    queryList(url,params);
     $('#table_data tr td:eq(5)').attr('some text')
  }

  // 查询
  function search() {
    // form查询域赋值
    searchForm();
    var url = "/index.php/Admin/Learning/Learning/query?" + $("#searchForm").serialize();
    $g_params.data.page = parseInt($('.pagination li.active a').text());
    queryList(url,$g_params);
  }

  //新增/编辑
  function edit(id) {
    var url = g_config.host + '/index.php/Admin/Learning/Learning/get';
    custom_edit(id,url,{learning_id:id},'formWindow','modalWindow');
  }
  function setEditDataEx(data) {
    $("#f_tb").val(data.icon_url);
    $("#icon_m_tb").attr('src',data.icon_url);
    $("#learning_id").val(data.id);
    fnAjax(data.id);
  };
  function fnAjax(learningId) {
    $.ajax({
      type: "get",
      url: "/index.php/Admin/index/get_tree?type=school&learning_id=" + learningId,
      dataType : 'json',
      success: function(msg) {
        ajaxTreeNodesCheckbox = msg.data;
        $.fn.zTree.init($("#treeDemo"), settingCheckbox, ajaxTreeNodesCheckbox);
      },
      error: function(msg) {
        dialog.error("请求服务器异常！");
      }
    });
  }

  //保存数据
  function save() {
    var url = g_config.host + '/index.php/Admin/Learning/Learning/edit';
    fpost(url,'formWindow',btnCallbackRefreshTable1);
  }

  //删除
  function del() {
    var url    = g_config.host + "/index.php/Admin/Learning/Learning/del";
    var ids    = getIdSelections($('.js-custom-table-data'),'id');
    var strIds = ids.join(',');
    var data   = {};
    data.id    = strIds;
    if (ids.length == 0) {
      dialog.notify('请先选择要删除的记录');
      return;
    } else {
      dialog.confirm('您确定要删除当前选项吗？',function(){dpost(url,data,btnCallbackRefreshTable1);});
    }
  }

  //状态处理
  function line(type){
    var type_title = "上线";
    if (type==1) {
      type_title = "下线";
    };
    var url = g_config.host + "/index.php/Admin/Learning/Learning/line";
    var ids = getIdSelections($('.js-custom-table-data'),'id');
    var strIds = ids.join(',');
    var data   = {};
    data.id = strIds;
    data.status=type;
    if (ids.length == 0) {
      dialog.notify('请先选择要处理的记录');
      return;
    } 
    else {
      dialog.confirm('您确定要'+type_title+'吗？',function(){dpost(url,data,btnCallbackRefreshTable1);});
    }
  }
  // 上线
  $('.online').click(function() {
    line(0);
  });

  // 下线
  $('.downline').click(function() {
    line(1);
  });
</script>
</body>
</html>