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
App::uses('Route', 'Core.Model');

class WasabiRoute extends CakeRoute {

	CONST PAGE_PART = 'p';

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
		if (!is_array($url) || empty($url) || !isset($url[Route::PAGE_KEY]) || !isset($url[Route::LANG_KEY])) {
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
			//'Route.plugin' => ($url['plugin'] == null) ? '' : $url['plugin'],
			//'Route.controller' => $url['controller'],
			//'Route.action' => $url['action'],
			'Route.' . Route::PAGE_KEY => $url[Route::PAGE_KEY],
			'Route.' . Route::LANG_KEY => $url[Route::LANG_KEY],
			'Route.status_code' => null
		);

		unset($url['plugin'], $url['controller'], $url['action'], $url[Route::PAGE_KEY], $url[Route::LANG_KEY]);

		$passedParams = array();
		$namedParams = array();

		foreach ($url as $key => $value) {
			if (is_int($key)) {
				$passedParams[] = $value;
			}
			if (is_string($key)) {
				$namedParams[] = $key . ':' . $value;
			}
		}

//		$conditions['Route.params'] = implode('|', $passedParams);

		$route = ClassRegistry::init('Core.Route')->find('first', array(
			'conditions' => $conditions
		));

		if (!$route) {
			return false;
		}

		if (!empty($namedParams)) {
			$route['Route']['url'] .= '/' . implode('/', $namedParams);
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
			'plugin' => 'cms',
			'controller' => 'cms_pages_frontend',
			'action' => 'view',
			'pass' => array(),
			'named' => array()
		);

		$urlParts = explode('/', $url);

		// check for named params
		foreach ($urlParts as $key => $value) {
			if ($value === '') {
				unset($urlParts[$key]);
				continue;
			}
			if (strpos($value, ':') !== false) {
				$namedParams = explode(':', $value);
				$params['named'][$namedParams[0]] = $namedParams[1];
				unset($urlParts[$key]);
			}
		}

		$route = Route::instance()->find('first', array(
			'conditions' => array(
				'Route.url' => '/' . implode('/', $urlParts)
			),
			'related' => array(
				'CmsPage' => array(
					'Collection'
				)
			)
		));

		$pageNumber = false;

		// no direct route found -> try paged collection routes
		if (!$route) {
			$part = array_pop($urlParts);
			if (preg_match('/^[0-9]*$/', $part)) {
				$pageNumber = (int) $part;
				$part = array_pop($urlParts);
			}
			// paged url found so lets try to find a proper collection route
			if ($pageNumber !== false && $part === self::PAGE_PART) {
				$route = Route::instance()->find('first', array(
					'conditions' => array(
						'Route.url' => '/' . implode('/', $urlParts),
						'Collection.type' => 'collection'
					),
					'related' => array(
						'CmsPage' => array(
							'Collection'
						)
					)
				));
			}
		}

		if (!$route) {
			return false;
		}

		if ($route) {
			$redirectUrl = false;
			$statusCode = 301;
			if ($route['Route']['redirect_to'] !== null) {
				$redirectRoute = Route::instance()->findById($route['Route']['redirect_to']);
				if ($redirectRoute) {
					$redirectUrl = $redirectRoute['Route']['url'];
					if ($pageNumber !== false && $pageNumber > 1) {
						$redirectUrl .= '/' . self::PAGE_PART . '/' . $pageNumber;
					}
					if ($route['Route']['status_code'] !== null) {
						$statusCode = (int) $route['Route']['status_code'];
					}
				}
			}
			if ($pageNumber !== false && $pageNumber <= 1) {
				$redirectUrl = '/' . implode('/', $urlParts);
			}
			if ($redirectUrl !== false) {
				$this->_redirect($redirectUrl, $statusCode);
			}
		}

		$params['pass'] = array(
			$route['Route'][Route::PAGE_KEY],
			$route['Route'][Route::LANG_KEY]
		);

		if (isset($route['Collection']['type'])) {
			if ($route['Collection']['type'] === 'collection') {
				$params['collection'] = $route['Collection']['identifier'];
			}
			if ($pageNumber !== false) {
				$params['collection_page'] = $pageNumber;
			}
		}

		return $params;
	}

	protected function _redirect($redirectUrl, $statusCode = 301) {
		if (!$this->response) {
			$this->response = new CakeResponse();
		}
		$request = new CakeRequest('/');
		$base = $request->base;
		$redirectUrl = $base . $redirectUrl;
		$redirectUrl = preg_replace("/\/\//", '/', $redirectUrl);
		$this->response->header('Location', Router::url($redirectUrl, true));
		$this->response->statusCode($statusCode);
		$this->_sendResponse();
		$this->_stop();
	}

	/**
	 * Wrapper to send a response.
	 * Can be easily mocked when testing.
	 */
	protected function _sendResponse() {
		$this->response->send();
	}

	/**
	 * Stop execution. Wraps exit()
	 * making testing easier.
	 *
	 * @param integer|string $status see http://php.net/exit for values
	 */
	protected function _stop($status = 0) {
		exit($status);
	}

}
