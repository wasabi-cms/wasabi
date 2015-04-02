(function($, undefined) {

  "use strict";

  var MultiSelect = function(element, options) {
    this.settings = $.extend({}, $.fn.multiSelect.defaults, options);
    this.$el = $(element);

    this.$container = $('<div/>', { 'class': 'ms-container row' });
    this.$available = $('<ul/>', { 'class': 'ms-list', 'tabindex' : '-1' });
    this.$selection = null;

    this.init();
  };

  MultiSelect.prototype = (function() {

    function _init() {
      var that = this;

      this.$el.css({
        position: 'absolute',
        left: '-9999px'
      });

      this.$el.find('option').each(function(index, el) {
        _addOption.call(that, $(el));
      });

      this.$selection = this.$available.clone();
      this.$selection
        .addClass('ms-selection')
        .attr('unselectable', 'on')
        .css('user-select', 'none')
        .on('selectstart', false);
      this.$available
        .addClass('ms-available')
        .attr('unselectable', 'on')
        .css('user-select', 'none')
        .on('selectstart', false);

      this.$el.find('option[selected]').each(function(index, el) {
        var $item = that.$available.find('.ms-item[data-value="' + $(el).attr('value') + '"]');
        _selectOption.call(that, $item, true);
      });

      _buildEvents.call(this);
      if (!this.$el.attr('disabled')) {
        $.attachEvents(this._primaryEvents);
      } else {
        this.$container.addClass('disabled');
      }

      this.$container.append(this.$available).append(this.$selection);
      this.$el.after(this.$container);
    }

    function _addOption($el) {
      var $item = $('<li/>', {class: 'ms-item'})
        .attr('data-value', $el.attr('value'))
        .text($el.attr('value'));
      var $optGroup = $el.parent('optgroup');

      if ($el.attr('disabled')) {
        $item.addClass(this.settings.disabledClass);
      }

      if ($optGroup.length !== 0) {
        var label = $optGroup.attr('label');

        var $existing = this.$available.find('li[data-optgrp="' + label + '"]');
        if ($existing.length === 0) {
          var $optGrp = $('<li/>', {class: 'ms-optgrp'})
            .attr('data-optgrp', label)
            .append($('<span/>').text(label))
            .append($('<ul/>'));
          this.$available.append($optGrp);
          $existing = this.$available.find('li[data-optgrp="' + label + '"]');
        }

        $existing.find('> ul').append($item);
      } else {
        this.$available.append($item);
      }
    }

    /**
     *
     * @param {*} $el
     * @param {boolean} init
     * @private
     */
    function _selectOption($el, init) {
      var value = $el.attr('data-value');

      // update available
      var $available = this.$available.find('li[data-value="' + value + '"]').addClass('active');
      _updateAvailableOptGroup.call(this, $available);

      // update selection
      var $selection = this.$selection.find('li[data-value="' + value + '"]').addClass('active');
      _updateSelectionOptGroup.call(this, $selection);

      if (!init) {
        this.$el.find('option[value="' + value + '"]').prop('selected', true);
      }
    }

    function _deselectOption($el) {
      var value = $el.attr('data-value');

      // update available
      var $available = this.$available.find('li[data-value="' + value + '"]').removeClass('active');
      _updateAvailableOptGroup.call(this, $available);

      // update selection
      var $selection = this.$selection.find('li[data-value="' + value + '"]').removeClass('active');
      _updateSelectionOptGroup.call(this, $selection);

      this.$el.find('option[value="' + value + '"]').prop('selected', false);
    }

    function _updateAvailableOptGroup($item) {
      var $optGroup = $item.parent().parent('.ms-optgrp');
      if ($optGroup.length === 1) {
        if ($optGroup.find('> ul li:not(.active)').length === 0) {
          $optGroup.addClass('all-active');
        } else {
          $optGroup.removeClass('all-active');
        }
      }
    }

    function _updateSelectionOptGroup($item) {
      var $optGroup = $item.parent().parent('.ms-optgrp');
      if ($optGroup.length === 1) {
        if ($optGroup.find('> ul li.active').length > 0) {
          $optGroup.addClass('active');
        } else {
          $optGroup.removeClass('active');
        }
      }
    }

    function _buildEvents() {
      this._primaryEvents = [
        [this.$el, {
          focus: $.proxy(_onSelectFocus, this),
          blur: $.proxy(_onSelectBlur, this),
          keydown: $.proxy(_onKeydown, this)
        }],
        [this.$container, '.ms-item:not(.' + this.settings.disabledClass + ')', [
          ['mouseenter', $.proxy(_onItemMouseEnter, this)],
          ['mouseleave', $.proxy(_onItemMouseLeave, this)],
          ['click', $.proxy(_onItemClick, this)]
        ]]
      ];
    }

    function _onSelectFocus(event) {
      if (!this.$available.hasClass('ms-focus') && !this.$selection.hasClass('ms-focus')) {
        this.$available.addClass('ms-focus');
      }
      this.disableTimeout = false;
    }

    function _onSelectBlur(event) {
      if (this.disableTimeout) {
        this.$available.removeClass('ms-focus');
        this.$selection.removeClass('ms-focus');
      } else {
        var that = this;
        setTimeout(function() {
          if (!that.$el.is(':focus')) {
            that.$available.removeClass('ms-focus');
            that.$selection.removeClass('ms-focus');
          }
        }, 100);
      }
    }

    function _onItemMouseEnter(event) {
      var $target = $(event.target);
      if ($target.parents().is(this.$available)) {
        this.$available.find('.ms-item').removeClass('hover');
      } else {
        this.$selection.find('.ms-item').removeClass('hover');
      }
      $target.addClass('hover');
    }

    function _onItemMouseLeave(event) {
      var $target = $(event.target);
      $target.removeClass('hover');
    }

    function _onItemClick(event) {
      var $item = $(event.target);
      if ($item.parents().is(this.$selection)) {
        this.$selection.addClass('ms-focus');
        this.$available.removeClass('ms-focus');
      } else {
        this.$available.addClass('ms-focus');
        this.$selection.removeClass('ms-focus');
      }

      this.$el.focus();

      if (!$item.hasClass('active')) {
        _selectOption.call(this, $item);
      } else {
        _deselectOption.call(this, $item);
      }
    }

    function _onKeydown(event) {
      switch (event.which) {
        case 40:
        case 38:
          event.preventDefault();
          event.stopPropagation();
          _moveHighlight.call(this, (event.which === 38) ? 1 : -1);
          break;
        case 32:
          event.preventDefault();
          event.stopPropagation();
          var $hovered;
          if (this.$available.hasClass('ms-focus')) {
            $hovered = this.$available.find('.ms-item.hover').first();
          } else {
            $hovered = this.$selection.find('.ms-item.hover').first();
          }
          $hovered.removeClass('hover');
          if (!$hovered.hasClass('active')) {
            _selectOption.call(this, $hovered);
          } else {
            _deselectOption.call(this, $hovered);
          }
          break;
        case 37:
        case 39:
          event.preventDefault();
          event.stopPropagation();
          if (this.$available.hasClass('ms-focus') && event.which === 39) {
            this.$available.removeClass('ms-focus');
            this.$selection.addClass('ms-focus');
          } else if (this.$selection.hasClass('ms-focus') && event.which === 37) {
            this.$selection.removeClass('ms-focus');
            this.$available.addClass('ms-focus');
          }
          break;
        case 9:
          this.disableTimeout = true;
      }
    }

    function _moveHighlight(direction) {
      var $hovered, $items, $next, idx, $list;
      if (this.$available.hasClass('ms-focus')) {
        $list = this.$available;
        $hovered = $list.find('.ms-item.hover');
        $items = $list.find('.ms-item:not(.active):not(.' + this.settings.disabledClass + ')');

      } else {
        $list = this.$selection;
        $hovered = $list.find('.ms-item.hover');
        $items = $list.find('.ms-item.active:not(.' + this.settings.disabledClass + ')');
      }
      $items.removeClass('hover');
      if (direction === -1) {// DOWN
        if ($hovered.length > 0) {
          idx = $items.index($hovered) + 1;
          if (idx > ($items.length - 1)) {
            idx = 0;
          }
        } else {
          idx = 0;
        }
      } else {
        if ($hovered.length > 0) {
          idx = $items.index($hovered) - 1;
          if (idx < 0) {
            idx = $items.length - 1;
          }
        } else {
          idx = $items.length - 1;
        }
      }
      $next = $($items.get(idx));
      if ($next.length > 0) {
        $next.addClass('hover');
        var scrollTo = $list.scrollTop() + $next.position().top - ($list.height() + $next.outerHeight()) / 2;
        $list.scrollTop(scrollTo);
      }
    }

    return {

      /**
       * Constructor
       */
      constructor: MultiSelect,

      /**
       * Initialize MultiSelect
       */
      init: function() {
        _init.call(this);
      },

      selectAll: function() {
        var that = this;
        this.$available.find('.ms-item:not(.active):not(.' + this.settings.disabledClass + ')').each(function(index, el) {
          _selectOption.call(that, $(el));
        });
      },

      deselectAll: function() {
        var that = this;
        this.$available.find('.ms-item.active:not(.' + this.settings.disabledClass + ')').each(function(index, el) {
          _deselectOption.call(that, $(el));
        });
      },

      enable: function() {
        if (this.$el.attr('disabled')) {
          this.$el.prop('disabled', false);
          $.attachEvents(this._primaryEvents);
          this.$container.removeClass('disabled');
        }
        return this.$el;
      },

      disable: function() {
        if (!this.$el.attr('disabled')) {
          $.detachEvents(this._primaryEvents);
          this.$el.attr('disabled', 'disabled');
          this.$container.addClass('disabled');
        }
        return this.$el;
      }
    }

  })();

  $.fn.multiSelect = function (options) {
    if (!options || typeof options === 'object') {
      return this.each(function() {
        if (!$(this).data('multiSelect')) {
          $(this).data('multiSelect', new MultiSelect(this, options));
        }
      });
    } else if (typeof options === 'string' && options.charAt(0) !== '_') {
      var multiSelect = this.data('multiSelect');
      if (!multiSelect) {
        throw new Error('multiSelect is not initialized on this DOM element.');
      }
      if (multiSelect && multiSelect[options]) {
        return multiSelect[options].apply(multiSelect, Array.prototype.slice.apply(arguments, [1]))
      }
    }
    throw new Error('"' + options + '" is no valid api method.');
  };

  $.fn.multiSelect.defaults = {
    disabledClass : 'disabled'
  };

})(window.jQuery);
