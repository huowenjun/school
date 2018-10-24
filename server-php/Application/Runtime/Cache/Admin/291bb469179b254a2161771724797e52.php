<?php if (!defined('THINK_PATH')) exit();?>
  <!-- Main Header -->
  <!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>教师管理</title>
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


  <!-- jquery.mCustomScrollbar -->
  <link rel="stylesheet" href="/Public/dist/css/jquery.mCustomScrollbar.css" />

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <ol class="breadcrumb">
        <li><a href="/index.php/Admin/Index.html"><i class="fa fa-home"></i> 首页</a></li>
        <li class="active">教师管理</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Your Page Content Here -->
      <div class="row">
        <div class="col-md-3">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">校区列表</h3>
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
              <li class="active"><a href="#tab1" data-toggle="tab">教师信息</a></li>
              <li><a href="#tab2" data-toggle="tab">分组管理</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab1">
                <nav class="navbar navbar-default">
                  <!-- container-fluid -->
                  <div class="container-fluid">
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="form-inline navbar-form navbar-left">
                      <div class="form-group">
                        <label for="group_name1">添加到分组：</label>
                        <select id="group_name1" class="form-control input-sm group_name1"></select>
                      </div>
                      <div class="form-group">
                        <label for="d_id1">部门：</label>
                        <select id="d_id1" class="form-control input-sm d_id1"></select>
                      </div>
                      <div class="form-group">
                        <label for="name1">姓名：</label>
                        <input id="name1" type="text" class="form-control input-sm" placeholder="请输入姓名" />
                      </div>
                      <div class="form-group">
                        <label for="user_name1">登录帐号：</label>
                        <input id="user_name1" type="text" class="form-control input-sm" placeholder="请输入登录帐号" />
                      </div>
                      <!-- <div class="form-group">
                        <label class="checkbox-inline">
                          <input id="tipsay1" type="checkbox" name="tipsay" class="flat-red" /> 代课
                        </label>
                      </div> -->
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
                        <button type="button" class="btn btn-success btn-sm template" title="下载教师信息模板">
                          <i class="fa fa-download"></i> 下载模板
                        </button>
                      </div>
                      <div class="form-group">
                        <button type="button" class="btn btn-success btn-sm import">
                          <i class="fa fa-download"></i> 批量导入
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
                      <!-- <div id="toolbar">
                        <div id="remove" class="btn btn-danger btn-sm" style="margin-top:12px">
                          <i class="fa fa-trash"></i> 删除
                        </div>
                      </div> -->
                      <table id="table_data" class="js-custom-table-data">
                        <thead>
                          <tr>
                            <th data-field="state" data-checkbox="true"></th>
                            <th data-align="center" data-formatter="schoolteachingFormatter" data-events="schoolteachingoperateEvents" name="schoolteachingFormatter">授课操作</th>
                            <th data-align="center" data-sortable="true" data-field="name" name="name">姓名</th>
                            <th data-align="center" data-sortable="false" data-field="a_id" name="a_id">校区</th>
                            <th data-align="center" data-sortable="false" data-field="d_id" name="d_id">部门</th>
                            <th data-align="center" data-sortable="false" data-field="sex" name="sex">性别</th>
                            <th data-align="center" data-sortable="false" data-field="email" name="email">邮件</th>
                            <th data-align="center" data-sortable="false" data-field="charge" name="charge">担任班主任</th>
                            <th data-align="center" data-sortable="false" data-field="valid" name="valid">状态</th>
                            <th data-align="center" data-sortable="false" data-field="phone" name="phone">联系方式</th>
                            <th data-align="center" data-sortable="false" data-field="user_name" name="user_name">登录帐号</th>
                          </tr>
                        </thead>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /#tab1 -->

              <div class="tab-pane" id="tab2">
                <nav class="navbar navbar-default">
                  <!-- container-fluid -->
                  <div class="container-fluid">
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="form-inline navbar-form navbar-left">
                      <div class="form-group">
                        <label for="group_name2">组名：</label>
                        <input id="group_name2" type="text" class="form-control input-sm" placeholder="请输入姓名" />
                      </div>
                      <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-sm query2">
                          <i class="fa fa-search"></i> 查询
                        </button>
                      </div>
                      <div class="form-group">
                        <button type="button" class="btn btn-success btn-sm select_add2">
                          <i class="fa fa-plus"></i> 添加
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
                      <div id="toolbar2">
                        <div id="remove2" class="btn btn-danger btn-sm" style="margin-top:12px">
                          <i class="fa fa-trash"></i> 删除
                        </div>
                      </div>
                      <table id="table_data2" class="js-custom-table-data">
                        <thead>
                          <tr>
                            <th data-field="state" data-checkbox="true"></th>
                            <th data-align="center" data-formatter="editFormatter" data-events="operateEvents2" name="editFormatter">操作</th>
                            <th data-align="center" data-sortable="true" data-field="group_name" name="group_name">组名</th>
                            <th data-align="center" data-sortable="false" data-field="memo" name="memo">备注</th>
                          </tr>
                        </thead>
                      </table>
                    </div>
                  </div>
                </div>
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

