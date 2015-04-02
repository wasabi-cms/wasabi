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

class Collections {

	protected $_collections = array();

	/**
	 * @param string $collectionId a unique collection identifier
	 * @param array $options
	 */
	public function register($collectionId, $options) {
		$collectionId = (string) $collectionId;
		if (isset($this->_collections[$collectionId])) {
			user_error(__d('core', 'A collection with the id "%s" already exists.', array($collectionId)));
			return;
		}
		$defaults = array(
			'model' => 'Plugin.Model',
			'displayName' => 'A translated Collection Name'
		);
		$options = array_merge($defaults, $options);
		$this->_collections[$collectionId] = $options;
	}

	public function getForSelect() {
		return array_combine(array_keys($this->_collections), Hash::extract($this->_collections, '{s}.displayName'));
	}

	public function getDisplayName($collectionId) {
		if (!isset($this->_collections[$collectionId])) {
			return false;
		}
		return $this->_collections[$collectionId]['displayName'];
	}

	public static function exists($collectionId) {
		return isset(self::instance()->_collections[$collectionId]);
	}

	public static function instance() {
		static $instance;
		if (!$instance) {
			$instance = new Collections();
		}
		return $instance;
	}

}