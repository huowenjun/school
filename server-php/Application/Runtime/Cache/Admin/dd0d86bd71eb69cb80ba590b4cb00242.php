<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>我的消息</title>
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

  <!-- 单选 复选 -->
  <link rel="stylesheet" href="/Public/plugins/iCheck/all.css" />
  <!-- ztree style -->
  <link rel="stylesheet" href="/Public/plugins/zTree/zTreeStyle.css" />
</head>

<body class="hold-transition skin-blue-light sidebar-mini">
<div class="wrapper">
<div class="myMessage">
    <!-- Main Header -->
    <!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>我的消息</title>
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
          <li class="active">我的消息</li>
        </ol>
      </section>

      <!-- Main content -->
      <section class="content">
        <!-- Your Page Content Here -->
        <div class="row">
          <div class="col-xs-12">
            <div class="nav-tabs-custom">
              <form action="" id="from_input" method="post">
              <div class="nav-tabs-custom">
                <ul class="nav nav-tabs js_nav_tab">
                  <input type="hidden" id="tab_type" name="type" value="<?php echo ($type); ?>" />
                  <input type="hidden"  name="s_id" value="" />
                  <input type="hidden"  name="a_id" value="" />
                  <input type="hidden"  name="g_id" value="" />
                  <input type="hidden"  name="c_id" value="" />
                  <input type="hidden" id="m_type" name="m_type" value="<?php echo ($m_type); ?>" />
                  <li class="<?php if($type == 1): ?>active<?php endif; ?> tab tab1" custom-tab="1"><a>收到的消息</a></li>
                  <li class="<?php if($type == 2): ?>active<?php endif; ?> tab tab2" custom-tab="2"><a>已发送的</a></li>
                  <li class="<?php if($type == 3): ?>active<?php endif; ?> tab tab3" custom-tab="3"><a>发送新消息</a></li>
                </ul>
              </div>
              </form>
              <div class="tab-content">
              <?php if($type != 3): ?><!-- /#tab1 -->
                <div class="tab-pane active" id="tab<?php echo ($type); ?>">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="box box-default">
                        <div class="box-header with-border">
                          <ul class="pagination pagination-sm no-margin pull-left js_m_type">
                            <li class="infoBtn" custom-tab=""><a href="#">全部消息</a></li>
                            <li class="infoBtn" custom-tab="1"><a href="#">系统消息</a></li>
                            <li class="infoBtn" custom-tab="2"><a href="#">公告</a></li>
                            <li class="infoBtn" custom-tab="3"><a href="#">私信</a></li>
                          </ul>
                          <div class="box-tools pull-right">
                            <button type="button" class="btn btn-danger btn-sm js_receive_del">
                              <i class="fa fa-trash"></i> 删除
                            </button>
                            <button type="button" class="btn btn-danger btn-sm js_send_del" style="display:none">
                              <i class="fa fa-trash"></i> 删除
                            </button>
                            <button type="button" class="btn btn-primary btn-sm js_read">
                              <i class="fa fa-check-square-o"></i> 设置成已读
                            </button>
                          </div>
                          <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body chat" id="chat-box">
                          <div class="row">
                            <div class="col-md-12">
                              <hr style="border-color: #FFF; margin-top: 0;" />
                              <!-- chat item -->
                              <?php if(is_array($sentmes)): foreach($sentmes as $key=>$vo): ?><div class="user-block item js_item <?php if(($vo['status'] == 1)): ?>active<?php endif; ?>">
                                <input type="hidden" class="js_m_id" name="m_id" value="<?php echo ($vo["m_id"]); ?>" />
                                <img class="img-circle" src="/Public/dist/img/users.png" alt="User Image">
                                <div class="username">
                                  <a href="/index.php/Admin/Parent/MyMessage/detail?m_id=<?php echo ($vo["m_id"]); ?>">
                                    <small class="label tag"><?php echo ($vo["m_type"]); ?></small>
                                    <span class="tit"><?php echo ($vo["user_id"]); ?></span>
                                  </a>
                                  <div class="pull-right btn-box-tool">
                                    <input id="" type="checkbox" name="" class="flat-red checkbox" />
                                  </div>
                                </div>
                                <div class="description"><?php echo ($vo["create_time"]); ?></div>
                                <div class="message">
                                  <div class="text">
                                    <?php echo ($vo["content"]); ?>
                                  </div>
                                </div>
                                <div class="direct-chat-info clearfix">
                                  <span class="direct-chat-timestamp pull-left"><?php echo ($vo["user_id"]); ?></span>
                                </div><br /><hr />
                              </div><?php endforeach; endif; ?>
                              <!-- /.item -->
                            </div>
                          </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="box-footer clearfix">
                          <?php echo ($page); ?>
                        </div>
                        <!-- /.box-footer -->
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /#tab2 --><?php endif; ?>
                <div class="tab-pane js_tab_cont3">
                  <form class="form-horizontal js_form_val">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="m_type1" class="col-sm-4 control-label">选择类型：</label>
                          <div class="col-md-8">
                            <select id="m_type1" class="form-control js_selective_type">
                            <?php if(($user_type == 1) || ($user_type == 3)): ?><option value="2">公告</option>
                            <option value="3">私信</option>
                            <?php elseif($user_type == 4): ?>
                            <option value="3">私信</option>
                            <?php else: ?>
                            <?php if(is_array($mType)): foreach($mType as $k=>$v): ?><option value="<?php echo ($k); ?>"><?php echo ($v); ?></option><?php endforeach; endif; endif; ?>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row value2">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="col-md-4 control-label" for="jjcd1">紧急程度：</label>
                          <div class="col-md-8" id="jjcd1">
                            <label class="checkbox-inline">
                              <input type="radio" name="input1" class="flat-red" value="1" checked /> 普通
                            </label>
                            <label class="checkbox-inline">
                              <input type="radio" name="input1" class="flat-red" value="2" /> 紧急
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row value3">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="col-md-4 control-label">发送对象：</label>
                          <div class="col-md-8" id="sendObj">
                            <label class="checkbox-inline">
                              <input type="radio" name="input2" class="flat-red zuzhi_btn" value="1" checked /> 组织
                            </label>
                            <label class="checkbox-inline">
                              <input type="radio" name="input2" class="flat-red geren_btn" value="2" /> 个人
                            </label>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row value2 zuzhi_ztree">
                      <div class="col-md-4 col-md-offset-2">
                        <div class="box box-default">
                          <div class="box-header with-border">
                            <h3 class="box-title">班级列表</h3>
                            <!-- <form class="form-inline" action="" method="post" onsubmit="return false" id="">
                              <div class="form-group">
                                <input type="text" class="form-control keyword-tactics" placeholder="输入策略名称查询" />
                              </div>
                            </form> -->
                          </div>
                          <div class="box-body">
                            <ul id="treeDemo" class="ztree"></ul>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row value3 geren_ztree">
                      <div class="col-md-4 col-md-offset-2">
                        <div class="box box-default">
                          <div class="box-header with-border">
                            <h3 class="box-title">班级列表</h3>
                          </div>
                          <div class="box-body">
                            <ul id="treeDemo2" class="ztree"></ul>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-4 js_teacherParents" style="display:none">
                        <ul class="pagination pagination-sm no-margin" id="type1">
                          <li class="teacher active" custom-val="1"><a href="javascript:;">老师</a></li>
                          <li class="parents" custom-val="2"><a href="javascript:;">家长</a></li>
                        </ul>
                        <div class="navbar-form person" id="u_id1">
                          <!-- <label class="checkbox-inline">
                            <input id="211" name="" class="flat-red" type="checkbox"> 111
                          </label>
                          <label class="checkbox-inline">
                            <input id="212" name="" class="flat-red" type="checkbox"> 2
                          </label> -->
                        </div>
                      </div>
                    </div>
                    <div class="row value23">
                      <div class="col-md-6">
                        <div class="form-group sendScope">
                          <label class="col-md-4 control-label" for="fsdx1">发送范围：</label>
                          <div class="col-md-8" id="fsdx1">
                            <label class="checkbox-inline">
                              <input id="" type="checkbox" name="" class="flat-red" /> 教师
                            </label>
                            <label class="checkbox-inline">
                              <input id="" type="checkbox" name="" class="flat-red" /> 家长
                            </label>
                          </div>
                        </div>
                        <!--<div class="form-group">
                          <label class="col-md-4 control-label" for="m_tzfs1">通知方式：</label>
                          <div class="col-md-8" id="m_tzfs1">
                            <label class="checkbox-inline">
                              <input id="" type="checkbox" name="" class="flat-red" checked disabled /> 平台
                            </label>
                            <label class="checkbox-inline" id="js_app">
                              <input id="" type="checkbox" name="" class="flat-red" /> APP
                            </label>
                          </div>
                        </div>-->
                      </div>
                    </div>
                    <div class="row value2">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="col-md-4 control-label" for="title1"><span class="xing"></span> 标题：</label>
                          <div class="col-md-8">
                            <input type="text" id="title1" name="title" class="form-control" placeholder="请输入标题" />
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label for="content1" class="col-sm-4 control-label">内容：</label>
                          <div class="col-sm-8">
                            <textarea class="form-control" id="content1" name="content" placeholder="内容"></textarea>
                          </div>
                        </div>
                        <div class="form-group">
                          <div class="col-sm-2 col-sm-offset-4">
                            <button type="button" class=" btn btn-primary btn-sm save">
                              <i class="fa fa-paper-plane-o"></i> 发送消息
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
                <!-- /#tab3 -->
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
</div>
<!-- ./wrapper -->

