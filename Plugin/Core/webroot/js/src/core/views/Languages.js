define(['jquery', 'common/views/BaseView', 'jquery.tSortable'], function($, BaseView) {

  return BaseView.extend({

    /**
     * The element of this view.
     *
     * @type {string} CSS selector
     */
    el: '#languages',

    /**
     * Registered events of this view.
     *
     * @type {Object}
     */
    events: {
      'tSortable-change': 'onSort'
    },

    /**
     * Holds a reference to the #LanguageIndexForm.
     *
     * @type {jQuery}
     */
    $languageIndexForm: null,

    /**
     * Initialization of the view.
     */
    initialize: function() {
      this.$languageIndexForm = $('#LanguageIndexForm');
      this.$el.tSortable({
        handle: 'a.sort',
        placeholder: 'sortable-placeholder',
        opacity: 0.8
      });
    },

    /**
     * onSort event handler
     * Updates the position fields and submits the #LanguageIndexForm.
     */
    onSort: function() {
      var i = 1;
      this.$('tbody > tr').each(function() {
        $(this).find('input.position').first().val(i);
        i++;
      });
      $.ajax({
        url: this.$languageIndexForm.attr('action'),
        data: this.$languageIndexForm.serialize(),
        type: 'post'
      });
    }

  });
});
