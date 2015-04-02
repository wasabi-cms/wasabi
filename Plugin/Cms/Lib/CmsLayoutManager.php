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

class CmsLayoutManager {

	/**
	 * Holds all available layout objects.
	 *
	 * @var CmsLayout[]
	 */
	protected static $_layouts = array();

	/**
	 * Determines if the layout manager has been initialized.
	 *
	 * @var boolean
	 */
	protected static $_initialized = false;

	/**
	 * Initialize all available layouts.
	 *
	 * @return void
	 */
	protected static function _init() {
		$layoutFolder = new Folder(APP. 'View' . DS . 'CmsLayouts', false);
		$layoutFolders = $layoutFolder->read(true)[0];
		if (!empty($layoutFolders)) {
			foreach ($layoutFolders as $folder) {
				$layoutClass = Inflector::camelize($folder) . 'CmsLayout';
				$file = $layoutFolder->path . DS . $folder . DS . $layoutClass . '.php';
				if (file_exists($file)) {
					App::uses($layoutClass, 'View/CmsLayouts/' . $folder);
					/** @var CmsLayout $layout */
					$layout = new $layoutClass;
					self::$_layouts[$layout->getId()] = $layout;
				}
			}
		}
		self::$_initialized = true;
	}

	/**
	 * Get all available layouts.
	 *
	 * @return array|CmsLayout[]
	 */
	public static function getLayoutList() {
		if (!self::$_initialized) {
			self::_init();
		}
		return self::$_layouts;
	}

	/**
	 * Get all available layouts for select options.
	 *
	 * @return array
	 */
	public static function getLayoutsForSelect() {
		if (!self::$_initialized) {
			self::_init();
		}

		$results = array();
		foreach (self::$_layouts as $layout) {
			$results[$layout->getId()] = $layout->getName();
		}

		return $results;
	}

	/**
	 * Get a single layout by id.
	 *
	 * @param string $id
	 * @return CmsLayout
	 * @throws CakeException
	 */
	public static function getLayout($id) {
		if (!self::$_initialized) {
			self::_init();
		}

		if (!isset(self::$_layouts[$id])) {
			throw new CakeException(__d('cms', 'No Layout with id %s exists.', array($id)));
		}

		return self::$_layouts[$id];
	}

	/**
	 * Return true if layout exists, false otherwise.
	 *
	 * @param string $id
	 * @return boolean
	 */
	public static function layoutExists($id) {
		return isset(self::$_layouts[$id]);
	}

}