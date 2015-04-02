define(['jquery', 'common/views/BaseView', 'Spinner', 'common/constants/SpinPresets', 'jquery.color'], function($, BaseView, Spinner, SpinPresets) {

  return BaseView.extend({

    /**
     * The element of this view.
     *
     * @type {string} CSS selector
     */
    el: '#GroupPermissionIndexForm',

    /**
     * Registered events of this view.
     *
     * @type {Object}
     */
    events: {
      'click .single-submit': 'onSingleSubmit'
    },

    /**
     * The api endpoint where single permission changes are submitted to.
     *
     * @type {string}
     */
    singleSubmitEndpoint: null,

    /**
     * Initialization of the view.
     */
    initialize: function() {
      this.singleSubmitEndpoint = this.$el.attr('action');
    },

    onSingleSubmit: function(event) {
      event.preventDefault();

      var $target = $(event.target);
      $target.hide().blur();

      var spinner = new Spinner(SpinPresets.small);
      var $spinHolder = $('<div/>').css({
        position: 'relative'
      });
      $target.parent().append($spinHolder);
      spinner.spin($spinHolder[0]);

      $.ajax({
        type: 'post',
        url: this.singleSubmitEndpoint,
        data: $target.parent().parent().find('input').serialize(),
        cache: false,
        success: function() {
          var $tr = $target.parent().parent();
          var bgColor = $target.parent().css('backgroundColor');
          spinner.stop();
          $spinHolder.remove();
          $target.show();
          $tr.find('td:not(.controller)').stop().css({
            backgroundColor: '#fff7d9'
          }).animate({
            backgroundColor: bgColor
          }, 1000, function() {
            $(this).css({backgroundColor: ''});
          });
        }
      });
    }

  });
});
