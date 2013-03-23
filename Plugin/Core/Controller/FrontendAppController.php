<?php
/**
 * Frontend wide base controller class
 *
 * PHP 5
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the below copyright notice.
 *
 * @copyright     Copyright 2013, Frank Förster (http://frankfoerster.com)
 * @link          http://github.com/frankfoerster/wasabi
 * @package       Wasabi
 * @subpackage    Wasabi.Plugin.Core.Controller
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppController', 'Controller');

/**
 * @property RequestHandlerComponent $RequestHandler
 */

class FrontendAppController extends AppController {

	/**
	 * Components used by this controller
	 *
	 * @var array
	 */
	public $components = array(
		'RequestHandler'
	);

	/**
	 * Helpers used by this controller
	 *
	 * @var array
	 */
	public $helpers = array(
		'Form',
		'Html',
		'Session'
	);

	/**
	 * The default View Class for all backend controller actions.
	 * Main purpose is to reflect properties of custom helpers into the view
	 * by simple phpdoc annotations ontop of the CoreView class.
	 *
	 * @var string
	 */
	public $viewClass = 'Core.Core';

	/**
	 * beforeFilter callback
	 *
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();
	}

	/**
	 * Load and setup all languages and language related config options.
	 *
	 * @param integer|null $language_id The id of the current active language
	 * @return void
	 */
	public function loadLanguages($language_id = null) {
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

		if ($language_id !== null) {
			foreach ($languages['frontend'] as $frontend_language) {
				if ($frontend_language['id'] == $language_id) {
					Configure::write('Wasabi.content_language', $frontend_language);
					Configure::write('Config.language', $frontend_language['iso']);
					break;
				}
			}
		} else {
			Configure::write('Wasabi.content_language', $languages['frontend'][0]);
			Configure::write('Config.language', $languages['frontend'][0]['iso']);
		}
	}

	/**
	 * Wrapper to trigger an event via WasabiEventManager.
	 * The wrapper is needed to easily mock _triggerEvent for controller tests.
	 *
	 * @param object $origin
	 * @param string $event_name
	 * @param null|mixed $data
	 * @return array
	 */
	protected function _triggerEvent(&$origin, $event_name, $data = null) {
		return WasabiEventManager::trigger($origin, $event_name, $data);
	}

}
