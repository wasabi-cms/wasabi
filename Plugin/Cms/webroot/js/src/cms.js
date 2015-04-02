define(['jquery', 'wasabi', 'core'], function($, wasabi, Core) {

  var Cms = function(options) {
    options = options || {};
    this.translations = options.translations || {};
  };

  _.extend(Cms.prototype, Core.prototype, (function(win, doc) {

    /**
     * Initialize the cms backend.
     *
     * @private
     */
    function _init() {
      wasabi.cms = this;
      wasabi.views.Cms = {};
    }

    return {
      init: function() {
        _init.call(this);
      },
      pages: function() {
        require(['Cms/views/Pages'], function(Pages) {
          wasabi.views.Cms.pages = wasabi.viewFactory.create(Pages);
        });
      },
      routes: function() {
        require(['Cms/views/Routes'], function(Routes) {
          wasabi.views.Cms.routes = wasabi.viewFactory.create(Routes);
        })
      }
//      menuItems: function() {
//        require(['Core/views/Core/MenuItems'], function(MenuItems) {
//          wasabi.views.Core.menuItems = wasabi.viewFactory.create(MenuItems);
//        });
//      }
    }

  })(window, document));

  return Cms;

});
