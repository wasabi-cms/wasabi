<?php
/**
 *
 * PHP 5
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the below copyright notice.
 *
 * @copyright     Copyright 2013, Frank Förster (http://frankfoerster.com)
 * @link          http://github.com/frankfoerster/wasabi
 * @package       Wasabi
 * @subpackage    Wasabi.Plugin.Cms.View
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('View', 'View');

/**
 * @property CmsPageHelper $CmsPage
 * @property MetaHelper $Meta
 * @property MenuHelper $Menu
 * @property WasabiAssetHelper $WasabiAsset
 */

class CmsPageView extends View {

	/**
	 * Returns layout filename for this template as a string.
	 *
	 * @param string $name The name of the layout to find.
	 * @return string Filename for layout file (.ctp).
	 * @throws CakeException if no layoutPath is present
	 * @throws MissingLayoutException when a layout cannot be located
	 */
	protected function _getLayoutFileName($name = null) {
		if ($name === null) {
			$name = $this->layout;
		}

		if (is_null($this->layoutPath)) {
			throw new CakeException('Please provide $this->layoutPath in your Controller.');
		}

		$layout = $this->layoutPath . DS . $name . '.ctp';

		if (file_exists($layout)) {
			return $layout;
		}

		throw new MissingLayoutException(array('file' => $layout));
	}

	/**
	 * Finds an element filename, returns false on failure.
	 *
	 * @param string $name The name of the element to find.
	 * @return mixed Either a string to the element filename or false when one can't be found.
	 */
	protected function _getElementFileName($name) {
		if (is_null($this->layoutPath)) {
			return parent::_getElementFileName($name);
		}

		$element = $this->layoutPath . DS . 'Elements' . DS . $name . '.ctp';
		if (file_exists($element)) {
			return $element;
		}

		$element = dirname($this->_current) . DS . 'Elements' . DS . $name . '.ctp';
		if (file_exists($element)) {
			return $element;
		}

		return parent::_getElementFileName($name);
	}

}
