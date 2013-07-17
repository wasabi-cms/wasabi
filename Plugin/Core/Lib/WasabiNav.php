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
	 * @var WasabiMenu[]
	 */
	protected static $_menus = array();

	/**
	 * @param $alias
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
	 * @param $alias
	 * @param bool $orderedArray
	 * @return array|WasabiMenu
	 * @throws CakeException
	 */
	public static function getMenu($alias, $orderedArray = false) {
		if (!isset(self::$_menus[$alias])) {
			throw new CakeException(__d('core', 'No menu with alias "' . $alias . '" does exist.'));
		}
		if (!$orderedArray) {
			return self::$_menus[$alias];
		}

		return self::$_menus[$alias]->getOrderedArray();
	}

	public static function clear() {
		self::$_menus = array();
	}


}
