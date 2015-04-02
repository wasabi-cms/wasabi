(function($) {

  /**
   * ToggleSelect
   *
   * @param element
   * @constructor
   */
  var ToggleSelect = function(element) {
    this.$el = $(element);
    this.$bottomEl = this.$el.closest('form').find('[data-toggle="select-secondary"]').first();
    this.$items = this.$el.closest('form').find('input[type="checkbox"][name="' + this.$el.attr('data-target') + '"]');
    this.init();
  };

  /**
   * ToggleSelect Prototype
   */
  ToggleSelect.prototype = (function() {

    /**
     * _onClick event handler
     *
     * @param event
     * @private
     */
    function _onClick(event) {
      this.$bottomEl.prop('checked', this.$el[0].checked);
      this.$items.prop('checked', this.$el[0].checked);
      this.$items.closest('tr').toggleClass('selected', this.$el[0].checked);
    }

    /**
     * _onBottomClick event handler
     *
     * @param event
     * @private
     */
    function _onBottomClick(event) {
      this.$el.prop('checked', this.$bottomEl[0].checked);
      this.$items.prop('checked', this.$bottomEl[0].checked);
      this.$items.closest('tr').toggleClass('selected', this.$bottomEl[0].checked);
    }

    /**
     * _onItemChange event handler
     * Sets the state of the global checkbox to reflect the current selection.
     *
     * @param event
     * @private
     */
    function _onItemChange(event) {
      var $target = $(event.target);
      if (!$target[0].checked) {
        this.$el.prop('checked', false);
        this.$bottomEl.prop('checked', false);
      }
      if (this.$items.length === this.$items.filter(':checked').length) {
        this.$el.prop('checked', true);
        this.$bottomEl.prop('checked', true);
      }
      $target.closest('tr').toggleClass('selected', $target[0].checked);
    }

    /**
     * public methods
     */
    return {

      /**
       * Initialization
       */
      init: function() {
        this.$el.attr('autocomplete', 'off');
        this.$el.on('click', $.proxy(_onClick, this));

        this.$bottomEl.attr('autocomplete', 'off');
        this.$bottomEl.on('click', $.proxy(_onBottomClick, this));

        this.$items.attr('autocomplete', 'off');
        this.$items.on('change', $.proxy(_onItemChange, this));
      }

    }

  })();

  /**
   * toggleSelect jQuery plugin definition
   *
   * @param options
   * @returns {*}
   */
  $.fn.toggleSelect = function (options) {
    return this.each(function() {
      if (!$(this).data('toggleSelect')) {
        $(this).data('toggleSelect', new ToggleSelect(this, options));
      }
    });
  };

})(jQuery);
