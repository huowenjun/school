<?php if (!defined('THINK_PATH')) exit();?>
<!-- Main Header -->
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>学生证在线数量</title>
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

<link rel="stylesheet" href="/Public/plugins/echarts/style.css" />
  <!-- Main Side Menu -->
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
        <li class="active">学生证在线数量</li>
      </ol>
    </section>   
    <section class="content">
      <div class="row">
        <div class="col-md-3">
          <div class="box">
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
              <li class="active"><a href="#tab1" data-toggle="tab">在线数统计</a></li>
              <li><a href="#tab2" data-toggle="tab">考勤分析</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab1">
                <nav class="navbar navbar-default">
                  <!-- container-fluid -->
                  <div class="container-fluid">
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="form-inline navbar-form navbar-left">
                      <div class="form-group">
                        <label for="select_view_type">查看类型：</label>
                        <select id="select_view_type" class="form-control"> 
                          <option value="1" selected = "selected">今日</option><option value="2">近七日</option><option value="3">近十五日</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <div class="onLine-num" style="display:none; margin-bottom:5px; margin-left:15px; font-weight: 700;">学生证总数：<strong class="onLineCount" style="margin-right: 30px; font-size:21px;" ></strong></div>
                      </div>
                      
                    </div>
                  </div>
                  <!-- /.container-fluid -->
                </nav>
                <!-- table -->
                <div class="content-wrap">
                  <div class="row">
                    <div class="col-md-12">
                      <div id="onlineNumChart" style=" width:100%; height:500px;"></div>
                      <div id="onlineBox" class="box" style="display:none;">
                          <div class="box-header with-border">
                            <h3 class="box-title">在线人数详情</h3>
                            <div class="box-tools pull-right">
                              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                              </button>
                            </div>
                          </div>
                          <!-- /.box-header -->
                          <div class="box-body">
                            <div id="onlineTable" class="table-responsive">
                              <table class="table no-margin">
                                <tbody>
                                </tbody>
                              </table>
                            </div>
                            <!-- /.table-responsive -->
                          </div>
                          <!-- /.box-body -->
                        </div>
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
                        <label for="defaultDay">考勤时间：</label>
                        <input id="defaultDay" class="form-control defaultDay dataDay" type="text" value="" placeholder="">
                      </div>
                      <div class="form-group">
                        <div class="onLine-num" style="display:none; margin-bottom:5px; margin-left:15px; font-weight: 700;">学生证总数：<strong class="onLineCount" style="margin-right: 30px; font-size:21px;" ></strong></div>
                      </div>
                    </div>
                  </div>
                  <!-- /.container-fluid -->
                </nav>
                <!-- table -->
                <div class="content-wrap">
                  <div class="row">
                    <div class="col-md-12">
                      <div id="attrNumChart" style=" height:500px;"></div>   
                    </div>
                  </div>
                </div>
              </div>
              <!-- /#tab2 -->

            </div>
            <!-- /.tab-content -->
          </div>
          <!-- <div class="box">
            <div class="box-header with-border">
              <div class="form-inline navbar-form navbar-left">
                <div class="form-group">
                  <label for="select_view_type">查看类型：</label>
                  <select id="select_view_type" class="form-control"> 
                    <option value="1" selected = "selected">今日</option><option value="2">近七日</option><option value="3">近十五日</option>
                  </select>
                </div>
                <div class="form-group">
                  <div class="onLine-num" style="display:none; margin-bottom:5px; margin-left:15px; font-weight: 700;">学生证总数：<strong id="onLineCount" style="margin-right: 30px; font-size:21px;" ></strong></div>
                </div>
              </div>
            </div>
            <div class="box-body">
              <div id="main" style=" width:100%; height:500px;"></div>
            </div>
          </div> -->
        </div>
      </div>
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

