
/******************************************************Ztree 树 管理*************************************************************/
// 教师文件树
var setting = {
    check: {
      enable: false,
      chkStyle: "radio",
      radioType: "all" // 在整棵树范围内当做一个分组
    },
    view: {
      dblClickExpand: false
    },
    data: {
      key: {
        title: "title"
      },
      simpleData: {
        enable: true
      }
    },
    callback: {
        beforeExpand : beforeExpand,
        onExpand : onExpand,
        onClick : onClick,
        onDblClick : onDblClick,
        beforeDrag : beforeDrag,
        beforeDrop : beforeDrop
    }
};
// var setting = {
//         view : {
//             showLine : false,
//         },
//         edit : {
//             enable : true,
//             showRemoveBtn : false,
//             showRenameBtn : false
//         },
//         data : {
//             simpleData : {
//                 enable : true
//             }
//         },
//         callback : {
//             beforeExpand : beforeExpand,
//             onExpand : onExpand,
//             onClick : onClick,
//             onDblClick : onDblClick,
//             beforeDrag : beforeDrag,
//             beforeDrop : beforeDrop
//         }
// };



var arr = new Array();
var tree1, tree2;
var zNodes = "";

var tempData="";

tree1 = $.fn.zTree.init($("#treeDemo01"), setting, zNodes);
tree2 = $.fn.zTree.init($("#chosen-list"), setting);

$(document).ready(function() {
    // $.ajax({
    //     type : "get",
    //     dataType : 'json',
    //     url : '/index.php/Admin/School/Teacher/getTreeList',
    //     success : function(ret) {
    //         zNodes = ret.data.data;
    //         tree1 = $.fn.zTree.init($("#treeDemo01"),setting, zNodes);
    //         $("#all-number").text(allNodeFilter());
    //     },
    //     error: function(msg) {
    //       dialog.error("请求服务器异常！");
    //     }
    // });

    
    // $.ajax({
    //     type : "get",
    //     dataType : 'json',
    //     url : '/index.php/Admin/School/Teacher/get_teacher_course?t_id=' + $('#schoolteachingformWindow #t_id').val(),
    //     success : function(ret) {
    //         cNodes = ret.data;
    //         tree2 = $.fn.zTree.init($("#chosen-list"),setting, cNodes);
    //         $("#chosen-number").text(tree2.getNodes().length);
    //     },
    //     error: function(msg) {
    //       dialog.error("请求服务器异常！");
    //     }
    // });



    // key = $("#key");
    // key.bind("focus", focusKey).bind("blur", blurKey).bind("propertychange", searchNode).bind("input",searchNode);
    // $("#name").bind("change", clickRadio);

    // $("#all-number").text(allNodeFilter());

    //添加节点
    $("#chosen-all-node,#chosen-node").bind("click",function() {
        var nodes;
        if ($(this).attr("id") == "chosen-all-node") {
            //获取全部节点
            nodes = tree1.getNodes();
        } else {
            nodes = tree1.getSelectedNodes(); //获取所有选择的节点
        }
        forNodes(nodes);
    });

    //移除选择节点 
    $("#remove-node").bind("click", function() {
        var nodes2 = tree2.getSelectedNodes(); //获取所有选择的节点
        for (var rIndex = 0; rIndex < nodes2.length; rIndex++) {
            tree2.removeNode(nodes2[rIndex]);

            // 删除对应隐藏域
            $('#chosenListForm .tab_'+nodes2[rIndex].istatue).remove();
        }
        $("#chosen-number").text(tree2.getNodes().length);
    });

    //移除全部节点
    $("#delete-all,#remove-all-node").bind("click", function() {
        var nodes_all = tree2.getNodes(); //获取所有选择的节点
        var len = nodes_all.length;
        for (var aIndex = 0; aIndex < len; aIndex++) {
            tree2.removeNode(nodes_all[0]);
        }
        $("#chosen-number").text(tree2.getNodes().length);

        // 清空隐藏域
        $('#chosenListForm').html('');
    });

    //滚动条
    $("#schoolteachingformWindow .tree-scroll, .chosen-scroll").mCustomScrollbar({
        setHeight : 277,    
        scrollButtons : {
            enable : true
        },
        theme : "dark-3"
    });
});

function beforeDrag(treeId, treeNodes) {
    for (var i = 0, l = treeNodes.length; i < l; i++) {
        if (treeNodes[i].drag === false) {
            return false;
        }
    }
    return true;
}
function beforeDrop(treeId, treeNodes, targetNode, moveType) {
    return targetNode ? targetNode.drop !== false : true;
}

