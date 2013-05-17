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
 * @subpackage    Wasabi.Plugin.Core.Controller.Component
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Folder', 'Utility');
App::uses('CakePlugin', 'Core');
App::uses('ClassRegistry', 'Utility');

class GuardianComponent extends Component {

	/**
	 * Holds an instance of the Plugin model.
	 *
	 * @var Plugin
	 */
	protected $Plugin;

	/**
	 * Holds an instance of the GroupPermission model.
	 *
	 * @var GroupPermission
	 */
	protected $GroupPermission;

	/**
	 * Holds all public accessible action paths.
	 *
	 * Structure:
	 * ----------
	 * Array(
	 *     'Plugin.Controller.action',
	 *     'Plugin.Controller.action2,
	 *     ...
	 * )
	 *
	 * Global access via:
	 * ------------------
	 * Guardian::getGuestActions();
	 *
	 * @var array
	 */
	protected $_guestActions = array();

	/**
	 * Holds already generated paths indexed by an md5 hash of the url.
	 *
	 * @var array
	 */
	protected $_cachedPaths = array();

	/**
	 * Called before the Controller::beforeFilter().
	 *
	 * @param Controller $controller Controller with components to initialize
	 * @return void
	 */
	public function initialize(Controller $controller) {
		$this->Plugin = $this->_getPluginModel();
		$this->_loadGuestActions();

		parent::initialize($controller);
		new Guardian($this);
	}

	public function hasAccess($url) {
		$path = $this->getPathFromUrl($url);

		if (in_array($path, $this->_guestActions)) {
			return true;
		}

		$groupId = Authenticator::get('Group.id');

		if ($groupId === null) {
			return false;
		}

		if ($groupId == 1) {
			return true;
		}

		$permissions = $this->_getGroupPermissionModel()->findAllForGroup($groupId);
		if (in_array($path, $permissions)) {
			return true;
		}

		return false;
	}

	/**
	 * Get all public available action paths.
	 *
	 * @return array
	 */
	public function getGuestActions() {
		return $this->_guestActions;
	}

	public function getPathFromUrl($url) {
		$cacheKey = md5(serialize($url));
		if ($path = Cache::read($cacheKey, 'core.guardian_paths')) {
			return $path;
		}

		$plugin = 'App';
		$controller = null;
		$action = 'index';

		if (!is_array($url)) {
			$url = Router::parse($url);
		}

		if (isset($url['plugin']) && $url['plugin'] !== '' && $url['plugin'] !== false && $url['plugin'] !== null) {
			$plugin = Inflector::camelize($url['plugin']);
		}
		$controller = Inflector::camelize($url['controller']);
		if (isset($url['action']) && $url['action'] !== '') {
			$action = $url['action'];
		}

		$path = $plugin . '.' . $controller . '.' . $action;

		Cache::write($cacheKey, $path, 'core.guardian_paths');

		return $path;
	}

	/**
	 * Get a mapped array of all guardable controller actions
	 * excluding the provided guest actions.
	 *
	 * @return array
	 */
	public function getActionMap() {
		$plugins = $this->getLoadedPluginPaths();

		$actionMap = array();
		foreach ($plugins as $plugin => $path) {
			$controllers = $this->getControllersForPlugin($plugin, $path);
			foreach ($controllers as $controller) {
				$actions = $this->introspectController($controller['path']);
				if (empty($actions)) {
					continue;
				}
				foreach ($actions as $action) {
					$path = "{$plugin}.{$controller['name']}.{$action}";
					if (in_array($path, $this->_guestActions)) {
						continue;
					}
					$actionMap[$path] = array(
						'plugin' => $plugin,
						'controller' => $controller['name'],
						'action' => $action
					);
				}
			}
		}

		return $actionMap;
	}

	/**
	 * Get the paths of all installed and active plugins.
	 *
	 * @return array
	 */
	public function getLoadedPluginPaths() {
		$pluginPaths = array();

		$plugins = CakePlugin::loaded();
		foreach ($plugins as $p) {
			$pluginPaths[$p] = CakePlugin::path($p);
		}

		return $pluginPaths;
	}

	/**
	 * Retrieve all controller names + paths for a given plugin.
	 *
	 * @param string $plugin
	 * @param string $pluginPath
	 * @return array
	 */
	public function getControllersForPlugin($plugin, $pluginPath) {
		$controllers = array();
		$Folder = new Folder();

		$ctrlFolder = $Folder->cd($pluginPath . 'Controller');

		if (!empty($ctrlFolder)) {
			$files = $Folder->find('.*Controller\.php$');
			$subLength = strlen('Controller.php');
			foreach ($files as $f) {
				$filename = basename($f);
				if ($filename === $plugin . 'AppController.php') {
					continue;
				}
				$ctrlName = substr($filename, 0, strlen($filename) - $subLength);
				$controllers[] = array(
					'name' => $ctrlName,
					'path' => $Folder->path . DS . $f
				);
			}
		}

		return $controllers;
	}

	/**
	 * Retrieve all controller actions from a given controller.
	 *
	 * @param string $controllerPath
	 * @return array
	 */
	public function introspectController($controllerPath) {
		$content = file_get_contents($controllerPath);
		preg_match_all('/public\s+function\s+\&?\s*([^(]+)/', $content, $methods);

		$guardableActions = array();
		foreach ($methods[1] as $m) {
			if (in_array($m, array('__construct', 'setRequest', 'invokeAction', 'beforeFilter', 'beforeRender', 'beforeRedirect', 'afterFilter'))) {
				continue;
			}
			$guardableActions[] = $m;
		}

		return $guardableActions;
	}

	/**
	 * Get or initialize an instance of the Plugin model.
	 *
	 * @return Plugin
	 */
	protected function _getPluginModel() {
		if (get_class($this->Plugin) === 'Plugin') {
			return $this->Plugin;
		}
		return $this->Plugin = ClassRegistry::init('Core.Plugin');
	}

	/**
	 * Get or initialize an instance of the GroupPermission model.
	 *
	 * @return GroupPermission
	 */
	protected function _getGroupPermissionModel() {
		if (get_class($this->GroupPermission) === 'GroupPermission') {
			return $this->GroupPermission;
		}
		return $this->GroupPermission = ClassRegistry::init('Core.GroupPermission');
	}

	/**
	 * Load all available guest actions from all active plugins via an event trigger 'Guardian.GuestActions.load'
	 * that can be listened to by plugins.
	 *
	 * @return void
	 */
	protected function _loadGuestActions() {
		$guestActions = WasabiEventManager::trigger($this, 'Guardian.GuestActions.load');
		$guestActions = $guestActions['Guardian.GuestActions.load'];
		$allGuestActions = array();

		foreach ($guestActions as $actions) {
			$allGuestActions = array_merge($allGuestActions, $actions);
		}

		$this->_guestActions = $allGuestActions;
	}

}

class Guardian {

	/**
	 * @var GuardianComponent
	 */
	protected static $_instance;

	public function __construct(&$instance) {
		if (!self::$_instance) {
			self::$_instance = $instance;
		}
	}

	public static function hasAccess($url) {
		return self::$_instance->hasAccess($url);
	}

	public static function getGuestActions() {
		return self::$_instance->getGuestActions();
	}

}
