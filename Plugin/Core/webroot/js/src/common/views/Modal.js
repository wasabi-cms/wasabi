define(['jquery', 'common/views/BaseView', 'hbs!common/templates/Modal', 'common/util/eventify'], function($, BaseView, ModalTemplate, eventify) {

  /**
   * Holds a reference to the body.
   *
   * @type {jQuery}
   */
  var $body = $('body');

  /**
   * Holds a reference to the document.
   *
   * @type {jQuery}
   */
  var $document = $(document);

  /**
   * Holds a reference to the window.
   *
   * @type {jQuery}
   */
  var $window = $(window);

  /**
   * Default settings.
   *
   * @type {Object}
   */
  var defaults = {
    width: 400,
    offsetTop: 200,
    opacity: 0.6,
    padding: 20,
    cssClasses: {
      backdrop: 'modal-backdrop',
      scrollable: 'modal-scrollable',
      container: 'modal-container',
      modalHeader: 'modal-header',
      modalBody: 'modal-body',
      modalFooter: 'modal-footer',
      confirmFooter: 'modal-confirm'
    },
    confirmYes: 'Yes',
    confirmNo: 'No'
  };

  return BaseView.extend({

    /**
     * Holds a reference to the modal backdrop element.
     *
     * @type {jQuery}
     */
    $backdrop: null,

    /**
     * Holds a reference to the modal container element.
     *
     * @type {jQuery}
     */
    $container: null,

    /**
     * Holds references to all close buttons ([data-dismiss="modal"]) of the modal.
     *
     * @type {jQuery}
     */
    $closeButtons: null,

    /**
     * Holds the modal content including backdrop, scrollable wrapper and the modal container.
     *
     * @type {jQuery}
     */
    $modal: null,

    /**
     * An optional element on which modal interaction events should be triggered on.
     *
     * @type {jQuery}
     */
    $notify: null,

    /**
     * Holds a reference to the modal scrollable wrapper of the container.
     *
     * @type {jQuery}
     */
    $scrollable: null,

    /**
     * Holds references to all submit buttons (button[type="submit"]) of the modal.
     *
     * @type {jQuery}
     */
    $submitButtons: null,

    /**
     * Registered events of the modal view.
     *
     * @type {Object}
     */
    events: {
      'click': 'openModal'
    },

    /**
     * Determines if the modal dialogue is a confirm dialogue (true) or not (false).
     *
     * @type {boolean}
     */
    isConfirmModal: false,

    /**
     * Tracks if the modal is currently opened (true) or not (false).
     *
     * @type {boolean}
     */
    isOpened: false,

    /**
     * The method for ajax modals that should submit data.
     *
     * @type {string}
     */
    method: null,

    /**
     * The event name that should be triggered on
     * the $notify element on modal success.
     *
     * @type {string}
     */
    modalSuccessEvent: null,

    /**
     * Modal options
     *
     * @type {Object}
     */
    options: {},

    /**
     * Initialization of the Modal View.
     *
     * @param {Object=} options
     */
    initialize: function(options) {
      this.options = $.extend({}, defaults, options);
    },

    /**
     * Initialize the modal options.
     */
    initModalOptions: function() {
      this.method = this.$el.attr('data-modal-method') || 'post';
      this.isAjax = (this.$el.attr('data-modal-ajax') === '1' || this.$el.attr('data-modal-ajax') === 'true');

      if (this.isAjax) {
        var notify = this.$el.attr('data-modal-notify');
        this.$notify = (notify !== undefined) ? $(notify) : $document;
        this.modalSuccessEvent = this.$el.attr('data-modal-event') || 'modal:success';
      }
    },

    /**
     * Configure the template options that are used to
     * render the ModalTemplate (hbs).
     *
     * @returns {Object}
     */
    createTemplateOptions: function() {
      var options = {}, hasTarget, $target;

      options.modalHeader = this.$el.attr('data-modal-title');
      options.hasHeader = (options.modalHeader !== undefined);

      options.modalBody = this.$el.attr('data-modal-body');
      options.hasBody = (options.modalBody !== undefined);

      if (!options.hasBody) {
        $target = $(this.$el.attr('data-modal-target'));
        hasTarget = ($target.length > 0);
        if (hasTarget) {
          options.modalBody = $target.html();
          options.hasBody = true;
        }
      }

      options.isConfirmModal = (this.$el.attr('data-toggle') === 'confirm');
      if (options.isConfirmModal) {
        options.hasFooter = true;
      } else {
        options.modalFooter = this.$el.attr('data-modal-footer');
        options.hasFooter = (options.modalFooter !== undefined);
      }

      options.action = this.$el.attr('data-modal-action') || this.$el.attr('href') || false;
      options.method = this.method;
      options.cssClasses = this.options.cssClasses;
      options.confirmYes = this.options.confirmYes;
      options.confirmNo = this.options.confirmNo;

      return options;
    },

    /**
     * Initialize all modal elements.
     */
    initModalElements: function() {
      this.$modal = $(ModalTemplate(this.createTemplateOptions()));

      this.$backdrop = this.$modal.find('.' + this.options.cssClasses.backdrop);
      this.$backdrop.css({
        opacity: this.options.opacity
      });

      this.$scrollable = this.$modal.find('.' + this.options.cssClasses.scrollable);

      this.$container = this.$modal.find('.' + this.options.cssClasses.container);
      this.$container.css({
        width: this.options.width,
        position: 'fixed',
        left: -99999,
        top: this.options.offsetTop,
        opacity: 0,
        zIndex: 10000
      });

      this.$closeButtons = this.$modal.find('[data-dismiss="modal"]');
      this.$submitButtons = this.$modal.find('button[type="submit"]');
    },

    /**
     * Update the modal position.
     */
    updatePosition: function() {
      var modalHeight = this.$container.outerHeight();
      var modalOffsetTop = this.$container.offset().top - this.$scrollable.offset().top;
      var screenHeight = this.$scrollable.height();
      var needsVerticalCentering = ((screenHeight - modalHeight - modalOffsetTop) < this.options.offsetTop);

      $body.toggleClass('page-overflow', $window.height() < $document.height());

      if (needsVerticalCentering) {
        if ((modalHeight + 2 * this.options.padding) <= screenHeight) {
          this.$container.css({
            top: '50%',
            marginTop: -modalHeight / 2
          });
        } else {
          this.$container.css({
            top: this.options.padding,
            marginTop: 0
          });
        }
      } else {
        this.$container.css({
          top: this.options.offsetTop,
          marginTop: 0
        });
      }

      this.$container.css({
        position: 'absolute',
        marginLeft: -this.options.width / 2,
        left: '50%'
      });
    },

    /**
     * Reset the body and $modal references.
     */
    resetDOM: function() {
      $body.removeClass('modal-open').removeClass('page-overflow');
      this.$modal = null;
      this.$container = null;
      this.$scrollable = null;
      this.$backdrop = null;
    },

    /**
     * onResize is called if the window gets resized.
     * If the modal is currently opened, then its position will be updated.
     */
    onResize: function() {
      this.isOpened && this.updatePosition();
    },

    submit: function(event) {
      var $target = $(event.target), that = this;

      if ($target.attr('disabled') !== undefined) {
        return;
      }

      if (this.isAjax) {
        event.preventDefault();
        $target.prop('disabled', true);
        this.$notify.trigger('modal:beforeAjax');
        $.ajax({
          type: this.method,
          url: this.action,
          cache: false,
          success: function(data) {
            that.closeModal();
            that.$notify.trigger(that.modalSuccessEvent, data);
          }
        });
      } else {
        this.closeModal();
      }
    },

    /**
     * Open the modal.
     *
     * @param {Object=} event
     */
    openModal: function(event) {
      event && event.preventDefault();

      if (this.isOpened) return;
      this.isOpened = true;

      this.initModalOptions();
      this.initModalElements();

      $body
        .addClass('modal-open')
        .append(this.$modal);

      this.updatePosition();
      this.$container.hide().css('opacity', 1).fadeIn();

      this.listenTo(eventify($window), 'resize', _.bind(this.onResize, this));
      this.listenTo(eventify(this.$closeButtons), 'click', _.bind(this.closeModal, this));
      this.listenTo(eventify(this.$scrollable), 'click', _.bind(this.closeModal, this));
      this.listenTo(eventify(this.$submitButtons), 'click', _.bind(this.submit, this));
    },

    /**
     * Close the modal.
     * Fades out the backdrop and modal container, resets the body classes and detaches the
     * window resize event listener.
     *
     * @param {Object=} event
     */
    closeModal: function(event) {
      if (event) {
        if (event.target !== event.currentTarget) {
          return;
        }
        event.preventDefault();
      }

      this.stopListening(eventify($window), 'resize', _.bind(this.onResize, this));
      this.stopListening(eventify(this.$closeButtons), 'click', _.bind(this.closeModal, this));
      this.stopListening(eventify(this.$scrollable), 'click', _.bind(this.closeModal, this));
      this.stopListening(eventify(this.$submitButtons), 'click', _.bind(this.submit, this));

      $.when.apply(null, [
        this.$container.fadeOut(200),
        this.$backdrop.fadeOut(200)
      ]).then($.proxy(function() {
        this.$modal.remove();
        this.resetDOM();
        this.isOpened = false;
      }, this));
    }

  });
});
