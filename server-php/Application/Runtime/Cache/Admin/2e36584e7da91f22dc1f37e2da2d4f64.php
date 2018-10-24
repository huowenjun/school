<?php if (!defined('THINK_PATH')) exit();?>
  <!-- Main Header -->
  <!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>学校管理</title>
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
        <li class="active">学校管理</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Your Page Content Here -->
      <div class="row">
        <div class="col-md-12">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#tab1" data-toggle="tab">学校管理</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="tab1">
                <nav class="navbar navbar-default">
                  <!-- container-fluid -->
                  <div class="container-fluid">
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="form-inline navbar-form navbar-left">
                      <div class="form-group">
                        <label for="keyword1">学校名称：</label>
                        <input id="keyword1" type="text" class="form-control input-sm" placeholder="请输入学校名称" />
                      </div>
                      <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-sm query">
                          <i class="fa fa-search"></i> 查询
                        </button>
                      </div>
                      <div class="form-group">
                        <button type="button" class="btn btn-success btn-sm add">
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
                      <div id="toolbar">
                        <div id="remove" class="btn btn-danger btn-sm" style="margin-top:12px">
                          <i class="fa fa-trash"></i> 删除
                        </div>
                      </div>
                      <table id="table_data" class="js-custom-table-data">
                        <thead>
                          <tr>
                            <th data-field="state" data-checkbox="true"></th>
                            <th data-align="center" data-formatter="operateFormatter" data-events="operateEvents" name="operateFormatter">操作</th>
                            <th data-align="center" data-sortable="true" data-field="region_id" name="">地区</th>
                            <th data-align="center" data-sortable="true" data-field="name" name="">学校名称</th>
                            <th data-align="center" data-sortable="true" data-field="build_time" name="">成立时间</th>
                            <th data-align="center" data-sortable="false" data-field="main_zrr" name="">主责任人</th>
                            <th data-align="center" data-sortable="true" data-field="main_phone" name="">主责任人电话</th>
                            <th data-align="center" data-sortable="true" data-field="sub_zzr" name="">副责任人</th>
                            <th data-align="center" data-sortable="true" data-field="sub_phone" name="">副责任人电话</th>
                            <th data-align="center" data-sortable="true" data-field="tel" name="">学校电话内线</th>
                            <th data-align="center" data-sortable="true" data-field="address" name="">学校地址</th>
                            <th data-align="center" data-sortable="true" data-field="valid" name="">有效状态</th>
                            <th data-align="center" data-sortable="true" data-field="create_time" name="">创建时间</th>
                            <th data-align="center" data-sortable="true" data-field="memo" name="">备注</th>
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
<div class="shade" style="" id="modalWindow">
  <div class="modal-body">
    <div class="container-fluid">
      <form class="form-horizontal" action="" method="post" onsubmit="return false" id="formWindow">
        <input class="primaryKey" type="hidden" name="s_id" id="s_id" />
        <input class="primaryKey" type="hidden" name="u_id" id="u_id" />
        <div class="row">
          <div class="col-md-12">
            <div class="clearfix">
              <p class="note pull-right">注：<span class="xing">*</span>标记为必填项</p>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label class="col-md-3 control-label" for="county_id"><span class="xing">*</span> 地区：</label>
                  <div class="col-md-5">
                    <input id="regionname" type="text" class="form-control" readonly value="" style="width:100%;" placeholder="请选择区域" name="regionname" />
                    <input id="county_id" name="county_id" type="hidden" class="form-control" value="" />
                  </div>
                  <div class="col-md-4"><div class="btn btn-primary" onclick="showMenuArea();">选择区域</div></div>
                </div>
                <div class="form-group">
                  <label class="col-md-3 control-label" for="name"><span class="xing">*</span> 学校名称：</label>
                  <div class="col-md-8">
                    <input type="text" placeholder="学校名称" id="name" name="name" class="form-control" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-3 control-label" for="build_time"><span class="xing">*</span> 成立时间：</label>
                  <div class="col-md-8">
                    <input type="text" id="build_time" name="build_time" class="form-control dataTime" placeholder="成立时间" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-3 control-label" for="main_zrr"><span class="xing">*</span> 主责任人：</label>
                  <div class="col-md-8">
                    <input type="text" placeholder="主责任人" id="main_zrr" name="main_zrr" class="form-control" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-3 control-label" for="sub_zzr"><span class="xing">*</span> 副责任人：</label>
                  <div class="col-md-8">
                    <input type="text" placeholder="副责任人" id="sub_zzr" name="sub_zzr" class="form-control" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-3 control-label" for="memo"><span class="xing"></span> 备注：</label>
                  <div class="col-md-8">
                    <textarea class="form-control" id="memo" name="memo" placeholder="最多输入200字！" maxlength="200"></textarea>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label class="col-md-3 control-label" for="valid"><span class="xing">*</span> 有效状态：</label>
                  <div class="col-md-8">
                    <select id="valid" name="valid" class="form-control">
                      <option value="0">有效</option>
                      <option value="1">无效</option>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-3 control-label" for="tel"><span class="xing">*</span> 学校电话内线：</label>
                  <div class="col-md-8">
                    <input type="text" placeholder="格式：0427-43275670 或 010-23237788" id="tel" name="tel" class="form-control" />
                  </div>
                </div>
                <div class="form-group">
                  <label class="col-md-3 control-label" for="address"><span class="xing">*</span> 学校地址：</label>
                  <div class="col-md-8">
                    <input type="text" placeholder="学校地址" id="address" name="address" class="form-control" />
                  </div>
                </div>
				        <div class="form-group">
                  <label class="col-md-3 control-label" for="main_phone"><span class="xing">*</span> 主责任人电话：</label>
                  <div class="col-md-8">
                    <input type="text" placeholder="主责任人电话" id="main_phone" name="main_phone" class="form-control" />
                  </div>
                </div>
				        <div class="form-group">
                  <label class="col-md-3 control-label" for="sub_phone"><span class="xing">*</span> 副责任人电话：</label>
                  <div class="col-md-8">
                    <input type="text" placeholder="副责任人电话" id="sub_phone" name="sub_phone" class="form-control" />
                  </div>
                </div>
				        <div class="form-group">
                  <label class="col-md-3 control-label" for="user_name"><span class="xing">*</span> 用户名：</label>
                  <div class="col-md-8">
                    <input type="text" placeholder="登陆用户名" id="user_name" name="user_name" class="form-control" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>

 <!-- 行政区域 文件树 -->
  <div id="menuContentArea" class="menuContent" style="display:none; position: absolute;">
    <ul id="treeArea" class="ztree" style="margin-top:0; background:#fff; border:1px solid #ccc;"></ul>
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
<form id="searchForm" name="searchForm" action="">
  <input type="hidden" id="keyword" name="keyword" value="" />
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


