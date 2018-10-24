<?php if (!defined('THINK_PATH')) exit();?>
  <!-- Main Header -->
  <!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>成绩管理</title>
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

  <!-- select筛选 -->
  <link rel="stylesheet" type="text/css" href="http://cdn.bootcss.com/bootstrap-select/2.0.0-beta1/css/bootstrap-select.css" />

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
        <li class="active">成绩管理</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Your Page Content Here -->
      <div class="row">
        <div class="col-md-3">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">年级列表</h3>
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
              <li class="active"><a href="#tab1" data-toggle="tab">统考成绩</a></li>
              <li><a href="#tab2" data-toggle="tab">单考成绩</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab1">
                <nav class="navbar navbar-default">
                  <!-- container-fluid -->
                  <div class="container-fluid">
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="form-inline navbar-form navbar-left">
                      <div class="form-group">
                        <label for="e_id1">统考名称：</label>
                        <select id="e_id1" class="form-control input-sm e_id1 selectpicker" data-live-search="true">
                          <option value="">请选择</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="stu_id1">学生姓名：</label>
                        <input id="stu_id1" type="text" class="form-control input-sm" placeholder="请输入学生姓名" />
                      </div>
                      <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-sm query">
                          <i class="fa fa-search"></i> 查询
                        </button>
                      </div>
                      <div class="form-group">
                        <button type="button" class="btn btn-success btn-sm select_add" onclick="examAll_add()">
                          <i class="fa fa-plus"></i> 添加
                        </button>
                      </div>
                      <div class="form-group">
                        <button type="button" class="btn btn-success btn-sm template">
                          <i class="fa fa-download"></i> 下载模板
                        </button>
                      </div>
                      <div class="form-group">
                        <button type="button" class="btn btn-success btn-sm examAll_import">
                          <i class="fa fa-download"></i> 批量导入
                        </button>
                      </div>
                      <div class="form-group">
                        <button type="button" class="btn btn-success btn-sm" onclick="method('js-table-data')">
                          <i class="fa fa-share"></i> 导出数据
                        </button>
                      </div>
                    </div>
                  </div>
                  <!-- /.container-fluid -->
                </nav>

                <!-- table -->
                <div class="content-wrap">
                  <div class="custom-fixed-table-container">
                    <div class="row">
                      <div class="col-md-12" id="tab_data"></div>
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
                        <label for="ex_id2">单考名称：</label>
                        <select id="ex_id2" class="form-control input-sm ex_id2 selectpicker" data-live-search="true">
                          <option value="">请选择</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <label for="crs_id2">学科名称：</label>
                        <input id="crs_id2" type="text" class="form-control input-sm" placeholder="请输入学科名称" />
                      </div>
                      <div class="form-group">
                        <label for="stu_id2">学生姓名：</label>
                        <input id="stu_id2" type="text" class="form-control input-sm" placeholder="请输入学生姓名" />
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
                      <div class="form-group">
                        <button type="button" class="btn btn-success btn-sm template">
                          <i class="fa fa-download"></i> 下载模板
                        </button>
                      </div>
                      <div class="form-group">
                        <button type="button" class="btn btn-success btn-sm exam_import">
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
                      <div id="toolbar2">
                        <div id="remove2" class="btn btn-danger btn-sm" style="margin-top:12px">
                          <i class="fa fa-trash"></i> 删除
                        </div>
                      </div>
                      <table id="table_data2" class="js-custom-table-data">
                        <thead>
                          <tr>
                            <th data-field="state" data-checkbox="true"></th>
                            <th data-align="center" data-formatter="editFormatter" data-events="exoperateEvents" name="editFormatter">操作</th>
                            <th data-align="center" data-sortable="true" data-field="c_id" name="c_id">班级</th>
                            <th data-align="center" data-sortable="false" data-field="stu_id" name="stu_id">姓名</th>
                            <th data-align="center" data-sortable="false" data-field="crs_id" name="crs_id">科目</th>
                            <th data-align="center" data-sortable="false" data-field="ex_id" name="ex_id">考试名称</th>
                            <!-- <th data-align="center" data-sortable="false" data-field="exam_type" name="exam_type">考试类型</th> -->
                            <th data-align="center" data-sortable="false" data-field="scores" name="scores">成绩</th>
                            <th data-align="center" data-sortable="false" data-field="create_time" name="create_time">发布时间</th>
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

