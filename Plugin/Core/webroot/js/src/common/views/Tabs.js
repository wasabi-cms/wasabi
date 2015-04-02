define(['jquery', 'common/views/BaseView'], function($, BaseView) {

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
     * Registered events of this view.
     *
     * @type {Object}
     */
    events: {
      'click li': 'onClick',
      'click a': 'onClick'
    },

    /**
     * Options
     *
     * @type {Object}
     */
    options: {},

    /**
     * Stores the tabify id of the current tab view instance.
     *
     * @type {string}
     */
    tabifyId: '',

    /**
     * Holds a reference to all links of the tab el.
     *
     * @type {jQuery}
     */
    $links: null,

    /**
     * Holds a reference to all list items of the tab el.
     *
     * @type {jQuery}
     */
    $listItems: null,

    /**
     * Holds a reference to all tabs of the tab el.
     *
     * @type {jQuery}
     */
    $tabs: null,

    /**
     * Initialization of the view.
     *
     * @param {Object=} options
     */
    initialize: function(options) {
      this.options = $.extend({}, defaults, options);
      this.tabifyId = this.$el.attr('data-tabify-id');
      this.$listItems = this.$('li');
      this.$links = this.$('a');
      this.$tabs = $('div[data-tabify-tab]').filter('[data-tabify-id="' + this.tabifyId + '"]');
      this.markErrorsOnTab();
    },

    /**
     * onClick event handler
     *
     * @param {Object} event
     */
    onClick: function(event) {
      event.preventDefault();
      var $target = $(event.target), targetTab, disabled;
      if ($target.is('a')) {
        $target = $target.parent();
      }
      if (!$target.is('li')) return;

      disabled = $target.attr('data-tabify-disabled');
      if (typeof disabled !== 'undefined' && disabled === 'true') return;

      targetTab = $target.attr('data-tabify-target');
      this.$listItems.removeClass('active');
      this.$tabs.removeClass('active').hide();
      this.$tabs.filter('[data-tabify-tab="' + targetTab + '"]').addClass('active').show();
      $target.addClass('active');
    },

    /**
     * If a tab has form rows that contains errors,
     * then reflect the error state on the list item of the tab.
     */
    markErrorsOnTab: function() {
      var that = this;
      this.$tabs.each(function() {
        if ($(this).find('.form-row.error').length > 0) {
          that.$listItems.filter('[data-tabify-target="' + $(this).attr('data-tabify-tab') + '"]').addClass('has-error');
        }
      });
    }

  });
});
