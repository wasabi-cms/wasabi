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
 * @subpackage    Wasabi.Plugin.Core.Model.Behavior
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Hash', 'Utility');
App::uses('ModelBehavior', 'Model');

class KeyValueBehavior extends ModelBehavior {

	/**
	 * Map custom find types to each model the behavior is
	 * attached to.
	 *
	 * @var array
	 */
	public $mapMethods = array(
		'/\b_findKeyValues\b/' => 'findKeyValues',
		'/\b_findAllKeyValues\b/' => 'findAllKeyValues'
	);

	/**
	 * Holds the default behavior settings for each model.
	 *
	 * @var array
	 */
	protected $_settings = array();

	/**
	 * Defines default behavior settings.
	 *
	 * @var array
	 */
	protected $_defaults = array(
		'scope' => 'App',
		'fields' => array(
			'key' => 'key',
			'value' => 'value'
		)
	);

	/**
	 * Initiate behavior for the model using specified settings.
	 *
	 * Options:
	 *  - scope: This string defines the current scope namespace
	 *  - uniqueKeys: If this is true it deletes all pre-existing entries for the key on save.
	 *  - fields: Can be used to override the default mapping of 'key' and 'value' fields.
	 *
	 * @param Model $model
	 * @param array $options
	 */
	public function setup(Model $model, $options = array()) {
		$this->_settings[$model->alias] = Hash::merge($this->_defaults, $options);
		$model->findMethods['keyValues'] = true;
		$model->findMethods['allKeyValues'] = true;
	}

	/**
	 * Save key value pairs.
	 *
	 * @param Model $model
	 * @param array $data the request->data array
	 * @return boolean|array
	 */
	public function saveKeyValues(Model $model, $data) {
		/**
		 * @var string $scope
		 * @var array $fields
		 */
		extract($this->_settings[$model->alias]);
		$keys = array_keys($data[$model->alias]);
		$rawData = $data;

		if ((in_array($fields['key'], $keys) &&	in_array($fields['value'], $keys)) ||
			!(isset($rawData[$model->alias]) && !empty($rawData[$model->alias]))
		) {
			return true;
		}

		$model->create($data);
		if (!$model->validates()) {
			return false;
		}

		$mapping = $this->_getMapping($model);

		$dataToSave = array();

		foreach ($rawData[$model->alias] as $key => $value) {
			if ($model->hasField($key)) {
				continue;
			}

			$data = array(
				$model->alias => array(
					'scope' => $scope,
					$fields['key'] => $key,
					$fields['value'] => $value
				)
			);

			$mappingKey = $scope . '.' . $key;
			if (isset($mapping[$mappingKey])) {
				$data[$model->alias]['id'] = $mapping[$mappingKey];
			}

			$dataToSave[] = $data;
		}

		return $model->saveAll($dataToSave, array('validate' => false));
	}

	/**
	 * Custom find type 'keyValues'.
	 *
	 * Returns a scoped array that can be used by FormHelper.
	 *
	 * @param Model $model
	 * @param $functionCall
	 * @param string $state
	 * @param array $query
	 * @param array $results
	 * @return array
	 */
	public function findKeyValues(Model $model, $functionCall, $state, $query, $results = array()) {
		/**
		 * @var string $scope
		 * @var array $fields
		 */
		extract($this->_settings[$model->alias]);
		if ($state === 'before') {
			$query['conditions'][$model->alias . '.scope'] = $scope;
			return $query;
		}

		$rawResults = $results;
		$results = array(
			$model->alias => array()
		);

		foreach ($rawResults as $result) {
			if (!isset($result[$model->alias][$fields['key']]) || !isset($result[$model->alias][$fields['value']])) {
				continue;
			}
			$key = $result[$model->alias][$fields['key']];
			$value = $result[$model->alias][$fields['value']];
			$results[$model->alias][$key] = $value;
		}

		return $results;
	}

	/**
	 * Custom find type 'allKeyValues'.
	 *
	 * Returns all keys and their values for all scopes.
	 *
	 * result:
	 * -------
	 * Array(
	 *   'scope1' => Array(
	 *     'key1' => 'value1',
	 *     'key2' => 'value2',
	 * 	   ...
	 *   ),
	 *   'scope2' => Array(
	 * 	   'key' => 'value',
	 *     ...
	 *   ),
	 *   ...
	 * )
	 *
	 * @param Model $model
	 * @param $functionCall
	 * @param string $state
	 * @param array $query
	 * @param array $results
	 * @return array
	 */
	public function findAllKeyValues(Model $model, $functionCall, $state, $query, $results = array()) {
		if ($state === 'before') {
			return $query;
		}

		/**
		 * @var array $fields
		 */
		extract($this->_settings[$model->alias]);

		$rawResults = $results;
		$results = array();

		foreach ($rawResults as $result) {
			if (!isset($result[$model->alias][$fields['key']]) ||
				!isset($result[$model->alias][$fields['value']]) ||
				!isset($result[$model->alias]['scope'])
			) {
				continue;
			}
			$scope = $result[$model->alias]['scope'];
			$key = $result[$model->alias][$fields['key']];
			$value = $result[$model->alias][$fields['value']];

			$results[$scope . '__' . $key] = $value;
		}

		return Hash::expand($results, '__');
	}

	/**
	 * Returns an array of existing keys and
	 * their corresponding ids.
	 *
	 * @param Model $model
	 * @return array
	 */
	protected function _getMapping(Model $model) {
		/**
		 * @var string $scope
		 * @var array $fields
		 */
		extract($this->_settings[$model->alias]);
		$results = $model->find('all');
		$mapping = array();

		foreach ($results as $result) {
			$key = $scope . '.' . $result[$model->alias][$fields['key']];
			$mapping[$key] = $result[$model->alias]['id'];
		}

		return $mapping;
	}
}
