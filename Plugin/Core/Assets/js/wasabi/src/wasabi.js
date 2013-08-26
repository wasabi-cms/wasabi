//window.Wasabi = window.Wasabi || {};
goog.provide('wasabi');
goog.require('wasabi.core');

/**
 * Public wrapper for Core _translateEntity.
 *
 * @param {string} entity
 * @returns {string}
 */
wasabi.translateEntity = function(entity) {
  return wasabi.run.core.translateEntity(entity);
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
