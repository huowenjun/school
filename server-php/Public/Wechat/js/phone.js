/**
 * 
 * @authors LJS (1114510520@qq.com)
 * @date    2017-02-08 13:21:16
 * @version $Id$
 */

// 返回上一页
function back() {
  history.back();
  history.go(-1);
}

// input清空文本框
$('.weui_cells_form').delegate('.custom_icon_clear', 'click', function() {
  $(this).parents('.weui_cell').find('.weui_input').val('');
});