<!-- 教师信息 弹出层 -->
<div class="shade" id="modalWindow1">
  <div class="modal-body">
    <div class="container-fluid">
      <form class="form-horizontal" action="" method="post" onsubmit="return false" id="formWindow1">
        <input class="primaryKey" type="hidden" name="t_id" id="t_id" />
        <input type="hidden" class="prov_id_hide" id="prov_id" name="prov_id" value="" placeholder="省" />
        <input type="hidden" class="city_id_hide" id="city_id" name="city_id" value="" placeholder="市" />
        <input type="hidden" class="county_id_hide" id="county_id" name="county_id" value="" placeholder="区县" />
        <input type="hidden" class="s_id_hide" id="s_id" name="s_id" value="" placeholder="学校" />
        <input type="hidden" class="a_id_hide" id="a_id" name="a_id" value="" placeholder="校区" />
        <div class="row">
          <div class="col-md-12">
            <div class="clearfix">
              <p class="note pull-right">注：<span class="xing">*</span>标记为必填项</p>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="col-md-3 control-label" for="name"><span class="xing">*</span> 教师姓名：</label>
                  <div class="col-md-8">
                    <input type="text" id="name" name="name" class="form-control" placeholder="教师姓名" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-3 control-label" for="education"><span class="xing">*</span> 学历：</label>
                  <div class="col-md-8">
                    <select id="education" name="education" class="form-control">
                      <option value="1">初中</option>
                      <option value="2">中技</option>
                      <option value="3">高中</option>
                      <option value="4">中专</option>
                      <option value="5">大专</option>
                      <option value="6">本科</option>
                      <option value="7">硕士</option>
                      <option value="8">EMBA</option>
                      <option value="9">博士</option>
                      <option value="0">其他</option>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-3 control-label" for="email"><span class="xing"></span> 邮件：</label>
                  <div class="col-md-8">
                    <input type="text" id="email" name="email" class="form-control" placeholder="请输入邮箱地址" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-3 control-label" for="user_name"><span class="xing">*</span> 登录帐号：</label>
                  <div class="col-md-8">
                    <input type="text" id="user_name" name="user_name" class="form-control" placeholder="请输入登录账号" />
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="col-md-3 control-label" for="phone"><span class="xing">*</span> 联系方式：</label>
                  <div class="col-md-8">
                    <input type="text" id="phone" name="phone" class="form-control" placeholder="联系方式(手机)" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-3 control-label" for="d_id"><span class="xing">*</span> 部门：</label>
                  <div class="col-md-8">
                    <select id="d_id" name="d_id" class="form-control d_id"></select>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-3 control-label" for="sex">性别：</label>
                  <div class="col-md-8 js_sex">
                    <label class="checkbox-inline">
                      <input type="radio" id="" name="sex" class="flat-red" value="1" checked /> 男
                    </label>
                    <label class="checkbox-inline">
                      <input type="radio" id="" name="sex" class="flat-red" value="0" /> 女
                    </label>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-3 control-label" for="valid">状态：</label>
                  <div class="col-md-8 js_valid">
                    <label class="checkbox-inline">
                      <input type="radio" id="" name="valid" class="flat-red"  value="0" checked /> 有效
                    </label>
                    <label class="checkbox-inline">
                      <input type="radio" id="" name="valid" class="flat-red" value="1" /> 无效
                    </label>
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