<!-- 统考导入弹出层 -->
<div class="shade" style="" id="importModalWindow1">
  <div class="modal-body">
    <div class="container-fluid">
      <form class="form-horizontal" action="" method="post" onsubmit="return false" id="importFormWindow1">
        <input type="hidden" class="prov_id_hide" name="prov_id" value="" placeholder="省" />
        <input type="hidden" class="city_id_hide" name="city_id" value="" placeholder="市" />
        <input type="hidden" class="county_id_hide" name="county_id" value="" placeholder="区县" />
        <input type="hidden" class="s_id_hide" name="s_id" value="" placeholder="学校" />
        <input type="hidden" class="a_id_hide" name="a_id" value="" placeholder="校区" />
        <input type="hidden" class="g_id_hide" name="g_id" value="" placeholder="年级" />
        <input type="hidden" class="c_id_hide" name="c_id" value="" placeholder="班级" />
        <div class="row">
          <div class="col-md-12">
            <div class="clearfix">
              <p class="note pull-right">注：<span class="xing">*</span>标记为必填项</p>
            </div>
            <div class="form-group">
              <label class="col-md-3 control-label" for="e_id01"><span class="xing">*</span> 考试名称：</label>
              <div class="col-md-8">
                <select id="e_id01" name="e_id" class="form-control input-sm e_id01"></select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-3 control-label" for="crs_id01"><span class="xing">*</span> 课程：</label>
              <div class="col-md-8">
                <select id="crs_id01" name="crs_id" class="form-control input-sm crs_id01"></select>
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-8 col-md-offset-3">
                <button type="button" class="btn btn-primary btn-sm import1">
                  <i class="fa fa-fw fa-upload"></i> 上传文件
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="modal-footer" style="text-align:center;">
    <button type="button" class="btn btn-default btn-sm layui-layer-close">
      <span class="glyphicon glyphicon-remove"></span> 取消
    </button>
  </div>
</div>

<!-- 单考导入弹出层 -->
<div class="shade" style="" id="importModalWindow2">
  <div class="modal-body">
    <div class="container-fluid">
      <form class="form-horizontal" action="" method="post" onsubmit="return false" id="importFormWindow2">
        <input type="hidden" class="prov_id_hide" name="prov_id" value="" placeholder="省" />
        <input type="hidden" class="city_id_hide" name="city_id" value="" placeholder="市" />
        <input type="hidden" class="county_id_hide" name="county_id" value="" placeholder="区县" />
        <input type="hidden" class="s_id_hide" name="s_id" value="" placeholder="学校" />
        <input type="hidden" class="a_id_hide" name="a_id" value="" placeholder="校区" />
        <input type="hidden" class="g_id_hide" name="g_id" value="" placeholder="年级" />
        <input type="hidden" class="c_id_hide" name="c_id" value="" placeholder="班级" />
        <div class="row">
          <div class="col-md-12">
            <div class="clearfix">
              <p class="note pull-right">注：<span class="xing">*</span>标记为必填项</p>
            </div>
            <div class="form-group">
              <label class="col-md-3 control-label" for="ex_id02"><span class="xing">*</span> 考试名称：</label>
              <div class="col-md-8">
                <select id="ex_id02" name="ex_id" class="form-control input-sm ex_id02"></select>
              </div>
            </div>
            <div class="form-group">
              <label class="col-md-3 control-label" for="crs_id02"><span class="xing">*</span> 课程：</label>
              <div class="col-md-8">
                <select id="crs_id02" name="crs_id" class="form-control input-sm crs_id02"></select>
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-8 col-md-offset-3">
                <button type="button" class="btn btn-primary btn-sm import2">
                  <i class="fa fa-fw fa-upload"></i> 上传文件
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="modal-footer" style="text-align:center;">
    <button type="button" class="btn btn-default btn-sm layui-layer-close">
      <span class="glyphicon glyphicon-remove"></span> 取消
    </button>
  </div>
</div>

<!-- 统考 隐藏域 -->
<form id="searchForm" name="searchForm" action="" style="position: fixed; top: 0; right: 0; z-index: 99999;">
  <input type="hidden" class="prov_id_hide" name="prov_id" value="" placeholder="省" />
  <input type="hidden" class="city_id_hide" name="city_id" value="" placeholder="市" />
  <input type="hidden" class="county_id_hide" name="county_id" value="" placeholder="区县" />
  <input type="hidden" class="s_id_hide" name="s_id" value="" placeholder="学校" />
  <input type="hidden" class="a_id_hide" name="a_id" value="" placeholder="校区" />
  <input type="hidden" class="g_id_hide" name="g_id" value="" placeholder="年级" />
  <input type="hidden" class="c_id_hide" name="c_id" value="" placeholder="班级" />
  <input type="hidden" class="stu_id_hide" name="stu_id" value="" placeholder="学生" />
  <input type="hidden" id="e_id_hide" name="e_id" value="" />
  <input type="hidden" id="stu_id_hide" name="stu_name_id" value="" placeholder="学生姓名" />
