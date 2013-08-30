//window.Wasabi = window.Wasabi || {};
goog.provide('wasabi');
goog.require('wasabi.core');

/**
 * Public wrapper for Core _translate.
 *
 * @param {string} message
 * @param {Array=} context
 * @returns {string}
 */
wasabi.i18n = function(message, context) {
  return wasabi.run.core.translate(message, context);
};

/**
 * Public wrapper for _flashMessage.
 *
 * @param {string|jQuery} elAfter The element after which the message should be rendered.
 * @param {string}        cls     The css class of the flash message.
 * @param {string}        message The content of the flash message.
 */
wasabi.flash = function(elAfter, cls, message) {
  wasabi.run.core.flash(elAfter, cls, message);
};
