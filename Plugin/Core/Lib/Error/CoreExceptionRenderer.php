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

	/**
	 * @param Exception $exception
	 * @throws MissingControllerException
	 * @return BackendErrorController|CakeErrorController|Controller|FrontendErrorController
	 */
	protected function _getController($exception) {
		App::uses('BackendErrorController', 'Core.Controller');
		App::uses('CakeErrorController', 'Controller');
		App::uses('CakeRequest', 'Network');
		App::uses('CakeResponse', 'Network');
		App::uses('Controller', 'Controller');
		App::uses('FrontendErrorController', 'Core.Controller');
		App::uses('Router', 'Routing');

		if (!$request = Router::getRequest(true)) {
			$request = new CakeRequest();
		}
		$response = new CakeResponse();

		if (method_exists($exception, 'responseHeader')) {
			$response->header($exception->responseHeader());
		}

		if (isset($request['plugin']) && $request['plugin'] == 'core' && isset($request['controller']) && $request['controller'] == 'core_install') {
			$controller = new FrontendErrorController($request, $response);
			$controller->constructClasses();
			$controller->startupProcess();
			return $controller;
		}

		try {
			$parentClass = '';
			if (isset($request->params['controller']) && $request->params['controller'] != '') {
				$pluginName = '';
				if (isset($request->params['plugin'])) {
					$pluginName = Inflector::camelize($request->params['plugin']) . '.';
				}

				$controllerName = Inflector::camelize($request->params['controller']) . 'Controller';
				App::uses($controllerName, $pluginName . 'Controller');

				if (!class_exists($controllerName)) {
					throw new MissingControllerException($controllerName);
				}
				$tmpController = new $controllerName(new CakeRequest(), new CakeResponse());

				$parentClass = get_parent_class($tmpController);
				unset($pluginName, $controllerName, $tmpController);
			}
			if ($parentClass == 'BackendAppController') {
				$controller = new BackendErrorController($request, $response);
			} else if ($parentClass == 'FrontendAppController') {
				$controller = new FrontendErrorController($request, $response);
			} else {
				$controller = new CakeErrorController($request, $response);
			}
			$controller->constructClasses();
			$controller->startupProcess();
		} catch (Exception $e) {
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