<!-- 隐藏表单域 -->
<form id="searchForm" name="searchForm" action="" style="position: fixed; top: 0; right: 0; z-index: 99999;">、
<?php if(($user_type == 2)): ?><input type="hidden" class="prov_id_hide id_hide" name="prov_id" value="" placeholder="省/直辖市" />
  <input type="hidden" class="city_id_hide id_hide" name="city_id" value="" placeholder="市" />
  <input type="hidden" class="county_id_hide id_hide" name="county_id" value="" placeholder="区/县" />
  <input type="hidden" class="s_id_hide id_hide" name="s_id" value="" placeholder="学校" />
  <input type="hidden" class="a_id_hide id_hide" name="a_id" value="" placeholder="校区" />
  <input type="hidden" class="g_id_hide id_hide" name="g_id" value="" placeholder="年级" />
  <input type="hidden" class="c_id_hide id_hide" name="c_id" value="" placeholder="班级" />
  <input type="hidden" name="view_type" id="view_type" placeholder="" />
  <input type="hidden" name="date" id="day_value" placeholder="" /><?php endif; ?>
<?php if(($user_type == 1)): ?><input type="hidden" class="s_id_hide id_hide" name="s_id" value="" placeholder="学校" />
  <input type="hidden" class="a_id_hide id_hide" name="a_id" value="" placeholder="校区" />
  <input type="hidden" class="g_id_hide id_hide" name="g_id" value="" placeholder="年级" />
  <input type="hidden" class="c_id_hide id_hide" name="c_id" value="" placeholder="班级" />
  <input type="hidden" name="view_type" id="view_type" placeholder="" /> 
  <input type="hidden" name="date" id="day_value" placeholder="" /><?php endif; ?>

<?php if(($user_type == 5)): ?><input type="hidden" class="prov_id_hide id_hide" name="prov_id" value="" placeholder="省/直辖市" />
  <input type="hidden" class="city_id_hide id_hide" name="city_id" value="" placeholder="市" />
  <input type="hidden" class="county_id_hide id_hide" name="county_id" value="" placeholder="区/县" />
  <input type="hidden" class="s_id_hide id_hide" name="s_id" value="" placeholder="学校" />
  <input type="hidden" class="a_id_hide id_hide" name="a_id" value="" placeholder="校区" />
  <input type="hidden" class="g_id_hide id_hide" name="g_id" value="" placeholder="年级" />
  <input type="hidden" class="c_id_hide id_hide" name="c_id" value="" placeholder="班级" />
  <input type="hidden" name="view_type" id="view_type" placeholder="" /> 
  <input type="hidden" name="date" id="day_value" placeholder="" /><?php endif; ?>
<?php if(($user_type == 6)): ?><input type="hidden" class="city_id_hide id_hide" name="city_id" value="" placeholder="市" />
  <input type="hidden" class="county_id_hide id_hide" name="county_id" value="" placeholder="区/县" />
  <input type="hidden" class="s_id_hide id_hide" name="s_id" value="" placeholder="学校" />
  <input type="hidden" class="a_id_hide id_hide" name="a_id" value="" placeholder="校区" />
  <input type="hidden" class="g_id_hide id_hide" name="g_id" value="" placeholder="年级" />
  <input type="hidden" class="c_id_hide id_hide" name="c_id" value="" placeholder="班级" />
  <input type="hidden" name="view_type" id="view_type" placeholder="" /> 
  <input type="hidden" name="date" id="day_value" placeholder="" /><?php endif; ?>
<?php if(($user_type == 7)): ?><input type="hidden" class="county_id_hide id_hide" name="county_id" value="" placeholder="区/县" />
  <input type="hidden" class="s_id_hide id_hide" name="s_id" value="" placeholder="学校" />
  <input type="hidden" class="a_id_hide id_hide" name="a_id" value="" placeholder="校区" />
  <input type="hidden" class="g_id_hide id_hide" name="g_id" value="" placeholder="年级" />
  <input type="hidden" class="c_id_hide id_hide" name="c_id" value="" placeholder="班级" />
  <input type="hidden" name="view_type" id="view_type" placeholder="" /> 
  <input type="hidden" name="date" id="day_value" placeholder="" /><?php endif; ?>
