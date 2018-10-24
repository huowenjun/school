<?php if (!defined('THINK_PATH')) exit();?>
  <!-- Main Header -->
  <!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>学生管理</title>
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
  <!-- Main Sidebar -->
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


  <!-- 弹出层文件树滚动条 -->
  <link rel="stylesheet" href="/Public/dist/css/jquery.mCustomScrollbar.css" />

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <ol class="breadcrumb">
        <li><a href="/index.php/Admin/Index.html"><i class="fa fa-home"></i> 首页</a></li>
        <li class="active">学生管理</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Your Page Content Here -->
      <div class="row">
        <div class="col-md-3">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">班级列表</h3>
            </div>
            <div class="box-body">
              <div class="tree-search">
                <i class="tree-search-icon" id="searchNodeBtn"></i> 
                <input type="text" placeholder="搜索关键字" id="searchNodeVal" value="" class="empty fs-12" />
              </div>
              <div class="tree-scroll">
                <ul id="treeDemo" class="ztree js_ztree_height"></ul>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab1" data-toggle="tab">学生信息管理</a></li>
              <li class="js_tab_switch2"><a href="#tab2" data-toggle="tab">批量操作管理</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab1">
                <nav class="navbar navbar-default">
                  <!-- container-fluid -->
                  <div class="container-fluid">
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="form-inline navbar-form navbar-left">
                      <div class="form-group">
                        <label for="stu_no1">学生学号：</label>
                        <input id="stu_no1" type="text" class="form-control input-sm" placeholder="请输入学号" />
                      </div>
                      <div class="form-group">
                        <label for="stu_name1">学生姓名：</label>
                        <input id="stu_name1" type="text" class="form-control input-sm" placeholder="请输入学生姓名" />
                      </div>
                      <div class="form-group">
                        <label for="imei_id1">IMEI号：</label>
                        <input id="imei_id1" type="text" class="form-control input-sm" placeholder="请输入IMEI号" />
                      </div>
                      <div class="form-group">
                        <label for="parent_name1">家长姓名：</label>
                        <input id="parent_name1" type="text" class="form-control input-sm" placeholder="请输入家长姓名" />
                      </div>
                      <div class="form-group">
                        <label for="phone1">联系方式：</label>
                        <input id="phone1" type="text" class="form-control input-sm" placeholder="请输入监护人联系方式" />
                      </div>
                      <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-sm query">
                          <i class="fa fa-search"></i> 查询
                        </button>
                      </div>
                      <div class="form-group">
                        <button type="button" class="btn btn-success btn-sm select_add">
                          <i class="fa fa-plus"></i> 添加
                        </button>
                      </div>
                      <div class="form-group">
                        <button type="button" class="btn btn-success btn-sm batch_unwrap">
                          <i class="fa fa-chain-broken"></i> 解绑
                        </button>
                      </div>
                      <div class="form-group">
                        <button type="button" class="btn btn-success btn-sm js_upgrade">
                          <i class="fa fa-arrow-up"></i> 升级年级
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
                            <th data-align="center" data-formatter="studentFormatter" data-events="studentoperateEvents" name="studentFormatter">操作</th>
                            <th data-align="center" data-formatter="editFormatter" data-events="studentoperateEvents" name="editFormatter">操作</th>
                            <th data-align="center" data-sortable="true" data-field="stu_no" name="">学号</th>
                            <th data-align="center" data-sortable="false" data-field="stu_name" name="">学生姓名</th>
                            <th data-align="center" data-sortable="false" data-field="imei_id" name="">IMEI号</th>
                            <th data-align="center" data-sortable="false" data-field="icc_id" name="">ICCID</th>
                            <th data-align="center" data-sortable="false" data-field="nfc_id" name="">NFCID</th>
                            <th data-align="center" data-sortable="false" data-field="stu_phone" name="">学生卡手机号</th>
                            <th data-align="center" data-sortable="false" data-field="sex" name="">性别</th>
                            <th data-align="center" data-sortable="false" data-field="g_id" name="">年级</th>
                            <th data-align="center" data-sortable="false" data-field="c_id" name="">班级</th>
                            <th data-align="center" data-sortable="false" data-field="parent_name" name="">监护人</th>
                            <th data-align="center" data-sortable="false" data-field="phone" name="">联系方式</th>
                            <th data-align="center" data-sortable="false" data-field="username" name="">登录帐号</th>
                            <th data-align="center" data-sortable="false" data-field="status" name="">绑定状态</th>
                          </tr>
                        </thead>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /#tab1 -->

              <div class="tab-pane" id="tab2">
                <section class="box-body">
                  <div class="row">
                    <div class="col-xs-12">
                      <strong>数据批量操作</strong>
                      <div class="form-inline navbar-form">
                        <div class="form-group">
                          <button type="button" class="btn btn-default btn-sm template" title="学生档案信息模版">
                            <i class="fa fa-download"></i> 下载模板
                          </button>
                        </div>
                        <div class="form-group">
                          <button type="button" class="btn btn-default btn-sm import" title="学生档案信息批量导入">
                            <i class="fa fa-download"></i> 批量导入
                          </button>
                        </div>
                      </div>
                    </div>
                    <!-- /.col -->
                  </div>
                  <hr />
                  <div class="row">
                    <div class="col-xs-12">
                      <strong>设备批量操作</strong>
                      <div class="form-inline navbar-form">
                        <div class="form-group">
                          <button type="button" class="btn btn-default btn-sm deviceTemplate" title="批量设备绑定模板">
                            <i class="fa fa-download"></i> 下载模板
                          </button>
                        </div>
                        <div class="form-group">
                          <button type="button" class="btn btn-default btn-sm deviceImport" title="设备信息批量导入">
                            <i class="fa fa-download"></i> 批量导入
                          </button>
                        </div>
                        <div class="form-group">
                          <button type="button" class="btn btn-default btn-sm" title="批量设备批量绑定" disabled>
                            <i class="fa fa-link"></i> 批量绑定
                          </button>
                        </div>
                        <div class="form-group">
                          <button type="button" class="btn btn-default btn-sm" title="批量设备批量解绑" disabled>
                            <i class="fa fa-chain-broken"></i> 批量解绑
                          </button>
                        </div>
                      </div>
                    </div>
                    <!-- /.col -->
                  </div>
                </section>
              </div>
              <!-- /#tab2 -->

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

