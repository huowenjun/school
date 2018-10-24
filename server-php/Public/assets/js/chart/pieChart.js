
// 设备在线分析
function fndeviceOnlineAnalysis(online,noline) {
  var data = [
    {
        value: online,
        color:"#F38630"
    },
    {
        value : noline,
        color : "#E0E4CC"
    }
    // {
    //     value : 100,
    //     color : "#69D2E7"
    // }
  ]
  var ctx = document.getElementById("device_online_analysis").getContext("2d");
  var myNewChart = new Chart(ctx).Pie(data);
}










