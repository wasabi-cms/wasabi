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
 * @subpackage    Wasabi.Plugin.Core.Model
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class Plugin extends CoreAppModel {

	public $useTable = false;

	/**
	 * Find all plugins
	 *
	 * @return array
	 */
	public function findAll() {
		$plugin_folder = new Folder(APP . 'Plugin' . DS, false);
		$plugin_folders = $plugin_folder->read(true, array('.', 'Core', 'Migrations'));
		$plugin_folders = $plugin_folders[0];

		$plugins = array();
		foreach ($plugin_folders as $plugin) {
			$plugins[] = array(
				'Plugin' => array(
					'name' => $plugin,
					'active' => $this->isActive($plugin),
					'installed' => $this->isInstalled($plugin),
					'bootstrap' => $this->hasBootstrap($plugin),
					'routes' => $this->hasRoutes($plugin)
				),
				'PluginInfo' => $this->getInfo($plugin)
			);
		}

		return $plugins;
	}

	public function exists($plugin) {
		return file_exists($this->getPluginPath($plugin));
	}

	public function isActive($plugin) {
		return file_exists($this->getPluginConfigPath($plugin) . '.active');
	}

	public function isInstalled($plugin) {
		return file_exists($this->getPluginConfigPath($plugin) . '.installed');
	}

	public function hasBootstrap($plugin) {
		return file_exists($this->getPluginConfigPath($plugin) . 'bootstrap.php');
	}

	public function hasRoutes($plugin) {
		return file_exists($this->getPluginConfigPath($plugin) . 'routes.php');
	}

	public function getPluginPath($plugin) {
		return APP . 'Plugin' . DS . $plugin . DS;
	}

	public function getPluginConfigPath($plugin) {
		return $this->getPluginPath($plugin) . 'Config' . DS;
	}

	public function install($plugin) {
		$install_file = new File($this->getPluginConfigPath($plugin) . '.installed', true, 0755);
		return $this->isInstalled($plugin);
	}

	public function uninstall($plugin) {
		if ($this->isInstalled($plugin)) {
			$install_file = new File($this->getPluginConfigPath($plugin) . '.installed', false);
			$install_file->delete();
		}
		return !$this->isInstalled($plugin);
	}

	public function activate($plugin) {
		$active_file = new File($this->getPluginConfigPath($plugin) . '.active', true, 0755);
		$this->clearActivePluginCache();
		return $this->isActive($plugin);
	}

	public function deactivate($plugin) {
		if ($this->isActive($plugin)) {
			$active_file = new File($this->getPluginConfigPath($plugin) . '.active', false);
			$active_file->delete();
		}
		$this->clearActivePluginCache();
		return !$this->isActive($plugin);
	}

	public function clearActivePluginCache() {
		Cache::delete('active_plugins', 'core.infinite');
	}

	public function getInfo($plugin) {
		$info_file = APP . 'Plugin' . DS . $plugin . DS . 'Config' . DS . 'plugin.json';

		$info = array();
		if (file_exists($info_file)) {
			$info = file_get_contents($info_file);
			$info = (array) json_decode($info);
		}

		return $info;
	}

}
