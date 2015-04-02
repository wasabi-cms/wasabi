goog.provide('wasabi.core.Media');

(function($) {

  /**
   * Media Constructor
   *
   * @constructor
   */
  var Media = function() {

    /**
     * Media events and their registered handlers.
     *
     * @type {Array}
     */
    this.events = [];

    this.$allowAllMimeTypes = $('#CoreMediaSettingMediaAllowAllMimeTypes');
    this.$allowedMimeTypes  = $('#CoreMediaSettingMediaAllowedMimeTypes');
    this.$allowAllFileExtensions = $('#CoreMediaSettingMediaAllowAllFileExtensions');
    this.$allowedFileExtensions  = $('#CoreMediaSettingMediaAllowedFileExtensions');

    this.init();
  };

  /**
   * Media prototype
   *
   * @type {Object}
   */
  Media.prototype = (function() {

    /**
     * Build all default events.
     *
     * @private
     */
    function _buildEvents() {
      if (this.$allowAllMimeTypes.length > 0 && this.$allowedMimeTypes.length > 0) {
        this.events.push(
          [this.$allowAllMimeTypes, {
            change: $.proxy(_onAllowAllMimeTypesChange, this)
          }]
        );
      }
      if (this.$allowAllFileExtensions.length > 0 && this.$allowedFileExtensions.length > 0) {
        this.events.push(
          [this.$allowAllFileExtensions, {
            change: $.proxy(_onAllowAllFileExtensionsChange, this)
          }]
        );
      }
    }

    /**
     * _onAllowAllMimeTypesChange event handler
     * Enables / Disables the mime type multi select.
     *
     * @private
     */
    function _onAllowAllMimeTypesChange() {
      if (this.$allowAllMimeTypes[0].checked) {
        this.$allowedMimeTypes.multiSelect('disable');
      } else {
        this.$allowedMimeTypes.multiSelect('enable');
      }
    }

    /**
     * _onAllowAllFileExtensionsChange event handler
     * Enables / Disables the file extension multi select.
     *
     * @private
     */
    function _onAllowAllFileExtensionsChange() {
      if (this.$allowAllFileExtensions[0].checked) {
        this.$allowedFileExtensions.multiSelect('disable');
      } else {
        this.$allowedFileExtensions.multiSelect('enable');
      }
    }

    return {

      /**
       * Constructor
       */
      constructor: Media,

      /**
       * Initialization fn
       */
      init: function() {
        _buildEvents.call(this);
        $.attachEvents(this.events);
      }
    }

  })();

  wasabi.core.Media = Media;

})(jQuery);
