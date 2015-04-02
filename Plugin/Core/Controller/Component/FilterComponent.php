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
 * @subpackage    Wasabi.Plugin.Core.Controller.Component
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

class FilterComponent extends Component {

	/**
	 * Request object.
	 *
	 * @var CakeRequest
	 */
	public $request;

	/**
	 * Filter fields registered on the controller.
	 *
	 * @var array
	 */
	public $filterFields = array();

	/**
	 * Filter options for find calls.
	 *
	 * @var array
	 */
	public $filterOptions = array();

	/**
	 * Holds all active filters and their values.
	 *
	 * @var array
	 */
	public $activeFilters = array();

	/**
	 * Sort fields registered on the controller.
	 *
	 * @var array
	 */
	public $sortFields = array();

	/**
	 * Holds the active sort field (key) and its direction (value).
	 *
	 * @var array
	 */
	public $activeSort = array();

	public $defaultSort = array();

	/**
	 * @var integer The current page.
	 */
	public $page = 1;

	public $paginationParams = array();

	/**
	 * Called before the Controller::beforeFilter().
	 *
	 * @param Controller $controller Controller with components to initialize
	 * @return void
	 */
	public function initialize(Controller $controller) {
		parent::initialize($controller);

		$isSort = isset(
			$controller->sortFields,
			$controller->sortableActions,
			$controller->sortableActions[$controller->request->params['action']]
		);

		$isFilter = isset(
			$controller->filterFields,
			$controller->filteredActions,
			$controller->filteredActions[$controller->request->params['action']]
		);

		if ($isSort || $isFilter) {
			$this->request = $controller->request;

			if ($isSort) {
				$this->sortFields = array_intersect_key(
					$controller->sortFields,
					array_flip($controller->sortableActions[$this->request->params['action']])
				);
				foreach ($this->sortFields as $field => $options) {
					if (!isset($options['default'])) {
						continue;
					}
					$dir = strtolower($options['default']);
					if (in_array($dir, array('asc', 'desc'))) {
						$this->defaultSort = array(
							'field' => $field,
							'dir' => $dir
						);
					}
				}
			}

			if ($isFilter) {
				$this->filterFields = array_intersect_key(
					$controller->filterFields,
					array_flip($controller->filteredActions[$this->request->params['action']])
				);
			}
		}
	}

	/**
	 * Called after the Controller::beforeFilter() and before the controller action.
	 *
	 * @param Controller $controller
	 * @return void
	 */
	public function startup(Controller $controller) {
		$this->_initFilterOptions();
	}

	/**
	 * Called after Controller::beforeRender()
	 *
	 * @param Controller $controller
	 * @return void
	 */
	public function beforeRender(Controller $controller) {

	}

	public function getFilterOptions($ignoreFilters = array()) {
		if (empty($ignoreFilters)) {
			return $this->filterOptions;
		}

		$filters = array_diff_key($this->activeFilters, array_flip($ignoreFilters));

		$options = array(
			'conditions' => array(),
			'order' => array()
		);

		foreach ($filters as $field => $value) {
			$options = $this->_createFilterFieldOption($field, $value, $options);
		}

		return $options;
	}

	public function paginate($limit, $total, $options) {
		if ($limit !== null && $total !== null) {
			$pages = ceil($total / $limit);
			if ($this->page > $pages) {
				$this->page = $pages;
			}
			if ($this->page < 1) {
				$this->page = 1;
			}
			$offset = ($this->page > 1) ? ($this->page - 1) * $limit : 0;
			$from = ($offset > 0) ? $offset + 1 : 1;
			$to = (($from + $limit - 1) > $total) ? $total : $from + $limit - 1;

			$options['limit'] = $limit;
			$options['offset'] = $offset;

			$this->paginationParams = array(
				'from' => $from,
				'page' => $this->page,
				'pages' => $pages,
				'to' => $to,
			);
		}

		return $options;
	}

	/**
	 * Create the filter options that
	 * can be used for model find calls
	 * in the controller.
	 *
	 * @return void
	 */
	protected function _initFilterOptions() {
		if ((empty($this->request->query) && empty($this->defaultSort)) ||
			(empty($this->filterFields) && empty($this->sortFields))
		) {
			return;
		}

		$options = array(
			'conditions' => array(),
			'order' => array()
		);

		foreach ($this->request->query as $field => $value) {
			if (in_array($field, array('s', 'd', 'p')) || !isset($this->filterFields[$field])) {
				continue;
			}

			$options = $this->_createFilterFieldOption($field, $value, $options);

			$this->activeFilters[$field] = $value;
		}

		if (isset($this->request->query['s'], $this->sortFields[$this->request->query['s']])) {
			$d = 'asc';
			if (isset($this->request->query['d'])) {
				$dir = strtolower($this->request->query['d']);
				if (in_array($dir, array('asc', 'desc'))) {
					$d = $dir;
				}
			}
			$field = $this->request->query['s'];
			$options = $this->_createSortFieldOption($field, $d, $options);

			$this->activeSort[$field] = $d;
		} elseif (!empty($this->defaultSort)) {
			$options = $this->_createSortFieldOption($this->defaultSort['field'], $this->defaultSort['dir'], $options);
			$this->activeSort[$this->defaultSort['field']] = $this->defaultSort['dir'];
		}

		if (isset($this->request->query['p'])) {
			$this->page = $this->request->query['p'];
		}

		$this->filterOptions = $options;
	}

	protected function _createSortFieldOption($field, $dir, $options) {
		$sortField = $this->sortFields[$field];
		$options['order'][] = $sortField['modelField'] . ' ' . $dir;

		return $options;
	}

	protected function _createFilterFieldOption($field, $value, $options) {
		$filterField = $this->filterFields[$field];

		if (isset($filterField['type'])) {
			switch ($filterField['type']) {
				case 'like':
					$options['conditions'][] = $filterField['modelField'] . ' LIKE "%' . $value . '%"';
					break;
				case 'value':
					$options['conditions'][] = $filterField['modelField'] . ' = "' . $value . '"';
					break;
				case 'having':
					$options['group'] = 'HAVING ' . $filterField['modelField'] . ' LIKE "%' . $value . '%"';
					break;
				case 'date_range':
//						$options['conditions'][] = '';
					break;
			}

			if (isset($filterField['related'])) {
				if (!isset($options['related'])) {
					$options['related'] = array();
				}
				$options['related'] = array_merge($options['related'], $filterField['related']);
			}
		}

		return $options;
	}

}
