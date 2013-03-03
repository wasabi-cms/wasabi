<?php
/**
 * Application wide Controller
 *
 * PHP 5
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the below copyright notice.
 *
 * @copyright     Copyright 2013, Frank Förster (http://frankfoerster.com)
 * @link          http://github.com/frankfoerster/wasabi
 * @package       Wasabi
 * @subpackage    Wasabi.Controller
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');

class AppController extends Controller {

	/**
	 * beforeFilter callback
	 *
	 * initialize all cache configs
	 * load all settings
	 *
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		WasabiEventManager::trigger(new stdClass(), 'Common.CacheConfig.init');

		$this->_loadSettings();
	}

	/**
	 * Loads all settings via an event trigger 'Common.Settings.load'
	 * that can be listened to by plugins.
	 *
	 * Stores the loaded settings in 'Settings' key accessible via Configure::read('Settings')
	 *
	 * @return void
	 */
	private function _loadSettings() {
		$stored_settings = WasabiEventManager::trigger(new stdClass(), 'Common.Settings.load');
		$stored_settings = $stored_settings['Common.Settings.load'];

		$settings = array();
		foreach ($stored_settings as $plugin_settings) {
			foreach ($plugin_settings as $key => $s) {
				if (!isset($settings[$key])) {
					$settings[$key] = $s;
				} else {
					$settings = Hash::merge($settings, array(
						"${$key}" => $s
					));
				}
			}
		}

		Configure::write('Settings', $settings);
	}

}
