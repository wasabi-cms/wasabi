define(['jquery', 'common/views/BaseView', 'common/constants/SpinPresets'], function($, BaseView, SpinPresets) {

  return BaseView.extend({

    /**
     * The element of this view.
     *
     * @type {string} CSS selector
     */
    el: '[class*="cms-cms_pages-"] .field.routes',

    /**
     * Registered events of this view.
     *
     * @type {Object}
     */
    events: {
      'click .new-route button[type="submit"]': 'onNewRouteSubmit',
      'beforeAjax.modal': 'block',
      'deleteRoute': 'unblockUpdate',
      'makeDefaultRoute': 'unblockUpdate'
    },

    cmsPageId: null,
    routeEndpoint: null,

    /**
     * Initialization of the view.
     */
    initialize: function() {
      this.cmsPageId = $('#CmsPageId').val();
      this.routeEndpoint = this.$el.find('table.routes').attr('data-add-route-url');

      $('#CmsPageEditForm').on('submit', _.bind(function(event) {
        var id = $(event.originalEvent.explicitOriginalTarget).attr('id');
        if (id === 'RouteUrl' || id === 'RouteType') {
          event.preventDefault();
          this.submitRoute();
        }
      }, this));
    },

    onNewRouteSubmit: function(event) {
      event.preventDefault();
      this.submitRoute();
    },

    block: function(event) {
      this.blockThis({
        backgroundColor: '#fff',
        spinner: SpinPresets.large
      });
    },

    unblockUpdate: function(event, data) {
      this.unblockThis();
      this.$el.html(data);
    },

    submitRoute: function() {
      this.block(null);
      $.ajax({
        type: 'post',
        url: this.routeEndpoint,
        data: {
          "Route[url]": this.$el.find('#RouteUrl').val(),
          "Route[type]": this.$el.find('#RouteType').val(),
          pageId: this.cmsPageId
        },
        dataType: 'json',
        success: _.bind(function(data) {
          this.unblockUpdate(null, data);
        }, this)
      });
    }

  });
});
