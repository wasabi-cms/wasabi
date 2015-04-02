define(['Underscore'], function(_) {

  var BaseViewFactory = function(eventBus) {
    this.eventBus = eventBus;
    this.registry = {
      factory: this
    };
  };

  BaseViewFactory.prototype = _.extend({}, {

    register: function(key, value) {
      return this.registry[key] = value;
    },

    create: function(ViewClass, options) {
      var klass, passedOptions;
      options = options || {};
      passedOptions = _.extend(options, this.registry, {
        eventBus: this.eventBus
      });
      klass = ViewClass;
      klass.prototype.eventBus = this.eventBus;
      return new klass(passedOptions);
    }

  });

  return BaseViewFactory;

});
