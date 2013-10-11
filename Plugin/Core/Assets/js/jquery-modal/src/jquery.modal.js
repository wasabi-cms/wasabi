(function($, doc) {
  "use strict";

  /**
   * Modal
   * 
   * @param {HTMLElement} element
   * @param {Object} options
   * @constructor
   */
  var Modal = function(element, options) {

    /**
     * Options/Settings for this modal instance.
     * 
     * @type {Object}
     */
    this.o = $.extend({}, $.fn.modal.defaults, options);

    /**
     * The jQuery object on which the modal is instantiated on.
     * 
     * @type {jQuery}
     */
    this.$el = $(element);

    /**
     * Determines if the modal dialogue is a confirm dialogue (true) or not (false).
     *
     * @type {Boolean}
     */
    this.isConfirm = (this.$el.attr('data-toggle') === 'confirm');

    this.$target = $(this.$el.attr('data-target'));
    this.$targetContents = null;

    /**
     * Holds a reference to the modal backdrop jQuery object.
     *
     * @type {jQuery}
     */
    this.$backdrop = null;

    /**
     * Holds a reference to the modal scrollable jQuery object.
     *
     * @type {jQuery}
     */
    this.$scrollable = null;

    /**
     * Holds a reference to the modal container jQuery object.
     *
     * @type {jQuery}
     */
    this.$container = null;

    /**
     * Determines if the modal is open (true) or not (false).
     *
     * @type {Boolean}
     */
    this.isOpen = false;

    this.header = false;

    this.body = false;

    this.footer = false;

    this.isAjax = false;

    this.$notify = false;

    this.event = false;

    this.method = false;

    this.action = false;

    this._primaryEvents = [];
    this._secondaryEvents = [];

    this.useTarget = (this.$target.length > 0);

    this.init();
  };

  /**
   * Modal Prototype
   */
  Modal.prototype = (function() {

    /**
     * Holds a reference to the body jQuery object.
     * 
     * @type {jQuery}
     */
    var $body = $('body');

    /**
     * 
     * @param {Boolean} secondary
     * @private
     */
    function _buildEvents(secondary) {
      this._primaryEvents = [
        [this.$el, {
          click: $.proxy(_openModal, this)
        }]
      ];

      if (secondary === true) {
        this._secondaryEvents = [
          [this.$scrollable, {
            click: $.proxy(function(event) {
              if (event.target === this.$scrollable[0]) {
                _closeModal.call(this, event);
              }
            }, this)
          }],
          [this.$container, '[data-dismiss="modal"]', [
            ['click', $.proxy(_closeModal, this)]
          ]],
          [this.$container, '[type="submit"]', [
            ['click', $.proxy(_submit, this)]
          ]],
          [$(window), {
            resize: $.proxy(_onResize, this)
          }]
        ];
      }
    }

    /**
     *
     * @private
     */
    function _initDOMElements() {
      this.$backdrop = $('<div/>').addClass(this.o.backdrop).css({
        opacity: this.o.opacity,
        zIndex: 10000
      });

      this.$scrollable = $('<div/>').addClass(this.o.scrollable).css({
        zIndex: this.$backdrop.css('zIndex') + 1
      });

      this.$container = $('<div/>').addClass(this.o.container);
    }

    /**
     *
     * @private
     */
    function _resetDOMElements() {
      if (this.useTarget) {
        this.$target.append(this.$targetContents);
        this.$targetContents = null;
      }
      this.$container.remove();
      this.$container = null;
      $body.removeClass('modal-open').removeClass('page-overflow');
      this.$scrollable.remove();
      this.$scrollable = null;
      this.$backdrop.remove();
      this.$backdrop = null;
    }

    /**
     *
     * @private
     */
    function _initAttributes() {
      var mtitle = this.$el.attr('data-modal-title');
      if (mtitle !== undefined) {
        this.header = mtitle;
      }

      var mbody = this.$el.attr('data-modal-body');
      if (mbody !== undefined) {
        this.body = mbody;
      }

      this.method = this.$el.attr('data-method') || 'post';

      this.isAjax = (this.$el.attr('data-ajax') === '1' || this.$el.attr('data-ajax') === 'true');

      if (this.isAjax) {
        var $notify = this.$el.attr('data-notify');
        this.$notify = $notify ? $($notify) : $(doc);
        this.event = this.$el.attr('data-event') || 'succes.modal';
      }

      this.action = this.$el.attr('data-action') || this.$el.attr('href') || false;
    }

    /**
     *
     * @private
     */
    function _createModalContent() {
      if (this.useTarget) {
        this.$targetContents = this.$target.children();
        this.$container.append(this.$targetContents);
      } else {
        var mtitle = this.$el.attr('data-modal-title');
        var mbody = this.$el.attr('data-modal-body');
        var $header = false;
        var $body = $('<div/>', {class: this.o.body});
        var $footer = false;

        if (mtitle !== undefined && mtitle !== '0') {
          $header = $('<div/>', {class: this.o.header}).append($('<span/>').text(mtitle));
        }

        if (mbody !== undefined) {
          $body.append(mbody);
        }

        if (this.isConfirm) {
          $footer = $('<div/>', {class: this.o.footer + ' ' + this.o.confirmFooter});
          var $btn = $('<button/>', {
            class: 'button',
            type: 'submit'
          });
          $btn.append($('<span/>').text(this.o.confirmYes));
          var $cancel = $('<a href="javascript:void(0)" data-dismiss="modal"/>');
          $cancel.text(this.o.confirmNo);
          $footer.append($btn).append($cancel);
        }

        $header && this.$container.append($header);
        $body && this.$container.append($body);
        $footer && this.$container.append($footer);
      }
    }

    /**
     *
     * @param event
     * @private
     */
    function _openModal(event) {
      event.preventDefault();
      $.detachEvents(this._primaryEvents);

      _initDOMElements.call(this);
      _buildEvents.call(this, true);
      $.attachEvents(this._secondaryEvents);

      $body
        .toggleClass('page-overflow', $(window).height() < $(document).height())
        .addClass('modal-open');

      _initAttributes.call(this);

      _createModalContent.call(this);

      this.$container.css({
        width: this.o.width,
        position: 'fixed',
        left: -99999,
        top: this.o.offsetTop
      });
      this.$scrollable.append(this.$container);

      $body.append(this.$backdrop).append(this.$scrollable);

      _updatePosition.call(this);
      this.$container.hide();
      this.$container.fadeIn();
      this.isOpen = true;
    }

    /**
     *
     * @param event
     * @private
     */
    function _closeModal(event) {
      event && event.preventDefault();
      $.detachEvents(this._secondaryEvents);

      // fadeout and remove modal from DOM and reset body classes
      $.when.apply(null, [
          this.$container.fadeOut(200),
          this.$backdrop.fadeOut(200)
        ]).then($.proxy(function() {
          _resetDOMElements.call(this);
          this.isOpen = false;
          $.attachEvents(this._primaryEvents);
        }, this));
    }

    /**
     *
     * @private
     */
    function _updatePosition() {
      var modalHeight = this.$container.outerHeight();
      var modalOffsetTop = this.$container.offset().top - this.$scrollable.offset().top;
      var screenHeight = this.$scrollable.height();
      var needsVerticalCentering = ((screenHeight - modalHeight - modalOffsetTop) < this.o.offsetTop);
      if (needsVerticalCentering) {
        if ((modalHeight + 2 * this.o.padding) <= screenHeight) {
          this.$container.css({
            top: '50%',
            marginTop: -modalHeight / 2
          });
        } else {
          this.$container.css({
            top: this.o.padding,
            marginTop: 0
          });
        }
      } else {
        this.$container.css({
          top: this.o.offsetTop,
          marginTop: 0
        });
      }

      this.$container.css({
        position: 'absolute',
        marginLeft: -this.o.width / 2,
        left: '50%'
      });
    }

    /**
     *
     * @param event
     * @private
     */
    function _onResize(event) {
      this.isOpen && _updatePosition.call(this);
    }

    /**
     *
     * @param event
     * @private
     */
    function _submit(event) {
      event.preventDefault();

      var $target = $(event.target);
      if ($target.attr('disabled') !== undefined) {
        return;
      }
      
      if (this.isAjax) {
        var that = this;
        $target.attr('disabled', true);
        this.$notify.trigger('beforeAjax.modal');
        $.ajax({
          type: this.method,
          url: this.action,
          cache: false,
          success: function(data) {
            that.$notify.trigger(that.event, data);
          }
        });
      } else {
        $('<form></form>')
          .attr('action', this.action)
          .attr('method', this.method)
          .appendTo('body')
          .submit();
      }

      _closeModal.call(this);
    }

    /**
     * public methods
     */
    return {

      init: function() {
        _buildEvents.call(this);
        $.attachEvents(this._primaryEvents);
      }

    }

  })();

  /**
   * jQuery modal plugin definition
   *
   * @param options
   * @returns {*}
   */
  $.fn.modal = function(options) {
    return this.each(function() {
      if (!$(this).data('Modal')) {
        $(this).data('Modal', new Modal(this, options));
      }
      return $(this).data('Modal');
    });
  };

  /**
   * jQuery modal default settings.
   *
   * @type {object}
   */
  $.fn.modal.defaults = {
    width: 400,
    offsetTop: 200,
    opacity: 0.6,
    padding: 20,
    backdrop: 'modal-backdrop',
    scrollable: 'modal-scrollable',
    container: 'modal-container',
    header: 'modal-header',
    body: 'modal-body',
    footer: 'modal-footer',
    confirmFooter: 'modal-confirm',
    confirmYes: 'Yes',
    confirmNo: 'No'
  };

})(jQuery, document);
