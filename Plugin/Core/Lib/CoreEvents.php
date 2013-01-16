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
 * @subpackage    Wasabi.Plugin.Core.Lib
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Router', 'Routing');

class CoreEvents {

	public $implements = array(
		'Plugin.Routes.load' => array(
			'method' => 'loadPluginRoutes',
			'priority' => 100
		),
		'Backend.Menu.load' => array(
			'method' => 'loadBackendMenu',
			'priority' => 0
		)
	);

	public static function loadPluginRoutes(WasabiEvent $event) {
		$prefix = Configure::read('Wasabi.backend_prefix');

		// Users
		Router::connect("/${prefix}/users", array('plugin' => 'core', 'controller' => 'users', 'action' => 'index'));
		Router::connect("/${prefix}/users/:action/*", array('plugin' => 'core', 'controller' => 'users'));

		// Groups
		Router::connect("/${prefix}/groups", array('plugin' => 'core', 'controller' => 'groups', 'action' => 'index'));
		Router::connect("/${prefix}/groups/:action/*", array('plugin' => 'core', 'controller' => 'groups'));

		// Languages
		Router::connect("/${prefix}/languages", array('plugin' => 'core', 'controller' => 'languages', 'action' => 'index'));
		Router::connect("/${prefix}/languages/:action/*", array('plugin' => 'core', 'controller' => 'languages'));
	}

	public static function loadBackendMenu(WasabiEvent $event) {
		return array(
			'primary' => array(
				'name' => __d('core', 'Administration'),
				'url' => array('plugin' => 'core', 'controller' => 'users', 'action' => 'index'),
				'children' => array(
					array(
						'name' => __d('core', 'Users'),
						'url' => array('plugin' => 'core', 'controller' => 'users', 'action' => 'index')
					),
					array(
						'name' => __d('core', 'Groups'),
						'url' => array('plugin' => 'core', 'controller' => 'groups', 'action' => 'index')
					)
				)
			)
		);
	}
}
