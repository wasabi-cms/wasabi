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

App::uses('ClassRegistry', 'Utility');
App::uses('Router', 'Routing');
App::uses('WasabiNav', 'Core.Lib');

class CoreEvents {

	public $implements = array(
		'Backend.Menu.load' => array(
			'method' => 'loadBackendMenu',
			'priority' => 0
		),
		'Backend.MenuItems.getAvailableLinks' => array(
			'method' => 'getAvailableLinksForMenuItem',
			'priority' => 99999
		),
		'Backend.JS.Translations.load' => array(
			'method' => 'loadJsTranslations',
			'priority' => 100
		),
		'Guardian.GuestActions.load' => array(
			'method' => 'loadGuestActions',
			'priority' => 99999
		)
	);

	public static function loadBackendMenu(WasabiEvent $event) {
		$main = WasabiNav::createMenu('main');

		$main
			->addMenuItem(array(
				'alias' => 'core_dashboard',
				'name' => __d('core', 'Dashboard'),
				'priority' => 1,
				'url' => array(
					'plugin' => 'core',
					'controller' => 'dashboard',
					'action' => 'index'
				),
				'icon' => 'icon-dashboard'
			))
			->addMenuItem(array(
				'alias' => 'core_content',
				'name' => __d('core', 'Content'),
				'priority' => 1000,
				'icon' => 'icon-cms',
			))
			->addMenuItem(array(
				'alias' => 'core_menus',
				'name' => __d('core', 'Menus'),
				'priority' => 2000,
				'url' => array(
					'plugin' => 'core',
					'controller' => 'menus',
					'action' => 'index'
				),
				'icon' => 'icon-menu3',
			))
			->addMenuItem(array(
				'alias' => 'core_media',
				'name' => __d('core', 'Media'),
				'priority' => 3000,
				'url' => array(
					'plugin' => 'core',
					'controller' => 'media',
					'action' => 'index'
				),
				'icon' => 'icon-20-pictures',
			))
			->addMenuItem(array(
				'alias' => 'core_administration',
				'name' => __d('core', 'Administration'),
				'priority' => 4000,
				'icon' => 'icon-cogs'
			))
				->addMenuItem(array(
					'alias' => 'core_users',
					'name' => __d('core', 'Users'),
					'priority' => 100,
					'parent' => 'core_administration',
					'url' => array(
						'plugin' => 'core',
						'controller' => 'users',
						'action' => 'index'
					)
				))
				->addMenuItem(array(
					'alias' => 'core_groups',
					'name' => __d('core', 'Groups'),
					'priority' => 200,
					'parent' => 'core_administration',
					'url' => array(
						'plugin' => 'core',
						'controller' => 'groups',
						'action' => 'index'
					)
				))
				->addMenuItem(array(
					'alias' => 'core_languages',
					'name' => __d('core', 'Languages'),
					'priority' => 300,
					'parent' => 'core_administration',
					'url' => array(
						'plugin' => 'core',
						'controller' => 'languages',
						'action' => 'index'
					)
				))
				->addMenuItem(array(
					'alias' => 'core_plugins',
					'name' => __d('core', 'Plugins'),
					'priority' => 400,
					'parent' => 'core_administration',
					'url' => array(
						'plugin' => 'core',
						'controller' => 'plugins',
						'action' => 'index'
					)
				))
				->addMenuItem(array(
					'alias' => 'core_permissions',
					'name' => __d('core', 'Permissions'),
					'priority' => 500,
					'parent' => 'core_administration',
					'url' => array(
						'plugin' => 'core',
						'controller' => 'permissions',
						'action' => 'index'
					)
				))
			->addMenuItem(array(
				'alias' => 'core_settings',
				'name' => __d('core', 'Settings'),
				'priority' => 5000,
				'icon' => 'icon-16-wrench'
			))
				->addMenuItem(array(
					'alias' => 'core_general_settings',
					'name' => __d('core', 'General'),
					'priority' => 100,
					'parent' => 'core_settings',
					'url' => array(
						'plugin' => 'core',
						'controller' => 'core_settings',
						'action' => 'general'
					),
					'matchAction' => true
				))
				->addMenuItem(array(
					'alias' => 'core_cache_settings',
					'name' => __d('core', 'Cache'),
					'priority' => 200,
					'parent' => 'core_settings',
					'url' => array(
						'plugin' => 'core',
						'controller' => 'core_settings',
						'action' => 'cache'
					),
					'matchAction' => true
				))
				->addMenuItem(array(
					'alias' => 'core_media_settings',
					'name' => __d('core', 'Media'),
					'priority' => 300,
					'parent' => 'core_settings',
					'url' => array(
						'plugin' => 'core',
						'controller' => 'core_settings',
						'action' => 'media'
					),
					'matchAction' => true
				));
	}

	public static function getAvailableLinksForMenuItem(WasabiEvent $event) {
		return array(
			'General' => array(
				'ExternalLink' => __d('core', 'External Link'),
				'CustomAction' => __d('core', 'Custom Controller Action')
			)
		);
	}

	public static function loadJsTranslations(WasabiEvent $event) {
		return array(
			'Yes' => __d('core', 'Yes'),
			'No' => __d('core', 'No')
		);
	}

	public static function loadGuestActions(WasabiEvent $event) {
		return array(
			'Core.Browser.notSupported',
			'Core.Users.login',
			'Core.Users.logout',
			'Core.CoreInstall.check',
			'Core.CoreInstall.database',
			'Core.CoreInstall.import',
			'Core.CoreInstall.config',
			'Core.CoreInstall.finish'
		);
	}

}
