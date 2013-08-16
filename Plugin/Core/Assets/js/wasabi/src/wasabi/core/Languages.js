goog.provide('wasabi.core.Languages');

(function($) {

  /**
   * Languages Constructor
   *
   * @constructor
   */
  var Languages = function(languages) {

    /**
     * Languages events and their registered handlers.
     *
     * @type {Array}
     */
    this.events = [];

    this.$languages = $(languages);

    this.init();
  };

  /**
   * Languages prototype
   *
   * @type {Function}
   */
  Languages.prototype = (function() {

    /**
     * Build all default events.
     *
     * @private
     */
    function _buildEvents() {
      if (this.$languages.length > 0) {
        this.events.push(
          [this.$languages, {
            "tSortable-change": $.proxy(_onTSortableChange, this)
          }]
        )
      }
    }

    /**
     * _onTSortableChange event handler
     * Updates the position fields and submits the #LanguagesIndexForm.
     *
     * @private
     */
    function _onTSortableChange() {
      var i = 1;
      this.$languages.find('tbody > tr').each(function() {
        $(this).find('input.position').first().val(i);
        i++;
      });
      var form = $('#LanguageIndexForm');
      $.ajax({
        url: form.attr('action'),
        data: form.serialize(),
        type: 'post'
      });
    }

    /**
     * Initialize table sortable behavior
     *
     * @private
     */
    function _initTableSortable() {
      this.$languages.tSortable({
        handle: 'a.sort',
        placeholder: 'sortable-placeholder',
        opacity: 0.8
      });
    }

    return {

      /**
       * Constructor
       */
      constructor: Languages,

      /**
       * Initialization fn
       */
      init: function() {
        _buildEvents.call(this);
        $.attachEvents(this.events);

        if (this.$languages.length > 0) {
          _initTableSortable.call(this);
        }
      }
    }

  })();

  wasabi.core.Languages = Languages;

})(jQuery);
