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
 * @subpackage    Wasabi.Plugin.Core.View.Helper
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppHelper', 'View/Helper');

/**
 * @property HtmlHelper $Html
 * @property CoreView $_View
 */

class CMenuHelper extends AppHelper {

	/**
	 * Helpers used by this helper.
	 *
	 * @var array
	 */
	public $helpers = array(
		'Html'
	);

	public function renderMenuTree($menuItems, $level = null) {
		$output = '';

		$depth = ($level !== null) ? $level : 1;

		foreach ($menuItems as $menuItem) {
			$classes =  array('menu-item');
			$menuItemRow = $this->_View->element('menus/menu_item_row', array(
				'menuItem' => $menuItem['MenuItem'],
				'level' => $level
			));

			if (!empty($menuItem['children'])) {
				$menuItemRow .= '<ul>' . $this->renderMenuTree($menuItem['children'], $depth + 1) . '</ul>';
			} else {
				$classes[] = 'no-children';
			}
			$output .= '<li class="' . join(' ', $classes) . '" data-menu-item-id="' . $menuItem['MenuItem']['id'] . '">' . $menuItemRow . '</li>';
		}

		return $output;
	}
}