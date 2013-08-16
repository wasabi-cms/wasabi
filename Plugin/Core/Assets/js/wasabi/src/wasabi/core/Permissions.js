goog.provide('wasabi.core.Permissions');

(function($) {

  /**
   * Permissions Constructor
   *
   * @constructor
   */
  var Permissions = function(permissions) {

    /**
     * Permissions events and their registered handlers.
     *
     * @type {Array}
     */
    this.events = [];

    this.$permissions = $(permissions);

    this.init();
  };

  /**
   * Permissions prototype
   *
   * @type {Function}
   */
  Permissions.prototype = (function() {

    /**
     * Build all default events.
     *
     * @private
     */
    function _buildEvents() {
      this.events = [
        [this.$permissions, '.single-submit', [
          ['click', $.proxy(_onSingleSubmit, this)]
        ]]
      ];
    }

    /**
     * onSingleSubmit event handler
     * Submit a single permission.
     *
     * @param event
     * @private
     */
    function _onSingleSubmit(event) {
      event.preventDefault();
      var $target = $(event.target);
      $target.hide().blur().parent().spin('small');
      $.ajax({
        type: 'post',
        url: $('#GroupPermissionIndexForm').attr('action'),
        data: $target.parent().parent().find('input').serialize(),
        cache: false,
        success: function() {
          var tr = $target.parent().parent();
          var bgColor = $target.parent().css('backgroundColor');
          $target.parent().spin(false);
          $target.show();
          tr.find('td:not(.controller)').stop().css({
            backgroundColor: '#fff7d9'
          }).animate({
              backgroundColor: bgColor
            }, 1000, function() {
              $(this).css({backgroundColor: ''});
            });
        }
      });
    }

    return {

      /**
       * Constructor
       */
      constructor: Permissions,

      /**
       * Initialization fn
       */
      init: function() {
        if (this.$permissions.length > 0) {
          _buildEvents.call(this);
          $.attachEvents(this.events);
        }
      }
    }

  })();

  wasabi.core.Permissions = Permissions;

})(jQuery);
