define(['jquery', 'common/views/BaseView'], function($, BaseView) {

  /**
   * Holds a reference to the body.
   *
   * @type {jQuery}
   */
  var $body = $('body');

  /**
   * Default options.
   *
   * @type {{navClosedClass: string}}
   */
  var defaults = {
    navClosedClass: 'nav-closed'
  };

  return BaseView.extend({

    /**
     * The element of this view.
     *
     * @type {string} CSS selector
     */
    el: 'body > header .toggle-nav',

    /**
     * Registered events of this view.
     *
     * @type {Object}
     */
    events: {
      'click': 'toggleNav'
    },

    /**
     * Determines if the navigation bar is collapsed to the small size (true) or not (false).
     *
     * @type {boolean}
     */
    isClosed: false,

    /**
     * Options
     *
     * @param {Object}
     */
    options: {},

    /**
     * Initialization of the view.
     *
     * @param {Object=} options
     */
    initialize: function(options) {
      this.options = $.extend({}, defaults, options);
      this.isClosed = $body.hasClass(this.options.navClosedClass);
    },

    /**
     * toggleNav event handler
     * Toggles navClosedClass on the body to show/hide the navigation.
     */
    toggleNav: function() {
      if (!this.closed) {
        $body.addClass(this.options.navClosedClass);
        this.closed = true;
      } else {
        $body.removeClass(this.options.navClosedClass);
        this.closed = false;
      }
    }

  });
});