<?php if(($user_type == 3)): ?><input type="hidden" class="g_id_hide id_hide" name="g_id" value="" placeholder="年级" />
  <input type="hidden" class="c_id_hide id_hide" name="c_id" value="" placeholder="班级" />
  <input type="hidden" name="view_type" id="view_type" placeholder="" /> 
  <input type="hidden" name="date" id="day_value" placeholder="" /><?php endif; ?>
</form>

<!-- REQUIRED JS SCRIPTS -->
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

<script type="text/javascript" src="/Public/plugins/echarts/echarts.js"></script>
<script>

var attrNumChartBox = $("#attrNumChart");
attrNumChartBox.css({'width':$(".container-fluid").width(),"margin":"0 auto"});

// 清空隐藏域
$(function(){
  $('.id_hide').each(function() {
    $(this).val("");
  });
})

// 统计图清空
function chartClear(chart){
  chart.clear();
}

// 考勤数量分析接口调用及数据处理
function stuCardInfo(){
searchForm();
  var url = '/index.php/Admin/StuCard/StuCardLasttime/queryList3?' + $("#searchForm").serialize();
  fget(url,'',function(res){
    if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
    g_loadingIndex = -1;
    if(res.status==1){
      var numArr =[];
      var cardInfo = res.data;
      $.each(cardInfo, function(key, value) {
        numArr.push(value.value);
      })
      stuCardInfoChart(cardInfo);     
    }else{
      dialog.error(res.info);
    }
  })
}

// 考勤数量分析(饼状图)
var timer=null;
var attrNumChart = echarts.init(document.getElementById('attrNumChart'));
function stuCardInfoChart(data){
  clearInterval(timer);
  var app ={};
  option = {
    color: ['#84df56','#cc5664','#E1955E'],
    title : {
      text: '考勤数量分析',
      x:'center',
      textStyle: {
        color: '#38AEFF',
        fontSize: "25"
      },
      top: '10%',
    },
    legend: {
      orient: 'horizontal',
      x : 'center',
      top: '20%',
      data:[data[0].name,data[1].name,data[2].name],
      textStyle: {
        color:'#08082a'
      },
    },
    startAngle: [90,270],
    tooltip: {
      trigger: 'item',
      formatter: "{b} : {c} ({d}%)"
    },
    series: [
      {
        type: 'pie',
        radius: ['20%','40%'],
        center: ['50%','55%'],
        tooltip: {
           position: ['60%','63%'],
        },
        label: {
          normal: {
            position: 'outside',
            formatter: "{c}",
            color: "#08082a",
            fontSize:'15',
          }
        },
        data: data,
        itemStyle: {
          emphasis: {
            shadowBlur: 10,
            shadowOffsetX: 0,
            shadowColor: 'rgba(0, 0, 0, 0.5)'
          }
        }
      }
    ]
  };
  app.currentIndex = -1; 
    timer = setInterval(function () {
    var dataLen = option.series[0].data.length;
    // 取消之前高亮的图形
    attrNumChart.dispatchAction({
      type: 'downplay',
      seriesIndex: 0,
      dataIndex: app.currentIndex
    });
    app.currentIndex = (app.currentIndex + 1) % dataLen;
    // 高亮当前图形
    attrNumChart.dispatchAction({
      type: 'highlight',
      seriesIndex: 0,
      dataIndex: app.currentIndex
    });
    // 显示 tooltip
    attrNumChart.dispatchAction({
      type: 'showTip',
      seriesIndex: 0,
      dataIndex: app.currentIndex
    });
  }, 3000);

  chartClear(attrNumChart);
  attrNumChart.setOption(option); 
}