<!-- 分组管理 编辑弹出层 -->
<div class="shade" id="modalWindow2">
  <div class="modal-body">
    <div class="container-fluid">
      <form class="form-horizontal" action="" method="post" onsubmit="return false" id="formWindow2">
        <input class="primaryKey" type="hidden" name="id" id="id" />
        <input type="hidden" class="prov_id_hide" id="prov_id" name="prov_id" value="" placeholder="省" />
        <input type="hidden" class="city_id_hide" id="city_id" name="city_id" value="" placeholder="市" />
        <input type="hidden" class="county_id_hide" id="county_id" name="county_id" value="" placeholder="区县" />
        <input type="hidden" class="s_id_hide" id="s_id" name="s_id" value="" placeholder="学校" />
        <input type="hidden" class="a_id_hide" id="a_id" name="a_id" value="" placeholder="校区" />
        <div class="row">
          <div class="col-md-12">
            <div class="clearfix">
              <p class="note pull-right">注：<span class="xing">*</span>标记为必填项</p>
            </div>
            <div class="form-group">
              <label class="col-md-3 control-label" for="group_name"><span class="xing">*</span> 组名：</label>
              <div class="col-md-8">
                <input type="text" id="group_name" name="group_name" class="form-control" placeholder="分组名称" />
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-3 control-label" for="memo">备注：</label>
              <div class="col-md-8">
                <textarea id="memo" name="memo" class="form-control" placeholder="备注"></textarea>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-3 control-label" for="teacher">教师：</label>
              <div class="col-md-8" id="teacher"></div>
              <input type="hidden" id="teacher_hide" name="teacher" />
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="modal-footer" style="text-align:center;">
    <button type="button" class="btn btn-success btn-sm save2">
      <span class="glyphicon glyphicon-save"></span> 保存
    </button>
    <button type="button" class="btn btn-default btn-sm layui-layer-close">
      <span class="glyphicon glyphicon-remove"></span> 取消
    </button>
  </div>
</div>

<!-- 教师信息授课 弹出层 -->
<div class="shade" style="" id="schoolteachingmodalWindow">
  <div class="modal-body">
    <div class="container-fluid">
      <form class="form-horizontal" action="" method="post" onsubmit="return false" id="schoolteachingformWindow">
        <input type="hidden" id="t_id" name="t_id" value="" />
        <!-- 授课管理-已选数据 -->
        <div id="chosenListForm" name="chosenListForm" action=""></div>

        <div class="row">
          <div class="col-md-12">
            <div class="form-group clearfix">
              <form class="form-horizontal">
                <label class="col-md-12" style="margin-bottom: 0;">教师姓名：<span class="js_teachername"></span></label>
              </form>
            </div>
            <div class="form-group clearfix">
              <form class="form-horizontal" id="myForm">
                <input type="hidden" value="" name="studydepartId" id="studydepartId" />
                <input type="hidden" value="" name="teacherId" id="teacherId" />
                <input type="hidden" value="" name="tempData" id="tempData" /> 
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
                  <button id="chosen-all-node" type="button" title="添加全部">&nbsp;&nbsp;&gt;&gt;&nbsp;&nbsp;</button>
                  <br> <br>
                  <button id="chosen-node" type="button" title="添加">&nbsp;&nbsp;&gt;&nbsp;&nbsp;</button>
                  <br> <br>
                  <button id="remove-node" type="button" title="移除">&nbsp;&nbsp;&lt;&nbsp;&nbsp;</button>
                  <br> <br>
                  <button id="remove-all-node" type="button" title="移除全部">&nbsp;&nbsp;&lt;&lt;&nbsp;&nbsp;</button>
                </div>
              </div>
              <div class="col-sm-5">
                <div class="tree-box">
                  <div class="chosen-title fs-12">
                    <a class="delete-all pull-right" id="delete-all" href="javascript:;">清空</a>
                    已选（<span id="chosen-number" class="chosen-number">0</span>/<span
                      id="all-number" class="all-number">10</span>）
                  </div>
                  <div class="chosen-scroll">
                    <ul class="chosen-list ztree" id="chosen-list"></ul>
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
    <button type="button" class="btn btn-success btn-sm save22">
      <span class="glyphicon glyphicon-save"></span> 保存
    </button>
    <button type="button" class="btn btn-default btn-sm layui-layer-close">
      <span class="glyphicon glyphicon-remove"></span> 取消
    </button>
  </div>
</div>

<!-- 隐藏域 -->
<div class="js_ztree_data" style="display:block; position:fixed; top:0; left:0; z-index:999999"></div>

