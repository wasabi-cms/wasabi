/** live-editor - v0.0.1 */
(function($) {
  "use strict";

  $(function() {
    var $content = $('#content');
    $content
      .css({
        height: $('body').innerHeight()
      })
      .spin('large');

    var $iframe = $('#live-edit-iframe');
    $iframe.load(function(event) {
      $content
        .spin(false)
        .css('height', '');
      $iframe
        .removeAttr('data-src')
        .show();
      var resizeTimer;
      var onResize = function() {
        $iframe.css('height', $iframe.contents().innerHeight());
      };
      onResize();
      $(window).on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(onResize, 100);
      });
    });

    $iframe.attr('src', $iframe.attr('data-src'));
  });

  var LiveEditor = function() {

  };


})(jQuery);