<!-- 添加/编辑 弹出层 -->
<div class="shade" style="" id="modalWindow">
  <div class="modal-body">
    <div class="container-fluid">
      <form class="form-horizontal" action="" method="post" onsubmit="return false" id="formWindow">
        <input class="primaryKey" type="hidden" name="stu_id" id="stu_id" />
        <input type="hidden" class="prov_id_hide" id="prov_id" name="prov_id" value="" placeholder="省" />
        <input type="hidden" class="city_id_hide" id="city_id" name="city_id" value="" placeholder="市" />
        <input type="hidden" class="county_id_hide" id="county_id" name="county_id" value="" placeholder="区县" />
        <input type="hidden" class="s_id_hide" id="s_id" name="s_id" value="" placeholder="学校" />
        <input type="hidden" class="a_id_hide" id="a_id" name="a_id" value="" placeholder="校区" />
        <input type="hidden" class="g_id_hide" id="g_id" name="g_id" value="" placeholder="年级" />
        <input type="hidden" class="c_id_hide" id="c_id" name="c_id" value="" placeholder="班级" />
        <input type="hidden" id="type_hide" name="type" value="" placeholder="添加学生保存时，后台返回给我的数据参数" />
        <div class="row">
          <div class="col-md-12">
            <div class="nav-tabs-custom">
              <ul class="nav nav-tabs js_nav_tab2">
                <li><a data-toggle="tab">学生信息</a></li>
                <li class="js_tab2"><a data-toggle="tab">监护人信息</a></li>
              </ul>
              <div class="tab-content js_tab_content2">
                <div class="tab-pane">
                  <div class="clearfix">
                    <p class="note pull-right">注：<span class="xing">*</span>标记为必填项</p>
                  </div>
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="col-md-3 control-label" for="stu_no"><span class="xing">*</span> 学号：</label>
                        <div class="col-md-8">
                          <input type="text" placeholder="学号" id="stu_no" name="stu_no" class="form-control" />
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-3 control-label" for="birth_date"><span class="xing">*</span> 出生日期：</label>
                        <div class="col-md-8">
                          <input type="text" placeholder="出生日期" id="birth_date" name="birth_date" class="form-control dataTime" />
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-3 control-label" for="card_id"><span class="xing">*</span> 身份证号：</label>
                        <div class="col-md-8">
                          <input type="text" placeholder="身份证号" id="card_id" name="card_id" class="form-control" />
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-3 control-label" for="sex"><span class="xing">*</span> 性别：</label>
                        <div class="col-md-9 js_sex">
                          <label class="checkbox-inline">
                            <input type="radio" name="sex" class="flat-red men" value="1" checked="" /> 男
                          </label>
                          <label class="checkbox-inline">
                            <input type="radio" name="sex" class="flat-red women" value="0" /> 女
                          </label> 
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-3 control-label" for="icc_id"><span class="xing">*</span> ICCID：</label>
                        <div class="col-md-8">
                          <input type="text" placeholder="ICCID" id="icc_id" name="icc_id" class="form-control" />
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="col-md-3 control-label" for="stu_name"><span class="xing">*</span> 学生姓名：</label>
                        <div class="col-md-8">
                          <input type="text" placeholder="学生姓名" id="stu_name" name="stu_name" class="form-control" />
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-3 control-label" for="rx_date"><span class="xing">*</span> 入学时间：</label>
                        <div class="col-md-8">
                          <input type="text" placeholder="入学时间" id="rx_date" name="rx_date" class="form-control dataTime" />
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-3 control-label" for="imei_id"><span class="xing">*</span> IMEI号：</label>
                        <div class="col-md-8">
                          <input type="text" placeholder="IMEI号" id="imei_id" name="imei_id" class="form-control" />
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-3 control-label" for="stu_phone"><span class="xing">*</span> 学生卡手机号：</label>
                        <div class="col-md-8">
                          <input type="text" placeholder="学生卡手机号" id="stu_phone" name="stu_phone" class="form-control" />
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-md-3 control-label" for="nfc_id"><span class="xing">*</span> NFCID：</label>
                        <div class="col-md-8">
                          <input type="text" placeholder="NFCID" id="nfc_id" name="nfc_id" class="form-control" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /#tab1 -->

                <div class="tab-pane">
                  <div class="clearfix">
                    <p class="note pull-right">注：<span class="xing">*</span>标记为必填项</p>
                  </div>
                  <div class="js_info_cont"></div>
                  <!-- <div class="form-group" style="margin-left: auto;">
                    <button type="button" class="btn btn-success btn-sm select_parents">
                      <i class="fa fa-plus"></i> 添加监护人
                    </button>
                  </div> -->
                </div>
                <!-- /#tab2 -->

              </div>
              <!-- /.tab-content -->
            </div>
            <!-- /.nav-tabs-custom -->
            
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

<!-- 绑定设备 -->
<div class="shade" style="" id="modalWindow1">
  <div class="modal-body">
    <div class="container-fluid">
      <form class="" action="" method="post" onsubmit="return false" id="formWindow1">
        <input class="primaryKey" type="hidden" id="stu_id2" name="stu_id" />
        <input class="primaryKey" type="hidden" id="dc_id" name="dc_id" />
        <div class="form-group clearfix">
          <form class="form-horizontal">
            <label class="col-sm-4 control-label" style="margin-bottom: 0;">
              学生学号：<span class="js_studNo"></span>
            </label>
            <label class="col-sm-4 control-label" style="margin-bottom: 0;">
              学生姓名：<span class="js_studentName"></span>
            </label>
          </form>
        </div>
        <nav class="navbar navbar-default" style="margin-bottom:10px">
          <div class="container-fluid">
            <div class="form-inline navbar-form navbar-left">
              <div class="form-group">
                <label for="imei2">设备IMEI号：</label>
                <input id="imei2" type="text" class="form-control input-sm" placeholder="请输入设备IMEI号搜索" />
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-primary btn-sm query2">
                  <i class="fa fa-search"></i> 查询
                </button>
              </div>
            </div>
          </div>
        </nav>

        <!-- table -->
        <div class="content-wrap">
          <div class="row">
            <div class="col-md-12">
              <table id="table_data2" class="js-custom-table-data2">
                <thead>
                  <tr>
                    <th data-field="state" data-radio="true"></th>
                    <th data-align="center" data-sortable="false" data-field="imei" name="">设备IMEI</th>
                    <th data-align="center" data-sortable="false" data-field="rfid" name="">设备RFID</th>
                    <th data-align="center" data-sortable="false" data-field="model" name="">设备机型</th>
                    <th data-align="center" data-sortable="false" data-field="expire_time" name="">到期时间</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="modal-footer" style="text-align:center;">
    <button type="button" class="btn btn-success btn-sm save2">
      <span class="glyphicon glyphicon-ok"></span> 确认
    </button>
    <button type="button" class="btn btn-default btn-sm layui-layer-close">
      <span class="glyphicon glyphicon-remove"></span> 取消
    </button>
  </div>
</div>

<!-- 隐藏域 -->
<form id="searchForm" name="searchForm" action="" style="position: fixed; top: 0; right: 0; z-index: 99999;">
  <input type="hidden" class="prov_id_hide" name="prov_id" value="" placeholder="省" />
  <input type="hidden" class="city_id_hide" name="city_id" value="" placeholder="市" />
  <input type="hidden" class="county_id_hide" name="county_id" value="" placeholder="区县" />
  <input type="hidden" class="s_id_hide" name="s_id" value="" placeholder="学校" />
  <input type="hidden" class="a_id_hide" name="a_id" value="" placeholder="校区" />
  <input type="hidden" class="g_id_hide" name="g_id" value="" placeholder="年级" />
  <input type="hidden" class="c_id_hide" name="c_id" value="" placeholder="班级" />

  <input type="hidden" id="stu_no_hide" name="stu_no" value="" />
  <input type="hidden" id="stu_name_hide" name="stu_name" value="" />
  <input type="hidden" id="imei_id_hide" name="imei_id" value="" />
  <input type="hidden" id="parent_name_hide" name="parent_name" value="" />
  <input type="hidden" id="phone_hide" name="phone" value="" />
</form>
<form id="searchForm2" name="searchForm" action="">
  <input type="hidden" id="imei_hide" name="imei" value="" />
  <input type="hidden" id="s_id_hide2" name="s_id" value="" />
</form>

