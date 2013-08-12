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

	public function renderNested($items, $activeClass = 'active', $openClass = 'open', $subNavClass = 'sub-nav') {
		$out = '';
		foreach ($items as $item) {
			$cls = array();
			if (isset($item['active']) && $item['active'] === true) {
				$cls[] = $activeClass;
			}
			if (isset($item['open']) && $item['open'] === true) {
				$cls[] = $openClass;
			}
			$out .= '<li' . ((count($cls) > 0) ? ' class="' . join(' ', $cls) . '"' : '') . '>';
			if (isset($item['url'])) {
				$options = array();
				if (isset($item['icon'])) {
					$item['name'] = '<i class="' . $item['icon'] . '"></i><span class="item-name">' . $item['name'] . '</span>';
					$options['escape'] = false;
				}
				$out .= $this->CHtml->backendLink($item['name'], $item['url'], $options);
			} else {
				$out .= '<a href="javascript:void(0)">';
				if (isset($item['icon'])) {
					$out .= '<i class="' . $item['icon'] . '"></i>';
				}
				$out .= '<span class="item-name">' . $item['name'] . '</span>';
				if (isset($item['children']) && !empty($item['children'])) {
					$out .= ' <i class="arrow"></i>';
				}
				$out .= '</a>';
			}
			if (isset($item['children']) && !empty($item['children'])) {
				$out .= '<ul class="' . $subNavClass . '">';
				$out .= $this->renderNested($item['children']);
				$out .= '</ul>';
			}
			$out .= '</li>';
		}

		return $out;
	}

}
