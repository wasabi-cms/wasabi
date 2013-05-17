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
 * @property CHtmlHelper $CHtml
 */
class NavigationHelper extends AppHelper {

	/**
	 * Helpers used by this helper
	 *
	 * @var array
	 */
	public $helpers = array(
		'Html',
		'CHtml' => array(
			'className' => 'Core.CHtml'
		)
	);

	/**
	 * Render provided $items as <li> elements
	 *
	 * @param array $items
	 * @param string $activeClass css class to add to active items
	 * @return string
	 */
	public function render($items, $activeClass = 'active') {
		$out = '';
		foreach ($items as $item) {
			$class = '';
			if (isset($item['active']) && $item['active'] === true) {
				$class = ' class="' . $activeClass . '"';
			}
			$out .= '<li' . $class . '>';
			$out .= $this->CHtml->backendLink($item['name'], $item['url']);
			$out .= '</li>';
		}
		return $out;
	}

}
