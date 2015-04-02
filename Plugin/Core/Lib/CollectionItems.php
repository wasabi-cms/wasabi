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
 * @subpackage    Wasabi.Plugin.Core.Lib
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class CollectionItems {

	protected $_collectionItems = array();

	/**
	 * @param string $itemId a unique identifier for a collection item
	 * @param array $options
	 */
	public function register($itemId, $options) {
		$itemId = (string) $itemId;
		if (isset($this->_collectionItems[$itemId])) {
			user_error(__d('core', 'A collection with the id "%s" already exists.', array($itemId)));
			return;
		}
		$defaults = array(
			'model' => 'Plugin.Model',
			'displayName' => 'A translated Collection Name'
		);
		$options = array_merge($defaults, $options);
		$this->_collectionItems[$itemId] = $options;
	}

	public function getForSelect() {
		return array_combine(array_keys($this->_collectionItems), Hash::extract($this->_collectionItems, '{s}.displayName'));
	}

	public function getDisplayName($itemId) {
		if (!isset($this->_collectionItems[$itemId])) {
			return false;
		}
		return $this->_collectionItems[$itemId]['displayName'];
	}

	public static function exists($itemId) {
		return isset(self::instance()->_collectionItems[$itemId]);
	}

	public static function instance() {
		static $instance;
		if (!$instance) {
			$instance = new CollectionItems();
		}
		return $instance;
	}

}