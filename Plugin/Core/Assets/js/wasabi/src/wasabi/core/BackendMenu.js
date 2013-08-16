goog.provide('wasabi.core.BackendMenu');

(function($, win) {

  /**
   * BackendMenu Constructor
   *
   * @constructor
   */
  var BackendMenu = function(nav, navToggle) {
    this.$nav = $(nav);
    this.$navToggle = $(navToggle);
    this.$mainItems = this.$nav.find('> li');
    this.$body = $('body');

    this.events = [];
    this.navCollapsedEvents = [];
    this.closed = false;
    this.collapsed = false;
    this.resizeTimer = null;

    this.init();
  };

  /**
   * BackendMenu prototype
   *
   * @type {Function}
   */
  BackendMenu.prototype = (function() {

    /**
     * Build all backend menu events.
     *
     * @private
     */
    function _buildEvents() {
      this.events = [
        [this.$navToggle, {
          click: $.proxy(_onNavToggleClick, this)
        }],
        [this.$mainItems.filter(':has(ul)'), '> a', [
          ['click', $.proxy(_onMainItemClick, this)]
        ]],
        [$(win), {
          resize: $.proxy(_onWindowResize, this)
        }]
      ];

      this.navCollapsedEvents = [
        [this.$mainItems, {
          mouseenter: $.proxy(_onMainItemMouseenter, this),
          mouseleave: $.proxy(_onMainItemMouseleave, this)
        }]
      ];
    }

    /**
     * onNavToggleClick event handler
     * Toggles .nav-closed class on the body.
     *
     * @param event
     * @private
     */
    function _onNavToggleClick(event) {
      if (!this.closed) {
        this.$body.addClass('nav-closed');
        this.closed = true;
      } else {
        this.$body.removeClass('nav-closed');
        this.closed = false;
      }
    }

    /**
     * onWindowResize event handler
     * Calls _collapseMenu on window resize.
     *
     * @private
     */
    function _onWindowResize() {
      clearTimeout(this.resizeTimer);
      this.resizeTimer = setTimeout($.proxy(_collapseMenu, this), 100);
    }

    /**
     * Attaches/detaches event handlers if the navigation is collapsed
     * and toggles the .collapsed class on the navigation.
     *
     * The visuals of the collapsed nav are done via media queries and not via JS.
     *
     * @private
     */
    function _collapseMenu() {
      var prevCollapsed = this.collapsed;
      this.collapsed = this.$nav.find('> li > a > .item-name').first().css('display') === 'none';
      if (this.collapsed) {
        this.$nav.addClass('collapsed');
        if (this.collapsed !== prevCollapsed) {
          $.attachEvents(this.navCollapsedEvents);
        }
      } else {
        this.$nav.removeClass('collapsed');
        $.detachEvents(this.navCollapsedEvents);
      }
    }

    /**
     * onMainItemClick event handler
     * Toggles the .open class on every nav li that has parent items.
     *
     * @param event
     * @private
     */
    function _onMainItemClick(event) {
      var $target = $(event.target);
      if (!$target.is('li')) {
        $target = $target.closest('li');
      }
      $target.toggleClass('open');
    }

    /**
     * onMainItemMouseenter event handler
     * Add .popout class to hovered li.
     *
     * @param event
     * @private
     */
    function _onMainItemMouseenter(event) {
      if (!this.collapsed) {
        return;
      }
      var $target = $(event.target);
      if (!$target.is('li')) {
        $target = $target.closest('li');
      }
      $target.addClass('popout');
    }

    /**
     * onMainItemMouseLeave event handler
     * Remove .popout class from the previously hovered li
     *
     * @param event
     * @private
     */
    function _onMainItemMouseleave(event) {
      if (!this.collapsed) {
        return;
      }
      var $target = $(event.target);
      if (!$target.is('li')) {
        $target = $target.closest('li.popout');
      }
      $target.removeClass('popout');
    }

    return {

      /**
       * Constructor
       */
      constructor: BackendMenu,

      /**
       * Initialization fn
       */
      init: function() {
        _buildEvents.call(this);
        _collapseMenu.call(this);
        $.attachEvents(this.events);
      }
    }

  })();

  wasabi.core.BackendMenu = BackendMenu;

})(jQuery, window);
