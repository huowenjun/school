

// $(function() {
//   $.ajax({
//     url: 'http://cloud.com/index.php/audit/index/maclog',
//     type: 'GET',
//     dataType: 'json',
//     success: function (tt) { 
//         var json = eval(tt).data.eimdata; 
//         console.log(json);
//         var arr=[]
//         $.each(json, function (index) {
//             console.log(json[index]) 
//             var arr1 = [];
//             $.each(json[index], function (keys, vals) {
//                 var a1 = json[index][keys];
//                 console.log(a1) 
//                 arr1.push(a1)
//             });
//            arr.push(arr1);
//         });
//          console.log(arr)
//     }
// }); 

// });


function createNode(){
  var root = {
    "id" : "0",
    "text" : "root",
    "value" : "86",
    "showcheck" : true,
    complete : true,
    "isexpand" : true,
    "checkstate" : 0,
    "hasChildren" : true
  };


  
  var arr = [];
  for(var i= 1;i<100; i++){
    var subarr = [];
    for(var j=1;j<100;j++){
      var value = "node-" + i + "-" + j; 
      subarr.push( {
         "id" : value,
         "text" : value,
         "value" : value,
         "showcheck" : true,
         complete : true,
         "isexpand" : false,
         "checkstate" : 0,
         "hasChildren" : false
      });
    }


    
    arr.push( {
      "id" : "node-" + i,
      "text" : "node-" + i,
      "value" : "node-" + i,
      "showcheck" : true,
      complete : true,
      "isexpand" : false,
      "checkstate" : 0,
      "hasChildren" : true,
      "ChildNodes" : subarr
    });
  }
  root["ChildNodes"] = arr;
  return root; 
}

treedata = [createNode()];
