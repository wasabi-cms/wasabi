goog.provide('wasabi.core.Menus');

(function($) {

  /**
   * Parses a url string with the form of
   *
   * plugin:pluginName/controller:ctrlName/action:actionName/params/query
   *
   * @param urlString
   * @returns {{plugin: string, controller: string, action: string, params: Array, query: string}}
   */
  function parseUrlString(urlString) {
    var _i, _len, url = {
      plugin: '',
      controller: '',
      action: '',
      params: [],
      query: ''
    };

    var parts = urlString.split('?');
    urlString = parts[0];

    if (parts[1] !== undefined) {
      url.query = parts[1];
    }

    parts = urlString.split('/');

    for (_i = 0, _len = parts.length; _i < _len; _i++) {

      if (_i === 0) {
        var plugin = parts[_i].split('plugin:')[1];
        if (plugin !== undefined) {
          url.plugin = plugin;
          continue;
        }
      }

      if ((_i === 0 || _i === 1) && url.controller === '') {
        var controller = parts[_i].split('controller:')[1];
        if (controller !== undefined) {
          url.controller = controller;
          continue;
        }
      }

      if ((_i ===1 || _i === 2) && url.action === '') {
        var action = parts[_i].split('action:')[1];
        if (action !== undefined) {
          url.action = action;
          continue;
        }
      }

      url.params.push(parts[_i]);
    }

    if (url.params.length > 0) {
      url.params = url.params.join('/');
    } else {
      url.params = '';
    }

    return url;
  }

  /**
   * Menus Constructor
   *
   * @constructor
   */
  var Menus = function(menuItems, menuItemId, menuSelect, menuItemParent, menuItemTypeSelect) {

    /**
     * Menu events and their registered handlers.
     *
     * @type {Array}
     */
    this.events = [];

    this.$menuItems = $(menuItems);

    this.menuItemId = $(menuItemId).val();
    this.$menuSelect = $(menuSelect);
    this.$menuItemParent = $(menuItemParent);
    this.$menuItemTypeSelect = $(menuItemTypeSelect);

    this.init();
  };

  /**
   * Menus prototype
   *
   * @type {Function}
   */
  Menus.prototype = (function() {

    /**
     * Build all default events.
     *
     * @private
     */
    function _buildEvents() {
      if (this.$menuItems.length > 0) {
        this.events.push(
          [this.$menuItems, {
            "nSortable-change": $.proxy(_onNSortableChange, this)
          }]
        )
      }
      if (this.$menuSelect.length > 0) {
        this.events.push(
          [this.$menuSelect, {
            change: $.proxy(_onMenuSelectChange, this)
          }]
        );
      }
      if (this.$menuItemTypeSelect.length > 0) {
        this.events.push(
          [this.$menuItemTypeSelect, {
            change: $.proxy(_onMenuItemTypeChange, this)
          }]
        )
      }
    }

    /**
     * _onNSortableChange event handler
     * Serializes all menu items and posts them
     * to the reorder url.
     *
     * @param event
     * @param nSortable
     * @private
     */
    function _onNSortableChange(event, nSortable) {
      var url = this.$menuItems.attr('data-reorder-url');
      if (typeof url === 'undefined' || url === 'false') {
        return;
      }
      this.$menuItems.block({
        backgroundColor: 'transparent'
      });
      var items = nSortable.toArray();
      for (var key in items) {
        var item = items[key];
        var $add = this.$menuItems.find('li[data-menu-item-id="' + item.id + '"]').first().find('a.wicon-add').first();
        if (item.depth > 2) {
          $add.addClass('hide');
        } else {
          $add.removeClass('hide');
        }
      }
      $.ajax({
        type: 'post',
        dataType: 'json',
        url: url,
        data: nSortable.serialize(),
        cache: false,
        success: $.proxy(function() {
          this.$menuItems.unblock();
        }, this)
      });
    }

    function _onMenuSelectChange() {
      var val = this.$menuSelect.val();
      var $field = this.$menuItemParent.parent();
      $field.block({
        backgroundColor: '#fff'
      });
      $.ajax({
        dataType: 'json',
        url: this.$menuSelect.attr('data-parents-url') + '/' + this.menuItemId + '/' + this.$menuSelect.val(),
        cache: false,
        success: $.proxy(function(data) {
          var $newParents = $(data);
          this.$menuItemParent.val(false);
          this.$menuItemParent.html($newParents.html());
          $field.unblock();
        }, this)
      })
    }

    /**
     * onMenuItemTypeChange event handler
     * Parses the selected options and displays
     * the corresponding link options.
     *
     * @private
     */
    function _onMenuItemTypeChange() {
      var val = this.$menuItemTypeSelect.val();
      var parts = val.split('|');
      var $container = $('.field.link-options');
      var url;

      $('#MenuItemType').val(parts[0]);

      var $active_div = $container.find('div.active');
      $active_div.removeClass('active').find('input').prop('disabled', true);

      var $div = $container.find('div[data-type="' + parts[0] + '"]');

      if ($div.length > 0) {
        $div.find('input').removeAttr('disabled');
        $div.addClass('active');
      } else {
        $container.find('div[data-type="empty"]').first().addClass('active');
      }

      if (parts[0] === 'Object' || parts[0] === 'Action') {
        $div = $container.find('div[data-type="' + parts[0] + '"]');
        url = {};

        if (parts[0] === 'Object') {
          url = parseUrlString(parts[3]);
          $div.find('input[name*="foreign_model"]').first().val(parts[1]);
          $div.find('input[name*="foreign_id"]').first().val(parts[2]);
        }

        if (parts[0] === 'Action') {
          url = parseUrlString(parts[1]);
        }

        $div.find('input[name*="plugin"]').first().val(url.plugin);
        $div.find('input[name*="controller"]').first().val(url.controller);
        $div.find('input[name*="action"]').first().val(url.action);
        $div.find('input[name*="params"]').first().val(url.params);
        $div.find('input[name*="query"]').first().val(url.query);
      }
    }

    /**
     * Initialize nested sortable behavior
     * for all menu items
     *
     * @private
     */
    function _initNestedSortable() {
      this.$menuItems.nSortable({
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
    }

    return {

      /**
       * Constructor
       */
      constructor: Menus,

      /**
       * Initialization fn
       */
      init: function() {
        _buildEvents.call(this);
        $.attachEvents(this.events);

        if (this.$menuItems.length > 0) {
          _initNestedSortable.call(this);
        }

        if (this.$menuItemTypeSelect.length > 0) {
          _onMenuItemTypeChange.call(this);
        }
      }
    }

  })();

  wasabi.core.Menus = Menus;

})(jQuery);
