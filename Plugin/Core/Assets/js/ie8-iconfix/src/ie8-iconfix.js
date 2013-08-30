/**
 * Fix for icon fonts not showing on page load in IE8.
 * @see http://stackoverflow.com/a/10557782/719907
 */
(function($, win, doc) {
  $(function() {
    var head = doc.getElementsByTagName('head')[0],
      style = doc.createElement('style');

    style.type = 'text/css';
    style.styleSheet.cssText = ':before,:after{content:none !important';
    head.appendChild(style);
    setTimeout(function() {
      head.removeChild(style);
    }, 0);
  });
})(jQuery, window, document);
