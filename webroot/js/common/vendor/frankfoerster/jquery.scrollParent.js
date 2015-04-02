/*!
 * jQuery ScrollParent v0.0.1
 * https://github.com/frankfoerster/scrollparent
 *
 * Copyright
 *  (c) 2013 jQuery Foundation and other contributors
 *  (c) 2013 Frank Förster (http://frankfoerster.com)
 * Licensed under the MIT License
 */

!function(t){t.fn.extend({scrollParent:function(){var s;return s=t("html").hasClass("lt-ie9")&&/(static|relative)/.test(this.css("position"))||/absolute/.test(this.css("position"))?this.parents().filter(function(){return/(relative|absolute|fixed)/.test(t.css(this,"position"))&&/(auto|scroll)/.test(t.css(this,"overflow")+t.css(this,"overflow-y")+t.css(this,"overflow-x"))}).eq(0):this.parents().filter(function(){return/(auto|scroll)/.test(t.css(this,"overflow")+t.css(this,"overflow-y")+t.css(this,"overflow-x"))}).eq(0),/fixed/.test(this.css("position"))||!s.length?t(this[0].ownerDocument||document):s}})}(jQuery);