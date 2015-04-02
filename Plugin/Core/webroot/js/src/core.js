/*!
* Wasabi Core
* Copyright (c) 2014 Frank Förster
*/
define(['jquery', 'wasabi', 'bootstrap.dropdown'], function($, wasabi) {

  var Core = function(options) {
    options = options || {};
    this.translations = options.translations || {};
  };

  Core.prototype = (function(win, doc) {

    /**
     * Initialize the backend.
     *
     * @private
     */
    function _init() {
      $('html').removeClass('no-js');

      wasabi.core = this;

      _setupAjax();
      _initTabs();
      _initModals();
      _initMultiSelect();
      _initToggleSelect();

      require(['common/views/Navigation', 'common/views/NavToggle'], function(Navigation, NavToggle) {
        wasabi.views.navigation = wasabi.viewFactory.create(Navigation);
        wasabi.views.navToggle = wasabi.viewFactory.create(NavToggle);
      });
    }

    /**
     * Setup default Ajax options and global Ajax event handlers.
     *
     * @private
     */
    function _setupAjax() {
      $.ajaxSetup({
        dataType: 'json'
      });
      $(doc)
        .ajaxSuccess(_onAjaxSuccess)
        .ajaxError(_onAjaxError);
    }

    /**
     * Default ajax success handler.
     * Displays a flash message if the response contains
     * {
     *   'status': '...' # the class of the flash message
     *   'flashMessage': '...' # the text of the flash message
     * }
     *
     * @param event
     * @param xhr
     * @private
     */
    function _onAjaxSuccess(event, xhr) {
      if (xhr.status == 200 && xhr.statusText == 'OK') {
        var data = $.parseJSON(xhr.responseText) || {};
        if (typeof data.status !== 'undefined' && typeof data.flashMessage !== 'undefined') {
          _flashMessage('div.title-pad', data.status, data.flashMessage);
        }
      }
    }

    /**
     * Default ajax error handler.
     *
     * @param event
     * @param xhr
     * @private
     */
    function _onAjaxError(event, xhr) {
      var data;
      if (xhr.status == 401) {
        data = $.parseJSON(xhr.responseText) || {};
        if (typeof data.name !== 'undefined') {
          if (confirm(data.name)) {
            win.location.reload();
          } else {
            win.location.reload();
          }
        }
      }
      if (xhr.status == 500) {
        data = $.parseJSON(xhr.responseText) || {};
        if (typeof data.name !== 'undefined') {
          _flashMessage('div.title-pad', 'error', data.name);
        }
      }
    }

    /**
     * Translate a message with its context.
     *
     * @param {string} message
     * @param {Array=} context
     * @returns {string}
     * @private
     */
    function _translate(message, context) {
      message = this.translations[message] || message.toString();
      if (context) {
        $.each(context, function (key, value) {
          message = message.replace('{' + key + '}', value);
        });
      }
      return message;
    }

    /**
     * Render a flash message after a specific element.
     *
     * @param {string|jQuery} elAfter The element after which the message should be rendered.
     * @param {string}        cls     The css class of the flash message.
     * @param {string}        message The content of the flash message.
     * @private
     */
    function _flashMessage(elAfter, cls, message) {
      var $ancestor = $(elAfter);
      if ($ancestor.length) {
        $('#flashMessage').remove();
        var $flashMessage = $('<div id="flashMessage"></div>');
        $flashMessage.addClass(cls).html(message);
        $ancestor.after($flashMessage);
      }
    }

    /**
     * Initialize tabs.
     *
     * @private
     */
    function _initTabs() {
      require(['common/views/Tabs'], function(Tabs) {
        $('.tabs').each(function() {
          wasabi.views.tabs = wasabi.views.tabs || [];
          wasabi.views.tabs.push(
            wasabi.viewFactory.create(Tabs, {
              el: $(this)
            })
          );
        });
      });
    }

    /**
     * Initialize modal dialogs
     *
     * @private
     */
    function _initModals() {
      require(['common/views/Modal', 'jquery.livequery'], function(ModalView) {
        $('[data-toggle="modal"], [data-toggle="confirm"]').livequery(function() {
          wasabi.views.modals = wasabi.views.modals || [];
          wasabi.views.modals.push(
            wasabi.viewFactory.create(ModalView, {
              el: $(this)
            })
          );
        });
      });
    }

    /**
     * Initialize jQuery MultiSelect on all
     * elements with class name 'mselect'.
     *
     * @private
     */
    function _initMultiSelect() {
      require(['jquery.multiselect'], function() {
        $('.mselect').multiSelect();
      });
    }

    /**
     * Initialize jQuery ToggleSelect for all
     * bulk tables.
     *
     * @private
     */
    function _initToggleSelect() {
      require(['jquery.toggleSelect'], function() {
        $('[data-toggle="select"]').toggleSelect();
      });
    }

    return {
      init: function() {
        _init.call(this);
      },
      menuItems: function() {
        require(['core/views/MenuItems'], function(MenuItems) {
          wasabi.views.Core.menuItems = wasabi.viewFactory.create(MenuItems);
        });
      },
      languages: function() {
        require(['core/views/Languages'], function(Languages) {
          wasabi.views.Core.languages = wasabi.viewFactory.create(Languages);
        });
      },
      settingsMedia: function() {
        require(['core/views/SettingsMedia'], function(SettingsMedia) {
          wasabi.views.Core.settingsMedia = wasabi.viewFactory.create(SettingsMedia);
        });
      },
      permissions: function() {
        require(['core/views/Permissions'], function(Permissions) {
          wasabi.views.Core.permissions = wasabi.viewFactory.create(Permissions);
        });
      }
    }

  })(window, document);

  return Core;

});