<!-- 隐藏域 -->
<form id="searchFormTab3" name="searchFormTab3" action="" style="position: fixed; top: 0; right: 0; z-index: 99999;">
  <!-- ztree 公告 + 私信(组织、个人) -->
  <input type="hidden" class="js_input_grade" id="prov_id_hide" name="prov_id" value="" placeholder="省" />
  <input type="hidden" class="js_input_grade" id="city_id_hide" name="city_id" value="" placeholder="市" />
  <input type="hidden" class="js_input_grade" id="county_id_hide" name="county_id" value="" placeholder="区县" />
  <input type="hidden" class="js_input_grade" id="s_id_hide" name="s_id" value="" placeholder="学校" />
  <input type="hidden" class="js_input_grade" id="a_id_hide" name="a_id" value="" placeholder="校区" />
  <input type="hidden" class="js_input_grade" id="g_id_hide" name="g_id" value="" placeholder="年级" />
  <input type="hidden" class="js_input_grade" id="c_id_hide" name="c_id" value="" placeholder="班级" />
  <input type="hidden" class="js_input_grade" id="stu_id_hide" name="stu_id" value="" placeholder="学生" />
  <!-- 系统消息 + 公告 + 私信(组织、个人) -->
  <input type="hidden" id="m_type_hide" name="m_type" value="" placeholder="选择类型" />
  <input type="hidden" id="content_hide" name="content" value="" placeholder="内容" />
  <!-- 公告 -->
  <input type="hidden" id="jjcd_hide" name="jjcd" value="" placeholder="紧急程度" />
  <input type="hidden" id="title_hide" name="title" value="" placeholder="标题" />
  <!-- 公告 + 私信-组织 -->
  <input type="hidden" id="fsdx_hide" name="fsdx" value="" placeholder="发送范围" />
  <!-- 公告 + 私信(组织、个人) -->
  <input type="hidden" id="m_tzfs_hide" name="m_tzfs" value="" placeholder="通知方式" />
  <!-- 私信-个人 -->
  <input type="hidden" id="type_hide" name="type" value="" placeholder="老师/家长" />
  <input type="hidden" id="u_id_hide" name="u_id" value="" placeholder="老师/家长(姓名)" />
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
<script type="text/javascript" data-src="/Public/dist/js/app.min.js" class="loadJS loadJSapp"></script>
<!-- AdminLTE for demo purposes -->
<script type="text/javascript" data-src="/Public/dist/js/demo.js" class="loadJS loadJSdemo"></script>

<!-- 自定义公共js -->
<script type="text/javascript" src="/Public/dist/js/custom.js"></script>
<script type="text/javascript" src="/Public/dist/js/dialog.js"></script>
<!-- layer弹出层 -->
<script type="text/javascript" src="/Public/plugins/layer/layer.js"></script>
<!-- 单选 复选 -->
<script type="text/javascript" src="/Public/plugins/iCheck/icheck.min.js"></script>
<!-- ztree -->
<script type="text/javascript" src="/Public/plugins/ztree/jquery.ztree.all.js"></script>

<script type="text/javascript">
// 用户权限
var user_type = "<?php echo ($user_type); ?>"; // 用户类型 1学校管理员 2系统用户 3老师 4家长 5省委 6市委 7县委

// 刷新页面初始化
$('#sendObj input[type="radio"]').eq(0).iCheck('check').parents('.checkbox-inline').siblings('.checkbox-inline').find('input[type="radio"]').iCheck('uncheck'); // 发送对象 恢复默认选中
$('#fsdx1 input[type="checkbox"]').iCheck('uncheck'); // 发送范围 恢复默认没有选中
$('#content1').val(''); // 内容清空

