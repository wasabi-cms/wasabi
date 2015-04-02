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
 * @property CoreView $_View
 * @property HtmlHelper $Html
 */
class FilterHelper extends AppHelper {

	/**
	 * Helpers used by this helper.
	 *
	 * @var array
	 */
	public $helpers = array(
		'Html'
	);

	/**
	 * @param array $showFilters
	 * @return string
	 */
	public function activeFilters($showFilters = array()) {
		$showFilters = array_intersect_key($showFilters, $this->_View->activeFilters);
		if (empty($showFilters)) {
			return '';
		}
		$out = '<ul class="active-filters">';
		foreach ($showFilters as $filter => $text) {
			$text = preg_replace('/{{VALUE}}/', $this->_View->activeFilters[$filter], $text);
			$out .= '<li>';
			$out .= $this->link($text . '<i class="icon-remove-sign"></i>', array(), array($filter), array('class' => 'remove-filter', 'escape' => false));
			$out .= '</li>';
		}
		$out .= '</ul>';
		return $out;
	}

	/**
	 * @param array $groups
	 * @param boolean $showAll if false only shows filters that have a count > 0
	 * @return string
	 */
	public function groups($groups = array(), $showAll = false) {
		$out = '<ul class="filter-groups row">';

		foreach ($groups as $filterField => $filterOptions) {
			$filterGroups = array_keys($groups);
			if (isset($filterGroups['EMPTY'])) {
				unset($filterGroups['EMPTY']);
			}
			if ($filterField === 'EMPTY') {
				$class = (!count(array_intersect_key(array_flip($filterGroups), $this->_View->activeFilters))) ? ' class="active"' : '';
				$out .= '<li' . $class . '>';
				$out .= $this->link($filterOptions['name'] . ' <span>(' . $filterOptions['count'] . ')</span>', array(), $filterGroups, array(
					'title' => $filterOptions['title'],
					'escape' => false
				));
				$out .= '</li>';
				continue;
			}
			unset($filterGroups[$filterField]);

			foreach ($filterOptions as $key => $g) {
				if ($showAll === false && (!isset($g['count']) || $g['count'] === 0)) {
					continue;
				}
				$options = array(
					'title' => $g['title'],
					'escape' => false
				);
				$class = (isset($this->_View->activeFilters[$filterField]) && $this->_View->activeFilters[$filterField] === $key) ? ' class="active"' : '';
				$addFilters = array($filterField => $key);
				$removeFilters = $filterGroups;
				$out .= '<li' . $class . '>';
				$out .= $this->link($g['name'] . ' <span>(' . $g['count'] . ')</span>', $addFilters, $removeFilters, $options);
				$out .= '</li>';
			}
		}

		$out .= '</ul>';
		return $out;
	}

	/**
	 * @param string $name
	 * @param array $addFilters
	 * @param array|boolean|string $removeFilters
	 * @param array $options
	 * @return string
	 */
	public function link($name, $addFilters = array(), $removeFilters = false, $options = array()) {
		$url = $this->_getFilterUrl(true, $removeFilters);
		foreach ($addFilters as $filter => $value) {
			if ($value === '') {
				continue;
			}
			if (!isset($url['?'])) {
				$url['?'] = array();
			}
			$url['?'] = array_merge($url['?'], array(
				$filter => $value
			));
		}
		return $this->Html->link($name, $url, $options);
	}

	public function sortLink($name, $field, $options = array()) {
		$url = $this->_getSortUrl($field);

		$iClass = 'icon-14-sortable';
		if (isset($this->_View->activeSort[$field])) {
			if ($this->_View->activeSort[$field] === 'asc') {
				$iClass = 'icon-14-sortable-up';
			} else {
				$iClass = 'icon-14-sortable-down';
			}
		}

		$name = '<span>' . $name . '</span><i class="' . $iClass . '"></i>';
		$options['escape'] = false;

		return $this->Html->link($name, $url, $options);
	}