</form>
<!-- 单考 隐藏域 -->
<form id="searchForm2" name="searchForm" action="" style="position: fixed; top: 0; right: 0; z-index: 99999;">
  <input type="hidden" class="prov_id_hide" name="prov_id" value="" placeholder="省" />
  <input type="hidden" class="city_id_hide" name="city_id" value="" placeholder="市" />
  <input type="hidden" class="county_id_hide" name="county_id" value="" placeholder="区县" />
  <input type="hidden" class="s_id_hide" name="s_id" value="" placeholder="学校" />
  <input type="hidden" class="a_id_hide" name="a_id" value="" placeholder="校区" />
  <input type="hidden" class="g_id_hide" name="g_id" value="" placeholder="年级" />
  <input type="hidden" class="c_id_hide" name="c_id" value="" placeholder="班级" />
  <input type="hidden" class="stu_id_hide" name="stu_id" value="" placeholder="学生" />
  <input type="hidden" id="ex_id_hide" name="ex_id" value="" />
  <input type="hidden" id="crs_id_hide" name="crs_id" value="" />
  <input type="hidden" id="stu_id_hide" name="stu_name_id" value="" placeholder="学生姓名" />
</form>

<!-- 统考成绩模版 -->
<script id="resultTemplate" type="text/html">
<table id="js-table-data" class="custom_table_data">
  <thead>
    <tr>
      <th class="text-center">班级</th>
      <th class="text-center">姓名</th>
      {{each course as value}}
        <th class="text-center">{{value}}</th>
      {{/each}}
      <th class="text-center">总分</th>
      <th class="text-center">平均分</th>
      <th class="text-center">排名</th>
      <th class="text-center js_showHide">编辑</th>
    </tr>
  </thead>
  <tbody>
    {{each info as val index}}
      <tr>
        <td class="text-center">{{val.c_id}}</td>
        <td class="text-center">{{val.name}}</td>
        {{each val.result as value inx}}
          <td class="text-center">{{value}}</td>
        {{/each}}
        <td class="text-center">{{val.sum}}</td>
        <td class="text-center">{{(val.sum/course.length).toFixed(2)}}</td>
        <td class="text-center">{{val.ranking}}</td>
        <td class="text-center js_showHide">
          <button type="button" class="btn btn-sm btn-primary" onclick="edits('{{val.stu_id}}')">
            <i class="glyphicon glyphicon-edit"></i> 编辑
          </button>
        </td>
      </tr>
    {{/each}}
  </tbody>
</table>
</script>

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

<!-- cookie -->
<script type="text/javascript" src="/Public/dist/js/jquery.cookie.js"></script>
<!-- js模板引擎 -->
<script type="text/javascript" src="/Public/dist/js/template.js"></script>

<!--文件上传-->
<script type="text/javascript" src="/Public/dist/js/jquery.form.js"></script>

<!-- 文件树搜索 -->
<script type="text/javascript" src="/Public/dist/js/jquery.ztree.exhide.min.js"></script>

<!-- select筛选 -->
<!-- <script type="text/javascript" src="http://cdn.bootcss.com/bootstrap-select/2.0.0-beta1/js/bootstrap-select.js"></script> -->
<script type="text/javascript" src="/Public/assets/js/bootstrap-table/bootstrap-select.js"></script>

<script type="text/javascript">

// select筛选初始化
function fninitSelect() {
  $('.selectpicker').selectpicker({
    'selectedText': 'cat',
    'val': ''
  });
  $('.selectpicker').selectpicker('refresh');
}

// 页面加载完select筛选先初始化一次
$(window).on('load', function() {
  fninitSelect();
});

// 根据登陆角色权限 展示相应按钮
var user_type = "<?php echo ($user_type); ?>"; // 用户类型 1学校管理员 2系统用户 3老师 4家长 5省委 6市委 7县委
if (user_type == '4') {
  $('.select_add, .select_add2, .examAll_import, .exam_import, .template').parents('.form-group').remove();
  $(' #remove2, th[data-checkbox="true"], th[data-formatter="editFormatter"]').remove();
}

var bIfCheckGrade = false;
var bIfCheckClass = false;

