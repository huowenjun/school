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
<!-- 地图 -->
<!-- <script type="text/javascript" src="http://api.map.baidu.com/getscript?v=2.0&ak=pObgXUnySvcX39VAqroiOt6xqUrNoOzK"></script> -->
<script type="text/javascript" src="http://api.map.baidu.com/library/Heatmap/2.0/src/Heatmap_min.js"></script>
<link rel="stylesheet" href="/Public/plugins/swiper/swiper-3.4.2.min.css" />
<link rel="stylesheet" href="/Public/plugins/swiper/animate.min.css" />
<link rel="stylesheet" href="/Public/plugins/echarts/style.css" />

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
    <div class="swiper-container">
      <div class="swiper-wrapper">
        <div class="swiper-slide">
          <div class="page page-one" style="margin-top:0;">
           <div style="width:40px; height:40px;"  id="changeModel1"></div> 
            <div class="page-left page-one-left">
              <div class="con">
                <div class="num-box">
                  <h3>学生卡发卡总数<span>单位(张)</span></h3>
                  <p></p>
                </div>
                <div id="chart1"></div>
              </div>
            </div>
            <div class="page-right page-one-right">
              <div class="con" >
                <div class="num-box">
                  <h3>各省发卡数量统计<span>单位(张)</span></h3>
                </div>
                <div id="chart2" class="areaChart"></div>
                <div id="chart3" class="areaChart"></div>
                <div id="chart4" class="areaChart"></div>
              </div>
            </div>
          </div>
        </div>
        <!-- <div class="swiper-slide">
          <div class="page">
            <div class="con" >
              <div class="num-box">
                <h3>当前学生卡正在定位</h3>
                <div id="monitorMap"></div>
              </div>
            </div>
          </div>
        </div> -->
        <div class="swiper-slide">
          <div class="page page-two">
            <div class="page-left page-two-left app-page-left">
              <div class="con">
                <div class="num-box">
                  <h3>APP老师用户数量<span>单位(位)</span></h3>
                  <p></p>
                </div>
                <div class="num-box">
                  <h3>APP老师在线数量<span>单位(位)</span></h3>
                  <p></p>
                </div> 
                <div class="num-box">
                  <h3>APP家长用户数量<span>单位(位)</span></h3>
                  <p></p>
                </div> 
                <div class="num-box">
                  <h3>APP家长在线数量<span>单位(位)</span></h3>
                  <p></p>
                </div>  
              </div>
            </div>
            <div class="page-right page-two-right">
              <div class="con">
                <div class="num-box">
                  <h3>当前学生卡正在进校数量<span>单位(张)</span></h3>
                </div>
                <div id="chart6" class="lineChart"></div>
              </div>
              <div class="con">
                <div class="num-box">
                  <h3>当前学生卡正在出校数量<span>单位(张)</span></h3>
                </div>
                <div id="chart7" class="lineChart"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="swiper-slide">
          <div class="page page-three">
            <div class="page-left page-three-left app-page-left">
              <div class="con" >
                <div class="num-box">
                  <h3>APP老师用户数量<span>单位(位)</span></h3>
                  <p></p>
                </div>
                <div class="num-box">
                  <h3>APP老师在线数量<span>单位(位)</span></h3>
                  <p></p>
                </div> 
                <div class="num-box">
                  <h3>APP家长用户数量<span>单位(位)</span></h3>
                  <p></p>
                </div> 
                <div class="num-box">
                  <h3>APP家长在线数量<span>单位(位)</span></h3>
                  <p></p>
                </div>  
              </div>
            </div>
            <div class="page-right page-three-right">
              <div class="con">
                <div class="num-box">
                  <h3>老师端APP使用次数<span>单位(次)</span></h3>
                </div>
                <div id="chart8" class="lineChart"></div>
              </div>
              <div class="con">
                <div class="num-box">
                  <h3>家长端APP使用次数<span>单位(次)</span></h3>
                </div>
                <div id="chart9" class="lineChart"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="swiper-slide">
          <div class="page page-four">
            <div class="page-left page-four-left app-page-left">
              <div class="con">
                <div class="num-box">
                  <h3>APP老师用户数量<span>单位(位)</span></h3>
                  <p></p>
                </div>
                <div class="num-box">
                  <h3>APP老师在线数量<span>单位(位)</span></h3>
                  <p></p>
                </div> 
                <div class="num-box">
                  <h3>APP家长用户数量<span>单位(位)</span></h3>
                  <p></p>
                </div> 
                <div class="num-box">
                  <h3>APP家长在线数量<span>单位(位)</span></h3>
                  <p></p>
                </div>  
              </div>
            </div>
            <div class="page-right page-four-right">
              <div class="con">
                <div class="num-box">
                  <h3>老师端使用部分功能次数<span>单位(次)</span></h3>
                </div>
                <div id="chart10"  class="lineChart"></div>
              </div>
              <div class="con">
                <div class="num-box">
                  <h3>家长端使用部分功能次数<span>单位(次)</span></h3>
                </div>
                <div id="chart11" class="lineChart"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="swiper-slide">
          <div class="page page-five">
            <div class="page-left page-five-left app-page-left">
              <div class="con">
                <div class="num-box">
                  <h3>APP老师用户数量<span>单位(位)</span></h3>
                  <p></p>
                </div>
                <div class="num-box">
                  <h3>APP老师在线数量<span>单位(位)</span></h3>
                  <p></p>
                </div> 
                <div class="num-box">
                  <h3>APP家长用户数量<span>单位(位)</span></h3>
                  <p></p>
                </div> 
                <div class="num-box">
                  <h3>APP家长在线数量<span>单位(位)</span></h3>
                  <p></p>
                </div>  
              </div>
            </div>
            <div class="page-right page-five-right ">
              <div class="con">
                <div class="num-box">
                  <h3>老师和家长沟通次数<span>单位(次)</span></h3>
                </div>
                <div id="chart12" class="lineChart"></div>
              </div>
              <div class="con">
                <div class="num-box">
                  <h3>教育/电商第三方广告点击数量<span>单位(次)</span></h3>
                </div>
                <div id="chart13" class="lineChart"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="swiper-slide">
          <div class="page page-six">
            <div class="page-left page-six-left app-page-left">
              <div class="con">
                <div class="num-box">
                  <h3>APP老师用户数量<span>单位(位)</span></h3>
                  <p></p>
                </div>
                <div class="num-box">
                  <h3>APP老师在线数量<span>单位(位)</span></h3>
                  <p></p>
                </div> 
                <div class="num-box">
                  <h3>APP家长用户数量<span>单位(位)</span></h3>
                  <p></p>
                </div> 
                <div class="num-box">
                  <h3>APP家长在线数量<span>单位(位)</span></h3>
                  <p></p>
                </div>  
              </div>
            </div>
            <div class="page-right page-six-right">
              <div class="con">
                <div class="num-box">
                  <h3>当前学生卡正在消费数量<span>单位(次)</span></h3>
                </div>
                <div id="chart14" class="lineCharts"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="pagination">
        </div>

    </div>
    </if>
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

