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

class WasabiNav {

	protected static $_rawItems = array();

	/**
	 * @var array|bool
	 */
	protected static $_orderedItems = false;

	/**
	 * @param string $alias
	 * @param array $options
	 * @throws CakeException
	 */
	public static function addPrimary($alias, $options) {
		if (isset(self::$_rawItems[$alias])) {
			throw new CakeException(__d('core', 'A primary Menu Item with alias "' . $alias . '" already exists.'));
		}

		$item = array(
			'name' => $options['name'],
			'alias' => $alias,
			'priority' => $options['priority'],
			'children' => array()
		);

		if (isset($options['url']) !== false) {
			$item['url'] = $options['url'];
		}

		self::$_rawItems[$alias] = $item;
		self::$_orderedItems = false;
	}

	/**
	 * @param string $parentAlias
	 * @param array $items
	 * @throws CakeException
	 */
	public static function addSecondary($parentAlias, $items) {
		if (!isset(self::$_rawItems[$parentAlias])) {
			throw new CakeException(__d('core', 'The parent alias "' . $parentAlias . '" does not exist.'));
		}
		if (!isset($items[0])) {
			$items = array($items);
		}

		foreach ($items as $options) {
			$item = array(
				'name' => $options['name'],
				'priority' => $options['priority'],
				'url' => $options['url']
			);
			self::$_rawItems[$parentAlias]['children'][] = $item;
			self::$_orderedItems = false;
		}
	}

	protected static function _orderByPriority() {
		if (self::$_orderedItems !== false) {
			return;
		}

		foreach (self::$_rawItems as $item) {
			self::$_orderedItems[] = $item;
		}

		self::$_orderedItems = Hash::sort(self::$_orderedItems, '{n}.priority', 'ASC');

		foreach (self::$_orderedItems as &$item) {
			$item['children'] = Hash::sort($item['children'], '{n}.priority', 'ASC');
		}
	}

	public static function getItems() {
		if (empty(self::$_rawItems)) {
			return array();
		}

		self::_orderByPriority();
		return self::$_orderedItems;
	}

	public static function clearItems() {
		self::$_rawItems = array();
	}

}
