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

App::uses('Router', 'Routing');

class CoreEvents {

	public $implements = array(
		'Plugin.Routes.load' => array(
			'method' => 'loadPluginRoutes',
			'priority' => 100
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
		'Common.CacheConfig.init' => array(
			'method' => 'initCacheConfig',
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
	}

	public static function loadBackendMenu(WasabiEvent $event) {
		return array(
			'primary' => array(
				'name' => __d('core', 'Administration'),
				'url' => array('plugin' => 'core', 'controller' => 'users', 'action' => 'index'),
				'children' => array(
					array(
						'name' => __d('core', 'Users'),
						'url' => array('plugin' => 'core', 'controller' => 'users', 'action' => 'index')
					),
					array(
						'name' => __d('core', 'Groups'),
						'url' => array('plugin' => 'core', 'controller' => 'groups', 'action' => 'index')
					),
					array(
						'name' => __d('core', 'Languages'),
						'url' => array('plugin' => 'core', 'controller' => 'languages', 'action' => 'index')
					),
					array(
						'name' => __d('core', 'Core Settings'),
						'url' => array('plugin' => 'core', 'controller' => 'core_settings', 'action' => 'edit')
					)
				)
			)
		);
	}

	public static function loadJsTranslations(WasabiEvent $event) {
		return array(
			'Yes' => __d('core', 'Yes'),
			'No'  => __d('core', 'No')
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
			 * @var $core_setting CoreSetting
			 */
			$core_setting = ClassRegistry::init('Core.CoreSetting');
			$core_settings = $core_setting->findById(1);

			$settings = array();
			if ($core_settings) {
				$core_settings = $core_settings['CoreSetting'];
				unset($core_settings['id'], $core_settings['created'], $core_settings['modified']);
				$settings = array(
					'core' => $core_settings
				);
			}

			Cache::write('core_settings', $settings, 'core.infinite');
		}

		return $settings;
	}

	/**
	 * Initialize all cache configs of the Core plugin
	 *
	 * @param WasabiEvent $event
	 * @return void
	 */
	public static function initCacheConfig(WasabiEvent $event) {
		$cache_folder = new Folder(CACHE . 'core' . DS . 'infinite', true, 0755);
		Cache::config('core.infinite', array(
			'engine' => 'File',
			'duration' => '+999 days',
			'prefix' => false,
			'path' => $cache_folder->path,
		));
	}
}
