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
 * @property HtmlHelper $Html
 * @property CoreView $_View
 */

class CHtmlHelper extends AppHelper {

	/**
	 * Helpers used by this helper.
	 *
	 * @var array
	 */
	public $helpers = array(
		'Html'
	);

	protected $_title = false;
	protected $_subTitle = false;
	protected $_actions = array();

	/**
	 * Create a properly prefixed backend link.
	 *
	 * automatically prepends the backend url prefix to the desired $url
	 *
	 * @param string $title
	 * @param array|string $url
	 * @param array $options
	 * @param bool $displayLinkTextIfUnauthorized
	 * @return string
	 */
	public function backendLink($title, $url, $options = array(), $displayLinkTextIfUnauthorized = false) {
		$url = $this->_getBackendUrl($url);
		if (!Guardian::hasAccess($url)) {
			if ($displayLinkTextIfUnauthorized) {
				return $title;
			}
			return '';
		}
		return $this->Html->link($title, $url, $options);
	}

	/**
	 * Create a backend confirmation link.
	 *
	 * @param string $title
	 * @param array|string $url
	 * @param array $options
	 * @param bool $displayLinkTextIfUnauthorized
	 * @return string
	 * @throws CakeException
	 */
	public function backendConfirmationLink($title, $url, $options, $displayLinkTextIfUnauthorized = false) {
		if (!isset($options['confirm-message'])) {
			throw new CakeException('\'confirm-message\' option is not set in confirmationLink.');
		}
		if (!isset($options['confirm-title'])) {
			throw new CakeException('\'confirm-title\' option is not set in confirmationLink.');
		}

		$url = $this->_getBackendUrl($url, true);
		if (!Guardian::hasAccess($url)) {
			if ($displayLinkTextIfUnauthorized) {
				return $title;
			}
			return '';
		}

		$confirmOptions = array(
			'class' => 'confirm',
			'data-confirm-action' => $url
		);

		if (isset($options['class']) && $options['class'] != '') {
			$confirmOptions['class'] .= ' ' . trim($options['class']);
			unset($options['class']);
		}

		$confirmOptions['data-confirm-message'] = $options['confirm-message'];
		unset($options['confirm-message']);

		$confirmOptions['data-confirm-title'] = $options['confirm-title'];
		unset($options['confirm-title']);

		if (isset($options['confirm-subtitle'])) {
			$confirmOptions['data-confirm-subtitle'] = $options['confirm-subtitle'];
			unset($options['confirm-subtitle']);
		}

		$confirmOptions = Hash::merge($confirmOptions, $options);

		return $this->Html->link($title, '#', $confirmOptions);
	}

	public function setTitle($title) {
		$this->_title = $title;
	}

	public function setSubTitle($subTitle) {
		$this->_subTitle = $subTitle;
	}

	public function addAction($action) {
		$this->_actions[] = $action;
	}

	public function titlePad() {
		$out = '';
		if ($this->_title === false) {
			return;
		}
		$out .= '<div class="title-pad row">';
		$out .= $this->_pageTitle($this->_title, $this->_subTitle);
		if (!empty($this->_actions)) {
			$out .= '<ul class="actions">';
			foreach ($this->_actions as $action) {
				$out .= '<li>' . $action . '</li>';
			}
			$out .= '</ul>';
		}
		$out .= '</div>';

		return $out;
	}

	protected function _pageTitle($title, $subtitle = false) {
		$out = '<h3 class="page-title">' . $title;
		if ($subtitle !== false) {
			$out .= ' <small>' . $subtitle . '</small>';
		}
		$out .= '</h3>';

		return $out;
	}

	/**
	 * Transform the supplied $url into a properly prefixed backend url.
	 *
	 * @param array|string $url
	 * @param bool $rel
	 * @return array|string
	 */
	protected function _getBackendUrl($url, $rel = false) {
		if (!is_array($url)) {
			$url = ltrim($url, '/');
			$url = '/' . Configure::read('Wasabi.backend_prefix') . '/' . $url;
		}
		if ($rel !== false) {
			$url = Router::url($url);
		}
		return $url;
	}
}