// 点击统考批量导入按钮
$('.examAll_import').click(function() {
  if ($(".c_id_hide").val() == '') {
    dialog.error('请选择班级！');
  } else {
    layer.open({
      type: 1,
      title: '批量导入统考成绩',
      shadeClose: true,
      shade: 0.2,
      area: ['60%', '60%'],
      content: $('#importModalWindow1')
    });
  }
  var g_id = $('#searchForm .g_id_hide').val();
  initSelectClass('e_id01',g_config.host+'/index.php/Admin/Parent/Result/get_examAll?g_id=' + g_id,0,{type:'examname'},true); // 统考名称
})
// 点击单考批量导入按钮
$('.exam_import').click(function() {
  if ($(".c_id_hide").val() == '') {
    dialog.error('请选择班级！');
  } else {
    layer.open({
      type: 1,
      title: '批量导入单考成绩',
      shadeClose: true,
      shade: 0.2,
      area: ['60%', '60%'],
      content: $('#importModalWindow2')
    });
  }
  var g_id = $('#searchForm .g_id_hide').val();
  initSelectClass('ex_id02',g_config.host+'/index.php/Admin/Parent/Result/get_exam?g_id=' + g_id,0,{type:'examname'},true); // 单考名称
})

// 获取统考导入弹出层学科下拉框数据
$('#e_id01').change(function() {
  var e_id = $(this).val();
  initSelectClass('crs_id01',g_config.host+'/index.php/Admin/Parent/Result/course?e_id=' + e_id,0,{type:'course'},true); // 统考科目
})
// 获取单考导入弹出层学科下拉框数据
$('#ex_id02').change(function() {
  var ex_id = $(this).val();
  initSelectClass('crs_id02',g_config.host+'/index.php/Admin/Parent/Result/course2?ex_id=' + ex_id,0,{type:'course'},true); // 单考科目
})

// 导入统考成绩
$('.import1').click(function() {
  if ($("#e_id01").val() == '' || $("#crs_id01").val() == '') {
    dialog.error('请选择考试名称和科目！');
  } else {
    var url = '/index.php/Admin/Parent/Result/import1?' + $("#importFormWindow1").serialize();
    showImportModal(url,btnCallbackRefresh);
  };
})
// 导入单考成绩
$('.import2').click(function() {
  if ($("#ex_id02").val() == '' || $("#crs_id02").val() == '') {
    dialog.error('请选择考试名称和科目！');
  } else {
    var url = '/index.php/Admin/Parent/Result/import2?' + $("#importFormWindow2").serialize();
    showImportModal(url,btnCallbackRefreshTable2);
  };
})

// 表格数据
var $g_params = {};
var $table2   = $('#table_data2');
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
  url: "/index.php/Admin/index/get_tree",
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

