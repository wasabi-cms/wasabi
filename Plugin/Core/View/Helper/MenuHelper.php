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
App::uses('Hash', 'Utility');

/**
 * @property HtmlHelper $Html
 * @property CoreView $_View
 */

class MenuHelper extends AppHelper {

	/**
	 * Helpers used by this helper.
	 *
	 * @var array
	 */
	public $helpers = array(
		'Html'
	);

	public function render($menuId = null, $options = array()) {
		if ($menuId === null) {
			return '';
		}
		$defaults = array(
			'maxDepth' => 2,
			'separator' => 'li',
			'path' => array(),
			'hasChildrenClass' => 'has-children'
		);
		$options = array_merge($defaults, $options);
		App::uses('MenuItem', 'Core.Model');
		$menuItems = MenuItem::instance()->find('publishedThreaded', array('menu' => $menuId));
		return $this->_renderTreeLevel($menuItems, $options);
	}

	protected function _renderTreeLevel($menuItems, $options, $depth = 0, &$subActiveFound = false) {
		$output = '';

		foreach ($menuItems as $menuItem) {
			$classes = [];
			$content = $this->_renderMenuLink($menuItem['MenuItem']);
			if ($this->_isActive($menuItem['MenuItem'])) {
				$classes[] = 'active';
				$subActiveFound = true;
			}

			if (!empty($menuItem['children']) && ($depth + 1 <= $options['maxDepth'])) {
				$sub = false;
				$classes[] = $options['hasChildrenClass'];

				$content .= '<ul>';
				$content .= $this->_renderTreeLevel($menuItem['children'], $options, $depth + 1, $sub);
				$content .= '</ul>';

				if ($sub === true) {
					$classes[] = 'active';
					$subActiveFound = true;
				}
			}

			if (!empty($classes)) {
				$output .= '<li class="' . join(' ', array_unique($classes)) . '">';
			} else {
				$output .= '<li>';
			}
			$output .= $content;
			$output .= '</li>';
		}

		return $output;
	}

	protected function _renderMenuLink($menuItem) {
		$output = '';

		switch ($menuItem['type']) {

			case MenuItem::TYPE_EXTERNAL_LINK:
				$output .= '<a href="' . $menuItem['external_link'] . '" title="' . $menuItem['name'] . '">' . $menuItem['name'] . '</a>';
				break;

			default:
				$output .= $this->Html->link($menuItem['name'], array(
					'page_id' => $menuItem['foreign_id'],
					'language_id' => Configure::read('Wasabi.content_language.id')
//					'plugin' => $menuItem['plugin'],
//					'controller' => $menuItem['controller'],
//					'action' => $menuItem['action'],
//					$menuItem['foreign_id'],
//					Configure::read('Wasabi.content_language.id')
				), array('title' => $menuItem['name']));
				break;
		}

		return $output;
	}

	protected function _isActive($menuItem) {
		switch ($menuItem['type']) {

			case MenuItem::TYPE_OBJECT:
				if ($menuItem['foreign_model'] !== '') {
					list($plugin, $model) = pluginSplit($menuItem['foreign_model']);
					App::uses($model, $plugin . '.Model');
					if (method_exists($model, 'isActive')) {
						return $model::isActive($menuItem, $this->request->params);
					}
				}
				break;

		}
		return false;
	}
}
