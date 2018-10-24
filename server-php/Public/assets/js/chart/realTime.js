// 终端采集数量
function terminal_collect_sum() {
  function n() {
    if (e.length > 0) e = e.slice(1);
    while (e.length < t) {
      var n = e.length > 0 ? e[e.length - 1] : 50;
      var r = n + Math.random() * 10 - 5;
      if (r < 0) r = 0;
      if (r > 100) r = 100;
      e.push(r)
    }
    var i = [];
    for (var s = 0; s < e.length; ++s) i.push([s, e[s]]);
    return i
  }

  function o() {
    s.setData([n()]);
    s.draw();
    setTimeout(o, r)
  }
  var e = [],
      t = 300;
  var r = 200;
  $("#terminal_collect_sum_update_time").val(r).change(function() {
    var e = $(this).val();
    if (e && !isNaN(+e)) {
      r = +e;
      if (r < 1) r = 1;
      if (r > 2e3) r = 2e3;
      $(this).val("" + r)
    }
  });
  var i = {
    series: {
      shadowSize: 0
    },
    yaxis: {
      min: 0,
      max: 100
    },
    xaxis: {
      min: 0,
      max: 100
    }
  };
  var s = $.plot($("#terminal_collect_sum"), [n()], i);
  o()
}

// 
$(function() {
  function n() {
    if (e.length > 0) e = e.slice(1);
    while (e.length < t) {
      var n = e.length > 0 ? e[e.length - 1] : 50;
      var r = n + Math.random() * 10 - 5;
      if (r < 0) r = 0;
      if (r > 100) r = 100;
      e.push(r)
    }
    var i = [];
    for (var s = 0; s < e.length; ++s) i.push([s, e[s]]);
    return i
  }

  function o() {
    s.setData([n()]);
    s.draw();
    setTimeout(o, r)
  }
  var e = [],
      t = 300;
  var r = 200;
  $("#updateInterval2").val(r).change(function() {
    var e = $(this).val();
    if (e && !isNaN(+e)) {
      r = +e;
      if (r < 1) r = 1;
      if (r > 2e3) r = 2e3;
      $(this).val("" + r)
    }
  });
  var i = {
    series: {
      shadowSize: 0
    },
    yaxis: {
      min: 0,
      max: 100
    },
    xaxis: {
      min: 0,
      max: 100
    }
  };
  var s = $.plot($("#placeholder2"), [n()], i);
  o()
})

// 
$(function() {
  function n() {
    if (e.length > 0) e = e.slice(1);
    while (e.length < t) {
      var n = e.length > 0 ? e[e.length - 1] : 50;
      var r = n + Math.random() * 10 - 5;
      if (r < 0) r = 0;
      if (r > 100) r = 100;
      e.push(r)
    }
    var i = [];
    for (var s = 0; s < e.length; ++s) i.push([s, e[s]]);
    return i
  }

  function o() {
    s.setData([n()]);
    s.draw();
    setTimeout(o, r)
  }
  var e = [],
      t = 300;
  var r = 200;
  $("#updateInterval3").val(r).change(function() {
    var e = $(this).val();
    if (e && !isNaN(+e)) {
      r = +e;
      if (r < 1) r = 1;
      if (r > 2e3) r = 2e3;
      $(this).val("" + r)
    }
  });
  var i = {
    series: {
      shadowSize: 0
    },
    yaxis: {
      min: 0,
      max: 100
    },
    xaxis: {
      min: 0,
      max: 100
    }
  };
  var s = $.plot($("#placeholder3"), [n()], i);
  o()
})

// 
$(function() {
  function n() {
    if (e.length > 0) e = e.slice(1);
    while (e.length < t) {
      var n = e.length > 0 ? e[e.length - 1] : 50;
      var r = n + Math.random() * 10 - 5;
      if (r < 0) r = 0;
      if (r > 100) r = 100;
      e.push(r)
    }
    var i = [];
    for (var s = 0; s < e.length; ++s) i.push([s, e[s]]);
    return i
  }

  function o() {
    s.setData([n()]);
    s.draw();
    setTimeout(o, r)
  }
  var e = [],
      t = 300;
  var r = 200;
  $("#updateInterval4").val(r).change(function() {
    var e = $(this).val();
    if (e && !isNaN(+e)) {
      r = +e;
      if (r < 1) r = 1;
      if (r > 2e3) r = 2e3;
      $(this).val("" + r)
    }
  });
  var i = {
    series: {
      shadowSize: 0
    },
    yaxis: {
      min: 0,
      max: 100
    },
    xaxis: {
      min: 0,
      max: 100
    }
  };
  var s = $.plot($("#placeholder4"), [n()], i);
  o()
})

