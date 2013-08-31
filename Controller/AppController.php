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
	 * Constructor
	 *
	 * @param CakeRequest $request
	 * @param CakeResponse $response
	 */
	public function __construct($request = null, $response = null) {
		if ((Configure::read('debug') > 0) && CakePlugin::loaded('DebugKit')) {
			$this->components[] = 'DebugKit.Toolbar';
		}

		parent::__construct($request, $response);
	}

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

		if ($this->request->params['controller'] !== 'core_install') {
			$this->_loadSettings();
		}
	}

	/**
	 * Loads all settings via an event trigger 'Common.Settings.load'
	 * that can be listened to by plugins.
	 *
	 * Stores the loaded settings in 'Settings' key accessible via Configure::read('Settings')
	 *
	 * Structure:
	 * ----------
	 * Array(
	 *     'PluginName|ScopeName' => Array(
	 *         'key1' => 'value1',
	 *         ...
	 *     ),
	 *     ...
	 * )
	 *
	 * Access via:
	 * -----------
	 * Configure::read('Settings.ScopeName.key1');
	 *
	 * @return void
	 */
	private function _loadSettings() {
		if (!$settings = Cache::read('settings', 'core.infinite')) {
			/**
			 * @var Setting
			 */
			$Setting = ClassRegistry::init('Core.Setting');
			$settings = $Setting->find('allKeyValues');

			Cache::write('settings', $settings, 'core.infinite');
		}

		Configure::write('Settings', $settings);
		WasabiEventManager::trigger(new stdClass(), 'Common.Settings.afterLoad');
	}

}