<!-- 监护人模版 -->
<div id="js_tianjia_cont">
  <div class="row getLastRow js_getLastRow">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="col-md-4 control-label" for=""><span class="xing">*</span> 监护人姓名(1)：</label>
            <div class="col-md-7">
              <input type="text" placeholder="请输入监护人姓名" id="parent_name" name="parent_name[]" class="form-control" />
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="col-md-3 control-label" for=""><span class="xing">*</span> 关系(1)：</label>
            <div class="col-md-8">
            <select id="relation" name="relation[]" class="form-control">
              <option value="">请选择关系</option>
              <option value="1">爸爸</option>
              <option value="2">妈妈</option>
              <option value="3">爷爷</option>
              <option value="4">奶奶</option>
              <option value="5">外公</option>
              <option value="6">外婆</option>
              <option value="0">其它</option>
            </select>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="col-md-4 control-label" for=""><span class="xing">*</span> 登录帐号(1)：</label>
            <div class="col-md-7">
              <input type="text" placeholder="平台及app登录帐号" id="username" name="username[]" class="form-control" />
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="col-md-3 control-label" for=""><span class="xing"></span> 邮箱(1)：</label>
            <div class="col-md-8">
              <input type="text" placeholder="请输入邮箱" id="email" name="email[]" class="form-control" />
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="col-md-4 control-label" for=""><span class="xing"></span> 家庭电话(1)：</label>
            <div class="col-md-7">
              <input type="text" placeholder="请输入家庭电话" id="family_tel" name="family_tel[]" class="form-control" />
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="col-md-3 control-label" for=""><span class="xing">*</span> 联系电话(1)：</label>
            <div class="col-md-8">
              <input type="text" placeholder="联系电话" id="parent_phone" name="parent_phone[]" class="form-control" />
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label class="col-md-2 control-label" for=""><span class="xing"></span> 工作单位(1)：</label>
            <div class="col-md-9">
              <input type="text" placeholder="请输入工作单位" id="work_unit" name="work_unit[]" class="form-control" />
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label class="col-md-2 control-label" for=""><span class="xing">*</span> 常住地址(1)：</label>
            <div class="col-md-9">
              <input type="text" placeholder="请输入常住地址" id="address" name="address[]" class="form-control" />
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="line"></div>
  </div>
  <div class="row getLastRow js_getLastRow">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="col-md-4 control-label" for=""><span class="xing">*</span> 监护人姓名(2)：</label>
            <div class="col-md-7">
              <input type="text" placeholder="请输入监护人姓名" id="parent_name" name="parent_name[]" class="form-control" />
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="col-md-3 control-label" for=""><span class="xing">*</span> 关系(2)：</label>
            <div class="col-md-8">
            <select id="relation" name="relation[]" class="form-control">
              <option value="">请选择关系</option>
              <option value="1">爸爸</option>
              <option value="2">妈妈</option>
              <option value="3">爷爷</option>
              <option value="4">奶奶</option>
              <option value="5">外公</option>
              <option value="6">外婆</option>
              <option value="0">其它</option>
            </select>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="col-md-4 control-label" for=""><span class="xing">*</span> 登录帐号(2)：</label>
            <div class="col-md-7">
              <input type="text" placeholder="平台及app登录帐号" id="username" name="username[]" class="form-control" />
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="col-md-3 control-label" for=""><span class="xing"></span> 邮箱(2)：</label>
            <div class="col-md-8">
              <input type="text" placeholder="请输入邮箱" id="email" name="email[]" class="form-control" />
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="col-md-4 control-label" for=""><span class="xing"></span> 家庭电话(2)：</label>
            <div class="col-md-7">
              <input type="text" placeholder="请输入家庭电话" id="family_tel" name="family_tel[]" class="form-control" />
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group">
            <label class="col-md-3 control-label" for=""><span class="xing">*</span> 联系电话(2)：</label>
            <div class="col-md-8">
              <input type="text" placeholder="联系电话" id="parent_phone" name="parent_phone[]" class="form-control" />
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label class="col-md-2 control-label" for=""><span class="xing"></span> 工作单位(2)：</label>
            <div class="col-md-9">
              <input type="text" placeholder="请输入工作单位" id="work_unit" name="work_unit[]" class="form-control" />
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label class="col-md-2 control-label" for=""><span class="xing">*</span> 常住地址(2)：</label>
            <div class="col-md-9">
              <input type="text" placeholder="请输入常住地址" id="address" name="address[]" class="form-control" />
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="line"></div>
  </div>
</div>
<script id="jhr_template" type="text/html">
<div class="row getLastRow js_getLastRow">
	<div class="col-md-12">
    <input type="hidden" id="u_id" value="{{u_id}}" name="u_id[]" />
    <input type="hidden" id="sp_id" value="{{sp_id}}" name="sp_id[]" />
	  <div class="row">
	    <div class="col-md-6">
	      <div class="form-group">
	        <label class="col-md-4 control-label" for=""><span class="xing">*</span> 监护人姓名({{index}})：</label>
	        <div class="col-md-7">
	          <input type="text" placeholder="请输入监护人姓名" id="parent_name" value="{{parent_name}}" name="parent_name[]" class="form-control" />
	        </div>
	      </div>
	    </div>
	    <div class="col-md-6">
	      <div class="form-group">
	        <label class="col-md-3 control-label" for=""><span class="xing">*</span> 关系({{index}})：</label>
	        <div class="col-md-8">
	        <select id="relation" name="relation[]" value="{{relation}}" class="form-control">
	          <option value="">请选择关系</option>
	          <option value="1">爸爸</option>
	          <option value="2">妈妈</option>
	          <option value="3">爷爷</option>
	          <option value="4">奶奶</option>
	          <option value="5">外公</option>
	          <option value="6">外婆</option>
	          <option value="0">其它</option>
	        </select>
	        </div>
	      </div>
	    </div>
	  </div>
	  <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label class="col-md-4 control-label" for=""><span class="xing">*</span> 登录帐号({{index}})：</label>
          <div class="col-md-7">
            <input type="text" placeholder="平台及app登录帐号" id="username" value="{{username}}" name="username[]" class="form-control" />
          </div>
        </div>
      </div>
	    <div class="col-md-6">
	      <div class="form-group">
	        <label class="col-md-3 control-label" for=""><span class="xing"></span> 邮箱({{index}})：</label>
	        <div class="col-md-8">
	          <input type="text" placeholder="请输入邮箱" id="email" value="{{email}}" name="email[]" class="form-control" />
	        </div>
	      </div>
	    </div>
	  </div>
	  <div class="row">
	    <div class="col-md-6">
	      <div class="form-group">
	        <label class="col-md-4 control-label" for=""><span class="xing"></span> 家庭电话({{index}})：</label>
	        <div class="col-md-7">
	          <input type="text" placeholder="请输入家庭电话" id="family_tel" value="{{family_tel}}" name="family_tel[]" class="form-control" />
	        </div>
	      </div>
	    </div>
      <div class="col-md-6">
        <div class="form-group">
          <label class="col-md-3 control-label" for=""><span class="xing">*</span> 联系电话({{index}})：</label>
          <div class="col-md-8">
            <input type="text" placeholder="联系电话" id="parent_phone" value="{{parent_phone}}" name="parent_phone[]" class="form-control" />
          </div>
        </div>
      </div>
	  </div>
	  <div class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label class="col-md-2 control-label" for=""><span class="xing"></span> 工作单位({{index}})：</label>
          <div class="col-md-9">
            <input type="text" placeholder="请输入工作单位" id="work_unit" value="{{work_unit}}" name="work_unit[]" class="form-control" />
          </div>
        </div>
      </div>
    </div>
    <div class="row">
	    <div class="col-md-12">
	      <div class="form-group">
	        <label class="col-md-2 control-label" for=""><span class="xing">*</span> 常住地址({{index}})：</label>
	        <div class="col-md-9">
            <input type="text" placeholder="请输入常住地址" id="address" value="{{address}}" name="address[]" class="form-control" />
	        </div>
	      </div>
	    </div>
	  </div>
	</div>
  <div class="line"></div>
</div>
</script>

