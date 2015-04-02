<?php
/**
 * Core bootstrap
 *
 * PHP 5
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the below copyright notice.
 *
 * @copyright     Copyright 2013, Frank Förster (http://frankfoerster.com)
 * @link          http://github.com/frankfoerster/wasabi
 * @package       Wasabi
 * @subpackage    Wasabi.Plugin.Core.Config
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('ClassRegistry', 'Utility');
App::uses('Folder', 'Utility');
App::uses('WasabiEventManager', 'Core.Lib');

/**
 * Setup cache configs.
 */
	Cache::config('core.infinite', array(
		'engine' => 'File',
		'duration' => '+999 days',
		'prefix' => '',
		'path' => CACHE . 'core' . DS . 'infinite',
	));

	Cache::config('core.routes', array(
		'engine' => 'File',
		'duration' => '+999 days',
		'prefix' => '',
		'path' => CACHE . 'core' . DS . 'routes'
	));

	Cache::config('core.group_permissions', array(
		'engine' => 'File',
		'duration' => '+999 days',
		'prefix' => '',
		'path' => CACHE . 'core' . DS . 'group_permissions'
	));

	Cache::config('core.guardian_paths', array(
		'engine' => 'File',
		'duration' => '+999 days',
		'prefix' => '',
		'path' => CACHE . 'core' . DS . 'guardian_paths'
	));

	Cache::config('frontend.pygmentize', array(
		'engine' => 'File',
		'duration' => '+999 days',
		'prefix' => '',
		'path' => CACHE . 'frontend' . DS . 'pygmentize'
	));

	Cache::config('core.main_nav', array(
		'engine' => 'File',
		'duration' => '+999 days',
		'prefix' => '',
		'path' => CACHE . 'core' . DS . 'main_nav'
	));

/**
 * Load active plugins.
 */
	$activePlugins = array();
	if (Configure::read('Wasabi.installed') === true) {
		$activePlugins = Cache::read('active_plugins', 'core.infinite');
	}

	if ($activePlugins === false) {
		/**
		 * @var Plugin $plugin
		 */
		$plugin = ClassRegistry::init('Core.Plugin');

		$activePlugins = array();
		foreach ($plugin->findAll() as $p) {
			if ($p['Plugin']['active'] === true) {
				$activePlugins[] = $p;
			}
		}
		unset($plugin);

		Cache::write('active_plugins', $activePlugins, 'core.infinite');
	}

	foreach ($activePlugins as $p) {
		CakePlugin::load($p['Plugin']['name'], array('bootstrap' => $p['Plugin']['bootstrap'], 'routes' => $p['Plugin']['routes']));
	}

	unset($activePlugins);

/**
 * Autoload namespaced vendor libs
 */
	spl_autoload_register(function ($class) {
		$path = CakePlugin::path('Core') . 'Vendor' . DS . str_replace('\\', DS, $class) . '.php';
		if (file_exists($path)) {
			include $path;
		}
	});

/**
 * Register all available Collections and CollectionItems.
 */
	WasabiEventManager::trigger(new stdClass(), 'Plugin.Collections.register');
	WasabiEventManager::trigger(new stdClass(), 'Plugin.CollectionItems.register');