// 根据用户登录 默认显示隐藏域
if (user_type == '1') { // 1学校管理员
  $('#searchFormTab3 .js_input_grade').slice(0, 3).remove();
};
if (user_type == '3') { // 3老师
  $('#searchFormTab3 .js_input_grade').slice(0, 5).remove();
};
if (user_type == '4') { // 4家长
  $('#searchFormTab3 .js_input_grade:not(.js_input_grade:last)').remove();
  $('#js_app input[type="checkbox"]').iCheck('uncheck').iCheck('disable');
};
if (user_type == '6') { // 6市委
  $('#searchFormTab3 .js_input_grade').eq(0).remove();
};
if (user_type == '7') { // 7县委
  $('#searchFormTab3 .js_input_grade').slice(0, 2).remove();
};

// 提交
function save() {
  var bool = true;
  if (user_type == '4') { // 4家长
    if ($('#stu_id_hide').val() == '') {
      bool = false;
      dialog.error('请选择学生！');
    };
  } else { // 1学校管理员 2系统用户 3老师
    if (($('#c_id_hide').val() == '' && $('.js_selective_type').val() == '2') || ($('#c_id_hide').val() == '' && $('.js_selective_type').val() == '3')) {
      bool = false;
      dialog.error('请选择班级(如若没有添加班级请先去班级管理添加相应班级)！');
    };
  }
  if ($('#content1').val() == '') {
    bool = false;
    dialog.error('内容不能为空！');
  };
  if ($('#m_type_hide').val() == '2' || $('#m_type_hide').val() == '3') { // 2公告 3私信
    if ($('.sendScope').css('display') != 'none') {
      if ($('#fsdx_hide').val() == '') {
        bool = false;
        dialog.error('请选择发送范围！');
      }
    };
  };
  if ($('#sendObj .geren_btn').parents('.iradio_flat-green').hasClass('checked')) {
    if ($('#u_id_hide').val() == '') { // 判断老师/家长(姓名)是否有选中值
      bool = false;
      dialog.error('请根据班级列表选择要发送的老师或家长！');
    };
  };
  if (bool) {
    $('#m_type_hide').val($('.js_selective_type').val()); // 选择类型隐藏域赋值
    $('#content_hide').val($('#content1').val());         // 隐藏域内容赋值
    $('#title_hide').val($('#title1').val());             // 隐藏域标题赋值
    var url = g_config.host + '/index.php/Admin/Parent/MyMessage/mymes_add';
    fpost(url,"searchFormTab3",btnCallbackRefresh);
  };
}

