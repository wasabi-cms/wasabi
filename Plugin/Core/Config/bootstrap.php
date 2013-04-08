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

$cacheFolder = new Folder(CACHE . 'core' . DS . 'infinite', true, 0755);
Cache::config('core.infinite', array(
	'engine' => 'File',
	'duration' => '+999 days',
	'prefix' => false,
	'path' => $cacheFolder->path,
));

$cacheFolder = new Folder(CACHE . 'core' . DS . 'routes', true, 0755);
Cache::config('core.routes', array(
	'engine' => 'File',
	'duration' => '+999 days',
	'prefix' => false,
	'path' => $cacheFolder->path
));

$cacheFolder = new Folder(CACHE . 'frontend' . DS . 'pygmentize', true, 0755);
Cache::config('frontend.pygmentize', array(
	'engine' => 'File',
	'duration' => '+999 days',
	'prefix' => false,
	'path' => $cacheFolder->path
));

unset($cacheFolder);

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