<!-- 升级年级 弹出层 -->
<div class="shade" style="" id="upgrademodalWindow">
  <div class="modal-body">
    <div class="container-fluid">
      <form class="form-horizontal" action="" method="post" onsubmit="return false" id="upgradeformWindow">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group clearfix">
              <form class="form-horizontal">
                <label class="col-md-12" style="margin-bottom: 0;">请选择要升年级的学生</label>
              </form>
            </div>
            <div class="form-group clearfix">
              <form class="form-horizontal" id="upgradeForm">
                <input type="hidden" id="stu_id_upgrade" name="stu_id" value="" />
                <input type="hidden" id="c_id_upgrade_if" name="" value="" />
                <input type="hidden" id="s_id_upgrade" name="s_id" value="" />
                <input type="hidden" id="a_id_upgrade" name="a_id" value="" />
                <input type="hidden" id="g_id_upgrade" name="g_id" value="" />
                <input type="hidden" id="c_id_upgrade" name="c_id" value="" />
              </form>
              <div class="col-sm-5">
                <div class="tree-box">
                  <div class="tree-search">
                    <i class="tree-search-icon" id="searchNodeBtn01"></i> 
                    <input type="text" placeholder="搜索" id="searchNodeVal01" value="" class="empty fs-12" />
                  </div>
                  <div class="tree-scroll">
                    <ul id="treeDemo01" class="ztree"></ul>
                  </div>
                </div>
              </div>
              <div class="col-sm-2">
                <div class="toggle text-center">
                  <br><br><br><br><br><br><br>
                  <div>选择要升级到的年级班级===>></div>
                </div>
              </div>
              <div class="col-sm-5">
                <div class="tree-box">
                  <div class="tree-search">
                    <i class="tree-search-icon" id="searchNodeBtn02"></i> 
                    <input type="text" placeholder="搜索" id="searchNodeVal02" value="" class="empty fs-12" />
                  </div>
                  <div class="tree-scroll">
                    <ul id="treeDemo02" class="ztree"></ul>
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
    <button type="button" class="btn btn-success btn-sm js_upgradeBtn">
      <span class="fa fa-arrow-up"></span> 升级
    </button>
    <button type="button" class="btn btn-default btn-sm layui-layer-close">
      <span class="glyphicon glyphicon-remove"></span> 取消
    </button>
  </div>
</div>

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


<!-- js模版引擎 -->
<script type="text/javascript" src="/Public/dist/js/template.js"></script>

<!--文件上传-->
<script type="text/javascript" src="/Public/dist/js/jquery.form.js"></script>

<!-- 文件树搜索 -->
<script type="text/javascript" src="/Public/dist/js/jquery.ztree.exhide.min.js"></script>

<!-- 弹出层文件树滚动条 -->
<script type="text/javascript" src="/Public/dist/js/jquery.mCustomScrollbar.concat.min.js"></script>

<script type="text/javascript">
// 根据登陆角色权限 展示相应按钮
var user_type = "<?php echo ($user_type); ?>"; // 用户类型 1学校管理员 2系统用户 3老师 4家长 5省委 6市委 7县委
if (user_type == '4') {
  $('.js_tab_switch2, #tab2, #tab1 .navbar, #remove, th[data-checkbox="true"], th[data-formatter="studentFormatter"]').remove();
} else {
  $('th[data-formatter="editFormatter"]').remove();
};

// 表格数据 (学生信息)
var $g_params = {};
var $table    = $('#table_data');
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

// 表格数据 (绑定设备)
var $g_params2 = {};
var $table2    = $('#table_data2');
// table 功能设置
$table2.bootstrapTable({
  toolbar:"#toolbar2",                  // 工具栏
  toggle:"table",                       // 表格
  showToggle:true,                      // 是否显示(表格样式)切换
  showColumns:true,                     // 是否显示列(功能:[显示/隐藏]列)
  showExport:true,                      // 是否显示导出
  pagination:true,                      // 分页
  pageList:[10, 25, 50, 100],           // 每页行数
  ajax:"ajaxRequest2",                  // 数据地址
  sidePagination:"server"               // 服务器
});

// 自定义编辑表格中的操作内容
function studentFormatter(value, row, index) {
  return [
    '<button type="button" class="btn btn-sm btn-primary edit" data-color="#39B3D7" data-opacity="0.95">',
    '<span class="glyphicon glyphicon-edit"></span> 编辑',
    '</button>',
    '<button type="button" class="btn btn-sm btn-primary rowBinding" data-color="#39B3D7" data-opacity="0.95">',
    '<span class="fa fa-link"></span> 绑定',
    '</button>',
    '<button type="button" class="btn btn-sm btn-primary unwrap" data-color="#39B3D7" data-opacity="0.95">',
    '<span class="fa fa-chain-broken"></span> 解绑',
    '</button>'
  ].join('');
}