<script type="text/javascript">
$(function() {

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

  // 日期
  $.datetimepicker.setLocale('ch'); // 设置中文
  $('.dataTime').datetimepicker({
    // lang:"ch",           // 语言选择中文
    format:"Y-m-d",      // 格式化日期
    timepicker:false,    // 关闭时间选项
    yearStart:1900,      // 设置最小年份
    yearEnd:2050,        // 设置最大年份
    todayButton:true     // 关闭选择今天按钮
  });
})

// 隐藏域赋值
function searchForm() {
  $("#keyword").val($("#keyword1").val());
}

// 自定义ajax请求
function ajaxRequest(params) {
  $g_params = params;
  searchForm();
  // 需要的数据
  var url =  "/index.php/Admin/School/SchManage/query?" + $("#searchForm").serialize();
  queryList(url,params);
}

// 查询
function search() {
  // form查询域赋值
  searchForm();
  var url = "/index.php/Admin/School/SchManage/query?" + $("#searchForm").serialize();
  $g_params.data.page = parseInt($('.active .pagination li.active a').text());
  queryList(url,$g_params);
}

//新增/编辑
function edit(id) {
  $("#user_name").removeAttr("disabled"); 
  
  var url = g_config.host + "/index.php/Admin/School/SchManage/get";
  custom_edit(id,url,{s_id:id},'formWindow','modalWindow');
}

//保存数据
function save() {
  var url = g_config.host + '/index.php/Admin/School/SchManage/edit';
  fpost(url,'formWindow',btnCallbackRefreshTable1);
}

//编辑状态特殊字段处理
function setEditDataEx(data){
  $("#user_name").attr('disabled', 'true');
}
//删除
function del() {
  var url    = g_config.host + "/index.php/Admin/School/SchManage/del";
  var ids    = getIdSelections($('.js-custom-table-data'),'s_id');
  var strIds = ids.join(',');
  var data   = {};
  data.s_id    = strIds;

  if (ids.length == 0) {
    dialog.notify('请先选择要删除的记录');
    return;
  } else {
    dialog.confirm('您确定要删除当前选项吗？',function(){dpost(url,data,btnCallbackRefreshTable1);});
  }
}

// 行政区域 文件树
var settingArea = {
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
    onClick: onClickArea,
  }
};
// function beforeClick(treeId, treeNode) {
//   var check = (treeNode && !treeNode.isParent);
//   if (!check) alert("只能选择城市...");
//   return check;
// }
function onClickArea(e, treeId, treeNode) {
  if(treeNode) {
    $("#regionname").val(treeNode.name);
    $("#county_id").val(treeNode.id);
  }
  $("#menuContentArea").hide();
}
function showMenuArea() {
  var cityObj = $("#regionname");
  var cityOffset = $("#regionname").offset();
  // 弹出层定位
  var parBoxOffset = $('.layui-layer').position();
  $("#menuContentArea").css({left:cityOffset.left - parBoxOffset.left + "px", top:cityOffset.top - parBoxOffset.top + "px", width:cityObj.outerWidth() + "px"}).slideDown("fast");
  $("body").bind("mousedown", onBodyDownArea);
}
function hideMenuArea() {
  $("#menuContentArea").fadeOut("fast");
  $("body").unbind("mousedown", onBodyDownArea);
}
function onBodyDownArea(event) {
  if (!(event.target.id == "menuBtn" || event.target.id == "menuContentArea" || $(event.target).parents("#menuContentArea").length>0)) {
    hideMenuArea();
  }
}
$(document).ready(function() {
  $.fn.zTree.init($("#treeArea"), settingArea);
});

// 获取数据(文件树)
$(function() {
  $.ajax({
    type: "get",
    url: "/index.php/Admin/Common/get_list?type=region",
    dataType : 'json', 
    success: function(msg) {
      var zNodes=msg.data;
      console.log(zNodes)
      $.fn.zTree.init($("#treeArea"), settingArea, zNodes);
    },
    error: function(msg) {
      alert( "error: " + msg );
    }
  })
})
// 行政区域 文件树 end


</script>

</body>
</html>