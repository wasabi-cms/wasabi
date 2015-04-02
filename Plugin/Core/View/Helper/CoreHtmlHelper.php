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

App::uses('HtmlHelper', 'View/Helper');

/**
 * @property HtmlHelper $Html
 * @property CoreView $_View
 */

class CoreHtmlHelper extends HtmlHelper {

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
	 * @param boolean $displayLinkTextIfUnauthorized
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
		return $this->link($title, $url, $options);
	}

	/**
	 * Create a properly prefixed backend link and
	 * don't check permissions.
	 *
	 * @param string $title
	 * @param array|string $url
	 * @param array $options
	 * @return string
	 */
	public function backendUnprotectedLink($title, $url, $options = array()) {
		$url = $this->_getBackendUrl($url);
		return $this->link($title, $url, $options);
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
			user_error('\'confirm-message\' option is not set on backendConfirmationLink.');
			$options['confirm-message'] = '';
		}
		if (!isset($options['confirm-title'])) {
			user_error('\'confirm-title\' option is not set on backendConfirmationLink.');
			$options['confirm-title'] = '';
		}

		$url = $this->_getBackendUrl($url);
		if (!Guardian::hasAccess($url)) {
			if ($displayLinkTextIfUnauthorized) {
				return $title;
			}
			return '';
		}

		$linkOptions = array(
			'data-modal-title' => $options['confirm-title'],
			'data-modal-body' => '<p>' . $options['confirm-message'] . '</p>',
			'data-method' => 'post',
			'data-toggle' => 'confirm'
		);
		unset($options['confirm-title'], $options['confirm-message']);

		if (isset($options['ajax']) && $options['ajax'] === true) {
			$linkOptions['data-ajax'] = 1;
			unset($options['ajax']);

			if (isset($options['notify'])) {
				$linkOptions['data-notify'] = $options['notify'];
				unset($options['notify']);
			}

			if (isset($options['event'])) {
				$linkOptions['data-event'] = $options['event'];
				unset($options['event']);
			}
		}

		$linkOptions = Hash::merge($linkOptions, $options);
		return $this->link($title, $url, $linkOptions);
	}

	public function getBackendUrl($url, $rel = false) {
		$checkUrl = $this->_getBackendUrl($url);
		if (!Guardian::hasAccess($checkUrl)) {
			return false;
		}
		return $this->_getBackendUrl($url, $rel);
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
			return $out;
		}
		$out .= '<div class="title-pad">';
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

	public function linkTo($type = 'page', $title = '', $params = array(), $options = array()) {
		$link = '';
		switch ($type) {
			case 'page':
				if (!isset($params['page_id'])) {
					user_error('Html::linkTo(\'page\', ...) $params requires the key \'page_id\'.');
				}
				if (!isset($params['language_id'])) {
					$params['language_id'] = Configure::read('Wasabi.content_language_id');
				}
				$url = array(
					'plugin' => 'cms',
					'controller' => 'cms_pages_frontend',
					'action' => 'view',
					$params['page_id'],
					$params['language_id']
				);
				$link = $this->link($title, $url, $options);
				break;
			case 'collection_item':
				if (!isset($params['collection'])) {
					user_error('Html::linkTo(\'collection_item\', ...) $params requires the key \'collection\'.');
				}
				if (!isset($params['item_id'])) {
					user_error('Html::linkTo(\'collection_item\', ...) $params requires the key \'item_id\'.');
				}
				break;
		}

		return $link;
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
