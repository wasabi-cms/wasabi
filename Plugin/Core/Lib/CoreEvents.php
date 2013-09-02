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
		'Plugin.Routes.load' => array(
			'method' => 'loadPluginRoutes',
			'priority' => 99999
		),
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

	public static function loadPluginRoutes(WasabiEvent $event) {
		// Handle .json and application/json requests
		Router::parseExtensions('json');

		$prefix = Configure::read('Wasabi.backend_prefix');

		// Login & Logout
		Router::connect("/${prefix}/login", array('plugin' => 'core', 'controller' => 'users', 'action' => 'login'));
		Router::connect("/${prefix}/logout", array('plugin' => 'core', 'controller' => 'users', 'action' => 'logout'));

		// Dashboard
		Router::connect("/${prefix}", array('plugin' => 'core', 'controller' => 'dashboard', 'action' => 'index'));

		// Edit Profile
		Router::connect("/${prefix}/profile", array('plugin' => 'core', 'controller' => 'users', 'action' => 'profile'));

		// Users
		Router::connect("/${prefix}/users", array('plugin' => 'core', 'controller' => 'users', 'action' => 'index'));
		Router::connect("/${prefix}/users/:action/*", array('plugin' => 'core', 'controller' => 'users'));

		// Groups
		Router::connect("/${prefix}/groups", array('plugin' => 'core', 'controller' => 'groups', 'action' => 'index'));
		Router::connect("/${prefix}/groups/:action/*", array('plugin' => 'core', 'controller' => 'groups'));

		// Languages
		Router::connect("/${prefix}/languages", array('plugin' => 'core', 'controller' => 'languages', 'action' => 'index'));
		Router::connect("/${prefix}/languages/:action/*", array('plugin' => 'core', 'controller' => 'languages'));

		// Core Settings
		Router::connect("/${prefix}/settings/:action/*", array('plugin' => 'core', 'controller' => 'core_settings'));

		// Plugins
		Router::connect("/${prefix}/plugins", array('plugin' => 'core', 'controller' => 'plugins', 'action' => 'index'));
		Router::connect("/${prefix}/plugins/:action/*", array('plugin' => 'core', 'controller' => 'plugins'));

		// Permissions
		Router::connect("/${prefix}/permissions", array('plugin' => 'core', 'controller' => 'permissions', 'action' => 'index'));
		Router::connect("/${prefix}/permissions/:action/*", array('plugin' => 'core', 'controller' => 'permissions'));

		// Menus
		Router::connect("/${prefix}/menus", array('plugin' => 'core', 'controller' => 'menus', 'action' => 'index'));
		Router::connect("/${prefix}/menus/:action/*", array('plugin' => 'core', 'controller' => 'menus'));

		// Media
		Router::connect("/${prefix}/media", array('plugin' => 'core', 'controller' => 'media', 'action' => 'index'));
		Router::connect("/${prefix}/media/:action/*", array('plugin' => 'core', 'controller' => 'media'));

		// Browser
		Router::connect("/${prefix}/browser/not-supported", array('plugin' => 'core', 'controller' => 'browser', 'action' => 'notSupported'));
	}

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
				'icon' => 'icon-home'
			))
			->addMenuItem(array(
				'alias' => 'core_menus',
				'name' => __d('core', 'Menus'),
				'priority' => 1000,
				'icon' => 'icon-list',
				'url' => array(
					'plugin' => 'core',
					'controller' => 'menus',
					'action' => 'index'
				)
			))
			->addMenuItem(array(
				'alias' => 'core_media',
				'name' => __d('core', 'Media'),
				'priority' => 2000,
				'icon' => 'icon-picture',
				'url' => array(
					'plugin' => 'core',
					'controller' => 'media',
					'action' => 'index'
				)
			))
			->addMenuItem(array(
				'alias' => 'core_administration',
				'name' => __d('core', 'Administration'),
				'priority' => 3000,
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
				'priority' => 4000,
				'icon' => 'icon-wrench'
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