	public function pagination($wrap = 'div', $class = 'pagination') {
		if (empty($this->_View->paginationParams) || ($this->_View->paginationParams['pages'] <= 1)) {
			return '';
		}
		$page = (int) $this->_View->paginationParams['page'];
		$pages = (int) $this->_View->paginationParams['pages'];
		$pMin = 1;
		$pMax = $pages;
		$prev = '';
		$list = '';
		$next = '';

		if ($page > 1) {
			$prev .= $this->Html->link(__d('core', 'prev'), $this->_getPaginatedUrl($page - 1), array('class' => 'prev', 'escape' => false));
		}
		if ($pages > 10) {
			if ($page - 4 >= 1) {
				$pMin = $page - 2;
				$prev .= $this->Html->link(1, $this->_getPaginatedUrl(1));
				$prev .= '<span>...</span>';
			}
			$leftOver = $pages - $page;
			if ($leftOver <= 4) {
				for ($i = -(5 - $leftOver); $i <= -1; $i++) {
					$p = $pMin + $i;
					$prev .= $this->Html->link($p, $this->_getPaginatedUrl($p));
				}
			}
			if ($page + 5 <= $pages) {
				$pMax = $page + 3;
				$next .= '<span>...</span>';
				$next .= $this->Html->link($pages, $this->_getPaginatedUrl($pages));
			}
			if ($page <= 4) {
				$pMax = 8;
			}
		}
		foreach (range($pMin, $pMax) as $p) {
			$options = array();
			if ($p === $page) {
				$options['class'] = 'current';
			}
			$list .= $this->Html->link($p, $this->_getPaginatedUrl($p), $options);
		}
		if ($page < $pages) {
			$next .= $this->Html->link(__d('core', 'next'), $this->_getPaginatedUrl($page + 1), array('class' => 'next', 'escape' => false));
		}
		return '<' . $wrap . ' class="' . $class . '">' . $prev . $list . $next . '</' . $wrap . '>';
	}

	protected function _getPaginatedUrl($page) {
		$url = $this->_getSortUrl();
		if ($page == 1) {
			return $url;
		}
		if (!isset($url['?'])) {
			$url['?'] = array();
		}
		$url['?']['p'] = $page;
		return $url;
	}

	/**
	 * @param string $field
	 * @return array
	 */
	protected function _getSortUrl($field = '') {
		$url = $this->_getFilterUrl();

		if ($field === '' && empty($this->_View->activeSort)) {
			return $url;
		}

		if (!isset($url['?'])) {
			$url['?'] = array();
		}

		if ($field !== '') {
			$url['?']['s'] = $field;
			$dir = 'asc';
			if (isset($this->_View->activeSort[$field])) {
				if ($this->_View->activeSort[$field] === 'asc') {
					$url['?']['d'] = 'desc';
					$dir = 'desc';
				}
			}
			if ($field === $this->_View->defaultSort['field'] && $dir === $this->_View->defaultSort['dir']) {
				unset($url['?']['s']);
				if (isset($url['?']['d'])) {
					unset($url['?']['d']);
				}
			}

			return $url;
		}

		if (!empty($this->_View->activeSort)) {
			$field = array_keys($this->_View->activeSort)[0];
			$dir = $this->_View->activeSort[$field];
			if (($field === $this->_View->defaultSort['field'] && $dir !== $this->_View->defaultSort['dir']) ||
				$field !== $this->_View->defaultSort['field']
			) {
				$url['?']['s'] = $field;
				if ($this->_View->activeSort[$field] !== 'asc') {
					$url['?']['d'] = 'desc';
				}
			}
		}

		return $url;
	}

	/**
	 * @param boolean $withFilters
	 * @param array|boolean|string $removeFilters
	 * @return array
	 */
	protected function _getFilterUrl($withFilters = true, $removeFilters = false) {
		$url = array(
			'plugin' => $this->request->params['plugin'],
			'controller' => $this->request->params['controller'],
			'action' => $this->request->params['action'],
		);
		if ($withFilters === true && !empty($this->_View->activeFilters)) {
			$url['?'] = $this->_View->activeFilters;
		}
		if ($removeFilters !== false && isset($url['?'])) {
			if (!is_array($removeFilters)) {
				$removeFilters = array($removeFilters);
			}
			foreach ($removeFilters as $rf) {
				if (isset($url['?'][$rf])) {
					unset($url['?'][$rf]);
				}
			}
			if (empty($url['?'])) {
				unset($url['?']);
			}
		}
		return $url;
	}
}
