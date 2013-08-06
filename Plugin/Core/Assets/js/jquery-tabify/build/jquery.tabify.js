/*!
 * jQuery Tabify v0.0.1
 *
 * Copyright (c) 2013 Frank Förster (http://frankfoerster.com)
 * Licensed under the MIT License
 */
(function($) {
  "use strict";

  var Tabify = function(tabUL) {
    this.$tabUL = $(tabUL);
    this.tabifyId = this.$tabUL.attr('data-tabify-id');
    this.$LIs = this.$tabUL.find('li');
    this.$As = this.$LIs.find('a');
    this.$tabs = $('div[data-tabify-tab]').filter('[data-tabify-id="' + this.tabifyId + '"]');

    this._events = [];
    this._buildEvents();
    $.attachEvents(this._events);
  };

  Tabify.prototype = {

    _buildEvents: function() {
      this._events = [
        [this.$LIs, {
          click: $.proxy(this.onClick, this)
        }],
        [this.$As, {
          click: $.proxy(this.onClick, this)
        }]
      ];
    },

    onClick: function(event) {
      event.preventDefault();
      var $this = $(event.target), target, disabled;
      if ($this.is('a')) {
        $this = $this.parent();
      }
      if (!$this.is('li')) {
        return;
      }

      target = $this.attr('data-tabify-target');
      disabled = $this.attr('data-tabify-disabled');

      if (typeof disabled !== 'undefined' && disabled === 'true') {
        return;
      }

      this.$LIs.removeClass('active');
      this.$tabs.removeClass('active').hide();
      this.$tabs.filter('[data-tabify-tab="' + target + '"]').addClass('active').show();
      if ($this.is('a')) {
        $this.parent().addClass('active');
      } else {
        $this.addClass('active');
      }
    }

  };

  $.fn.tabify = function() {
    return this.each(function() {
      if (!$(this).data('tabify')) {
        $(this).data('tabify', new Tabify(this));
      }
      return $(this).data('tabify');
    });
  };

})(jQuery);
