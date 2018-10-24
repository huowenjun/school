var dialog = {
    // 错误弹出层
    error: function(message) {
        layer.open({
            content:message,
            icon:2,
            title : '错误',
        });
    },

    //成功弹出层
    success : function(message,params) {
        layer.open({
            content : message,
            closeBtn :false,
            icon : 1,
            title:'成功',
            yes : function(){
                if (typeof (params) == "object") params.focus();
                if (typeof(params) == "function") params();
                if (params && typeof(params) == "string") window.location.href = params;
            },
        });
    },
    success1 : function(message,params) {
        layer.open({
            content : message,
            closeBtn :false,
            icon : 1,
            title:'成功',
        });
    },
    //成功弹出层
    success2 : function(message,params) {
        layer.open({
            content : message,
            closeBtn :false,
            icon : 1,
            title:'成功',
            yes : function(index){
                if (typeof (params) == "object") params.focus();
                if (typeof(params) == "function") {
                    layer.close(index);
                    params();
                }

                if (params && typeof(params) == "string"){  layer.close(index);}
            },
        });
    },

    // 确认弹出层
    confirm : function(message, params) {
        layer.confirm(message, {
            btn: ['是','否'], //按钮
            icon: 3, title:'提示'
        }, function(index){
            if (typeof (params) == "object") params.focus();
            if (typeof(params) == "function") params();
            if (params && typeof(params) == "string") window.location.href = params;
            layer.close(index);
        });
    },


    //提示层
    notify:function(message,url){
        if(typeof(url) == "undefined")
            layer.msg(message);
        else
            layer.msg(message,function(){window.location.href = url});
    },
    notify1:function(message,url){
        if(typeof(url) == "undefined")
            layer.msg(message);
        else
            layer.msg(message,function(){window.top.location.href = url});
    },

    //loading层
    showLoading:function(){
        var index = layer.load(1, {
            shade: [0.1,'#fff'] //0.1透明度的白色背景
        });
        return index;
    },
    closeLoding:function(index){
        layer.close(index);
    },

    /*弹出层*/
    /*
        参数解释：
        title   标题
        url     请求的url
        id      需要操作的数据id
        w       弹出层宽度（缺省调默认值）
        h       弹出层高度（缺省调默认值）
    */
    fullWindow:function(title,url,w,h){
        if (title == null || title == '') {
            title=false;
        };
        if (url == null || url == '') {
            url="404.html";
        };
        if (w == null || w == '') {
            w=95+'%';
        };
        if (h == null || h == '') {
            h=($(window).height() - 50+'px');
        };
        layer.open({
            type: 2,
            area: [w, h],
            fix: false, //不固定
            maxmin: true,
            shade:0.4,
            title: title,
            content: url
        });
    },
    /*关闭弹出框口*/
    m_close:function(){
        var index = parent.layer.getFrameIndex(window.name);
        parent.layer.close(index);
    },

}