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

$cache_folder = new Folder(CACHE . 'core' . DS . 'infinite', true, 0755);
Cache::config('core.infinite', array(
	'engine' => 'File',
	'duration' => '+999 days',
	'prefix' => false,
	'path' => $cache_folder->path,
));

unset($cache_folder);

$active_plugins = Cache::read('active_plugins', 'core.infinite');
if ($active_plugins === false) {
	/**
	 * @var Plugin $plugin
	 */
	$plugin = ClassRegistry::init('Core.Plugin');
	$ds = $plugin->getDataSource();
	$tables = $ds->listSources();
	$plugin_table_present = false;
	foreach ($tables as $t) {
		if ($t == $plugin->table) {
			$plugin_table_present = true;
			break;
		}
	}
	if ($plugin_table_present) {
		$active_plugins = $plugin->findActive();
		if (!$active_plugins) {
			$active_plugins = array();
		}
	}

	unset($plugin, $ds, $tables, $plugin_table_present);

	Cache::write('active_plugins', $active_plugins, 'core.infinite');
}

foreach ($active_plugins as $p) {
	CakePlugin::load($p['Plugin']['name'], array('bootstrap' => false, 'routes' => false));
}

unset($active_plugins);
