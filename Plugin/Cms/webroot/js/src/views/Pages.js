define(['jquery', 'common/views/BaseView', 'common/constants/SpinPresets', 'jquery.nSortable', 'jquery.cookie'], function($, BaseView, SpinPresets) {

  return BaseView.extend({

    /**
     * The element of this view.
     *
     * @type {string} CSS selector
     */
    el: '#pages',

    /**
     * Registered events of this view.
     *
     * @type {Object}
     */
    events: {
      'click .expander': 'onExpanderClick',
      'nSortable-change': 'onPageOrderChanged'
    },

    pageReorderEndpoint: null,

    /**
     * Initialization of the view.
     */
    initialize: function() {
      this.pageReorderEndpoint = this.$el.attr('data-reorder-url');
      this.$el.nSortable({
        handle: 'a.move',
        placeholder: 'sortable-placeholder',
        dataAttribute: 'data-cms-page-id',
        serializeKey: 'CmsPage',
        leftKey: 'lft',
        rightKey: 'rght',
        animateTarget: false
      });
    },

    onExpanderClick: function(event) {
      event.preventDefault();
      var $expander = $(event.target);
      var $li = $expander.parent().parent().parent().parent();
      if (!$li.hasClass('closed')) {
        $expander.removeClass('wicon-collapse').addClass('wicon-expand');
        $li.find('ul').first().slideUp(300, _.bind(function() {
          $li.addClass('closed');
          this.setClosedPagesCookie();
        }, this));
      } else {
        $expander.removeClass('wicon-expand').addClass('wicon-collapse');
        $li.find('ul').first().slideDown(300, _.bind(function() {
          $li.removeClass('closed');
          this.setClosedPagesCookie();
        }, this));
      }
    },

    onPageOrderChanged: function(event, nSortable) {
      this.blockThis({
        backgroundColor: '#fff',
        deltaHeight: -1,
        spinner: SpinPresets.large
      });
      $.ajax({
        type: 'post',
        dataType: 'json',
        url: this.pageReorderEndpoint,
        data: nSortable.serialize(),
        cache: false,
        success: _.bind(this.unblockThis, this)
      });
    },

    setClosedPagesCookie: function() {
      var closed_pages = [];
      this.$el.find('li.closed').each(function() {
        closed_pages.push($(this).attr('data-cms-page-id'));
      });
      $.cookie('closed_pages', closed_pages, {expires: 365});
    }

  });
});
