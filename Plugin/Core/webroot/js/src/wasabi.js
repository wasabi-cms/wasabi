define(['Backbone', 'common/views/BaseViewFactory'], function(Backbone, BaseViewFactory) {
  var wasabi = window.wasabi = {};

  wasabi.eventBus = _.extend({}, Backbone.Events);
  wasabi.viewFactory = new BaseViewFactory(wasabi.eventBus);
  wasabi.views = {
    Core: {}
  };

  return wasabi;
});
