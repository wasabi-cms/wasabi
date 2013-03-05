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

App::uses('CakePlugin', 'Core');
App::uses('WasabiEvent', 'Core.Lib');

class WasabiEventManager {

	protected $_events = array();

	public function __construct() {
		$this->reloadEventListeners();
	}

	public static function instance() {
		static $instance;
		if (!$instance) {
			$instance = new WasabiEventManager();
		}
		return $instance;
	}

	public static function trigger(&$eventOrigin, $eventName, $data = null) {
		$_this = self::instance();

		$eventNames = !is_array($eventName) ? array($eventName) : $eventName;

		$results = array();
		foreach ($eventNames as $eventName) {
			$results[$eventName] = $_this->_dispatchEvent($eventOrigin, $eventName, $data);
		}

		return $results;
	}

	public function reloadEventListeners() {
		$this->_events = array();
		foreach (CakePlugin::loaded() as $plugin) {
			$this->_loadEventListener($plugin);
		}
	}

	protected function _loadEventListener($plugin) {
		$class = $plugin . 'Events';
		$file  = CakePlugin::path($plugin) . 'Lib' . DS . $class . '.php';

		if (file_exists($file)) {
			$this->_loadEventHandlers($plugin, $class);
		}
	}

	protected function _loadEventHandlers($plugin, $class) {
		App::uses($class, $plugin . '.Lib');
		$eventClass = new $class();

		foreach ($eventClass->implements as $eventName => $handler) {
			if (!in_array($handler['method'], get_class_methods($class))) {
				throw new NotImplementedException('Class ' . $class . ' must implement an ' . $handler['method'] . '() method.');
			}
			$method = $handler['method'];
			$priority = $handler['priority'];
			$this->_addEventHandler($eventName, array(
				'plugin' => $plugin,
				'class'  => $class,
				'method' => $method,
				'priority' => $priority
			));
		}

		unset($eventClass);
	}

	protected function _addEventHandler($eventName, $options) {
		if (!isset($this->_events[$eventName])) {
			$this->_events[$eventName] = array();
		}
		$this->_events[$eventName][] = $options;
		$this->_events[$eventName] = Hash::sort($this->_events[$eventName], '{n}.priority', 'desc', 'numeric');
	}

	protected function _dispatchEvent(&$eventOrigin, $eventName, $data = null) {
		if (!isset($this->_events[$eventName]) ||!count($this->_events[$eventName])) {
			return array();
		}

		$results = array();
		foreach ($this->_events[$eventName] as $handler) {
			$event = new WasabiEvent($eventName, $eventOrigin, $data);
			$results[] = call_user_func_array(array($handler['class'], $handler['method']), array($event));
		}

		return $results;
	}

}
