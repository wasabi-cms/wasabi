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

	public function css($path, $plugin = false, $appendTime = true) {
		$href = $this->_getUrl($path, $plugin, $appendTime);

		return '<link rel="stylesheet" type="text/css" href="' . $href . '">';
	}

	public function js($path, $plugin = false, $appendTime = true) {
		$src = $this->_getUrl($path, $plugin, $appendTime);

		return '<script type="text/javascript" src="' . $src . '"></script>';
	}

	protected function _getUrl($path, $plugin, $appendTime = true) {
		$time = $appendTime ? $this->_getModifiedTime($path, $plugin) : '';

		if ($plugin !== false) {
			$path = '/' . strtolower($plugin) . $path;
		}

		return Router::url($path) . $time;
	}

	protected function _getBasePath($path, $plugin = false) {
		$path = preg_replace("/\//", DS, $path);
		$basePath = APP;

		if ($plugin !== false) {
			$basePath .= 'Plugin' . DS . $plugin . DS;
		}

		$basePath .= WEBROOT_DIR . $path;

		return $basePath;
	}

	protected function _getModifiedTime($path, $plugin = false) {
		$path = $this->_getBasePath($path, $plugin);

		$time = '';
		if (file_exists($path)) {
			$file = new File($path, false);
			$time = '?t=' . $file->lastChange();
		}

		return $time;
	}

}