$(function() {
  // 升级年级 文件树1
  var settingCheckbox1 = {
    check: {
      enable: true,
      chkStyle: "checkbox",
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
    var zTree     = $.fn.zTree.getZTreeObj("treeDemo01");
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

    var zTree = $.fn.zTree.getZTreeObj("treeDemo01"),
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
  // 保持展开单一路径 end
  function zTreeOnCheckCheckbox(event, treeId, treeNode) {
    // 获取所有被选中的节点
    var treeObj = $.fn.zTree.getZTreeObj("treeDemo01");
    var nodes   = treeObj.getCheckedNodes(true);
    var grade   = [];
    for (var i = 0; i < 8; i++) {
      grade[i] = [];
      for (var t = 0; t < nodes.length; t++) {
        if(nodes[t].level==i) {
          grade[i].push(nodes[t].id);
        }
      };
      console.log(grade[i].join())
    };
    // console.log(grade[7].join())
    var user_type = "<?php echo ($user_type); ?>"; // 用户类型 1学校管理员 2系统用户 3老师 4家长 5省委 6市委 7县委
    if (user_type == '1' || user_type == '3') {
      $('#c_id_upgrade_if').val(grade[3].join());
      $('#stu_id_upgrade').val(grade[4].join()); // 隐藏域赋值(要升年级的学生id)
    } else {
      $('#c_id_upgrade_if').val(grade[6].join());
      $('#stu_id_upgrade').val(grade[7].join()); // 隐藏域赋值(要升年级的学生id)
    };
  }

  // 升级年级 文件树2
  var setting02 = {
    check: {
      enable: false,
      chkStyle: "radio",
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
      // beforeClick: beforeClick,
      beforeExpand: beforeExpand02,
      onExpand: onExpand02,
      onClick: zTreeOnClick02
    }
  };

  // 保持展开单一路径 start
  var curExpandNode = null;
  function beforeExpand02(treeId, treeNode) {
    var pNode     = curExpandNode ? curExpandNode.getParentNode():null;
    var treeNodeP = treeNode.parentTId ? treeNode.getParentNode():null;
    var zTree     = $.fn.zTree.getZTreeObj("treeDemo02");
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
      singlePath(treeNode);
    }
  }
  function singlePath(newNode) {
    if (newNode === curExpandNode) return;

    var zTree = $.fn.zTree.getZTreeObj("treeDemo02"),
      rootNodes, tmpRoot, tmpTId, i, j, n;

    if (!curExpandNode) {
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
    } else if (curExpandNode && curExpandNode.open) {
      if (newNode.parentTId === curExpandNode.parentTId) {
        zTree.expandNode(curExpandNode, false);
      } else {
        var newParents = [];
        while (newNode) {
          newNode = newNode.getParentNode();
          if (newNode === curExpandNode) {
            newParents = null;
            break;
          } else if (newNode) {
            newParents.push(newNode);
          }
        }
        if (newParents!=null) {
          var oldNode = curExpandNode;
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
    curExpandNode = newNode;
  }
  function onExpand02(event, treeId, treeNode) {
    curExpandNode = treeNode;
  }
  // 保持展开单一路径 end

  var bIfCheckArea = false;
  function zTreeOnClick02(event, treeId, treeNode) {
    var treeObj = $.fn.zTree.getZTreeObj("treeDemo02");
    var nodes   = treeObj.getSelectedNodes();
    for (var i=0, l=nodes.length; i < l; i++) {
      treeObj.checkNode(nodes[i], true, true);
    }
    // 点击选择
    if(treeNode.typeFlag == 'school') {
      $("#s_id_upgrade").val(treeNode.id); // 学校
      $("#a_id_upgrade").val(''); // 校区
      $("#g_id_upgrade").val(''); // 年级
      $("#c_id_upgrade").val(''); // 班级
    };
    if(treeNode.typeFlag == 'area') {
      $("#s_id_upgrade").val(treeNode.getParentNode().id); // 学校
      $("#a_id_upgrade").val(treeNode.id); // 校区
      $("#g_id_upgrade").val(''); // 年级
      $("#c_id_upgrade").val(''); // 班级
    };
    if(treeNode.typeFlag == 'grade') {
      $("#s_id_upgrade").val(treeNode.getParentNode().getParentNode().id); // 学校
      $("#a_id_upgrade").val(treeNode.getParentNode().id); // 校区
      $("#g_id_upgrade").val(treeNode.id); // 年级
      $("#c_id_upgrade").val(''); // 班级
    };
    if(treeNode.typeFlag == 'class') {
      $("#s_id_upgrade").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 学校
      $("#a_id_upgrade").val(treeNode.getParentNode().getParentNode().id); // 校区
      $("#g_id_upgrade").val(treeNode.getParentNode().id); // 年级
      $("#c_id_upgrade").val(treeNode.id); // 班级
    };
  }

  // 滚动条
  $("#upgradeformWindow .tree-scroll, .chosen-scroll").mCustomScrollbar({
    setHeight : 277,    
    scrollButtons : {
      enable : true
    },
    theme : "dark-3"
  });

  // 点击 升级年级
  $('.js_upgrade').click(function() {
    layer.open({
      type: 1,
      title: '升级年级',
      shadeClose: true,
      shade: 0.2,
      area: ['60%', '60%'],
      content: $('#upgrademodalWindow')
    });
    // 请求树1
    $.ajax({
      type: 'get',
      url: '/index.php/Admin/index/get_tree',
      dataType: 'json',
      success: function(msg) {
        var ajaxTreeNodesCheckbox = msg.data;
        $.fn.zTree.init($('#treeDemo01'), settingCheckbox1, ajaxTreeNodesCheckbox);
      },
      error: function(msg) {
        dialog.error('请求服务器异常！');
      }
    });
    // 请求树2
    $.ajax({
      type: "get",
      url: "/index.php/Admin/index/get_tree?type=class",
      dataType : 'json',
      success: function(msg) {
        var groupNodes = msg.data;
        $.fn.zTree.init($("#treeDemo02"), setting02, groupNodes);
      },
      error: function(msg) {
        dialog.error("请求服务器异常！");
      }
    });
  })

  // 点击确认升级按钮
  $('.js_upgradeBtn').click(function() {
    var bool = true;
    if ($('#c_id_upgrade_if').val().indexOf(',') > 0) {
      dialog.error('请选择某一个年级下的一个班级或某一个班级里的某些学生进行升级！');
      bool = false;
    };
    if ($('#stu_id_upgrade').val() == '') {
      dialog.error('请选择要升级的班级或学生！');
      bool = false;
    };
    if ($('#c_id_upgrade').val() == '') {
      dialog.error('请选择要升级到的年级下的班级！');
      bool = false;
    };
    if (bool) {
      var url = g_config.host + '/index.php/Admin/School/Student/Upgrade';
      fpost(url,'upgradeForm',btnCallbackRefreshTable1);
    };
  })

  // 文件树
  var setting = {
    check: {
      enable: false,
      chkStyle: "radio",
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
      // beforeClick: beforeClick,
      beforeExpand: beforeExpand,
      onExpand: onExpand,
      onCollapse: onCollapse,
      onClick: zTreeOnClick
    }
  };

  // 保持展开单一路径 start
  var curExpandNode = null;
  function beforeExpand(treeId, treeNode) {
    var pNode     = curExpandNode ? curExpandNode.getParentNode():null;
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
      singlePath(treeNode);
    }
  }
  function singlePath(newNode) {
    if (newNode === curExpandNode) return;

    var zTree = $.fn.zTree.getZTreeObj("treeDemo"),
      rootNodes, tmpRoot, tmpTId, i, j, n;

    if (!curExpandNode) {
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
    } else if (curExpandNode && curExpandNode.open) {
      if (newNode.parentTId === curExpandNode.parentTId) {
        zTree.expandNode(curExpandNode, false);
      } else {
        var newParents = [];
        while (newNode) {
          newNode = newNode.getParentNode();
          if (newNode === curExpandNode) {
            newParents = null;
            break;
          } else if (newNode) {
            newParents.push(newNode);
          }
        }
        if (newParents!=null) {
          var oldNode = curExpandNode;
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
    curExpandNode = newNode;
  }
  function onExpand(event, treeId, treeNode) {
    curExpandNode = treeNode;
    leftTreeScroll();
  }
  function onCollapse(event, treeId, treeNode) {
    curExpandNode = treeNode;
    leftTreeScroll();
  }

  // 请求树
  $.ajax({
    type: "get",
    url: "/index.php/Admin/index/get_tree?type=class",
    dataType : 'json',
    success: function(msg) {
      var groupNodes = msg.data;
      $.fn.zTree.init($("#treeDemo"), setting, groupNodes);
    },
    error: function(msg) {
      dialog.error("请求服务器异常！");
    }
  });
  // 保持展开单一路径 end

  var bIfCheckClass = false;
  function zTreeOnClick(event, treeId, treeNode) {
    var treeObj = $.fn.zTree.getZTreeObj("treeDemo");
    var nodes   = treeObj.getSelectedNodes();
    for (var i=0, l=nodes.length; i < l; i++) {
      treeObj.checkNode(nodes[i], true, true);
    }
    var user_type = "<?php echo ($user_type); ?>"; // 用户类型 1学校管理员 2系统用户 3老师 4家长 5省委 6市委 7县委
    if (user_type == '1') {
      // 点击选择
      if(treeNode.typeFlag == 'school') {
        $(".s_id_hide").val(treeNode.id); // 学校
        $(".a_id_hide").val(''); // 校区
        $(".g_id_hide").val(''); // 年级
        $(".c_id_hide").val(''); // 班级
      };
      if(treeNode.typeFlag == 'area') {
        $(".s_id_hide").val(treeNode.getParentNode().id); // 学校
        $(".a_id_hide").val(treeNode.id); // 校区
        $(".g_id_hide").val(''); // 年级
        $(".c_id_hide").val(''); // 班级
      };
      if(treeNode.typeFlag == 'grade') {
        $(".s_id_hide").val(treeNode.getParentNode().getParentNode().id); // 学校
        $(".a_id_hide").val(treeNode.getParentNode().id); // 校区
        $(".g_id_hide").val(treeNode.id); // 年级
        $(".c_id_hide").val(''); // 班级
      };
      if(treeNode.typeFlag == 'class') {
        $(".s_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 学校
        $(".a_id_hide").val(treeNode.getParentNode().getParentNode().id); // 校区
        $(".g_id_hide").val(treeNode.getParentNode().id); // 年级
        $(".c_id_hide").val(treeNode.id); // 班级
      };
    };
    if (user_type == '2' || user_type == '5') {
      // 点击选择
      if(treeNode.typeFlag == 'prov') {
        $(".prov_id_hide").val(treeNode.id); // 省
        $(".city_id_hide").val(''); // 市
        $(".county_id_hide").val(''); // 区县
        $(".s_id_hide").val(''); // 学校
        $(".a_id_hide").val(''); // 校区
        $(".g_id_hide").val(''); // 年级
        $(".c_id_hide").val(''); // 班级
      };
      if(treeNode.typeFlag == 'city') {
        $(".prov_id_hide").val(treeNode.getParentNode().id); // 省
        $(".city_id_hide").val(treeNode.id); // 市
        $(".county_id_hide").val(''); // 区县
        $(".s_id_hide").val(''); // 学校
        $(".a_id_hide").val(''); // 校区
        $(".g_id_hide").val(''); // 年级
        $(".c_id_hide").val(''); // 班级
      };
      if(treeNode.typeFlag == 'county') {
        $(".prov_id_hide").val(treeNode.getParentNode().getParentNode().id); // 省
        $(".city_id_hide").val(treeNode.getParentNode().id); // 市
        $(".county_id_hide").val(treeNode.id); // 区县
        $(".s_id_hide").val(''); // 学校
        $(".a_id_hide").val(''); // 校区
        $(".g_id_hide").val(''); // 年级
        $(".c_id_hide").val(''); // 班级
      };
      if(treeNode.typeFlag == 'school') {
        $(".prov_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 省
        $(".city_id_hide").val(treeNode.getParentNode().getParentNode().id); // 市
        $(".county_id_hide").val(treeNode.getParentNode().id); // 区县
        $(".s_id_hide").val(treeNode.id); // 学校
        $(".a_id_hide").val(''); // 校区
        $(".g_id_hide").val(''); // 年级
        $(".c_id_hide").val(''); // 班级
      };
      if(treeNode.typeFlag == 'area') {
        $(".prov_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 省
        $(".city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 市
        $(".county_id_hide").val(treeNode.getParentNode().getParentNode().id); // 区县
        $(".s_id_hide").val(treeNode.getParentNode().id); // 学校
        $(".a_id_hide").val(treeNode.id); // 校区
        $(".g_id_hide").val(''); // 年级
        $(".c_id_hide").val(''); // 班级
      };
      if(treeNode.typeFlag == 'grade') {
        $(".prov_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().id); // 省
        $(".city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 市
        $(".county_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 区县
        $(".s_id_hide").val(treeNode.getParentNode().getParentNode().id); // 学校
        $(".a_id_hide").val(treeNode.getParentNode().id); // 校区
        $(".g_id_hide").val(treeNode.id); // 年级
        $(".c_id_hide").val(''); // 班级
      };
      if(treeNode.typeFlag == 'class') {
        $(".prov_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().id); // 省
        $(".city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().id); // 市
        $(".county_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 区县
        $(".s_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 学校
        $(".a_id_hide").val(treeNode.getParentNode().getParentNode().id); // 校区
        $(".g_id_hide").val(treeNode.getParentNode().id); // 年级
        $(".c_id_hide").val(treeNode.id); // 班级
      };
    };
    if (user_type == '6') {
      // 点击选择
      if(treeNode.typeFlag == 'city') {
        $(".city_id_hide").val(treeNode.id); // 市
        $(".county_id_hide").val(''); // 区县
        $(".s_id_hide").val(''); // 学校
        $(".a_id_hide").val(''); // 校区
        $(".g_id_hide").val(''); // 年级
        $(".c_id_hide").val(''); // 班级
      };
      if(treeNode.typeFlag == 'county') {
        $(".city_id_hide").val(treeNode.getParentNode().id); // 市
        $(".county_id_hide").val(treeNode.id); // 区县
        $(".s_id_hide").val(''); // 学校
        $(".a_id_hide").val(''); // 校区
        $(".g_id_hide").val(''); // 年级
        $(".c_id_hide").val(''); // 班级
      };
      if(treeNode.typeFlag == 'school') {
        $(".city_id_hide").val(treeNode.getParentNode().getParentNode().id); // 市
        $(".county_id_hide").val(treeNode.getParentNode().id); // 区县
        $(".s_id_hide").val(treeNode.id); // 学校
        $(".a_id_hide").val(''); // 校区
        $(".g_id_hide").val(''); // 年级
        $(".c_id_hide").val(''); // 班级
      };
      if(treeNode.typeFlag == 'area') {
        $(".city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 市
        $(".county_id_hide").val(treeNode.getParentNode().getParentNode().id); // 区县
        $(".s_id_hide").val(treeNode.getParentNode().id); // 学校
        $(".a_id_hide").val(treeNode.id); // 校区
        $(".g_id_hide").val(''); // 年级
        $(".c_id_hide").val(''); // 班级
      };
      if(treeNode.typeFlag == 'grade') {
        $(".city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 市
        $(".county_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 区县
        $(".s_id_hide").val(treeNode.getParentNode().getParentNode().id); // 学校
        $(".a_id_hide").val(treeNode.getParentNode().id); // 校区
        $(".g_id_hide").val(treeNode.id); // 年级
        $(".c_id_hide").val(''); // 班级
      };
      if(treeNode.typeFlag == 'class') {
        $(".city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().id); // 市
        $(".county_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 区县
        $(".s_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 学校
        $(".a_id_hide").val(treeNode.getParentNode().getParentNode().id); // 校区
        $(".g_id_hide").val(treeNode.getParentNode().id); // 年级
        $(".c_id_hide").val(treeNode.id); // 班级
      };
    };
    if (user_type == '7') {
      // 点击选择
      if(treeNode.typeFlag == 'county') {
        $(".county_id_hide").val(treeNode.id); // 区县
        $(".s_id_hide").val(''); // 学校
        $(".a_id_hide").val(''); // 校区
        $(".g_id_hide").val(''); // 年级
        $(".c_id_hide").val(''); // 班级
      };
      if(treeNode.typeFlag == 'school') {
        $(".county_id_hide").val(treeNode.getParentNode().id); // 区县
        $(".s_id_hide").val(treeNode.id); // 学校
        $(".a_id_hide").val(''); // 校区
        $(".g_id_hide").val(''); // 年级
        $(".c_id_hide").val(''); // 班级
      };
      if(treeNode.typeFlag == 'area') {
        $(".county_id_hide").val(treeNode.getParentNode().getParentNode().id); // 区县
        $(".s_id_hide").val(treeNode.getParentNode().id); // 学校
        $(".a_id_hide").val(treeNode.id); // 校区
        $(".g_id_hide").val(''); // 年级
        $(".c_id_hide").val(''); // 班级
      };
      if(treeNode.typeFlag == 'grade') {
        $(".county_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 区县
        $(".s_id_hide").val(treeNode.getParentNode().getParentNode().id); // 学校
        $(".a_id_hide").val(treeNode.getParentNode().id); // 校区
        $(".g_id_hide").val(treeNode.id); // 年级
        $(".c_id_hide").val(''); // 班级
      };
      if(treeNode.typeFlag == 'class') {
        $(".county_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 区县
        $(".s_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 学校
        $(".a_id_hide").val(treeNode.getParentNode().getParentNode().id); // 校区
        $(".g_id_hide").val(treeNode.getParentNode().id); // 年级
        $(".c_id_hide").val(treeNode.id); // 班级
      };
    };
    if (user_type == '3') {
      // 点击选择
      if(treeNode.typeFlag == 'grade') {
        $(".g_id_hide").val(treeNode.id); // 年级
        $(".c_id_hide").val(''); // 班级
      };
      if(treeNode.typeFlag == 'class') {
        $(".g_id_hide").val(treeNode.getParentNode().id); // 年级
        $(".c_id_hide").val(treeNode.id); // 班级
      };
    };
    search();
    // 是否选择了班级
    if ($(".c_id_hide").val() != '') {
      bIfCheckClass = true;
    } else {
      bIfCheckClass = false;
    };
  }
  // 点击添加
  $('.select_add').click(function() {
    $('.js_sex input[type="radio"]').eq(0).iCheck('check').parents('.checkbox-inline').siblings('.checkbox-inline').find('input[type="radio"]').iCheck('uncheck'); // 性别 恢复默认选中
    if ($(".c_id_hide").val() != '' && bIfCheckClass) {
      $('#modalWindow input[type="text"]').val('');
      $('#modalWindow select').val('');
      $('#stu_id').val('');
      fndefaultShow();
      layer.open({
        type: 1,
        title: '新增',
        shadeClose: true,
        shade: 0.2,
        area: ['60%', '60%'],
        content:$('#modalWindow')
      });
      $('.js_info_cont').html($('#js_tianjia_cont').html());
    } else {
      dialog.error('请选择班级！');
    };
  })

  // 日期
  $.datetimepicker.setLocale('ch'); // 设置中文
  $('.dataTime').datetimepicker({
    // lang:"ch",           // 语言选择中文
    format:"Y-m-d",      // 格式化日期
    timepicker:false,    // 关闭时间选项
    yearStart:2000,      // 设置最小年份
    yearEnd:2050,        // 设置最大年份
    todayButton:true     // 关闭选择今天按钮
  });

  // 单选、复选
  $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
    checkboxClass: 'icheckbox_flat-green',
    radioClass: 'iradio_flat-green'
  });

  // 弹出层 默认显示
  function fndefaultShow() {
    $('.js_nav_tab2 li').eq(0).addClass('active').siblings('li').removeClass('active');
    $('.js_tab_content2 .tab-pane').eq(0).addClass('active').show().siblings('.tab-pane').removeClass('active').hide();
  }

  function fnparentInfo(index) {
    html += '<div class="row getLastRow js_getLastRow">' +
              '<div class="col-md-12">' +
                '<div class="row">' +
                  '<div class="col-md-6">' +
                    '<div class="form-group">' +
                      '<label class="col-md-4 control-label" for=""><span class="xing"></span> 监护人姓名(' + index + ')：</label>' +
                      '<div class="col-md-7">' +
                        '<input type="text" placeholder="请输入监护人姓名" id="parent_name" name="parent_name[]" class="form-control" />' +
                      '</div>' +
                    '</div>' +
                  '</div>' +
                  '<div class="col-md-6">' +
                    '<div class="form-group">' +
                      '<label class="col-md-3 control-label" for=""><span class="xing"></span> 关系(' + index + ')：</label>' +
                      '<div class="col-md-8">' +
                      '<select id="relation" name="relation[]" class="form-control">' +
                        '<option value="">请选择关系</option>' +
                        '<option value="1">爸爸</option>' +
                        '<option value="2">妈妈</option>' +
                        '<option value="3">爷爷</option>' +
                        '<option value="4">奶奶</option>' +
                        '<option value="5">外公</option>' +
                        '<option value="6">外婆</option>' +
                        '<option value="0">其它</option>' +
                      '</select>' +
                      '</div>' +
                    '</div>' +
                  '</div>' +
                '</div>' +
                '<div class="row">' +
                  '<div class="col-md-6">' +
                    '<div class="form-group">' +
                      '<label class="col-md-4 control-label" for=""><span class="xing"></span> 登录帐号(' + index + ')：</label>' +
                      '<div class="col-md-7">' +
                        '<input type="text" placeholder="平台及app登录帐号" id="username" name="username[]" class="form-control" />' +
                      '</div>' +
                    '</div>' +
                  '</div>' +
                  '<div class="col-md-6">' +
                    '<div class="form-group">' +
                      '<label class="col-md-3 control-label" for=""><span class="xing"></span> 邮箱(' + index + ')：</label>' +
                      '<div class="col-md-8">' +
                        '<input type="text" placeholder="请输入邮箱" id="email" name="email[]" class="form-control" />' +
                      '</div>' +
                    '</div>' +
                  '</div>' +
                '</div>' +
                '<div class="row">' +
                  '<div class="col-md-6">' +
                    '<div class="form-group">' +
                      '<label class="col-md-4 control-label" for=""><span class="xing"></span> 家庭电话(' + index + ')：</label>' +
                      '<div class="col-md-7">' +
                        '<input type="text" placeholder="请输入家庭电话" id="family_tel" name="family_tel[]" class="form-control" />' +
                      '</div>' +
                    '</div>' +
                  '</div>' +
                  '<div class="col-md-6">' +
                    '<div class="form-group">' +
                      '<label class="col-md-3 control-label" for=""><span class="xing"></span> 联系电话(' + index + ')：</label>' +
                      '<div class="col-md-8">' +
                        '<input type="text" placeholder="联系电话" id="parent_phone" name="parent_phone[]" class="form-control" />' +
                      '</div>' +
                    '</div>' +
                  '</div>' +
                '</div>' +
                '<div class="row">' +
                  '<div class="col-md-12">' +
                    '<div class="form-group">' +
                      '<label class="col-md-2 control-label" for=""><span class="xing"></span> 工作单位(' + index + ')：</label>' +
                      '<div class="col-md-9">' +
                        '<input type="text" placeholder="请输入工作单位" id="work_unit" name="work_unit[]" class="form-control" />' +
                      '</div>' +
                    '</div>' +
                  '</div>' +
                '</div>' +
                '<div class="row">' +
                  '<div class="col-md-12">' +
                    '<div class="form-group">' +
                      '<label class="col-md-2 control-label" for=""><span class="xing"></span> 常住地址(' + index + ')：</label>' +
                      '<div class="col-md-9">' +
                        '<input type="text" placeholder="请输入常住地址" id="address" name="address[]" class="form-control" />' +
                      '</div>' +
                    '</div>' +
                  '</div>' +
                '</div>' +
              '</div>' +
              '<div class="line"></div>' +
            '</div>'
  }

  // 点击批量解绑
  $('.batch_unwrap').click(function() {
    var url     = g_config.host + "/index.php/Admin/Device/DeviceManage/unwrap";
    var ids     = getIdSelections($('.js-custom-table-data'),'stu_id');
    var strIds  = ids.join(',');
    var data    = {};
    data.stu_id = strIds;

    if (ids.length == 0) {
      dialog.notify('请先选择要解绑的记录');
      return;
    } else {
      dialog.confirm('您确定要解绑当前选项吗？',function(){dpost(url,data,btnCallbackRefreshTable1);});
    }
  });

  window.studentoperateEvents = {
    // 点击绑定
    'click .rowBinding': function (e, value, row, index) {
      console.log(row.stu_id)
      $('.js_studNo').text(row.stu_no);
      $('.js_studentName').text(row.stu_name);
      $('#stu_id2').val(row.stu_id)

      ajaxRequest2($g_params2, row.s_id)
      layer.open({
        type: 1,
        title: '绑定设备',
        shadeClose: true,
        shade: 0.2,
        area: ['50%', '60%'],
        content:$('#modalWindow1')
      });
    },
    // 点击解绑
    'click .unwrap': function (e, value, row, index) {
      $('#stu_id2').val(row.stu_id);
      var url = g_config.host + '/index.php/Admin/Device/DeviceManage/unwrap';
      dialog.confirm('您确定要解绑当前选项吗？',function(){fpost(url,'formWindow1',btnCallbackRefreshTable1);});
    },
    // 点击编辑
    'click .edit': function (e, value, row, index) {
      var key = Object.keys(row)[0]; // 主键第一个值
      var url = g_config.host + '/index.php/Admin/School/Student/get';
      $('#stu_id').val(row.stu_id);
      custom_edit(row[key],url,{stu_id:row[key]},'formWindow','modalWindow', function(msg) {        
        // 性别赋值
        // console.log(msg.data.data.sex)
        $('.js_sex input[type="radio"]').each(function(index, el) {
          if ($(this).val() == msg.data.data.sex) {
            $(this).iCheck('check');
          } else {
            $(this).iCheck('uncheck');
          };
        });
        // 获取监护人信息
        var obj = msg.data.data1;
        for (var i = 0; i < obj.length; i++) {
          obj[i].index = i+1;
          var html = template('jhr_template', obj[i]);
          $('.js_info_cont').append(html);
          $('.js_info_cont select').each(function(){
            if ($(this).attr('value')!='') {
              $(this).find('option[value='+$(this).attr('value')+']').attr('selected',true);
            };
          })
        };
      },'json')
      fndefaultShow();
      $('.js_info_cont').html('');
    }
  };

  // 点击添加监护人
  $('.js_tab_content2').delegate('.select_parents', 'click', function() {
    html = '';
    var index = $('.js_info_cont').find('.js_getLastRow').length + 1;
    fnparentInfo(index);
    $('.js_info_cont').append(html);
  });

  // 弹出层tab切换
  $('.js_nav_tab2').delegate('li', 'click', function() {
    $(this).addClass('active').siblings('li').removeClass('active');
    $('.js_tab_content2 .tab-pane').eq($(this).index()).show().siblings('.tab-pane').hide();
  });
})

// 隐藏域赋值
function searchForm() {
  $("#searchForm #stu_no_hide").val($("#stu_no1").val());
  $("#searchForm #stu_name_hide").val($("#stu_name1").val());
  $("#searchForm #imei_id_hide").val($("#imei_id1").val());
  $("#searchForm #parent_name_hide").val($("#parent_name1").val());
  $("#searchForm #phone_hide").val($("#phone1").val());
}

// 自定义ajax请求
function ajaxRequest(params) {
  $g_params = params;
  searchForm();
  // 需要的数据
  var url = "/index.php/Admin/School/Student/query?" + $("#searchForm").serialize();
  queryList(url,params);
}

// 查询
function search() {
  // form查询域赋值
  searchForm();
  var url = "/index.php/Admin/School/Student/query?" + $("#searchForm").serialize();
  $g_params.data.page = parseInt($('.active .pagination li.active a').text());
  queryList(url,$g_params);
}

// 保存数据
function save() {
  var aList   = $('.js_info_cont .form-group');
  var bool    = true;
  var phoneRe = /^1[34578]\d{9}$/;
  var telRe   = /^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$/;
  var emailRe = /^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/;
  for (var i = 0; i < aList.length; i++) {
    var name  = aList.eq(i).find('label').text().replace('：','');
    var value = aList.eq(i).find('input').val();
    if (name.search(/\*/)>=0) {
      // 必填项
      if (value == '') { // 判断必填项为空时
        bool = false;
        dialog.error(name.replace('*','')+'不能为空');
        // 跳出循环
        break;
      };
      if (name.search('联系电话')>=0) {
        if (!phoneRe.test(value)) {
          bool = false;
          dialog.error(name.replace('*','')+'格式不正确');
          break;
        };
      };
    } else {
      // 非必填项
      if (value != '') { // 判断非必填项不为空时
        if (name.search('家庭电话')>=0) {
          if (!telRe.test(value)) {
            bool = false;
            dialog.error(name.replace('*','')+'格式不正确');
            break;
          };
        };
        if (name.search('邮箱')>=0) {
          if (!emailRe.test(value)) {
            bool = false;
            dialog.error(name.replace('*','')+'格式不正确');
            break;
          };
        };
      };
    };
  };

  // 保存提交
  if (bool) {
    var url = g_config.host + '/index.php/Admin/School/Student/edit';
    fpost(url,'formWindow',function(data) {
      if (!!data.data && data.data.type == 1) {
        $('#type_hide').val(data.data.type);
        dialog.confirm('您添写的' + data.data.name + '已存在，是否确定绑定此登陆账号？',function() {
          fpost(url,'formWindow',btnCallbackRefreshTable1);
          $('#type_hide').val('');
        });
      } else {
        btnCallbackRefreshTable1(data);
      }
    });
  }
}

// 删除
function del() {
  var url    = g_config.host + "/index.php/Admin/School/Student/del";
  var ids    = getIdSelections($('.js-custom-table-data'),'stu_id');
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

// 下载学生信息模板
$('.template').click(function() {
  window.open('/Public/template/学生信息模板.xls');
})
// 下载设备批量绑定模板
$('.deviceTemplate').click(function() {
  window.open('/Public/template/批量设备绑定模板.xls');
})

// 导入学生信息
$('.import').click(function() {
  if ($(".c_id_hide").val() == '') {
    dialog.error('请选择班级！');
  } else {
    var s_id = $("#searchForm .s_id_hide").val();
    var a_id = $("#searchForm .a_id_hide").val();
    var g_id = $("#searchForm .g_id_hide").val();
    var c_id = $("#searchForm .c_id_hide").val();
    var url  = '/index.php/Admin/School/Student/import?s_id='+s_id+'&a_id='+a_id+'&g_id='+g_id+'&c_id='+c_id;
    showImportModal(url,btnCallbackRefreshTable1);
  };
})

// 导入设备信息
$('.deviceImport').click(function() {
  if ($(".c_id_hide").val() == '') {
    dialog.error('请选择班级！');
  } else {
    var s_id = $("#searchForm .s_id_hide").val();
    var a_id = $("#searchForm .a_id_hide").val();
    var g_id = $("#searchForm .g_id_hide").val();
    var c_id = $("#searchForm .c_id_hide").val();
    var url  = '/index.php/Admin/Device/DeviceManage/import?s_id='+s_id+'&a_id='+a_id+'&g_id='+g_id+'&c_id='+c_id;
    showImportModal(url,btnCallbackRefreshTable1);
  };
})

// 编辑状态(启用,禁止)特殊字段处理
function setEditDataEx(data) {
  if(data.status == 0) {
    $('.start-up').parent('.iradio_flat-green').addClass('checked').attr('aria-checked', true);
    $('.prohibit').parent('.iradio_flat-green').removeClass('checked').attr('aria-checked', false);
  }
  else if(data.status == 1) {
    $('.start-up').parent('.iradio_flat-green').removeClass('checked').attr('aria-checked', false);
    $('.prohibit').parent('.iradio_flat-green').addClass('checked').attr('aria-checked', true);
  }
};

// 隐藏域赋值
function searchForm2() {
  $("#searchForm2 #imei_hide").val($("#imei2").val());
}

// 自定义ajax请求
function ajaxRequest2(params, id) {
  $g_params2 = params;
  searchForm2();
  // 需要的数据
  $("#searchForm2 #s_id_hide2").val(id);
  var url = "/index.php/Admin/School/Student/inguiry_unit?" + $("#searchForm2").serialize();
  queryList(url,params);
}

// 查询
function search2() {
  // form查询域赋值
  searchForm2();
  var url = "/index.php/Admin/School/Student/inguiry_unit?type=1&" + $("#searchForm2").serialize();
  $g_params2.data.page = parseInt($('.active .pagination li.active a').text());
  queryList(url,$g_params2);
}

// 确认绑定
function save2() {
  var ids = getIdSelections($('.js-custom-table-data2'),'dc_id');
  $('#stu_id2').val();
  $('#dc_id').val(ids);
  var url = g_config.host + '/index.php/Admin/Device/DeviceManage/edit';
  fpost(url,'formWindow1',btnCallbackRefreshTable1);
}

// 左侧文件树搜索
$('#searchNodeBtn').click(function() {
  searchNodeAll('treeDemo',$('#searchNodeVal'));
  leftTreeScroll();
});
$('#searchNodeVal').on('input', function() {
  searchNodeAll('treeDemo',$('#searchNodeVal'));
  leftTreeScroll();
});
// 升级年级文件树搜索
$('#searchNodeBtn01').click(function() {
  searchNodeAll('treeDemo01',$('#searchNodeVal01'));
});
$('#searchNodeVal01').on('input', function() {
  searchNodeAll('treeDemo01',$('#searchNodeVal01'));
});
$('#searchNodeBtn02').click(function() {
  searchNodeAll('treeDemo02',$('#searchNodeVal02'));
});
$('#searchNodeVal02').on('input', function() {
  searchNodeAll('treeDemo02',$('#searchNodeVal02'));
});
</script>
</body>
</html>