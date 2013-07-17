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
	 * @return Controller
	 */
	protected function _getController($exception) {
		if (!$request = Router::getRequest(true)) {
			$request = new CakeRequest();
		}
		$response = new CakeResponse();

		if (method_exists($exception, 'responseHeader')) {
			$response->header($exception->responseHeader());
		}

		try {
			$exceptionType = get_class($exception);
			switch ($exceptionType) {
				case 'MissingControllerException':
					App::uses('BackendErrorController', 'Core.Controller');
					$controller = new BackendErrorController($request, $response);
					break;
				default:
					App::uses('FrontendErrorController', 'Core.Controller');
					$controller = new FrontendErrorController($request, $response);
			}
			$controller->constructClasses();
			$controller->startupProcess();
		} catch (Exception $e) {
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

	protected function _outputMessage($template) {
		try {
			$this->controller->plugin = 'Core';
			$this->controller->render($template, 'Core.default');
			$this->controller->afterFilter();
			$this->controller->response->send();
		} catch (MissingViewException $e) {
			$attributes = $e->getAttributes();
			if (isset($attributes['file']) && strpos($attributes['file'], 'error500') !== false) {
				$this->_outputMessageSafe('error500');
			} else {
				$this->_outputMessage('error500');
			}
		} catch (Exception $e) {
			var_dump($e);
			$this->_outputMessageSafe('error500');
		}
	}

	/**
	 * A safer way to render error messages, replaces all helpers, with basics
	 * and doesn't call component methods.
	 *
	 * @param string $template The template to render
	 * @return void
	 */
	protected function _outputMessageSafe($template) {
		$this->controller->layoutPath = null;
		$this->controller->subDir = null;
		$this->controller->viewPath = 'BackendError/';

		$view = new View($this->controller);
		$this->controller->response->body($view->render($template, 'Core.default'));
		$this->controller->response->type('html');
		$this->controller->response->send();
	}

}
