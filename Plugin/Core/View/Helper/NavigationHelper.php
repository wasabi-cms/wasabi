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

class NavigationHelper extends AppHelper {

	/**
	 * Helpers used by this helper
	 *
	 * @var array
	 */
	public $helpers = array(
		'Html'
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
		// TODO: implement AuthorizorComponent and static Authorizor wrapper class
		//$group_id = Authenticator::get('Group.id');
		//$group_permissions = Authorizor::getPermissions($group_id);
		foreach ($items as $item) {
			#if (($group_id !== 1) && !in_array($item['path'], $group_permissions)) {
			#	continue;
			#}
			$class = '';
			if (isset($item['active']) && $item['active'] === true) {
				$class = ' class="' . $activeClass . '"';
			}
			$out .= '<li' . $class . '>';
			$out .= $this->Html->link($item['name'], $item['url']);
			$out .= '</li>';
		}
		return $out;
	}

}