function zTreeOnClick(event, treeId, treeNode) {
  var treeObj = $.fn.zTree.getZTreeObj("treeDemo");
  // treeObj.expandNode(treeNode, null, null, null, true); // 单击文件名展开文件夹设置

  var nodes   = treeObj.getSelectedNodes();
  for (var i=0, l=nodes.length; i < l; i++) {
    treeObj.checkNode(nodes[i], true, true);
  }
  if (user_type == '1') {
    // 点击选择
    if(treeNode.typeFlag == 'school') {
      $(".s_id_hide").val(treeNode.id); // 学校
      $(".a_id_hide").val(''); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      if ($('.tab-pane.active').attr('id') == 'tab1') { // 统考
        dialog.error('请选择年级！');
      } else {
        dialog.error('请选择班级！');
      };
    };
    if(treeNode.typeFlag == 'area') {
      $(".s_id_hide").val(treeNode.getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.id); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      if ($('.tab-pane.active').attr('id') == 'tab1') { // 统考
        dialog.error('请选择年级！');
      } else {
        dialog.error('请选择班级！');
      };
    };
    if(treeNode.typeFlag == 'grade') {
      $(".s_id_hide").val(treeNode.getParentNode().getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.getParentNode().id); // 校区
      $(".g_id_hide").val(treeNode.id); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      if ($('.tab-pane.active').attr('id') == 'tab1') { // 单考
        fnTabPaneActive();
      } else {
        dialog.error('请选择班级！');
      }
    };
    if(treeNode.typeFlag == 'class') {
      $(".s_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.getParentNode().getParentNode().id); // 校区
      $(".g_id_hide").val(treeNode.getParentNode().id); // 年级
      $(".c_id_hide").val(treeNode.id); // 班级
      $(".stu_id_hide").val(''); // 学生
      fnTabPaneActive();
    };
    if(treeNode.typeFlag == 'student') {
      $(".s_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 校区
      $(".g_id_hide").val(treeNode.getParentNode().getParentNode().id); // 年级
      $(".c_id_hide").val(treeNode.getParentNode().id); // 班级
      $(".stu_id_hide").val(treeNode.id); // 学生
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
      $(".stu_id_hide").val(''); // 学生
      if ($('.tab-pane.active').attr('id') == 'tab1') { // 统考
        dialog.error('请选择年级！');
      } else {
        dialog.error('请选择班级！');
      };
    };
    if(treeNode.typeFlag == 'city') {
      $(".prov_id_hide").val(treeNode.getParentNode().id); // 省
      $(".city_id_hide").val(treeNode.id); // 市
      $(".county_id_hide").val(''); // 区县
      $(".s_id_hide").val(''); // 学校
      $(".a_id_hide").val(''); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      if ($('.tab-pane.active').attr('id') == 'tab1') { // 统考
        dialog.error('请选择年级！');
      } else {
        dialog.error('请选择班级！');
      };
    };
    if(treeNode.typeFlag == 'county') {
      $(".prov_id_hide").val(treeNode.getParentNode().getParentNode().id); // 省
      $(".city_id_hide").val(treeNode.getParentNode().id); // 市
      $(".county_id_hide").val(treeNode.id); // 区县
      $(".s_id_hide").val(''); // 学校
      $(".a_id_hide").val(''); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      if ($('.tab-pane.active').attr('id') == 'tab1') { // 统考
        dialog.error('请选择年级！');
      } else {
        dialog.error('请选择班级！');
      };
    };
    if(treeNode.typeFlag == 'school') {
      $(".prov_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 省
      $(".city_id_hide").val(treeNode.getParentNode().getParentNode().id); // 市
      $(".county_id_hide").val(treeNode.getParentNode().id); // 区县
      $(".s_id_hide").val(treeNode.id); // 学校
      $(".a_id_hide").val(''); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      if ($('.tab-pane.active').attr('id') == 'tab1') { // 统考
        dialog.error('请选择年级！');
      } else {
        dialog.error('请选择班级！');
      };
    };
    if(treeNode.typeFlag == 'area') {
      $(".prov_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 省
      $(".city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 市
      $(".county_id_hide").val(treeNode.getParentNode().getParentNode().id); // 区县
      $(".s_id_hide").val(treeNode.getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.id); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      if ($('.tab-pane.active').attr('id') == 'tab1') { // 统考
        dialog.error('请选择年级！');
      } else {
        dialog.error('请选择班级！');
      };
    };
    if(treeNode.typeFlag == 'grade') {
      $(".prov_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().id); // 省
      $(".city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 市
      $(".county_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 区县
      $(".s_id_hide").val(treeNode.getParentNode().getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.getParentNode().id); // 校区
      $(".g_id_hide").val(treeNode.id); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      if ($('.tab-pane.active').attr('id') == 'tab1') { // 单考
        fnTabPaneActive();
      } else {
        dialog.error('请选择班级！');
      }
    };
    if(treeNode.typeFlag == 'class') {
      $(".prov_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().id); // 省
      $(".city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().id); // 市
      $(".county_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 区县
      $(".s_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.getParentNode().getParentNode().id); // 校区
      $(".g_id_hide").val(treeNode.getParentNode().id); // 年级
      $(".c_id_hide").val(treeNode.id); // 班级
      $(".stu_id_hide").val(''); // 学生
      fnTabPaneActive();
    };
    if(treeNode.typeFlag == 'student') {
      $(".prov_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().id); // 省
      $(".city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().id); // 市
      $(".county_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().id); // 区县
      $(".s_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 校区
      $(".g_id_hide").val(treeNode.getParentNode().getParentNode().id); // 年级
      $(".c_id_hide").val(treeNode.getParentNode().id); // 班级
      $(".stu_id_hide").val(treeNode.id); // 学生
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
      $(".stu_id_hide").val(''); // 学生
      if ($('.tab-pane.active').attr('id') == 'tab1') { // 统考
        dialog.error('请选择年级！');
      } else {
        dialog.error('请选择班级！');
      };
    };
    if(treeNode.typeFlag == 'county') {
      $(".city_id_hide").val(treeNode.getParentNode().id); // 市
      $(".county_id_hide").val(treeNode.id); // 区县
      $(".s_id_hide").val(''); // 学校
      $(".a_id_hide").val(''); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      if ($('.tab-pane.active').attr('id') == 'tab1') { // 统考
        dialog.error('请选择年级！');
      } else {
        dialog.error('请选择班级！');
      };
    };
    if(treeNode.typeFlag == 'school') {
      $(".city_id_hide").val(treeNode.getParentNode().getParentNode().id); // 市
      $(".county_id_hide").val(treeNode.getParentNode().id); // 区县
      $(".s_id_hide").val(treeNode.id); // 学校
      $(".a_id_hide").val(''); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      if ($('.tab-pane.active').attr('id') == 'tab1') { // 统考
        dialog.error('请选择年级！');
      } else {
        dialog.error('请选择班级！');
      };
    };
    if(treeNode.typeFlag == 'area') {
      $(".city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 市
      $(".county_id_hide").val(treeNode.getParentNode().getParentNode().id); // 区县
      $(".s_id_hide").val(treeNode.getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.id); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      if ($('.tab-pane.active').attr('id') == 'tab1') { // 统考
        dialog.error('请选择年级！');
      } else {
        dialog.error('请选择班级！');
      };
    };
    if(treeNode.typeFlag == 'grade') {
      $(".city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 市
      $(".county_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 区县
      $(".s_id_hide").val(treeNode.getParentNode().getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.getParentNode().id); // 校区
      $(".g_id_hide").val(treeNode.id); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      if ($('.tab-pane.active').attr('id') == 'tab1') { // 单考
        fnTabPaneActive();
      } else {
        dialog.error('请选择班级！');
      }
    };
    if(treeNode.typeFlag == 'class') {
      $(".city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().id); // 市
      $(".county_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 区县
      $(".s_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.getParentNode().getParentNode().id); // 校区
      $(".g_id_hide").val(treeNode.getParentNode().id); // 年级
      $(".c_id_hide").val(treeNode.id); // 班级
      $(".stu_id_hide").val(''); // 学生
      fnTabPaneActive();
    };
    if(treeNode.typeFlag == 'student') {
      $(".city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().id); // 市
      $(".county_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().id); // 区县
      $(".s_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 校区
      $(".g_id_hide").val(treeNode.getParentNode().getParentNode().id); // 年级
      $(".c_id_hide").val(treeNode.getParentNode().id); // 班级
      $(".stu_id_hide").val(treeNode.id); // 学生
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
      $(".stu_id_hide").val(''); // 学生
      if ($('.tab-pane.active').attr('id') == 'tab1') { // 统考
        dialog.error('请选择年级！');
      } else {
        dialog.error('请选择班级！');
      };
    };
    if(treeNode.typeFlag == 'school') {
      $(".county_id_hide").val(treeNode.getParentNode().id); // 区县
      $(".s_id_hide").val(treeNode.id); // 学校
      $(".a_id_hide").val(''); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      if ($('.tab-pane.active').attr('id') == 'tab1') { // 统考
        dialog.error('请选择年级！');
      } else {
        dialog.error('请选择班级！');
      };
    };
    if(treeNode.typeFlag == 'area') {
      $(".county_id_hide").val(treeNode.getParentNode().getParentNode().id); // 区县
      $(".s_id_hide").val(treeNode.getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.id); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      if ($('.tab-pane.active').attr('id') == 'tab1') { // 统考
        dialog.error('请选择年级！');
      } else {
        dialog.error('请选择班级！');
      };
    };
    if(treeNode.typeFlag == 'grade') {
      $(".county_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 区县
      $(".s_id_hide").val(treeNode.getParentNode().getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.getParentNode().id); // 校区
      $(".g_id_hide").val(treeNode.id); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      if ($('.tab-pane.active').attr('id') == 'tab1') { // 单考
        fnTabPaneActive();
      } else {
        dialog.error('请选择班级！');
      }
    };
    if(treeNode.typeFlag == 'class') {
      $(".county_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 区县
      $(".s_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.getParentNode().getParentNode().id); // 校区
      $(".g_id_hide").val(treeNode.getParentNode().id); // 年级
      $(".c_id_hide").val(treeNode.id); // 班级
      $(".stu_id_hide").val(''); // 学生
      fnTabPaneActive();
    };
    if(treeNode.typeFlag == 'student') {
      $(".county_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().id); // 区县
      $(".s_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 校区
      $(".g_id_hide").val(treeNode.getParentNode().getParentNode().id); // 年级
      $(".c_id_hide").val(treeNode.getParentNode().id); // 班级
      $(".stu_id_hide").val(treeNode.id); // 学生
    };
  };
  if (user_type == '3') {
    if(treeNode.typeFlag == 'grade') {
      $(".g_id_hide").val(treeNode.id); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      if ($('.tab-pane.active').attr('id') == 'tab1') { // 单考
        fnTabPaneActive();
      } else {
        dialog.error('请选择班级！');
      }
    };
    if(treeNode.typeFlag == 'class') {
      $(".g_id_hide").val(treeNode.getParentNode().id); // 年级
      $(".c_id_hide").val(treeNode.id); // 班级
      $(".stu_id_hide").val(''); // 学生
      fnTabPaneActive();
    };
    if(treeNode.typeFlag == 'student') {
      $(".g_id_hide").val(treeNode.getParentNode().getParentNode().id); // 年级
      $(".c_id_hide").val(treeNode.getParentNode().id); // 班级
      $(".stu_id_hide").val(treeNode.id); // 学生
      fnTabPaneActive();
    };
  };
  if (user_type == '4') {
    if(treeNode.typeFlag == 'student') {
      $(".stu_id_hide").val(treeNode.id); // 学生
      fnTabPaneActive();
    };
  };
  // 是否选择了年级(统考)
  if ($(".g_id_hide").val() != '' && $(".c_id_hide").val() == '') {
    bIfCheckGrade = true;
  } else {
    bIfCheckGrade = false;
  };
  // 是否选择了班级(单考)
  if ($(".c_id_hide").val() != '' && $(".stu_id_hide").val() == '') {
    bIfCheckClass = true;
  } else {
    bIfCheckClass = false;
  };
}

function fnTabPaneActive() {
  var g_id   = $('#searchForm .g_id_hide').val();
  var c_id   = $('#searchForm .c_id_hide').val();
  var stu_id = $('#searchForm .stu_id_hide').val();
  if ($('.tab-pane.active').attr('id') == 'tab1') { // 统考
    if ( user_type == '4') { // 如果是家长账号登陆的话，文件树只显示学生节点，只能传学生id获取考试名称
      initSelectClass('e_id1',g_config.host+'/index.php/Admin/Parent/Result/get_examAll?stu_id=' + stu_id,0,{type:'course'},true,'请选择',function() {
        fninitSelect();
      }); // 统考名称
      initSelectClass('ex_id2',g_config.host+'/index.php/Admin/Parent/Result/get_exam?stu_id=' + stu_id,0,{type:'course'},true,'请选择',function() {
        fninitSelect();
      }); // 单考名称
    } else {
      // initSelectClass('e_id1',g_config.host+'/index.php/Admin/Parent/Result/get_examAll?g_id=' + g_id,0,{type:'course'},true); // 统考名称
      initSelectClass('e_id1',g_config.host+'/index.php/Admin/Parent/Result/get_examAll?g_id=' + g_id,0,{type:'course'},true,'请选择',function() {
        fninitSelect();
      }); // 统考名称
    };
    // search();
  } else { // 单考
    if ( user_type == '4') { // 如果是家长账号登陆的话，文件树只显示学生节点，只能传学生id获取考试名称
      initSelectClass('e_id1',g_config.host+'/index.php/Admin/Parent/Result/get_examAll?stu_id=' + stu_id,0,{type:'course'},true,'请选择',function() {
        fninitSelect();
      }); // 统考名称
      initSelectClass('ex_id2',g_config.host+'/index.php/Admin/Parent/Result/get_exam?stu_id=' + stu_id,0,{type:'course'},true,'请选择',function() {
        fninitSelect();
      }); // 单考名称
    } else {
      initSelectClass('ex_id2',g_config.host+'/index.php/Admin/Parent/Result/get_exam?g_id=' + g_id + '&c_id=' + c_id,0,{type:'course'},true,'请选择',function() {
        fninitSelect();
      }); // 单考名称
    };
    // search2();
  };
}

// 统考隐藏域赋值
function searchForm() {
  $("#searchForm #e_id_hide").val($("#e_id1").val());
  $("#searchForm #stu_id_hide").val($("#stu_id1").val());
}

// 统考名称改变时 获取统考表格数据
$('#e_id1').change(function() {
  search();
});

// 单考名称改变时 获取单考表格数据
$('#ex_id2').change(function() {
  search2();
});

// 查询
function search() {
  searchForm();
  if ($('#e_id_hide').val() == '') { // 如果统考名称没有被选择
    dialog.error('请选择统考名称！');
  } else {
    $.ajax({
      url: '/index.php/Admin/Parent/Result/query_index?' + $('#searchForm').serialize(),
      type: 'get',
      cache: false,
      dataType: 'json',
      success: function(data) {
        var datas = data.data;
        // 课程
        var aCourse = Object.keys(datas.course);

        // 所有数据
        var aInfo = datas.result;
        
        // 排名
        var iRanking = 0;

        // 提取每一行的课程成绩
        for (var i = 0; i < aInfo.length; i++) {
          // 计算排名
          if (i>0) {
            if (aInfo[i].sum < aInfo[i-1].sum) {
              iRanking++;
            };
          }else {
            iRanking++;
          };
          // 当前行的成绩
          var arr = [];
          for (var t = 0; t < aCourse.length; t++) {
            arr.push(aInfo[i][aCourse[t]]);
          };
          aInfo[i].result = arr;
          aInfo[i].ranking = iRanking;
        };

        var telData = {
          course:aCourse,
          info:aInfo
        };
        var html = template('resultTemplate', telData);
        $('#tab_data').html(html);
        if (user_type == '1' || user_type == '4') {
          $('.js_showHide').remove();
        }
      },
      error: function() {
        dialog.error('请求数据异常！');
      }
    })
  };
}

// 下载模板
$('.template').click(function() {
  window.open('/Public/template/成绩录入模板.xls');
})

// 单考隐藏域赋值
function searchForm2() {
  $("#searchForm2 #ex_id_hide").val($("#ex_id2").val());
  $("#searchForm2 #crs_id_hide").val($("#crs_id2").val());
  $("#searchForm2 #stu_id_hide").val($("#stu_id2").val());
}
// 自定义ajax请求
function ajaxRequest2(params) {
  $g_params = params;
  searchForm2();
  // 需要的数据
  var url = "/index.php/Admin/Parent/Result/query_exam?" + $("#searchForm2").serialize();
  queryList(url,params);
}
// 查询
function search2() {
  // form查询域赋值
  searchForm2();
  var url = "/index.php/Admin/Parent/Result/query_exam?" + $("#searchForm2").serialize();
  $g_params.data.page = parseInt($('.active .pagination li.active a').text());
  queryList(url,$g_params);
}
// 删除
function del2() {
  var url     = g_config.host + "/index.php/Admin/Parent/Result/exam_del";
  var ids     = getIdSelections($('#table_data2'),'exs_id');
  var strIds  = ids.join(',');
  var data    = {};
  data.exs_id = strIds;

  if (ids.length == 0) {
    dialog.notify('请先选择要删除的记录');
    return;
  } else {
    dialog.confirm('您确定要删除当前选项吗？',function(){dpost(url,data,btnCallbackRefreshTable2);});
  }
}

// 统考添加弹出层
function examAll_add() {
  var g_id = $('#searchForm .g_id_hide').val();
  if ($(".g_id_hide").val() != '' && $(".c_id_hide").val() == '' && bIfCheckGrade) {
    layer.open({
      type: 2,
      title: '添加统考成绩',
      shadeClose: true,
      shade: 0.2,
      area: ['60%', '60%'],
      content: "<?php echo U('/Admin/Parent/Result/examAll_add');;?>?g_id=" + g_id,
      cancel: function(index) {
        $.cookie("cId",null,{expires:1});
        $.cookie("eId",null,{expires:1});
        $.cookie("crsId",null,{expires:1});
        layer.close(index)
        return false; 
      }
    });
  } else {
    dialog.error('请选择年级！');
  }
}

// 单考添加弹出层
$('.select_add2').click(function() {
  var s_id = $('#searchForm2 .s_id_hide').val();
  var a_id = $('#searchForm2 .a_id_hide').val();
  var g_id = $('#searchForm2 .g_id_hide').val();
  var c_id = $('#searchForm2 .c_id_hide').val();
  if ($(".c_id_hide").val() != '') {
    layer.open({
      type: 2,
      title: '添加单考成绩',
      shadeClose: true,
      shade: 0.2,
      area: ['60%', '60%'],
      content: "<?php echo U('/Admin/Parent/Result/exam_add');;?>?s_id=" + s_id + '&a_id=' + a_id + '&g_id=' + g_id + '&c_id=' + c_id,
      cancel: function(index) {
        $.cookie("exId",null,{expires:1});
        layer.close(index)
        return false; 
      }
    });
  } else {
    dialog.error('请选择班级！');
  };
});

// 统考编辑弹出层
function edits(stuId) {
  layer.open({
    type: 2,
    title: '编辑统考成绩',
    shadeClose: true,
    shade: 0.2,
    area: ['60%', '60%'],
    content: "<?php echo U('/Admin/Parent/Result/examAll_edit');;?>?stu_id=" + stuId + '&e_id=' + $("#e_id1").val()
  });
}

window.exoperateEvents = {
  // 单考编辑弹出层
  'click .edit': function (e, value, row, index) {
    var key = Object.keys(row)[0]; // 主键第一个值
    console.log(key+':'+row[key]);
    layer.open({
      type: 2,
      title: '编辑单考成绩',
      shadeClose: true,
      shade: 0.2,
      area: ['60%', '60%'],
      content: "<?php echo U('/Admin/Parent/Result/exam_edit');;?>?exs_id=" + row.exs_id
    });
  }
};

// 左侧文件树搜索
$('#searchNodeBtn').click(function() {
  searchNodeAll('treeDemo',$('#searchNodeVal'));
  leftTreeScroll();
});
$('#searchNodeVal').on('input', function() {
  searchNodeAll('treeDemo',$('#searchNodeVal'));
  leftTreeScroll();
});

</script>
</body>
</html>