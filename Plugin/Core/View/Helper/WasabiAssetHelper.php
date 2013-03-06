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
 * @subpackage    Wasabi.Plugin.Core.View.Helper
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppHelper', 'View/Helper');

class WasabiAssetHelper extends AppHelper {

	public function css($path, $plugin = false, $append_time = true) {
		$time = $append_time ? $this->_getModifiedTime($path, $plugin) : '';

		if ($plugin !== false) {
			$path = '/' . strtolower($plugin) . $path;
		}

		return '<link rel="stylesheet" type="text/css" href="' . Router::url($path) . $time . '">';
	}

	public function js($path, $plugin = false, $append_time = true) {
		$time = $append_time ? $this->_getModifiedTime($path, $plugin) : '';

		if ($plugin !== false) {
			$path = '/' . strtolower($plugin) . $path;
		}

		return '<script type="text/javascript" src="' . Router::url($path) . $time . '"></script>';
	}

	private function _getBasePath($path, $plugin = false) {
		$path = preg_replace("/\//", DS, $path);
		$base_path = APP;

		if ($plugin !== false) {
			$base_path .= 'Plugin' . DS . $plugin . DS;
		}

		$base_path .= WEBROOT_DIR . $path;

		return $base_path;
	}

	private function _getModifiedTime($path, $plugin = false) {
		$path = $this->_getBasePath($path, $plugin);

		$time = '';
		if (file_exists($path)) {
			$file = new File($path, false);
			$time = '?t=' . $file->lastChange();
		}

		return $time;
	}

}
