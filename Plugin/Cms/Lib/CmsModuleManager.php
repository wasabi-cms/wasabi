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

App::uses('Folder', 'Utility');

class CmsModuleManager {

	/**
	 * Holds all available cms module objects.
	 *
	 * @var CmsModule[]
	 */
	protected static $_modules = array();

	/**
	 * Determines if the module manager has been initialized.
	 *
	 * @var boolean
	 */
	protected static $_initialized = false;

	/**
	 * Initialize all available modules.
	 *
	 * @return void
	 */
	protected static function _init() {
		$moduleFolder = new Folder(APP. 'View' . DS . 'CmsModules', false);
		$moduleFolders = $moduleFolder->read(true)[0];
		if (!empty($moduleFolders)) {
			foreach ($moduleFolders as $folder) {
				$moduleClass = Inflector::camelize($folder) . 'CmsModule';
				$file = $moduleFolder->path . DS . $folder . DS . $moduleClass . '.php';
				if (file_exists($file)) {
					App::uses($moduleClass, 'View/CmsModules/' . $folder);
					/** @var CmsModule $module */
					$module = new $moduleClass;
					self::$_modules[$module->getId()] = $module;
				}
			}
		}
		self::$_initialized = true;
	}

	/**
	 * Get all available modules.
	 *
	 * @return array|CmsLayout[]
	 */
	public static function getModules() {
		if (!self::$_initialized) {
			self::_init();
		}
		return self::$_modules;
	}

	/**
	 * Get all available modules for select options.
	 *
	 * @return array
	 */
	public static function getModulesForSelect() {
		if (!self::$_initialized) {
			self::_init();
		}

		$results = array();
		foreach (self::$_modules as $module) {
			$results[$module->getId()] = $module->getName();
		}

		return $results;
	}

	/**
	 * Get a single module by id.
	 *
	 * @param string $id
	 * @return CmsModule
	 * @throws CakeException
	 */
	public static function getModule($id) {
		if (!self::$_initialized) {
			self::_init();
		}

		if (!isset(self::$_modules[$id])) {
			throw new CakeException(__d('cms', 'No Module with id %s exists.', array($id)));
		}

		return self::$_modules[$id];
	}

}