(function($) {
  "use strict";

  var LightModal = function(element, options) {
    var that = this;

    this.source = $(element);
    this.settings = $.extend({}, $.fn.lightModal.defaults, options);

    this.source.click(function(event) {
      event.preventDefault();
      that.initModal();
    });

    /**
     * Initialize a new overlay
     * takes a callback function that is executed when the overlay fadein is complete
     */
    this.initOverlay = function(callback) {
      this.overlay = $('<div id="lightmodal_overlay"></div>');
      this.overlay.css({
        'opacity': this.settings.opacity,
        'zIndex': 10000
      });
      this.overlay.click(function() {
        that.closeModal();
      });
      $('body').append(this.overlay);
      this.overlay.fadeIn(300, callback);
    };

    /**
     * Initialize the modal dialog
     */
    this.initModal = function() {
      that.initOverlay(function() {
        that.modalBody = $('<div id="lightmodal_body"></div>');
        that.modalCloseButton = $('<a href="#" class="lightmodal-close"></a>');
        that.modalContainer = $('<div id="lightmodal_container"></div>');
        that.modalContent = $('<div id="lightmodal_content"></div>');
        that.modalHeader = $('<div id="lightmodal_header"></div>');

        that.initModalHeader();

        if (that.settings.type == 'confirm') {
          var action = that.source.attr('data-confirm-action') || false;
          var confirmMessage = that.source.attr('data-confirm') || false;
          if (confirmMessage !== false) {
            that.modalBody.append($('<p></p>').html(confirmMessage));
          }
          if (action !== false) {
            var form = $('<form></form>').attr('accept-charset', 'utf-8').attr('method', 'post').attr('action', action);
            var submitButton = $('<button type="submit"></button>').addClass('lightmodal-confirm button green primary').html($.translateEntity('Yes'));
            var cancelLink = $('<a></a>').attr('href', '#').addClass('lightmodal-close button danger').html($.translateEntity('No'));
            var confirmBar = $('<div id="lightmodal_confirm_bar"></div>');

            confirmBar.append(form.append(submitButton).append(cancelLink));

            that.modalBody.append(confirmBar);
            that.modalBody.find('.lightmodal-confirm').click(function(event) {
              $(this).attr('disabled', 'disabled');
            });
          }
        }

        that.modalContent.append(that.modalBody);
        that.modalContainer.append(that.modalContent);

        that.modalContainer.find('.lightmodal-close').click(function(event) {
          event.preventDefault();
          that.closeModal();
        });

        that.modalContainer.css({
          'display': 'block',
          'opacity': 0,
          'zIndex': 10001,
          'width': that.settings.width
        });
        $('body').append(that.modalContainer);

        that.adjustModalPosition();

        that.modalContainer.css({
          'display': 'none',
          'opacity': 1
        });
        that.modalContainer.fadeIn(300);
      });
    };

    this.initModalHeader = function() {
      var modalTitle = that.source.attr('data-modal-title') || false;
      var modalSubTitle = that.source.attr('data-modal-subtitle') || false;

      if (modalTitle !== false) {
        that.modalHeader.append($('<h2></h2>').html(modalTitle));
      }

      if (modalSubTitle !== false) {
        that.modalHeader.append($('<p></p>').html(modalSubTitle));
      }

      if (modalTitle !== false || modalSubTitle !== false) {
        if (that.settings.showCloseButton === true) {
          that.modalHeader.append(that.modalCloseButton);
        }
        that.modalContent.append(that.modalHeader);
      }
    };

    this.adjustModalPosition = function() {
      if (that.settings.verticalPosition === 'center') {
        that.modalContainer.css({
          'top': '50%',
          'marginTop': -that.modalContainer.outerHeight() / 2 + 'px'
        });
      } else {
        that.modalContainer.css({
          'top': that.settings.verticalPosition + 'px'
        });
      }

      that.modalContainer.css({
        'position': 'fixed',
        'left': '50%',
        'marginLeft': -that.modalContainer.outerWidth() / 2 + 'px'
      });
    };

    /**
     * Close the modal dialog
     */
    this.closeModal = function() {
      // hide modal container
      this.modalContainer.fadeOut(200, function() {
        // destroy modal container
        that.modalContainer.remove();
      });
      // hide overlay
      this.overlay.fadeOut(200, function() {
        // remove overlay from DOM
        that.overlay.remove();
      });
      console.log('close triggered');
    };

    return this;
  };

  $.fn.lightModal = function(options) {
    return this.each(function(key, value) {
      if (!$(this).data('lightModal')) {
        $(this).data('lightModal', new LightModal(this, options));
      }
      return $(this).data('lightModal');
    });
  };

  $.fn.lightModal.defaults = {
    width: 400,
    horizontalPosition: 'center',
    verticalPosition: 200,
    content: 'Default Modal Content',
    opacity: 0.5,
    showCloseButton: true,
    type: 'confirm'
  };

})(jQuery);

$(function() {

  $('.user-menu').hover(function() {
    $(this).addClass('hover');
    $(this).find('ul').show();
  }, function() {
    $(this).find('ul').hide();
    $(this).removeClass('hover');
  });

  $('a.confirm').lightModal({
    content: 'blub',
    type: 'confirm'
  });

  $('.list tr').hover(function() {
    $(this).addClass('hover');
  }, function() {
    $(this).removeClass('hover');
  });

  $('#languages').sortable({
    handle: 'a.sort',
    items: 'tr',
    placeholder: 'sortable-placeholder',
    forcePlaceholderSize: true,
    opacity: 0.8,
    helper: function(e, tr) {
      var originals = tr.children();
      var helper = tr.clone();
      helper.children().each(function(index) {
        $(this).width(originals.eq(index).outerWidth());
      });
      return helper;
    },
    start: function(event, ui) {
      ui.placeholder.html('<td colspan="7"></td>');
    },
    stop: function(event, ui) {
      var i = 1;
      $(this).find('tbody tr').each(function(index) {
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
  });

  /**
   * Default Ajax options
   */
  $.ajaxSetup({
    dataType: 'json'
  });

});

(function($) {

  $.extend({

    translateEntity: function(entity) {
      if (wasabiTranslations[entity] !== undefined) {
        return wasabiTranslations[entity];
      } else {
        return entity;
      }
    }

  });

})(jQuery);