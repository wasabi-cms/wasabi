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
 * @subpackage    Wasabi.Plugin.Core.Routing.Route
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('CakeResponse', 'Network');
App::uses('CakeRoute', 'Routing/Route');
App::uses('ClassRegistry', 'Utility');

class WasabiRoute extends CakeRoute {

	/**
	 * CakeResponse object
	 *
	 * @var CakeResponse
	 */
	public $response = null;

	/**
	 * Flag for disabling exit() when this route parses a url.
	 *
	 * @var boolean
	 */
	public $stop = true;

	/**
	 * Reverse routing method to generate a url from given url array
	 *
	 * @param array $url
	 * @return boolean|mixed
	 */
	public function match($url) {
		if (!is_array($url) || empty($url)) {
			return false;
		}

		if (!isset($url['plugin']) || $url['plugin'] == false) {
			$url['plugin'] = null;
		}

		$identifier = md5(serialize($url));

		if ($result = Cache::read($identifier, 'core.routes')) {
			return $result;
		}

		$conditions = array(
			'Route.plugin' => ($url['plugin'] == null) ? '' : $url['plugin'],
			'Route.controller' => $url['controller'],
			'Route.action' => $url['action'],
			'Route.status_code' => null
		);

		unset($url['plugin'], $url['controller'], $url['action']);

		$passed_params = array();
		$named_params = array();

		foreach ($url as $key => $value) {
			if (is_int($key)) {
				$passed_params[] = $value;
			}
			if (is_string($key)) {
				$named_params[] = $key . ':' . $value;
			}
		}

		$conditions['Route.params'] = implode('|', $passed_params);

		$route = ClassRegistry::init('Core.Route')->find('first', array(
			'conditions' => $conditions
		));

		if (!$route) {
			return false;
		}

		if (!empty($named_params)) {
			$route['Route']['url'] .= '/' . implode('/', $named_params);
		}

		Cache::write($identifier, $route['Route']['url'], 'core.routes');

		return $route['Route']['url'];
	}

	/**
	 * Parse a requested url and create routing parameters from the routes table
	 *
	 * @param string $url
	 * @return array|boolean|mixed
	 */
	public function parse($url) {
		if (!$url) {
			$url = '/';
		}

		$params = array(
			'pass' => array(),
			'named' => array(),
			'plugin' => null
		);

		// check for named params
		$url_parts = explode('/', $url);
		foreach ($url_parts as $key => $value) {
			if ($value === '') {
				unset($url_parts[$key]);
				continue;
			}
			if (strpos($value, ':') !== false) {
				$named_params = explode(':', $value);
				$params['named'][$named_params[0]] = $named_params[1];
				unset($url_parts[$key]);
			}
		}

		$route_model = ClassRegistry::init('Core.Route');

		$route = $route_model->find('first', array(
			'conditions' => array(
				'Route.url' => '/' . implode('/', $url_parts)
			)
		));

		if ($route && $route['Route']['redirect_to'] !== null) {
			$redirect_route = $route_model->findById($route['Route']['redirect_to']);
			if ($redirect_route) {
				if (!$this->response) {
					$this->response = new CakeResponse();
				}
				$request = new CakeRequest('/');
				$base = $request->base;
				$redirect_url = $base . $redirect_route['Route']['url'];
				$redirect_url = preg_replace("/\/\//", '/', $redirect_url);
				$this->response->header(array('Location' => Router::url($redirect_url, true)));
				if ($route['Route']['status_code'] !== null) {
					$this->response->statusCode((int) $route['Route']['status_code']);
				} else {
					$this->response->statusCode(301);
				}
				$this->_sendResponse();
			}
		}

		if (!$route || $route['Route']['controller'] == '' || $route['Route']['action'] == '') {
			return false;
		}

		if ($route['Route']['plugin'] != '') {
			$params['plugin'] = $route['Route']['plugin'];
		}

		$params['controller'] = $route['Route']['controller'];
		$params['action'] = $route['Route']['action'];

		if ($route['Route']['params'] != '') {
			$params['pass'] = explode('|', $route['Route']['params']);
		}

		return $params;
	}

	/**
	 * Wrapper to send a response.
	 * Can be easily mocked when testing.
	 */
	protected function _sendResponse() {
		$this->response->send();
	}

}
