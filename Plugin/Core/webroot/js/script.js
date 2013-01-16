$(function() {

  $('.user-menu').hover(function() {
    $(this).addClass('hover');
    $(this).find('ul').show();
  }, function() {
    $(this).find('ul').hide();
    $(this).removeClass('hover');
  });

});