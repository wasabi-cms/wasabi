(function($) {
  "use strict";

  var Modal = function(element, options) {
    this.settings = $.extend({}, $.fn.modal.defaults, options);

    this.$body = $('body');
    this.$el = $(element);
    this.$target = $(this.$el.attr('data-target'));
    this.$targetContents = null;

    this.$modal = null;
    this.$modalBackdrop = null;
    this.$modalWrapper = null;
    this.$modalContainer = null;

    this.isOpen = false;

    this._primaryEvents = [];
    this._secondaryEvents = [];

    console.log(this.$target);
    if (this.$target.length > 0) {
      this._buildEvents();
      $.attachEvents(this._primaryEvents);
    }
  };

  Modal.prototype = {

    _buildEvents: function(secondary) {
      this._primaryEvents = [
        [this.$el, {
          click: $.proxy(this._openModal, this)
        }]
      ];

      if (secondary === true) {
        this._secondaryEvents = [
          [this.$modalWrapper, {
            click: $.proxy(function(event) {
              if (event.target === this.$modalWrapper[0]) {
                this._closeModal(event);
              }
            }, this)
          }],
          [this.$modalContainer, '[data-dismiss="modal"]', [
            ['click', $.proxy(this._closeModal, this)]
          ]],
          [$(window), {
            resize: $.proxy(this._onResize, this)
          }]
        ];
      }
    },

    _initDOMElements: function() {
      this.$modalBackdrop = $('<div class="modal-backdrop"></div>').css({
        opacity: this.settings.opacity,
        zIndex: 10000
      });

      this.$modalWrapper = $('<div class="modal-scrollable"></div>').css({
        zIndex: this.$modalBackdrop.css('zIndex') + 1
      });

      this.$modalContainer = $('<div class="modal-container"></div>');
    },

    _resetDOMElements: function() {
      this.$target.append(this.$targetContents);
      this.$targetContents = null;
      this.$modalContainer.remove();
      this.$modalContainer = null;
      this.$body.removeClass('modal-open').removeClass('page-overflow');
      this.$modalWrapper.remove();
      this.$modalWrapper = null;
      this.$modalBackdrop.remove();
      this.$modalBackdrop = null;
    },

    _openModal: function(event) {
      event.preventDefault();
      $.detachEvents(this._primaryEvents);

      this._initDOMElements();
      this._buildEvents(true);
      $.attachEvents(this._secondaryEvents);

      this.$body
        .toggleClass('page-overflow', $(window).height() < this.$body.height())
        .addClass('modal-open');

      this.$targetContents = this.$target.children();

      this.$modalContainer.append(this.$targetContents).css({
        width: this.settings.width,
        position: 'fixed',
        left: -99999,
        top: this.settings.offsetTop
      });
      this.$modalWrapper.append(this.$modalContainer);

      this.$body.append(this.$modalBackdrop).append(this.$modalWrapper);

      this._updatePosition();
      this.$modalContainer.hide();
      this.$modalContainer.fadeIn();
      this.isOpen = true;
    },

    _closeModal: function(event) {
      event.preventDefault();
      $.detachEvents(this._secondaryEvents);

      // fadeout and remove modal from DOM and reset body classes
      $.when.apply(null, [
        this.$modalContainer.fadeOut(200),
        this.$modalBackdrop.fadeOut(200)
      ]).then($.proxy(function() {
        this._resetDOMElements();
        this.isOpen = false;
        $.attachEvents(this._primaryEvents);
      }, this));
    },

    _updatePosition: function() {
      var modalHeight = this.$modalContainer.outerHeight();
      var modalOffsetTop = this.$modalContainer.offset().top - this.$modalWrapper.offset().top;
      var screenHeight = this.$modalWrapper.height();
      var needsVerticalCentering = ((screenHeight - modalHeight - modalOffsetTop) < this.settings.offsetTop);
      if (needsVerticalCentering) {
        if ((modalHeight + 2 * this.settings.padding) <= screenHeight) {
          this.$modalContainer.css({
            top: '50%',
            marginTop: -modalHeight / 2
          });
        } else {
          this.$modalContainer.css({
            top: this.settings.padding,
            marginTop: 0
          });
        }
      } else {
        this.$modalContainer.css({
          top: this.settings.offsetTop,
          marginTop: 0
        });
      }

      this.$modalContainer.css({
        position: 'absolute',
        marginLeft: -this.settings.width / 2,
        left: '50%'
      });
    },

    _onResize: function(event) {
      if (!this.isOpen) {
        return;
      }
      this._updatePosition();
    }

  };

  $.fn.modal = function(options) {
    return this.each(function() {
      if (!$(this).data('Modal')) {
        $(this).data('Modal', new Modal(this, options));
      }
      return $(this).data('Modal');
    });
  };

  $.fn.modal.defaults = {
    width: 400,
    offsetTop: 200,
    opacity: 0.6,
    padding: 20
  };

})(jQuery);
