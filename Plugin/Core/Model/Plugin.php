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

App::uses('Migrations', 'Migrations.Lib');

class Plugin extends CoreAppModel {

	public $useTable = false;

	/**
	 * Find all plugins
	 *
	 * @return array
	 */
	public function findAll() {
		$pluginFolder = new Folder(APP . 'Plugin' . DS, false);
		$pluginFolders = $pluginFolder->read(true, array('.', 'Core', 'Lessy', 'Migrations'));
		$pluginFolders = $pluginFolders[0];

		$plugins = array();
		foreach ($pluginFolders as $plugin) {
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

	public function isAvailable($plugin) {
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
		if ($this->isInstalled($plugin)) {
			return true;
		}
		try {
			CakePlugin::load($plugin);
			$migrations = new Migrations();
			$migrations->migrate(array(
				'direction' => 'up',
				'scope' => $plugin
			));
			new File($this->getPluginConfigPath($plugin) . '.installed', true, 0755);
		} catch (Exception $e) {
			return false;
		}
		return $this->isInstalled($plugin);
	}

	public function uninstall($plugin) {
		if (!$this->isInstalled($plugin)) {
			return true;
		}
		try {
			$migrations = new Migrations();
			$migrations->migrate(array(
				'direction' => 'down',
				'scope' => $plugin
			));
			$installFile = new File($this->getPluginConfigPath($plugin) . '.installed', false);
			$installFile->delete();
		} catch (Exception $e) {
			return false;
		}
		return !$this->isInstalled($plugin);
	}

	public function activate($plugin) {
		if (!$this->isActive($plugin)) {
			new File($this->getPluginConfigPath($plugin) . '.active', true, 0755);
			$this->clearActivePluginCache();
		}
		return $this->isActive($plugin);
	}

	public function deactivate($plugin) {
		if ($this->isActive($plugin)) {
			$activeFile = new File($this->getPluginConfigPath($plugin) . '.active', false);
			$activeFile->delete();
		}
		$this->clearActivePluginCache();
		return !$this->isActive($plugin);
	}

	public function clearActivePluginCache() {
		Cache::delete('active_plugins', 'core.infinite');
	}

	public function getInfo($plugin) {
		$infoFile = APP . 'Plugin' . DS . $plugin . DS . 'Config' . DS . 'plugin.json';

		$info = array();
		if (file_exists($infoFile)) {
			$info = file_get_contents($infoFile);
			$info = (array) json_decode($info);
		}

		return $info;
	}

}
