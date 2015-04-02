define([], function() {

  /**
   * Use listenTo/stopListening from Backbone.js with any DOM element
   *
   * Example:
   *
   * view.listenTo(eventify(window), "resize", handler);
   *
   * and the listener will be removed automatically on view.remove() or
   * view.stoplistening()
   *
   * @param {Element|jQuery} el
   * @returns {{on: on, off: off}} Backbone Events style object
   * @see http://stackoverflow.com/questions/14460855/backbone-js-listento-window-resize-throwing-object-object-has-no-method-apply
   */
  function eventify(el) {

    // Unwrap jQuery
    if (typeof el.get === "function") el = el.get(0);

    var listeners = [];

    return {
      on: function(event, handler, context) {
        el.addEventListener(event, handler, false);
        listeners.push({
          args: [event, handler],
          context: context
        });
      },
      off: function(event, handler, context) {
        listeners = listeners.filter(function(listener) {
          if (listener.context === context) {
            el.removeEventListener.apply(el, listener.args);
            return true;
          }
        });
      }

    };
  }

  return eventify;

});
