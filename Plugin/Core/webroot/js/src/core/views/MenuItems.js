define(['jquery', 'common/views/BaseView', 'common/constants/SpinPresets', 'jquery.nSortable'], function($, BaseView, SpinPresets) {

  return BaseView.extend({

    /**
     * The element of this view.
     *
     * @type {string} CSS selector
     */
    el: '#menu-items',

    /**
     * Registered events of this view.
     *
     * @type {Object}
     */
    events: {
      'nSortable-change': 'onSort'
    },

    /**
     * Holds a the endpoint all reorder action are submitted to via ajax post.
     *
     * @type {string} url
     */
    $menuItemReorderEndpoint: null,

    /**
     * Initialization of the view.
     */
    initialize: function() {
      this.$menuItemReorderEndpoint = this.$el.attr('data-reorder-url');
      this.$el.nSortable({
        handle: 'a.move',
        tabWidth: 20,
        placeholder: 'sortable-placeholder',
        dataAttribute: 'data-menu-item-id',
        maxDepth: 2,
        serializeKey: 'MenuItem',
        leftKey: 'lft',
        rightKey: 'rght',
        animateTarget: false
      });
    },

    /**
     * onSort event handler
     * Updates the position fields and submits the #LanguageIndexForm.
     */
    onSort: function(event, nSortable) {
      this.blockThis({
        backgroundColor: '#fff',
        spinner: SpinPresets.large,
        deltaHeight: -1
      });
      var items = nSortable.toArray();
      for (var key in items) {
        if (!items.hasOwnProperty(key)) {
          continue;
        }
        var item = items[key];
        var $add = this.$el.find('li[data-menu-item-id="' + item.id + '"]').first().find('a.wicon-add').first();
        if (item.depth > 2) {
          $add.addClass('hide');
        } else {
          $add.removeClass('hide');
        }
      }
      $.ajax({
        type: 'post',
        dataType: 'json',
        url: this.$menuItemReorderEndpoint,
        data: nSortable.serialize(),
        cache: false,
        success: _.bind(this.unblockThis, this)
      });
    }

  });
});
