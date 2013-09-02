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

App::uses('WasabiMenu', 'Core.Lib');

class WasabiNav {

	/**
	 * Holds all menus.
	 *
	 * @var WasabiMenu[]
	 */
	protected static $_menus = array();

	/**
	 * Create a new menu.
	 *
	 * @param string $alias the alias of the menu
	 * @return WasabiMenu
	 * @throws CakeException
	 */
	public static function createMenu($alias) {
		if (isset(self::$_menus[$alias])) {
			throw new CakeException(__d('core', 'A Menu with alias "' . $alias . '" already exists.'));
		}
		self::$_menus[$alias] = new WasabiMenu($alias);

		return self::$_menus[$alias];
	}

	/**
	 * Get a WasabiMenu instance or an ordered array
	 * of menu items of a menu.
	 *
	 * @param string $alias the alias of the menu
	 * @param boolean $ordered true: return array of priority ordered menu items, false: return WasabiMenu instance
	 * @return array|WasabiMenu
	 * @throws CakeException
	 */
	public static function getMenu($alias, $ordered = false) {
		if (!isset(self::$_menus[$alias])) {
			throw new CakeException(__d('core', 'No menu with alias "' . $alias . '" does exist.'));
		}
		if (!$ordered) {
			return self::$_menus[$alias];
		}

		return self::$_menus[$alias]->getOrderedArray();
	}

	/**
	 * Get all processed menu items of a menu.
	 *
	 * @param string $alias the alias of the menu
	 * @param array $requestParams the current request params via $this->request->
	 * @return array
	 */
	public static function getProcessedMenu($alias, $requestParams) {
		$items = self::getMenu($alias, true);

		return self::_processMenuItems($items, $requestParams);
	}

	/**
	 * Process provided menu items and add
	 * classes 'active', 'open' depending
	 * on the provided request params.
	 *
	 * @param array $items
	 * @param array $requestParams
	 * @param boolean $subActiveFound
	 * @return array
	 */
	protected static function _processMenuItems($items, $requestParams, &$subActiveFound = false) {
		foreach ($items as &$item) {
			if (isset($item['url']) &&
				$item['url']['plugin'] === $requestParams['plugin'] &&
				$item['url']['controller'] === $requestParams['controller']
			) {
				if ($item['matchAction'] !== true) {
					$item['active'] = true;
					$subActiveFound = true;
				} elseif ($item['url']['action'] === $requestParams['action']) {
					$item['active'] = true;
					$subActiveFound = true;
				}
			}
			if (isset($item['children']) && !empty($item['children'])) {
				$sub = false;

				$item['children'] = self::_processMenuItems($item['children'], $requestParams, $sub);

				if ($sub === true) {
					$item['active'] = true;
					$item['open'] = true;
					$subActiveFound = true;
				}
			}

		}

		return $items;
	}

	/**
	 * Clear all menus.
	 *
	 * @return void
	 */
	public static function clear() {
		self::$_menus = array();
	}
}
