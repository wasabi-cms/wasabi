define(['jquery', 'Underscore', 'Backbone', 'Spinner'], function($, _, Backbone, Spinner) {

  return Backbone.View.extend({

    $blockBackdrop: $('<div class="block-backdrop"></div>'),
    numberOfBlocks: 0,
    spinner: null,

    delegateEvents: function(events) {
      var event, handler, results;

      Backbone.View.prototype.delegateEvents.call(this, events);

      this.globalEvents = this.globalEvents || {};

      if (typeof this.globalEvents === 'function') {
        this.globalEvents = this.globalEvents();
      }

      results = [];

      for (event in this.globalEvents) {
        if (!this.globalEvents.hasOwnProperty(event)) {
          continue;
        }
        handler = this.globalEvents[event];
        results.push(this.eventBus.bind(event, _.bind(this[handler], this)));
      }

      return results;
    },

    /**
     * Block a view by:
     * - overlaying a div with a specified color and opacity
     * - showing a spinner in the center of this div
     * - catching all user generated events on the blocked element
     *
     * @param options
     */
    blockThis: function(options) {
      this.numberOfBlocks++;
      var offset = this.$el.offset();
      var borderLeft = this.$el.css('borderLeftWidth');
      var borderTop = this.$el.css('borderTopWidth');
      var width = this.$el.innerWidth() + (options.deltaWidth || 0);
      var height = this.$el.innerHeight() + (options.deltaHeight || 0);

      if (borderLeft !== '') {
        borderLeft = parseInt(borderLeft.split('px')[0]);
        offset.left += borderLeft;
      }

      if (borderTop !== '') {
        borderTop = parseInt(borderTop.split('px')[0]);
        offset.top += borderTop;
      }

      this.$blockBackdrop.css({
        position: 'absolute',
        top: offset.top,
        left: offset.left,
        width: width,
        height: height,
        backgroundColor: options.backgroundColor || 'transparent',
        opacity: options.opacity || 0.6,
        cursor: options.cursor || 'wait',
        zIndex: options.zIndex || 9997
      }).hide().appendTo($('body'));

      this.$blockBackdrop.fadeIn(100, $.proxy(function() {
        this.delegateEvents({
          'mousedown': 'handleBlockedEvent',
          'mouseup': 'handleBlockedEvent',
          'keydown': 'handleBlockedEvent',
          'keypress': 'handleBlockedEvent',
          'keyup': 'handleBlockedEvent',
          'touchstart': 'handleBlockedEvent',
          'touchend': 'handleBlockedEvent',
          'touchmove': 'handleBlockedEvent'
        });
        this.spinner = this.spinner || new Spinner(options.spinner || false);
        this.spinner.spin(this.$blockBackdrop.get(0));
      }, this));
    },

    /**
     * Unblock a view.
     *
     * @param callback
     */
    unblockThis: function(callback) {
      if (this.numberOfBlocks >= 1) {
        this.numberOfBlocks--;
      }
      if (this.numberOfBlocks === 0) {
        callback = callback || function() {};
        this.spinner.stop();
        this.$blockBackdrop.remove();
        this.delegateEvents();
        if (typeof callback === 'function') {
          callback.call(this);
        }
      }
    },

    /**
     * Catch all event handler for blocked views.
     *
     * @param event
     */
    handleBlockedEvent: function(event) {
      event.preventDefault();
      event.stopPropagation();
    }

  });

});
