if (user_type == '2') {
// 统计区域总高度控制
function swiperContainerH() {
  var windowH     = $(window).height();
  var headerH     = $('.main-header').outerHeight(true);
  var FooterH     = $('.main-footer').outerHeight(true);
  var swiperContainerH    = windowH - headerH - FooterH;
  $('.swiper-container').css({
    'overflow-y': 'auto',
    'height': swiperContainerH
  });

  var pageH = swiperContainerH-80+"px"
  $('.page').css({
    'height': pageH
  });

  $('.page-left .con').css({
    'height': pageH
  });
}
swiperContainerH();
// 统计区域右侧宽度控制
function pageRightW() {
  var pageW = $('.swiper-slide .page').width();
  var pageLeftW = $('.swiper-slide .page-left').width() +20;
  var pageRightW    = pageW - pageLeftW;
  $('.swiper-slide .page-right').css({'width':pageRightW});
}
pageRightW();

// 控制统计图高度
function resizeChart() {
  swiperContainerH();
  var stuCardChartH = $("#chart1");
  var areaChartH = $(".areaChart");
  // var monitorMapH = $("#monitorMap");
  var appPageleftH = $(".app-page-left .con");
  var lineChartH = $(".lineChart");
  var lineChartsH = $(".lineCharts");
  stuCardChartH.css({
    'height': $(".page").height() - 154
  });
  areaChartH.css({
    'height': $(".page").height()/3 -20
  });
  // monitorMapH.css({
  //   'height': $(".page").height() - 102
  // });
  appPageleftH.css({
    'height': $(".page").height()
  });

  lineChartH.css({
    'height': $(".page").height()/2 -64
  });

  lineChartsH.css({
    'height': $(".page").height() -60
  });
};
resizeChart();

// 控制统计数量字体大小
function fontChart(){
  var pageLeftW = $(".swiper-slide .page-left").width();
  if(pageLeftW<370){ 
    $(".swiper-slide .page-left .num-box h3").css('font-size', '23px');
    $(".swiper-slide .page-left .num-box p").css('font-size', '30px');
  }else{
    $(".swiper-slide .page-left .num-box h3").css('font-size', '35px');
    $(".swiper-slide .page-left .num-box p").css('font-size', '45px');
  }
}
fontChart();

// swiper轮播
var mySwiper = new Swiper ('.swiper-container', {
  pagination: '.pagination',
  paginationClickable :true,
  autoplay : 30000,
  speed:500,
  onInit: function(swiper){ 
    swiperAnimateCache(swiper); 
    swiperAnimate(swiper); 
  }, 
  onSlideChangeEnd: function(swiper){ 
    swiperAnimate(swiper);
  },
  onSlideChangeEnd: function(swiper){
    if(swiper.activeIndex==0){
      stuCardInfo();
      stuCardAreaInfo();
    }
    // if(swiper.activeIndex==1){
    //   heatmapOverlay();
    // }
    if(swiper.activeIndex==1){
      userInfo();
      attendanceInfo();
    }
    if(swiper.activeIndex==2){
      userInfo();
      getAppCounts();
    }
    if(swiper.activeIndex==3){
      userInfo();
      getAppModuleCounts();
    }
    if(swiper.activeIndex==4){
      userInfo();
      getChatCounts();
      getAppModuleCounts();
    }
    if(swiper.activeIndex==5){
      userInfo();
      stuCardBillCounts();
    }
  }
})    

// 统计图清空
function chartClear(chart){
  chart.clear();
}

// 学生卡总数量计算
function getCardSum(arr){
  var cardSum = 0;
  for (var i = 0; i < arr.length; i++){
  cardSum += parseInt(arr[i]);
  }
  return cardSum;
}

// 学生卡数量信息接口调用及数据处理
function stuCardInfo(){
  var url = '/index.php/Api/Counts/stuCardInfo';
   fget(url,'',function(res){
    if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
    g_loadingIndex = -1;
    if(res.status==1){
      var numArr =[];
      var cardInfo = res.data.info;
      $.each(cardInfo, function(key, value) {
        numArr.push(value.value);
      })   
      var totalnum =getCardSum(numArr);
      $(".page-one-left p").html(totalnum);
      stuCardInfoChart(cardInfo);     
    }else{
      dialog.error(res.info);
    }
  })
}
stuCardInfo();

// 学生卡总数量及使用数量(饼状图)
var timer=null;
var chart1 = echarts.init(document.getElementById('chart1'));
function stuCardInfoChart(data){
  clearInterval(timer);
  var app ={};
  option = {
    color: ['#84df56','#cc5664'],
    title : {
      text: '使用数量统计',
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
        data:[data[1].name,data[0].name],
        textStyle: {
          color:'#fff'
        },
    },
    startAngle: [90,270],
    tooltip: {
      trigger: 'item',
      formatter: "{a} <br/>{b} : {c} ({d}%)"
    },
    series: [
      {
        name: '使用数量统计',
        type: 'pie',
        radius: ['20%','40%'],
        center: ['50%','48%'],
        tooltip: {
           position: ['40%','63%'],
        },
        label: {
          normal: {
            position: 'outside',
            formatter: "{c}",
            color: "#fff",
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
    chart1.dispatchAction({
      type: 'downplay',
      seriesIndex: 0,
      dataIndex: app.currentIndex
    });
    app.currentIndex = (app.currentIndex + 1) % dataLen;
    // 高亮当前图形
    chart1.dispatchAction({
      type: 'highlight',
      seriesIndex: 0,
      dataIndex: app.currentIndex
    });
    // 显示 tooltip
    chart1.dispatchAction({
      type: 'showTip',
      seriesIndex: 0,
      dataIndex: app.currentIndex
    });
  }, 3000);

  chartClear(chart1);
  chart1.setOption(option); 
}

// 各省发卡数量接口调用
function stuCardAreaInfo(){
  var url = '/index.php/Api/Counts/stuCardAreaInfo';
  fget(url,'',function(res){
    if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
    g_loadingIndex = -1;
    if(res.status==1){
      var provInfo=res.data.prov;
      var timeInfo = res.data.info[0].data;
      var sendInfo = res.data.info[1].data;
      areaDP(provInfo,timeInfo,sendInfo);
    }else{
      dialog.error(res.info);
    }
  })
}
stuCardAreaInfo();

//各省发卡数据处理
function areaDP(data1,data2,data3){
  var sendInfo = [];
  for (var i = 0; i < arguments.length; i++) {
    var provLength=arguments[i].length;
    var tenLength=parseInt(provLength/11);
    var tenMod=provLength%11;
    if (tenMod>0){
      tenLength=tenLength+1;
    }
    for (var multiple = 0; multiple <tenLength; multiple++) {
      var start=parseInt(11*multiple);
      var end=parseInt(start+11);
      if(end>=provLength){
        end=parseInt(provLength+1);
      }
      var arrys = arguments[i].slice(start, end);
      sendInfo.push(arrys);
    };
  };
  areaInfoChart(sendInfo);
}

// 各省发卡数量及使用数量情况统计(饼状图)
var chart2 = echarts.init(document.getElementById('chart2'));
var chart3 = echarts.init(document.getElementById('chart3'));
var chart4 = echarts.init(document.getElementById('chart4'));
function areaInfoChart(datas){
  var app ={};
  app.config = {
    rotate: 90,
    align: 'left',
    verticalAlign: 'middle',
    position: 'insideBottom',
    distance: 15,
    onChange: function () {
      var labelOption = {
        normal: {
          rotate: app.config.rotate,
          align: app.config.align,
          verticalAlign: app.config.verticalAlign,
          position: app.config.position,
          distance: app.config.distance
        }
      };
      
    }
  };
  var labelOption = {
    normal: {
      show: true,
      position: app.config.position,
      distance: app.config.distance,
      align: app.config.align,
      verticalAlign: app.config.verticalAlign,
      rotate: app.config.rotate,
      formatter: '{c}  {name|{a}}',
      fontSize: 16,
      rich: {
        name: {
          textBorderColor: '#fff'
        }
      }
    }
  };

  // myChart1
  option1 = {
    color: ['#84df56', '#f58345'],
    tooltip: {
      trigger: 'axis',
      axisPointer: {
        type: 'shadow'
      }
    },
    legend: {
      data: ['总数量','正在使用数量'],
      right:'5%',
      textStyle: {
        color:'#fff'
      }
    },
    calculable: true,
    xAxis: [
      {
        type: 'category',
        axisTick: {show: false},
        axisLine: {
          lineStyle:{
            color:'#0e5370',
            width:'3'
          }
        },
        axisLabel:{
          interval:0,
          fontSize:'12',
          color:'#83ECF3'
        },
        splitLine: {
          show: false,
        },
        data: datas[0]
      },  
    ],
    yAxis: [
      {
        type: 'value',
        axisTick: {show: false},
        axisLine: {
          lineStyle:{
            color:'#115372',
            width:'3'
          }
        },
        axisLabel:{
          fontSize:'15',
          color:'#fff'
        },
        splitLine: {
            show: true,
            lineStyle: {
                color: ['#0e5370'],
                type: 'dotted'
            }
        },minInterval: 1,
        splitNumber:3

      }
    ],
    label: {
      emphasis: {
        show: false,
      }
    },
    series: [
      {
        name: '总数量',
        type: 'bar',
        barWidth : 15,
        data: datas[3]
      },
      {
        name: '正在使用数量',
        type: 'bar',
        barWidth : 15,
        data: datas[6]
      }
    ]
  };
  chartClear(chart2);
  chart2.setOption(option1); 
  // myChart2
  option2 = {
    color: ['#84df56', '#f58345'],
    tooltip: {
      trigger: 'axis',
      axisPointer: {
          type: 'shadow'
      }
    },
    // legend: {
    //   data: ['总数量', '正在使用数量'],
    //   right:'5%',
    //   textStyle: {
    //     color:'#fff'
    //   }   
    // },
    calculable: true,
    xAxis: [
      {
        type: 'category',
        axisTick: {show: false},
        axisLine: {

          lineStyle:{
            color:'#0e5370',
            width:'3'
          }
        },
        axisLabel:{
          interval:0,
          fontSize:'12',
          color:'#83ECF3'
        },
        splitLine: {
            show: false,
        },
        data: datas[1]
      },  
    ],
    yAxis: [
      {
        type: 'value',
        axisTick: {show: false},
        axisLine: {
          lineStyle:{
            color:'#0e5370',
            width:'3'
          }
        },
        axisLabel:{
          fontSize:'15',
          color:'#fff'
        },
        splitLine: {
          show: true,
          lineStyle: {
            color: ['#0e5370'],
            type: 'dotted'    
          }
        },minInterval:1,
        splitNumber:3
      }
    ],
    label: {
      emphasis: {
        show: false,
      }
    },
    series: [
      {
       name: '总数量',
       type: 'bar',
       barWidth : 15,
       data: datas[4]
      },
      {
        name: '正在使用数量',
        type: 'bar',
        barWidth : 15,
        data: datas[7]
      }
    ]
  };
  chartClear(chart3);
  chart3.setOption(option2); 
  // myChart3
  option3 = {
    color: ['#84df56', '#f58345'],
    tooltip: {
      trigger: 'axis',
      axisPointer: {
          type: 'shadow'
      }
    },
    // legend: {
    //   data: ['总数量', '正在使用数量'],
    //   right:'5%',
    //   textStyle: {
    //     color:'#fff'
    //   }
    // },
    calculable: true,
    xAxis: [
      {
        type: 'category',
        axisTick: {show: false},
        axisLine: {

          lineStyle:{
            color:'#0e5370',
            width:'3'
          }
        },
        axisLabel:{
          interval:0,
          fontSize:'12',
          color:'#83ECF3'
        },
        splitLine: {
            show: false,
        },
        data: datas[2]
      },  
    ],
    yAxis: [
      {
        type: 'value',
        axisTick: {show: false},
        axisLine: {
          lineStyle:{
            color:'#0e5370',
            width:'3'
          }
        },
        axisLabel:{
          fontSize:'15',
          color:'#fff'
        },
        splitLine: {
          show: true,
          lineStyle: {
            color: ['#0e5370'],
            type: 'dotted'    
          }
        },minInterval:1,
        splitNumber:3    
      }
    ],
    label: {
      emphasis: {
        show: false,
      }
    },
    series: [
      {
       name: '总数量',
       type: 'bar',
       barWidth : 15,
       data: datas[5]
      },
      {
        name: '正在使用数量',
        type: 'bar',
        barWidth : 15,
        data: datas[8]
      }
    ]
  };
  chartClear(chart4);
  chart4.setOption(option3); 
}

// 考勤数量接口调用
function attendanceInfo(){
  var url = '/index.php/Api/Counts/attendanceInfo';
  fget(url,'',function(res){
    if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
    g_loadingIndex = -1;
    if(res.status==1){
      var inInfo = res.data.signin;
      var outInfo = res.data.signout;
      var atteInfoArr = [inInfo,outInfo];
      attInfoDP(atteInfoArr); 
    }else{
      dialog.error(res.info);
    }
  })
} 
attendanceInfo();

// 考勤数据处理
function attInfoDP(datas){
  var attInfo = [];
  for (var i = 0; i < datas.length; i++) {
  var attInfoKey = [];
  var attInfoValue = [];
    for(var key in datas[i]){
      attInfoKey.push(key);
      attInfoValue.push(datas[i][key]);
    }
   attInfo.push(attInfoKey,attInfoValue)
  };
  attInfoChart(attInfo);
}
// 考勤数量统计(折线图)
var chart6 = echarts.init(document.getElementById('chart6'));
var chart7 = echarts.init(document.getElementById('chart7'));
function attInfoChart(datas){
  option1 ={
    color: ['#d71b3c'],
    tooltip : {
      trigger: 'axis',
      // formatter: "{b}<br/>{a0}{c0}<br/>{a1}{c1} %"
    },
    calculable : false,
    xAxis : [
      {
        type : 'category',
        axisTick: {show: false},
        name : '时段',
        nameTextStyle: {
          color : '#fff'
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
          color:'#83ECF3'
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
        color:'#fff'
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
        name:'进校数量',
        type:'line',
        smooth:true,
        data:  datas[1]

      }
    ],
  }
  chartClear(chart6);
  chart6.setOption(option1);
  option2 ={
    color: ['#d71b3c'],
    tooltip : {
      trigger: 'axis',
      // formatter: "{b}<br/>{a0}{c0}<br/>{a1}{c1} %"
    },
    calculable : false,
    xAxis : [
      {
        type : 'category',
        axisTick: {show: false},
        name : '时段',
        nameTextStyle: {
          color : '#fff'
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
          color:'#83ECF3'
        },
        splitLine: {
          show: false,
        },
        data : datas[2]
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
        color:'#fff'
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
        name:'出校数量',
        type:'line',
        smooth:true,
        data:  datas[3]

      }
    ],
  }
  chartClear(chart7);
  chart7.setOption(option2);
}

//APP 用户数及在线数接口调用
function userInfo(){
  var url = '/index.php/Api/Counts/userInfo';
  fget(url,'',function(res){
    if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
    g_loadingIndex = -1;
    if(res.status==1){
      var teacher = res.data.teacher;
      var teacherOnline = res.data.teacher_online;
      var parents = res.data.parents;
      var parentsOnline = res.data.parents_online;
      var userInfoArr = [teacher,teacherOnline,parents,parentsOnline];
      userInfoShow(userInfoArr);
    }else{
      dialog.error(res.info);
    }
  })
}  
userInfo();

//APP 用户数及在线数展示
function userInfoShow(datas){
  var pageTwo = $(".page-two .num-box p");
  var pageThree= $(".page-three .num-box p");
  var pageFour = $(".page-four .num-box p");
  var pageFive = $(".page-five .num-box p");
  var pageSix = $(".page-six .num-box p");
  for (var i = 0; i < datas.length; i++) {
    pageTwo.eq(i).html(datas[i]);
    pageThree.eq(i).html(datas[i]);
    pageFour.eq(i).html(datas[i]);
    pageFive.eq(i).html(datas[i]);
    pageSix.eq(i).html(datas[i]);
  }
}

//APP 用户使用次数接口调用
function getAppCounts(){
  var url = '/index.php/Api/Counts/getAppCounts';
  fget(url,'',function(res){
    if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
    g_loadingIndex = -1;
    if(res.status==1){
      var IOSteacher = res.data.IOSteacher;
      var Androidteacher = res.data.Androidteacher;
      var IOSparents = res.data.IOSparents;
      var Androidparents = res.data.Androidparents;
      var appCountsArr = [IOSteacher,Androidteacher,IOSparents,Androidparents];  
      getAppCountsDP(appCountsArr); 
    }else{
      dialog.error(res.info);
    }
  })
}
getAppCounts();

//APP 用户使用次数数据处理
function getAppCountsDP(datas){
  var appCounts = [];
  for (var i = 0; i < datas.length; i++) {
  var appCountsKey = [];
  var appCountsValue = [];
    for(var key in datas[i]){
      appCountsKey.push(key);
      appCountsValue.push(datas[i][key]);
    }
   appCounts.push(appCountsKey,appCountsValue)
  };
  appCountChart(appCounts);
}

// APP 用户使用次数统计(折线图)
var chart8 = echarts.init(document.getElementById('chart8'));
var chart9 = echarts.init(document.getElementById('chart9'));
function appCountChart(datas){
  option1 ={
    color: ['#8DE160','#d71b3c'],
    tooltip : {
      trigger: 'axis',
      // formatter: "{b}<br/>{a0}{c0}<br/>{a1}{c1} %"
    },
    legend: {
      data:['苹果端','安卓端'],
      right:'5%',
      textStyle: {
        color:'#fff'
      }
    },
    calculable : false,
    xAxis : [
      {
        type : 'category',
        axisTick: {show: false},
        name : '时段',
        nameTextStyle: {
          color : '#fff'
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
          color:'#83ECF3'
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
        color:'#fff'
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
        name:'苹果端',
        type:'line',
        smooth:true,
        data:  datas[1]

      },
      {
        name:'安卓端',
        type:'line',
        smooth:true,
        data: datas[3]
      }
    ],
  }
  chartClear(chart8)
  chart8.setOption(option1);
  option2 = {
    color: ['#8DE160','#d71b3c'],
    tooltip : {
      trigger: 'axis',
      // formatter: "{b}<br/>{a0}{c0}<br/>{a1}{c1} %"
    },
    legend: {
      data:['苹果端','安卓端'],
      right:'5%',
      textStyle: {
        color:'#fff'
      }
    },
    calculable : false,
    xAxis : [
      {
        type : 'category',
        axisTick: {show: false},
        name : '时段',
        nameTextStyle: {
          color : '#fff'
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
          color:'#83ECF3'
        },
        splitLine: {
          show: false,
        },
        data : datas[4]
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
        color:'#fff'
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
        name:'苹果端',
        type:'line',
        smooth:true,
        data:  datas[5]

      },
      {
        name:'安卓端',
        type:'line',
        smooth:true,
        data:  datas[7]

      }
    ],
  }
  chartClear(chart9)
  chart9.setOption(option2);
}

//APP 用户功能使用次数接口调用
function getAppModuleCounts(){
  var url = '/index.php/Api/Counts/getAppModuleCounts';
  fget(url,'',function(res){
    if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
    g_loadingIndex = -1;
    if(res.status==1){
      var TeaTrack = res.data.TeaTrack;
      var TeaHomework = res.data.TeaHomework;
      var TeaLeave = res.data.TeaLeave;
      var ParTrack = res.data.ParTrack;
      var ParHomework = res.data.ParHomework;
      var ParLeave = res.data.ParLeave;
      var ParStuCard = res.data.ParStuCard;
      var BannerClick = res.data.BannerClick;
      var BannerView = res.data.BannerView;
      var appModuleCountsArr = [TeaTrack,TeaHomework,TeaLeave,ParTrack,ParHomework,ParLeave,ParStuCard,BannerClick,BannerView];
      appFunCountDP(appModuleCountsArr);    
    }else{
      dialog.error(res.info);
    }
  })
}   
getAppModuleCounts();

//APP 用户功能使用次数数据处理
function appFunCountDP(datas){
  var appFunCount = [];
  for (var i = 0; i < datas.length; i++) {
  var appFunCountKey = [];
  var appFunCountValue = [];
    for(var key in datas[i]){
      appFunCountKey.push(key);
      appFunCountValue.push(datas[i][key]);
    }
   appFunCount.push(appFunCountKey,appFunCountValue)
  };
  appFunChart(appFunCount);
}

// APP 用户功能使用次数统计(折线图)
var chart10 = echarts.init(document.getElementById('chart10'));
var chart11 = echarts.init(document.getElementById('chart11'));
var chart13 = echarts.init(document.getElementById('chart13'));
function appFunChart(datas){
  option1 ={
    color: ['#8DE160','#d71b3c','#E1955E'],
    tooltip : {
      trigger: 'axis',
      // formatter: "{b}<br/>{a0}{c0}<br/>{a1}{c1} %"
    },
    legend: {
      data:['查看位置','发布作业','处理审批'],
      right:'5%',
      textStyle: {
        color:'#fff'
      }
    },
    calculable : false,
    xAxis : [
      {
        type : 'category',
        axisTick: {show: false},
        name : '时段',
        nameTextStyle: {
          color : '#fff'
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
          color:'#83ECF3'
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
        color:'#fff'
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
        name:'查看位置',
        type:'line',
        smooth:true,
        data:  datas[1]

      },
      {
        name:'发布作业',
        type:'line',
        smooth:true,
        data: datas[3]
      },
      {
        name:'处理审批',
        type:'line',
        smooth:true,
        data: datas[5]
      }
    ],
  }
  chartClear(chart10);
  chart10.setOption(option1);
  option2 = {
    color: ['#8DE160','#d71b3c','#ce864c','#38AEFF'],
    tooltip : {
      trigger: 'axis',
      // formatter: "{b}<br/>{a0}{c0}<br/>{a1}{c1} %"
    },
    legend: {
      data:['查看位置','查看作业','发布审批','设置学生卡'],
      right:'5%',
      textStyle: {
        color:'#fff'
      }
    },
    calculable : false,
    xAxis : [
      {
        type : 'category',
        axisTick: {show: false},
        name : '时段',
        nameTextStyle: {
          color : '#fff'
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
          color:'#83ECF3'
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
        color:'#fff'
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
        name:'查看位置',
        type:'line',
        smooth:true,
        data: datas[7]
      },
      {
        name:'查看作业',
        type:'line',
        smooth:true,
        data: datas[9]
      },
      {
        name:'发布审批',
        type:'line',
        smooth:true,
        data: datas[11]
      },
      {
        name:'设置学生卡',
        type:'line',
        smooth:true,
        data: datas[13]
      }
    ],
  }
  chartClear(chart11);
  chart11.setOption(option2);
  option3 = {
    color: ['#8DE160','#d71b3c'],
    tooltip : {
      trigger: 'axis',
      // formatter: "{b}<br/>{a0}{c0}<br/>{a1}{c1} %"
    },
    legend: {
      data:['点击数量','展示数量'],
      right:'5%',
      textStyle: {
        color:'#fff'
      }
    },
    calculable : false,
    xAxis : [
      {
        type : 'category',
        axisTick: {show: false},
        name : '时段',
        nameTextStyle: {
          color : '#fff'
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
          color:'#83ECF3'
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
        color:'#fff'
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
        name:'点击数量',
        type:'line',
        smooth:true,
        data: datas[15]
      },
      {
        name:'展示数量',
        type:'line',
        smooth:true,
        data: datas[17]
      }
    ],
  }
  chartClear(chart13);
  chart13.setOption(option3);
}

// 老师和家长沟通次数接口调用
function getChatCounts(){
  var url = '/index.php/Api/Counts/getChatCounts';
  fget(url,'',function(res){
    if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
    g_loadingIndex = -1;
    if(res.status==1){
      var chatCounts = res.data;
      chatCountsDP(chatCounts);    
    }else{
      dialog.error(res.info);
    }
  })
}   
getChatCounts();

// 老师和家长沟通次数数据处理
function chatCountsDP(data){
  var chatCounts = [];
  var chatCountsKey = [];
  var chatCountsValue = [];
  for(var key in data){
    chatCountsKey.push(key);
    chatCountsValue.push(data[key]);
  }
  chatCounts.push(chatCountsKey,chatCountsValue)
  chatCountsChart(chatCounts);
}

// 老师和家长沟通次数统计(折线图)
var chart12 = echarts.init(document.getElementById('chart12'));
function chatCountsChart(datas){
  option ={
    color: ['#d71b3c'],
    tooltip : {
      trigger: 'axis',
      // formatter: "{b}<br/>{a0}{c0}<br/>{a1}{c1} %"
    },
    calculable : false,
    xAxis : [
      {
        type : 'category',
        axisTick: {show: false},
        name : '时段',
        nameTextStyle: {
          color : '#fff'
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
          color:'#83ECF3'
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
        color:'#fff'
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
        name:'沟通次数',
        type:'line',
        smooth:true,  
        data:  datas[1]
      }
    ],
  }
  chartClear(chart12);
  chart12.setOption(option);
}

// // 定位热点图
// var map = new BMap.Map("monitorMap");
// map.enableScrollWheelZoom(true); 
// var centrePoint = new BMap.Point(116.490237, 38.51337);// ,默认中心点
// function heatmapOverlay(){
//   var heatmapOverlay = new BMapLib.HeatmapOverlay({"radius":20});
//   map.centerAndZoom(centrePoint, 5); 
//   map.clearOverlays();
//   var pointData =[];
//   var url = '/index.php/Api/Counts/TrailCount';
//   fget(url,'',function(res){
//     if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
//     g_loadingIndex = -1;
//     if(res.status==1){
//       pointData = res.data; 
//       map.addOverlay(heatmapOverlay);
//       heatmapOverlay.setDataSet({data:pointData,max:100});
//     }else{
//       dialog.error(res.info);
//     }
//   })
// }

// 学生卡正在消费数量接口调用及数据处理
function stuCardBillCounts(){
  var url = '/index.php/Api/Counts/stuCardBillCounts';
  fget(url,'',function(res){
    if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
    g_loadingIndex = -1;
    if(res.status==1){
      var stuCardBillInfo = res.data;
      stuCardBillInfoDP(stuCardBillInfo); 
    }else{
      dialog.error(res.info);
    }
  })
}   
stuCardBillCounts();

// 学生卡正在消费数量及数据处理
function stuCardBillInfoDP(data){
  var stuCardBillInfoCounts = [];
  var stuCardBillInfoCountsKey = [];
  var stuCardBillInfoCountsValue = [];
  for(var key in data){
    stuCardBillInfoCountsKey.push(key);
    stuCardBillInfoCountsValue.push(data[key]);
  }
  stuCardBillInfoCounts.push(stuCardBillInfoCountsKey,stuCardBillInfoCountsValue)
  stuCardBillChart(stuCardBillInfoCounts);
}
// 学生卡正在消费数量统计(折线图)
var chart14 = echarts.init(document.getElementById('chart14'));
function stuCardBillChart(datas){
  option ={
    color: ['#d71b3c'],
    tooltip : {
      trigger: 'axis',
      // formatter: "{b}<br/>{a0}{c0}<br/>{a1}{c1} %"
    },
    calculable : false,
    xAxis : [
      {
        type : 'category',
        axisTick: {show: false},
        name : '时段',
        nameTextStyle: {
          color : '#fff'
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
          color:'#83ECF3'
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
        color:'#fff'
      },
      splitLine: {
        show: true,
        lineStyle: {
          color: ['#0e5370'],
          type: 'dotted'    
        }
      },minInterval: 1
    },
    series : [
      {
        name:'进校数量',
        type:'line',
        smooth:true,  
        data:  datas[1]
      }
    ],
  }
  chartClear(chart14);
  chart14.setOption(option);
}
}

if (user_type != '2') {
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
}