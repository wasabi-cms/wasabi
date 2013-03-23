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
 * @subpackage    Wasabi.Plugin.Core.Lib.Error
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('ExceptionRenderer', 'Error');

class CoreExceptionRenderer extends ExceptionRenderer {

	protected function _getController(Exception $exception) {
		App::uses('CakeRequest', 'Network');
		App::uses('CakeResponse', 'Network');
		App::uses('Controller', 'Controller');
		App::uses('Router', 'Routing');

		if (!$request = Router::getRequest(true)) {
			$request = new CakeRequest();
		}
		$response = new CakeResponse();

		if (method_exists($exception, 'responseHeader')) {
			$response->header($exception->responseHeader());
		}

		try {
			$parent_class = '';
			if (isset($request->params['controller']) && $request->params['controller'] != '') {
				$plugin_name = '';
				if (isset($request->params['plugin'])) {
					$plugin_name = Inflector::camelize($request->params['plugin']) . '.';
				}

				$controller_name = Inflector::camelize($request->params['controller']) . 'Controller';
				App::uses($controller_name, $plugin_name . 'Controller');

				if (!class_exists($controller_name)) {
					throw new MissingControllerException($controller_name);
				}
				$tmp_controller = new $controller_name(new CakeRequest(), new CakeResponse());

				$parent_class = get_parent_class($tmp_controller);
				unset($plugin_name, $controller_name, $tmp_controller);
			}
			if ($parent_class == 'BackendAppController') {
				App::uses('BackendErrorController', 'Core.Controller');
				$controller = new BackendErrorController($request, $response);
			} else if ($parent_class == 'FrontendAppController') {
				App::uses('FrontendErrorController', 'Core.Controller');
				$controller = new FrontendErrorController($request, $response);
			} else {
				App::uses('CakeErrorController', 'Controller');
				$controller = new CakeErrorController($request, $response);
			}
			$controller->constructClasses();
			$controller->startupProcess();
		} catch (Exception $e) {
			App::uses('FrontendErrorController', 'Core.Controller');
			$controller = new FrontendErrorController($request, $response);
			$controller->constructClasses();
			$controller->startupProcess();
			if (!empty($controller) && $controller->Components->enabled('RequestHandler')) {
				$controller->RequestHandler->startup($controller);
			}
		}
		if (empty($controller)) {
			$controller = new Controller($request, $response);
			$controller->viewPath = 'Errors';
		}
		return $controller;
	}

}
