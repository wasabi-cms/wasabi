/*!
 * jQuery ScrollParent v0.0.1
 * https://github.com/frankfoerster/scrollparent
 *
 * Copyright
 *  (c) 2013 jQuery Foundation and other contributors
 *  (c) 2013 Frank Förster (http://frankfoerster.com)
 * Licensed under the MIT License
 */
(function($) {

  $.fn.extend({
    scrollParent: function() {
      var scrollParent;
      if (($('html').hasClass('lt-ie9') && (/(static|relative)/).test(this.css("position"))) || (/absolute/).test(this.css("position"))) {
        scrollParent = this.parents().filter(function() {
          return (/(relative|absolute|fixed)/).test($.css(this,"position")) && (/(auto|scroll)/).test($.css(this,"overflow")+$.css(this,"overflow-y")+$.css(this,"overflow-x"));
        }).eq(0);
      } else {
        scrollParent = this.parents().filter(function() {
          return (/(auto|scroll)/).test($.css(this,"overflow")+$.css(this,"overflow-y")+$.css(this,"overflow-x"));
        }).eq(0);
      }

      return ( /fixed/ ).test( this.css( "position") ) || !scrollParent.length ? $( this[ 0 ].ownerDocument || document ) : scrollParent;
    }
  });

})(jQuery);
