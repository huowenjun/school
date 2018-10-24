var uploadSubmit = $(".upload-submit");
var checkInfo = $(".check-info");
var instruSize = $(".instru-size");
function imgUpload(ele) {

  var eleList = document.querySelectorAll(ele);
  eleList[0].innerHTML ='<div id="img-container" >'+
  '<div class="img-up-add  img-item"> <span class="img-add-icon">+</span> </div>'+
  '<input type="file" name="files" id="imgFileInput" multiple>'+
  '</div>';
   var addBtn = document.querySelector('.img-up-add');
    addBtn.addEventListener('click',function () {
      document.querySelector('#imgFileInput').value = null;
      document.querySelector('#imgFileInput').click();
      return false;
    },false)
  ele = eleList[0].querySelector('#img-container');
  ele.addEventListener('click', removeImg, false);       
  ele.files = [];   // 当前上传的文件数组
    
  // 监控审核状态
  $.ajax({
     type: "get",
     url: "/index.php/Wap/WeFinancial/fileUploadAuditing",
     dataType : 'json', 
     success: function(res){
      if(res.data!=null){
        if(res.data.status==0){
          uploadSubmit.attr('onclick', 'return false').css('opacity', '0.4').html("正在审核中");
          checkInfo.html(res.data.reply);
          var applyImages =res.data.apply_images;
          var arrImages=applyImages.split(",")
          eleList[0].innerHTML ='<div id="img-container" ></div>';
          var imgContainer = $("#img-container");
          var imgItem ="";
          for(var i=0; i<arrImages.length; i++){
            imgItem+='<div class="img-thumb img-item"><img class="thumb-icon" src="'+arrImages[i]+'"></div>';
            imgContainer.html(imgItem);
          }
          $(".img-item").css('opacity', '0.4');
        }else if(res.data.status==2){
          uploadSubmit.css('opacity', '1').html("提交申请");
          checkInfo.html(res.data.reply);
        }
      }
    }
  })
                
  //处理input选择的图片 预览图片
  function handleFileSelect(evt) {
    checkInfo.html("");
    instruSize.html("");
    var files = evt.target.files;
    for(var i=0, f; f=files[i];i++){
      // 过滤掉非图片类型文件
      if(!f.type.match('image.*')){
          continue;
      }
      if(f.size>2097152){
        instruSize.html("上传图片不能大于2M!");
        continue;
      }

      // 过滤掉重复上传的图片
      var tip = false;
      for(var j=0; j<(ele.files).length; j++){
        if((ele.files)[j].name == f.name){
          tip = true;
          dialogHint.hint("图片不可重复上传！");
          break;
        }
      }
      if(!tip){
        // 图片文件绑定到容器元素上
        ele.files.push(f);
        var reader = new FileReader();
        reader.onload =  function (e) {
          var oDiv = document.createElement('div');
          oDiv.className = 'img-thumb img-item';
          // 向图片容器里添加元素
          oDiv.innerHTML = '<img class="thumb-icon" src="'+e.target.result+'" />'+'<a href="javscript:;" class="img-remove">x</a>'
          ele.insertBefore(oDiv, addBtn);
        };
        reader.readAsDataURL(f);
      }
    }
  }
  document.querySelector('#imgFileInput').addEventListener('change', handleFileSelect, false);

  // 删除图片
  function removeImg(evt) {
    if(evt.target.className.match(/img-remove/)){
      // 获取删除的节点的索引
      function getIndex(ele){
        if(ele && ele.nodeType && ele.nodeType == 1) {
          var oParent = ele.parentNode;
          var oChilds = oParent.children;
          for(var i = 0; i < oChilds.length; i++){
              if(oChilds[i] == ele)
                  return i;
          }
        }else {
          return -1;
        }
      }
      // 根据索引删除指定的文件对象
      var index = getIndex(evt.target.parentNode);
      ele.removeChild(evt.target.parentNode);
      if(index < 0){
        return;
      }else {
        ele.files.splice(index, 1);
      }
    }
  }
    
    // 上传图片
  function uploadImg() {
    if($(".img-thumb").length == 0){
      dialogHint.hint("你还没有上传图片信息，请上传图片成功后再提交申请！");
      return;
    }

    var formData = new FormData(); // 实例化formData对象
    for(var i=0, f; f=ele.files[i]; i++){ 
      formData.append('files[]', f); // 往formData对象里加入键值对
    }
    
   
    // 上传文件
    function fileUploadAuditing(data){
      $.ajax({
         type: "post",
         data:{apply_images:data},
         url: "/index.php/Wap/WeFinancial/fileUploadAuditing",
         dataType : 'json', 
         success: function(res){
            uploadSubmit.attr('onclick', 'return false').css('opacity', '0.4').html("提交申请");
            checkInfo.html(res.data.reply);
            var applyImages =res.data.apply_images;
            var arrImages=applyImages.split(",")
            eleList[0].innerHTML ='<div id="img-container" ></div>';
            var imgContainer = $("#img-container");
            var imgItem ="";
            for(var i=0; i<arrImages.length; i++){
              imgItem+='<div class="img-thumb img-item"><img class="thumb-icon" src="'+arrImages[i]+'"></div>';
              imgContainer.html(imgItem);
            }
            $(".img-item").css('opacity', '0.4');

          },
          
          error: function(res){
            dialogHint.hint("请求服务器异常！");
        }
      })
    }  
    // 上传数据
    function uploadImages(){
      $.ajax({
        url: "/index.php/Admin/Upload/upload_images.html",
        type: "POST",
        data: formData,
        processData: false,  // 告诉jQuery不要去处理发送的数据
        contentType: false,   // 告诉jQuery不要去设置Content-Type请求头
        success: function(res){
          var arrUrl =res.data.url;
          var strUrl=arrUrl.join(',');
          console.log(strUrl);
          fileUploadAuditing(strUrl)
        },
        beforeSend: function (){
          g_loadingIndex = dialog.showLoading();
        },
        complete: function (){
          if(g_loadingIndex > -1) dialog.closeLoding(g_loadingIndex);
        },
        error: function(res){
          dialogHint.hint("请求服务器异常！");
        }
      });
    }
    dialogHint.hint("您提交成功后，审核成功会为你下发二维码信息，确认提交申请？",uploadImages);
    
  }
  return uploadImg;
}