<form id="searchForm" name="searchForm" action="" style="position: fixed; top: 0; right: 0; z-index: 99999;">
  <input type="hidden" class="prov_id_hide" name="prov_id" value="" placeholder="省" />
  <input type="hidden" class="city_id_hide" name="city_id" value="" placeholder="市" />
  <input type="hidden" class="county_id_hide" name="county_id" value="" placeholder="区县" />
  <input type="hidden" class="s_id_hide" name="s_id" value="" placeholder="学校" />
  <input type="hidden" class="a_id_hide" name="a_id" value="" placeholder="校区" />
  <input type="hidden" id="group_name_hide" name="group_name" value="" />
  <input type="hidden" id="d_id_hide" name="d_id" value="" />
  <input type="hidden" id="name_hide" name="name" value="" />
  <input type="hidden" id="user_name_hide" name="user_name" value="" />
  <input type="hidden" id="tipsay_hide" name="tipsay" value="" />
</form>

<form id="searchForm2" name="searchForm2" action="" style="position: fixed; top: 0; right: 0; z-index: 99999;">
  <input type="hidden" class="prov_id_hide" name="prov_id" value="" placeholder="省" />
  <input type="hidden" class="city_id_hide" name="city_id" value="" placeholder="市" />
  <input type="hidden" class="county_id_hide" name="county_id" value="" placeholder="区县" />
  <input type="hidden" class="s_id_hide" name="s_id" value="" placeholder="学校" />
  <input type="hidden" class="a_id_hide" name="a_id" value="" placeholder="校区" />
  <input type="hidden" id="group_name2_hide" name="group_name" value="" />
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


<!-- 双向选择 -->
<script type="text/javascript" src="/Public/dist/js/jquery.ztree.exhide.min.js"></script>
<script type="text/javascript" src="/Public/dist/js/ztree-teachering.js"></script>
<script type="text/javascript" src="/Public/dist/js/jquery.mCustomScrollbar.concat.min.js"></script>

<!--文件上传-->
<script type="text/javascript" src="/Public/dist/js/jquery.form.js"></script>

<script type="text/javascript">

// 添加到分组
$('#group_name1').change(function() {
  var val    = $(this).val();
  var url    = g_config.host + "/index.php/Admin/School/Teacher/group_add?id=" + val;
  var ids    = getIdSelections($('#table_data'),'t_id');
  var strIds = ids.join(',');
  var data   = {};
  data.t_id  = strIds;

  if (val != '') {
    if (ids.length == 0) {
      dialog.notify('请先选择要添加到此分组的选项');
      return;
    } else {
      dialog.confirm('您确定要将当前选项添加到此分组吗？',function(){fget(url,data,btnCallbackRefreshTable2);});
    }
  };
});

// 分组 编辑
window.operateEvents2 = {
  // tab2 点击编辑
  'click .edit': function (e, value, row, index) {
    $('#teacher').parents('.form-group').show();
    for(var key in row) {
      console.log(key+':'+row[key]);
    }
    var key = Object.keys(row)[0]; // 主键第一个值
    edit2(row[key]);

    $.get('/index.php/Admin/School/Teacher/get2?id='+row.id, function(data) {

      // 获取分组老师
      var htmls = '';
      for(var key in data.data.teacher) {
        htmls+= '<a class="btn btn-sm btn-default js_teach" id="' + key + '" style="margin-right: 5px; margin-top: 2px;">';
        htmls+= data.data.teacher[key] + '<i class="fa fa-times js_delTeacher" style="margin-left: 4px;"></i>';
        htmls+= '</a>';
      }
      $('#teacher').html(htmls);
      fnteacherHide();
    });
  }
};

// 分组老师隐藏域赋值
function fnteacherHide() {
  var obj = $('#teacher').find('.js_teach');
  var arr = [];
  $.each(obj, function(index, val) {
    arr.push($(val).attr('id'));
  });
  $('#teacher_hide').val(arr);
}

// 删除分组老师
$('#teacher').delegate('.js_delTeacher', 'click', function() {
  $(this).parents('.js_teach').remove();
  fnteacherHide();
})

// 下载教师信息模板
$('.template').click(function() {
  window.open('/Public/template/教师信息模板.xls');
})

// 自定义编辑 教师管理表格中的操作按钮
function schoolteachingFormatter(value, row, index) {
  return [
    '<button type="button" class="btn btn-sm btn-primary edit" data-color="#39B3D7" data-opacity="0.95">',
    '<span class="glyphicon glyphicon-edit"></span> 编辑',
    '</button>',
    '<button type="button" class="btn btn-sm btn-primary schoolteaching" data-color="#39B3D7" data-opacity="0.95">',
    '<span class="glyphicon glyphicon-pencil"></span> 授课',
    '</button>'
  ].join('');
}

