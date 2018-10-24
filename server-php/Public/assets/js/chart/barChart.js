var barChartData = {

  labels : ["January","February","March","April","May","June","July"],
  datasets : [
    {
      fillColor : "rgba(106, 218, 228, 0.8)",
      data : [65,59,90,81,56,55,40]
    },
    {
      fillColor : "rgba(52, 152, 219, 0.8)",
      data : [28,48,40,19,96,27,100]
    }
  ]
}

// var myLine = new Chart(document.getElementById("canvas1").getContext("2d")).Bar(barChartData,{
//   scaleShowLabels : false,
//   pointLabelFontSize : 24
// });

// var myLine = new Chart(document.getElementById("canvas2").getContext("2d")).Bar(barChartData,{
//   scaleShowLabels : false,
//   pointLabelFontSize : 24
// });

// var myLine = new Chart(document.getElementById("canvas3").getContext("2d")).Bar(barChartData,{
//   scaleShowLabels : false,
//   pointLabelFontSize : 24
// });

// var myLine = new Chart(document.getElementById("canvas4").getContext("2d")).Bar(barChartData,{
//   scaleShowLabels : false,
//   pointLabelFontSize : 24
// });

// 场所采集排行
function fnplaceCollectRank(aTit,aVal) {
  var barChartData = {
    labels : aTit,
    datasets : [
      // {
      //   fillColor : "rgba(52, 152, 219, 0.8)",
      //   data : [28,48,40,19,96,27,100]
      // },
      {
        fillColor : "rgba(106, 218, 228, 0.8)",
        data : aVal
      }
    ]
  }
  var myLine = new Chart(document.getElementById("place_collect_rank").getContext("2d")).Bar(barChartData,{
    scaleShowLabels : false,
    pointLabelFontSize : 24
  });
}

// 接入设备厂商
function fnaccessDeviceManufacturer(aTit,aVal) {
  var barChartData = {
    labels : aTit,
    datasets : [
      {
        fillColor : "rgba(106, 218, 228, 0.8)",
        data : aVal
      }
    ]
  }
  var myLine = new Chart(document.getElementById("access_device_manufacturer").getContext("2d")).Bar(barChartData,{
    scaleShowLabels : false,
    pointLabelFontSize : 24
  });
}

// 设备采集排行
function fndeviceCollectRank(aTit,aVal) {
  var barChartData = {
    labels : aTit,
    datasets : [
      {
        fillColor : "rgba(106, 218, 228, 0.8)",
        data : aVal
      }
    ]
  }
  var myLine = new Chart(document.getElementById("device_collect_rank").getContext("2d")).Bar(barChartData,{
    scaleShowLabels : false,
    pointLabelFontSize : 24
  });
}

// 告警数量排行
function fnwarnSumRank(aTit,aVal) {
  var barChartData = {
    labels : aTit,
    datasets : [
      {
        fillColor : "rgba(106, 218, 228, 0.8)",
        data : aVal
      }
    ]
  }
  var myLine = new Chart(document.getElementById("warn_sum_rank").getContext("2d")).Bar(barChartData,{
    scaleShowLabels : false,
    pointLabelFontSize : 24
  });
}

// 安全厂商在线率统计
function fnsecurityOnlineSum(aTit,aVal) {
  var barChartData = {
    labels : aTit,
    datasets : [
      {
        fillColor : "rgba(106, 218, 228, 0.8)",
        data : aVal
      }
    ]
  }
  var myLine = new Chart(document.getElementById("security_online_sum").getContext("2d")).Bar(barChartData,{
    scaleShowLabels : false,
    pointLabelFontSize : 24
  });
}


























