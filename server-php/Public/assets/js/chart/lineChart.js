var lineChartData = {
    labels : ["January","February","March","April","May","June","July"],
    datasets : [
        {
            fillColor : "rgba(49, 195, 166, 0.2)",
            strokeColor : "rgba(49, 195, 166, 1)",
            pointColor : "rgba(49, 195, 166, 1)",
            pointStrokeColor : "#fff",
            data : [65,59,90,81,56,55,40]
        },
        {
            fillColor : "rgba(151,187,205,0.5)",
            strokeColor : "rgba(151,187,205,1)",
            pointColor : "rgba(151,187,205,1)",
            pointStrokeColor : "#fff",
            data : [28,48,40,19,96,27,100]
        }
    ]
}
// var myLine = new Chart(document.getElementById("canvas4").getContext("2d")).Line(lineChartData);

// 终端采集数量
function fnterminalCollectSum(aTit,aVal) {
    var lineChartData = {
        labels : aTit,
        datasets : [
            // {
            //     fillColor : "rgba(151,187,205,0.5)",
            //     strokeColor : "rgba(151,187,205,1)",
            //     pointColor : "rgba(151,187,205,1)",
            //     pointStrokeColor : "#fff",
            //     data : [28,48,40,19,96,27,100]
            // },
            {
                fillColor : "rgba(49, 195, 166, 0.2)",
                strokeColor : "rgba(49, 195, 166, 1)",
                pointColor : "rgba(49, 195, 166, 1)",
                pointStrokeColor : "#fff",
                data : aVal
            }
        ]
    }
    var ctx = document.getElementById("terminal_collect_sum").getContext("2d");
    var myNewChart = new Chart(ctx).Line(lineChartData);
}