// 在线数量及在线率接口调用
var onLineCount = $(".onLineCount");
function search(){
  onlinebox.css('display', 'none');
  searchForm();
  $(".onLine-num").css('display', 'block');
  var url = '/index.php/Admin/StuCard/StuCardLasttime/queryList2?' + $("#searchForm").serialize();
  $.ajax({
    url: url,
    async: true,
    type: 'GET',
    dataType: 'json',
    beforeSend: function (){
      showLoad();
    },
    complete: function (){
      hideLoad();
    },
    success: function(res) {
      var countCounts = res.data.count;
      var ratioCounts = res.data.ratio;
      var totalCounts = res.data.total;
      var onlineCounts = res.data.online;
      onLineCount.html(totalCounts);// 在线总数
      var onlineCountsArr=[countCounts,ratioCounts,onlineCounts];  
      onlineCountsDP(onlineCountsArr);  
    }
  });
}
function showLoad() {
  onLineCount.html('<img class="load" src="/Public/dist/img/load/load.gif">');
}
function hideLoad() {
  $(".load").css('display', 'none');
}

// 在线数量及在线率数据处理
function onlineCountsDP(datas){
  var onlineDetail = [];
  for (var i = 0; i < datas.length-1; i++) {  
  var onlineDetailKey = [];
  var onlineDetailValue = [];
    for(var key in datas[i]){
      onlineDetailKey.push(key);
      onlineDetailValue.push(datas[i][key]);
    }
   onlineDetail.push(onlineDetailKey,onlineDetailValue)
  };

  var onlineCounts = datas[datas.length-1];
  onlineNumChart(onlineDetail);
  onlineDetailClickDP(onlineCounts)
}

// 在线数量及在线率折线图
var onlineNum = echarts.init(document.getElementById('onlineNumChart'));
function onlineNumChart(datas){
  option ={
    color: ['#8DE160','#d71b3c'],
    tooltip : {
      trigger: 'axis',
      formatter: "{b}<br/>{a0}{c0}<br/>{a1}{c1} %"
    },
    legend: {
      data:['在线数','在线率'],
      right:'5%',
      textStyle: {
        color:'#08082a'
      }
    },
    calculable : false,
    xAxis : [
      {
        type : 'category',
        axisTick: {show: false},
        name : '时段',
        nameTextStyle: {
          color : '#08082a'
        },

        axisLine: {
          lineStyle:{
            color:'#0e5370',
            width:'3'
          }
        },
        axisLabel:{
          interval:0,
          fontSize:'12',
          color:'#08082a'
        },
        splitLine: {
          show: false,
        },
        data : datas[0]
      }
    ],
    yAxis : {
      type : 'value',
      axisTick: {show: false},
      axisLine: {
        lineStyle:{
          color:'#0e5370',
          width:'3'
        }
      },
      axisLabel:{
        fontSize:'15',
        color:'#08082a'
      },
      splitLine: {
        show: true,
        lineStyle: {
          color: ['#0e5370'],
          type: 'dotted'    
        }
      },minInterval: 1,
       splitNumber:3
    },
    series : [
      {
        name:'在线数',
        type:'line',
        smooth:true,  
        data:  datas[1]
      },
      {
        name:'在线率',
        type:'line',
        smooth:true,
        data: datas[3]
      }
    ],
  }
  chartClear(onlineNum);
  onlineNum.setOption(option);
}

// 在线详情点击数据处理
var onlinebox  = $("#onlineBox");
function onlineDetailClickDP(data){
  onlineNum.on('click', function (params) { // 点击折线图
    onlinebox.css('display', 'block');
    for(var key in data){
      if(params.name == key){
        onlineShow(data[key]);
      } 
    }
  })
}

// 循环遍历在线数据
var listHtml;
var onlinetable = $("#onlineTable tbody");
function onlineShow(data){
  listHtml='';
  if(data.length!=0){
    for (var i = 0; i < data.length; i++) {
      listHtml+='<tr><td>'+data[i]+'</td></tr>';
    }
    onlinetable.html(listHtml);
  }else{
    listHtml='<tr><td>暂无数据</td></tr>';
    onlinetable.html(listHtml);
  } 
}

//切换查看类型
$("#select_view_type").change(function() {
  $('option:selected', this).each(function() {
    if($('.id_hide').val()!=""){
      $("#view_type").val(this.value);
      search();
    }else{
      dialog.error("请选择学校、校区或年级！");
    }
  });
});  