$(function() {
  var $table    = $('#table_data');
  var $g_params = {};
  initSelectClass('group_name1',g_config.host+'/index.php/Admin/School/Teacher/get_list1',0,{type:'teachergroup'},true); // 分组
  initSelectClass('d_id1',g_config.host+'/index.php/Admin/School/Teacher/get_list1',0,{type:'dept'},true); // 部门

  // 搜索栏的部门---当下拉框有默认值时，需回调函数，请求Select下拉数据，写法如下：
  // initSelect('d_id1',g_config.host+'/index.php/Admin/Common/get_list',0,{type:'dept'},false,'undefined',function() {
  //   searchForm();
  //   // table 功能设置
  //   $table.bootstrapTable({
  //     toolbar:"#toolbar",                   // 工具栏
  //     toggle:"table",                       // 表格
  //     showToggle:true,                      // 是否显示(表格样式)切换
  //     showColumns:true,                     // 是否显示列(功能:[显示/隐藏]列)
  //     showExport:true,                      // 是否显示导出
  //     pagination:true,                      // 分页
  //     pageList:[10, 25, 50, 100],           // 每页行数
  //     ajax:"ajaxRequest",                   // 数据地址
  //     sidePagination:"server"               // 服务器
  //   });
  // });

  window.schoolteachingoperateEvents = {
    // 点击授课
    'click .schoolteaching': function (e, value, row, index) {
      $('#schoolteachingformWindow #t_id').val(row.t_id);
      $.ajax({
        type : "get",
        dataType : 'json',
        url : '/index.php/Admin/School/Teacher/get_teacher_course?t_id=' + $('#schoolteachingformWindow #t_id').val(),
        success : function(ret) {
            cNodes = ret.data;
            tree2 = $.fn.zTree.init($("#chosen-list"),setting, cNodes);
            $("#chosen-number").text(tree2.getNodes().length);
            forNodes(false);
        },
        error: function(msg) {
          dialog.error("请求服务器异常！");
        }
      });
      var name = row.name;
      layer.open({
        type: 1,
        title: '授课管理',
        shadeClose: true,
        shade: 0.2,
        area: ['60%', '60%'],
        content:$('#schoolteachingmodalWindow')
      });
      $('.js_teachername').text(name);

      // 请求授课弹出层文件树数据
      $.ajax({
        type : "get",
        dataType : 'json',
        url : '/index.php/Admin/School/Teacher/getTreeList?t_id=' + $('#schoolteachingformWindow #t_id').val(),
        success : function(ret) {
            zNodes = ret.data.data;
            tree1 = $.fn.zTree.init($("#treeDemo01"),setting, zNodes);
            $("#all-number").text(allNodeFilter());
        },
        error: function(msg) {
          dialog.error("请求服务器异常！");
        }
      });
    },
    // 点击编辑
    'click .edit': function (e, value, row, index) {
      $('#user_name').attr('disabled', true);
      var url = g_config.host + '/index.php/Admin/School/Teacher/get';
      custom_edit(row.t_id,url,{t_id:row.t_id},'formWindow1','modalWindow1',function(res) {
        var a_id = res.data.a_id;
        var d_id = res.data.d_id;
        initSelectClass('d_id',g_config.host+'/index.php/Admin/School/Teacher/get_list?a_id=' + a_id,0,{type:'teacher'},false,'undefined',function() {
          $('#d_id').val(d_id);
        }); // 部门

        // 性别赋值
        $('.js_sex input[type="radio"]').each(function(index, el) {
          if ($(this).val() == res.data.sex) {
            $(this).iCheck('check');
          } else {
            $(this).iCheck('uncheck');
          };
        });
        // 状态赋值
        $('.js_valid input[type="radio"]').each(function(index, el) {
          if ($(this).val() == res.data.valid) {
            $(this).iCheck('check');
          } else {
            $(this).iCheck('uncheck');
          };
        });
      });
    }
  };

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

  // 表格数据
  var $table2    = $('#table_data2');
  var $g_params2 = {};
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

  // 单选、复选
  $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
    checkboxClass: 'icheckbox_flat-green',
    radioClass: 'iradio_flat-green'
  });

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
    url: "/index.php/Admin/index/get_tree?type=area",
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

  var bIfCheckArea = false;
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
        var s_id = $("#searchForm .s_id_hide").val();
        initSelectClass('group_name1',g_config.host+'/index.php/Admin/School/Teacher/get_list1?s_id=' + s_id,0,{type:'teachergroup'},true); // 分组
      };
      if(treeNode.typeFlag == 'area') {
        $(".s_id_hide").val(treeNode.getParentNode().id); // 学校
        $(".a_id_hide").val(treeNode.id); // 校区
        var s_id = $("#searchForm .s_id_hide").val();
        var a_id = $("#searchForm .a_id_hide").val();
        initSelectClass('group_name1',g_config.host+'/index.php/Admin/School/Teacher/get_list1?s_id=' + s_id + '&a_id=' + a_id,0,{type:'teachergroup'},true); // 分组
        initSelectClass('d_id1',g_config.host+'/index.php/Admin/School/Teacher/get_list?a_id=' + a_id,0,{type:'teacher'},true); // 部门
      };
    }
    if (user_type == '2' || user_type == '5') {
      // 点击选择
      if(treeNode.typeFlag == 'prov') {
        $(".prov_id_hide").val(treeNode.id); // 省
        $(".city_id_hide").val(''); // 市
        $(".county_id_hide").val(''); // 区县
        $(".s_id_hide").val(''); // 学校
        $(".a_id_hide").val(''); // 校区
      };
      if(treeNode.typeFlag == 'city') {
        $(".prov_id_hide").val(treeNode.getParentNode().id); // 省
        $(".city_id_hide").val(treeNode.id); // 市
        $(".county_id_hide").val(''); // 区县
        $(".s_id_hide").val(''); // 学校
        $(".a_id_hide").val(''); // 校区
      };
      if(treeNode.typeFlag == 'county') {
        $(".prov_id_hide").val(treeNode.getParentNode().getParentNode().id); // 省
        $(".city_id_hide").val(treeNode.getParentNode().id); // 市
        $(".county_id_hide").val(treeNode.id); // 区县
        $(".s_id_hide").val(''); // 学校
        $(".a_id_hide").val(''); // 校区
      };
      if(treeNode.typeFlag == 'school') {
        $(".prov_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 省
        $(".city_id_hide").val(treeNode.getParentNode().getParentNode().id); // 市
        $(".county_id_hide").val(treeNode.getParentNode().id); // 区县
        $(".s_id_hide").val(treeNode.id); // 学校
        $(".a_id_hide").val(''); // 校区
        var s_id = $("#searchForm .s_id_hide").val();
        initSelectClass('group_name1',g_config.host+'/index.php/Admin/School/Teacher/get_list1?s_id=' + s_id,0,{type:'teachergroup'},true); // 分组
      };
      if(treeNode.typeFlag == 'area') {
        $(".prov_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 省
        $(".city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 市
        $(".county_id_hide").val(treeNode.getParentNode().getParentNode().id); // 区县
        $(".s_id_hide").val(treeNode.getParentNode().id); // 学校
        $(".a_id_hide").val(treeNode.id); // 校区
        var s_id = $("#searchForm .s_id_hide").val();
        var a_id = $("#searchForm .a_id_hide").val();
        initSelectClass('group_name1',g_config.host+'/index.php/Admin/School/Teacher/get_list1?s_id=' + s_id + '&a_id=' + a_id,0,{type:'teachergroup'},true); // 分组
        initSelectClass('d_id1',g_config.host+'/index.php/Admin/School/Teacher/get_list?a_id=' + a_id,0,{type:'teacher'},true); // 部门
      };
    }
    if (user_type == '6') {
      // 点击选择
      if(treeNode.typeFlag == 'city') {
        $(".city_id_hide").val(treeNode.id); // 市
        $(".county_id_hide").val(''); // 区县
        $(".s_id_hide").val(''); // 学校
        $(".a_id_hide").val(''); // 校区
      };
      if(treeNode.typeFlag == 'county') {
        $(".city_id_hide").val(treeNode.getParentNode().id); // 市
        $(".county_id_hide").val(treeNode.id); // 区县
        $(".s_id_hide").val(''); // 学校
        $(".a_id_hide").val(''); // 校区
      };
      if(treeNode.typeFlag == 'school') {
        $(".city_id_hide").val(treeNode.getParentNode().getParentNode().id); // 市
        $(".county_id_hide").val(treeNode.getParentNode().id); // 区县
        $(".s_id_hide").val(treeNode.id); // 学校
        $(".a_id_hide").val(''); // 校区
        var s_id = $("#searchForm .s_id_hide").val();
        initSelectClass('group_name1',g_config.host+'/index.php/Admin/School/Teacher/get_list1?s_id=' + s_id,0,{type:'teachergroup'},true); // 分组
      };
      if(treeNode.typeFlag == 'area') {
        $(".city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 市
        $(".county_id_hide").val(treeNode.getParentNode().getParentNode().id); // 区县
        $(".s_id_hide").val(treeNode.getParentNode().id); // 学校
        $(".a_id_hide").val(treeNode.id); // 校区
        var s_id = $("#searchForm .s_id_hide").val();
        var a_id = $("#searchForm .a_id_hide").val();
        initSelectClass('group_name1',g_config.host+'/index.php/Admin/School/Teacher/get_list1?s_id=' + s_id + '&a_id=' + a_id,0,{type:'teachergroup'},true); // 分组
        initSelectClass('d_id1',g_config.host+'/index.php/Admin/School/Teacher/get_list?a_id=' + a_id,0,{type:'teacher'},true); // 部门
      };
    }
    if (user_type == '7') {
      // 点击选择
      if(treeNode.typeFlag == 'county') {
        $(".county_id_hide").val(treeNode.id); // 区县
        $(".s_id_hide").val(''); // 学校
        $(".a_id_hide").val(''); // 校区
      };
      if(treeNode.typeFlag == 'school') {
        $(".county_id_hide").val(treeNode.getParentNode().id); // 区县
        $(".s_id_hide").val(treeNode.id); // 学校
        $(".a_id_hide").val(''); // 校区
        var s_id = $("#searchForm .s_id_hide").val();
        initSelectClass('group_name1',g_config.host+'/index.php/Admin/School/Teacher/get_list1?s_id=' + s_id,0,{type:'teachergroup'},true); // 分组
      };
      if(treeNode.typeFlag == 'area') {
        $(".county_id_hide").val(treeNode.getParentNode().getParentNode().id); // 区县
        $(".s_id_hide").val(treeNode.getParentNode().id); // 学校
        $(".a_id_hide").val(treeNode.id); // 校区
        var s_id = $("#searchForm .s_id_hide").val();
        var a_id = $("#searchForm .a_id_hide").val();
        initSelectClass('group_name1',g_config.host+'/index.php/Admin/School/Teacher/get_list1?s_id=' + s_id + '&a_id=' + a_id,0,{type:'teachergroup'},true); // 分组
        initSelectClass('d_id1',g_config.host+'/index.php/Admin/School/Teacher/get_list?a_id=' + a_id,0,{type:'teacher'},true); // 部门
      };
    }
    // if($('.tab-pane.active').attr('id') == 'tab1') { // 统考
      search();
    // } else { // 单考
      search2();
    // };
    // 是否选择了校区
    if ($(".a_id_hide").val() != '') {
      bIfCheckArea = true;
    } else {
      bIfCheckArea = false;
    };
  }

  // 导入教师信息
  $('.import').click(function() {
    if ($(".a_id_hide").val() != '' && bIfCheckArea) {
      var s_id = $("#searchForm .s_id_hide").val();
      var a_id = $("#searchForm .a_id_hide").val();
      var url  = '/index.php/Admin/School/Teacher/import?s_id='+s_id+'&a_id='+a_id;
      showImportModal(url,btnCallbackRefreshTable1);
    } else {
      dialog.error('请选择校区！');
    };
  })

  // 点击添加教师
  $('.select_add').click(function() {
    $('#user_name').attr('disabled', false);
    // 性别 恢复默认选中
    $('.js_sex input[type="radio"]').eq(0).iCheck('check').parents('.checkbox-inline').siblings('.checkbox-inline').find('input[type="radio"]').iCheck('uncheck'); 
    // 状态 恢复默认选中
    $('.js_valid input[type="radio"]').eq(0).iCheck('check').parents('.checkbox-inline').siblings('.checkbox-inline').find('input[type="radio"]').iCheck('uncheck');
    if ($(".a_id_hide").val() != '' && bIfCheckArea) {
      $('.primaryKey').val('0');
      edit(0);
      var a_id = $("#searchForm .a_id_hide").val();
      initSelectClass('d_id',g_config.host+'/index.php/Admin/School/Teacher/get_list?a_id=' + a_id,0,{type:'teacher'},false,'undefined',function(){}); // 部门
    } else {
      dialog.error('请选择校区！');
    };
  })

  // 点击添加分组
  $('.select_add2').click(function() {
    $('#teacher').parents('.form-group').hide();
    if ($(".a_id_hide").val() != '' && bIfCheckArea) {
      $('.primaryKey').val('0');
      edit2(0);
    } else {
      dialog.error('请选择校区！');
    };
  })
})

// 隐藏域赋值
function searchForm() {
  $("#searchForm #d_id_hide").val($("#d_id1").val());
  $("#searchForm #name_hide").val($("#name1").val());
  $("#searchForm #user_name_hide").val($("#user_name1").val());
  $("#searchForm #tipsay_hide").val($("#tipsay1").val());
}

// 自定义ajax请求
function ajaxRequest(params) {
  $g_params = params;
  searchForm();
  // 需要的数据
  var url = "/index.php/Admin/School/Teacher/query?" + $("#searchForm").serialize();
  queryList(url,params);
}

// 查询
function search() {
  // form查询域赋值
  searchForm();
  var url = "/index.php/Admin/School/Teacher/query?" + $("#searchForm").serialize();
  $g_params.data.page = parseInt($('.active .pagination li.active a').text());
  queryList(url,$g_params);
}

// 新增/编辑
function edit(id) {
  var url = g_config.host + '/index.php/Admin/School/Teacher/get';
  custom_edit(id,url,{t_id:id},'formWindow1','modalWindow1');
}

// 保存数据
function save() {
  var url = g_config.host + '/index.php/Admin/School/Teacher/edit';
  fpost(url,'formWindow1',btnCallbackRefreshTable1);
}

// 删除
function del() {
  var url    = g_config.host + "/index.php/Admin/School/Teacher/del";
  var ids    = getIdSelections($('#table_data'),'t_id');
  var strIds = ids.join(',');
  var data   = {};
  data.t_id  = strIds;

  if (ids.length == 0) {
    dialog.notify('请先选择要删除的记录');
    return;
  } else {
    dialog.confirm('您确定要删除当前选项吗？',function(){dpost(url,data,btnCallbackRefreshTable1);});
  }
}

// 隐藏域赋值
function searchForm2() {
  $("#searchForm2 #group_name2_hide").val($("#group_name2").val());
}

// 自定义ajax请求
function ajaxRequest2(params) {
  $g_params2 = params;
  searchForm2();
  // 需要的数据
  var url = "/index.php/Admin/School/Teacher/query2?" + $("#searchForm2").serialize();
  queryList(url,params);
}

// 查询
function search2() {
  // form查询域赋值
  searchForm2();
  var url = "/index.php/Admin/School/Teacher/query2?" + $("#searchForm2").serialize();
  $g_params2.data.page = parseInt($('.active .pagination li.active a').text());
  queryList(url,$g_params2);
}

// 新增/编辑
function edit2(id) {
  var url = g_config.host + '/index.php/Admin/School/Teacher/get2';
  custom_edit(id,url,{id:id},'formWindow2','modalWindow2');
}

// 保存数据
function save2() {
  var url = g_config.host + '/index.php/Admin/School/Teacher/edit2';
  fpost(url,'formWindow2',btnCallbackRefreshTable2);
}

// 授课保存
$('.save22').click(function() {
  var url = g_config.host + '/index.php/Admin/School/Teacher/lecture';
  fpost(url,'schoolteachingformWindow',btnCallbackRefreshTable1);
})

// 删除
function del2() {
  var url    = g_config.host + "/index.php/Admin/School/Teacher/del2";
  var ids    = getIdSelections($('#table_data2'),'id');
  var strIds = ids.join(',');
  var data   = {};
  data.id    = strIds;

  if (ids.length == 0) {
    dialog.notify('请先选择要删除的记录');
    return;
  } else {
    dialog.confirm('您确定要删除当前选项吗？',function(){dpost(url,data,btnCallbackRefreshTable2);});
  }
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
// 授课文件树搜索
$('#searchNodeBtn01').click(function() {
  searchNodeAll('treeDemo01',$('#searchNodeVal01'));
});
$('#searchNodeVal01').on('input', function() {
  searchNodeAll('treeDemo01',$('#searchNodeVal01'));
});

</script>
</body>
</html>