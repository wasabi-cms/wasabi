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

class WasabiMenu {

	/**
	 * @var string
	 */
	public $alias;

	/**
	 * @var array
	 */
	protected $_menuItems;

	/**
	 * @var array
	 */
	protected $_orderedItems;

	/**
	 * Constructor
	 *
	 * @param string $alias
	 */
	public function __construct($alias) {
		$this->alias = $alias;
	}

	/**
	 * Add a new menu item
	 *
	 * @param array $options
	 * @return $this
	 * @throws CakeException
	 */
	public function addMenuItem($options) {
		if (!isset($options['alias']) || (isset($options['alias']) && $options['alias'] === '')) {
			throw new CakeException('$options[\'alias\'] is missing.');
		}
		if (!isset($options['name']) || (isset($options['name']) && $options['name'] === '')) {
			throw new CakeException('$options[\'name\'] is missing.');
		}
		if (!isset($options['priority']) || (isset($options['priority']) && $options['priority'] === '')) {
			throw new CakeException('$options[\'priority\'] is missing.');
		}

		$menuItem = array(
			'alias' => $options['alias'],
			'name' => $options['name'],
			'priority' => $options['priority']
		);

		if (isset($options['icon'])) {
			$menuItem['icon'] = $options['icon'];
		}

		if (isset($options['url']) && is_array($options['url']) && !empty($options['url'])) {
			$url = $options['url'];
			if (!isset($url['plugin']) || (isset($url['plugin']) && $url['plugin'] === '')) {
				$url['plugin'] = null;
			}
			if (!isset($url['controller'])) {
				throw new CakeException('$options[\'url\'][\'controller\'] is missing.');
			}
			if (!isset($url['action'])) {
				throw new CakeException('$options[\'url\'][\'action\'] is missing.');
			}
			$menuItem['url'] = $url;
		}

		if (isset($options['parent'])) {
			$parts = preg_split('/\//', $options['parent']);
			if (count($parts) === 1) {
				if (!isset($this->_menuItems[$parts[0]])) {
					throw new CakeException('No menu item with the alias specified in $options[\'parent\'] exists.');
				}
				$menuItem['parent'] = $options['parent'];
				$this->_menuItems[$parts[0]]['children'][$menuItem['alias']] = $menuItem;
			}
			if (count($parts) === 2) {
				if (!isset($this->_menuItems[$parts[0]]['children'][$parts[1]])) {
					throw new CakeException('No menu item with the alias specified in $options[\'parent\'] exists.');
				}
				$menuItem['parent'] = $options['parent'];
				$this->_menuItems[$parts[0]]['children'][$parts[1]]['children'][$menuItem['alias']] = $menuItem;
			}
		} else {
			$this->_menuItems[$menuItem['alias']] = $menuItem;
		}

		return $this;
	}

	/**
	 * Create and return an array clone of menu items ordered by their priority.
	 *
	 * @param array $items
	 * @return array
	 */
	public function getOrderedArray($items = array()) {
		$ordered = array();
		if (empty($items)) {
			$items = $this->_menuItems;
		}

		foreach ($items as $item) {
			$ordered[] = $item;
		}

		$ordered = Hash::sort($ordered, '{n}.priority', 'ASC');

		foreach ($ordered as &$item) {
			if (isset($item['children']) && !empty($item['children'])) {
				$item['children'] = $this->getOrderedArray($item['children']);
			}
		}

		return $ordered;
	}

}