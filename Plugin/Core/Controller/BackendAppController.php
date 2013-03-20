<?php
/**
 * Backend wide base controller class
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
 * @property AuthenticatorComponent  $Authenticator
 * @property RequestHandlerComponent $RequestHandler
 */

class BackendAppController extends AppController {

	/**
	 * Components used by this controller
	 *
	 * @var array
	 */
	public $components = array(
		'Core.Authenticator' => array(
			'model' => 'Core.User',
			'sessionKey' => 'wasabi',
			'cookieKey' => 'wasabi_remember'
		),
		'RequestHandler',
		'Security'
	);

	/**
	 * Default Session flash message when form errors are present.
	 *
	 * @var string
	 */
	public $formErrorMessage;

	/**
	 * Default Session flash message when a request is invalid.
	 *
	 * @var string
	 */
	public $invalidRequestMessage;

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
	 * beforeFilter callback
	 *
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->_checkPermissions();
		$this->_setupBackend();
	}

	/**
	 * Check if the current request needs an authenticated user.
	 * Check if the user is authorized to complete the request.
	 *
	 * @return void
	 */
	protected function _checkPermissions() {
		$plugin = $this->request->params['plugin'];
		$controller = $this->request->params['controller'];
		$action = $this->request->params['action'];
		$path = "${plugin}.${controller}.${action}";

		$guest_actions = array(
			'core.users.login',
			'core.users.logout'
		);

		// action requires login
		if (!in_array($path, $guest_actions)) {

			// user is not logged in -> save current request and redirect to login page
			if (!$this->Authenticator->get()) {
				$this->Session->write('login_referer', $this->request->url);
				$this->redirect(array(
					'plugin' => 'core',
					'controller' => 'users',
					'action' => 'login'
				));
			}
		}
	}

	/**
	 * Setup backend
	 * - layout
	 * - interface language
	 * - content language
	 * - backend menu items
	 * - backend_prefix
	 * - default form error message
	 * - default invalid request message
	 *
	 * @return void
	 */
	protected function _setupBackend() {
		$this->_loadBackendMenu();
		$this->_loadLanguages();
		$this->layout = 'Core.default';
		$this->set('backend_prefix', Configure::read('Wasabi.backend_prefix'));
		$this->formErrorMessage = __d('core', 'Please correct the marked errors.');
		$this->invalidRequestMessage = __d('core', 'Invalid Request.');
	}

	/**
	 * Load all backend menu items of all active plugins
	 * via plugin event handlers.
	 *
	 * @return void
	 */
	protected function _loadBackendMenu() {
		$event_name = 'Backend.Menu.load';
		$menu_items = WasabiEventManager::trigger($this, $event_name);
		if (empty($menu_items)) {
			return;
		}
		$menu_items = $menu_items[$event_name];

		$primary_menu = array();
		$secondary_menu = array();

		foreach ($menu_items as $items) {
			$secondary_active_found = false;
			$secondary_items = $items['primary']['children'];
			foreach ($secondary_items as &$secondary_item) {
				if ($secondary_item['url']['plugin'] === $this->request->params['plugin']
					&& $secondary_item['url']['controller'] === $this->request->params['controller']) {
					$secondary_active_found = true;
					$secondary_item['active'] = true;
					$secondary_menu = $secondary_items;
					break;
				}
			}
			if ($secondary_active_found) {
				$items['primary']['active'] = true;
			}
			unset($items['primary']['children']);
			$primary_menu[] = $items['primary'];
		}

		$this->set('backend_menu_for_layout', array(
			'primary' => $primary_menu,
			'secondary' => $secondary_menu
		));
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
