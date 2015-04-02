define(['jquery', 'common/views/BaseView', 'common/util/eventify'], function($, BaseView, eventify) {

  /**
   * Default options.
   *
   * @type {{collapsedClass: string, openClass: string, popoutClass: string}}
   */
  var defaults = {
    collapsedClass: 'collapsed',
    openClass: 'open',
    popoutClass: 'popout'
  };

  return BaseView.extend({

    /**
     * The element of this view.
     *
     * @type {string} CSS selector
     */
    el: '#wrapper > aside > .main-nav',

    /**
     * Registered events of this view.
     *
     * @returns {Object}
     */
    events: function() {
      var events = {
        'click > li:has(ul) > a': 'onMainItemClick'
      };

      if (this.isCollapsed) {
        events = $.extend(events, {
          'mouseenter > li': 'onMainItemMouseenter',
          'mouseleave > li': 'onMainItemMouseleave'
        });
      }

      return events;
    },

    /**
     * Determines if the navigation bar is collapsed to the small size (true) or not (false).
     *
     * @type {boolean}
     */
    isCollapsed: false,

    /**
     * Options
     *
     * @param {Object}
     */
    options: {},

    /**
     * Resize timer for window resize events.
     *
     * @type {number}
     */
    resizeTimeout: null,

    /**
     * Initialization of the view.
     *
     * @param {Object=} options
     */
    initialize: function(options) {
      this.options = $.extend({}, defaults, options);
      this.$('> li').filter('.active').prev().addClass('prev-active');
      this.collapseMenu();
      this.listenTo(eventify(window), 'resize', _.bind(this.onResize, this));
    },

    /**
     * Attaches/detaches event handlers if the navigation is collapsed
     * and toggles the .collapsed class.
     *
     * The visuals of the collapsed navigation are done via media queries and not via JS.
     */
    collapseMenu: function() {
      var prevCollapsed = this.isCollapsed;
      this.isCollapsed = (this.$('> li > a > .item-name').first().css('display') === 'none');
      if (this.isCollapsed) {
        this.$el.addClass(this.options.collapsedClass);
        if (this.isCollapsed !== prevCollapsed) {
          this.delegateEvents(this.events());
        }
      } else {
        this.$el.removeClass(this.options.collapsedClass);
        this.delegateEvents(this.events());
      }
    },

    /**
     * Gets the list element of the current event target.
     *
     * @param {Object} event
     * @param {string=} cls
     * @returns {jQuery}
     */
    getEventTarget: function(event, cls) {
      var $target = $(event.target);
      if (!$target.is('li')) {
        cls = cls || '';
        return $target.closest('li' + cls);
      }

      return $target;
    },

    /**
     * onMainItemClick event handler
     * Toggles the .open class on every li that has a child ul.
     *
     * @param {Object} event
     */
    onMainItemClick: function(event) {
      this.getEventTarget(event).toggleClass(this.options.openClass);
    },

    /**
     * onMainItemMouseenter event handler
     * Add .popout class to hovered li if the menu is collapsed.
     *
     * @param {Object} event
     */
    onMainItemMouseenter: function(event) {
      if (!this.isCollapsed) return;
      this.getEventTarget(event).addClass(this.options.popoutClass);
    },

    /**
     * onMainItemMouseLeave event handler
     * Remove .popout class from the previously hovered li if the menu is collapsed.
     *
     * @param {Object} event
     */
    onMainItemMouseleave: function(event) {
      if (!this.isCollapsed) return;
      this.getEventTarget(event, '.' + this.options.popoutClass).removeClass(this.options.popoutClass);
    },

    /**
     * onWindowResize event handler
     * Calls collapseMenu on window resize.
     */
    onResize: function() {
      clearTimeout(this.resizeTimeout);
      this.resizeTimeout = setTimeout(_.bind(this.collapseMenu, this), 100);
    }

  });
});