// function focusKey(e) {
//     if (key.hasClass("empty")) {
//         key.removeClass("empty");
//     }
// }
// function blurKey(e) {
//     if (key.get(0).value === "") {
//         key.addClass("empty");
//     }
// }
var lastValue = "", nodeList = [], fontCss = {};
// function clickRadio(e) {
//     lastValue = "";
//     searchNode(e);
// }
//解决IE9按键盘按Backspace键无法搜索
// document.onkeyup=function(event){
//     var e = event || window.event || arguments.callee.caller.arguments[0];
//     if(e && e.keyCode==8){ 
//         searchNode(e);
//     }
// }; 

// function searchNode(e) {
//     var zTree = $.fn.zTree.getZTreeObj("treeDemo01");
//     if (!$("#getNodesByFilter").attr("checked")) {
//         var value = $.trim(key.get(0).value);
//         var keyType = "";
//         if ($("#name").attr("checked")) {
//             keyType = "name";
//         } else if ($("#level").attr("checked")) {
//             keyType = "level";
//             value = parseInt(value);
//         } else if ($("#id").attr("checked")) {
//             keyType = "id";
//             value = parseInt(value);
//         }
//         if (key.hasClass("empty")) {
//             value = "";
//         }
//         if (lastValue === value) return;
//         lastValue = value;
//         if (value === "") {
//             zTree.showNodes(zTree.transformToArray(zTree.getNodes())) ;
//             return;
//         }

//         if ($("#getNodeByParam").attr("checked")) {
//             var node = zTree.getNodeByParam(keyType, value);
//             if (node === null) {
//                 nodeList = [];
//             } else {
//                 nodeList = [node];
//             }
//         } else if ($("#getNodesByParam").attr("checked")) {
//             nodeList = zTree.getNodesByParam(keyType, value);
//         } else if ($("#getNodesByParamFuzzy").attr("checked")) {
//             nodeList = zTree.getNodesByParamFuzzy(keyType, value);
//         }
//     } else {
//         updateNodes(false);
//         nodeList = zTree.getNodesByFilter(filter);
//     }
//     nodeList = zTree.transformToArray(nodeList);
//     updateNodes(true);

// }
function updateNodes(highlight) {
    var zTree = $.fn.zTree.getZTreeObj("treeDemo01");
    var allNode = zTree.transformToArray(zTree.getNodes());
    zTree.hideNodes(allNode);
    for ( var n in nodeList) {
        findParent(zTree, nodeList[n]);
    }
    zTree.showNodes(nodeList);
}

function findParent(zTree, node) {
    zTree.expandNode(node, true, false, false);
    var pNode = node.getParentNode();
    if (pNode != null) {
        nodeList.push(pNode);
        findParent(zTree, pNode);
    }

}

function getFontCss(treeId, treeNode) {
    return (!!treeNode.highlight) ? {
        color : "#A60000",
        "font-weight" : "bold"
    } : {
        color : "#333",
        "font-weight" : "normal"
    };
}
function filter(node) {
    return !node.isParent && node.isFirstNode;
}

var key;

var curExpandNode = null;
/**
 * 实时加载选择树形
 * @param treeId 
 * @param treeNode
 */