<script type="text/javascript" src="/Public/plugins/echarts/echarts.js"></script>
<script type="text/javascript" src="/Public/plugins/swiper/swiper-3.4.2.jquery.min.js"></script>
<script type="text/javascript" src="/Public/plugins/swiper/swiper.animate1.0.2.min.js"></script>
<script type="text/javascript">
var user_type = "<?php echo ($user_type); ?>"; // 用户类型 1学校管理员 2系统用户 3老师 4家长 5省/直辖市教委 6 市教委 7 区/县教委  
</script>
<script id="indexecharts" type="text/javascript" src="/Public/plugins/echarts/indexecharts.js"></script>  
<script type="text/javascript">
  $(document).ready(function() {
    var url  = "/Public/plugins/echarts/indexecharts.js";
    var url1 = "/Public/plugins/echarts/indexecharts1.js";
    $(document).delegate('#changeModel1', 'click', function() {
      console.log($(this).hasClass('active'))
      mySwiper.destroy(false);
      if ($(this).hasClass('active')) {
        $(this).removeClass('active');
        jQuery.getScript(url, function(data, status, jqxhr) {});
        // jQuery.ajax({
        //   url: url,
        //   dataType: "script",
        //   cache: false
        // })
      } else {
        $(this).addClass('active');
        jQuery.getScript(url1, function(data, status, jqxhr) {});
        // jQuery.ajax({
        //   url: url1,
        //   dataType: "script",
        //   cache: false
        // })
      };
    });
  })
</script>
</body>
</html>