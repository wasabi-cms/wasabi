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
 * @subpackage    Wasabi.Plugin.Core.View.Helper
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppHelper', 'View/Helper');

/**
 * @property CoreHtmlHelper $Html
 * @property CoreView $_View
 */
class PaginationHelper extends AppHelper {

	/**
	 * Helpers used by this helper
	 *
	 * @var array
	 */
	public $helpers = array(
		'Html' => array(
			'className' => 'Core.CoreHtml'
		)
	);

	public $options = array();

	public function sort($key, $direction = false) {

	}

//	public function beforeRender() {
//		if (!isset($options['url'])) {
//			$options['url'] = array();
//		}
//		if (!empty($this->request->query)) {
//			$options['url']['?'] = $this->request->query;
//		}
//	}

	protected function _getSortUrl() {
		$url = array(
			'plugin' => $this->request->params['plugin'],
			'controller' => $this->request->params['controller'],
			'action' => $this->request->params['action'],
		);
		if (!empty($this->_View->activeFilters)) {
			$url['?'] = $this->_View->activeFilters;
		}
		return $url;
	}

}