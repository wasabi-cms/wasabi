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
		'Backend.JS.Translations.load' => array(
			'method' => 'loadJsTranslations',
			'priority' => 100
		),
		'Common.Settings.load' => array(
			'method' => 'loadSettings',
			'priority' => 99999
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
		Router::connect("/${prefix}/settings/edit", array('plugin' => 'core', 'controller' => 'core_settings', 'action' => 'edit'));

		// Plugins
		Router::connect("/${prefix}/plugins", array('plugin' => 'core', 'controller' => 'plugins', 'action' => 'index'));
		Router::connect("/${prefix}/plugins/:action/*", array('plugin' => 'core', 'controller' => 'plugins'));

		// Permissions
		Router::connect("/${prefix}/permissions", array('plugin' => 'core', 'controller' => 'permissions', 'action' => 'index'));
		Router::connect("/${prefix}/permissions/:action/*", array('plugin' => 'core', 'controller' => 'permissions'));
	}

	public static function loadBackendMenu(WasabiEvent $event) {
		WasabiNav::addPrimary('dashboard', array(
			'name' => __d('core', 'Dashboard'),
			'priority' => 1,
			'url' => array('plugin' => 'core', 'controller' => 'dashboard', 'action' => 'index')
		));
		WasabiNav::addPrimary('administration', array(
			'name' => __d('core', 'Administration'),
			'priority' => 99999
		));
		WasabiNav::addSecondary('administration', array(
			array(
				'name' => __d('core', 'Users'),
				'priority' => 100,
				'url' => array('plugin' => 'core', 'controller' => 'users', 'action' => 'index')
			),
			array(
				'name' => __d('core', 'Groups'),
				'priority' => 200,
				'url' => array('plugin' => 'core', 'controller' => 'groups', 'action' => 'index')
			),
			array(
				'name' => __d('core', 'Languages'),
				'priority' => 300,
				'url' => array('plugin' => 'core', 'controller' => 'languages', 'action' => 'index')
			),
			array(
				'name' => __d('core', 'Plugins'),
				'priority' => 400,
				'url' => array('plugin' => 'core', 'controller' => 'plugins', 'action' => 'index')
			),
			array(
				'name' => __d('core', 'Permissions'),
				'priority' => 500,
				'url' => array('plugin' => 'core', 'controller' => 'permissions', 'action' => 'index')
			),
			array(
				'name' => __d('core', 'Core Settings'),
				'priority' => 600,
				'url' => array('plugin' => 'core', 'controller' => 'core_settings', 'action' => 'edit')
			)
		));
	}

	public static function loadJsTranslations(WasabiEvent $event) {
		return array(
			'Yes' => __d('core', 'Yes'),
			'No' => __d('core', 'No')
		);
	}

	/**
	 * Load and cache all core settings
	 *
	 * @param WasabiEvent $event
	 * @return array
	 */
	public static function loadSettings(WasabiEvent $event) {
		if (!$settings = Cache::read('core_settings', 'core.infinite')) {
			/**
			 * @var $coreSetting CoreSetting
			 */
			$coreSetting = ClassRegistry::init('Core.CoreSetting');
			$coreSettings = $coreSetting->findById(1);

			$settings = array();
			if ($coreSettings) {
				$coreSettings = $coreSettings['CoreSetting'];
				unset($coreSettings['id'], $coreSettings['created'], $coreSettings['modified']);
				$settings = array(
					'core' => $coreSettings
				);
			}

			Cache::write('core_settings', $settings, 'core.infinite');
		}

		return $settings;
	}

	public static function loadGuestActions(WasabiEvent $event) {
		return array(
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
