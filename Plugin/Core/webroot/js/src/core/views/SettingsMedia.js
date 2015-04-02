define(['jquery', 'common/views/BaseView', 'jquery.multiselect'], function($, BaseView) {

  return BaseView.extend({

    /**
     * The element of this view.
     *
     * @type {string} CSS selector
     */
    el: '#CoreMediaSettingMediaForm',

    /**
     * Registered events of this view.
     *
     * @type {Object}
     */
    events: {
      'change #CoreMediaSettingMediaAllowAllMimeTypes': 'onAllowAllMimeTypesChanged',
      'change #CoreMediaSettingMediaAllowAllFileExtensions': 'onAllowAllFileExtensionsChanged'
    },

    $allowedMimeTypes: null,
    $allowedFileExtensions: null,

    /**
     * Initialization of the view.
     */
    initialize: function() {
      this.$allowedMimeTypes  = $('#CoreMediaSettingMediaAllowedMimeTypes');
      this.$allowedFileExtensions  = $('#CoreMediaSettingMediaAllowedFileExtensions');

    },

    onAllowAllMimeTypesChanged: function(event) {
      if (event.target.checked) {
        this.$allowedMimeTypes.multiSelect('disable');
      } else {
        this.$allowedMimeTypes.multiSelect('enable');
      }
    },

    onAllowAllFileExtensionsChanged: function(event) {
      if (event.target.checked) {
        this.$allowedFileExtensions.multiSelect('disable');
      } else {
        this.$allowedFileExtensions.multiSelect('enable');
      }
    }

  });
});