// 公告 + 私信-组织(文件树)
var ajaxTreeNodesCheckbox = null;
var settingCheckbox = {
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
// 当选中发送新消息时 才会请求文件树数据
if ($('.tab.tab3').hasClass('active')) {
  // 请求树
  $.ajax({
    type: "get",
    url: user_type != '4'? "/index.php/Admin/index/get_tree?type=class":"/index.php/Admin/index/get_tree",
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
// 保持展开单一路径 end
function zTreeOnCheckCheckbox(event, treeId, treeNode) {
  // 获取所有被选中的节点
  var treeObj = $.fn.zTree.getZTreeObj("treeDemo");
  var nodes   = treeObj.getCheckedNodes(true);
  var grade   = [];
  // console.log(nodes)
  for (var i = 0; i < 8; i++) {
    grade[i] = [];
    for (var t = 0; t < nodes.length; t++) {
      if(nodes[t].level==i) {
        grade[i].push(nodes[t].id);
      }
    };
    // console.log(grade[i].join())
    $('.js_input_grade').eq(i).val(grade[i].join()); // 隐藏域赋值(地区、学校、校区、年级、班级、学生)
  };
  if (user_type == '4') { // 家长
    if ($('#stu_id_hide').val() != '') {
      fnIfHasSendobj();
    } else {
      $('#u_id_hide').val(''); // 老师/家长(姓名) 隐藏域清空
    };
  } else {
    if ($('#c_id_hide').val() != '') {
      fnIfHasSendobj();
    } else {
      $('#u_id_hide').val(''); // 老师/家长(姓名) 隐藏域清空
    };
  }
}

// 私信-个人(文件树)
var ajaxTreeNodesRadio = null;
var settingRadio = {
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
    onClick: zTreeOnClick
  }
};
// 保持展开单一路径 start
var curExpandNodeRadio = null;
function beforeExpand(treeId, treeNode) {
  var pNode     = curExpandNodeRadio ? curExpandNodeRadio.getParentNode():null;
  var treeNodeP = treeNode.parentTId ? treeNode.getParentNode():null;
  var zTree     = $.fn.zTree.getZTreeObj("treeDemo2");
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
  if (newNode === curExpandNodeRadio) return;

  var zTree = $.fn.zTree.getZTreeObj("treeDemo2"),
    rootNodes, tmpRoot, tmpTId, i, j, n;

  if (!curExpandNodeRadio) {
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
  } else if (curExpandNodeRadio && curExpandNodeRadio.open) {
    if (newNode.parentTId === curExpandNodeRadio.parentTId) {
      zTree.expandNode(curExpandNodeRadio, false);
    } else {
      var newParents = [];
      while (newNode) {
        newNode = newNode.getParentNode();
        if (newNode === curExpandNodeRadio) {
          newParents = null;
          break;
        } else if (newNode) {
          newParents.push(newNode);
        }
      }
      if (newParents!=null) {
        var oldNode = curExpandNodeRadio;
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
  curExpandNodeRadio = newNode;
}
function onExpand(event, treeId, treeNode) {
  curExpandNodeRadio = treeNode;
}
// 当选中发送新消息时 才会请求文件树数据
if ($('.tab.tab3').hasClass('active')) {
  // 请求树
  $.ajax({
    type: "get",
    url: "/index.php/Admin/index/get_tree",
    dataType : 'json',
    success: function(msg) {
      // var groupNodes = msg.data;
      ajaxTreeNodesRadio = msg.data;
      $.fn.zTree.init($("#treeDemo2"), settingRadio, ajaxTreeNodesRadio);
    },
    error: function(msg) {
      dialog.error("请求服务器异常！");
    }
  });
};

// 保持展开单一路径 end
function zTreeOnClick(event, treeId, treeNode) {
  // 勾选当前选中的节点 checkNode方法
  var treeObj = $.fn.zTree.getZTreeObj("treeDemo2");
  var nodes   = treeObj.getSelectedNodes();
  for (var i=0, l=nodes.length; i < l; i++) {
    treeObj.checkNode(nodes[i], true, true);
  }
  if (user_type == '1') { // 学校管理员
    // 点击选择 ztree单选框
    if(treeNode.typeFlag == 'school') {
      $('.js_teacherParents').hide().find('#u_id1').html(); // 隐藏老师和家长按钮
      $('#type_hide, #u_id_hide').val(''); // 清空隐藏域-------老师/家长 和 老师/家长(姓名)
      dialog.error("请选择班级！");
    };
    if(treeNode.typeFlag == 'area') {
      $('.js_teacherParents').hide().find('#u_id1').html(); // 隐藏老师和家长按钮
      $('#type_hide, #u_id_hide').val(''); // 清空隐藏域-------老师/家长 和 老师/家长(姓名)
      dialog.error("请选择班级！");
    };
    if(treeNode.typeFlag == 'grade') {
      $('.js_teacherParents').hide().find('#u_id1').html(); // 隐藏老师和家长按钮
      $('#type_hide, #u_id_hide').val(''); // 清空隐藏域-------老师/家长 和 老师/家长(姓名)
      dialog.error("请选择班级！");
    };
    if(treeNode.typeFlag == 'class') {
      $("#s_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 学校
      $("#a_id_hide").val(treeNode.getParentNode().getParentNode().id); // 校区
      $("#g_id_hide").val(treeNode.getParentNode().id); // 年级
      $("#c_id_hide").val(treeNode.id); // 班级
      $("#stu_id_hide").val(''); // 学生
      $('.js_teacherParents').show().find('.teacher').addClass('active').siblings('li').removeClass('active');
      $('#type_hide').val($('#type1 li.active').attr('custom-val'));
      var url = '/index.php/Admin/Parent/MyMessage/get_user_id1?' + $('#searchFormTab3').serialize();
      fnteacherParents(url, function() {
        dialog.error("请选择班级！");
      });
    };
    if(treeNode.typeFlag == 'student') {
      $("#s_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 学校
      $("#a_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 校区
      $("#g_id_hide").val(treeNode.getParentNode().getParentNode().id); // 年级
      $("#c_id_hide").val(treeNode.getParentNode().id); // 班级
      $("#stu_id_hide").val(treeNode.id); // 学生
      $('.js_teacherParents').show().find('.teacher').addClass('active').siblings('li').removeClass('active');
      $('#type_hide').val($('#type1 li.active').attr('custom-val'));
      var url = '/index.php/Admin/Parent/MyMessage/get_user_id1?' + $('#searchFormTab3').serialize();
      fnteacherParents(url, function() {
        dialog.error("请选择班级！");
      });
    };
  }
  if (user_type == '2' || user_type == '5') { // 2管理员 5省委
    // 点击选择 ztree单选框
    if(treeNode.typeFlag == 'prov') {
      $('.js_teacherParents').hide().find('#u_id1').html(); // 隐藏老师和家长按钮
      $('#type_hide, #u_id_hide').val(''); // 清空隐藏域-------老师/家长 和 老师/家长(姓名)
      dialog.error("请选择班级！");
    };
    if(treeNode.typeFlag == 'city') {
      $('.js_teacherParents').hide().find('#u_id1').html(); // 隐藏老师和家长按钮
      $('#type_hide, #u_id_hide').val(''); // 清空隐藏域-------老师/家长 和 老师/家长(姓名)
      dialog.error("请选择班级！");
    };
    if(treeNode.typeFlag == 'county') {
      $('.js_teacherParents').hide().find('#u_id1').html(); // 隐藏老师和家长按钮
      $('#type_hide, #u_id_hide').val(''); // 清空隐藏域-------老师/家长 和 老师/家长(姓名)
      dialog.error("请选择班级！");
    };
    if(treeNode.typeFlag == 'school') {
      $('.js_teacherParents').hide().find('#u_id1').html(); // 隐藏老师和家长按钮
      $('#type_hide, #u_id_hide').val(''); // 清空隐藏域-------老师/家长 和 老师/家长(姓名)
      dialog.error("请选择班级！");
    };
    if(treeNode.typeFlag == 'area') {
      $('.js_teacherParents').hide().find('#u_id1').html(); // 隐藏老师和家长按钮
      $('#type_hide, #u_id_hide').val(''); // 清空隐藏域-------老师/家长 和 老师/家长(姓名)
      dialog.error("请选择班级！");
    };
    if(treeNode.typeFlag == 'grade') {
      $('.js_teacherParents').hide().find('#u_id1').html(); // 隐藏老师和家长按钮
      $('#type_hide, #u_id_hide').val(''); // 清空隐藏域-------老师/家长 和 老师/家长(姓名)
      dialog.error("请选择班级！");
    };
    if(treeNode.typeFlag == 'class') {
      $("#prov_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().id); // 地区
      $("#city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().id); // 地区
      $("#county_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 地区
      $("#s_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 学校
      $("#a_id_hide").val(treeNode.getParentNode().getParentNode().id); // 校区
      $("#g_id_hide").val(treeNode.getParentNode().id); // 年级
      $("#c_id_hide").val(treeNode.id); // 班级
      $("#stu_id_hide").val(''); // 学生
      $('.js_teacherParents').show().find('.teacher').addClass('active').siblings('li').removeClass('active');
      $('#type_hide').val($('#type1 li.active').attr('custom-val'));
      var url = '/index.php/Admin/Parent/MyMessage/get_user_id1?' + $('#searchFormTab3').serialize();
      fnteacherParents(url, function() {
        dialog.error("请选择班级！");
      });
    };
    if(treeNode.typeFlag == 'student') {
      $("#prov_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().id); // 地区
      $("#city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().id); // 地区
      $("#county_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().id); // 地区
      $("#s_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 学校
      $("#a_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 校区
      $("#g_id_hide").val(treeNode.getParentNode().getParentNode().id); // 年级
      $("#c_id_hide").val(treeNode.getParentNode().id); // 班级
      $("#stu_id_hide").val(treeNode.id); // 学生
      $('.js_teacherParents').show().find('.teacher').addClass('active').siblings('li').removeClass('active');
      $('#type_hide').val($('#type1 li.active').attr('custom-val'));
      var url = '/index.php/Admin/Parent/MyMessage/get_user_id1?' + $('#searchFormTab3').serialize();
      fnteacherParents(url, function() {
        dialog.error("请选择班级！");
      });
    };
  }
  if (user_type == '6') { // 市委
    // 点击选择 ztree单选框
    if(treeNode.typeFlag == 'city') {
      $('.js_teacherParents').hide().find('#u_id1').html(); // 隐藏老师和家长按钮
      $('#type_hide, #u_id_hide').val(''); // 清空隐藏域-------老师/家长 和 老师/家长(姓名)
      dialog.error("请选择班级！");
    };
    if(treeNode.typeFlag == 'county') {
      $('.js_teacherParents').hide().find('#u_id1').html(); // 隐藏老师和家长按钮
      $('#type_hide, #u_id_hide').val(''); // 清空隐藏域-------老师/家长 和 老师/家长(姓名)
      dialog.error("请选择班级！");
    };
    if(treeNode.typeFlag == 'school') {
      $('.js_teacherParents').hide().find('#u_id1').html(); // 隐藏老师和家长按钮
      $('#type_hide, #u_id_hide').val(''); // 清空隐藏域-------老师/家长 和 老师/家长(姓名)
      dialog.error("请选择班级！");
    };
    if(treeNode.typeFlag == 'area') {
      $('.js_teacherParents').hide().find('#u_id1').html(); // 隐藏老师和家长按钮
      $('#type_hide, #u_id_hide').val(''); // 清空隐藏域-------老师/家长 和 老师/家长(姓名)
      dialog.error("请选择班级！");
    };
    if(treeNode.typeFlag == 'grade') {
      $('.js_teacherParents').hide().find('#u_id1').html(); // 隐藏老师和家长按钮
      $('#type_hide, #u_id_hide').val(''); // 清空隐藏域-------老师/家长 和 老师/家长(姓名)
      dialog.error("请选择班级！");
    };
    if(treeNode.typeFlag == 'class') {
      $("#city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().id); // 地区
      $("#county_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 地区
      $("#s_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 学校
      $("#a_id_hide").val(treeNode.getParentNode().getParentNode().id); // 校区
      $("#g_id_hide").val(treeNode.getParentNode().id); // 年级
      $("#c_id_hide").val(treeNode.id); // 班级
      $("#stu_id_hide").val(''); // 学生
      $('.js_teacherParents').show().find('.teacher').addClass('active').siblings('li').removeClass('active');
      $('#type_hide').val($('#type1 li.active').attr('custom-val'));
      var url = '/index.php/Admin/Parent/MyMessage/get_user_id1?' + $('#searchFormTab3').serialize();
      fnteacherParents(url, function() {
        dialog.error("请选择班级！");
      });
    };
    if(treeNode.typeFlag == 'student') {
      $("#city_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().id); // 地区
      $("#county_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().id); // 地区
      $("#s_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 学校
      $("#a_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 校区
      $("#g_id_hide").val(treeNode.getParentNode().getParentNode().id); // 年级
      $("#c_id_hide").val(treeNode.getParentNode().id); // 班级
      $("#stu_id_hide").val(treeNode.id); // 学生
      $('.js_teacherParents').show().find('.teacher').addClass('active').siblings('li').removeClass('active');
      $('#type_hide').val($('#type1 li.active').attr('custom-val'));
      var url = '/index.php/Admin/Parent/MyMessage/get_user_id1?' + $('#searchFormTab3').serialize();
      fnteacherParents(url, function() {
        dialog.error("请选择班级！");
      });
    };
  }
  if (user_type == '7') { // 县委
    // 点击选择 ztree单选框
    if(treeNode.typeFlag == 'county') {
      $('.js_teacherParents').hide().find('#u_id1').html(); // 隐藏老师和家长按钮
      $('#type_hide, #u_id_hide').val(''); // 清空隐藏域-------老师/家长 和 老师/家长(姓名)
      dialog.error("请选择班级！");
    };
    if(treeNode.typeFlag == 'school') {
      $('.js_teacherParents').hide().find('#u_id1').html(); // 隐藏老师和家长按钮
      $('#type_hide, #u_id_hide').val(''); // 清空隐藏域-------老师/家长 和 老师/家长(姓名)
      dialog.error("请选择班级！");
    };
    if(treeNode.typeFlag == 'area') {
      $('.js_teacherParents').hide().find('#u_id1').html(); // 隐藏老师和家长按钮
      $('#type_hide, #u_id_hide').val(''); // 清空隐藏域-------老师/家长 和 老师/家长(姓名)
      dialog.error("请选择班级！");
    };
    if(treeNode.typeFlag == 'grade') {
      $('.js_teacherParents').hide().find('#u_id1').html(); // 隐藏老师和家长按钮
      $('#type_hide, #u_id_hide').val(''); // 清空隐藏域-------老师/家长 和 老师/家长(姓名)
      dialog.error("请选择班级！");
    };
    if(treeNode.typeFlag == 'class') {
      $("#county_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 地区
      $("#s_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 学校
      $("#a_id_hide").val(treeNode.getParentNode().getParentNode().id); // 校区
      $("#g_id_hide").val(treeNode.getParentNode().id); // 年级
      $("#c_id_hide").val(treeNode.id); // 班级
      $("#stu_id_hide").val(''); // 学生
      $('.js_teacherParents').show().find('.teacher').addClass('active').siblings('li').removeClass('active');
      $('#type_hide').val($('#type1 li.active').attr('custom-val'));
      var url = '/index.php/Admin/Parent/MyMessage/get_user_id1?' + $('#searchFormTab3').serialize();
      fnteacherParents(url, function() {
        dialog.error("请选择班级！");
      });
    };
    if(treeNode.typeFlag == 'student') {
      $("#county_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().getParentNode().id); // 地区
      $("#s_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().getParentNode().id); // 学校
      $("#a_id_hide").val(treeNode.getParentNode().getParentNode().getParentNode().id); // 校区
      $("#g_id_hide").val(treeNode.getParentNode().getParentNode().id); // 年级
      $("#c_id_hide").val(treeNode.getParentNode().id); // 班级
      $("#stu_id_hide").val(treeNode.id); // 学生
      $('.js_teacherParents').show().find('.teacher').addClass('active').siblings('li').removeClass('active');
      $('#type_hide').val($('#type1 li.active').attr('custom-val'));
      var url = '/index.php/Admin/Parent/MyMessage/get_user_id1?' + $('#searchFormTab3').serialize();
      fnteacherParents(url, function() {
        dialog.error("请选择班级！");
      });
    };
  }
  if (user_type == '3') { // 老师
    // 点击选择 ztree单选框
    if(treeNode.typeFlag == 'grade') {
      $('.js_teacherParents').hide().find('#u_id1').html(); // 隐藏老师和家长按钮
      $('#type_hide, #u_id_hide').val(''); // 清空隐藏域-------老师/家长 和 老师/家长(姓名)
      dialog.error("请选择班级！");
    };
    if(treeNode.typeFlag == 'class') {
      $("#g_id_hide").val(treeNode.getParentNode().id); // 年级
      $("#c_id_hide").val(treeNode.id); // 班级
      $("#stu_id_hide").val(''); // 学生
      $('.js_teacherParents').show().find('.teacher').addClass('active').siblings('li').removeClass('active');
      $('#type_hide').val($('#type1 li.active').attr('custom-val'));
      var url = '/index.php/Admin/Parent/MyMessage/get_user_id1?' + $('#searchFormTab3').serialize();
      fnteacherParents(url, function() {
        dialog.error("请选择班级！");
      });
    };
    if(treeNode.typeFlag == 'student') {
      $("#g_id_hide").val(treeNode.getParentNode().getParentNode().id); // 年级
      $("#c_id_hide").val(treeNode.getParentNode().id); // 班级
      $("#stu_id_hide").val(treeNode.id); // 学生
      $('.js_teacherParents').show().find('.teacher').addClass('active').siblings('li').removeClass('active');
      $('#type_hide').val($('#type1 li.active').attr('custom-val'));
      var url = '/index.php/Admin/Parent/MyMessage/get_user_id1?' + $('#searchFormTab3').serialize();
      fnteacherParents(url, function() {
        dialog.error("请选择班级！");
      });
    };
  }
  if (user_type == '4') { // 家长
    // 点击选择 ztree单选框
    if(treeNode.typeFlag == 'student') {
      $("#stu_id_hide").val(treeNode.id); // 学生
      $('.js_teacherParents').show().find('.teacher').addClass('active').siblings('li').removeClass('active');
      $('#type_hide').val($('#type1 li.active').attr('custom-val'));
      var url = '/index.php/Admin/Parent/MyMessage/get_user_id1?' + $('#searchFormTab3').serialize();
      fnteacherParents(url, function() {
        dialog.error("请选择学生！");
      });
    };
  };
}

// 公告 + 私信-组织选项 + 私信-个人选项 (获取老师/家长)
function fnteacherParents(url, fn, ifval) { // ifval=false或为空 为单选框文件树选项展示，ifval=true 为复选框文件树选项展示
  $.ajax({
    url:url,
    data:{},
    type:'get',
    cache:false,
    dataType:'json',
    success:function(res) {
      console.log(res)
      if (res.status == 1) {
        if (!ifval) { // 私信-个人选项 获取老师姓名/家长姓名
          var htmls = '';
          for(var key in res.data) {
            htmls+= '<label class="checkbox-inline">';
            htmls+= '<input class="createInput" id="' + key + '" type="checkbox" name="" class="flat-red" /> ' + res.data[key];
            htmls+= '</label>';
          }
          $('#u_id1').html(htmls);
          $('#u_id1 .checkbox-inline').css({
            'width': '100px',
            'height': '30px',
            'line-height': '30px',
            'overflow': 'hidden',
            'margin-left': '0',
            'white-space': 'nowrap'
          });
          $('#u_id1 input').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
          });
          fnU_idSelect();
          // console.log($('#u_id1 input[type="checkbox"]').length)
        } else {
          $('#u_id1').html('');
          $('#u_id_hide').val('');
        };
        $('#u_id_hide').val(res.info);
      } else {
        $('#u_id_hide').val('');
        fn();
      };
    },
    error: function() {
      dialog.error("请求服务器异常！");
    }
  });
}

// 单选框、复选框 css初始化
$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
  checkboxClass: 'icheckbox_flat-green',
  radioClass: 'iradio_flat-green'
});

$('#searchFormTab3 input').val(''); // 清空隐藏域
$.fn.zTree.init($("#treeDemo"), settingCheckbox, ajaxTreeNodesCheckbox); // checkbox文件树初始化
$.fn.zTree.init($("#treeDemo2"), settingRadio, ajaxTreeNodesRadio); // radio文件树初始化

// 私信-个人 (点击老师)
$('.js_teacherParents .teacher').click(function() {
  $('#type_hide').val($(this).attr('custom-val'));
  var url = '/index.php/Admin/Parent/MyMessage/get_user_id1?' + $('#searchFormTab3').serialize();
  $(this).addClass('active').siblings('.parents').removeClass('active');
  fnteacherParents(url, function() {
    dialog.error("请选择班级！");
  });
});
// 私信-个人 (点击家长)
$('.js_teacherParents .parents').click(function() {
  $('#type_hide').val($(this).attr('custom-val'));
  var url = '/index.php/Admin/Parent/MyMessage/get_user_id1?' + $('#searchFormTab3').serialize();
  $(this).addClass('active').siblings('.teacher').removeClass('active');
  fnteacherParents(url, function() {
    dialog.error("请选择班级！");
  });
});

// 页面初始去掉选中状态
$('.js_item .username .icheckbox_flat-green').removeClass('checked');
$('.js_item .username :checkbox:checked').removeAttr('checked');

// 收到的消息 设置成已读
$('.js_read').click(function() {
  var obj = $(this).parents('.box-header').siblings('.box-body').find('.js_item :checkbox:checked').parents('.js_item').find('.js_m_id');
  var arr = [];
  $.each(obj, function(index, val) {
    arr.push($(val).val());
  });
  var str    = arr.join();
  var url    = g_config.host + "/index.php/Admin/Parent/MyMessage/readed";
  var strIds = str;
  var data   = {};
  data.m_id  = strIds;

  if (str.length == 0) {
    dialog.notify('请先选择要置为已读的记录');
    return;
  } else {
    dialog.confirm('您确定要置为已读吗？',function(){dpost(url,data,btnCallbackRefresh);});
  }
});

// 收到的消息 删除
$('.js_receive_del').click(function() {
  var obj = $(this).parents('.box-header').siblings('.box-body').find('.js_item :checkbox:checked').parents('.js_item').find('.js_m_id');
  var arr = [];
  $.each(obj, function(index, val) {
    arr.push($(val).val());
  });
  var str    = arr.join();
  var url    = g_config.host + "/index.php/Admin/Parent/MyMessage/receiveMes_del";
  var strIds = str;
  var data   = {};
  data.m_id  = strIds;

  if (str.length == 0) {
    dialog.notify('请先选择要删除的记录');
    return;
  } else {
    dialog.confirm('您确定要删除已选项吗？',function(){dpost(url,data,btnCallbackRefresh);});
  }
});

// 已发送的 删除
$('.js_send_del').click(function() {
  var obj = $(this).parents('.box-header').siblings('.box-body').find('.js_item :checkbox:checked').parents('.js_item').find('.js_m_id');
  var arr = [];
  $.each(obj, function(index, val) {
    arr.push($(val).val());
  });
  var str    = arr.join();
  var url    = g_config.host + "/index.php/Admin/Parent/MyMessage/sentMes_del";
  var strIds = str;
  var data   = {};
  data.m_id  = strIds;

  if (str.length == 0) {
    dialog.notify('请先选择要删除的记录');
    return;
  } else {
    dialog.confirm('您确定要删除已选项吗？',function(){dpost(url,data,btnCallbackRefresh);});
  }
});

// 发送消息-选择类型
var ifCheakStyle = true;
// js_selective_type判断初始值
if ($('.js_selective_type').val() == '3') { // 家长端用户默认显示私信
  $('#searchFormTab3 input').val(''); // 清空隐藏域
  $('.value2').hide();
  $('.value3, .value23, .value2.zuzhi_ztree').show();
  $('.value3.geren_ztree').hide();
};

if ($('.js_selective_type').val() == '2') { // 老师端用户默认显示公告
  $('.value2, .value23, .sendScope').show();
  $('#jjcd_hide').val($('#jjcd1 :radio:checked').val()); // 紧急程度 隐藏域赋值
  $('#fsdx_hide').val(''); // 发送范围 隐藏域赋值
  tzfsDefault(); // 通知方式 隐藏域赋默认值
};

$('.js_selective_type').change(function() {
  ifCheakStyle = false;
  if ($(this).val() == '1') {
    $('#searchFormTab3 input').val(''); // 清空隐藏域
    $('.value2, .value3, .value23').hide();
    $('#content1').val(''); // 内容清空
    $('#m_type_hide').val($(this).val()); // 选择类型隐藏域赋值
  };
  if ($(this).val() == '2') {
    $.fn.zTree.init($("#treeDemo"), settingCheckbox, ajaxTreeNodesCheckbox); // checkbox文件树初始化
    $('#searchFormTab3 input').val(''); // 清空隐藏域
    $('.value3').hide();
    $('.value2, .value23, .sendScope').show();
    $.fn.zTree.init($("#treeDemo"), settingCheckbox, ajaxTreeNodesCheckbox); // checkbox文件树初始化
    $('#jjcd1 input[type="radio"]').eq(0).iCheck('check').parents('.checkbox-inline').siblings('.checkbox-inline').find('input[type="radio"]').iCheck('uncheck'); // 紧急程度 恢复默认选中
    $('#jjcd_hide').val($('#jjcd1 :radio:checked').val()); // 紧急程度 隐藏域赋值
    $('#fsdx1 input[type="checkbox"]').iCheck('uncheck'); // 发送范围 恢复默认没有选中
    $('#fsdx_hide').val(''); // 发送范围 隐藏域赋值
    $('#m_tzfs1 input[type="checkbox"]').eq(0).iCheck('check').parents('.checkbox-inline').siblings('.checkbox-inline').find('input[type="checkbox"]').iCheck('uncheck'); // 通知方式 恢复默认选中
    tzfsDefault(); // 通知方式 隐藏域赋默认值
    $('#title1, #content1').val(''); // 标题清空 内容清空
    $('#m_type_hide').val($(this).val()); // 选择类型隐藏域赋值
    $('#js_app input[type="checkbox"]').iCheck('enable');
  };
  if ($(this).val() == '3') {
    $.fn.zTree.init($("#treeDemo"), settingCheckbox, ajaxTreeNodesCheckbox); // checkbox文件树初始化
    $('#searchFormTab3 input').val(''); // 清空隐藏域
    $('.value2').hide();
    $('.value3, .value23, .value2.zuzhi_ztree').show();
    $('.value3.geren_ztree').hide();
    $.fn.zTree.init($("#treeDemo"), settingCheckbox, ajaxTreeNodesCheckbox); // checkbox文件树初始化
    $.fn.zTree.init($("#treeDemo2"), settingRadio, ajaxTreeNodesRadio); // radio文件树初始化
    $('.sendScope').show();
    $('#sendObj input[type="radio"]').eq(0).iCheck('check').parents('.checkbox-inline').siblings('.checkbox-inline').find('input[type="radio"]').iCheck('uncheck'); // 发送对象 恢复默认选中
    $('#m_tzfs1 input[type="checkbox"]').eq(0).iCheck('check').parents('.checkbox-inline').siblings('.checkbox-inline').find('input[type="checkbox"]').iCheck('uncheck'); // 通知方式 恢复默认选中
    tzfsDefault(); // 通知方式 隐藏域赋默认值
    $('#fsdx1 input[type="checkbox"]').iCheck('uncheck'); // 发送范围 恢复默认没有选中
    $('#content1').val(''); // 内容清空
    $('#m_type_hide').val($(this).val()); // 选择类型隐藏域赋值
    $('#js_app input[type="checkbox"]').iCheck('uncheck').iCheck('disable');
  };
  ifCheakStyle = true;
});

// 点击组织/个人 显示对应文件树
$('.zuzhi_btn').on('ifChecked', function() {
  $.fn.zTree.init($("#treeDemo"), settingCheckbox, ajaxTreeNodesCheckbox); // checkbox文件树初始化
  $('#searchFormTab3 input').val(''); // 清空隐藏域
  $('.value2.zuzhi_ztree').show().siblings('.value3.geren_ztree').hide();
  $('.sendScope').show(); // 显示发送范围
  $.fn.zTree.init($("#treeDemo"), settingCheckbox, ajaxTreeNodesCheckbox); // checkbox文件树初始化
  $.fn.zTree.init($("#treeDemo2"), settingRadio, ajaxTreeNodesRadio); // radio文件树初始化
  $('#fsdx1 input[type="checkbox"]').iCheck('uncheck'); // 发送范围 恢复默认没有选中
  $('#m_tzfs1 input[type="checkbox"]').eq(0).iCheck('check').parents('.checkbox-inline').siblings('.checkbox-inline').find('input[type="checkbox"]').iCheck('uncheck'); // 通知方式 恢复默认选中
  tzfsDefault(); // 通知方式 隐藏域赋默认值
  $('#content1').val(''); // 内容清空
});
$('.geren_btn').on('ifChecked', function() {
  $.fn.zTree.init($("#treeDemo"), settingCheckbox, ajaxTreeNodesCheckbox); // checkbox文件树初始化
  $('#searchFormTab3 input').val(''); // 清空隐藏域
  $('.value3.geren_ztree').show().siblings('.value2.zuzhi_ztree').hide();
  $('.sendScope').hide();
  $.fn.zTree.init($("#treeDemo"), settingCheckbox, ajaxTreeNodesCheckbox); // checkbox文件树初始化
  $.fn.zTree.init($("#treeDemo2"), settingRadio, ajaxTreeNodesRadio); // radio文件树初始化
  $('#m_tzfs1 input[type="checkbox"]').eq(0).iCheck('check').parents('.checkbox-inline').siblings('.checkbox-inline').find('input[type="checkbox"]').iCheck('uncheck'); // 通知方式 恢复默认选中
  tzfsDefault(); // 通知方式 隐藏域赋默认值
  $('#content1').val(''); // 内容清空
  $('.js_teacherParents').hide().find('#u_id1').html(); // 隐藏老师和家长按钮
});

// 获取tab类型初始值 显示相应内容
if ($('#tab_type').attr('value') == '1') { // 接收到的消息
  $('.js_receive_del').show().siblings('.js_send_del').hide();
};
if ($('#tab_type').attr('value') == '2') { // 已发送的
  $('.js_send_del').show().siblings('.js_receive_del').hide();
  $('.js_read').hide();
} else {
  $('.js_read').show();
};
if ($('#tab_type').attr('value') == '3') { // 发送新消息
  $('.js_tab_cont3').show();
  $('#m_type_hide').val($('.js_selective_type').val()); // 页面初始化 选择类型隐藏域赋值
} else {
  $('.js_tab_cont3').hide();
  $('#m_type_hide').val(''); // 页面初始化 选择类型隐藏域赋值
};

// 获取消息类型初始值
if ($('#m_type').val() != '') {
  $('.js_m_type li[custom-tab='+$('#m_type').val()+']').addClass('active');
} else {
  $('.js_m_type li').eq(0).addClass('active');
};

// 标签样式(系统消息 公告 私信)
var objTxt = $('.js_item .username .tag');
$.each(objTxt, function(index, val) {
  if ($(val).text() == '系统消息') { // 系统消息
    $(this).addClass('bg-red');
  };
  if ($(val).text() == '公告') { // 公告
    $(this).addClass('bg-yellow');
  };
  if ($(val).text() == '私信') { // 私信
    $(this).addClass('bg-blue');
  };
});

// 点击tab切换 获取tab类型和消息类型 并提交表单
$('.js_nav_tab .tab').click(function() {
  $('#tab_type').val($(this).attr('custom-tab'));
  $('#m_type').val($('.js_m_type .infoBtn').eq(0).attr('custom-tab'));
  // console.log($('#from_input').serialize());
  $('#from_input').submit();
})

// 点击消息类型 获取消息类型 并提交表单
$('.js_m_type .infoBtn').click(function() {
  $('#m_type').val($(this).attr('custom-tab'));
  // console.log($('#from_input').serialize());
  $('#from_input').submit();
})

// 选择紧急程度 并给隐藏域赋值
$('#jjcd1 input[type="radio"]').on('ifChecked', function() {
  $('#jjcd_hide').val($(this).val());
});

// 选择发送范围 并给隐藏域赋值
$('#fsdx1 input[type="checkbox"]').on('ifChanged', function() {
  var obj = $('#fsdx1 input[type="checkbox"]');
  var arr = [];
  $.each(obj, function(index, val) {
    arr.push(Number($(val).prop('checked')));
  });
  // console.log(arr)
  if (arr == '0,0') {
    $('#fsdx_hide').val('');
  } else {
    $('#fsdx_hide').val(arr);
  };
  // if (ifCheakStyle) {
  //   fnIfHasSendobj();
  // };
  if (user_type == '4') { // 家长
    if ($('#stu_id_hide').val() != '') {
      fnIfHasSendobj();
    };
  } else {
    if ($('#c_id_hide').val() != '') {
      fnIfHasSendobj();
    };
  }
});

// 选择老师姓名/家长姓名 并给隐藏域赋值 (私信-个人)
function fnU_idSelect() {
  $('#u_id1 input[type="checkbox"]').on('ifChanged', function() {
    var obj = $('#u_id1 input[type="checkbox"]:checked');
    var arr = [];
    if ($('#u_id1 input[type="checkbox"]:checked').length > 0) {
      $.each(obj, function(index, val) {
        arr.push($(val).attr('id'));
      });
      // console.log(arr)
      $('#u_id_hide').val(arr);
    } else {
      $('#u_id_hide').val('');
    };    
  });
}

// 选择通知方式 并给隐藏域赋值
function tzfsDefault() {
  var obj = $('#m_tzfs1 input[type="checkbox"]');
  var arr = [];
  $.each(obj, function(index, val) {
    var num = Number($(val).prop('checked'));
    if (index > 0 && num) {
      num+=1;
    };
    arr.push(num);
  });
  // console.log(arr)
  $('#m_tzfs_hide').val(arr);
}
$('#m_tzfs1 input[type="checkbox"]').on('ifChanged', function() {
  tzfsDefault();
});

// 判断是否有选中的发送范围
function fnIfHasSendobj() {
  if ($('#fsdx1 input[type="checkbox"]:checked').length > 0) {
    // 请求接口 获取u_id并赋值
    var url = '/index.php/Admin/Parent/MyMessage/get_user_id?' + $('#searchFormTab3').serialize();
    fnteacherParents(url, function(){}, true);
    // if (user_type == '4') { // 家长
    //   fnteacherParents(url, function() {
    //     dialog.error("请选择学生！");
    //   },true);
    // } else {
    //   fnteacherParents(url, function() {
    //     dialog.error("请选择班级！");
    //   },true);
    // }
  } else {
    $('#type_hide, #u_id_hide').val(''); // 老师/家长+老师/家长(姓名) 隐藏域清空
    // dialog.error("请选择发送范围！");
  };
}

// layer异步分页
// function demo(curr) {
//   $.getJSON('test/demo1.json', {
//     page: curr || 1                 // 向服务端传的参数
//   }, function(res) {
//     // 变化的内容
//     // var demoContent = (new Date().getTime()/Math.random()/1000)|0;
//     // document.getElementById('view1').innerHTML = res.content + demoContent;
//     // 显示分页
//     laypage({
//       cont: 'page',                 // 容器。值支持id名、原生dom对象，jquery对象。【如该容器为】：<div id="page1"></div>
//       pages: res.pages,             // 通过后台拿到的总页数
//       curr: curr || 1,              // 当前页
//       jump: function(obj, first) {  // 触发分页后的回调
//         if(!first) {                // 点击跳页触发函数自身，并传递当前页：obj.curr
//           demo(obj.curr);
//         }
//       }
//     });
//   });
// };
// demo();

// laypage({
//   cont: 'page',
//   pages: 18, //可以叫服务端把总页数放在某一个隐藏域，再获取。假设我们获取到的是18
//   curr: function(){ //通过url获取当前页，也可以同上（pages）方式获取
//     var page = location.search.match(/page=(\d+)/);
//     return page ? page[1] : 1;
//   }(),
//   jump: function(e, first){ //触发分页后的回调
//     if(!first){ //一定要加此判断，否则初始时会无限刷新
//       location.href = '?page='+e.curr;
//     }
//   }
// });
</script>

</body>
</html>