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
		$this->_loadLanguages();
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
	 *     'plugin_key' => Array(
	 *         'setting_name_1' => 'setting_value_1',
	 *         ...
	 *     ),
	 *     ...
	 * )
	 *
	 * Access via:
	 * -----------
	 * Configure::read('Settings.plugin_key.setting_name_1');
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

	/**
	 * Load and setup all languages and language related config options.
	 *
	 * @return void
	 */
	private function _loadLanguages() {
		// All available languages for frontend / backend
		if (!$languages = Cache::read('languages', 'core.infinite')) {
			$language = ClassRegistry::init('Core.Language');
			$all_languages = $language->findAll();

			$languages = array(
				'frontend' => array(),
				'backend' => array()
			);
			foreach ($all_languages as $lang) {
				if ($lang['Language']['available_at_backend'] === true) {
					$languages['backend'][] = $lang['Language'];
				}
				if ($lang['Language']['available_at_frontend'] === true) {
					$languages['frontend'][] = $lang['Language'];
				}
			}
			Cache::write('languages', $languages, 'core.infinite');
		}
		Configure::write('Languages', $languages);

		// current backend language of the logged in user
		$user = Authenticator::get();
		$user_language = $languages['backend'][0];
		if ($user && isset($user['Language']) && isset($user['Language']['id'])) {
			foreach ($languages['backend'] as $b_lang) {
				if ($b_lang['id'] == $user['Language']['id']) {
					$user_language = $b_lang;
					break;
				}
			}
		}
		Configure::write('Wasabi.backend_language', $user_language);
		Configure::write('Config.language', $user_language['iso']);

		// current content language the user has active
		$content_language = $languages['frontend'][0];
		if ($this->Session->check('Wasabi.content_language_id')) {
			$c_lang_id = $this->Session->read('Wasabi.content_language_id');
			foreach ($languages['frontend'] as $c_lang) {
				if ($c_lang['id'] == $c_lang_id) {
					$content_language = $c_lang;
					break;
				}
			}
		}
		Configure::write('Wasabi.content_language', $content_language);
	}

}
