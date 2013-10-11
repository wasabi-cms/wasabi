/*!
 * jQuery blockThis v0.0.1
 */
(function($) {
  "use strict";

  var BlockThis = function(element, options) {
    this.$el = $(element);
    this.settings = $.extend({}, $.fn.block.defaults, options);

    this.$blockBackdrop = $('<div class="block-backdrop"></div>');
    this.numberOfBlocks = 0;

    this._primaryEvents = [];
    this._buildEvents();
  };

  BlockThis.prototype = {
    _buildEvents: function() {
      this._primaryEvents = [
        [$(document), '', [
          ['mousedown mouseup keydown keypress keyup touchstart touchend touchmove', $.proxy(this._handler, this)]
        ]]
      ];
    },

    _handler: function(event) {
      var $el = this.$el;
      if (event.target === this.$blockBackdrop[0] ||
        $(event.target).parents().filter(function() { return ($(this)[0] === $el[0]); }).length > 0
      ) {
        event.preventDefault();
        event.stopPropagation();
      }
    },

    _block: function() {
      this.numberOfBlocks++;
      if (this.numberOfBlocks > 1 && (typeof this.settings.onBlock === 'function')) {
        this.settings.onBlock.call(this);
        return;
      }
      var offset = this.$el.offset();
      var borderLeft = this.$el.css('borderLeftWidth');
      if (borderLeft !== '') {
        borderLeft = parseInt(borderLeft.split('px')[0]);
        offset.left += borderLeft;
      }
      var borderTop = this.$el.css('borderTopWidth');
      if (borderTop !== '') {
        borderTop = parseInt(borderTop.split('px')[0]);
        offset.top += borderTop;
      }
      this.$blockBackdrop
        .css({
          position: 'absolute',
          top: offset.top,
          left: offset.left,
          width: this.$el.innerWidth(),
          height: this.$el.innerHeight(),
          backgroundColor: this.settings.backgroundColor,
          opacity: this.settings.opacity,
          cursor: this.settings.cursor,
          zIndex: 9997
        })
        .hide();
      $('body').append(this.$blockBackdrop);
      this.$blockBackdrop.fadeIn(100, $.proxy(function() {
        this.$blockBackdrop.spin('large');
        $.attachEvents(this._primaryEvents);
        this.settings.onBlock.call(this);
      }, this));
    },

    _unblock: function(callback) {
      if (this.numberOfBlocks >= 1) {
        this.numberOfBlocks--;
      }
      if (this.numberOfBlocks === 0) {
        callback = callback || function() {};
        this.$blockBackdrop.spin(false);
        this.$blockBackdrop.remove();
        $.detachEvents(this._primaryEvents);
        if (typeof callback === 'function') {
          callback.call(this);
        }
      }
    }
  };

  $.fn.block = function(options) {
    return this.each(function() {
      if (!$(this).data('blockThis')) {
        $(this).data('blockThis', new BlockThis(this, options));
      }
      $(this).data('blockThis')._block();
      return $(this);
    });
  };

  $.fn.block.defaults = {
    backgroundColor: '#000',
    opacity: 0.6,
    cursor: 'wait',
    onBlock: function(blockThis) {}
  };

  $.fn.unblock = function(callback) {
    return this.each(function() {
      if (!$(this).data('blockThis')) {
        return $(this);
      } else {
        $(this).data('blockThis')._unblock(callback);
        return $(this);
      }
    });
  }

})(jQuery);