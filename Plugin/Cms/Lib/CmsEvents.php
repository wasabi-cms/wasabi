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
 * @subpackage    Wasabi.Plugin.Cms.Lib
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Router', 'Routing');
App::uses('WasabiNav', 'Core.Lib');
App::uses('CmsPage', 'Cms.Model');

class CmsEvents {

	public $implements = array(
		'Backend.Menu.load' => array(
			'method' => 'loadBackendMenu',
			'priority' => 0
		),
		'Backend.MenuItems.getAvailableLinks' => array(
			'method' => 'getAvailableLinksForMenuItem',
			'priority' => 50000
		)
	);

	public static function loadBackendMenu(WasabiEvent $event) {
		$main = WasabiNav::getMenu('main');

		$main
			->addMenuItem(array(
				'alias' => 'pages',
				'name' => __d('cms', 'Pages'),
				'priority' => 100,
				'parent' => 'core_content',
				'url' => array(
					'plugin' => 'cms',
					'controller' => 'cms_pages',
					'action' => 'index'
				)
			))
			->addMenuItem(array(
				'alias' => 'modules',
				'name' => __d('cms', 'Modules'),
				'priority' => 200,
				'parent' => 'core_content',
				'url' => array(
					'plugin' => 'cms',
					'controller' => 'cms_modules',
					'action' => 'index'
				)
			))
			->addMenuItem(array(
				'alias' => 'settings',
				'name' => __d('cms', 'Settings'),
				'priority' => 400,
				'parent' => 'core_content',
				'url' => array(
					'plugin' => 'cms',
					'controller' => 'cms_settings',
					'action' => 'edit'
				)
			));
	}

	public static function getAvailableLinksForMenuItem(WasabiEvent $event) {
		$CmsPage = ClassRegistry::init('Cms.CmsPage');
		$pages = $CmsPage->generateTreeList();

		if (!$pages) {
			return array();
		}

		/** @var string $group */
		$group = __d('core', 'Cms Pages');

		$results = array(
			$group => array()
		);

		foreach ($pages as $id => $name) {
			$key = 'Object|Cms.CmsPage|' . $id . '|plugin:cms/controller:cms_pages_frontend/action:view';
			$results[$group][$key] = $name;
		}

		return $results;

//		array(
//			'Cms Pages' => array(
//				'Object|Cms.CmsPage|23|plugin:cms/controller:cms_pages_frontend/action:view' => 'Test',
//				'Object|Cms.CmsPage|24|plugin:cms/controller:cms_pages_frontend/action:view/param1:jojo/param2:blub?query1:yeoman/query2:heya' => '_Test'
//			)
//		)
	}

}