function beforeExpand(treeId, treeNode) {
    var pNode = curExpandNode ? curExpandNode.getParentNode() : null;
    var treeNodeP = treeNode.parentTId ? treeNode.getParentNode(): null;
    var zTree = $.fn.zTree.getZTreeObj("treeDemo01");
    for (var i = 0, l = !treeNodeP ? 0 : treeNodeP.children.length; i < l; i++) {
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
/**
 * 初始数据
 * @param newNode
 */
function singlePath(newNode) {
    if (newNode === curExpandNode)
        return;
    if (curExpandNode && curExpandNode.open == true) {
        var zTree = $.fn.zTree.getZTreeObj("treeDemo01");
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
            if (newParents != null) {
                var oldNode = curExpandNode;
                var oldParents = [];
                while (oldNode) {
                    oldNode = oldNode.getParentNode();
                    if (oldNode) {
                        oldParents.push(oldNode);
                    }
                }
                if (newParents.length > 0) {
                    zTree.expandNode(oldParents[Math.abs(oldParents.length- newParents.length) - 1], false);
                } else {
                    zTree.expandNode(oldParents[oldParents.length - 1],false);
                }
            }
        }
    }
    curExpandNode = newNode;
}

function onExpand(event, treeId, treeNode) {
    curExpandNode = treeNode;
}
/**
 * 单击选中节点
 *      明确其上级以及根节点
 * @param e
 * @param treeId
 * @param treeNode
 */
function onClick(e, treeId, treeNode) {
    var zTree = $.fn.zTree.getZTreeObj("treeDemo01");
    zTree.expandNode(treeNode, null, null, null, true);
    var nodes = zTree.getSelectedNodes();

    var zTree2 = $.fn.zTree.getZTreeObj("chosen-list");
    zTree2.expandNode(treeNode, null, null, null, true);

    var nodes2 = zTree.getSelectedNodes();
}

/**
 * 双击选择节点，并分配
 * @param e
 * @param treeId
 * @param treeNode
 */
function onDblClick(e, treeId, treeNode) {

    if(treeNode.pId==null){  //选择根节点   ~ 校区 / 校区
        layer.msg('请选择班级下对应的课程', {icon: 5});
    }else{
        var zTree = $.fn.zTree.getZTreeObj("treeDemo01");
        zTree.expandNode(treeNode, null, null, null, true);
        var nodes = zTree.getSelectedNodes();

        var zTree2 = $.fn.zTree.getZTreeObj("chosen-list");
        zTree2.expandNode(treeNode, null, null, null, true);
        var nodes2 = zTree.getSelectedNodes();

        forNodes(nodes);
    }
}

function showIconForTree(treeId, treeNode) {
    return !treeNode.isParent;
};

function nodeFilter(node) {
    return !node.isParent;
}

/**
 * 统计数量
 * @returns {Number}
 */
function allNodeFilter() {
    var i = 0, a = 0, all_nodes = tree1.getNodes();
    for (var allIndex = 0; allIndex < all_nodes.length; allIndex++) {
        if (all_nodes[allIndex].isParent) {
            var all_children = tree1.getNodesByFilter(nodeFilter,
                    false, all_nodes[allIndex]);
            for (var dIndex = 0; dIndex < all_children.length; dIndex++) {
                i++;
            }
        } else {
            a++;
        }
    }
    return i + a;
}
/**
 * 折叠当前节点下的子节点
 *       显示新增节点数据
 *       初始化所选择的课程/课程IDS
 * @param nodes
 */
function forNodes(nodes) {
    for (var pIndex = 0; pIndex < nodes.length; pIndex++) {
        if (nodes[pIndex].isParent) {   //选择根节点
            var children = tree1.getNodesByFilter(nodeFilter, false,nodes[pIndex]);
            for (var cIndex = 0; cIndex < children.length; cIndex++) {
                if (!tree2.getNodeByParam("istatue", children[cIndex].istatue,null)){
                    var cnodes=children[cIndex].getParentNode();
                    children[cIndex].name=children[cIndex].showName;
                    tree2.addNodes(null, children[cIndex]);
                }
            }
        } else {            //子节点
            if (!tree2.getNodeByParam("istatue", nodes[pIndex].istatue, null)){
                var pnodes=nodes[pIndex].getParentNode();
                if(nodes[pIndex].showName!=undefined){
                    nodes[pIndex].name=nodes[pIndex].showName;
                    tree2.addNodes(null, nodes[pIndex]);
                }
            }
        }
        $("#chosen-number").text(tree2.getNodes().length);
    }

    // 生成隐藏域id
    var tree2Node = tree2.getNodes();
    // 先清空隐藏域
    $('#chosenListForm').html('');
    for (var i = 0; i < tree2Node.length; i++) {
        var ids   = tree2Node[i].istatue;
        var aId   = ids.split('-');
        var input = '<input class="tab_'+ids+'" type="hidden" id="" name="g_c_crs_id[]" value="'+aId+'" />';
        $('#chosenListForm').append(input);
    };
}

/***
 * 数据编辑
 */
// function saveForm(){
//     var treeObj = $.fn.zTree.getZTreeObj("chosen-list");
//     var nodes = treeObj.getNodes();

//     for (var i = 0; i < nodes.length; i++){
//         tempData +=nodes[i].gradesId+","+nodes[i].classesId+","+nodes[i].id+"#";
//     }
//     $("#tempData").val(tempData);

//     if($("#tempData")!=""){
//         layer.confirm('确定要保存授课信息', {
//             btn: ['确定','取消'],
//             shade: false 
//         }, function(){
//             $.post(WEB_ROOT + "/teaching/insert?="+ new Date().valueOf(), $("#myForm").serialize(),function(returnData){
//                 if (returnData){
//                     //刷新列表
//                     parent.frames["main-iframe"].window.initPageList(); 
//                     //关闭窗体
//                     var index = parent.layer.getFrameIndex(window.name); 
//                     parent.layer.close(index);
//                 }
//             });
//         });
//     }else{
//         layer.msg('请选择对应的的课程', {icon: 8});
//     }
// }