//隐藏域赋值
function searchForm() {
  $("#view_type").val($("#select_view_type").val());
  $("#day_value").val($(".defaultDay").val());
}

// 左侧文件树搜索
$('#searchNodeBtn').click(function() {
  searchNodeAll('treeDemo',$('#searchNodeVal'));
});
$('#searchNodeVal').on('input', function() {
  searchNodeAll('treeDemo',$('#searchNodeVal'));
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
    beforeExpand: beforeExpand,
    onCollapse: onCollapse,
    onExpand: onExpand,
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
  leftTreeScroll();
  curExpandNode = treeNode;
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
var user_type = "<?php echo ($user_type); ?>"; // 用户类型 1学校管理员 2系统用户 3老师 4家长 5省/直辖市教委 6 市教委 7 区/县教委  
function zTreeOnClick(event, treeId, treeNode) {
  var treeObj = $.fn.zTree.getZTreeObj("treeDemo");
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
      search();
      stuCardInfo();
    };
    if(treeNode.typeFlag == 'area') {
      $(".s_id_hide").val(treeNode.getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.id); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      search();
      stuCardInfo();
    };
    if(treeNode.typeFlag == 'grade') {
      $(".s_id_hide").val(treeNode.getParentNode().getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.getParentNode().id); // 校区
      $(".g_id_hide").val(treeNode.id); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      search();
      stuCardInfo();
    };
    if(treeNode.typeFlag == 'class') {
      $(".s_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.getParentNode().getParentNode().id); // 校区
      $(".g_id_hide").val(treeNode.getParentNode().id); // 年级
      $(".c_id_hide").val(treeNode.id); // 班级
      $(".stu_id_hide").val(''); // 学生
      search();
      stuCardInfo();
    };
  };
  if (user_type == '5') {
    // 点击选择
    if(treeNode.typeFlag == 'prov') {
      $(".prov_id_hide").val(treeNode.id); // 省/直辖市
      $(".city_id_hide").val(''); // 市
      $(".county_id_hide").val(''); // 区/县
      $(".s_id_hide").val(''); // 学校
      $(".a_id_hide").val(''); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
    };
    if(treeNode.typeFlag == 'city') {
      $(".prov_id_hide").val(treeNode.getParentNode().id); // 省/直辖市
      $(".city_id_hide").val(treeNode.id); // 市
      $(".county_id_hide").val(''); // 区/县
      $(".s_id_hide").val(''); // 学校
      $(".a_id_hide").val(''); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
    };
    if(treeNode.typeFlag == 'county') {
      $(".prov_id_hide").val(treeNode.getParentNode().getParentNode().id); // 省/直辖市
      $(".city_id_hide").val(treeNode.getParentNode().id); // 市
      $(".county_id_hide").val(treeNode.id); // 区/县
      $(".s_id_hide").val(''); // 学校
      $(".a_id_hide").val(''); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
    };
    if(treeNode.typeFlag == 'school') {
      $(".prov_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 省/直辖市
      $(".city_id_hide").val(treeNode.getParentNode().getParentNode().id); // 市
      $(".county_id_hide").val(treeNode.getParentNode().id); // 区/县
      $(".s_id_hide").val(treeNode.id); // 学校
      $(".a_id_hide").val(''); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      search();
      stuCardInfo();
    };
    if(treeNode.typeFlag == 'area') {
      $(".prov_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 省/直辖市
      $(".city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 市
      $(".county_id_hide").val(treeNode.getParentNode().getParentNode().id); // 区/县
      $(".s_id_hide").val(treeNode.getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.id); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      search();
      stuCardInfo();
    };
    if(treeNode.typeFlag == 'grade') {
      $(".prov_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().id); // 省/直辖市
      $(".city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 市
      $(".county_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 区/县
      $(".s_id_hide").val(treeNode.getParentNode().getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.getParentNode().id); // 校区
      $(".g_id_hide").val(treeNode.id); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      search();
      stuCardInfo();
    };
    if(treeNode.typeFlag == 'class') {
      $(".s_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.getParentNode().getParentNode().id); // 校区
      $(".g_id_hide").val(treeNode.getParentNode().id); // 年级
      $(".c_id_hide").val(treeNode.id); // 班级
      $(".stu_id_hide").val(''); // 学生
      search();
      stuCardInfo();
    };
  };
  if (user_type == '6') {
    // 点击选择
    if(treeNode.typeFlag == 'city') {
      $(".city_id_hide").val(treeNode.id); // 市
      $(".county_id_hide").val(''); // 区/县
      $(".s_id_hide").val(''); // 学校
      $(".a_id_hide").val(''); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
    };
    if(treeNode.typeFlag == 'county') {
      $(".city_id_hide").val(treeNode.getParentNode().id); // 市
      $(".county_id_hide").val(treeNode.id); // 区/县
      $(".s_id_hide").val(''); // 学校
      $(".a_id_hide").val(''); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
    };
    if(treeNode.typeFlag == 'school') {
      $(".city_id_hide").val(treeNode.getParentNode().getParentNode().id); // 市
      $(".county_id_hide").val(treeNode.getParentNode().id); // 区/县
      $(".s_id_hide").val(treeNode.id); // 学校
      $(".a_id_hide").val(''); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      search();
      stuCardInfo();
    };
    if(treeNode.typeFlag == 'area') {
      $(".city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 市
      $(".county_id_hide").val(treeNode.getParentNode().getParentNode().id); // 区/县
      $(".s_id_hide").val(treeNode.getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.id); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      search();
      stuCardInfo();
    };
    if(treeNode.typeFlag == 'grade') {
      $(".city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 市
      $(".county_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 区/县
      $(".s_id_hide").val(treeNode.getParentNode().getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.getParentNode().id); // 校区
      $(".g_id_hide").val(treeNode.id); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      search();
      stuCardInfo();
    };
    if(treeNode.typeFlag == 'class') {
      $(".s_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.getParentNode().getParentNode().id); // 校区
      $(".g_id_hide").val(treeNode.getParentNode().id); // 年级
      $(".c_id_hide").val(treeNode.id); // 班级
      $(".stu_id_hide").val(''); // 学生
      search();
      stuCardInfo();
    };
  };
  if (user_type == '7') {
    // 点击选择
    if(treeNode.typeFlag == 'county') {
      $(".county_id_hide").val(treeNode.id); // 区/县
      $(".s_id_hide").val(''); // 学校
      $(".a_id_hide").val(''); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
    };
    if(treeNode.typeFlag == 'school') {
      $(".county_id_hide").val(treeNode.getParentNode().id); // 区/县
      $(".s_id_hide").val(treeNode.id); // 学校
      $(".a_id_hide").val(''); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      search();
      stuCardInfo();
    };
    if(treeNode.typeFlag == 'area') {
      $(".county_id_hide").val(treeNode.getParentNode().getParentNode().id); // 区/县
      $(".s_id_hide").val(treeNode.getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.id); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      search();
      stuCardInfo();
    };
    if(treeNode.typeFlag == 'grade') {
      $(".county_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 区/县
      $(".s_id_hide").val(treeNode.getParentNode().getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.getParentNode().id); // 校区
      $(".g_id_hide").val(treeNode.id); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      search();
      stuCardInfo();
    };
    if(treeNode.typeFlag == 'class') {
      $(".s_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.getParentNode().getParentNode().id); // 校区
      $(".g_id_hide").val(treeNode.getParentNode().id); // 年级
      $(".c_id_hide").val(treeNode.id); // 班级
      $(".stu_id_hide").val(''); // 学生
      search();
      stuCardInfo();
    };
  };
  if (user_type == '2') {
    // 点击选择
    if(treeNode.typeFlag == 'prov') {
      $(".prov_id_hide").val(treeNode.id); // 省/直辖市
      $(".city_id_hide").val(''); // 市
      $(".county_id_hide").val(''); // 区/县
      $(".s_id_hide").val(''); // 学校
      $(".a_id_hide").val(''); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
    };
    if(treeNode.typeFlag == 'city') {
      $(".prov_id_hide").val(treeNode.getParentNode().id); // 省/直辖市
      $(".city_id_hide").val(treeNode.id); // 市
      $(".county_id_hide").val(''); // 区/县
      $(".s_id_hide").val(''); // 学校
      $(".a_id_hide").val(''); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
    };
    if(treeNode.typeFlag == 'county') {
      $(".prov_id_hide").val(treeNode.getParentNode().getParentNode().id); // 省/直辖市
      $(".city_id_hide").val(treeNode.getParentNode().id); // 市
      $(".county_id_hide").val(treeNode.id); // 区/县
      $(".s_id_hide").val(''); // 学校
      $(".a_id_hide").val(''); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
    };
    if(treeNode.typeFlag == 'school') {
      $(".prov_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 省/直辖市
      $(".city_id_hide").val(treeNode.getParentNode().getParentNode().id); // 市
      $(".county_id_hide").val(treeNode.getParentNode().id); // 区/县
      $(".s_id_hide").val(treeNode.id); // 学校
      $(".a_id_hide").val(''); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      search();
      stuCardInfo();
    };
    if(treeNode.typeFlag == 'area') {
      $(".prov_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 省/直辖市
      $(".city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 市
      $(".county_id_hide").val(treeNode.getParentNode().getParentNode().id); // 区/县
      $(".s_id_hide").val(treeNode.getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.id); // 校区
      $(".g_id_hide").val(''); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      search();
      stuCardInfo();
    };
    if(treeNode.typeFlag == 'grade') {
      $(".prov_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().id); // 省/直辖市
      $(".city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 市
      $(".county_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 区/县
      $(".s_id_hide").val(treeNode.getParentNode().getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.getParentNode().id); // 校区
      $(".g_id_hide").val(treeNode.id); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      search();
      stuCardInfo();
    };
    if(treeNode.typeFlag == 'class') {
      $(".s_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 学校
      $(".a_id_hide").val(treeNode.getParentNode().getParentNode().id); // 校区
      $(".g_id_hide").val(treeNode.getParentNode().id); // 年级
      $(".c_id_hide").val(treeNode.id); // 班级
      $(".stu_id_hide").val(''); // 学生
      search();
      stuCardInfo();
    };
    
  };
  if (user_type == '3') {
    // 点击选择
    if(treeNode.typeFlag == 'grade') {
      $(".g_id_hide").val(treeNode.id); // 年级
      $(".c_id_hide").val(''); // 班级
      $(".stu_id_hide").val(''); // 学生
      search();
      stuCardInfo();
    };
    if(treeNode.typeFlag == 'class') {
      $(".g_id_hide").val(treeNode.getParentNode().id); // 年级
      $(".c_id_hide").val(treeNode.id); // 班级
      $(".stu_id_hide").val(''); // 学生
      search();
      stuCardInfo();
    };
  }
}

// 日期
$.datetimepicker.setLocale('ch'); 
$('.dataDay').datetimepicker({
  format:"Y-m-d",      // 格式化日期
  timepicker:false,    // 关闭时间选项
  yearStart:2000,      // 设置最小年份
  yearEnd:2050,        // 设置最大年份
  todayButton:true    // 关闭选择今天按钮
}).on('change',function(ev){
  if($('.id_hide').val()!=""){
    stuCardInfo();
  }else{
    dialog.error("请选择学校、校区或年级！");
  }
})

//------------ 查询条件 获取初始日期 -----------//
function fnGetDay(curTime) {
  var date = new Date(curTime);
  var year = date.getFullYear();
  var month = date.getMonth() + 1;
  var day = date.getDate();
  var hour = date.getHours(); //获取系统时，
  var minute = date.getMinutes(); //分
  month = month>9?month:'0'+month;
  day = day>9?day:'0'+day;
  return {
    'date':year + '-' + month + '-' + day,
  }
}

var defaultDay = fnGetDay(new Date().getTime());
$('.defaultDay').val(defaultDay.date);

$('#searchNodeBtn').click(function() {
// 左侧文件树搜索
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