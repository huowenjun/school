function zoom(mask, bigimg, smallimg) {
  this.bigimg = bigimg;
  this.smallimg = smallimg;
  this.mask = mask
}
function stopBubbling(e) {
  e = window.event || e;
  if (e.stopPropagation) {
      e.stopPropagation();      //阻止事件 冒泡传播
  } else {
      e.cancelBubble = true;   //ie兼容
  }
}
zoom.prototype = {
  init: function() {
    var that = this;
    this.smallimgClick();
    this.maskClick();
    // this.mouseWheel()
  },
  smallimgClick: function() {
      var that = this;
      $("." + that.smallimg).click(function(event) {
      stopBubbling(event) 
      $("." + that.mask).fadeIn();
      $("." + that.bigimg).attr("src", $(this).attr("src")).fadeIn()
       $(".bigimgbox").fadeIn()
       bigImgScroll()
    })
  },
  maskClick: function() {
    var that = this;
    $("." + that.mask).click(function() {
      $("." + that.bigimg).fadeOut();
      $("." + that.mask).fadeOut()
        $(".bigimgbox").fadeOut()
    })
  },
};

      
